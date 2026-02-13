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
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function authorizeAccess(): void
    {
        abort_unless(Auth::user()?->role === 'admin', 403);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            $this->getRegistrationStatusSection(),
            $this->getPersonalInformationSection(),
            $this->getEmploymentInformationSection(),
            $this->getAccountInformationSection(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getApproveAction(),
            $this->getRejectAction(),
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    // Infolist Sections

    private function getRegistrationStatusSection(): Section
    {
        return Section::make('Registration Status')
            ->schema([
                Grid::make(3)->schema([
                    TextEntry::make('status')
                        ->label('Account Status')
                        ->badge()
                        ->color(fn($state) => match ($state) {
                            'active' => 'success',
                            'pending' => 'warning',
                            'inactive' => 'danger',
                            default => 'gray',
                        }),

                    TextEntry::make('email_verified_at')
                        ->label('Email Verification')
                        ->badge()
                        ->color(fn($state) => $state ? 'success' : 'danger')
                        ->formatStateUsing(fn($state) => $state ? 'Verified' : 'Not Verified'),

                    TextEntry::make('must_change_password')
                        ->label('Password Status')
                        ->badge()
                        ->color(fn($state) => $state ? 'warning' : 'success')
                        ->formatStateUsing(fn($state) => $state ? 'Temporary' : 'Set'),
                ]),
            ])
            ->visible(fn($record) => $record->status === 'pending' || is_null($record->email_verified_at))
            ->collapsible();
    }

    private function getPersonalInformationSection(): Section
    {
        return Section::make('Personal Information')
            ->schema([
                Grid::make(3)->schema([
                    TextEntry::make('first_name')->label('First Name'),
                    TextEntry::make('middle_name')->label('Middle Name')->placeholder('N/A'),
                    TextEntry::make('last_name')->label('Last Name'),
                    TextEntry::make('email')->label('Email Address')->copyable(),
                    TextEntry::make('birthday')->label('Date of Birth')->date('F d, Y'),
                    TextEntry::make('age')
                        ->state(fn($record) => $record->birthday
                            ? Carbon::parse($record->birthday)->age . ' years old'
                            : 'N/A'),
                ]),
            ])
            ->collapsible();
    }

    private function getEmploymentInformationSection(): Section
    {
        return Section::make('Employment Information')
            ->schema([
                Grid::make(2)->schema([
                    TextEntry::make('employee_id')->label('Employee ID')->copyable()->badge(),
                    TextEntry::make('role')->label('System Role')->badge(),
                    TextEntry::make('position')->label('Position')->placeholder('Not assigned'),
                    TextEntry::make('department')->label('Department')->placeholder('Not assigned'),
                    TextEntry::make('employment_status')
                        ->label('Employment Status')
                        ->placeholder('Not assigned')
                        ->badge(),
                ]),
            ])
            ->collapsible();
    }

    private function getAccountInformationSection(): Section
    {
        return Section::make('Account Information')
            ->schema([
                Grid::make(2)->schema([
                    TextEntry::make('created_at')
                        ->label('Registration Date')
                        ->dateTime('F d, Y h:i A'),

                    TextEntry::make('email_verified_at')
                        ->label('Verification Date')
                        ->dateTime('F d, Y h:i A')
                        ->placeholder('Not verified yet'),

                    TextEntry::make('updated_at')
                        ->label('Last Updated')
                        ->dateTime('F d, Y h:i A'),

                    TextEntry::make('days_pending')
                        ->label('Days Pending')
                        ->state(function ($record) {
                            if ($record->status !== 'pending') {
                                return 'N/A';
                            }
                            return $record->created_at->diffInDays(now()) . ' days';
                        })
                        ->visible(fn($record) => $record->status === 'pending'),
                ]),
            ])
            ->collapsible();
    }

    // Actions

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
                "You are about to approve {$this->record->name}'s registration. " .
                "A temporary password will be generated from their birthday (MMDDYYYY) " .
                "and sent to their email."
            )
            ->action(function () {
                $service = app(EmployeeRegistrationService::class);

                if (!$service->approveEmployee($this->record)) {
                    Notification::make()
                        ->title('Invalid Birthday Format')
                        ->body('Unable to generate password from birthday.')
                        ->danger()
                        ->send();
                    return;
                }

                Notification::make()
                    ->title('Employee Approved')
                    ->body('The employee has been approved and notified via email.')
                    ->success()
                    ->send();

                return redirect()->route('filament.hrms.resources.employee-resource.index');
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
                "Are you sure you want to reject {$this->record->name}'s registration? " .
                "This will set their account status to inactive."
            )
            ->action(function () {
                app(EmployeeRegistrationService::class)->rejectEmployee($this->record);

                Notification::make()
                    ->title('Employee Rejected')
                    ->body('The registration has been rejected.')
                    ->warning()
                    ->send();

                return redirect()->route('filament.hrms.resources.employee-resource.index');
            });
    }

    // Helper Methods

    private function isPendingAndUnverified(): bool
    {
        return is_null($this->record->email_verified_at)
            && $this->record->status === 'pending';
    }
}
