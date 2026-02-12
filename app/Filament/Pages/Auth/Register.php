<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Section;
use Filament\Pages\Auth\Register as BaseRegister;

use App\Models\User;
use App\Models\Region;
use App\Models\Province;
use App\Models\City;
use App\Models\Barangay;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use App\Mail\PendingRegistrationMail;
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

            /* =====================================================
             | I. PERSONAL INFORMATION
             ===================================================== */
            Section::make('I. Personal Information')
                ->schema([

                    Forms\Components\TextInput::make('employee_id')
                        ->required()
                        ->unique('users', 'employee_id'),

                    Forms\Components\TextInput::make('first_name')->required(),
                    Forms\Components\TextInput::make('middle_name'),
                    Forms\Components\TextInput::make('last_name')->required(),

                    Forms\Components\Select::make('suffix')
                        ->options([
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

                    /* ===============================
                       ADDRESS CASCADING DROPDOWNS
                       =============================== */

                    Forms\Components\TextInput::make('region_id')
                        ->label('Region')
                        ->required(),

                    Forms\Components\TextInput::make('province_id')
                        ->label('Province')
                        ->required(),

                    Forms\Components\TextInput::make('city_id')
                        ->label('City / Municipality')
                        ->required(),

                    Forms\Components\TextInput::make('barangay_id')
                        ->label('Barangay')
                        ->required(),

                    Forms\Components\TextInput::make('purok_street'),
                ])
                ->columns(2),

            /* =====================================================
             | II. EMPLOYMENT INFORMATION
             ===================================================== */
            Section::make('II. Employment Information')
                ->schema([

                    Forms\Components\TextInput::make('position')
                        ->required(),

                    Forms\Components\TextInput::make('department')
                        ->required(),

                    Forms\Components\Select::make('employment_status')
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

            'region_id' => $data['region_id'],
            'province_id' => $data['province_id'],
            'city_id' => $data['city_id'],
            'barangay_id' => $data['barangay_id'],
            'purok_street' => $data['purok_street'] ?? null,

            'position' => $data['position'],
            'department' => $data['department'],
            'employment_status' => $data['employment_status'],

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
