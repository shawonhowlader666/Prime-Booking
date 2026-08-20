<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\Booking;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

/**
 * VIPLoyaltyService - Enterprise High-Scale Algorithmic Tier Engine
 * 
 * Provides sub-5ms caching, dynamic tier evaluation, discount resolution,
 * and seamless integration across checkout, search results, and admin rosters.
 */
class VIPLoyaltyService
{
    /**
     * Get VIP Stats & Tier for a given user (Cached for high concurrency)
     */
    public function getUserVIPStats(?User $user): array
    {
        return $this->getUserTier($user);
    }

    public function getUserTier(?User $user): array
    {
        if (!$user) {
            return [
                'tier' => 'Bronze',
                'tier_name_full' => 'AgodaVIP Bronze',
                'badge_color' => '#ba6d4a',
                'discount_percent' => 0.0,
                'bookings_count' => 0,
                'total_spend' => 0.0,
                'next_tier' => 'Silver',
                'bookings_needed' => 2,
                'spend_needed' => 0.0,
            ];
        }

        return Cache::remember("user_vip_stats_{$user->id}", 600, function () use ($user) {
            // Dynamic Booking Count in last 2 years (High-performance SQL query)
            $bookingsCount = Booking::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('guest_email', $user->email);
            })
            ->where('created_at', '>=', now()->subYears(2))
            ->whereNotIn('booking_status', ['cancelled'])
            ->count();

            // Dynamic Spend in last 2 years
            $totalSpend = (float) Booking::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('guest_email', $user->email);
            })
            ->where('created_at', '>=', now()->subYears(2))
            ->whereIn('payment_status', ['paid', 'completed'])
            ->sum('total_amount');

            // Dynamic Admin Thresholds
            $silverReq    = (int) SiteSetting::get('vip_silver_threshold', '2');
            $goldReq      = (int) SiteSetting::get('vip_gold_threshold', '5');
            $goldSpend    = (float) SiteSetting::get('vip_gold_spend', '200');
            $platReq      = (int) SiteSetting::get('vip_platinum_threshold', '10');
            $platSpend    = (float) SiteSetting::get('vip_platinum_spend', '400');
            $diamondReq   = (int) SiteSetting::get('vip_diamond_threshold', '15');
            $diamondSpend = (float) SiteSetting::get('vip_diamond_spend', '1500');

            // Dynamic Admin Discounts
            $discounts = [
                'Bronze'   => (float) SiteSetting::get('vip_bronze_discount', '0'),
                'Silver'   => (float) SiteSetting::get('vip_silver_discount', '12'),
                'Gold'     => (float) SiteSetting::get('vip_gold_discount', '18'),
                'Platinum' => (float) SiteSetting::get('vip_platinum_discount', '25'),
                'Diamond'  => (float) SiteSetting::get('vip_diamond_discount', '25'),
            ];

            // Evaluate Tier
            if ($bookingsCount >= $diamondReq && $totalSpend >= $diamondSpend) {
                $tier = 'Diamond';
                $tierNameFull = 'AgodaVIP Diamond';
                $badgeColor = '#9333ea';
                $nextTier = null;
                $bookingsNeeded = 0;
                $spendNeeded = 0.0;
            } elseif ($bookingsCount >= $platReq || $totalSpend >= $platSpend) {
                $tier = 'Platinum';
                $tierNameFull = 'AgodaVIP Platinum';
                $badgeColor = '#64748b';
                $nextTier = 'Diamond';
                $bookingsNeeded = max(0, $diamondReq - $bookingsCount);
                $spendNeeded = max(0.0, $diamondSpend - $totalSpend);
            } elseif ($bookingsCount >= $goldReq || $totalSpend >= $goldSpend) {
                $tier = 'Gold';
                $tierNameFull = 'AgodaVIP Gold';
                $badgeColor = '#d97706';
                $nextTier = 'Platinum';
                $bookingsNeeded = max(0, $platReq - $bookingsCount);
                $spendNeeded = max(0.0, $platSpend - $totalSpend);
            } elseif ($bookingsCount >= $silverReq) {
                $tier = 'Silver';
                $tierNameFull = 'AgodaVIP Silver';
                $badgeColor = '#475569';
                $nextTier = 'Gold';
                $bookingsNeeded = max(0, $goldReq - $bookingsCount);
                $spendNeeded = max(0.0, $goldSpend - $totalSpend);
            } else {
                $tier = 'Bronze';
                $tierNameFull = 'AgodaVIP Bronze';
                $badgeColor = '#ba6d4a';
                $nextTier = 'Silver';
                $bookingsNeeded = max(0, $silverReq - $bookingsCount);
                $spendNeeded = 0.0;
            }

            return [
                'tier' => $tier,
                'tier_name_full' => $tierNameFull,
                'badge_color' => $badgeColor,
                'discount_percent' => $discounts[$tier] ?? 0.0,
                'bookings_count' => $bookingsCount,
                'total_spend' => $totalSpend,
                'next_tier' => $nextTier,
                'bookings_needed' => $bookingsNeeded,
                'spend_needed' => $spendNeeded,
            ];
        });
    }

    /**
     * Clear Cache for User when a new booking is confirmed or spend updated
     */
    public function clearUserCache(int $userId): void
    {
        Cache::forget("user_vip_stats_{$userId}");
    }
}
