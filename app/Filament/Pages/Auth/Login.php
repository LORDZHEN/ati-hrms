<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Http\Responses\Auth\LoginResponse as FilamentLoginResponse;
use App\Filament\Pages\HrmsDashboard;

class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.login';

    public static function getSlug(): string
    {
        return 'login';
    }

    // Filament-compatible authenticate method
    public function authenticate(): ?LoginResponse
    {
        $credentials = $this->form->getState();

        // Attempt login
        if (!Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $credentials['remember'] ?? false)) {
            // Failed login
            throw ValidationException::withMessages([
                'email' => __('The provided credentials are incorrect.'),
            ]);
        }

        // Successful login: redirect to HRMS dashboard
        return new FilamentLoginResponse(
            redirect(HrmsDashboard::getUrl())
        );
    }
}
