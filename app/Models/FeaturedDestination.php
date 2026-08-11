<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class FeaturedDestination extends Model
{
    protected $fillable = [
        'city', 'country', 'image_url', 'description',
        'property_count_override', 'min_price_override',
        'is_active', 'is_featured', 'sort_order',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
    ];

    // ─── Scopes ───────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    // ─── Accessors ────────────────────────────────────────────────────────

    public function getNameAttribute(): string
    {
        return $this->attributes['city'] ?? '';
    }

    /** Live property count from DB (or override) */
    public function getPropertyCountAttribute(): int
    {
        if ($this->property_count_override) {
            return $this->property_count_override;
        }
        return Property::where('city', 'like', "%{$this->city}%")
            ->where(fn($q) => $q->where('status','active')->orWhere('status','published'))
            ->count();
    }

    public function getMinPriceAttribute(): float
    {
        if ($this->min_price_override) {
            return $this->min_price_override;
        }
        return (float)(Property::where('city', 'like', "%{$this->city}%")
            ->where(fn($q) => $q->where('status','active')->orWhere('status','published'))
            ->min('price_per_night') ?? 0);
    }
}
