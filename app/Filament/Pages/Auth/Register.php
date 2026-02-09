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
use Filament\Forms\Components\Section;

class Register extends BaseRegister
{
    protected static string $view = 'filament.pages.auth.register';
    protected static ?string $slug = 'register';

    // Required for Blade
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

            /* =====================================================
             | I. PERSONAL INFORMATION
             ===================================================== */
            Section::make('I. Personal Information')
                ->description('Basic personal details of the employee')
                ->schema([
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
                        ->label('Mobile Number')
                        ->regex('/^[0-9]{11}$/'),

                    Forms\Components\TextInput::make('region')->required(),
                    Forms\Components\TextInput::make('province')->required(),
                    Forms\Components\TextInput::make('city/municipality')->required(),
                    Forms\Components\TextInput::make('barangay')->required(),
                    Forms\Components\TextInput::make('purok_street'),
                ])
                ->columns(2),

            /* =====================================================
             | II. EMPLOYMENT INFORMATION
             ===================================================== */
            Section::make('II. Employment Information')
                ->description('Employment details to be reviewed by HR')
                ->schema([
                    Forms\Components\TextInput::make('position')
                        ->label('Position/Designation')
                        ->required(),

                    Forms\Components\TextInput::make('department')
                        ->required(),

                    Forms\Components\Select::make('employment_status')
                        ->label('Employment Status')
                        ->options([
                            'Permanent' => 'Permanent',
                            'Contractual' => 'Contractual',
                            'Job Order' => 'Job Order',
                            'COS' => 'COS',
                        ])
                        ->required(),
                ])
                ->columns(2),
        ];
    }

    /**
     * Handle registration
     */
    protected function handleRegistration(array $data): User
    {
        $birthday = Carbon::parse($data['birthday']);
        $tempPassword = $birthday->format('mdy');

        $user = User::create([
            // Personal Info
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

            // Address
            'region_name' => $data['region'],
            'province' => $data['province'],
            'city/municipality' => $data['city_municipality'],
            'barangay_name' => $data['barangay'],
            'purok_street' => $data['purok_street'] ?? null,

            // Employment Info
            'position' => $data['position'],
            'department' => $data['department'],
            'employment_status' => $data['employment_status'],

            // System Defaults
            'role' => User::ROLE_EMPLOYEE,
            'status' => 'pending',
            'verification_status' => 'pending',
            'must_change_password' => true,
        ]);

        try {
            Mail::to($user->email)->send(new PendingRegistrationMail($user));
        } catch (\Throwable $e) {
            Log::error('Registration mail failed: ' . $e->getMessage());
        }

        $this->showSuccessMessage = true;
        $this->successMessage = 'Registration successful! Please wait for admin verification.';

        return $user;
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.hrms.auth.login');
    }
}
