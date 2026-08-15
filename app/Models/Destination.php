<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Destination extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'tagline',
        'description',
        'country',
        'image_url',
        'image',
        'video_url',
        'sort_order',
        'is_featured',
        'is_active',
    ];

    public function setImageAttribute($value): void
    {
        $this->attributes['image_url'] = $value;
    }

    protected $casts = [
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
        'sort_order'  => 'integer',
    ];

    /**
     * Get real count of properties registered in this destination city.
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'city', 'name');
    }
}
