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
        'check_in', 'check_out', 'check_in_date', 'check_out_date', 'nights', 'nights_count', 'guests', 'guests_count', 'rooms', 'rooms_count', 'adults', 'children', 'adults_count', 'children_count',
        'price_per_night', 'subtotal', 'tax_amount', 'total_price', 'amount',
        'total_amount', 'payable_amount',   // legacy compat
        'currency', 'payment_status', 'payment_method', 'transaction_id',
        'status', 'booking_status', // both supported
        'special_requests',
        'coupon_code', 'discount_amount', 'commission_rate', 'commission_amount', 'vendor_payout_amount', 'vendor_payout',
        'vip_discount_amount', 'points_redeemed', 'points_discount_amount', 'addons_total',
        'payment_expires_at', 'cancelled_at',
        'created_at', 'updated_at',
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
        'payment_expires_at' => 'datetime',
        'cancelled_at'   => 'datetime',
    ];

    /** Payment methods settled at the property, not through an online gateway. */
    public const OFFLINE_PAYMENT_METHODS = ['cash', 'pay_at_hotel', 'bank_transfer'];

    public function isOfflinePayment(): bool
    {
        return in_array($this->payment_method, self::OFFLINE_PAYMENT_METHODS, true);
    }

    /** True while the booking is waiting for an online payment that can still arrive. */
    public function isAwaitingPayment(): bool
    {
        return $this->status === 'pending' && $this->payment_status === 'pending' && !$this->isOfflinePayment();
    }

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

    // ─── Accessors & Mutators ─────────────────────────────────────────────

    public function setBookingCodeAttribute($value): void
    {
        $this->attributes['booking_reference'] = $value;
    }

    public function getBookingCodeAttribute(): ?string
    {
        return $this->attributes['booking_reference'] ?? null;
    }

    public function setGuestsCountAttribute($value): void
    {
        $this->attributes['guests'] = $value;
    }

    public function getGuestsCountAttribute(): int
    {
        return (int)($this->attributes['guests'] ?? (($this->attributes['adults'] ?? 1) + ($this->attributes['children'] ?? 0)));
    }

    public function setVendorPayoutAttribute($value): void
    {
        $this->attributes['vendor_payout_amount'] = $value;
    }

    protected static function booted(): void
    {
        static::saving(function (Booking $booking) {
            if ($booking->total_price !== null && empty($booking->total_amount)) {
                $booking->total_amount = $booking->total_price;
            } elseif ($booking->total_amount !== null && empty($booking->total_price)) {
                $booking->total_price = $booking->total_amount;
            }

            // `status` and `booking_status` are legacy twins. Keep them identical on every write,
            // otherwise availability and reporting queries disagree about the same booking.
            if ($booking->isDirty('status') && $booking->status !== null) {
                $booking->booking_status = $booking->status;
            } elseif ($booking->isDirty('booking_status') && $booking->booking_status !== null) {
                $booking->status = $booking->booking_status;
            } elseif ($booking->status !== null && empty($booking->booking_status)) {
                $booking->booking_status = $booking->status;
            } elseif ($booking->booking_status !== null && empty($booking->status)) {
                $booking->status = $booking->booking_status;
            }

            if ($booking->nights !== null && empty($booking->nights_count)) {
                $booking->nights_count = $booking->nights;
            } elseif ($booking->nights_count !== null && empty($booking->nights)) {
                $booking->nights = $booking->nights_count;
            }
        });
    }

    public function getVendorPayoutAttribute()
    {
        return $this->attributes['vendor_payout_amount'] ?? 0.0;
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
