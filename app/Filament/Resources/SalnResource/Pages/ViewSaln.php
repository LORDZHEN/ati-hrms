<?php

namespace App\Filament\Resources\SalnResource\Pages;

use App\Filament\Concerns\WorkflowHelper;
use App\Filament\Resources\SalnResource;
use App\Notifications\SalnRemarksAdded;
use App\Services\FilingSeasonService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewSaln extends ViewRecord
{
    use WorkflowHelper;

    protected static string $resource = SalnResource::class;

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
        $isAdmin = auth()->user()?->role === 'admin';

        return [

            // ── ADMIN: Approve ────────────────────────────────────────────────
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(function () use ($isAdmin) {
                    return $isAdmin && in_array($this->record->status, ['pending', 'submitted', 'disapproved']);
                })
                ->requiresConfirmation()
                ->modalHeading('Approve SALN')
                ->modalDescription('Are you sure you want to approve this SALN? The employee will be notified.')
                ->modalSubmitActionLabel('Yes, Approve')
                ->action(function () {
                    $this->record->update([
                        'status' => 'approved',
                        'editing_unlocked' => false, // lock on fresh approval
                    ]);
                    $this->record->user?->notify(new \App\Notifications\SalnStatusUpdated($this->record));
                    Notification::make()->title('SALN Approved')->success()->send();
                    $this->record->refresh();
                    $this->refreshFormData(['status', 'editing_unlocked']);
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
                        ->rows(4)
                        ->required()
                        ->placeholder('Enter administrative remarks or comments...'),
                ])
                ->action(function (array $data) {
                    $this->record->update(['remarks' => $data['remarks']]);
                    $this->record->user?->notify(new SalnRemarksAdded($this->record));
                    Notification::make()->title('Remarks Updated')->success()->send();
                    $this->record->refresh();
                    $this->refreshFormData(['remarks']);
                }),

            // ── ADMIN: Print ──────────────────────────────────────────────────
            Actions\Action::make('print')
                ->label('Print SALN')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->visible(fn() => $isAdmin && $this->record->status === 'approved')
                ->url(fn() => route('saln.print', $this->record))
                ->openUrlInNewTab(),

            // ── ADMIN: Unlock Editing ─────────────────────────────────────────
            //    Visible when: admin + approved + currently LOCKED
            Actions\Action::make('unlockEditing')
                ->label('Unlock Editing')
                ->icon('heroicon-o-lock-open')
                ->color('info')
                ->visible(function () use ($isAdmin) {
                    return $isAdmin
                        && $this->record->status === 'approved'
                        && !$this->record->editing_unlocked;
                })
                ->requiresConfirmation()
                ->modalHeading('Unlock for Employee Editing')
                ->modalDescription(
                    app(FilingSeasonService::class)->isEnabled()
                    ? 'This will allow the employee to edit and resubmit their SALN. Filing season is currently OPEN.'
                    : '⚠️ Filing season is currently CLOSED. The employee will still not be able to edit until filing season is enabled.'
                )
                ->modalSubmitActionLabel('Yes, Unlock')
                ->action(function () {
                    $this->record->update(['editing_unlocked' => true]);
                    Notification::make()
                        ->title('Editing Unlocked')
                        ->body('The employee can now edit and resubmit this SALN (when filing season is open).')
                        ->success()
                        ->send();
                    $this->record->refresh();
                    $this->refreshFormData(['editing_unlocked']);
                }),

            // ── ADMIN: Lock Editing ───────────────────────────────────────────
            //    Visible when: admin + approved + currently UNLOCKED
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
                    Notification::make()
                        ->title('Record Locked')
                        ->body('Editing has been locked for this record.')
                        ->warning()
                        ->send();
                    $this->record->refresh();
                    $this->refreshFormData(['editing_unlocked']);
                }),

            // ── EMPLOYEE: Print (approved only) ───────────────────────────────
            Actions\Action::make('employeePrint')
                ->label('Print SALN')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->visible(fn() => !$isAdmin && $this->record->status === 'approved')
                ->url(fn() => route('saln.print', $this->record))
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
