<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Services\EmployeeRegistrationService;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function authorizeAccess(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            // Hero Section
            Section::make()
                ->schema([
                    Split::make([
                        Grid::make(2)->schema([
                            TextEntry::make('name')
                                ->label('Full Name')
                                ->size('lg')
                                ->weight('bold')
                                ->icon('heroicon-m-user')
                                ->copyable()
                                ->copyMessage('Name copied')
                                ->copyMessageDuration(1500),

                            TextEntry::make('employee_id')
                                ->label('Employee ID')
                                ->badge()
                                ->color('primary')
                                ->icon('heroicon-m-identification')
                                ->copyable(),
                        ]),

                        Group::make([
                            TextEntry::make('status')
                                ->badge()
                                ->size('lg')
                                ->color(fn($state) => match ($state) {
                                    'active' => 'success',
                                    'pending' => 'warning',
                                    'inactive' => 'danger',
                                    default => 'gray',
                                })
                                ->icon(fn($state) => match ($state) {
                                    'active' => 'heroicon-m-check-circle',
                                    'pending' => 'heroicon-m-clock',
                                    'inactive' => 'heroicon-m-x-circle',
                                    default => 'heroicon-m-question-mark-circle',
                                })
                                ->formatStateUsing(fn($state) => match ($state) {
                                    'pending' => 'Pending Approval',
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                    default => ucfirst($state),
                                }),
                        ])->columnSpan(1),
                    ])->from('md'),
                ])
                ->headerActions([
                    $this->getQuickApproveAction(),
                ]),

            // Status Overview (for pending employees)
            Section::make('Account Status Overview')
                ->description('Review the current status of this employee account')
                ->icon('heroicon-o-shield-check')
                ->schema([
                    Grid::make(4)->schema([
                        TextEntry::make('status')
                            ->label('Account Status')
                            ->badge()
                            ->size('md')
                            ->color(fn($state) => match ($state) {
                                'active' => 'success',
                                'pending' => 'warning',
                                'inactive' => 'danger',
                                default => 'gray',
                            })
                            ->icon(fn($state) => match ($state) {
                                'active' => 'heroicon-m-check-circle',
                                'pending' => 'heroicon-m-clock',
                                'inactive' => 'heroicon-m-x-circle',
                                default => 'heroicon-m-question-mark-circle',
                            }),

                        TextEntry::make('email_verified_at')
                            ->label('Email Status')
                            ->badge()
                            ->size('md')
                            ->color(fn($state) => $state ? 'success' : 'danger')
                            ->icon(fn($state) => $state ? 'heroicon-m-check-badge' : 'heroicon-m-envelope')
                            ->formatStateUsing(fn($state) => $state ? 'Verified' : 'Not Verified'),

                        TextEntry::make('must_change_password')
                            ->label('Password Status')
                            ->badge()
                            ->size('md')
                            ->color(fn($state) => $state ? 'warning' : 'success')
                            ->icon(fn($state) => $state ? 'heroicon-m-key' : 'heroicon-m-lock-closed')
                            ->formatStateUsing(fn($state) => $state ? 'Temporary' : 'Set by User'),

                        TextEntry::make('days_pending')
                            ->label('Time Pending')
                            ->badge()
                            ->size('md')
                            ->color(fn($record) => $record->created_at->diffInDays(now()) > 7 ? 'danger' : 'gray')
                            ->icon('heroicon-m-clock')
                            ->formatStateUsing(function ($record) {
                                if ($record->status !== 'pending') {
                                    return 'N/A';
                                }
                                $days = $record->created_at->diffInDays(now());
                                return $days === 0 ? 'Today' : "{$days} days";
                            }),
                    ]),
                ])
                ->visible(fn($record) => $record->status === 'pending' || is_null($record->email_verified_at))
                ->collapsible()
                ->collapsed(false),

            // Personal Information
            Section::make('Personal Information')
                ->description('Employee personal and contact details')
                ->icon('heroicon-o-user')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('first_name')
                            ->label('First Name')
                            ->icon('heroicon-m-user')
                            ->copyable(),

                        TextEntry::make('middle_name')
                            ->label('Middle Name')
                            ->icon('heroicon-m-user')
                            ->placeholder('N/A')
                            ->copyable(),

                        TextEntry::make('last_name')
                            ->label('Last Name')
                            ->icon('heroicon-m-user')
                            ->copyable(),
                    ]),

                    Grid::make(3)->schema([
                        TextEntry::make('email')
                            ->label('Email Address')
                            ->icon('heroicon-m-envelope')
                            ->copyable()
                            ->copyMessage('Email copied')
                            ->url(fn($record) => "mailto:{$record->email}")
                            ->color('primary'),

                        TextEntry::make('birthday')
                            ->label('Date of Birth')
                            ->date('F d, Y')
                            ->icon('heroicon-m-cake'),

                        TextEntry::make('age')
                            ->label('Current Age')
                            ->icon('heroicon-m-calendar')
                            ->badge()
                            ->color('gray')
                            ->formatStateUsing(fn($record) => $record->birthday
                                ? Carbon::parse($record->birthday)->age . ' years old'
                                : 'N/A'
                            ),
                    ]),
                ])
                ->collapsible()
                ->collapsed(false),

            // Employment Information
            Section::make('Employment Details')
                ->description('Job position and organizational information')
                ->icon('heroicon-o-briefcase')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('employee_id')
                            ->label('Employee ID')
                            ->badge()
                            ->color('primary')
                            ->icon('heroicon-m-identification')
                            ->copyable(),

                        TextEntry::make('role')
                            ->label('System Role')
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                'admin' => 'danger',
                                'employee' => 'info',
                                default => 'gray',
                            })
                            ->icon(fn($state) => match ($state) {
                                'admin' => 'heroicon-m-shield-check',
                                'employee' => 'heroicon-m-user',
                                default => 'heroicon-m-question-mark-circle',
                            })
                            ->formatStateUsing(fn($state) => ucfirst($state)),

                        TextEntry::make('status')
                            ->label('Employment Status')
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                'active' => 'success',
                                'pending' => 'warning',
                                'inactive' => 'danger',
                                default => 'gray',
                            }),
                    ]),

                    Grid::make(2)->schema([
                        TextEntry::make('position')
                            ->label('Position/Title')
                            ->icon('heroicon-m-briefcase')
                            ->placeholder('Not assigned')
                            ->default('N/A'),

                        TextEntry::make('department')
                            ->label('Department')
                            ->icon('heroicon-m-building-office')
                            ->placeholder('Not assigned')
                            ->default('N/A'),
                    ]),
                ])
                ->collapsible(),

            // Account Timeline
            Section::make('Account Timeline')
                ->description('Important dates and account history')
                ->icon('heroicon-o-clock')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('created_at')
                            ->label('Registration Date')
                            ->dateTime('F d, Y h:i A')
                            ->icon('heroicon-m-calendar-days')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('email_verified_at')
                            ->label('Email Verification Date')
                            ->dateTime('F d, Y h:i A')
                            ->icon('heroicon-m-check-badge')
                            ->placeholder('Not verified yet')
                            ->badge()
                            ->color(fn($state) => $state ? 'success' : 'danger')
                            ->formatStateUsing(fn($state) => $state
                                ? Carbon::parse($state)->format('F d, Y h:i A')
                                : 'Not verified'
                            ),

                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime('F d, Y h:i A')
                            ->icon('heroicon-m-arrow-path')
                            ->badge()
                            ->color('gray'),
                    ]),

                    Grid::make(2)->schema([
                        TextEntry::make('member_since')
                            ->label('Member Since')
                            ->icon('heroicon-m-user-group')
                            ->formatStateUsing(fn($record) => $record->created_at->diffForHumans()),

                        TextEntry::make('last_activity')
                            ->label('Last Activity')
                            ->icon('heroicon-m-clock')
                            ->formatStateUsing(fn($record) => $record->updated_at->diffForHumans())
                            ->visible(fn($record) => $record->status === 'active'),
                    ]),
                ])
                ->collapsible()
                ->collapsed(true),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getApproveAction(),
            $this->getRejectAction(),
            $this->getSendCredentialsAction(),
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('warning'),
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Delete Employee')
                ->modalDescription('Are you sure you want to delete this employee? This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, delete')
                ->successNotificationTitle('Employee deleted successfully'),
        ];
    }

    // Actions

    private function getQuickApproveAction(): Actions\Action
    {
        return Actions\Action::make('quick_approve')
            ->label('Quick Approve')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->size('sm')
            ->visible(fn() => $this->isPendingAndUnverified())
            ->requiresConfirmation()
            ->modalHeading('Quick Approve Employee')
            ->modalDescription(fn() =>
                "Approve {$this->record->name} and send login credentials?"
            )
            ->action(function () {
                $service = app(EmployeeRegistrationService::class);

                if (!$service->approveEmployee($this->record)) {
                    Notification::make()
                        ->title('Approval Failed')
                        ->body('Unable to generate password from birthday. Please check the date format.')
                        ->danger()
                        ->send();
                    return;
                }

                Notification::make()
                    ->title('Employee Approved')
                    ->body("Login credentials sent to {$this->record->email}")
                    ->success()
                    ->send();

                redirect()->route('filament.hrms.resources.employees.index');
            });
    }

    private function getApproveAction(): Actions\Action
    {
        return Actions\Action::make('approve')
            ->label('Approve Registration')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->visible(fn() => $this->isPendingAndUnverified())
            ->requiresConfirmation()
            ->modalHeading('Approve Employee Registration')
            ->modalDescription(fn() =>
                "You are about to approve {$this->record->name}'s registration. A temporary password will be generated from their birthday (MMDDYYYY format) and sent via email."
            )
            ->modalIcon('heroicon-o-check-badge')
            ->modalIconColor('success')
            ->action(function () {
                $service = app(EmployeeRegistrationService::class);

                if (!$service->approveEmployee($this->record)) {
                    Notification::make()
                        ->title('Invalid Birthday Format')
                        ->body('Unable to generate password from birthday. Please verify the date is correct.')
                        ->danger()
                        ->duration(5000)
                        ->send();
                    return;
                }

                Notification::make()
                    ->title('Employee Approved Successfully')
                    ->body("Credentials sent to {$this->record->email}")
                    ->success()
                    ->duration(5000)
                    ->send();

                redirect()->route('filament.hrms.resources.employees.index');
            });
    }

    private function getRejectAction(): Actions\Action
    {
        return Actions\Action::make('reject')
            ->label('Reject Registration')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->visible(fn() => $this->record->status === 'pending')
            ->requiresConfirmation()
            ->modalHeading('Reject Employee Registration')
            ->modalDescription(fn() =>
                "Are you sure you want to reject {$this->record->name}'s registration? This will set their account status to inactive and they won't be able to access the system."
            )
            ->modalIcon('heroicon-o-exclamation-triangle')
            ->modalIconColor('danger')
            ->modalSubmitActionLabel('Yes, Reject')
            ->action(function () {
                app(EmployeeRegistrationService::class)->rejectEmployee($this->record);

                Notification::make()
                    ->title('Registration Rejected')
                    ->body('The employee has been notified.')
                    ->warning()
                    ->duration(5000)
                    ->send();

                redirect()->route('filament.hrms.resources.employees.index');
            });
    }

    private function getSendCredentialsAction(): Actions\Action
    {
        return Actions\Action::make('send_credentials')
            ->label('Resend Credentials')
            ->icon('heroicon-o-envelope')
            ->color('info')
            ->visible(fn() => $this->record->status === 'active' && $this->record->must_change_password)
            ->requiresConfirmation()
            ->modalHeading('Resend Login Credentials')
            ->modalDescription(fn() =>
                "Send login credentials to {$this->record->name} again?"
            )
            ->action(function () {
                // Implement resend credentials logic here

                Notification::make()
                    ->title('Credentials Sent')
                    ->body("Login details sent to {$this->record->email}")
                    ->success()
                    ->send();
            });
    }

    // Helper Methods

    private function isPendingAndUnverified(): bool
    {
        return is_null($this->record->email_verified_at)
            && $this->record->status === 'pending';
    }
}
