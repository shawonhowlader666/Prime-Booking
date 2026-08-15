<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'date',
        'price',
        'price_override',
        'available_qty',
        'available_count',
        'available_rooms',
        'is_blocked',
        'is_closed',
        'is_available',
    ];

    protected $casts = [
        'date'       => 'date',
        'price'      => 'decimal:2',
        'is_blocked' => 'boolean',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Dynamic Aliases for ultra-compatibility
    public function setPriceOverrideAttribute($value): void
    {
        $this->attributes['price'] = $value;
    }

    public function getPriceOverrideAttribute()
    {
        return $this->attributes['price'] ?? null;
    }

    public function setAvailableCountAttribute($value): void
    {
        $this->attributes['available_qty'] = $value;
    }

    public function getAvailableCountAttribute()
    {
        return $this->attributes['available_qty'] ?? null;
    }

    public function setAvailableRoomsAttribute($value): void
    {
        $this->attributes['available_qty'] = $value;
    }

    public function getAvailableRoomsAttribute()
    {
        return $this->attributes['available_qty'] ?? null;
    }

    public function setIsClosedAttribute($value): void
    {
        $this->attributes['is_blocked'] = (bool)$value;
    }

    public function getIsClosedAttribute(): bool
    {
        return (bool)($this->attributes['is_blocked'] ?? false);
    }

    public function setIsAvailableAttribute($value): void
    {
        $this->attributes['is_blocked'] = !(bool)$value;
    }

    public function getIsAvailableAttribute(): bool
    {
        return !(bool)($this->attributes['is_blocked'] ?? false);
    }
}
