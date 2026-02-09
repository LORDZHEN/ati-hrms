<?php

namespace App\Filament\Resources\TravelOrderResource\Pages;

use App\Filament\Resources\TravelOrderResource;
use App\Models\TravelOrder;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Notifications\TravelOrderStatusUpdated;

class ViewTravelOrder extends ViewRecord
{
    protected static string $resource = TravelOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ✅ APPROVE
            Actions\Action::make('approve')
                ->label('Approve Travel Order')
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn(TravelOrder $record) =>
                    Auth::user()->role === 'admin' &&
                    $record->status === 'pending'
                )
                ->action(function (TravelOrder $record) {
                    $record->update([
                        'status' => 'approved',
                        'approved_by' => Auth::id(),
                        'approved_at' => now(),
                        'recommended_by_assistant_director' => true,
                        'recommended_by' => Auth::id(),
                    ]);

                    $record->creator->notify(
                        new TravelOrderStatusUpdated($record)
                    );

                    Notification::make()
                        ->title('Travel order approved')
                        ->success()
                        ->send();
                }),

            // ❌ REJECT
            Actions\Action::make('reject')
                ->label('Reject Travel Order')
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn(TravelOrder $record) =>
                    Auth::user()->role === 'admin' &&
                    $record->status === 'pending'
                )
                ->action(function (TravelOrder $record) {
                    $record->update([
                        'status' => 'rejected',
                        'approved_by' => Auth::id(),
                        'approved_at' => now(),
                    ]);

                    $record->creator->notify(
                        new TravelOrderStatusUpdated($record)
                    );

                    Notification::make()
                        ->title('Travel order rejected')
                        ->danger()
                        ->send();
                }),
        ];
    }
}
