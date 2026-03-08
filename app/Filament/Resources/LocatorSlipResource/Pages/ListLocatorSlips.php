<?php

namespace App\Filament\Resources\LocatorSlipResource\Pages;

use App\Filament\Resources\LocatorSlipResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Carbon\Carbon;

class ListLocatorSlips extends ListRecords
{
    protected static string $resource = LocatorSlipResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Employees get the "New Locator Slip" create button.
        // canCreate() on the Resource already gates this, but we guard
        // here too so the button never appears for admins.
        if (auth()->user()->role === 'employee') {
            $actions[] = Actions\CreateAction::make()
                ->label('New Locator Slip')
                ->icon('heroicon-o-plus')
                ->color('primary');
        }

        // Admins get the Generate Report modal.
        // WHY: This is Filament\Actions\Action (page-level) — safe to use
        // in getHeaderActions() unlike Tables\Actions\Action which would throw.
        if (auth()->user()->role === 'admin') {
            $actions[] = Actions\Action::make('generateReport')
                ->label('Generate Report')
                ->icon('heroicon-o-document-chart-bar')
                ->color('info')
                ->modalHeading('Generate Locator Slip Report')
                ->modalDescription('Create a detailed PDF report of locator slips within a specific period.')
                ->modalWidth('2xl')
                ->modalSubmitActionLabel('Generate PDF')
                ->form([
                    Grid::make(2)->schema([
                        Select::make('status')
                            ->label('Locator Slip Status')
                            ->options([
                                'all' => 'All',
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'disapproved' => 'Disapproved',
                            ])
                            ->default('all')
                            ->required()
                            ->native(false),

                        Select::make('period')
                            ->label('Report Period')
                            ->options([
                                'weekly' => 'This Week',
                                'monthly' => 'This Month',
                                'quarterly' => 'This Quarter',
                                'yearly' => 'This Year',
                                'custom' => 'Custom Date Range',
                            ])
                            ->default('monthly')
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $now = Carbon::now();
                                match ($state) {
                                    'weekly' => [
                                        $set('from', $now->copy()->startOfWeek()->toDateString()),
                                        $set('to', $now->copy()->endOfWeek()->toDateString()),
                                    ],
                                    'monthly' => [
                                        $set('from', $now->copy()->startOfMonth()->toDateString()),
                                        $set('to', $now->copy()->endOfMonth()->toDateString()),
                                    ],
                                    'quarterly' => [
                                        $set('from', $now->copy()->startOfQuarter()->toDateString()),
                                        $set('to', $now->copy()->endOfQuarter()->toDateString()),
                                    ],
                                    'yearly' => [
                                        $set('from', $now->copy()->startOfYear()->toDateString()),
                                        $set('to', $now->copy()->endOfYear()->toDateString()),
                                    ],
                                    default => null,
                                };
                            }),
                    ]),

                    Grid::make(2)->schema([
                        DatePicker::make('from')
                            ->label('From Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('M d, Y')
                            ->default(Carbon::now()->startOfMonth()->toDateString()),

                        DatePicker::make('to')
                            ->label('To Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('M d, Y')
                            ->after('from')
                            ->default(Carbon::now()->endOfMonth()->toDateString()),
                    ]),
                ])
                ->action(function (array $data) {
                    $url = route('locator-slip.report', [
                        'status' => $data['status'] ?? 'all',
                        'period' => $data['period'],
                        'from' => $data['from'],
                        'to' => $data['to'],
                    ]);

                    $this->redirect($url, navigate: false);
                });
        }

        return $actions;
    }
}
