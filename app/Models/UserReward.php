<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points_balance',
        'total_earned_points',
        'total_redeemed_points',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
