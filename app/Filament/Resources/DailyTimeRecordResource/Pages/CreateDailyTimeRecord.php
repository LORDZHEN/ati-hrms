<?php

namespace App\Filament\Resources\DailyTimeRecordResource\Pages;

use App\Filament\Resources\DailyTimeRecordResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;


class CreateDailyTimeRecord extends CreateRecord
{
    protected static string $resource = DailyTimeRecordResource::class;

    public function mount(): void
    {
        abort_unless(Auth::user()->role === \App\Models\User::ROLE_ADMIN, 403);
    }
    protected function afterCreate(): void
    {
        $record = $this->record;

        $user = $record->employee;

        if ($user) {
            \Filament\Notifications\Notification::make()
                ->title('New Daily Time Record')
                ->body("A new Daily Time Record has been uploaded for you.")
                ->success()
                ->sendToDatabase($user);
        }
    }



}
