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

    public function setIsActiveAttribute($value): void
    {
        $this->attributes['status'] = $value ? 'active' : 'inactive';
    }
}
