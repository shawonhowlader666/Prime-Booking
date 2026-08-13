<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_name',
        'email',
        'phone',
        'saas_plan',
        'commission_rate',
        'status',
        'notes',
    ];

    protected $casts = [
        'commission_rate' => 'float',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'vendor_id', 'id');
    }

    public function rooms()
    {
        return $this->hasManyThrough(Room::class, Property::class, 'vendor_id', 'property_id', 'id', 'id');
    }
}
