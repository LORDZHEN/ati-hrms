<?php
// ============================================================
// FILE: app/Filament/Resources/SalnResource/Pages/ListSalns.php
// ============================================================

namespace App\Filament\Resources\SalnResource\Pages;

use App\Filament\Resources\SalnResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;

class ListSalns extends ListRecords
{
    protected static string $resource = SalnResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [
            Actions\CreateAction::make()
                ->label('File SALN')
                ->icon('heroicon-o-plus'),
        ];

        // Admin-only report generation (matching PDS pattern)
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
                            'weekly'  => 'Weekly',
                            'monthly' => 'Monthly',
                            'yearly'  => 'Yearly',
                        ])
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $now = Carbon::now();
                            match ($state) {
                                'weekly'  => [$set('from', $now->startOfWeek()->toDateString()), $set('to', $now->endOfWeek()->toDateString())],
                                'monthly' => [$set('from', $now->startOfMonth()->toDateString()), $set('to', $now->endOfMonth()->toDateString())],
                                'yearly'  => [$set('from', $now->startOfYear()->toDateString()), $set('to', $now->endOfYear()->toDateString())],
                                default   => null,
                            };
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
}
