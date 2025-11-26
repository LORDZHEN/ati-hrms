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
    public function getUser()
    {
        return Auth::user();
    }
}
