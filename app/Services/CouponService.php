<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class CouponService
{
    /**
     * Validate coupon code against order subtotal and rules.
     */
    public function validateCoupon(string $code, float $subtotal, ?int $propertyId = null, ?int $userId = null): array
    {
        $cleanCode = strtoupper(trim($code));

        if (empty($cleanCode)) {
            return ['valid' => false, 'discount' => 0.0, 'message' => 'Please enter a coupon code.'];
        }

        $coupon = Coupon::where('code', $cleanCode)->first();

        if (!$coupon) {
            return ['valid' => false, 'discount' => 0.0, 'message' => "Coupon '{$cleanCode}' is invalid."];
        }

        if ($coupon->status !== 'active') {
            return ['valid' => false, 'discount' => 0.0, 'message' => "This coupon is no longer active."];
        }

        if ($coupon->expires_at && Carbon::parse($coupon->expires_at)->endOfDay()->isPast()) {
            return ['valid' => false, 'discount' => 0.0, 'message' => "This coupon expired on " . Carbon::parse($coupon->expires_at)->format('M d, Y') . "."];
        }

        // Property / vendor scoped coupons only apply to their own inventory.
        if (!empty($coupon->property_id) && $propertyId !== null && (int) $coupon->property_id !== $propertyId) {
            return ['valid' => false, 'discount' => 0.0, 'message' => 'This coupon is not valid for the selected property.'];
        }

        if (!empty($coupon->vendor_id) && empty($coupon->property_id) && $propertyId !== null) {
            $ownerId = \App\Models\Property::whereKey($propertyId)->value('vendor_id');
            if ((int) $ownerId !== (int) $coupon->vendor_id) {
                return ['valid' => false, 'discount' => 0.0, 'message' => 'This coupon is not valid for the selected property.'];
            }
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return ['valid' => false, 'discount' => 0.0, 'message' => "This coupon has reached its maximum usage limit."];
        }

        if ($coupon->min_spend > 0 && $subtotal < (float)$coupon->min_spend) {
            return [
                'valid' => false,
                'discount' => 0.0,
                'message' => "Minimum booking amount of ৳ " . number_format((float)$coupon->min_spend) . " required for this coupon.",
            ];
        }

        // Calculate discount
        $discount = 0.0;
        if (in_array(strtolower((string)$coupon->type), ['percentage', 'percent'])) {
            $discount = round(($subtotal * (float)$coupon->amount) / 100, 2);
        } else {
            $discount = min($subtotal, (float)$coupon->amount);
        }

        return [
            'valid'          => true,
            'code'           => $coupon->code,
            'type'           => $coupon->type,
            'rate'           => (float)$coupon->amount,
            'discount'       => $discount,
            'min_spend'      => (float)$coupon->min_spend,
            'formatted_disc' => '৳ ' . number_format($discount),
            'message'        => "Coupon applied successfully! You saved ৳ " . number_format($discount) . ".",
        ];
    }

    /**
     * Alias for validateCoupon for seamless compatibility.
     */
    public function validate(string $code, float $subtotal, ?int $propertyId = null, ?int $userId = null): array
    {
        $res = $this->validateCoupon($code, $subtotal, $propertyId, $userId);
        $res['discount_amount'] = $res['discount'] ?? 0.0;
        return $res;
    }

    /**
     * Claim one use of a coupon. The conditional UPDATE is atomic, so two
     * simultaneous checkouts can never push used_count past usage_limit.
     *
     * @return bool false when the coupon ran out between validation and booking.
     */
    public function recordUsage(string $code): bool
    {
        $cleanCode = strtoupper(trim($code));

        $claimed = Coupon::where('code', $cleanCode)
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                  ->orWhere('usage_limit', 0)
                  ->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->increment('used_count');

        return $claimed > 0;
    }

    /** Give a use back when the booking that consumed it is cancelled or expires unpaid. */
    public function releaseUsage(?string $code): void
    {
        $cleanCode = strtoupper(trim((string) $code));
        if ($cleanCode === '') {
            return;
        }

        Coupon::where('code', $cleanCode)->where('used_count', '>', 0)->decrement('used_count');
    }
}
