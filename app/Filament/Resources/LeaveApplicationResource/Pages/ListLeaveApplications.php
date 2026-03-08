<?php

namespace App\Filament\Resources\LeaveApplicationResource\Pages;

use App\Filament\Resources\LeaveApplicationResource;
use App\Filament\Widgets\LeaveCreditWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Carbon\Carbon;

class ListLeaveApplications extends ListRecords
{
    protected static string $resource = LeaveApplicationResource::class;

    // ── Widget: show leave credit balances to employees above the table ───────
    // canView() inside LeaveCreditWidget already restricts this to employees.
    protected function getHeaderWidgets(): array
    {
        return [
            LeaveCreditWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Employees get a "New leave application" button.
        // canCreate() on the Resource already gates this to employees only,
        // but we guard here too for defence-in-depth.
        if (auth()->user()->role === 'employee') {
            $actions[] = Actions\CreateAction::make()
                ->label('New leave application')
                ->icon('heroicon-o-plus')
                ->color('primary');
        }

        // Admins get the Generate Report modal.
        // WHY: This is a page-level Actions\Action (not a table action) so it
        // can legitimately live in getHeaderActions(). The redirect inside
        // ->action() navigates to the report route without a full page reload.
        if (auth()->user()->role === 'admin') {
            $actions[] = Actions\Action::make('generateReport')
                ->label('Generate Report')
                ->icon('heroicon-o-document-chart-bar')
                ->color('info')
                ->modalHeading('Generate Leave Application Report')
                ->modalDescription('Create a detailed PDF report of leave applications within a specific period.')
                ->modalWidth('2xl')
                ->modalSubmitActionLabel('Generate PDF')
                ->form([
                    Grid::make(2)->schema([
                        Select::make('status')
                            ->label('Leave Status')
                            ->options([
                                'all' => 'All Applications',
                                'approved' => 'Approved',
                                'disapproved' => 'Disapproved',
                                'pending' => 'Pending',
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
                    $url = route('leave-applications.report', [
                        'status' => $data['status'] ?? 'all',
                        'period' => $data['period'] ?? 'monthly',
                        'from' => $data['from'],
                        'to' => $data['to'],
                    ]);

                    $this->redirect($url, navigate: false);
                });
        }

        return $actions;
    }
}
