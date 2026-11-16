<?php

declare(strict_types=1);

namespace App\Services\Booking;

use App\Models\Property;
use App\Models\Room;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\UserReward;
use App\Services\CouponService;
use App\Services\DynamicPricingService;
use App\Services\RewardPointService;
use App\Services\VIPLoyaltyService;
use Carbon\Carbon;

/**
 * The single source of truth for what a stay costs.
 *
 * The checkout page, the live quote endpoint and the booking write all call
 * quote() with the same inputs, so the amount a guest sees is always the
 * amount that is charged. Nothing in here trusts a price sent by the browser.
 *
 * Order of operations:
 *   subtotal        = nightly rates x rooms (dynamic pricing engine)
 *   - coupon        (a valid coupon replaces the VIP discount; they do not stack)
 *   - VIP discount  (only when no coupon is applied)
 *   - points        (reward points, capped so the stay never goes below zero)
 *   + add-ons
 *   + tax           (admin "tax_rate" setting, applied to the amount after discounts)
 */
class BookingPriceCalculator
{
    public const MAX_ROOMS_PER_BOOKING = 10;

    public function __construct(
        private readonly DynamicPricingService $pricing,
        private readonly CouponService $coupons,
        private readonly VIPLoyaltyService $vip,
        private readonly RewardPointService $rewards,
    ) {}

    /** Add-ons a guest can attach at checkout. Prices are computed server-side only. */
    public function availableAddons(int $guests, int $nights): array
    {
        return [
            'airport_transfer' => ['key' => 'airport_transfer', 'name' => 'Private Airport Pickup & Transfer', 'price' => 1500.0],
            'daily_breakfast'  => ['key' => 'daily_breakfast', 'name' => 'Buffet Breakfast (All Guests)', 'price' => 500.0 * max(1, $guests) * max(1, $nights)],
            'spa_package'      => ['key' => 'spa_package', 'name' => '60-Min Wellness Spa Voucher', 'price' => 2000.0],
        ];
    }

    public function taxRatePercent(): float
    {
        $rate = (float) SiteSetting::get('tax_rate', 7.5);
        return ($rate < 0 || $rate > 100) ? 7.5 : $rate;
    }

