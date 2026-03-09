<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
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
                    $pending = User::whereIn('role', [User::ROLE_REGULAR, User::ROLE_JOB_ORDER])
                        ->where('status', 'pending')
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
                ->modalDescription('Create a detailed PDF report of employee data within a specific period.')
                ->modalWidth('2xl')
                ->form([
                    Grid::make(2)->schema([
                        Select::make('status')
                            ->label('Employee Status')
                            ->options([
                                'all' => 'All Employees',
                                'active' => 'Active',
                                'pending' => 'Pending',
                                'inactive' => 'Inactive',
                            ])
                            ->default('all')->required()->native(false),

                        Select::make('period')
                            ->label('Report Period')
                            ->options([
                                'weekly' => 'This Week',
                                'monthly' => 'This Month',
                                'quarterly' => 'This Quarter',
                                'yearly' => 'This Year',
                                'custom' => 'Custom Date Range',
                            ])
                            ->default('monthly')->required()->native(false)->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $now = Carbon::now();
                                match ($state) {
                                    'weekly' => [$set('from', $now->copy()->startOfWeek()->toDateString()), $set('to', $now->copy()->endOfWeek()->toDateString())],
                                    'monthly' => [$set('from', $now->copy()->startOfMonth()->toDateString()), $set('to', $now->copy()->endOfMonth()->toDateString())],
                                    'quarterly' => [$set('from', $now->copy()->startOfQuarter()->toDateString()), $set('to', $now->copy()->endOfQuarter()->toDateString())],
                                    'yearly' => [$set('from', $now->copy()->startOfYear()->toDateString()), $set('to', $now->copy()->endOfYear()->toDateString())],
                                    default => null,
                                };
                            }),
                    ]),
                    Grid::make(2)->schema([
                        DatePicker::make('from')->label('From Date')->required()->native(false)
                            ->default(Carbon::now()->startOfMonth()->toDateString()),
                        DatePicker::make('to')->label('To Date')->required()->native(false)
                            ->after('from')->default(Carbon::now()->endOfMonth()->toDateString()),
                    ]),
                ])
                ->action(function (array $data) {
                    $url = route('employee.report', [
                        'status' => $data['status'] ?? 'all',
                        'period' => $data['period'] ?? 'monthly',
                        'from' => $data['from'],
                        'to' => $data['to'],
                    ]);
                    $this->redirect($url, navigate: false);
                })
                ->modalSubmitActionLabel('Generate PDF')
                ->visible(fn() => auth()->user()?->isAdmin() ?? false),
        ];
    }

    public function getTabs(): array
    {
        $base = fn() => User::whereIn('role', [User::ROLE_REGULAR, User::ROLE_JOB_ORDER]);

        return [
            'all' => Tab::make('All Employees')
                ->icon('heroicon-o-user-group')
                ->badge(fn() => $base()->count()),

            'pending' => Tab::make('Pending Approval')
                ->icon('heroicon-o-clock')
                ->badge(fn() => $base()->where('status', 'pending')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending')),

            'active' => Tab::make('Active')
                ->icon('heroicon-o-check-circle')
                ->badge(fn() => $base()->where('status', 'active')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'active')),

            'inactive' => Tab::make('Inactive')
                ->icon('heroicon-o-x-circle')
                ->badge(fn() => $base()->where('status', 'inactive')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'inactive')),

            'unverified' => Tab::make('Unverified Email')
                ->icon('heroicon-o-envelope')
                ->badge(fn() => $base()->whereNull('email_verified_at')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNull('email_verified_at')),

            'job_order' => Tab::make('Job Order')
                ->icon('heroicon-o-briefcase')
                ->badge(fn() => User::where('role', User::ROLE_JOB_ORDER)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('role', User::ROLE_JOB_ORDER)),

            'recent' => Tab::make('Recently Added')
                ->icon('heroicon-o-sparkles')
                ->badge(fn() => $base()->where('created_at', '>=', now()->subDays(7))->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('created_at', '>=', now()->subDays(7))),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
