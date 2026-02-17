<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.pages.profile';
    protected static ?string $navigationLabel = 'My Profile';
    protected static ?string $title = 'My Profile';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 99;

    /**
     * Disable default page actions
     */
    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * Get the current authenticated user
     */
    public function getUser()
    {
        return Auth::user();
    }

    /**
     * Compute profile completion percentage
     */
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
