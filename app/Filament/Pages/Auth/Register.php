<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms;
use Filament\Pages\Auth\Register as BaseRegister;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Mail\PendingRegistrationMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class Register extends BaseRegister
{
    protected static string $view = 'filament.pages.auth.register';
    protected static ?string $slug = 'register';

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
                ->unique(User::class, 'employee_id_number'),

            Forms\Components\TextInput::make('name')
                ->label('Full Name')
                ->required(),

            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique(User::class),

            Forms\Components\DatePicker::make('birthday')
                ->label('Birthday')
                ->required()
                ->before(today()),

            // OPTIONAL FIELDS
            Forms\Components\TextInput::make('phone')
                ->label('Phone')
                ->nullable(),

            Forms\Components\TextInput::make('purok_street')
                ->label('Purok / Street')
                ->nullable(),

            Forms\Components\TextInput::make('city_municipality')
                ->label('City / Municipality')
                ->nullable(),

            Forms\Components\TextInput::make('province')
                ->label('Province')
                ->nullable(),
        ];
    }

    protected function handleRegistration(array $data): User
    {
        $birthday = Carbon::parse($data['birthday']);
        $tempPassword = $birthday->format('mdy');

        $user = User::create([
            'name' => $data['name'],
            'employee_id_number' => $data['employee_id'],
            'role' => 'employee', // fixed role
            'email' => $data['email'],
            'password' => Hash::make($tempPassword),
            'birthday' => $data['birthday'],
            'phone' => $data['phone'] ?? null,
            'purok_street' => $data['purok_street'] ?? null,
            'city_municipality' => $data['city_municipality'] ?? null,
            'province' => $data['province'] ?? null,
            'status' => 'pending',
            'verification_status' => 'pending',
            'email_verified_at' => null,
            'must_change_password' => true,
        ]);

        // Send registration email
        Mail::to($user->email)->send(new PendingRegistrationMail($user));

        $this->successMessage = 'Registration successful! Your account is pending verification. Please check your email.';
        $this->showSuccessMessage = true;

        return $user;
    }

    protected function authenticateUsing(): ?\Closure
    {
        return null; // prevent automatic login
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.hrms.auth.login');
    }
}
