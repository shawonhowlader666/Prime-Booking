<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A short-lived checkout hold on room inventory. Expired rows are ignored by
 * availability checks and pruned by the bookings:expire-pending command.
 */
class RoomHold extends Model
{
    protected $fillable = ['room_id', 'token', 'check_in', 'check_out', 'rooms_count', 'expires_at'];

    protected $casts = [
        'check_in'   => 'date',
        'check_out'  => 'date',
        'expires_at' => 'datetime',
    ];
}
