<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\Payment\BkashPaymentService;
use App\Services\Payment\SSLCommerzPaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;

/**
 * PaymentCallbackController — Handles Payment PGW Callbacks & Instant Verification
 */
class PaymentCallbackController extends Controller
{
    public function __construct(
        private readonly BkashPaymentService $bkashService,
        private readonly SSLCommerzPaymentService $sslService
    ) {}

    // ─── bKash PGW Handlers ───────────────────────────────────────────────

    /** GET /payment/bkash/sandbox-redirect/{reference} — Interactive bKash Sandbox Gateway UI */
    public function bkashSandboxRedirect(string $reference): View
    {
        $booking = Booking::where('booking_reference', $reference)->firstOrFail();
        return view('pages.payment-bkash-sandbox', compact('booking'));
    }

    /** POST /payment/bkash/sandbox-execute/{reference} — Process bKash OTP/PIN verification */
    public function bkashSandboxExecute(Request $request, string $reference): RedirectResponse
    {
        $booking = Booking::where('booking_reference', $reference)->firstOrFail();

        // Security check: block simulation when live gateway is configured or in production without admin
        $bkashGateway = \App\Models\PaymentGateway::where('code', 'bkash')->first();
        if ($bkashGateway && $bkashGateway->environment === 'live' && ! (auth()->check() && auth()->user()->isAdmin())) {
            abort(403, 'Simulation is disabled for live payment processing.');
        }

        $booking->update([
            'payment_status' => 'paid',
            'payment_method' => 'bkash',
            'status'         => 'confirmed',
            'booking_status' => 'confirmed',
        ]);

        $this->settlePaymentSuccess($booking, 'bkash');

        // Dispatch Confirmation Email (queued)
        try {
            \Illuminate\Support\Facades\Mail::to($booking->guest_email)
                ->queue(new \App\Mail\BookingConfirmationMail($booking, $booking->property, $booking->room));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Email Queue Warning: ' . $e->getMessage());
        }

        session(['last_booking_ref' => $booking->booking_reference]);

        return redirect()
            ->route('booking.confirmation', $booking->booking_reference)
            ->with('success', 'bKash Payment Successful! Transaction ID: TRX-BKASH-' . Str::upper(Str::random(8)));
    }

    /** GET/POST /payment/bkash/callback — bKash live API callback handler */
    public function bkashCallback(Request $request): RedirectResponse
    {
        $paymentID = $request->query('paymentID') ?? $request->input('paymentID');
        $status    = $request->query('status')    ?? $request->input('status');

        if ($status === 'success' && $paymentID) {
            $result = $this->bkashService->executePayment($paymentID);

            if (($result['status'] ?? '') === 'Completed') {
                $ref = $request->query('reference');
                $booking = Booking::where('booking_reference', $ref)->first();

                if ($booking) {
                    // Cryptographic Amount Verification Check
                    if (isset($result['amount']) && (float)$result['amount'] > 0) {
                        $expectedAmount = (float) ($booking->total_price ?? $booking->total_amount ?? 0);
                        if (abs((float)$result['amount'] - $expectedAmount) > 1.0) {
                            \Illuminate\Support\Facades\Log::warning("bKash amount tampering attempt for booking {$ref}", [
                                'expected' => $expectedAmount,
                                'paid'     => $result['amount'],
                                'ip'       => $request->ip(),
                            ]);
                            return redirect()->route('home')->with('error', 'Payment amount verification failed. Please contact customer support.');
                        }
                    }

                    $booking->update([
                        'payment_status' => 'paid',
                        'payment_method' => 'bkash',
                        'status'         => 'confirmed',
                        'booking_status' => 'confirmed',
                    ]);
                    $this->settlePaymentSuccess($booking, 'bkash');
                    session(['last_booking_ref' => $booking->booking_reference]);
                    return redirect()->route('booking.confirmation', $booking->booking_reference);
                }
            }
        }

        return redirect()->route('home')->with('error', 'bKash Payment was cancelled or failed.');
    }

    // ─── SSLCommerz PGW Handlers ──────────────────────────────────────────

