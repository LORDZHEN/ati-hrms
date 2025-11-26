<?php

namespace App\Filament\Resources\LocatorSlipResource\Pages;

use App\Filament\Resources\LocatorSlipResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLocatorSlip extends CreateRecord
{
    protected static string $resource = LocatorSlipResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
