<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use App\Mail\PendingRegistrationMail;
use App\Notifications\NewEmployeeRegistered;
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
            Section::make('I. Personal Information')
                ->extraAttributes(['class' => 'fi-section-transparent'])
                ->schema([
                    Forms\Components\TextInput::make('employee_id')
                        ->label('Employee ID')
                        ->required()
                        ->unique('users', 'employee_id'),

                    Forms\Components\TextInput::make('first_name')
                        ->label('First Name')
                        ->required(),

                    Forms\Components\TextInput::make('middle_name')
                        ->label('Middle Name'),

                    Forms\Components\TextInput::make('last_name')
                        ->label('Last Name')
                        ->required(),

                    Forms\Components\Select::make('suffix')
                        ->label('Suffix')
                        ->options([
                            'Jr'  => 'Jr',
                            'Sr'  => 'Sr',
                            'I'   => 'I',
                            'II'  => 'II',
                            'III' => 'III',
                            'IV'  => 'IV',
                        ]),

                    Forms\Components\TextInput::make('email')
                        ->label('Email Address')
                        ->email()
                        ->required()
                        ->unique('users', 'email'),

                    Forms\Components\DatePicker::make('birthday')
                        ->label('Date of Birth')
                        ->required()
                        ->before(today()),

                    Forms\Components\TextInput::make('phone')
                        ->label('Phone Number')
                        ->nullable()
                        ->rule('nullable')
                        ->regex('/^[0-9]{11}$/'),
                ])
                ->columns(2),

            // ── Address fields are Hidden here ──
            // The actual dropdowns are rendered in the Blade via Alpine.js
            // and push their values back into these hidden form fields via $wire.set()
            Forms\Components\Hidden::make('region_id'),
            Forms\Components\Hidden::make('province_id'),
            Forms\Components\Hidden::make('city_id'),
            Forms\Components\Hidden::make('barangay_id'),
            // Forms\Components\Hidden::make('purok_street'),

            Section::make('II. Employment Information')
                ->extraAttributes(['class' => 'fi-section-transparent'])
                ->schema([
                    Forms\Components\TextInput::make('position')
                        ->label('Position')
                        ->required(),

                    Forms\Components\TextInput::make('department')
                        ->label('Department')
                        ->required(),

                    Forms\Components\Select::make('employment_status')
                        ->label('Employment Status')
                        ->options([
                            'Permanent'   => 'Permanent',
                            'Contractual' => 'Contractual',
                            'Job Order'   => 'Job Order',
                            'COS'         => 'COS',
                        ])
                        ->required(),
                ])
                ->columns(2),
        ];
    }

    /**
     * Must match Filament's base signature exactly: ?RegistrationResponse
     * Return null to suppress Filament's default auto-login + redirect.
     * We handle everything ourselves via session flash + $this->redirect().
     */
    public function register(): ?RegistrationResponse
    {
        $data = $this->form->getState();

        // Validate that address fields were filled via Alpine.js
        if (empty($data['region_id']) || empty($data['province_id']) ||
            empty($data['city_id'])   || empty($data['barangay_id'])) {

            $this->addError('data.region_id', 'Please complete your full address.');
            return null;
        }

        $this->handleRegistration($data);

        session()->flash('registration_success', $this->successMessage);

        $this->redirect(route('filament.hrms.auth.login'));

        return null;
    }

    protected function handleRegistration(array $data): User
    {
        // Temp password = birthday in MMDDYYYY format
        // e.g. December 04, 2002 → "12042002"
        $tempPassword = Carbon::parse($data['birthday'])->format('mdY');

        $user = User::create([
            'employee_id'          => $data['employee_id'],
            'first_name'           => $data['first_name'],
            'middle_name'          => $data['middle_name'] ?? null,
            'last_name'            => $data['last_name'],
            'suffix'               => $data['suffix'] ?? null,
            'name'                 => trim(implode(' ', array_filter([
                                          $data['first_name'],
                                          $data['middle_name'] ?? null,
                                          $data['last_name'],
                                          $data['suffix'] ?? null,
                                      ]))),
            'email'                => $data['email'],
            'password'             => Hash::make($tempPassword),
            'birthday'             => $data['birthday'],
            'phone'                => $data['phone'] ?? null,
            'region_id'            => $data['region_id'],
            'province_id'          => $data['province_id'],
            'city_id'              => $data['city_id'],
            'barangay_id'          => $data['barangay_id'],
            // 'purok_street'         => $data['purok_street'] ?? null,
            'position'             => $data['position'],
            'department'           => $data['department'],
            'employment_status'    => $data['employment_status'],
            'role'                 => User::ROLE_EMPLOYEE,
            'status'               => 'pending',
            'verification_status'  => 'pending',
            'must_change_password' => true,
        ]);

        // Send pending registration notification email
        try {
            Mail::to($user->email)->send(new PendingRegistrationMail($user));
        } catch (\Throwable $e) {
            Log::error('Registration mail failed: ' . $e->getMessage());
        }

        // Notify all admins via in-app notification (bell icon)
        try {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new NewEmployeeRegistered($user));
            }
        } catch (\Throwable $e) {
            Log::error('Admin notification failed: ' . $e->getMessage());
        }

        // Ensure the newly created user is never auto-logged in
        Auth::logout();

        $this->showSuccessMessage = true;
        $this->successMessage     = 'Registration successful! Please wait for admin verification before logging in.';

        return $user;
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.hrms.auth.login');
    }
}
