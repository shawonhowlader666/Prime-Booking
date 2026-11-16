<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PaymentTransaction;
use App\Services\Booking\BookingLifecycleService;
use App\Services\Payment\BkashPaymentService;
use App\Services\Payment\SSLCommerzPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Starts gateway payments and handles their callbacks.
 *
 * Rules that hold for every path in this controller:
 *  - A booking is only marked paid after the gateway itself confirmed the
 *    payment server-to-server, for the booking's own amount.
 *  - Simulated payment pages exist only outside production.
 *  - All state changes go through BookingLifecycleService, which is idempotent.
 *
 * SSLCommerz callbacks are cross-site POSTs, so they run without the session
 * middleware (see routes) and hand the guest over with a signed URL instead.
 */
class PaymentCallbackController extends Controller
{
    public function __construct(
        private readonly BkashPaymentService $bkashService,
        private readonly SSLCommerzPaymentService $sslService,
        private readonly BookingLifecycleService $lifecycle,
    ) {}

    // ─── Payment status page ──────────────────────────────────────────────

    /** GET /payment/pending/{reference} — pay now / retry page for an unpaid online booking. */
    public function pending(Request $request, string $reference): View|RedirectResponse
    {
        $booking = Booking::where('booking_reference', $reference)->with(['property', 'room'])->firstOrFail();

        if ($booking->payment_status === 'paid') {
            return redirect()->to($this->signedConfirmationUrl($booking));
        }

        return view('pages.payment-pending', [
            'booking'           => $booking,
            'state'             => (string) $request->query('state', ''),
            'canPay'            => $booking->isAwaitingPayment() && $this->withinPaymentWindow($booking),
            'secondsRemaining'  => $booking->payment_expires_at ? max(0, (int) now()->diffInSeconds($booking->payment_expires_at, false)) : null,
            'bkashAvailable'    => $this->bkashService->isConfigured() || BookingLifecycleService::simulationAllowed(),
        ]);
    }

    // ─── bKash ────────────────────────────────────────────────────────────

    /** GET /payment/bkash/sandbox/{reference} — start a bKash payment. */
    public function bkashSandboxRedirect(string $reference): View|RedirectResponse
    {
        $booking = $this->payableBooking($reference);
        if ($booking instanceof RedirectResponse) {
            return $booking;
        }

        if ($this->bkashService->isConfigured()) {
            $created = $this->bkashService->createPayment($booking, route('payment.bkash.callback'));

            if (($created['status'] ?? '') === 'success') {
                PaymentTransaction::updateOrCreate(
                    ['booking_id' => $booking->id, 'txn_id' => $created['paymentID']],
                    [
                        'gateway_code' => 'bkash',
                        'amount'       => (float) $booking->total_price,
                        'currency'     => 'BDT',
                        'status'       => 'initiated',
                        'ip_address'   => request()->ip(),
                        'user_agent'   => Str::limit((string) request()->userAgent(), 500, ''),
                    ]
                );

                return redirect()->away($created['bkashURL']);
            }

            return $this->toPending($booking, 'gateway_error');
        }

        if (BookingLifecycleService::simulationAllowed()) {
            return view('pages.payment-bkash-sandbox', compact('booking'));
        }

        // bKash is not set up as a direct gateway: SSLCommerz hosted checkout offers bKash, Nagad and Rocket.
        return redirect()->route('payment.ssl.sandbox-redirect', $booking->booking_reference);
    }

    /** POST /payment/bkash/sandbox-execute/{reference} — simulated payment (never in production). */
    public function bkashSandboxExecute(Request $request, string $reference): RedirectResponse
    {
        abort_unless(BookingLifecycleService::simulationAllowed(), 404);

        $booking = Booking::where('booking_reference', $reference)->firstOrFail();
        $txnId   = 'SIM-BKASH-' . Str::upper(Str::random(10));

        return $this->finish($booking, $this->lifecycle->confirmPayment($booking, 'bkash', $txnId, null, ['simulated' => true]));
    }

