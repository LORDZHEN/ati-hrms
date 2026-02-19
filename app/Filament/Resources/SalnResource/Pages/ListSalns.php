<?php

namespace App\Filament\Resources\SalnResource\Pages;

use App\Filament\Resources\SalnResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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

        // Admin-only: generate PDF report
        if (auth()->check() && auth()->user()->role === 'admin') {
            $actions[] = Actions\Action::make('generateReport')
                ->label('Generate Report')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->modalHeading('Generate SALN Report')
                ->modalDescription('Create a detailed PDF report of SALN submissions within a specific period.')
                ->modalWidth('2xl')
                ->modalSubmitActionLabel('Generate PDF')
                ->form([
                    Grid::make(2)->schema([
                        Select::make('remarks_filter')
                            ->label('Remarks Status')
                            ->options([
                                'all'          => 'All',
                                'with_remarks' => 'With Remarks',
                                'no_remarks'   => 'Without Remarks',
                            ])
                            ->default('all')
                            ->required()
                            ->native(false),

                        Select::make('period')
                            ->label('Report Period')
                            ->options([
                                'weekly'    => 'This Week',
                                'monthly'   => 'This Month',
                                'quarterly' => 'This Quarter',
                                'yearly'    => 'This Year',
                                'custom'    => 'Custom Date Range',
                            ])
                            ->default('monthly')
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $now = Carbon::now();

                                match ($state) {
                                    'weekly'    => [$set('from', $now->copy()->startOfWeek()->toDateString()),    $set('to', $now->copy()->endOfWeek()->toDateString())],
                                    'monthly'   => [$set('from', $now->copy()->startOfMonth()->toDateString()),   $set('to', $now->copy()->endOfMonth()->toDateString())],
                                    'quarterly' => [$set('from', $now->copy()->startOfQuarter()->toDateString()), $set('to', $now->copy()->endOfQuarter()->toDateString())],
                                    'yearly'    => [$set('from', $now->copy()->startOfYear()->toDateString()),    $set('to', $now->copy()->endOfYear()->toDateString())],
                                    default     => null,
                                };
                            }),
                    ]),

                    Grid::make(2)->schema([
                        DatePicker::make('from')
                            ->label('From Date')
                            ->required()
                            ->native(false)
                            ->default(Carbon::now()->startOfMonth()->toDateString()),

                        DatePicker::make('to')
                            ->label('To Date')
                            ->required()
                            ->native(false)
                            ->after('from')
                            ->default(Carbon::now()->endOfMonth()->toDateString()),
                    ]),
                ])
                ->action(function (array $data) {
                    $url = route('saln.report', [
                        'remarks_filter' => $data['remarks_filter'] ?? 'all',
                        'period'         => $data['period'],
                        'from'           => $data['from'],
                        'to'             => $data['to'],
                    ]);

                    // Full navigation so browser receives the PDF stream
                    $this->redirect($url, navigate: false);
                });
        }

        return $actions;
    }
}
