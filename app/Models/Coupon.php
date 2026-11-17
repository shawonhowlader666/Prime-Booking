<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'property_id',
        'code',
        'type',
        'discount_type',
        'amount',
        'value',
        'discount_value',
        'min_spend',
        'expires_at',
        'usage_limit',
        'used_count',
        'status',
        'is_active',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'min_spend'  => 'decimal:2',
        'used_count' => 'integer',
        'usage_limit'=> 'integer',
        'expires_at' => 'date',
    ];

    public function setDiscountTypeAttribute($value): void
    {
        $this->attributes['type'] = $value;
    }

    public function setDiscountValueAttribute($value): void
    {
        $this->attributes['amount'] = $value;
    }

    public function setValueAttribute($value): void
    {
        $this->attributes['amount'] = $value;
    }

    public function getValueAttribute()
    {
        return $this->attributes['amount'] ?? 0;
    }

    public function setIsActiveAttribute($value): void
    {
        $this->attributes['status'] = $value ? 'active' : 'inactive';
    }
    /**
     * A site-wide code that can be advertised right now: exists, active, not expired,
     * not used up and not tied to one hotel. Null when the admin has switched it off.
     */
    public static function advertisable(string $code): ?self
    {
        $coupon = static::where('code', strtoupper(trim($code)))->where('status', 'active')->first();
        if (!$coupon || $coupon->property_id || $coupon->vendor_id) {
            return null;
        }
        if ($coupon->expires_at && $coupon->expires_at->copy()->endOfDay()->isPast()) {
            return null;
        }
        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return null;
        }
        return $coupon;
    }

    /** "10%" or "৳500" for display. */
    public function getDisplayAmountAttribute(): string
    {
        return in_array(strtolower((string) $this->type), ['percentage', 'percent'], true)
            ? rtrim(rtrim(number_format((float) $this->amount, 2), '0'), '.') . '%'
            : '৳' . number_format((float) $this->amount);
    }
}
