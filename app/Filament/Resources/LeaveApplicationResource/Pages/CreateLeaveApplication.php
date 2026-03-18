<?php

namespace App\Filament\Resources\LeaveApplicationResource\Pages;

use App\Filament\Resources\LeaveApplicationResource;
use App\Filament\Widgets\LeaveCreditWidget;
use App\Models\User;
use App\Notifications\LeaveApplicationSubmitted;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions;
use App\Models\LeaveCredit;
use Filament\Notifications\Notification;

class CreateLeaveApplication extends CreateRecord
{
    protected static string $resource = LeaveApplicationResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            LeaveCreditWidget::class,
        ];
    }

    /**
     * Runs before the record is created.
     * Blocks submission when the employee has insufficient leave credits.
     */
    protected function beforeCreate(): void
    {
        $data = $this->form->getState();
        $leaveType = $data['type_of_leave'] ?? null;
        $daysNeeded = (float) ($data['number_of_working_days'] ?? 0);

        // Only validate types that have a tracked balance column
        $balanceCol = LeaveCredit::balanceColumn((string) $leaveType);

        if ($balanceCol === null || $daysNeeded <= 0) {
            return; // Non-tracked leave types pass through freely
        }

        $credit = LeaveCredit::where('user_id', auth()->id())->first();
        $available = $credit ? (float) $credit->{$balanceCol} : 0.0;

        if ($daysNeeded > $available) {
            $leaveLabel = str_replace('_', ' ', ucwords((string) $leaveType, '_'));

            Notification::make()
                ->danger()
                ->title('Insufficient Leave Credits')
                ->body("You are applying for {$daysNeeded} working day(s) of {$leaveLabel}, but your available balance is {$available} day(s).")
                ->send();

            $this->halt(); // Stops the create lifecycle — record is NOT saved
        }
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Submit Leave Application')
                ->submit('create')
                ->color('primary')
                ->icon('heroicon-o-paper-airplane'),

            Actions\Action::make('cancel')
                ->label('Cancel')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->icon('heroicon-o-x-mark'),
        ];
    }

    protected function afterCreate(): void
    {
        $admins = User::where('role', User::ROLE_ADMIN)->get();

        foreach ($admins as $admin) {
            $admin->notify(new LeaveApplicationSubmitted($this->record));
        }

        \Filament\Notifications\Notification::make()
            ->title('Leave Application Submitted')
            ->body('Your leave application has been sent for review.')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
