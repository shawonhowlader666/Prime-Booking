<?php

declare(strict_types=1);

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\PaymentTransaction;
use App\Services\AccountingService;
use App\Services\CouponService;
use App\Services\InventoryService;
use App\Services\NotificationService;
use App\Services\RewardPointService;
use App\Services\VIPLoyaltyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Every state change of a booking after it is created goes through here:
 * payment confirmed, payment failed, cancelled, expired unpaid.
 *
 * Each transition locks the booking row first, so gateway retries, a success
 * redirect racing an IPN, or a double click can never apply side effects
 * (ledger, points, coupon usage, notifications) twice.
 */
class BookingLifecycleService
{
    public const PAID             = 'confirmed';
    public const ALREADY_PAID     = 'already_paid';
    public const AMOUNT_MISMATCH  = 'amount_mismatch';
    public const NEEDS_REVIEW     = 'needs_review';

    public function __construct(
        private readonly InventoryService $inventory,
        private readonly CouponService $coupons,
        private readonly RewardPointService $rewards,
    ) {}

    /**
     * Simulated ("sandbox") payments exist for local development and automated
     * tests only. They are never available in production, whatever the gateway
     * settings say.
     */
    public static function simulationAllowed(): bool
    {
        if (app()->environment('production')) {
            return false;
        }

        return (bool) config('services.payments.allow_simulation', true);
    }

    /**
     * Record a verified payment and confirm the booking.
     * Callers must have verified the payment with the gateway before calling this.
     *
     * @return string One of the class constants.
     */
    public function confirmPayment(Booking $booking, string $gateway, ?string $txnId = null, ?float $paidAmount = null, array $raw = []): string
    {
        $outcome = DB::transaction(function () use ($booking, $gateway, $txnId, $paidAmount, $raw) {
            /** @var Booking $locked */
            $locked = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();

            if ($locked->payment_status === 'paid') {
                return self::ALREADY_PAID;
            }

            $expected = round((float) ($locked->total_price ?? $locked->total_amount ?? 0), 2);
            if ($paidAmount !== null && abs($paidAmount - $expected) > 1.0) {
                Log::warning("Payment amount mismatch for booking {$locked->booking_reference}", [
                    'expected' => $expected, 'paid' => $paidAmount, 'gateway' => $gateway,
                ]);
                $this->recordTransaction($locked, $gateway, $txnId, (float) $paidAmount, 'failed', $raw + ['reason' => 'amount_mismatch']);
                return self::AMOUNT_MISMATCH;
            }

            // Money arrived for a booking that was already cancelled or expired.
            // Re-admit it only if the rooms are still free; otherwise keep the money
            // visible for a refund instead of overbooking the hotel.
            if (in_array($locked->status, ['cancelled', 'failed', 'refunded'], true)) {
                $roomsFree = !$locked->room_id || $this->inventory->checkAvailability(
                    (int) $locked->room_id,
                    $locked->check_in->toDateString(),
                    $locked->check_out->toDateString(),
                    max(1, (int) $locked->rooms_count)
                )['is_available'];

                if (!$roomsFree) {
                    $locked->update(['payment_status' => 'refund_pending', 'transaction_id' => $txnId]);
                    $this->recordTransaction($locked, $gateway, $txnId, $paidAmount ?? $expected, 'verified', $raw + ['note' => 'paid_after_release']);
                    Log::critical("Payment received for released booking {$locked->booking_reference}; rooms no longer available. Refund required.");
                    return self::NEEDS_REVIEW;
                }

                // The cancellation gave these back; take them again.
                if ($locked->coupon_code) {
                    $this->coupons->recordUsage($locked->coupon_code);
                }
            }

            $locked->update([
                'payment_status'     => 'paid',
                'payment_method'     => $gateway,
                'transaction_id'     => $txnId,
                'status'             => 'confirmed',
                'payment_expires_at' => null,
                'cancelled_at'       => null,
            ]);

            $this->recordTransaction($locked, $gateway, $txnId, $paidAmount ?? $expected, 'verified', $raw);

            return self::PAID;
        });

        $booking->refresh();

        if ($outcome === self::PAID) {
            $this->afterConfirmed($booking, $gateway);
        }

        return $outcome;
    }

