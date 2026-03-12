<?php

namespace App\Filament\Resources\LeaveApplicationResource\Pages;

use App\Filament\Resources\LeaveApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ViewLeaveApplication extends ViewRecord
{
    protected static string $resource = LeaveApplicationResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);
        view()->share('isReadOnly', true);
    }

    // =========================================================================
    //  HEADER ACTIONS
    //
    //  ADMIN    : Approve (if not yet approved) | Add/Edit Remarks | Print (approved) | Back
    //  EMPLOYEE : Print (approved only) | Back
    //             Admin remarks are shown via the Textarea in the form schema.
    // =========================================================================

    protected function getHeaderActions(): array
    {
        $isAdmin = Auth::user()->role === 'admin';

        return [

            // ── ADMIN: Approve ────────────────────────────────────────────────
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn() => $isAdmin && $this->record->status !== 'approved')
                ->requiresConfirmation()
                ->modalHeading('Approve Leave Application')
                ->modalDescription('Are you sure you want to approve this leave application?')
                ->action(function () {
                    $this->record->update(['status' => 'approved']);
                    $this->record->user?->notify(new \App\Notifications\LeaveApplicationStatusUpdated($this->record));
                    Notification::make()
                        ->success()
                        ->title('Leave Application Approved')
                        ->body('The leave application has been approved and the employee has been notified.')
                        ->send();
                    $this->redirect($this->getResource()::getUrl('index'));
                }),

            // ── ADMIN: Add / Edit Remarks ─────────────────────────────────────
            Actions\Action::make('remarks')
                ->label(fn() => blank($this->record->remarks) ? 'Add Remarks' : 'Edit Remarks')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('warning')
                ->visible(fn() => $isAdmin)
                ->fillForm(fn() => ['remarks' => $this->record->remarks])
                ->form([
                    \Filament\Forms\Components\Textarea::make('remarks')
                        ->label('Admin Remarks')
                        ->rows(5)
                        ->required()
                        ->placeholder('Add notes or feedback for the employee...'),
                ])
                ->action(function (array $data) {
                    $this->record->update(['remarks' => $data['remarks']]);
                    $this->record->user?->notify(new \App\Notifications\LeaveApplicationRemarksAdded($this->record));
                    Notification::make()
                        ->success()
                        ->title('Remarks Updated')
                        ->body('Remarks saved and the employee has been notified.')
                        ->send();
                    // Refresh so remarks are visible immediately
                    $this->redirect(request()->header('Referer'));
                }),

            // ── ADMIN + EMPLOYEE: Print (approved only) ───────────────────────
            Actions\Action::make('print')
                ->label('Print Leave Form')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->visible(fn() => $this->record->status === 'approved')
                ->url(fn() => route('leave_application.print', $this->record->id))
                ->openUrlInNewTab(),

            // ── Back to list ──────────────────────────────────────────────────
            Actions\Action::make('back')
                ->label('Back to List')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
