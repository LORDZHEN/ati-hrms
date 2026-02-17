<?php

namespace App\Filament\Resources\LocatorSlipResource\Pages;

use App\Filament\Resources\LocatorSlipResource;
use App\Models\LocatorSlip;
use App\Notifications\LocatorSlipStatusUpdated;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms;
use Filament\Notifications\Notification;

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
                ->visible(fn(LocatorSlip $record) =>
                    $record->status === 'pending' &&
                    auth()->user()->role === 'admin'
                )
                ->action(function (LocatorSlip $record) {
                    $record->update([
                        'status'      => 'approved',
                        'approved_by' => auth()->user()->name,
                        'approved_at' => now(),
                    ]);

                    // Notify the employee — stored in DB, shows in their bell
                    $record->user->notify(new LocatorSlipStatusUpdated($record));

                    // Flash notification for the admin performing the action
                    Notification::make()
                        ->title('Locator Slip Approved')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('disapprove')
                ->label('Disapprove')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn(LocatorSlip $record) =>
                    $record->status === 'pending' &&
                    auth()->user()->role === 'admin'
                )
                ->form([
                    Forms\Components\Textarea::make('admin_remarks')
                        ->label('Reason for Disapproval')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (LocatorSlip $record, array $data) {
                    $record->update([
                        'status'        => 'disapproved',
                        'approved_by'   => auth()->user()->name,
                        'admin_remarks' => $data['admin_remarks'],
                    ]);

                    // Notify the employee — stored in DB, shows in their bell
                    $record->user->notify(new LocatorSlipStatusUpdated($record));

                    // Flash notification for the admin performing the action
                    Notification::make()
                        ->title('Locator Slip Disapproved')
                        ->danger()
                        ->send();
                }),
        ];
    }
}