    /**
     * Mark an online payment as failed / abandoned and release everything the booking held.
     * No-op unless the booking is still waiting for its payment.
     */
    public function failPayment(Booking $booking, string $paymentStatus = 'failed', string $reason = 'Payment was not completed'): bool
    {
        return $this->release($booking, 'cancelled', $paymentStatus, $reason, requireAwaitingPayment: true);
    }

    /**
     * Whether a guest may still cancel, and what they get back.
     *
     * @return array{allowed:bool,refundable:bool,reason:?string}
     */
    public function cancellationTerms(Booking $booking): array
    {
        if (!in_array($booking->status, ['pending', 'confirmed'], true)) {
            return ['allowed' => false, 'refundable' => false, 'reason' => 'This reservation cannot be cancelled in its current state.'];
        }

        if ($booking->check_in && $booking->check_in->copy()->startOfDay()->lte(now()->startOfDay())) {
            return ['allowed' => false, 'refundable' => false, 'reason' => 'Reservations cannot be cancelled online on or after the check-in date. Please contact support.'];
        }

        $refundable = (bool) ($booking->room?->free_cancellation ?? $booking->property?->free_cancellation ?? false);

        return ['allowed' => true, 'refundable' => $refundable, 'reason' => null];
    }

    /**
     * Cancel a booking and unwind its side effects.
     *
     * @param bool|null $refund Force the refund decision; null = follow the cancellation policy.
     */
    public function cancel(Booking $booking, string $reason, ?bool $refund = null): bool
    {
        $refund ??= $this->cancellationTerms($booking)['refundable'];
        $wasPaid = $booking->payment_status === 'paid';

        $paymentStatus = $wasPaid ? ($refund ? 'refunded' : 'paid') : ($booking->isOfflinePayment() ? $booking->payment_status : 'cancelled');

        $done = $this->release($booking, 'cancelled', $paymentStatus, $reason, requireAwaitingPayment: false);

        if ($done && $wasPaid && $refund) {
            try {
                AccountingService::recordRefundLedger($booking->fresh(['property']), null, $reason);
            } catch (\Throwable $e) {
                Log::error("Refund ledger failed for {$booking->booking_reference}: " . $e->getMessage());
            }
        }

        if ($done) {
            try {
                app(NotificationService::class)->sendBookingCancellation($booking->fresh(['property']));
            } catch (\Throwable $e) {
                Log::warning("Cancellation notification failed for {$booking->booking_reference}: " . $e->getMessage());
            }
        }

        return $done;
    }

    /**
     * Cancel unpaid online bookings whose payment window has closed and prune stale holds.
     *
     * @return int Number of bookings released.
     */
    public function expireUnpaid(int $limit = 500): int
    {
        $released = 0;

        Booking::where('status', 'pending')
            ->where('payment_status', 'pending')
            ->whereNotIn('payment_method', Booking::OFFLINE_PAYMENT_METHODS)
            ->where(function ($q) {
                $q->where('payment_expires_at', '<=', now())
                  ->orWhere(function ($legacy) {
                      $legacy->whereNull('payment_expires_at')
                             ->where('created_at', '<=', now()->subMinutes(InventoryService::PAYMENT_WINDOW_MINUTES));
                  });
            })
            ->orderBy('id')
            ->limit($limit)
            ->get()
            ->each(function (Booking $booking) use (&$released) {
                if ($this->failPayment($booking, 'expired', 'Payment window expired')) {
                    $released++;
                }
            });

        $this->inventory->pruneExpiredHolds();

        return $released;
    }

