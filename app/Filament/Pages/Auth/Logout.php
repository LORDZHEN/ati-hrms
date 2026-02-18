<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\BasePage;
use Illuminate\Support\Facades\Auth;

class Logout extends BasePage
{
    public function mount(): mixed
    {
        Auth::guard()->logout();

        session()->invalidate();
        session()->regenerateToken();

        return redirect()->to(filament()->getLoginUrl());
    }
}
