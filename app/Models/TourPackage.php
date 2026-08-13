<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TourPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'title',
        'slug',
        'destination',
        'duration_days',
        'duration_nights',
        'price_per_person',
        'discount_price',
        'featured_image',
        'gallery_images',
        'inclusions',
        'highlights',
        'itinerary',
        'status',
        'max_seats',
        'available_seats',
    ];

    protected $casts = [
        'gallery_images'   => 'array',
        'inclusions'       => 'array',
        'highlights'       => 'array',
        'itinerary'        => 'array',
        'price_per_person' => 'decimal:2',
        'discount_price'   => 'decimal:2',
        'duration_days'    => 'integer',
        'duration_nights'  => 'integer',
        'max_seats'        => 'integer',
        'available_seats'  => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (TourPackage $package) {
            if (empty($package->slug)) {
                $package->slug = Str::slug($package->title) . '-' . Str::random(5);
            }
        });
    }

    /** Scope active packages */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('status', 'active')
              ->orWhere('is_active', true);
        });
    }

    /** Scope ordered packages */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->latest();
    }

    /** Scope vendor packages */
    public function scopeForVendor(Builder $query, int $vendorId): Builder
    {
        return $query->where('vendor_id', $vendorId);
    }

    /** Scope destination keyword */
    public function scopeDestination(Builder $query, string $dest): Builder
    {
        return $query->where('destination', 'LIKE', "%{$dest}%")
                     ->orWhere('title', 'LIKE', "%{$dest}%");
    }

    /** Relation to Vendor (User) */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
