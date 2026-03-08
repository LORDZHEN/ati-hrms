<?php

namespace App\Filament\Resources\PersonalDataSheetResource\Pages;

use App\Filament\Resources\PersonalDataSheetResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPersonalDataSheet extends ViewRecord
{
    protected static string $resource = PersonalDataSheetResource::class;

    // =========================================================================
    //  PASS READ-ONLY FLAG TO BLADE VIEWS
    //
    //  The PDS form uses native HTML inputs inside Blade view components
    //  (@included page-1 through page-4). Filament's ViewRecord disables its
    //  own form components automatically but cannot reach raw HTML inputs.
    //
    //  view()->share() makes $isReadOnly available to ALL @included blade
    //  sub-views without needing to pass it through each include manually.
    // =========================================================================

    public function mount(int | string $record): void
    {
        parent::mount($record);
        view()->share('isReadOnly', true);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->status !== 'approved')
                ->requiresConfirmation()
                ->modalHeading('Approve PDS')
                ->modalDescription('Are you sure you want to approve this Personal Data Sheet?')
                ->action(function () {
                    $this->record->update(['status' => 'approved']);
                    $this->record->user?->notify(new \App\Notifications\PDSStatusUpdated($this->record));
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('PDS Approved')
                        ->body('The Personal Data Sheet has been approved successfully.')
                        ->send();
                    $this->redirect($this->getResource()::getUrl('index'));
                }),

            Actions\Action::make('disapprove')
                ->label('Disapprove')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => $this->record->status === 'submitted')
                ->form([
                    \Filament\Forms\Components\Textarea::make('remarks')
                        ->label('Reason for Disapproval')
                        ->required()
                        ->rows(4)
                        ->placeholder('Please provide a clear reason for disapproval...'),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'status'  => 'disapproved',
                        'remarks' => $data['remarks'],
                    ]);
                    $this->record->user?->notify(new \App\Notifications\PDSStatusUpdated($this->record));
                    $this->record->user?->notify(new \App\Notifications\PDSRemarksAdded($this->record));
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('PDS Disapproved')
                        ->body('The employee has been notified with your remarks.')
                        ->send();
                    $this->redirect($this->getResource()::getUrl('index'));
                }),

            Actions\Action::make('print')
                ->label('Print PDS')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->visible(fn () => $this->record->status === 'approved')
                ->url(fn () => route('pds.print', $this->record->id))
                ->openUrlInNewTab(),

            Actions\Action::make('back')
                ->label('Back to List')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
