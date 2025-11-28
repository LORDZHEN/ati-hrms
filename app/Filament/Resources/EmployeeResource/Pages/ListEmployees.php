<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        $actions = auth()->user()?->role === 'admin'
            ? [
                Actions\CreateAction::make()->label('New Admin User'),
                Actions\Action::make('Generate Report')
                    ->button()
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->url(route('employee.report'))
                    ->openUrlInNewTab(),
            ]
            : [];

        return $actions;
    }



}
