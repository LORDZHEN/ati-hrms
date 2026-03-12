<?php

namespace App\Filament\Resources\LocatorSlipResource\Pages;

use App\Filament\Resources\LocatorSlipResource;
use App\Models\User;
use App\Notifications\LocatorSlipStatusUpdated;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewLocatorSlip extends ViewRecord
{
    protected static string $resource = LocatorSlipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Approve Locator Slip')
                ->modalDescription('This will mark the locator slip as approved and notify the employee.')
                ->modalSubmitActionLabel('Yes, Approve')
                ->visible(
                    fn() => $this->record->status === 'pending'
                    && auth()->user()->role === User::ROLE_ADMIN
                )
                ->action(function () {
                    $this->record->update([
                        'status' => 'approved',
                        'approved_by' => auth()->user()->name,
                        'approved_at' => now(),
                    ]);

                    $this->record->user->notify(new LocatorSlipStatusUpdated($this->record));

                    Notification::make()
                        ->title('Locator Slip Approved')
                        ->body('The employee has been notified.')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('disapprove')
                ->label('Disapprove')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(
                    fn() => $this->record->status === 'pending'
                    && auth()->user()->role === User::ROLE_ADMIN
                )
                ->form([
                    Forms\Components\Textarea::make('admin_remarks')
                        ->label('Reason for Disapproval')
                        ->required()
                        ->rows(3)
                        ->placeholder('Please provide a clear reason for disapproving this locator slip...'),
                ])
                ->requiresConfirmation()
                ->modalHeading('Disapprove Locator Slip')
                ->modalSubmitActionLabel('Yes, Disapprove')
                ->action(function (array $data) {
                    $this->record->update([
                        'status' => 'disapproved',
                        'approved_by' => auth()->user()->name,
                        'admin_remarks' => $data['admin_remarks'],
                    ]);

                    $this->record->user->notify(new LocatorSlipStatusUpdated($this->record));

                    Notification::make()
                        ->title('Locator Slip Disapproved')
                        ->body('The employee has been notified.')
                        ->danger()
                        ->send();
                }),

            Actions\Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn() => route('locator_slip.print', $this->record->id))
                ->openUrlInNewTab()
                ->visible(fn() => $this->record->status === 'approved'),

            Actions\EditAction::make()
                ->visible(
                    fn() => $this->record->status === 'pending'
                    && (auth()->user()->role === User::ROLE_ADMIN
                        || auth()->id() === $this->record->user_id)
                ),
        ];
    }
}
