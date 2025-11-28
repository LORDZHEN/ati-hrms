<?php

namespace App\Filament\Resources\SalnResource\Pages;

use App\Filament\Resources\SalnResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;

class ListSalns extends ListRecords
{
    protected static string $resource = SalnResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('view_print')
                ->label('View/Print')
                ->icon('heroicon-o-document-text')
                ->url(fn($record) => route('saln.print', $record->id))
                ->openUrlInNewTab(),
        ];
    }
}
