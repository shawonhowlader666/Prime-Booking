<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'status',
        'phone', 'avatar', 'country', 'city', 'dob', 'gender',
        'passport_number', 'passport_expiry',
        'email_verified_at',
        'last_login_at', 'last_login_ip',
        'total_bookings', 'total_spent',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
            'total_spent'       => 'decimal:2',
        ];
    }

    // ─── Role Helpers ────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isBanned(): bool
    {
        return $this->status === 'banned';
    }

    // ─── Accessors ────────────────────────────────────────────────────────

    /** Role badge color for admin UI */
    public function getRoleBadgeColorAttribute(): string
    {
        return match($this->role) {
            'super_admin' => '#7367f0',
            'admin'       => '#1890ff',
            'vendor'      => '#28c76f',
            default       => '#8c8c8c',
        };
    }

    /** Status badge class */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'active'   => 'confirmed',
            'inactive' => 'pending',
            'banned'   => 'cancelled',
            default    => 'pending',
        };
    }

    /** Avatar URL with fallback to UI Avatars */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return str_starts_with($this->avatar, 'http') ? $this->avatar
                : asset('storage/' . $this->avatar);
        }
        $initial = urlencode(substr($this->name, 0, 2));
        return "https://ui-avatars.com/api/?name={$initial}&background=1890ff&color=fff&size=40&rounded=true";
    }

    // ─── Relationships ────────────────────────────────────────────────────

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'vendor_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVendors($query)
    {
        return $query->where('role', 'vendor');
    }

    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    public function scopeAdmins($query)
    {
        return $query->whereIn('role', ['admin', 'super_admin']);
    }

    // ─── Track Login ──────────────────────────────────────────────────────

    public function recordLogin(string $ip): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }
}