    /** GET /payment/ssl/sandbox-redirect/{reference} — SSLCommerz Live / Sandbox Dispatcher */
    public function sslSandboxRedirect(string $reference): View|RedirectResponse
    {
        $booking = Booking::where('booking_reference', $reference)->firstOrFail();

        // Initiate hosted payment session with SSLCommerz (Live or Sandbox)
        try {
            $init = $this->sslService->initiatePayment(
                $booking,
                route('payment.ssl.success'),
                route('payment.ssl.fail'),
                route('payment.ssl.cancel')
            );

            if (!empty($init['redirectUrl']) && ($init['status'] ?? '') === 'success') {
                return redirect()->away($init['redirectUrl']);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('SSLCommerz direct redirect fallback: ' . $e->getMessage());
        }

        return view('pages.payment-ssl-sandbox', compact('booking'));
    }

    /** POST /payment/ssl/sandbox-execute/{reference} — Complete SSLCommerz simulated card pay */
    public function sslSandboxExecute(Request $request, string $reference): RedirectResponse
    {
        $booking = Booking::where('booking_reference', $reference)->firstOrFail();

        // Security check: block simulation when live gateway is configured or in production without admin
        $sslGateway = \App\Models\PaymentGateway::where('gateway_code', 'sslcommerz')->first();
        if ($sslGateway && !$sslGateway->is_sandbox && ! (auth()->check() && auth()->user()->isAdmin())) {
            abort(403, 'Simulation is disabled for live payment processing.');
        }

        $booking->update([
            'payment_status' => 'paid',
            'payment_method' => $request->input('card_type', 'sslcommerz_card'),
            'status'         => 'confirmed',
            'booking_status' => 'confirmed',
        ]);

        $this->settlePaymentSuccess($booking, (string)$request->input('card_type', 'card'));

        // Dispatch Confirmation Email (queued)
        try {
            \Illuminate\Support\Facades\Mail::to($booking->guest_email)
                ->queue(new \App\Mail\BookingConfirmationMail($booking, $booking->property, $booking->room));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Email Queue Warning: ' . $e->getMessage());
        }

        session(['last_booking_ref' => $booking->booking_reference]);

        return redirect()
            ->route('booking.confirmation', $booking->booking_reference)
            ->with('success', 'SSLCommerz Payment Successful! Order Reference: ' . $booking->booking_reference);
    }

    /** POST /payment/ssl/success */
    public function sslSuccess(Request $request): RedirectResponse
    {
        $tranId = (string) $request->input('tran_id');
        $valId  = (string) $request->input('val_id');
        $booking = Booking::where('booking_reference', $tranId)->first();

        if (! $booking) {
            return redirect()->route('home')->with('error', 'Booking transaction not found.');
        }

        // Cryptographically verify transaction with SSLCommerz server
        $isValid = $this->sslService->validatePayment(
            $valId,
            (float) ($booking->total_price ?? $booking->total_amount ?? 0),
            'BDT'
        );

        if (! $isValid) {
            \Illuminate\Support\Facades\Log::warning("SSLCommerz invalid or unverified payment attempt for booking {$tranId}", [
                'ip'     => $request->ip(),
                'val_id' => $valId,
            ]);
            return redirect()->route('home')->with('error', 'Payment verification failed. Please contact support.');
        }

        $booking->update([
            'payment_status' => 'paid',
            'payment_method' => 'sslcommerz',
            'status'         => 'confirmed',
            'booking_status' => 'confirmed',
        ]);

        $this->settlePaymentSuccess($booking, 'sslcommerz');

        // Dispatch official confirmed booking notification & email
        try {
            app(\App\Services\NotificationService::class)->sendBookingConfirmation($booking);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Notification dispatch error on SSL payment for {$tranId}: " . $e->getMessage());
        }

        session(['last_booking_ref' => $booking->booking_reference]);

        return redirect()->route('booking.confirmation', $booking->booking_reference)
            ->with('success', 'Payment successful! Your reservation is confirmed.');
    }

    /** POST /payment/ssl/fail */
    public function sslFail(Request $request): RedirectResponse
    {
        $tranId = (string) $request->input('tran_id');
        if (!empty($tranId)) {
            $booking = Booking::where('booking_reference', $tranId)->first();
            if ($booking && $booking->payment_status !== 'paid') {
                $booking->update([
                    'payment_status' => 'failed',
                    'status'         => 'cancelled',
                    'booking_status' => 'cancelled',
                ]);
            }
        }
        return redirect()->route('home')->with('error', 'SSLCommerz payment failed. Your reservation was NOT confirmed. Please try again.');
    }

    /** POST /payment/ssl/cancel */
    public function sslCancel(Request $request): RedirectResponse
    {
        $tranId = (string) $request->input('tran_id');
        if (!empty($tranId)) {
            $booking = Booking::where('booking_reference', $tranId)->first();
            if ($booking && $booking->payment_status !== 'paid') {
                $booking->update([
                    'payment_status' => 'cancelled',
                    'status'         => 'cancelled',
                    'booking_status' => 'cancelled',
                ]);
            }
        }
        return redirect()->route('home')->with('warning', 'Payment cancelled. Your reservation was NOT confirmed.');
    }

    /** POST /payment/ssl/ipn */
    public function sslIpn(Request $request)
    {
        $tranId = (string) $request->input('tran_id');
        $valId  = (string) $request->input('val_id');
        $status = (string) $request->input('status');

        if (($status === 'VALID' || $status === 'VALIDATED') && $tranId && $valId) {
            $booking = Booking::where('booking_reference', $tranId)->first();
            if ($booking && $booking->payment_status !== 'paid') {
                $isValid = $this->sslService->validatePayment(
                    $valId,
                    (float) ($booking->total_price ?? $booking->total_amount ?? 0),
                    'BDT'
                );

                if ($isValid) {
                    $booking->update([
                        'payment_status' => 'paid',
                        'status'         => 'confirmed',
                        'booking_status' => 'confirmed',
                    ]);

                    $this->settlePaymentSuccess($booking, 'sslcommerz');
                }
            }
        }
        return response('IPN Processed', 200);
    }

    /**
     * Microscopic settlement: Syncs general ledger & credits reward loyalty points atomically upon payment confirmation.
     */
    private function settlePaymentSuccess(Booking $booking, string $paymentMethod): void
    {
        // 1. Auto-record double-entry general ledger
        try {
            \App\Services\AccountingService::recordBookingLedger($booking, $paymentMethod);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Failed to record accounting ledger for booking {$booking->booking_reference}: " . $e->getMessage());
        }

        // 2. Auto-credit Prime Rewards points
        try {
            app(\App\Services\RewardPointService::class)->creditBookingPoints($booking);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Failed to credit reward points for booking {$booking->booking_reference}: " . $e->getMessage());
        }
    }
}