    /** GET /payment/bkash/callback — bKash returns the guest here after they approve or cancel. */
    public function bkashCallback(Request $request): RedirectResponse
    {
        $paymentID = (string) $request->input('paymentID', '');
        $status    = (string) $request->input('status', '');

        // The booking is resolved from the paymentID we stored when the payment was created,
        // never from a reference supplied in the callback URL.
        $transaction = $paymentID !== ''
            ? PaymentTransaction::where('gateway_code', 'bkash')->where('txn_id', $paymentID)->first()
            : null;
        $booking = $transaction?->booking;

        if (!$booking) {
            return redirect()->route('home')->with('error', 'We could not match this bKash payment to a booking.');
        }

        if ($booking->payment_status === 'paid') {
            return redirect()->to($this->signedConfirmationUrl($booking));
        }

        if ($status !== 'success') {
            $transaction->update(['status' => $status === 'cancel' ? 'cancelled' : 'failed']);
            return $this->toPending($booking, $status === 'cancel' ? 'cancelled' : 'failed');
        }

        $result = $this->bkashService->executePayment($paymentID);

        if (($result['status'] ?? '') !== 'Completed'
            || ($result['invoice'] !== null && $result['invoice'] !== $booking->booking_reference)
            || $result['amount'] === null) {
            Log::warning("bKash payment not confirmed for {$booking->booking_reference}", ['message' => $result['message'] ?? null]);
            $transaction->update(['status' => 'failed', 'gateway_response' => $result['raw'] ?? $result]);
            return $this->toPending($booking, 'failed');
        }

        $outcome = $this->lifecycle->confirmPayment($booking, 'bkash', $result['trxID'] ?: $paymentID, (float) $result['amount'], $result['raw'] ?? []);
        $transaction->update(['status' => $outcome === BookingLifecycleService::AMOUNT_MISMATCH ? 'failed' : 'verified', 'verified_at' => now()]);

        return $this->finish($booking, $outcome);
    }

    // ─── SSLCommerz ───────────────────────────────────────────────────────

    /** GET /payment/ssl/sandbox/{reference} — start an SSLCommerz hosted payment (cards, net banking, MFS). */
    public function sslSandboxRedirect(string $reference): View|RedirectResponse
    {
        $booking = $this->payableBooking($reference);
        if ($booking instanceof RedirectResponse) {
            return $booking;
        }

        if ($this->sslService->isConfigured()) {
            $init = $this->sslService->initiatePayment(
                $booking,
                route('payment.ssl.success'),
                route('payment.ssl.fail'),
                route('payment.ssl.cancel')
            );

            if (($init['status'] ?? '') === 'success') {
                return redirect()->away($init['redirectUrl']);
            }

            if (!BookingLifecycleService::simulationAllowed()) {
                return $this->toPending($booking, 'gateway_error');
            }
        }

        if (BookingLifecycleService::simulationAllowed()) {
            return view('pages.payment-ssl-sandbox', compact('booking'));
        }

        Log::critical('Online payment requested but no payment gateway is configured.');
        return $this->toPending($booking, 'gateway_error');
    }

    /** POST /payment/ssl/sandbox-execute/{reference} — simulated payment (never in production). */
    public function sslSandboxExecute(Request $request, string $reference): RedirectResponse
    {
        abort_unless(BookingLifecycleService::simulationAllowed(), 404);

        $booking = Booking::where('booking_reference', $reference)->firstOrFail();
        $txnId   = 'SIM-SSL-' . Str::upper(Str::random(10));

        return $this->finish($booking, $this->lifecycle->confirmPayment($booking, 'sslcommerz', $txnId, null, ['simulated' => true]));
    }

    /** POST /payment/ssl/success */
    public function sslSuccess(Request $request): RedirectResponse
    {
        $booking = $this->bookingFromSslRequest($request);
        if (!$booking) {
            return redirect()->route('home');
        }

        return $this->finish($booking, $this->settleSsl($request, $booking));
    }

