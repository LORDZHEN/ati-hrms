<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('bulk_approve')
                ->label('Bulk Approve Pending')
                ->icon('heroicon-o-user-group')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Bulk Approve Pending Employees')
                ->modalDescription('This will approve all pending employees and send them their login credentials.')
                ->action(function () {
                    $pending = \App\Models\User::where('status', 'pending')
                        ->whereNull('email_verified_at')
                        ->get();

                    $count = 0;
                    $service = app(\App\Services\EmployeeRegistrationService::class);

                    foreach ($pending as $employee) {
                        if ($service->approveEmployee($employee)) {
                            $count++;
                        }
                    }

                    Notification::make()
                        ->title('Bulk Approval Complete')
                        ->body("{$count} employees approved successfully")
                        ->success()
                        ->send();
                })
                ->visible(fn() => auth()->user()?->isAdmin() ?? false),

            Actions\Action::make('generate_report')
                ->label('Generate Report')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->modalHeading('Generate Employee Report')
                ->modalDescription('Create a detailed report of employee data within a specific period')
                ->modalWidth('2xl')
                ->form([
                    Grid::make(2)->schema([
                        Select::make('report_type')
                            ->label('Report Type')
                            ->options([
                                'summary' => 'Summary Report',
                                'detailed' => 'Detailed Report',
                                'demographics' => 'Demographics Report',
                                'status' => 'Status Report',
                            ])
                            ->default('summary')
                            ->required()
                            ->native(false),

                        Select::make('status')
                            ->label('Employee Status')
                            ->options([
                                'all' => 'All Employees',
                                'active' => 'Active',
                                'pending' => 'Pending',
                                'inactive' => 'Inactive',
                            ])
                            ->default('all')
                            ->native(false),
                    ]),

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
                                    $set('from', $now->startOfWeek()->toDateString()),
                                    $set('to', $now->endOfWeek()->toDateString()),
                                ],
                                'monthly' => [
                                    $set('from', $now->startOfMonth()->toDateString()),
                                    $set('to', $now->endOfMonth()->toDateString()),
                                ],
                                'quarterly' => [
                                    $set('from', $now->startOfQuarter()->toDateString()),
                                    $set('to', $now->endOfQuarter()->toDateString()),
                                ],
                                'yearly' => [
                                    $set('from', $now->startOfYear()->toDateString()),
                                    $set('to', $now->endOfYear()->toDateString()),
                                ],
                                default => null,
                            };
                        }),

                    Grid::make(2)->schema([
                        DatePicker::make('from')
                            ->label('From Date')
                            ->required()
                            ->native(false)
                            ->visible(fn(callable $get) => $get('period') === 'custom'),

                        DatePicker::make('to')
                            ->label('To Date')
                            ->required()
                            ->native(false)
                            ->after('from')
                            ->visible(fn(callable $get) => $get('period') === 'custom'),
                    ]),

                    Select::make('format')
                        ->label('Output Format')
                        ->options([
                            'pdf' => 'PDF Document',
                            'excel' => 'Excel Spreadsheet',
                            'csv' => 'CSV File',
                        ])
                        ->default('pdf')
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data) {
                    // Implement report generation logic
                    Notification::make()
                        ->title('Report Generated')
                        ->body('Your report is being prepared and will be downloaded shortly.')
                        ->success()
                        ->send();

                    // For now, just redirect to a report route
                    // return redirect()->route('employee.report', $data);
                })
                ->visible(fn() => auth()->user()?->isAdmin() ?? false),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Employees')
                ->icon('heroicon-o-user-group')
                ->badge(fn() => \App\Models\User::whereIn('role', ['employee', 'admin'])->count()),

            'active' => Tab::make('Active')
                ->icon('heroicon-o-check-circle')
                ->badge(fn() => \App\Models\User::where('status', 'active')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'active')),

            'pending' => Tab::make('Pending Approval')
                ->icon('heroicon-o-clock')
                ->badge(fn() => \App\Models\User::where('status', 'pending')->whereNull('email_verified_at')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->where('status', 'pending')->whereNull('email_verified_at')
                ),

            'inactive' => Tab::make('Inactive')
                ->icon('heroicon-o-x-circle')
                ->badge(fn() => \App\Models\User::where('status', 'inactive')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'inactive')),

            'unverified' => Tab::make('Unverified Email')
                ->icon('heroicon-o-envelope')
                ->badge(fn() => \App\Models\User::whereNull('email_verified_at')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNull('email_verified_at')),

            'recent' => Tab::make('Recently Added')
                ->icon('heroicon-o-sparkles')
                ->badge(fn() => \App\Models\User::where('created_at', '>=', now()->subDays(7))->count())
                ->badgeColor('info')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->where('created_at', '>=', now()->subDays(7))
                ),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // You can add stat widgets here if needed
        ];
    }
}
