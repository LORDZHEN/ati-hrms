<?php

namespace App\Filament\Resources\TravelOrderResource\Pages;

use App\Filament\Resources\TravelOrderResource;
use Filament\Actions;
use App\Models\TravelOrder;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ListTravelOrders extends ListRecords
{
    protected static string $resource = TravelOrderResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [Actions\CreateAction::make()];

        if (auth()->check() && auth()->user()->role === 'admin') {
            $actions[] = Actions\Action::make('generateReport')
                ->label('Generate Report')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->modalHeading('Generate Travel Order Report')
                ->modalDescription('Create a detailed PDF report of travel orders within a specific period.')
                ->modalWidth('2xl')
                ->modalSubmitActionLabel('Generate PDF')
                ->form([
                    Grid::make(2)->schema([
                        Select::make('status')
                            ->label('Travel Order Status')
                            ->options([
                                'all'         => 'All',
                                'pending'     => 'Pending',
                                'recommended' => 'Recommended',
                                'approved'    => 'Approved',
                                'rejected'    => 'Rejected',
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
                                    'weekly'    => [$set('from', $now->copy()->startOfWeek()->toDateString()), $set('to', $now->copy()->endOfWeek()->toDateString())],
                                    'monthly'   => [$set('from', $now->copy()->startOfMonth()->toDateString()), $set('to', $now->copy()->endOfMonth()->toDateString())],
                                    'quarterly' => [$set('from', $now->copy()->startOfQuarter()->toDateString()), $set('to', $now->copy()->endOfQuarter()->toDateString())],
                                    'yearly'    => [$set('from', $now->copy()->startOfYear()->toDateString()), $set('to', $now->copy()->endOfYear()->toDateString())],
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
                    $url = route('travel-order.report', [
                        'status' => $data['status'] ?? 'all',
                        'period' => $data['period'],
                        'from'   => $data['from'],
                        'to'     => $data['to'],
                    ]);
                    $this->redirect($url, navigate: false);
                });
        }

        return $actions;
    }

    public function getTabs(): array
    {
        // Admin sees all MAIN orders (excluding single-employee tagged copies)
        if (Auth::user()->role === 'admin') {
            return [
                'all' => Tab::make('All Travel Orders')
                    ->icon('heroicon-o-rectangle-stack')
                    ->badge(fn() => TravelOrder::where(function ($query) {
                        $query->where('travel_type', 'solo')
                            ->orWhere(function ($q) {
                                $q->where('travel_type', 'batch')
                                    ->whereRaw('JSON_LENGTH(employee_ids) > 1');
                            });
                    })->count())
                    ->modifyQueryUsing(fn(Builder $query) =>
                        $query->where(function ($q) {
                            $q->where('travel_type', 'solo')
                                ->orWhere(function ($subQ) {
                                    $subQ->where('travel_type', 'batch')
                                        ->whereRaw('JSON_LENGTH(employee_ids) > 1');
                                });
                        })
                    ),
            ];
        }

        // Employees see their own orders and orders they are tagged in
        return [
            'my_orders' => Tab::make('My Travel Orders')
                ->icon('heroicon-o-user')
                ->badge(fn() => TravelOrder::where('created_by', Auth::id())
                    ->where(function ($query) {
                        $query->where('travel_type', 'solo')
                            ->orWhere(function ($q) {
                                $q->where('travel_type', 'batch')
                                    ->whereRaw('JSON_LENGTH(employee_ids) > 1');
                            });
                    })
                    ->count()
                )
                ->modifyQueryUsing(fn(Builder $query) =>
                    $query->where('created_by', Auth::id())
                        ->where(function ($q) {
                            $q->where('travel_type', 'solo')
                                ->orWhere(function ($subQ) {
                                    $subQ->where('travel_type', 'batch')
                                        ->whereRaw('JSON_LENGTH(employee_ids) > 1');
                                });
                        })
                ),

            'tagged' => Tab::make('Tagged Travel Orders')
                ->icon('heroicon-o-tag')
                ->badge(fn() => TravelOrder::where('travel_type', 'batch')
                    ->whereJsonContains('employee_ids', Auth::id())
                    ->whereRaw('JSON_LENGTH(employee_ids) = 1')
                    ->count()
                )
                ->badgeColor('success')
                ->modifyQueryUsing(fn(Builder $query) =>
                    $query->where('travel_type', 'batch')
                        ->whereJsonContains('employee_ids', Auth::id())
                        ->whereRaw('JSON_LENGTH(employee_ids) = 1')
                ),
        ];
    }
}
