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
            // Only pending slips can be deleted.
            // canDelete() on the Resource enforces this at the model level,
            // but we gate the button here too for defence-in-depth.
            Actions\DeleteAction::make()
                ->visible(fn() => $this->record->status === 'pending'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
