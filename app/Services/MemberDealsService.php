<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Property;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * MemberDealsService — Agoda-Style Member Secret Deals & VIP Flash Sales Engine.
 * 
 * Features:
 * - Dynamic 5% to 15% Member-Exclusive Secret Discount
 * - VIP Tier stacking for Silver, Gold, Platinum, and Diamond travelers
 * - Lock/Unlock state resolver for guest vs authenticated users
 */
class MemberDealsService
{
    private const BASE_MEMBER_DISCOUNT_PERCENT = 8.0; // 8% Member Secret Deal

    /**
     * Resolve dynamic member pricing and deal tags for a property/room.
     */
    public function getDealDetails(float $basePrice, ?User $user = null): array
    {
        $isMember = ($user !== null);

        $memberDiscountPercent = self::BASE_MEMBER_DISCOUNT_PERCENT;
        $vipDiscountPercent = 0.0;
        $tierName = 'Guest';

        if ($isMember) {
            $vipService = app(VIPLoyaltyService::class);
            $vipStats = $vipService->getUserTier($user);
            $tierName = $vipStats['tier'] ?? 'Bronze';
            $vipDiscountPercent = (float) ($vipStats['discount_percent'] ?? 0.0);
        }

        // Total combined discount
        $totalDiscountPercent = $isMember
            ? min(25.0, $memberDiscountPercent + $vipDiscountPercent)
            : 0.0;

        $secretSavings = round(($basePrice * $totalDiscountPercent) / 100, 2);
        $finalPrice = max(0.0, $basePrice - $secretSavings);

        // Calculate potential savings for unauthenticated guests to boost signup conversion
        $potentialSavings = round(($basePrice * self::BASE_MEMBER_DISCOUNT_PERCENT) / 100, 2);

        return [
            'is_member'               => $isMember,
            'original_price'          => $basePrice,
            'final_price'             => $finalPrice,
            'savings_amount'          => $secretSavings,
            'discount_percent'        => $totalDiscountPercent,
            'tier_name'               => $tierName,
            'badge_text'              => $isMember ? "MEMBER SECRET DEAL (-{$totalDiscountPercent}%)" : 'SECRET DEAL AVAILABLE',
            'unlock_cta'              => $isMember ? null : "Sign in to unlock extra ৳" . number_format($potentialSavings) . " savings",
            'has_active_deal'         => $isMember && $secretSavings > 0,
        ];
    }
}
