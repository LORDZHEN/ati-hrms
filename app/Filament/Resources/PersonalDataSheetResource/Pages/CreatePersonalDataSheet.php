<?php

namespace App\Filament\Resources\PersonalDataSheetResource\Pages;

use App\Filament\Resources\PersonalDataSheetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Notifications\PDSSubmittedNotification;
use Illuminate\Support\Facades\Notification as LaravelNotification;

class CreatePersonalDataSheet extends CreateRecord
{
    protected static string $resource = PersonalDataSheetResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
{
    // Notify Admins
    $admins = User::where('role', 'admin')->get();

    LaravelNotification::send($admins, new PDSSubmittedNotification(auth()->user(), $this->record));

    // Optionally, show toast to current user
    Notification::make()
        ->title('PDS Submitted Successfully!')
        ->success()
        ->send();
}
}
