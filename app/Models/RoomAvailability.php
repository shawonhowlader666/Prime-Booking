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
        'available_qty',
        'is_blocked',
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
}
