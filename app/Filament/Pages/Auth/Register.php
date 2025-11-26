<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms;
use Filament\Pages\Auth\Register as BaseRegister;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Mail\PendingRegistrationMail;
use App\Mail\AdminTemporaryPasswordMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Register extends BaseRegister
{
    protected static string $view = 'filament.pages.auth.register';
    protected static ?string $slug = 'register';

    // Livewire properties for success message
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

            Forms\Components\Select::make('role')
                ->label('Role')
                ->options([
                    'employee' => 'Employee',
                    'admin' => 'Admin',
                ])
                ->default('employee')
                ->required(),

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
        $isAdmin = $data['role'] === 'admin';

        if ($isAdmin) {
            $tempPassword = \Str::random(6);
            $password = \Hash::make($tempPassword);
        } else {
            $birthday = \Carbon\Carbon::parse($data['birthday']);
            $tempPassword = $birthday->format('mdy');
            $password = \Hash::make($tempPassword);
        }

        // Create user
        $user = User::create([
            'name' => $data['name'],
            'employee_id_number' => $data['employee_id'],
            'role' => $data['role'],
            'email' => $data['email'],
            'password' => $password,
            'birthday' => $data['birthday'],
            'phone' => $data['phone'] ?? null,
            'purok_street' => $data['purok_street'] ?? null,
            'city_municipality' => $data['city_municipality'] ?? null,
            'province' => $data['province'] ?? null,
            'status' => $isAdmin ? 'active' : 'pending',
            'verification_status' => $isAdmin ? 'verified' : 'pending',
            'email_verified_at' => $isAdmin ? now() : null,
            'must_change_password' => true,
        ]);

        // Send emails
        if ($isAdmin) {
            \Mail::to($user->email)->send(new AdminTemporaryPasswordMail($user, $tempPassword));
        } else {
            \Mail::to($user->email)->send(new PendingRegistrationMail($user));
        }

        // Do NOT log in the user
        // BaseRegister automatically logs in. We override that behavior by returning null.

        // Show success message
        $this->successMessage = $isAdmin
            ? 'Admin account created successfully. A temporary password has been sent to your email.'
            : 'Registration successful! Your account is pending verification. Please check your email.';
        $this->showSuccessMessage = true;

        return $user;
    }

    /**
     * Prevent automatic login after registration
     */
    protected function authenticateUsing(): ?\Closure
    {
        return null; // this prevents Filament from logging in the user
    }

    /**
     * Redirect everyone back to login after registration
     */
    protected function getRedirectUrl(): string
    {
        return route('filament.hrms.auth.login');
    }

}
