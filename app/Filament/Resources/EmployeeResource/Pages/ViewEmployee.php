<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountVerifiedMail;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function authorizeAccess(): void
    {
        abort_if(Auth::user()?->role !== 'admin', 403);
    }

    /**
     * Display all employee fields in a view-only format
     */
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('employee_id')->label('Employee ID'),
                TextEntry::make('name')->label('Full Name'),
                TextEntry::make('email')->label('Email'),
                TextEntry::make('position')->label('Position')->placeholder('Not assigned'),
                TextEntry::make('employment_status')
                    ->label('Employment Status')
                    ->placeholder('Not assigned'),
                TextEntry::make('department')->label('Department')->placeholder('Not assigned'),
                TextEntry::make('status')->label('Account Status')->badge(),
                TextEntry::make('email_verified_at')
                    ->label('Email Verification')
                    ->formatStateUsing(fn ($state) => $state ? 'Verified' : 'Not Verified')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
                TextEntry::make('created_at')->label('Date Registered')->date(),
                TextEntry::make('birthday')->label('Birthday')->date(),
            ])
            ->columns(2);
    }

    /**
     * Approve / Disapprove actions for admins
     */
    protected function getHeaderActions(): array
    {
        return [

            Actions\Action::make('approve')
                ->label('Approve Employee')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn () =>
                    is_null($this->record->email_verified_at)
                    && $this->record->status === 'pending'
                )
                ->requiresConfirmation()
                ->action(function () {

                    $birthday = $this->record->birthday?->format('mdY');

                    if (!$birthday || strlen($birthday) !== 8) {
                        Notification::make()
                            ->title('Invalid birthday format')
                            ->danger()
                            ->send();
                        return;
                    }

                    $this->record->update([
                        'email_verified_at' => now(),
                        'password' => bcrypt($birthday),
                        'must_change_password' => true,
                        'status' => 'active',
                        'verification_status' => 'verified',
                    ]);

                    Mail::to($this->record->email)
                        ->send(new AccountVerifiedMail($this->record, $birthday));

                    Notification::make()
                        ->title('Employee Approved')
                        ->body("Temporary password: **{$birthday}**")
                        ->success()
                        ->persistent()
                        ->send();
                }),

            Actions\Action::make('disapprove')
                ->label('Disapprove')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => $this->record->status === 'pending')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status' => 'inactive',
                    ]);

                    Notification::make()
                        ->title('Employee Disapproved')
                        ->warning()
                        ->send();
                }),
        ];
    }
}
