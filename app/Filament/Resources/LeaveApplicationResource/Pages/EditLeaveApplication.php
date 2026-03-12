<?php

namespace App\Filament\Resources\LeaveApplicationResource\Pages;

use App\Filament\Resources\LeaveApplicationResource;
use App\Models\User;
use App\Notifications\LeaveApplicationSubmitted;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditLeaveApplication extends EditRecord
{
    protected static string $resource = LeaveApplicationResource::class;

    // =========================================================================
    //  HEADER ACTIONS
    //
    //  Print  — only when approved
    //  Delete — only when pending (employee's own record)
    // =========================================================================

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Print Leave Form')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->visible(fn() => $this->record->status === 'approved')
                ->url(fn() => route('leave_application.print', $this->record))
                ->openUrlInNewTab(),

            Actions\DeleteAction::make()
                ->visible(fn() => $this->record->status === 'pending'),
        ];
    }

    // =========================================================================
    //  RESUBMISSION — force status back to pending on employee save
    // =========================================================================

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (Auth::user()->role !== 'admin') {
            $data['status'] = 'pending';
        }
        return $data;
    }

    protected function afterSave(): void
    {
        if (Auth::user()->role === 'admin')
            return;

        // Stamp new submission time
        $this->record->updateQuietly(['created_at' => now()]);

        // Notify all admins
        $admins = User::where('role', User::ROLE_ADMIN)->get();
        foreach ($admins as $admin) {
            $admin->notify(new LeaveApplicationSubmitted($this->record));
        }

        Notification::make()
            ->title('Leave Application Resubmitted')
            ->body('Your leave application has been updated and sent for review.')
            ->success()
            ->send();
    }

    // =========================================================================
    //  FORM ACTIONS
    // =========================================================================

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Resubmit Application')
                ->submit('save')
                ->color('primary')
                ->icon('heroicon-o-paper-airplane'),

            Actions\Action::make('cancel')
                ->label('Cancel')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->icon('heroicon-o-x-mark'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
