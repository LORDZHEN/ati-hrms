<?php

namespace App\Filament\Resources\LeaveApplicationResource\Pages;

use App\Filament\Resources\LeaveApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeaveApplication extends EditRecord
{
    protected static string $resource = LeaveApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Print Leave Form')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn() => route('leave_application.print', $this->record))
                ->openUrlInNewTab()
                ->visible(fn() => $this->record->status === 'approved'),

            Actions\DeleteAction::make()
                ->visible(fn() => $this->record->status === 'pending'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // ============================================================
    // NOTE: Leave Application has NO repeaters
    // Unlike PDS/SALN, no add/remove methods needed here
    // All fields are simple inputs without dynamic add/remove
    // ============================================================
}