    /** POST /payment/ssl/fail — the booking stays reserved until its payment window ends so the guest can retry. */
    public function sslFail(Request $request): RedirectResponse
    {
        $booking = $this->bookingFromSslRequest($request);
        return $booking ? $this->toPending($booking, 'failed') : redirect()->route('home');
    }

    /** POST /payment/ssl/cancel */
    public function sslCancel(Request $request): RedirectResponse
    {
        $booking = $this->bookingFromSslRequest($request);
        return $booking ? $this->toPending($booking, 'cancelled') : redirect()->route('home');
    }

    /** POST /payment/ssl/ipn — server-to-server notification; confirms even if the guest closed the browser. */
    public function sslIpn(Request $request)
    {
        $booking = $this->bookingFromSslRequest($request);

        if ($booking && in_array((string) $request->input('status'), ['VALID', 'VALIDATED'], true)) {
            $this->settleSsl($request, $booking);
        }

        return response('IPN Processed', 200);
    }

    // ─── Internals ────────────────────────────────────────────────────────

    /** Validate with SSLCommerz, then confirm. Returns a BookingLifecycleService outcome or 'unverified'. */
    private function settleSsl(Request $request, Booking $booking): string
    {
        if ($booking->payment_status === 'paid') {
            return BookingLifecycleService::ALREADY_PAID;
        }

        $data = $this->sslService->fetchValidation((string) $request->input('val_id', ''));

        if (!$data
            || SSLCommerzPaymentService::referenceFromTranId((string) ($data['tran_id'] ?? '')) !== $booking->booking_reference
            || strtoupper((string) ($data['currency_type'] ?? 'BDT')) !== 'BDT') {
            Log::warning("SSLCommerz payment could not be verified for {$booking->booking_reference}", ['ip' => $request->ip()]);
            return 'unverified';
        }

        return $this->lifecycle->confirmPayment(
            $booking,
            'sslcommerz',
            (string) ($data['bank_tran_id'] ?? $data['tran_id']),
            (float) ($data['currency_amount'] ?? $data['amount'] ?? 0),
            $data
        );
    }

    private function bookingFromSslRequest(Request $request): ?Booking
    {
        $reference = SSLCommerzPaymentService::referenceFromTranId((string) $request->input('tran_id', ''));
        return $reference !== '' ? Booking::where('booking_reference', $reference)->first() : null;
    }

    /** A booking that may still be paid for, or a redirect explaining why it cannot. */
    private function payableBooking(string $reference): Booking|RedirectResponse
    {
        $booking = Booking::where('booking_reference', $reference)->with(['property', 'room'])->firstOrFail();

        if ($booking->payment_status === 'paid') {
            return redirect()->to($this->signedConfirmationUrl($booking));
        }

        if (!$booking->isAwaitingPayment() || !$this->withinPaymentWindow($booking)) {
            return $this->toPending($booking, 'expired');
        }

        return $booking;
    }

    private function withinPaymentWindow(Booking $booking): bool
    {
        return !$booking->payment_expires_at || $booking->payment_expires_at->isFuture();
    }

    private function finish(Booking $booking, string $outcome): RedirectResponse
    {
        return match ($outcome) {
            BookingLifecycleService::PAID,
            BookingLifecycleService::ALREADY_PAID => redirect()->to($this->signedConfirmationUrl($booking)),
            BookingLifecycleService::NEEDS_REVIEW => $this->toPending($booking, 'review'),
            default                                => $this->toPending($booking, 'failed'),
        };
    }

    private function toPending(Booking $booking, string $state): RedirectResponse
    {
        return redirect()->to(URL::temporarySignedRoute(
            'payment.pending',
            now()->addHours(2),
            ['reference' => $booking->booking_reference, 'state' => $state]
        ));
    }

    private function signedConfirmationUrl(Booking $booking): string
    {
        return URL::temporarySignedRoute('booking.confirmation', now()->addHours(2), ['reference' => $booking->booking_reference]);
    }
}
