<?php

namespace App\Filament\Resources\PersonalDataSheetResource\Pages;

use App\Filament\Resources\PersonalDataSheetResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ViewPersonalDataSheet extends ViewRecord
{
    protected static string $resource = PersonalDataSheetResource::class;

    // =========================================================================
    //  PASS READ-ONLY FLAG TO BLADE VIEWS
    //
    //  view()->share() makes $isReadOnly available to ALL @included blade
    //  sub-views without needing to pass it through each include manually.
    // =========================================================================

    public function mount(int|string $record): void
    {
        parent::mount($record);
        view()->share('isReadOnly', true);
    }

    // =========================================================================
    //  HEADER ACTIONS
    //
    //  ADMIN  : Approve (if not yet approved) | Add/Edit Remarks | Print (approved only) | Back
    //  EMPLOYEE: Print (approved only) | Back
    //
    //  The employee's view always shows admin remarks via the form field
    //  (the Textarea::make('remarks') in the Resource form is shown when
    //  remarks are present and the user is a regular employee viewing their PDS).
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
                ->modalHeading('Approve PDS')
                ->modalDescription('Are you sure you want to approve this Personal Data Sheet?')
                ->action(function () {
                    $this->record->update(['status' => 'approved']);
                    $this->record->user?->notify(new \App\Notifications\PDSStatusUpdated($this->record));
                    Notification::make()
                        ->success()
                        ->title('PDS Approved')
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
                    Notification::make()
                        ->success()
                        ->title('Remarks Updated')
                        ->body('Admin remarks have been saved and the employee has been notified.')
                        ->send();
                    // Refresh the page so the updated remarks are visible immediately
                    $this->redirect(request()->header('Referer'));
                }),

            // ── ADMIN + EMPLOYEE: Print (approved only) ───────────────────────
            Actions\Action::make('print')
                ->label('Print PDS')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->visible(fn() => $this->record->status === 'approved')
                ->url(fn() => route('pds.print', $this->record->id))
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
