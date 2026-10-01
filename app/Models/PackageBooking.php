<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_reference',
        'package_id',
        'user_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'travel_date',
        'guests',
        'total_amount',
        'payment_method',
        'payment_status',
        'status',
        'special_requests',
    ];

    protected $casts = [
        'travel_date'  => 'date',
        'total_amount' => 'decimal:2',
        'guests'       => 'integer',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class, 'package_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
