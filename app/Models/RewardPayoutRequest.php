<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardPayoutRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points',
        'amount',
        'payment_gateway',
        'account_number',
        'account_name',
        'status',
        'admin_note',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'amount'       => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
