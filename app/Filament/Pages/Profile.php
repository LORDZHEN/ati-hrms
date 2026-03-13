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
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 1;

    public bool $mustChangePassword = false;

    public function mount(): void
    {
        // Force a fresh DB read on every page load so we never serve stale data
        // after UpdateProfile redirects back here.
        $user = Auth::user()->fresh();
        Auth::setUser($user);

        $this->mustChangePassword = (bool) $user->must_change_password;
    }

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

    /**
     * Always returns a fresh user instance so callers never read stale data.
     */
    public function getUser()
    {
        return Auth::user()->fresh();
    }

    /**
     * Returns the full URL for the user's profile photo.
     *
     * Uses Auth::user() directly (not ->fresh()) because mount() already
     * replaced the auth singleton with a fresh instance. Appends a filemtime
     * version string so browsers always fetch the latest file.
     */
    public function getProfilePhotoUrl(): string
    {
        // Always fresh — mount() already called ->fresh() and Auth::setUser()
        $user = Auth::user()->fresh();

        if (!empty($user->profile_photo_path)) {
            $diskPath = $user->profile_photo_path;
            $absPath = storage_path('app/public/' . $diskPath);

            if (file_exists($absPath)) {
                // Use time() not filemtime() — guarantees browser re-fetches after redirect
                return asset('storage/' . $diskPath) . '?v=' . time();
            }

            if (Storage::disk('public')->exists($diskPath)) {
                return Storage::disk('public')->url($diskPath) . '?v=' . time();
            }
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'User')
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

    // -------------------------------------------------------------------------
    // Stub methods — prevent Livewire MethodNotFoundException when calls from
    // child components (UpdateProfile, ChangePassword) bubble up to this page.
    // -------------------------------------------------------------------------

    public function update(): void
    {
    }

    public function openModal(): void
    {
    }

    public function closeModal(): void
    {
    }
}
