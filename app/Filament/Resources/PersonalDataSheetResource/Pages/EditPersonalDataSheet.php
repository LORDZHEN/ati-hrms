<?php

namespace App\Filament\Resources\PersonalDataSheetResource\Pages;

use App\Filament\Resources\PersonalDataSheetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditPersonalDataSheet extends EditRecord
{
    protected static string $resource = PersonalDataSheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Print PDS')
                ->icon('heroicon-o-printer')
                ->visible(fn() => $this->record->status === 'approved')
                ->url(fn() => route('pds.print', $this->record->id))
                ->openUrlInNewTab(),

            Actions\DeleteAction::make()
                ->visible(fn() =>
                    Auth::user()->role === 'admin' ||
                    (Auth::user()->role === 'employee' && $this->record->status !== 'approved')
                ),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
