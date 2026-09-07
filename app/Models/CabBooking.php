<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CabBooking extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'hotel_id',
        'cab_id',
        'reservation_id',
        'guest_id',
        'customer_name',
        'customer_phone',
        'booking_date',
        'booking_time',
        'transport_type',
        'pickup_location',
        'drop_location',
        'fare_amount',
        'payment_status',
        'status',
        'driver_name',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'date:Y-m-d',
        'fare_amount' => 'decimal:2',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function cab(): BelongsTo
    {
        return $this->belongsTo(Cab::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }
}
