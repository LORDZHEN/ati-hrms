<?php

namespace App\Filament\Resources\PersonalDataSheetResource\Pages;

use App\Filament\Resources\PersonalDataSheetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;
use Filament\Forms\Components\ViewField;

class ListPersonalDataSheets extends ListRecords
{
    protected static string $resource = PersonalDataSheetResource::class;

    /**
     * 🔹 Customize header actions
     */
    protected function getHeaderActions(): array
    {
        $actions = [
            Actions\Action::make('createPDS')
                ->label('New Personal Data Sheet')
                ->color('primary')
                ->modalHeading('New Personal Data Sheet')
                ->modalWidth('6xl')
                ->form([
                    ViewField::make('pdsForm')->view('livewire.employee.pds.edit-pds')
                ]),
        ];

        // Admin-only report generation action
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
                    DatePicker::make('from')->label('From')->required(),
                    DatePicker::make('to')->label('To')->required()->after('from'),
                ])
                ->action(function (array $data) {
                    return redirect()->route('pds.report', [
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
