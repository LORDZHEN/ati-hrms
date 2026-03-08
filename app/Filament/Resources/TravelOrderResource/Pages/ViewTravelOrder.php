<?php

namespace App\Filament\Resources\TravelOrderResource\Pages;

use App\Filament\Resources\TravelOrderResource;
use App\Models\TravelOrder;
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

            // ── APPROVE ──────────────────────────────────────────────────────
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Approve Travel Order')
                ->modalDescription('This will mark the travel order as approved and notify the employee.')
                ->modalSubmitActionLabel('Yes, Approve')
                ->visible(
                    fn() =>
                    $this->record->status === 'pending' &&
                    Auth::user()->role === 'admin'
                )
                ->action(function () {
                    $this->record->update([
                        'status' => 'approved',
                        'approved_by' => Auth::user()->name,
                        'approved_at' => now(),
                    ]);

                    // WHY: Notify AFTER update — employee is never notified
                    // about an approval that fails to persist.
                    $this->record->creator->notify(new TravelOrderStatusUpdated($this->record));

                    Notification::make()
                        ->title('Travel Order Approved')
                        ->body('The employee has been notified.')
                        ->success()
                        ->send();
                }),

            // ── REJECT ───────────────────────────────────────────────────────
            Actions\Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(
                    fn() =>
                    $this->record->status === 'pending' &&
                    Auth::user()->role === 'admin'
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
                        'approved_by' => Auth::user()->name,
                        'rejection_remark' => $data['rejection_remark'],
                    ]);

                    $this->record->creator->notify(new TravelOrderStatusUpdated($this->record));

                    Notification::make()
                        ->title('Travel Order Rejected')
                        ->body('The employee has been notified.')
                        ->danger()
                        ->send();
                }),

            // ── PRINT ─────────────────────────────────────────────────────────
            Actions\Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn() => route('travel-order.print', $this->record->id))
                ->openUrlInNewTab()
                ->visible(fn() => $this->record->status === 'approved'),

            // ── EDIT / REVISE ─────────────────────────────────────────────────
            Actions\EditAction::make()
                ->label(fn() => $this->record->status === 'rejected' ? 'Revise & Resubmit' : 'Edit')
                ->visible(
                    fn() =>
                    (Auth::user()->role === 'admin' && $this->record->status === 'pending') ||
                    (Auth::user()->role === 'employee'
                        && $this->record->created_by === Auth::id()
                        && $this->record->status === 'rejected')
                ),
        ];
    }
}
