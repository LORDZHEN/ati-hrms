<?php

namespace App\Filament\Resources\LocatorSlipResource\Pages;

use App\Filament\Resources\LocatorSlipResource;
use App\Models\User;
use App\Notifications\LocatorSlipSubmitted;
use Filament\Resources\Pages\CreateRecord;

class CreateLocatorSlip extends CreateRecord
{
    protected static string $resource = LocatorSlipResource::class;

    protected function afterCreate(): void
    {
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new LocatorSlipSubmitted($this->record));
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
