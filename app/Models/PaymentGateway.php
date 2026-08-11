<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway_code',
        'name',
        'is_active',
        'is_sandbox',
        'api_key',
        'api_secret',
        'merchant_id',
        'settings',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'is_sandbox' => 'boolean',
        'settings'   => 'array',
    ];
}
