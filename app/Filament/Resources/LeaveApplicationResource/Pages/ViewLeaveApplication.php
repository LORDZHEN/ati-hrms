<?php

namespace App\Filament\Resources\LeaveApplicationResource\Pages;

use App\Filament\Resources\LeaveApplicationResource;
use App\Filament\Widgets\LeaveCreditWidget;
use App\Services\LeaveCreditService;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Notifications\Notification;

class ViewLeaveApplication extends ViewRecord
{
    protected static string $resource = LeaveApplicationResource::class;

    // ── Leave balance widget visible to employees viewing their own application
    protected function getHeaderWidgets(): array
    {
        return [
            LeaveCreditWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [

            // ── APPROVE ──────────────────────────────────────────────────────
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(
                    fn() => auth()->user()->role === 'admin'
                    && $this->record->status === 'pending'
                )
                ->requiresConfirmation()
                ->modalHeading('Approve Leave Application')
                ->modalDescription("Approving will deduct the corresponding leave credits from the employee's balance.")
                ->modalSubmitActionLabel('Yes, Approve')
                ->action(function () {
                    $this->record->update([
                        'status' => 'approved',
                        'authorized_officer' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                        'date_approved_disapproved' => now(),
                    ]);

                    app(LeaveCreditService::class)->deductForApplication($this->record);

                    // WHY: Notify AFTER update succeeds so the employee is not
                    // notified about an approval that then fails to persist.
                    $this->record->employee->notify(
                        new \App\Notifications\LeaveApplicationStatusUpdated($this->record)
                    );

                    Notification::make()
                        ->title('Leave Application Approved')
                        ->body('Leave credits have been deducted from the employee\'s balance.')
                        ->success()
                        ->send();
                }),

            // ── DISAPPROVE ───────────────────────────────────────────────────
            Actions\Action::make('disapprove')
                ->label('Disapprove')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(
                    fn() => auth()->user()->role === 'admin'
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
                ->modalSubmitActionLabel('Yes, Disapprove')
                ->action(function (array $data) {
                    // Capture status BEFORE the update so credit reversal is correct.
                    $wasApproved = $this->record->status === 'approved';

                    $this->record->update([
                        'status' => 'disapproved',
                        'authorized_officer' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                        'disapproval_reason' => $data['disapproval_reason'],
                        'date_approved_disapproved' => now(),
                    ]);

                    // Only reverse credits if leave was previously approved.
                    if ($wasApproved) {
                        app(LeaveCreditService::class)->reverseDeduction($this->record);
                    }

                    $this->record->employee->notify(
                        new \App\Notifications\LeaveApplicationStatusUpdated($this->record)
                    );

                    Notification::make()
                        ->title('Leave Application Disapproved')
                        ->body($wasApproved ? 'Leave credits have been restored.' : 'Application has been disapproved.')
                        ->danger()
                        ->send();
                }),

            // ── PRINT ─────────────────────────────────────────────────────────
            Actions\Action::make('print')
                ->label('Print Leave Form')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn() => route('leave_application.print', $this->record))
                ->openUrlInNewTab(),

            // ── EDIT (employee, pending only) ─────────────────────────────────
            Actions\EditAction::make()
                ->visible(
                    fn() => auth()->user()->role === 'employee'
                    && $this->record->status === 'pending'
                ),
        ];
    }
}
