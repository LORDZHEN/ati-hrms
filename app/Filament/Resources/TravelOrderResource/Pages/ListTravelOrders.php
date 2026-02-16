<?php

namespace App\Filament\Resources\TravelOrderResource\Pages;

use App\Filament\Resources\TravelOrderResource;
use Filament\Actions;
use App\Models\TravelOrder;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\DatePicker;
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
                ->color('primary')
                ->modalHeading('Generate Travel Order Report')
                ->modalSubmitActionLabel('Generate')
                ->form([
                    Select::make('status')
                        ->label('Travel Order Status')
                        ->options([
                            'all' => 'All',
                            'recommended' => 'Recommended',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ])
                        ->default('all')
                        ->required(),

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
                    return redirect()->route('travel-order.report', [
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

    public function getTabs(): array
    {
        // Admin sees all MAIN orders (excluding tagged copies)
        if (Auth::user()->role === 'admin') {
            return [
                'all' => Tab::make('All Travel Orders')
                    ->icon('heroicon-o-rectangle-stack')
                    ->badge(fn() => TravelOrder::where(function($query) {
                        // Show main batch orders (created_by matches batch creator)
                        // Or solo orders
                        $query->where('travel_type', 'solo')
                              ->orWhere(function($q) {
                                  $q->where('travel_type', 'batch')
                                    ->whereRaw('JSON_LENGTH(employee_ids) > 1');
                              });
                    })->count())
                    ->modifyQueryUsing(fn(Builder $query) =>
                        $query->where(function($q) {
                            // Show main batch orders or solo orders
                            $q->where('travel_type', 'solo')
                              ->orWhere(function($subQ) {
                                  $subQ->where('travel_type', 'batch')
                                      ->whereRaw('JSON_LENGTH(employee_ids) > 1');
                              });
                        })
                    ),
            ];
        }

        // Employees see two tabs
        return [
            'my_orders' => Tab::make('My Travel Orders')
                ->icon('heroicon-o-user')
                ->badge(fn() => TravelOrder::where('created_by', Auth::id())
                    ->where(function($query) {
                        // Show main batch orders or solo orders
                        $query->where('travel_type', 'solo')
                              ->orWhere(function($q) {
                                  $q->where('travel_type', 'batch')
                                    ->whereRaw('JSON_LENGTH(employee_ids) > 1');
                              });
                    })
                    ->count()
                )
                ->modifyQueryUsing(fn(Builder $query) =>
                    $query->where('created_by', Auth::id())
                        ->where(function($q) {
                            // Show main batch orders or solo orders
                            $q->where('travel_type', 'solo')
                              ->orWhere(function($subQ) {
                                  $subQ->where('travel_type', 'batch')
                                      ->whereRaw('JSON_LENGTH(employee_ids) > 1');
                              });
                        })
                ),

            'tagged' => Tab::make('Tagged Travel Orders')
                ->icon('heroicon-o-tag')
                ->badge(fn() => TravelOrder::where('travel_type', 'batch')
                    ->whereJsonContains('employee_ids', Auth::id())
                    ->whereRaw('JSON_LENGTH(employee_ids) = 1') // Only tagged copies
                    ->count()
                )
                ->badgeColor('success')
                ->modifyQueryUsing(fn(Builder $query) =>
                    $query->where('travel_type', 'batch')
                        ->whereJsonContains('employee_ids', Auth::id())
                        ->whereRaw('JSON_LENGTH(employee_ids) = 1') // Only tagged copies
                ),
        ];
    }
}
