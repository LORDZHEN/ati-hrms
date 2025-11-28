<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'phone',
        'purok_street',
        'city_municipality',
        'province',
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
        'birthday',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'must_change_password' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birthday' => 'date',
    ];

    const ROLE_EMPLOYEE = 'employee';
    const ROLE_ADMIN = 'admin';

    public static function getRoles(): array
    {
        return [
            self::ROLE_EMPLOYEE => 'Employee',
            self::ROLE_ADMIN => 'Admin',
        ];
    }

    /* ============================================================
        FILAMENT REQUIRED METHODS
       ============================================================ */

    public function getFilamentName(): string
    {
        return $this->name ?? 'User';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'hrms' => $this->role === self::ROLE_ADMIN || $this->role === self::ROLE_EMPLOYEE,
            default => false,
        };
    }


    /* ============================================================
        ACCESSORS
       ============================================================ */

    public function getFullNameAttribute(): string
    {
        return $this->name ?? 'User';
    }

    public function getRoleDisplayName(): string
    {
        return self::getRoles()[$this->role] ?? 'Unknown';
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?? 'User');
    }

    /* ============================================================
        RELATIONSHIPS
       ============================================================ */

    // public function notifications()
    // {
    //     return $this->hasMany(\App\Models\Notification::class);
    // }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    /* ============================================================
        MODEL EVENTS
       ============================================================ */

    protected static function booted(): void
    {
        static::creating(function ($user) {
            if ($user->role === self::ROLE_EMPLOYEE) {
                $user->must_change_password = true;
            }
        });
    }

    /* ============================================================
        ROLE CHECKS
       ============================================================ */

    public function isEmployee(): bool
    {
        return $this->role === self::ROLE_EMPLOYEE;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }
}
