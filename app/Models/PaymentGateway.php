<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * PaymentGateway — Manages bKash, Nagad, SSLCommerz, and Pay-at-Hotel configurations.
 * API keys are encrypted at rest via Laravel's built-in 'encrypted' cast.
 */
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

    /**
     * Sensitive keys are automatically encrypted/decrypted by Laravel.
     * Plain text is never written to the database.
     */
    protected $casts = [
        'is_active'  => 'boolean',
        'is_sandbox' => 'boolean',
        'settings'   => 'array',
        'api_key'    => 'encrypted',
        'api_secret' => 'encrypted',
    ];

    /** @return Builder Active gateways only */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Helper: is this gateway in live (production) mode? */
    public function isLive(): bool
    {
        return !$this->is_sandbox;
    }
}
