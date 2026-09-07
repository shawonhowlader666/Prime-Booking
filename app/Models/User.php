<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'hotel_id',
        'department_id',
        'name',
        'email',
        'role',
        'designation',
        'salary',
        'joining_date',
        'avatar',
        'phone',
        'about_me',
        'is_active',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'joining_date' => 'date',
            'salary' => 'decimal:2',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function rosters(): HasMany
    {
        return $this->hasMany(DutyRoster::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(EmployeeLeave::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'SUPERADMIN';
    }

    public function isHotelOwner(): bool
    {
        return in_array($this->role, ['SUPERADMIN', 'HOTEL_OWNER'], true);
    }

    public function isManager(): bool
    {
        return in_array($this->role, ['SUPERADMIN', 'HOTEL_OWNER', 'MANAGER'], true);
    }

    public function isReceptionist(): bool
    {
        return in_array($this->role, ['SUPERADMIN', 'HOTEL_OWNER', 'MANAGER', 'RECEPTIONIST'], true);
    }

    public function isHousekeeper(): bool
    {
        return $this->role === 'HOUSEKEEPER';
    }
}
