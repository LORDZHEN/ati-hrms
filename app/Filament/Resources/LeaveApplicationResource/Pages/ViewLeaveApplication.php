<?php

namespace App\Filament\Resources\LeaveApplicationResource\Pages;

use App\Filament\Resources\LeaveApplicationResource;
use App\Models\LeaveApplication;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Notifications\Notification;

class ViewLeaveApplication extends ViewRecord
{
    protected static string $resource = LeaveApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [

            // 🔹 ADMIN ONLY — APPROVE
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () =>
                    auth()->user()->role === 'admin'
                    && $this->record->status === 'pending'
                )
                ->requiresConfirmation()
                ->action(function () {

                    $this->record->update([
                        'status' => 'approved',
                        'authorized_officer' =>
                            auth()->user()->first_name . ' ' . auth()->user()->last_name,
                        'date_approved_disapproved' => now(),
                    ]);

                    $notification = new \App\Notifications\LeaveApplicationStatusUpdated($this->record);
                    $notification->notifyUser($this->record->employee);

                    Notification::make()
                        ->title('Leave Approved')
                        ->success()
                        ->send();
                }),

            // 🔹 ADMIN ONLY — DISAPPROVE
            Actions\Action::make('disapprove')
                ->label('Disapprove')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () =>
                    auth()->user()->role === 'admin'
                    && $this->record->status === 'pending'
                )
                ->form([
                    \Filament\Forms\Components\Textarea::make('disapproval_reason')
                        ->label('Reason for Disapproval')
                        ->required(),
                ])
                ->requiresConfirmation()
                ->action(function (array $data) {

                    $this->record->update([
                        'status' => 'disapproved',
                        'authorized_officer' =>
                            auth()->user()->first_name . ' ' . auth()->user()->last_name,
                        'disapproval_reason' => $data['disapproval_reason'],
                        'date_approved_disapproved' => now(),
                    ]);

                    $notification = new \App\Notifications\LeaveApplicationStatusUpdated($this->record);
                    $notification->notifyUser($this->record->employee);

                    Notification::make()
                        ->title('Leave Disapproved')
                        ->danger()
                        ->send();
                }),
        ];
    }
}
