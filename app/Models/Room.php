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
        'available_rooms',
        'breakfast_included',
        'free_cancellation',
        'facilities',
        'images',
        'view_type',
        'bathroom_count',
        'bathroom_features',
        'smoking_policy',
        'balcony_type',
        'extra_bed_allowed',
        'status',
    ];

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
}
