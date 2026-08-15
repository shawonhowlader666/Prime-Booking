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
        if ($coupon->type === 'percentage') {
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
     * Increment coupon usage count upon successful booking.
     */
    public function recordUsage(string $code): void
    {
        $cleanCode = strtoupper(trim($code));
        $coupon = Coupon::where('code', $cleanCode)->first();
        if ($coupon) {
            $coupon->increment('used_count');
        }
    }
}
