<?php

namespace App\Services;

use App\Models\User;
use App\Models\Booking;
use App\Models\UserReward;
use App\Models\RewardTransaction;
use App\Models\RewardPayoutRequest;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RewardPointService
{
    /**
     * Get dynamic conversion settings with defaults:
     * - 1,000 BDT spent = 1 Point
     * - 1 Point = 10 BDT Value
     * - Minimum 100 Points required to withdraw / redeem
     */
    public function getSpendPerPoint(): float
    {
        return (float) SiteSetting::get('reward_spend_per_point', 1000);
    }

    public function getPointValueInBdt(): float
    {
        return (float) SiteSetting::get('reward_point_value_bdt', 10);
    }

    public function getMinRedemptionPoints(): int
    {
        return (int) SiteSetting::get('reward_min_redemption_points', 100);
    }

    /**
     * Calculate points earned from spend
     */
    public function calculatePoints(float $amount): int
    {
        $spendRate = max(1, $this->getSpendPerPoint());
        return (int) floor($amount / $spendRate);
    }

    /**
     * Convert points to cash value in BDT
     */
    public function convertPointsToBdt(int $points): float
    {
        return round($points * $this->getPointValueInBdt(), 2);
    }

    /**
     * Get or initialize user reward wallet summary
     */
    public function getUserRewardSummary(?User $user): array
    {
        if (!$user) {
            return [
                'points_balance'         => 0,
                'bdt_value'              => 0.00,
                'min_threshold'          => $this->getMinRedemptionPoints(),
                'min_threshold_bdt'      => $this->convertPointsToBdt($this->getMinRedemptionPoints()),
                'can_withdraw'           => false,
                'progress_percent'       => 0,
                'total_earned_points'    => 0,
                'total_redeemed_points'  => 0,
                'spend_rate'             => $this->getSpendPerPoint(),
                'point_value'            => $this->getPointValueInBdt(),
            ];
        }

        $wallet = UserReward::firstOrCreate(
            ['user_id' => $user->id],
            ['points_balance' => 0, 'total_earned_points' => 0, 'total_redeemed_points' => 0]
        );

        $balance = (int) $wallet->points_balance;
        $minThreshold = $this->getMinRedemptionPoints();
        $bdtValue = $this->convertPointsToBdt($balance);
        $progress = min(100, round(($balance / max(1, $minThreshold)) * 100));

        return [
            'points_balance'         => $balance,
            'bdt_value'              => $bdtValue,
            'min_threshold'          => $minThreshold,
            'min_threshold_bdt'      => $this->convertPointsToBdt($minThreshold),
            'can_withdraw'           => ($balance >= $minThreshold),
            'progress_percent'       => $progress,
            'total_earned_points'    => (int) $wallet->total_earned_points,
            'total_redeemed_points'  => (int) $wallet->total_redeemed_points,
            'spend_rate'             => $this->getSpendPerPoint(),
            'point_value'            => $this->getPointValueInBdt(),
        ];
    }

    /**
     * Credit Reward Points upon successful booking completion
     */
    public function creditBookingPoints(Booking $booking): ?int
    {
        if (!$booking->user_id) {
            return null;
        }

        $paidAmount = (float) $booking->total_amount;
        $earnedPoints = $this->calculatePoints($paidAmount);

        if ($earnedPoints <= 0) {
            return 0;
        }

        // Prevent duplicate crediting for same booking
        $existing = RewardTransaction::where('user_id', $booking->user_id)
            ->where('booking_id', $booking->id)
            ->where('type', 'earned')
            ->exists();

        if ($existing) {
            return 0;
        }

        return DB::transaction(function () use ($booking, $earnedPoints) {
            $wallet = UserReward::firstOrCreate(
                ['user_id' => $booking->user_id],
                ['points_balance' => 0, 'total_earned_points' => 0, 'total_redeemed_points' => 0]
            );

            $wallet->increment('points_balance', $earnedPoints);
            $wallet->increment('total_earned_points', $earnedPoints);

            $amountValue = $this->convertPointsToBdt($earnedPoints);

            RewardTransaction::create([
                'user_id'      => $booking->user_id,
                'booking_id'   => $booking->id,
                'type'         => 'earned',
                'points'       => $earnedPoints,
                'amount_value' => $amountValue,
                'description'  => "Earned +{$earnedPoints} Rewards for Booking #{$booking->booking_reference}",
                'status'       => 'completed',
            ]);

            return $earnedPoints;
        });
    }

    /**
     * Redeem Points at checkout to reduce order bill
     */
    public function redeemPointsAtCheckout(User $user, int $pointsToRedeem, ?int $bookingId = null): array
    {
        $wallet = UserReward::where('user_id', $user->id)->first();
        $currentBalance = (int) ($wallet?->points_balance ?? 0);
        $minThreshold = $this->getMinRedemptionPoints();

        if ($pointsToRedeem < $minThreshold) {
            return [
                'success' => false,
                'message' => "Minimum {$minThreshold} Points required to redeem.",
                'discount' => 0,
            ];
        }

        if ($pointsToRedeem > $currentBalance) {
            return [
                'success' => false,
                'message' => "Insufficient points balance (Available: {$currentBalance} Points).",
                'discount' => 0,
            ];
        }

        $discountBdt = $this->convertPointsToBdt($pointsToRedeem);
        $validBookingId = ($bookingId && Booking::where('id', $bookingId)->exists()) ? $bookingId : null;

        return DB::transaction(function () use ($user, $wallet, $pointsToRedeem, $discountBdt, $validBookingId) {
            $wallet->decrement('points_balance', $pointsToRedeem);
            $wallet->increment('total_redeemed_points', $pointsToRedeem);

            RewardTransaction::create([
                'user_id'      => $user->id,
                'booking_id'   => $validBookingId,
                'type'         => 'redeemed',
                'points'       => -$pointsToRedeem,
                'amount_value' => $discountBdt,
                'description'  => "Redeemed -{$pointsToRedeem} Points (-৳{$discountBdt}) at Checkout",
                'status'       => 'completed',
            ]);

            return [
                'success'  => true,
                'message'  => "Successfully redeemed {$pointsToRedeem} Points (-৳{$discountBdt})!",
                'discount' => $discountBdt,
                'points'   => $pointsToRedeem,
            ];
        });
    }

    /**
     * Request Cash Payout / Withdrawal (bKash / Nagad / Bank)
     */
    public function requestPayout(User $user, int $points, string $gateway, string $accountNumber, ?string $accountName = null): array
    {
        $wallet = UserReward::where('user_id', $user->id)->first();
        $currentBalance = (int) ($wallet?->points_balance ?? 0);
        $minThreshold = $this->getMinRedemptionPoints();

        if ($points < $minThreshold) {
            return [
                'success' => false,
                'message' => "Minimum withdrawal threshold is {$minThreshold} Points (= ৳" . $this->convertPointsToBdt($minThreshold) . ").",
            ];
        }

        if ($points > $currentBalance) {
            return [
                'success' => false,
                'message' => "Requested points ({$points}) exceed current balance ({$currentBalance} Points).",
            ];
        }

        $payoutAmount = $this->convertPointsToBdt($points);

        return DB::transaction(function () use ($user, $wallet, $points, $payoutAmount, $gateway, $accountNumber, $accountName) {
            // Deduct from active balance and hold
            $wallet->decrement('points_balance', $points);
            $wallet->increment('total_redeemed_points', $points);

            $payout = RewardPayoutRequest::create([
                'user_id'         => $user->id,
                'points'          => $points,
                'amount'          => $payoutAmount,
                'payment_gateway' => $gateway,
                'account_number'  => $accountNumber,
                'account_name'    => $accountName,
                'status'          => 'pending',
            ]);

            RewardTransaction::create([
                'user_id'      => $user->id,
                'type'         => 'payout',
                'points'       => -$points,
                'amount_value' => $payoutAmount,
                'description'  => "Requested Payout #{$payout->id} to {$gateway} ({$accountNumber})",
                'status'       => 'pending',
            ]);

            return [
                'success' => true,
                'message' => "Payout request for ৳{$payoutAmount} ({$points} Points) submitted successfully! Admin will process via {$gateway}.",
                'payout'  => $payout,
            ];
        });
    }

    /**
     * Admin: Approve Payout Request
     */
    public function approvePayout(RewardPayoutRequest $payout, ?string $adminNote = null): bool
    {
        if ($payout->status !== 'pending') {
            return false;
        }

        return DB::transaction(function () use ($payout, $adminNote) {
            $payout->update([
                'status'       => 'approved',
                'admin_note'   => $adminNote ?: 'Approved & Dispatched by Admin',
                'processed_at' => now(),
            ]);

            RewardTransaction::where('user_id', $payout->user_id)
                ->where('type', 'payout')
                ->where('points', -$payout->points)
                ->where('status', 'pending')
                ->update(['status' => 'completed']);

            return true;
        });
    }

    /**
     * Admin: Reject Payout Request & Refund Points to User
     */
    public function rejectPayout(RewardPayoutRequest $payout, string $reason): bool
    {
        if ($payout->status !== 'pending') {
            return false;
        }

        return DB::transaction(function () use ($payout, $reason) {
            $payout->update([
                'status'       => 'rejected',
                'admin_note'   => $reason,
                'processed_at' => now(),
            ]);

            // Refund points to user
            $wallet = UserReward::firstOrCreate(['user_id' => $payout->user_id]);
            $wallet->increment('points_balance', $payout->points);
            $wallet->decrement('total_redeemed_points', $payout->points);

            RewardTransaction::create([
                'user_id'      => $payout->user_id,
                'type'         => 'admin_adjustment',
                'points'       => $payout->points,
                'amount_value' => $payout->amount,
                'description'  => "Refunded Payout #{$payout->id} ({$reason})",
                'status'       => 'completed',
            ]);

            return true;
        });
    }
}
