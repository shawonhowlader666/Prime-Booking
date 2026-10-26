<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'guest_name',
        'rating',
        'comment',
        'helpful_count',
        'unhelpful_count',
        'status',
        'vendor_reply',
        'replied_at',
    ];

    protected $casts = [
        'rating'          => 'float',
        'helpful_count'   => 'integer',
        'unhelpful_count' => 'integer',
        'replied_at'      => 'datetime',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
