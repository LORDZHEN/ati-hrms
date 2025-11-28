<?php

namespace App\Filament\Resources\LeaveApplicationResource\Pages;

use App\Filament\Resources\LeaveApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeaveApplications extends ListRecords
{
    protected static string $resource = LeaveApplicationResource::class;

    protected function getHeaderActions(): array
    {
        // Start with actions everyone can see
        $actions = [
            Actions\CreateAction::make(),
        ];

        // Add "Generate Report" only for admin users
        if (auth()->check() && auth()->user()->role === 'admin') {
            $actions[] = Actions\Action::make('generateReport')
                ->label('Generate Report')
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->url(fn() => route('leave-applications.report'))
                ->openUrlInNewTab();
        }

        return $actions;
    }
}
