<?php

namespace App\Filament\Resources\PersonalDataSheetResource\Pages;

use App\Filament\Resources\PersonalDataSheetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPersonalDataSheet extends EditRecord
{
    protected static string $resource = PersonalDataSheetResource::class;

    protected function getHeaderActions(): array
{
    return [
        \Filament\Actions\Action::make('print')
            ->label('Print PDS')
            ->icon('heroicon-o-printer')
            ->url(fn() => route('pds.print', $this->record->id))
            ->openUrlInNewTab(),

        \Filament\Actions\DeleteAction::make(),
    ];
}

}
