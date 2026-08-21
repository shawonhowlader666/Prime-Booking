<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * PaymentTransaction — Stores every gateway interaction for audit, dispute, and reconciliation.
 * gateway_response holds the full raw JSON from bKash / Nagad / SSLCommerz for dispute resolution.
 */
class PaymentTransaction extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'amount'           => 'decimal:2',
        'gateway_response' => 'array',
        'verified_at'      => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    // ── Query Scopes ───────────────────────────────────────────────

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('status', 'verified');
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    public function scopeForGateway(Builder $query, string $gateway): Builder
    {
        return $query->where('gateway_code', $gateway);
    }

    // ── Helpers ────────────────────────────────────────────────────

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
