<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
    protected $fillable = [
        'title', 'subtitle', 'badge_text', 'cta_text', 'cta_link',
        'image_url', 'icon', 'bg_color', 'bg_color_end', 'text_color',
        'badge_bg', 'type', 'target_type', 'target_city',
        'vendor_id', 'property_id',
        'is_active', 'is_featured', 'sort_order', 'starts_at', 'ends_at',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
        'starts_at'   => 'datetime',
        'ends_at'     => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────

    /** Only currently active promotions (active flag + date range check) */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeGlobal(Builder $query): Builder
    {
        return $query->whereNull('vendor_id');
    }

    public function scopeForVendor(Builder $query, int $vendorId): Builder
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('is_featured');
    }

    // ─── Accessors ────────────────────────────────────────────────────────

    /** CSS gradient string for background */
    public function getGradientCssAttribute(): string
    {
        if ($this->bg_color_end) {
            return "linear-gradient(135deg, {$this->bg_color}, {$this->bg_color_end})";
        }
        return $this->bg_color;
    }

    /** Full CTA URL — route or absolute URL */
    public function getCtaUrlAttribute(): string
    {
        if (!$this->cta_link) {
            return url('/hotels');
        }
        if (str_starts_with($this->cta_link, 'http')) {
            return $this->cta_link;
        }
        // Named route
        try {
            return route($this->cta_link);
        } catch (\Throwable $e) {
            return url($this->cta_link);
        }
    }

    public function getIsLiveAttribute(): bool
    {
        return $this->is_active
            && ($this->starts_at === null || $this->starts_at->isPast())
            && ($this->ends_at === null || $this->ends_at->isFuture());
    }

    public function getStatusLabelAttribute(): string
    {
        if (!$this->is_active) return 'Inactive';
        if ($this->starts_at && $this->starts_at->isFuture()) return 'Scheduled';
        if ($this->ends_at && $this->ends_at->isPast()) return 'Expired';
        return 'Live';
    }
}
