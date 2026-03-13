<?php

namespace App\Filament\Resources\PersonalDataSheetResource\Pages;

use App\Filament\Concerns\WorkflowHelper;
use App\Filament\Resources\PersonalDataSheetResource;
use App\Services\FilingSeasonService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPersonalDataSheet extends ViewRecord
{
    use WorkflowHelper;

    protected static string $resource = PersonalDataSheetResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);
        view()->share('isReadOnly', true);
    }

    // =========================================================================
    //  HEADER ACTIONS
    // =========================================================================

    protected function getHeaderActions(): array
    {
        $isAdmin = auth()->user()->role === 'admin';

        return [

            // ── ADMIN: Approve ────────────────────────────────────────────────
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn() => $isAdmin && $this->record->status !== 'approved')
                ->requiresConfirmation()
                ->modalHeading('Approve PDS')
                ->modalDescription('Are you sure you want to approve this Personal Data Sheet?')
                ->action(function () {
                    $this->record->update([
                        'status'           => 'approved',
                        'editing_unlocked' => false, // lock on fresh approval
                    ]);
                    $this->record->user?->notify(new \App\Notifications\PDSStatusUpdated($this->record));
                    Notification::make()->success()->title('PDS Approved')
                        ->body('The Personal Data Sheet has been approved successfully.')
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
                    $this->record->user?->notify(new \App\Notifications\PDSRemarksAdded($this->record));
                    Notification::make()->success()->title('Remarks Updated')
                        ->body('Admin remarks have been saved and the employee has been notified.')
                        ->send();
                    $this->redirect(request()->header('Referer'));
                }),

            // ── ADMIN: Print ──────────────────────────────────────────────────
            Actions\Action::make('print')
                ->label('Print PDS')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->visible(fn() => $isAdmin && $this->record->status === 'approved')
                ->url(fn() => route('pds.print', $this->record->id))
                ->openUrlInNewTab(),

            // ── ADMIN: Unlock Editing ─────────────────────────────────────────
            Actions\Action::make('unlockEditing')
                ->label('Unlock Editing')
                ->icon('heroicon-o-lock-open')
                ->color('info')
                ->visible(function () use ($isAdmin) {
                    return $isAdmin
                        && $this->record->status === 'approved'
                        && ! $this->record->editing_unlocked;
                })
                ->requiresConfirmation()
                ->modalHeading('Unlock for Employee Editing')
                ->modalDescription(
                    app(FilingSeasonService::class)->isEnabled()
                        ? 'This will allow the employee to edit and resubmit their PDS. Filing season is currently OPEN.'
                        : '⚠️ Filing season is currently CLOSED. The employee will not be able to edit until filing season is enabled.'
                )
                ->modalSubmitActionLabel('Yes, Unlock')
                ->action(function () {
                    $this->record->update(['editing_unlocked' => true]);
                    Notification::make()
                        ->title('Editing Unlocked')
                        ->body('The employee can now edit and resubmit their PDS (when filing season is open).')
                        ->success()
                        ->send();
                    $this->record->refresh();
                }),

            // ── ADMIN: Lock Editing ───────────────────────────────────────────
            Actions\Action::make('lockEditing')
                ->label('Lock Editing')
                ->icon('heroicon-o-lock-closed')
                ->color('danger')
                ->visible(function () use ($isAdmin) {
                    return $isAdmin
                        && $this->record->status === 'approved'
                        && $this->record->editing_unlocked;
                })
                ->requiresConfirmation()
                ->modalHeading('Lock This Record')
                ->modalDescription('This will prevent the employee from making further edits.')
                ->modalSubmitActionLabel('Yes, Lock')
                ->action(function () {
                    $this->record->update(['editing_unlocked' => false]);
                    Notification::make()->title('Record Locked')->warning()->send();
                    $this->record->refresh();
                }),

            // ── EMPLOYEE: Print (approved only) ───────────────────────────────
            Actions\Action::make('employeePrint')
                ->label('Print PDS')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->visible(fn() => ! $isAdmin && $this->record->status === 'approved')
                ->url(fn() => route('pds.print', $this->record->id))
                ->openUrlInNewTab(),

            // ── Back ──────────────────────────────────────────────────────────
            Actions\Action::make('back')
                ->label('Back to List')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
