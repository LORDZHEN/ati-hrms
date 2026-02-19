<?php

namespace App\Filament\Resources\TravelOrderResource\Pages;

use App\Filament\Resources\TravelOrderResource;
use App\Models\TravelOrder;
use Filament\Actions;
use Filament\Forms;
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
                ->modalDescription(fn(TravelOrder $record) =>
                    'This will approve the travel order' .
                    ($record->travel_type === 'batch' ? ' and all tagged employee copies.' : '.')
                )
                ->visible(fn(TravelOrder $record) =>
                    Auth::user()->role === 'admin' &&
                    $record->status === 'pending'
                )
                ->action(function (TravelOrder $record) {
                    $updateData = [
                        'status'                            => 'approved',
                        'approved_by'                       => Auth::id(),
                        'approved_at'                       => now(),
                        'recommended_by_assistant_director' => true,
                        'recommended_by'                    => Auth::id(),
                        'approved_by_center_director'       => true,
                        'rejection_remark'                  => null,  // clear on approval
                    ];

                    $record->update($updateData);

                    if ($record->travel_type === 'batch' && $record->batch_id) {
                        TravelOrder::where('batch_id', $record->batch_id)
                            ->where('id', '!=', $record->id)
                            ->update($updateData);
                    }

                    $record->creator->notify(new TravelOrderStatusUpdated($record));

                    Notification::make()
                        ->title('Travel Order Approved')
                        ->body($record->travel_type === 'batch'
                            ? 'Batch travel order and all employee copies approved successfully.'
                            : 'Travel order approved successfully.')
                        ->success()
                        ->send();
                }),

            // ❌ REJECT (saves reason into dedicated rejection_remark column)
            Actions\Action::make('reject')
                ->label('Reject Travel Order')
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->modalHeading('Reject Travel Order')
                ->modalDescription(fn(TravelOrder $record) =>
                    'This will reject the travel order' .
                    ($record->travel_type === 'batch' ? ' and all tagged employee copies.' : '.') .
                    ' Please provide a reason so the employee can make the necessary corrections.'
                )
                ->modalWidth('lg')
                ->modalSubmitActionLabel('Confirm Rejection')
                ->form([
                    Forms\Components\Textarea::make('rejection_remark')
                        ->label('Rejection Reason')
                        ->placeholder('Explain why this travel order is being rejected...')
                        ->required()
                        ->rows(4)
                        ->maxLength(1000)
                        ->helperText('This remark will be visible to the employee.'),
                ])
                ->visible(fn(TravelOrder $record) =>
                    Auth::user()->role === 'admin' &&
                    $record->status === 'pending'
                )
                ->action(function (TravelOrder $record, array $data) {
                    $updateData = [
                        'status'           => 'rejected',
                        'approved_by'      => Auth::id(),
                        'approved_at'      => now(),
                        'rejection_remark' => $data['rejection_remark'],
                    ];

                    $record->update($updateData);

                    if ($record->travel_type === 'batch' && $record->batch_id) {
                        TravelOrder::where('batch_id', $record->batch_id)
                            ->where('id', '!=', $record->id)
                            ->update($updateData);
                    }

                    $record->creator->notify(new TravelOrderStatusUpdated($record));

                    Notification::make()
                        ->title('Travel Order Rejected')
                        ->body($record->travel_type === 'batch'
                            ? 'Batch travel order and all employee copies rejected.'
                            : 'Travel order rejected.')
                        ->danger()
                        ->send();
                }),

        ];
    }
}
