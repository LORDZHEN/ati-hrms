<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms;
use Filament\Pages\Auth\Register as BaseRegister;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\PendingRegistrationMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Register extends BaseRegister
{
    protected static string $view = 'filament.pages.auth.register';
    protected static ?string $slug = 'register';

    // ✅ REQUIRED for Blade
    public bool $showSuccessMessage = false;
    public string $successMessage = '';

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema($this->getFormSchema())
                    ->statePath('data')
            ),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('employee_id')
                ->label('Employee ID')
                ->required()
                ->unique('users', 'employee_id'),

            Forms\Components\TextInput::make('first_name')->required(),
            Forms\Components\TextInput::make('middle_name'),
            Forms\Components\TextInput::make('last_name')->required(),

            Forms\Components\Select::make('suffix')->options([
                'Jr' => 'Jr',
                'Sr' => 'Sr',
                'I' => 'I',
                'II' => 'II',
                'III' => 'III',
                'IV' => 'IV',
            ]),

            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->unique('users', 'email'),

            Forms\Components\DatePicker::make('birthday')
                ->required()
                ->before(today()),

            Forms\Components\TextInput::make('phone')
                ->regex('/^[0-9]{11}$/'),

            Forms\Components\TextInput::make('region')->required(),
            Forms\Components\TextInput::make('province')->required(),
            Forms\Components\TextInput::make('city_municipality')->required(),
            Forms\Components\TextInput::make('barangay')->required(),
            Forms\Components\TextInput::make('purok_street'),
        ];
    }

    /**
     * ✅ Filament registration handler
     */
    protected function handleRegistration(array $data): User
    {
        $birthday = Carbon::parse($data['birthday']);
        $tempPassword = $birthday->format('mdy');

        $user = User::create([
            'employee_id' => $data['employee_id'],
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'suffix' => $data['suffix'] ?? null,
            'name' => trim(implode(' ', array_filter([
                $data['first_name'],
                $data['middle_name'] ?? null,
                $data['last_name'],
                $data['suffix'] ?? null,
            ]))),
            'email' => $data['email'],
            'password' => Hash::make($tempPassword),
            'birthday' => $data['birthday'],
            'phone' => $data['phone'] ?? null,
            'region_name' => $data['region'],
            'province' => $data['province'],
            'city_municipality' => $data['city_municipality'],
            'barangay_name' => $data['barangay'],
            'purok_street' => $data['purok_street'] ?? null,
            'role' => 'employee',
            'status' => 'pending',
            'verification_status' => 'pending',
            'must_change_password' => true,
        ]);

        try {
            Mail::to($user->email)->send(new PendingRegistrationMail($user));
        } catch (\Throwable $e) {
            Log::error('Registration mail failed: ' . $e->getMessage());
        }

        // ✅ Optional UI feedback (will show briefly)
        $this->showSuccessMessage = true;
        $this->successMessage = 'Registration successful! Please wait for admin verification.';

        return $user;
    }

    /**
     * ✅ REDIRECT AFTER SUCCESSFUL REGISTRATION
     */
    protected function getRedirectUrl(): string
    {
        return route('filament.hrms.auth.login');
    }
}
