<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirportTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'pickup_location',
        'dropoff_location',
        'vehicle_type',
        'price',
        'capacity',
        'luggage_capacity',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price'     => 'float',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
