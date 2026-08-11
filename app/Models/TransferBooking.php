<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_reference',
        'user_id',
        'transfer_id',
        'passenger_name',
        'passenger_phone',
        'passenger_email',
        'pickup_location',
        'dropoff_location',
        'pickup_datetime',
        'flight_number',
        'passengers',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'pickup_datetime' => 'datetime',
        'total_amount'    => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transfer()
    {
        return $this->belongsTo(AirportTransfer::class, 'transfer_id');
    }
}
