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

        // Priority 1: Build directly from profile_photo_path (most reliable on shared hosts)
        if (!empty($user->profile_photo_path)) {
            if (Storage::disk('public')->exists($user->profile_photo_path)) {
                return Storage::disk('public')->url($user->profile_photo_path);
            }
            // File may still be served via symlink even if Storage::exists() is unreliable
            return url('storage/' . ltrim($user->profile_photo_path, '/'));
        }

        // Priority 2: Jetstream's accessor if already absolute
        if (method_exists($user, 'getProfilePhotoUrlAttribute')) {
            $url = $user->profile_photo_url;
            if (str_starts_with($url, 'http')) {
                return $url;
            }
        }

        // Priority 3: UI Avatars initials fallback
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
