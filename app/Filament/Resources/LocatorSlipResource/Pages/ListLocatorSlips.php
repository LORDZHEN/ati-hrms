<?php

namespace App\Filament\Resources\LocatorSlipResource\Pages;

use App\Filament\Resources\LocatorSlipResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;

class ListLocatorSlips extends ListRecords
{
    protected static string $resource = LocatorSlipResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [
            Actions\CreateAction::make(),
        ];

        if (auth()->check() && auth()->user()->role === 'admin') {
            $actions[] = Actions\Action::make('generateReport')
                ->label('Generate Report')
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->modalHeading('Generate Locator Slip Report')
                ->modalSubmitActionLabel('Generate')
                ->form([
                    // Status dropdown
                    Select::make('status')
                        ->label('Locator Slip Status')
                        ->nullable() // allow "All"
                        ->options([
                            'all' => 'All',
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'disapproved' => 'Disapproved',
                        ])
                        ->default('all')
                        ->reactive(),

                    // Period dropdown
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
                            if ($state === 'weekly') {
                                $set('from', $now->startOfWeek()->toDateString());
                                $set('to', $now->endOfWeek()->toDateString());
                            }
                            if ($state === 'monthly') {
                                $set('from', $now->startOfMonth()->toDateString());
                                $set('to', $now->endOfMonth()->toDateString());
                            }
                            if ($state === 'yearly') {
                                $set('from', $now->startOfYear()->toDateString());
                                $set('to', $now->endOfYear()->toDateString());
                            }
                        }),

                    DatePicker::make('from')
                        ->label('From')
                        ->required(),

                    DatePicker::make('to')
                        ->label('To')
                        ->required()
                        ->after('from'),
                ])
                ->action(function (array $data) {
                    return redirect()->route('locator-slip.report', [
                        'status' => $data['status'] ?? 'all',
                        'period' => $data['period'],
                        'from' => $data['from'],
                        'to' => $data['to'],
                    ]);
                })
                ->openUrlInNewTab();
        }

        return $actions;
    }
}
