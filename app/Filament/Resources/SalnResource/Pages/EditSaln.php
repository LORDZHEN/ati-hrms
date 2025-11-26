<?php

namespace App\Filament\Resources\SalnResource\Pages;

use App\Filament\Resources\SalnResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSaln extends EditRecord
{
    protected static string $resource = SalnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
