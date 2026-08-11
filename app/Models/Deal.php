<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'discount_pct',
        'original_price',
        'sale_price',
        'valid_until',
        'image_url',
        'badge_text',
        'link_url',
        'type',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'valid_until'    => 'datetime',
        'is_active'      => 'boolean',
        'discount_pct'   => 'decimal:2',
        'original_price' => 'decimal:2',
        'sale_price'     => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(function ($q) {
                         $q->whereNull('valid_until')->orWhere('valid_until', '>=', now());
                     });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc');
    }
}
