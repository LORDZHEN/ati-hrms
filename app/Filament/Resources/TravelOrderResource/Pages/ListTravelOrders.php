<?php

namespace App\Filament\Resources\TravelOrderResource\Pages;

use App\Filament\Resources\TravelOrderResource;
use App\Models\TravelOrder;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListTravelOrders extends ListRecords
{
    protected static string $resource = TravelOrderResource::class;

    // ── Tabs ──────────────────────────────────────────────────────────────────
    // WHY: Employees see "My Travel Orders" (their own) and "Tagged Travel Orders"
    // (batch orders they appear in as a traveler). Admins see one "All" tab.
    public function getTabs(): array
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';

        if ($isAdmin) {
            return [
                'all' => \Filament\Resources\Pages\ListRecords\Tab::make('All Travel Orders')
                    ->icon('heroicon-o-archive-box')
                    ->badge(TravelOrder::count()),
            ];
        }

        return [
            'mine' => \Filament\Resources\Pages\ListRecords\Tab::make('My Travel Orders')
                ->icon('heroicon-o-user-circle')
                ->badge(TravelOrder::where('created_by', $user->id)->count())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('created_by', $user->id)),

            'tagged' => \Filament\Resources\Pages\ListRecords\Tab::make('Tagged Travel Orders')
                ->icon('heroicon-o-tag')
                ->badge(
                    // WHY: employee_ids is a JSON array of user IDs (per schema).
                    // We find batch orders that contain this employee's ID,
                    // excluding orders they created themselves (those appear in "My" tab).
                    TravelOrder::where('travel_type', 'batch')
                        ->whereJsonContains('employee_ids', $user->id)
                        ->where('created_by', '!=', $user->id)
                        ->count()
                )
                ->modifyQueryUsing(
                    fn(Builder $query) => $query
                        ->where('travel_type', 'batch')
                        ->whereJsonContains('employee_ids', $user->id)
                        ->where('created_by', '!=', $user->id)
                ),
        ];
    }

    // ── Header Actions ────────────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Employees get "New travel order".
        // canCreate() on the Resource guards this, but we role-check here too.
        if (Auth::user()->role === 'employee') {
            $actions[] = Actions\CreateAction::make()
                ->label('New travel order')
                ->icon('heroicon-o-plus')
                ->color('primary');
        }

        // Admins get the Generate Report modal action.
        // Must be Filament\Actions\Action (page-level) — NOT a table action.
        if (Auth::user()->role === 'admin') {
            $actions[] = Actions\Action::make('generateReport')
                ->label('Generate Report')
                ->icon('heroicon-o-document-chart-bar')
                ->color('info')
                ->modalHeading('Generate Travel Order Report')
                ->modalDescription('Create a detailed PDF report of travel orders within a specific period.')
                ->modalWidth('2xl')
                ->modalSubmitActionLabel('Generate PDF')
                ->form([
                    Grid::make(2)->schema([
                        Select::make('status')
                            ->label('Order Status')
                            ->options([
                                'all' => 'All',
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
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
                    $url = route('travel-order.report', [
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
