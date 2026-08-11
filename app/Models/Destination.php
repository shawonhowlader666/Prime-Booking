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
        'image_url',
        'video_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get real count of properties registered in this destination city.
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'city', 'name');
    }
}
