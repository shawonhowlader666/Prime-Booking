<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccountingLedger;
use App\Models\Booking;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Services\AccountingService;
use App\Services\InventoryService;
use App\Services\NotificationService;
use App\Services\RewardPointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    /**
     * Universal Webhook / IPN Listener for all Payment Gateways:
     * - bKash (bKash Execute / Webhook)
     * - Nagad (Verify API Callback)
     * - SSLCommerz (IPN / Success URL)
     * - Stripe (Stripe Webhook Event)
     * - Bank Wire / Card Transfer
     */
    public function handleCallback(Request $request, string $gateway)
    {
        $gatewayModel = PaymentGateway::where('gateway_code', $gateway)->first();

        // Standardize reference extraction across gateways
        $reference = $request->input('tran_id')
            ?: ($request->input('paymentID')
            ?: ($request->input('val_id')
            ?: ($request->input('order_id')
            ?: $request->input('reference'))));

        $status = strtolower((string)$request->input('status', $request->input('transactionStatus', 'success')));
        $trxId  = $request->input('trx_id') ?: ($request->input('trxID') ?: ($request->input('bank_tran_id') ?: $request->input('id')));

        if (!$reference) {
            return response()->json(['success' => false, 'message' => 'Missing booking reference in IPN payload.'], 422);
        }

        $booking = Booking::where('booking_reference', $reference)
            ->with(['property', 'user', 'room'])
            ->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => "Booking reference {$reference} not found."], 404);
        }

        // A callback body proves nothing by itself: anyone can POST "status=success".
        // The payment must be confirmed with the gateway before any state changes.
        $verification = $this->verifyWithGateway($request, $gateway, $booking);
        if ($verification === null) {
            Log::warning("Unverified payment callback rejected for {$booking->booking_reference}", ['gateway' => $gateway, 'ip' => $request->ip()]);
            return response()->json(['success' => false, 'message' => 'Payment could not be verified with the gateway.'], 400);
        }
        $trxId = $verification['txn_id'] ?? $trxId;

        if (in_array($status, ['success', 'completed', 'valid', 'validated', 'paid', 'approved'])) {
            return DB::transaction(function () use ($booking, $gateway, $trxId, $request) {
                // 1. Idempotency Check: lock the row so concurrent gateway retries apply side-effects once
                $booking = Booking::whereKey($booking->id)->lockForUpdate()->with(['property', 'user', 'room'])->firstOrFail();
                if ($booking->payment_status === 'paid') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Booking already processed and confirmed.',
                        'data'    => ['reference' => $booking->booking_reference, 'payment_status' => 'paid'],
                    ]);
                }

                // 2. Mark Booking Paid & Confirmed
                $booking->update([
                    'payment_status' => 'paid',
                    'status'         => 'confirmed',
                    'payment_method' => $gateway,
                ]);

                // 3. Record Audit in PaymentTransaction
                PaymentTransaction::create([
                    'booking_id'        => $booking->id,
                    'gateway_code'      => $gateway,
                    'txn_id'            => $trxId ?? ('TRX-' . strtoupper(bin2hex(random_bytes(4)))),
                    'amount'            => $booking->total_price ?? $booking->amount,
                    'currency'          => $booking->currency ?? 'BDT',
                    'status'            => 'verified',
                    'verified_at'       => now(),
                    'gateway_response'  => $request->all(),
                ]);

                // 4. Auto-Record Double-Entry General Ledger with HMAC Seal
                $gross    = round((float) ($booking->total_price ?? $booking->amount ?? 0), 2);
                $rate     = (float) ($booking->property?->commission_rate ?? AccountingService::DEFAULT_COMMISSION_RATE);
                $comm     = round($gross * ($rate / 100.0), 2);
                $feeRate  = in_array($gateway, ['bkash', 'nagad', 'rocket']) ? AccountingService::FEE_MOBILE_BANKING : AccountingService::FEE_CARD;
                $fee      = round($gross * $feeRate, 2);
                $net      = round(max(0, $gross - $comm - $fee), 2);

                $auditSeal = hash_hmac('sha256', "TXN-BK-{$booking->id}|{$booking->property?->vendor_id}|{$gross}|" . now()->toIso8601String(), config('app.key') ?: 'prime_secret');

                AccountingLedger::updateOrCreate(
                    ['booking_id' => $booking->id],
                    [
                        'txn_reference'     => 'TXN-BK-' . str_pad((string) $booking->id, 6, '0', STR_PAD_LEFT),
                        'type'              => 'credit',
                        'category'          => 'hotel_booking',
                        'vendor_id'         => $booking->property?->vendor_id,
                        'property_id'       => $booking->property_id,
                        'user_id'           => $booking->user_id,
                        'gross_amount'      => $gross,
                        'commission_amount' => $comm,
                        'gateway_fee'       => $fee,
                        'net_amount'        => $net,
                        'payment_method'    => $gateway,
                        'currency'          => $booking->currency ?? 'BDT',
                        'status'            => 'completed',
                        'description'       => "Payment confirmed via {$gateway} for #{$booking->booking_reference}",
                        'metadata'          => [
                            'gateway'            => $gateway,
                            'trx_id'             => $trxId,
                            'cryptographic_seal' => $auditSeal,
                        ],
                    ]
                );

                // 5. Auto-Credit Reward Points to User Wallet
                try {
                    app(RewardPointService::class)->creditBookingPoints($booking);
                } catch (\Throwable $e) {
                    Log::warning("Points auto-credit warning: " . $e->getMessage());
                }

                // 6. Auto-Lock and Deduct Room Inventory
                try {
                    app(InventoryService::class)->lockAndDeduct($booking);
                } catch (\Throwable $e) {
                    Log::warning("Inventory lock warning: " . $e->getMessage());
                }

                // 7. Dispatch Multi-Channel Notifications (SMS, Email)
                try {
                    app(NotificationService::class)->sendBookingConfirmation($booking);
                } catch (\Throwable $e) {
                    Log::warning("Notification dispatch warning: " . $e->getMessage());
                }

                return response()->json([
                    'success' => true,
                    'message' => "Payment verified via {$gateway}. Ledger, Points, and Notifications automated seamlessly.",
                    'data'    => [
                        'reference'      => $booking->booking_reference,
                        'payment_status' => 'paid',
                        'status'         => 'confirmed',
                        'gross_amount'   => $gross,
                        'vendor_payout'  => $net,
                    ],
                ]);
            });
        }

        return response()->json(['success' => false, 'message' => 'Payment transaction was not successful.'], 400);
    }

    /**
     * Confirm the payment server-to-server with the gateway.
     *
     * @return array{txn_id:?string}|null null when the payment cannot be verified.
     */
    private function verifyWithGateway(Request $request, string $gateway, Booking $booking): ?array
    {
        $expected = round((float) ($booking->total_price ?? $booking->total_amount ?? 0), 2);

        if ($gateway === 'sslcommerz') {
            $data = app(\App\Services\Payment\SSLCommerzPaymentService::class)->fetchValidation((string) $request->input('val_id', ''));
            if ($data
                && \App\Services\Payment\SSLCommerzPaymentService::referenceFromTranId((string) ($data['tran_id'] ?? '')) === $booking->booking_reference
                && abs((float) ($data['currency_amount'] ?? $data['amount'] ?? 0) - $expected) <= 1.0) {
                return ['txn_id' => (string) ($data['bank_tran_id'] ?? $data['tran_id'])];
            }
        }

        if ($gateway === 'bkash' && $request->filled('paymentID')) {
            $result = app(\App\Services\Payment\BkashPaymentService::class)->queryPayment((string) $request->input('paymentID'));
            if (($result['status'] ?? '') === 'Completed'
                && ($result['invoice'] ?? null) === $booking->booking_reference
                && abs((float) ($result['amount'] ?? 0) - $expected) <= 1.0) {
                return ['txn_id' => $result['trxID'] ?? null];
            }
        }

        // Local development and automated tests may post simulated callbacks. Never in production.
        if (\App\Services\Booking\BookingLifecycleService::simulationAllowed()) {
            return ['txn_id' => null];
        }

        return null;
    }
}