    // ── Internals ────────────────────────────────────────────────────────

    private function release(Booking $booking, string $status, string $paymentStatus, string $reason, bool $requireAwaitingPayment): bool
    {
        $released = DB::transaction(function () use ($booking, $status, $paymentStatus, $reason, $requireAwaitingPayment) {
            /** @var Booking $locked */
            $locked = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();

            if ($requireAwaitingPayment && !$locked->isAwaitingPayment()) {
                return false;
            }

            if (!in_array($locked->status, ['pending', 'confirmed'], true)) {
                return false;
            }

            $note = trim((string) $locked->special_requests);
            $locked->update([
                'status'             => $status,
                'payment_status'     => $paymentStatus,
                'payment_expires_at' => null,
                'cancelled_at'       => now(),
                'special_requests'   => mb_substr(($note !== '' ? $note . ' | ' : '') . $reason . ' (' . now()->format('M d, Y H:i') . ')', 0, 2000),
            ]);

            if ($locked->coupon_code) {
                $this->coupons->releaseUsage($locked->coupon_code);
            }

            $this->rewards->refundRedeemedPoints($locked);
            $this->rewards->reverseEarnedPoints($locked);

            return true;
        });

        if ($released) {
            $booking->refresh();
            $this->inventory->releaseInventory($booking);
            if ($booking->user_id) {
                app(VIPLoyaltyService::class)->clearUserCache((int) $booking->user_id);
            }
        }

        return $released;
    }

    private function afterConfirmed(Booking $booking, string $gateway): void
    {
        $booking->loadMissing(['property', 'room']);

        try {
            AccountingService::recordBookingLedger($booking, $gateway);
        } catch (\Throwable $e) {
            Log::error("Ledger write failed for booking {$booking->booking_reference}: " . $e->getMessage());
        }

        try {
            $this->rewards->creditBookingPoints($booking);
        } catch (\Throwable $e) {
            Log::warning("Reward credit failed for booking {$booking->booking_reference}: " . $e->getMessage());
        }

        if ($booking->user_id) {
            app(VIPLoyaltyService::class)->clearUserCache((int) $booking->user_id);
        }

        $this->inventory->lockAndDeduct($booking);
        $this->sendConfirmation($booking);
    }

    /** Guest e-mail (queued) plus SMS alerts. Failures are logged, never thrown. */
    public function sendConfirmation(Booking $booking): void
    {
        try {
            if (!empty($booking->guest_email)) {
                Mail::to($booking->guest_email)
                    ->queue(new \App\Mail\BookingConfirmationMail($booking, $booking->property, $booking->room));
            }
        } catch (\Throwable $e) {
            Log::warning("Confirmation e-mail failed for {$booking->booking_reference}: " . $e->getMessage());
        }

        try {
            app(NotificationService::class)->sendBookingConfirmation($booking);
        } catch (\Throwable $e) {
            Log::warning("Confirmation SMS failed for {$booking->booking_reference}: " . $e->getMessage());
        }
    }

    private function recordTransaction(Booking $booking, string $gateway, ?string $txnId, float $amount, string $status, array $raw): void
    {
        try {
            PaymentTransaction::updateOrCreate(
                ['booking_id' => $booking->id, 'txn_id' => $txnId ?: ('NOID-' . $booking->booking_reference)],
                [
                    'gateway_code'     => mb_substr($gateway, 0, 32),
                    'amount'           => $amount,
                    'currency'         => $booking->currency ?: 'BDT',
                    'status'           => $status,
                    'gateway_response' => $raw,
                    'ip_address'       => request()?->ip(),
                    'user_agent'       => mb_substr((string) request()?->userAgent(), 0, 500),
                    'verified_at'      => $status === 'verified' ? now() : null,
                ]
            );
        } catch (\Throwable $e) {
            Log::error("Payment transaction audit failed for {$booking->booking_reference}: " . $e->getMessage());
        }
    }
}
