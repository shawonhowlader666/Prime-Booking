<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'hotel_id',
        'category_id',
        'unit_id',
        'supplier_id',
        'name',
        'sku',
        'purchase_price',
        'unit_price',
        'current_stock',
        'min_alert_stock',
        'description',
        'is_active',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'min_alert_stock' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(ItemUnit::class, 'unit_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(ItemSupplier::class, 'supplier_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'item_id');
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->current_stock <= $this->min_alert_stock;
    }

    public function getStockValuationAttribute(): float
    {
        return (float) ($this->current_stock * $this->purchase_price);
    }
}
