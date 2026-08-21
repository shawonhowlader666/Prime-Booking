<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountingLedger extends Model
{
    protected $table = 'accounting_ledgers';

    protected $fillable = [
        'txn_reference',
        'type',
        'category',
        'booking_id',
        'vendor_id',
        'property_id',
        'user_id',
        'gross_amount',
        'commission_amount',
        'gateway_fee',
        'net_amount',
        'payment_method',
        'currency',
        'status',
        'description',
        'metadata',
    ];

    protected $casts = [
        'gross_amount'      => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'gateway_fee'       => 'decimal:2',
        'net_amount'        => 'decimal:2',
        'metadata'          => 'array',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
