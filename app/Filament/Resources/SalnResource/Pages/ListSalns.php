<?php

namespace App\Filament\Resources\SalnResource\Pages;

use App\Filament\Resources\SalnResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;

class ListSalns extends ListRecords
{
    protected static string $resource = SalnResource::class;

    // Header actions (top-right buttons)
    protected function getHeaderActions(): array
    {
        // Actions visible to all users
        $actions = [
            Actions\CreateAction::make(),
        ];

        // Add "Generate Report" only for admin users
        if (auth()->check() && auth()->user()->role === 'admin') {
            $actions[] = Actions\Action::make('comprehensive_report')
                ->label('Generate Report')
                ->icon('heroicon-o-document-text')
                ->url(route('saln.report'))
                ->openUrlInNewTab();
        }

        return $actions;
    }

    // Row/table actions (per record)
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
