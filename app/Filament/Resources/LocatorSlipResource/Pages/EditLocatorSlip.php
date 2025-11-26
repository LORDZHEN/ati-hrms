<?php

namespace App\Filament\Resources\LocatorSlipResource\Pages;

use App\Filament\Resources\LocatorSlipResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLocatorSlip extends EditRecord
{
    protected static string $resource = LocatorSlipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
