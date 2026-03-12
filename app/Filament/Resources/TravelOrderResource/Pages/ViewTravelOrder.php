<?php

namespace App\Filament\Resources\TravelOrderResource\Pages;

use App\Filament\Resources\TravelOrderResource;
use App\Models\User;
use App\Notifications\TravelOrderStatusUpdated;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewTravelOrder extends ViewRecord
{
    protected static string $resource = TravelOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Approve Travel Order')
                ->modalDescription('This will mark the travel order as approved and notify the employee.')
                ->modalSubmitActionLabel('Yes, Approve')
                ->visible(
                    fn() => $this->record->status === 'pending'
                    && Auth::user()->role === User::ROLE_ADMIN
                )
                ->action(function () {
                    $this->record->update([
                        'status' => 'approved',
                        'approved_by' => Auth::id(),        // ← was Auth::user()->name
                        'approved_at' => now(),
                    ]);

                    $this->record->creator->notify(new TravelOrderStatusUpdated($this->record));

                    Notification::make()
                        ->title('Travel Order Approved')
                        ->body('The employee has been notified.')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(
                    fn() => $this->record->status === 'pending'
                    && Auth::user()->role === User::ROLE_ADMIN
                )
                ->form([
                    Forms\Components\Textarea::make('rejection_remark')
                        ->label('Reason for Rejection')
                        ->required()
                        ->rows(3)
                        ->placeholder('Please provide a clear reason for rejecting this travel order...'),
                ])
                ->requiresConfirmation()
                ->modalHeading('Reject Travel Order')
                ->modalSubmitActionLabel('Yes, Reject')
                ->action(function (array $data) {
                    $this->record->update([
                        'status' => 'rejected',
                        'approved_by' => Auth::id(),        // ← was Auth::user()->name
                        'rejection_remark' => $data['rejection_remark'],
                    ]);

                    $this->record->creator->notify(new TravelOrderStatusUpdated($this->record));

                    Notification::make()
                        ->title('Travel Order Rejected')
                        ->body('The employee has been notified.')
                        ->danger()
                        ->send();
                }),

            Actions\Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn() => route('travel-order.print', $this->record->id))
                ->openUrlInNewTab()
                ->visible(fn() => $this->record->status === 'approved'),

            // Fixed: was 'employee', now uses User::ROLE_REGULAR = 'regular'
        ];
    }
}
