<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms;
use Filament\Pages\Auth\Register as BaseRegister;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Mail\PendingRegistrationMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;

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
                ->unique(User::class, 'employee_id'),

            Forms\Components\TextInput::make('first_name')
                ->label('First Name')
                ->required(),

            Forms\Components\TextInput::make('middle_name')
                ->label('Middle Name')
                ->nullable(),

            Forms\Components\TextInput::make('last_name')
                ->label('Last Name')
                ->required(),

            Forms\Components\Select::make('suffix')
                ->label('Suffix')
                ->options([
                    'Jr' => 'Jr',
                    'Sr' => 'Sr',
                    'I' => 'I',
                    'II' => 'II',
                    'III' => 'III',
                    'IV' => 'IV',
                ])
                ->nullable(),

            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique(User::class),

            Forms\Components\DatePicker::make('birthday')
                ->label('Birthday')
                ->required()
                ->before(today()),

            Forms\Components\TextInput::make('phone')
                ->label('Phone')
                ->nullable()
                ->tel()
                ->inputMode('numeric')
                ->dehydrateStateUsing(fn ($state) => substr(preg_replace('/\D/', '', $state ?? ''), 0, 11))
                ->extraInputAttributes([
                    'oninput' => "this.value = this.value.replace(/\\D/g,'').slice(0,11)",
                ])
                ->rules(['nullable', 'digits:11']),

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
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'suffix' => $data['suffix'] ?? null,
            'name' => implode(' ', array_filter([
                $data['first_name'],
                $data['middle_name'] ?? null,
                $data['last_name'],
                $data['suffix'] ?? null,
            ])),
            'employee_id' => $data['employee_id'],
            'role' => 'employee',
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

    /**
     * Prevent automatic login
     */
    protected function authenticateUsing(): ?\Closure
    {
        return null;
    }

    /**
     * Override register method to prevent automatic login and redirect to login page
     */
    public function register(): ?RegistrationResponse
{
    $data = $this->form->getState();

    $this->handleRegistration($data);

    // flash message for toast after redirect
    session()->flash('registration_success', 'Thank you for registering! Please check your Email for further details.');

    // redirect manually
    return $this->redirectRoute('filament.hrms.auth.login');
}
}
