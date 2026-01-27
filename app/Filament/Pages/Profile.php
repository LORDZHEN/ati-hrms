<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static string $view = 'filament.pages.profile';

    protected static ?string $navigationLabel = 'Profile';

    protected static ?string $title = 'Profile';
    protected static ?string $navigationGroup = 'Settings';

    public bool $editingProfile = false;  // <-- add this
    public bool $changingPassword = false; // <-- add this

    // Optionally: mount user info
    public function mount(): void
    {
        // Initialize properties if needed
        $this->editingProfile = false;
        $this->changingPassword = false;
    }
    public function getUser()
    {
        return Auth::user();
    }
}
