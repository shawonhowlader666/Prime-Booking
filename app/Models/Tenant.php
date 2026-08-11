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
}
