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
