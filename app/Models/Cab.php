<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cab extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'hotel_id',
        'car_name',
        'car_number',
        'car_type',
        'fuel_type',
        'seat_capacity',
        'driver_name',
        'driver_phone',
        'base_fare',
        'status',
        'is_active',
    ];

    protected $casts = [
        'seat_capacity' => 'integer',
        'base_fare' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(CabBooking::class);
    }
}
