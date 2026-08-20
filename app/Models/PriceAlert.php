<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PriceAlert — Real-time Hotel Rate & Deal Watcher.
 *
 * @property int         $id
 * @property int|null    $user_id
 * @property int         $property_id
 * @property string      $email
 * @property float|null  $target_price
 * @property float       $current_price_at_alert
 * @property string      $status
 */
class PriceAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'email',
        'target_price',
        'current_price_at_alert',
        'status',
        'last_notified_at',
    ];

    protected $casts = [
        'target_price'           => 'decimal:2',
        'current_price_at_alert' => 'decimal:2',
        'last_notified_at'       => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
