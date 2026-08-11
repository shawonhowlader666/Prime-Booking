<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'country',
        'country_code',
        'latitude',
        'longitude',
        'image',
        'is_popular',
    ];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
