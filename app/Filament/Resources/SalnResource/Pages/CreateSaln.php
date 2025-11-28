<?php

namespace App\Filament\Resources\SalnResource\Pages;

use App\Filament\Resources\SalnResource;
use Filament\Resources\Pages\CreateRecord;
use App\Notifications\NewSalnFiled;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class CreateSaln extends CreateRecord
{
    protected static string $resource = SalnResource::class;

    protected function afterCreate(): void
    {
        parent::afterCreate();

        // Notify all admins
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new NewSalnFiled($this->record));
    }
}
