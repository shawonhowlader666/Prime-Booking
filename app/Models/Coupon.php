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
        'type', // fixed or percentage
        'amount',
        'min_spend',
        'expires_at',
        'usage_limit',
        'used_count',
        'status',
    ];
}
