<?php

namespace App\Traits;

use App\Models\Hotel;
use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    /**
     * Boot the BelongsToTenant trait for the model.
     */
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (empty($model->hotel_id) && Auth::check() && Auth::user()->hotel_id) {
                $model->hotel_id = Auth::user()->hotel_id;
            }
        });
    }

    /**
     * Get the hotel/tenant that owns the record.
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }
}
