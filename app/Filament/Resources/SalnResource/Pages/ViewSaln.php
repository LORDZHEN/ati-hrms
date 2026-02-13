<?php

namespace App\Filament\Resources\SalnResource\Pages;

use App\Filament\Resources\SalnResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSaln extends ViewRecord
{
    protected static string $resource = SalnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Print SALN')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn() => route('saln.print', $this->record))
                ->openUrlInNewTab(),

            Actions\EditAction::make()
                ->visible(fn() => auth()->user()?->role !== 'admin'),
        ];
    }
}
