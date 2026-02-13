<?php

namespace App\Filament\Resources\PersonalDataSheetResource\Pages;

use App\Filament\Resources\PersonalDataSheetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ListPersonalDataSheets extends ListRecords
{
    protected static string $resource = PersonalDataSheetResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        // ✅ Create PDS (Standard Filament Create Action)
        if (Auth::user()->role === 'employee') {
            $actions[] = Actions\CreateAction::make()
                ->label('New Personal Data Sheet')
                ->color('primary');
        }

        // ✅ Admin-only report generation
        if (auth()->check() && auth()->user()->role === 'admin') {
            $actions[] = Actions\Action::make('generateReport')
                ->label('Generate Report')
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->modalHeading('Generate PDS Report')
                ->modalSubmitActionLabel('Generate')
                ->form([
                    Select::make('period')
                        ->label('Report Period')
                        ->options([
                            'weekly' => 'Weekly',
                            'monthly' => 'Monthly',
                            'yearly' => 'Yearly',
                        ])
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $now = Carbon::now();

                            match ($state) {
                                'weekly' => [
                                    $set('from', $now->startOfWeek()->toDateString()),
                                    $set('to', $now->endOfWeek()->toDateString()),
                                ],
                                'monthly' => [
                                    $set('from', $now->startOfMonth()->toDateString()),
                                    $set('to', $now->endOfMonth()->toDateString()),
                                ],
                                'yearly' => [
                                    $set('from', $now->startOfYear()->toDateString()),
                                    $set('to', $now->endOfYear()->toDateString()),
                                ],
                            };
                        }),
                    DatePicker::make('from')->required(),
                    DatePicker::make('to')->required()->after('from'),
                ])
                ->action(fn (array $data) =>
                    redirect()->route('pds.report', $data)
                )
                ->openUrlInNewTab();
        }

        return $actions;
    }
}
