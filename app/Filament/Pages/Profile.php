<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Profile extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-user-circle';
    protected static string  $view            = 'filament.pages.profile';
    protected static ?string $navigationLabel = 'My Profile';
    protected static ?string $title           = 'My Profile';
    protected static ?string $navigationGroup = 'My Account';
    protected static ?int    $navigationSort  = 1;

    public bool $mustChangePassword = false;

    /**
     * BUG FIX #6 — profilePhotoUrl is now a public property on the
     * Filament page.  When the child UpdateProfile component dispatches
     * the 'profile-saved' event we call refreshProfilePhoto() which
     * re-reads the DB and updates this property.  Because it is a
     * public Livewire property the hero <img> in profile.blade.php
     * can bind to it and will update instantly without a page reload.
     */
    public string $profilePhotoUrl = '';

    public function mount(): void
    {
        $user = Auth::user()->fresh();
        Auth::setUser($user);

        $this->mustChangePassword = (bool) $user->must_change_password;
        $this->profilePhotoUrl    = $this->buildProfilePhotoUrl($user);
    }

    // -------------------------------------------------------------------------
    // Event listeners
    // -------------------------------------------------------------------------

    public function onPasswordChanged(): void
    {
        $this->mustChangePassword = false;
    }

    /**
     * BUG FIX #6 (cont.) — Called when the UpdateProfile child component
     * dispatches 'profile-saved'. Re-fetches a fresh user from the DB and
     * updates the public $profilePhotoUrl property so the hero avatar
     * refreshes in place — no redirect, no page reload required.
     */
    public function onProfileSaved(): void
    {
        $user = Auth::user()->fresh();
        Auth::setUser($user);
        $this->profilePhotoUrl = $this->buildProfilePhotoUrl($user);
    }

    protected function getListeners(): array
    {
        return [
            'password-changed' => 'onPasswordChanged',
            'profile-saved'    => 'onProfileSaved',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getUser()
    {
        return Auth::user()->fresh();
    }

    protected function buildProfilePhotoUrl($user): string
    {
        if (!empty($user->profile_photo_path)) {
            $diskPath = $user->profile_photo_path;
            $absPath  = storage_path('app/public/' . $diskPath);

            if (file_exists($absPath)) {
                // time() forces browser cache-bust after every save
                return asset('storage/' . $diskPath) . '?v=' . time();
            }

            if (Storage::disk('public')->exists($diskPath)) {
                return Storage::disk('public')->url($diskPath) . '?v=' . time();
            }
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'User')
            . '&color=ffffff&background=16a34a&size=256&bold=true';
    }

    /**
     * Kept for backwards compatibility (blade calls this directly in a few
     * places). Delegates to the property so there is a single source of truth.
     */
    public function getProfilePhotoUrl(): string
    {
        return $this->profilePhotoUrl;
    }

    public function getProfileCompletionProperty(): int
    {
        $user   = $this->getUser();
        $fields = [
            'employee_id', 'first_name', 'last_name', 'email',
            'position', 'employment_status', 'department', 'profile_photo_path',
        ];

        $completed = collect($fields)
            ->filter(fn ($field) => !empty($user->{$field}))
            ->count();

        return (int) (($completed / count($fields)) * 100);
    }

    // ── Stubs — prevent MethodNotFoundException from child component events ──
    public function update(): void    {}
    public function openModal(): void {}
    public function closeModal(): void {}
}
