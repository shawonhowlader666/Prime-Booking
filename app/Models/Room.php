<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    public const TYPE_STANDARD     = 'standard';
    public const TYPE_DELUXE       = 'deluxe';
    public const TYPE_SUPER_DELUXE = 'super_deluxe';
    public const TYPE_SUITE        = 'suite';

    protected static function booted(): void
    {
        static::saved(function (Room $room) {
            if ($room->wasChanged('total_rooms')) {
                RoomAvailability::where('room_id', $room->id)
                    ->where('available_qty', '>', (int) $room->total_rooms)
                    ->update(['available_qty' => (int) $room->total_rooms]);
            }

            if ($room->property_id) {
                Property::where('id', $room->property_id)->update(['updated_at' => now()]);
            }
        });

        static::deleted(function (Room $room) {
            if ($room->property_id) {
                Property::where('id', $room->property_id)->update(['updated_at' => now()]);
            }
        });
    }

    protected $fillable = [
        'property_id',
        'name',
        'room_type',
        'capacity',
        'max_guests',
        'max_adults',
        'max_children',
        'room_size_sqm',
        'bed_type',
        'price_per_night',
        'total_rooms',
        'available_rooms',
        'breakfast_included',
        'free_cancellation',
        'cancellation_policy',
        'facilities',
        'amenities',
        'images',
        'view_type',
        'has_ocean_view',
        'bathroom_count',
        'bathroom_features',
        'smoking_policy',
        'balcony_type',
        'extra_bed_allowed',
        'status',
    ];

    public function setCapacityAttribute($value): void
    {
        $this->attributes['max_guests'] = (int)$value;
        $this->attributes['max_adults'] = (int)$value;
    }

    public function setRoomTypeAttribute($value): void
    {
        if (empty($this->attributes['name'])) {
            $this->attributes['name'] = $value;
        }
    }

    public function setAvailableRoomsAttribute($value): void
    {
        if (!isset($this->attributes['total_rooms'])) {
            $this->attributes['total_rooms'] = (int)$value;
        }
    }

    public function setAmenitiesAttribute($value): void
    {
        $this->attributes['facilities'] = is_array($value) ? json_encode($value) : $value;
    }

    public function setHasOceanViewAttribute($value): void
    {
        if ($value) {
            $this->attributes['view_type'] = 'Sea View / Ocean Front';
        }
    }

    protected $casts = [
        'facilities' => 'array',
        'images' => 'array',
        'bathroom_features' => 'array',
        'breakfast_included' => 'boolean',
        'free_cancellation' => 'boolean',
        'extra_bed_allowed' => 'boolean',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function availabilities()
    {
        return $this->hasMany(RoomAvailability::class);
    }

    /**
     * Scope for active rooms only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Formatted Size in Square Meters & Feet
     */
    public function getFormattedSizeAttribute(): string
    {
        $sqm = $this->room_size_sqm ?: 46;
        $sqft = round($sqm * 10.764);
        return "{$sqm} m²/{$sqft} ft²";
    }

    /**
     * Primary Room Image URL resolved via CDN / MediaService.
     */
    public function getImageUrlAttribute(): string
    {
        $first = is_array($this->images) && !empty($this->images) ? $this->images[0] : null;
        return media_url($first, $this->property?->image_url ?? null);
    }

    /**
     * All Room Gallery URLs resolved via CDN / MediaService.
     */
    public function getGalleryUrlsAttribute(): array
    {
        $list = is_array($this->images) ? $this->images : [];
        return array_map(fn($img) => media_url($img), $list);
    }

    /**
     * Images attribute accessor: ensures all room image paths are resolved to fully-qualified CDN / media URLs.
     */
    public function getImagesAttribute($value): array
    {
        $arr = is_string($value) ? (json_decode($value, true) ?: []) : (is_array($value) ? $value : []);
        return array_values(array_filter(array_map(fn($img) => media_url($img), $arr)));
    }
}
