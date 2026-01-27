<?php

namespace App\Filament\Resources\SalnResource\Pages;

use App\Filament\Resources\SalnResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;
use Filament\Tables\Actions\Action;

class ListSalns extends ListRecords
{
    protected static string $resource = SalnResource::class;

    // Header actions (top-right buttons)
    protected function getHeaderActions(): array
    {
        $actions = [
            Actions\CreateAction::make(),
        ];

        // Add "Generate Report" only for admin users
        if (auth()->check() && auth()->user()->role === 'admin') {
            $actions[] = Actions\Action::make('generateReport')
                ->label('Generate Report')
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->modalHeading('Generate SALN Report')
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
                    return redirect()->route('saln.report', [
                        'period' => $data['period'],
                        'from'   => $data['from'],
                        'to'     => $data['to'],
                    ]);
                })
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
