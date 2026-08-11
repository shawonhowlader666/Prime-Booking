<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'name',
        'max_guests',
        'max_adults',
        'max_children',
        'room_size_sqm',
        'bed_type',
        'price_per_night',
        'total_rooms',
        'breakfast_included',
        'free_cancellation',
        'facilities',
        'images',
    ];

    protected $casts = [
        'facilities' => 'array',
        'images' => 'array',
        'breakfast_included' => 'boolean',
        'free_cancellation' => 'boolean',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
