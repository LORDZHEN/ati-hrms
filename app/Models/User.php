<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'email',
        'password',
        'role',
        'phone',
        'purok_street',
        'region_id',
        'barangay_id',
        'city_id',
        'province_id',
        'profile_photo_path',
        'e_signature',
        'position',
        'employment_status',
        'department',
        'status',
        'birthday',
        'email_verified_at',
        'must_change_password',
        'employee_id',
        'verification_status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'must_change_password' => 'boolean',
        'email_verified_at'    => 'datetime',
        'password'             => 'hashed',
        'birthday'             => 'date',
    ];

    /* ============================================================
       ROLE CONSTANTS
       ============================================================ */

    const ROLE_ADMIN     = 'admin';
    const ROLE_REGULAR   = 'regular';
    const ROLE_JOB_ORDER = 'job_order';

    public static function getRoles(): array
    {
        return [
            self::ROLE_ADMIN     => 'Administrator',
            self::ROLE_REGULAR   => 'Regular Employee',
            self::ROLE_JOB_ORDER => 'Job Order',
        ];
    }

    /* ============================================================
       FILAMENT REQUIRED METHODS
       ============================================================ */

    public function getFilamentName(): string
    {
        return $this->full_name ?: ($this->name ?: 'User');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'hrms' => in_array($this->role, [
                self::ROLE_ADMIN,
                self::ROLE_REGULAR,
                self::ROLE_JOB_ORDER,
            ]),
            default => false,
        };
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name ?: 'User')
            . '&background=10b981&color=ffffff&bold=true';
    }

    /* ============================================================
       ACCESSORS
       ============================================================ */

    public function getFullNameAttribute(): string
    {
        return implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
            $this->suffix,
        ]));
    }

    public function getRoleDisplayName(): string
    {
        return self::getRoles()[$this->role] ?? 'Unknown';
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->profile_photo_path
            ? asset('storage/' . $this->profile_photo_path)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name ?: 'User')
              . '&background=10b981&color=ffffff&bold=true';
    }

    /* ============================================================
       RELATIONSHIPS
       ============================================================ */

    public function locatorSlips(): HasMany
    {
        return $this->hasMany(LocatorSlip::class, 'user_id');
    }

    public function leaveCredits(): HasOne
    {
        return $this->hasOne(LeaveCredit::class);
    }

    public function leaveCreditLogs(): HasMany
    {
        return $this->hasMany(LeaveCreditLog::class);
    }

    /* ============================================================
       MODEL EVENTS
       ============================================================ */

    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (in_array($user->role, [self::ROLE_REGULAR, self::ROLE_JOB_ORDER])) {
                $user->must_change_password = true;
            }
        });
    }

    /* ============================================================
       ROLE CHECKS
       ============================================================ */

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isRegular(): bool
    {
        return $this->role === self::ROLE_REGULAR;
    }

    public function isJobOrder(): bool
    {
        return $this->role === self::ROLE_JOB_ORDER;
    }
}
