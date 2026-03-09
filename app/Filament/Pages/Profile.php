<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.pages.profile';
    protected static ?string $navigationLabel = 'My Profile';
    protected static ?string $title = 'My Profile';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 99;

    // FIX: declare the property so the blade can access $mustChangePassword
    public bool $mustChangePassword = false;

    public function mount(): void
    {
        // FIX: populate from the authenticated user on every page load
        $this->mustChangePassword = (bool) Auth::user()->must_change_password;
    }

    /**
     * Called by ChangePassword component via $this->dispatch('password-changed').
     * Clears the flag reactively without a full page reload.
     */
    public function onPasswordChanged(): void
    {
        $this->mustChangePassword = false;
    }

    protected function getListeners(): array
    {
        return [
            'password-changed' => 'onPasswordChanged',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getUser()
    {
        return Auth::user();
    }

    /**
     * Returns a fully-qualified, absolute URL for the user's profile photo.
     * Priority: profile_photo_path → Jetstream URL → UI Avatars fallback
     */
    public function getProfilePhotoUrl(): string
    {
        $user = Auth::user();

        if (!empty($user->profile_photo_path)) {
            if (Storage::disk('public')->exists($user->profile_photo_path)) {
                return Storage::disk('public')->url($user->profile_photo_path);
            }
            return url('storage/' . ltrim($user->profile_photo_path, '/'));
        }

        if (method_exists($user, 'getProfilePhotoUrlAttribute')) {
            $url = $user->profile_photo_url;
            if (str_starts_with($url, 'http')) {
                return $url;
            }
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($user->name)
            . '&color=ffffff&background=16a34a&size=256&bold=true';
    }

    public function getProfileCompletionProperty(): int
    {
        $user = $this->getUser();
        $fields = [
            'employee_id',
            'first_name',
            'last_name',
            'email',
            'position',
            'employment_status',
            'department',
            'profile_photo_path',
        ];

        $completed = collect($fields)
            ->filter(fn($field) => !empty($user->{$field}))
            ->count();

        return (int) (($completed / count($fields)) * 100);
    }
}
