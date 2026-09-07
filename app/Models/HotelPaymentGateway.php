<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelPaymentGateway extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'hotel_id',
        'gateway',
        'is_enabled',
        'environment',
        'currency_code',
        'endpoint_url',
        'publishable_key',
        'secret_key',
        'webhook_secret',
        'additional_config',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'additional_config' => 'array',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }
}