    /**
     * @param array<int,string> $addonKeys
     */
    public function quote(
        Property $property,
        ?Room $room,
        string $checkIn,
        string $checkOut,
        int $rooms = 1,
        int $guests = 1,
        ?string $couponCode = null,
        ?User $user = null,
        array $addonKeys = [],
        int $redeemPoints = 0,
    ): array {
        $start  = Carbon::parse($checkIn)->startOfDay();
        $end    = Carbon::parse($checkOut)->startOfDay();
        $nights = max(1, (int) $start->diffInDays($end));
        $rooms  = min(self::MAX_ROOMS_PER_BOOKING, max(1, $rooms));
        $guests = max(1, $guests);

        // ── Room charges ────────────────────────────────────────────────
        $breakdown = [];
        if ($room) {
            $stay          = $this->pricing->calculateStayPrice($room, $start->toDateString(), $end->toDateString(), $rooms);
            $subtotal      = (float) $stay['final_price'];
            $pricePerNight = (float) $stay['avg_nightly_rate'];
            $breakdown     = $stay['nightly_breakdown'] ?? [];
        } else {
            $pricePerNight = (float) ($property->price_per_night ?? 0);
            $subtotal      = $pricePerNight * $nights * $rooms;
        }
        $subtotal = round($subtotal, 2);

        // ── Coupon ──────────────────────────────────────────────────────
        $coupon = ['code' => null, 'valid' => false, 'discount' => 0.0, 'message' => null];
        $couponCode = $couponCode !== null ? trim($couponCode) : '';
        if ($couponCode !== '') {
            $check = $this->coupons->validateCoupon($couponCode, $subtotal, (int) $property->id, $user?->id);
            $coupon = [
                'code'     => $check['valid'] ? $check['code'] : strtoupper($couponCode),
                'valid'    => (bool) $check['valid'],
                'discount' => $check['valid'] ? round(min($subtotal, (float) $check['discount']), 2) : 0.0,
                'message'  => $check['message'] ?? null,
            ];
        }

        // ── VIP tier discount (never stacked on a coupon) ───────────────
        $vipStats    = $this->vip->getUserTier($user);
        $vipPercent  = (float) ($vipStats['discount_percent'] ?? 0);
        $vipDiscount = 0.0;
        if ($user && $vipPercent > 0 && !$coupon['valid']) {
            $vipDiscount = round(min($subtotal, $subtotal * $vipPercent / 100), 2);
        }

        $afterDiscounts = round(max(0, $subtotal - $coupon['discount'] - $vipDiscount), 2);

        // ── Reward points ───────────────────────────────────────────────
        $points = $this->resolvePointsRedemption($user, $redeemPoints, $afterDiscounts);

        // ── Add-ons ─────────────────────────────────────────────────────
        $catalog     = $this->availableAddons($guests, $nights);
        $addons      = [];
        $addonsTotal = 0.0;
        foreach (array_unique(array_map('strval', $addonKeys)) as $key) {
            if (isset($catalog[$key])) {
                $addons[]     = $catalog[$key];
                $addonsTotal += $catalog[$key]['price'];
            }
        }

        // ── Tax and total ───────────────────────────────────────────────
        $roomNet   = round(max(0, $afterDiscounts - $points['discount']), 2);
        $taxable   = round($roomNet + $addonsTotal, 2);
        $taxRate   = $this->taxRatePercent();
        $taxAmount = round($taxable * $taxRate / 100);
        $total     = round($taxable + $taxAmount, 2);

        // ── Commission is charged on what the guest pays for the room ───
        $commissionRate   = (float) ($property->commission_rate ?? 12.0);
        $commissionBase   = round(max(0, $subtotal - $coupon['discount'] - $vipDiscount), 2);
        $commissionAmount = round($commissionBase * $commissionRate / 100, 2);
        $vendorPayout     = round(max(0, $commissionBase - $commissionAmount), 2);

        return [
            'nights'             => $nights,
            'rooms'              => $rooms,
            'guests'             => $guests,
            'price_per_night'    => $pricePerNight,
            'subtotal'           => $subtotal,
            'nightly_breakdown'  => $breakdown,
            'coupon'             => $coupon,
            'vip'                => [
                'tier'     => $vipStats['tier'] ?? 'Bronze',
                'percent'  => $vipDiscount > 0 ? $vipPercent : 0.0,
                'discount' => $vipDiscount,
            ],
            'points'             => $points,
            'addons'             => $addons,
            'addons_total'       => round($addonsTotal, 2),
            'discount_total'     => round($coupon['discount'] + $vipDiscount + $points['discount'], 2),
            'taxable'            => $taxable,
            'tax_rate'           => $taxRate,
            'tax_amount'         => $taxAmount,
            'total'              => $total,
            'currency'           => 'BDT',
            'commission_rate'    => $commissionRate,
            'commission_amount'  => $commissionAmount,
            'vendor_payout'      => $vendorPayout,
            'earnable_points'    => $user ? $this->rewards->calculatePoints($total) : 0,
        ];
    }

    /**
     * Decide how many points can actually be redeemed for this stay.
     *
     * @return array{requested:int,redeemed:int,discount:float,balance:int,min:int,point_value:float,message:?string}
     */
    private function resolvePointsRedemption(?User $user, int $requested, float $payable): array
    {
        $pointValue = max(0.0, $this->rewards->getPointValueInBdt());
        $min        = $this->rewards->getMinRedemptionPoints();
        $balance    = $user ? (int) (UserReward::where('user_id', $user->id)->value('points_balance') ?? 0) : 0;

        $result = [
            'requested'   => max(0, $requested),
            'redeemed'    => 0,
            'discount'    => 0.0,
            'balance'     => $balance,
            'min'         => $min,
            'point_value' => $pointValue,
            'message'     => null,
        ];

        if (!$user || $requested <= 0 || $pointValue <= 0) {
            return $result;
        }

        if ($balance < $min) {
            $result['message'] = "You need at least {$min} points to redeem.";
            return $result;
        }

        $maxByPrice = (int) floor($payable / $pointValue);
        $redeem     = min($requested, $balance, $maxByPrice);

        if ($redeem < $min) {
            $result['message'] = "Minimum {$min} points per redemption.";
            return $result;
        }

        $result['redeemed'] = $redeem;
        $result['discount'] = round($redeem * $pointValue, 2);

        return $result;
    }
}
