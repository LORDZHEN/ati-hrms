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
                ->modalHeading('Approve Travel Order')
                ->modalDescription('This will approve the travel order' .
                    ($this->record->travel_type === 'batch' ? ' and all tagged employee copies.' : '.'))
                ->visible(fn(TravelOrder $record) =>
                    Auth::user()->role === 'admin' &&
                    $record->status === 'pending'
                )
                ->action(function (TravelOrder $record) {
                    // Update main record
                    $record->update([
                        'status' => 'approved',
                        'approved_by' => Auth::id(),
                        'approved_at' => now(),
                        'recommended_by_assistant_director' => true,
                        'recommended_by' => Auth::id(),
                        'approved_by_center_director' => true,
                    ]);

                    // If batch, approve all tagged copies
                    if ($record->travel_type === 'batch' && $record->batch_id) {
                        TravelOrder::where('batch_id', $record->batch_id)
                            ->where('id', '!=', $record->id)
                            ->update([
                                'status' => 'approved',
                                'approved_by' => Auth::id(),
                                'approved_at' => now(),
                                'recommended_by_assistant_director' => true,
                                'recommended_by' => Auth::id(),
                                'approved_by_center_director' => true,
                            ]);
                    }

                    // Notify creator
                    $record->creator->notify(new TravelOrderStatusUpdated($record));

                    Notification::make()
                        ->title('Travel Order Approved')
                        ->body($record->travel_type === 'batch' ?
                            'Batch travel order and all employee copies approved successfully.' :
                            'Travel order approved successfully.')
                        ->success()
                        ->send();
                }),

            // ❌ REJECT
            Actions\Action::make('reject')
                ->label('Reject Travel Order')
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Reject Travel Order')
                ->modalDescription('This will reject the travel order' .
                    ($this->record->travel_type === 'batch' ? ' and all tagged employee copies.' : '.'))
                ->visible(fn(TravelOrder $record) =>
                    Auth::user()->role === 'admin' &&
                    $record->status === 'pending'
                )
                ->action(function (TravelOrder $record) {
                    // Update main record
                    $record->update([
                        'status' => 'rejected',
                        'approved_by' => Auth::id(),
                        'approved_at' => now(),
                    ]);

                    // If batch, reject all tagged copies
                    if ($record->travel_type === 'batch' && $record->batch_id) {
                        TravelOrder::where('batch_id', $record->batch_id)
                            ->where('id', '!=', $record->id)
                            ->update([
                                'status' => 'rejected',
                                'approved_by' => Auth::id(),
                                'approved_at' => now(),
                            ]);
                    }

                    // Notify creator
                    $record->creator->notify(new TravelOrderStatusUpdated($record));

                    Notification::make()
                        ->title('Travel Order Rejected')
                        ->body($record->travel_type === 'batch' ?
                            'Batch travel order and all employee copies rejected.' :
                            'Travel order rejected.')
                        ->danger()
                        ->send();
                }),
        ];
    }
}
