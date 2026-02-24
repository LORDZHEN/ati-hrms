<?php

namespace App\Filament\Resources\LeaveApplicationResource\Pages;

use App\Filament\Resources\LeaveApplicationResource;
use App\Models\LeaveApplication;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Notifications\Notification;

class ViewLeaveApplication extends ViewRecord
{
    protected static string $resource = LeaveApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [

            // ADMIN ONLY — APPROVE
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(
                    fn() =>
                    auth()->user()->role === 'admin'
                    && $this->record->status === 'pending'
                )
                ->requiresConfirmation()
                ->modalHeading('Approve Leave Application')
                ->modalDescription('Are you sure you want to approve this leave application?')
                ->action(function () {
                    $this->record->update([
                        'status' => 'approved',
                        'authorized_officer' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                        'date_approved_disapproved' => now(),
                    ]);

                    // Notify the employee who filed the leave
                    $this->record->employee->notify(
                        new \App\Notifications\LeaveApplicationStatusUpdated($this->record)
                    );

                    Notification::make()
                        ->title('Leave Application Approved')
                        ->body('The leave application has been approved successfully.')
                        ->success()
                        ->send();
                }),

            // ADMIN ONLY — DISAPPROVE
            Actions\Action::make('disapprove')
                ->label('Disapprove')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(
                    fn() =>
                    auth()->user()->role === 'admin'
                    && $this->record->status === 'pending'
                )
                ->form([
                    \Filament\Forms\Components\Textarea::make('disapproval_reason')
                        ->label('Reason for Disapproval')
                        ->required()
                        ->rows(4)
                        ->placeholder('Please provide a clear reason for disapproving this leave application...'),
                ])
                ->requiresConfirmation()
                ->modalHeading('Disapprove Leave Application')
                ->modalDescription('Please provide a reason for disapproving this leave application.')
                ->action(function (array $data) {
                    $this->record->update([
                        'status' => 'disapproved',
                        'authorized_officer' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                        'disapproval_reason' => $data['disapproval_reason'],
                        'date_approved_disapproved' => now(),
                    ]);

                    $this->record->employee->notify(
                        new \App\Notifications\LeaveApplicationStatusUpdated($this->record)
                    );

                    Notification::make()
                        ->title('Leave Application Disapproved')
                        ->body('The leave application has been disapproved.')
                        ->danger()
                        ->send();
                }),

            // PRINT ACTION
            Actions\Action::make('print')
                ->label('Print Leave Form')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn() => route('leave_application.print', $this->record))
                ->openUrlInNewTab(),

            // EDIT ACTION (Employee only, pending status only)
            Actions\EditAction::make()
                ->visible(
                    fn() =>
                    auth()->user()->role === 'employee'
                    && $this->record->status === 'pending'
                ),
        ];
    }
}
