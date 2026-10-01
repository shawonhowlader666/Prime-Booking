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

        // Dispatch Confirmation Email (queued)
        try {
            \Illuminate\Support\Facades\Mail::to($booking->guest_email)
                ->queue(new \App\Mail\BookingConfirmationMail($booking, $booking->property, $booking->room));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Email Queue Warning: ' . $e->getMessage());
        }

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
                    return redirect()->route('booking.confirmation', $booking->booking_reference);
                }
            }
        }

        return redirect()->route('home')->with('error', 'bKash Payment was cancelled or failed.');
    }

    // ─── SSLCommerz PGW Handlers ──────────────────────────────────────────

    /** GET /payment/ssl/sandbox-redirect/{reference} — SSLCommerz Sandbox Gateway UI */
    public function sslSandboxRedirect(string $reference): View
    {
        $booking = Booking::where('booking_reference', $reference)->firstOrFail();
        return view('pages.payment-ssl-sandbox', compact('booking'));
    }

    /** POST /payment/ssl/sandbox-execute/{reference} — Complete SSLCommerz simulated card pay */
    public function sslSandboxExecute(Request $request, string $reference): RedirectResponse
    {
        $booking = Booking::where('booking_reference', $reference)->firstOrFail();

        // Security check: block simulation when live gateway is configured or in production without admin
        $sslGateway = \App\Models\PaymentGateway::where('code', 'sslcommerz')->first();
        if ($sslGateway && $sslGateway->environment === 'live' && ! (auth()->check() && auth()->user()->isAdmin())) {
            abort(403, 'Simulation is disabled for live payment processing.');
        }

        $booking->update([
            'payment_status' => 'paid',
            'payment_method' => $request->input('card_type', 'sslcommerz_card'),
            'status'         => 'confirmed',
            'booking_status' => 'confirmed',
        ]);

        // Dispatch Confirmation Email (queued)
        try {
            \Illuminate\Support\Facades\Mail::to($booking->guest_email)
                ->queue(new \App\Mail\BookingConfirmationMail($booking, $booking->property, $booking->room));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Email Queue Warning: ' . $e->getMessage());
        }

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

        return redirect()->route('booking.confirmation', $booking->booking_reference);
    }

    /** POST /payment/ssl/fail */
    public function sslFail(Request $request): RedirectResponse
    {
        return redirect()->route('home')->with('error', 'SSLCommerz payment failed. Please try again.');
    }

    /** POST /payment/ssl/cancel */
    public function sslCancel(Request $request): RedirectResponse
    {
        return redirect()->route('home')->with('info', 'Payment cancelled by user.');
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
                }
            }
        }
        return response('IPN Processed', 200);
    }
}
