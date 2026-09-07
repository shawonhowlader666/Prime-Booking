<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'currency',
        'tax_percentage',
        'service_charge_percentage',
        'check_in_time',
        'check_out_time',
        'sync_api_key',
        'sync_webhook_secret',
        'is_active',
        'subscription_plan',
        'subscription_status',
        'subscription_expires_at',
        'monthly_fee',
        'prime_booking_property_id',
        'is_subscription_locked',
    ];

    protected $casts = [
        'tax_percentage' => 'decimal:2',
        'service_charge_percentage' => 'decimal:2',
        'monthly_fee' => 'decimal:2',
        'is_active' => 'boolean',
        'is_subscription_locked' => 'boolean',
        'subscription_expires_at' => 'datetime',
    ];

    public function isSubscriptionActive(): bool
    {
        if ($this->is_subscription_locked) {
            return false;
        }

        if (in_array($this->subscription_status, ['EXPIRED', 'SUSPENDED'])) {
            return false;
        }

        if ($this->subscription_expires_at && $this->subscription_expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function daysUntilExpiry(): int
    {
        if (!$this->subscription_expires_at) {
            return 365;
        }

        return (int) max(0, now()->diffInDays($this->subscription_expires_at, false));
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(TenantSubscription::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function ratePlans(): HasMany
    {
        return $this->hasMany(RatePlan::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function folios(): HasMany
    {
        return $this->hasMany(Folio::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function housekeepingTasks(): HasMany
    {
        return $this->hasMany(HousekeepingTask::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function syncEvents(): HasMany
    {
        return $this->hasMany(SyncEvent::class);
    }
}
