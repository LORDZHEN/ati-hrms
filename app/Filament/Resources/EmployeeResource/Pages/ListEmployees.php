<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return auth()->user()?->role === 'admin'
            ? [
                Actions\Action::make('generateReport')
                    ->label('Generate Report')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->modalHeading('Generate Employee Report')
                    ->modalSubmitActionLabel('Generate')
                    ->form([
                        Select::make('status')
                            ->label('Account Status')
                            ->options([
                                '' => 'All',
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
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

                        DatePicker::make('from')
                            ->label('From')
                            ->required(),

                        DatePicker::make('to')
                            ->label('To')
                            ->required()
                            ->after('from'),
                    ])
                    ->action(function (array $data) {
                        return redirect()->route('employee.report', [
                            'status' => $data['status'],
                            'period' => $data['period'],
                            'from' => $data['from'],
                            'to' => $data['to'],
                        ]);
                    })
                    ->openUrlInNewTab(),
            ]
            : [];
    }
}
