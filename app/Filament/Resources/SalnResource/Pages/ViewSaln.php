<?php

namespace App\Filament\Resources\SalnResource\Pages;

use App\Filament\Resources\SalnResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSaln extends ViewRecord
{
    protected static string $resource = SalnResource::class;

    // =========================================================================
    //  PASS READ-ONLY FLAG TO BLADE VIEWS
    //
    //  The SALN form uses native HTML inputs inside Blade view components.
    //  Filament's ViewRecord disables its own form components automatically,
    //  but cannot reach inside raw HTML inputs.
    //
    //  view()->share() makes $isReadOnly available to ALL @included blade
    //  sub-views (page-1, page-2) without needing to pass it manually.
    // =========================================================================

    public function mount(int|string $record): void
    {
        parent::mount($record);
        view()->share('isReadOnly', true);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('remarks')
                ->label(fn() => blank($this->record->remarks) ? 'Add Remarks' : 'Edit Remarks')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('warning')
                ->visible(fn() => auth()->user()?->role === 'admin')
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
                    $this->record->user?->notify(new \App\Notifications\SalnRemarksAdded($this->record));
                    \Filament\Notifications\Notification::make()
                        ->title('Remarks Updated')
                        ->body('Administrative remarks have been saved and the employee has been notified.')
                        ->success()
                        ->send();
                    $this->refreshFormData(['remarks']);
                }),

            Actions\Action::make('print')
                ->label('Print SALN')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn() => route('saln.print', $this->record))
                ->openUrlInNewTab(),

            Actions\Action::make('back')
                ->label('Back to List')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
