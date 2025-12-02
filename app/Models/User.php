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
        'middle_name',
        'last_name',
        'suffix',
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

    /**
     * Fix: Filament Top-Right Avatar
     * Filament will call this method to show the user's profile photo
     */
    public function getFilamentAvatarUrl(): ?string {
        // If user has uploaded photo
        if ($this->profile_photo_path && file_exists(storage_path('app/public/' . $this->profile_photo_path))) {
            return asset('storage/' . $this->profile_photo_path);
        }
        // fallback to initials
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->getFullNameAttribute() ?? 'User');
    }

    /* ============================================================
       ACCESSORS
       ============================================================ */

    public function getFullNameAttribute(): string
    {
        $parts = [
            $this->first_name,
            $this->middle_name,
            $this->last_name,
            $this->suffix
        ];

        return implode(' ', array_filter($parts));
    }

    public function getRoleDisplayName(): string
    {
        return self::getRoles()[$this->role] ?? 'Unknown';
    }

    public function getProfilePhotoUrlAttribute(): string {
        return $this->profile_photo_path
            ? asset('storage/' . $this->profile_photo_path)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->getFullNameAttribute() ?? 'User');
    }

    /* ============================================================
       RELATIONSHIPS
       ============================================================ */

    public function locatorSlips()
    {
        return $this->hasMany(LocatorSlip::class);
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
