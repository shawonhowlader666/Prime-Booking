<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_reference', 'booking_code', 'user_id', 'property_id', 'room_id',
        'guest_name', 'guest_email', 'guest_phone',
        'check_in', 'check_out', 'check_in_date', 'check_out_date', 'nights', 'nights_count', 'guests', 'rooms', 'rooms_count', 'adults', 'children', 'adults_count', 'children_count',
        'price_per_night', 'subtotal', 'tax_amount', 'total_price', 'amount',
        'total_amount',   // legacy compat
        'currency', 'payment_status', 'payment_method',
        'status', 'booking_status', // both supported
        'special_requests',
        'coupon_code', 'discount_amount', 'commission_rate', 'commission_amount', 'vendor_payout_amount',
    ];

    protected $casts = [
        'check_in'       => 'date',
        'check_out'      => 'date',
        'total_price'    => 'decimal:2',
        'total_amount'   => 'decimal:2',
        'price_per_night'=> 'decimal:2',
        'subtotal'       => 'decimal:2',
        'tax_amount'     => 'decimal:2',
        'created_at'     => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function addons()
    {
        return $this->hasMany(BookingAddon::class);
    }

    // ─── Accessors ───────────────────────────────────────────────────────

    /** Unified status — supports both 'status' and 'booking_status' columns */
    public function getEffectiveStatusAttribute(): string
    {
        return $this->status ?? $this->booking_status ?? 'pending';
    }

    /** Total amount — supports both 'total_price' and 'total_amount' columns */
    public function getAmountAttribute(): float
    {
        return (float)($this->total_price ?? $this->total_amount ?? 0);
    }

    /** Number of nights booked */
    public function getNightsCountAttribute(): int
    {
        if ($this->check_in && $this->check_out) {
            $diff = (int) \Carbon\Carbon::parse($this->check_in)->diffInDays(\Carbon\Carbon::parse($this->check_out));
            if ($diff > 0) return $diff;
        }
        return $this->nights > 0 ? (int) $this->nights : 1;
    }

    /** Status badge CSS class for views */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->effective_status) {
            'confirmed', 'completed' => 'confirmed',
            'pending'                => 'pending',
            'cancelled'              => 'cancelled',
            default                  => 'pending',
        };
    }

    // ─── Scopes ──────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled'])->whereNotIn('booking_status', ['cancelled']);
    }

    public function scopePending($query)
    {
        return $query->where(fn($q) => $q->where('status','pending')->orWhere('booking_status','pending'));
    }
}
