<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveApplicationResource\Pages;
use App\Models\LeaveApplication;
use Filament\Forms;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeaveApplicationResource extends Resource
{
    protected static ?string $model = LeaveApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $slug = 'leave-applications';
    protected static ?string $navigationLabel = 'Leave Application';
    protected static ?string $title = 'Leave Applications';
    protected static ?string $modelLabel = 'Leave Application';
    protected static ?string $pluralModelLabel = 'Leave Application';
    protected static ?string $navigationGroup = 'Manage';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Employee Info
                Forms\Components\Section::make('Employee Information')
                    ->description('Auto-filled personal details')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('first_name')
                                    ->label('First Name')
                                    ->disabled()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        $component->state($record->employee?->first_name ?? auth()->user()->first_name);
                                    })
                                    ->required(),

                                Forms\Components\TextInput::make('middle_name')
                                    ->label('Middle Name')
                                    ->disabled()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        $component->state($record->employee?->middle_name ?? auth()->user()->middle_name);
                                    }),

                                Forms\Components\TextInput::make('last_name')
                                    ->label('Last Name')
                                    ->disabled()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        $component->state($record->employee?->last_name ?? auth()->user()->last_name);
                                    })
                                    ->required(),

                                Forms\Components\TextInput::make('office_department')
                                    ->label('Office/Department')
                                    ->disabled()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        $component->state($record->employee?->department ?? auth()->user()->department);
                                    }),

                                Forms\Components\TextInput::make('position')
                                    ->label('Position')
                                    ->disabled()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        $component->state($record->employee?->position ?? auth()->user()->position);
                                    }),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                // Leave Type
                Forms\Components\Section::make('Leave Type Selection')
                    ->description('Choose the type of leave you want to apply for')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        Forms\Components\Select::make('type_of_leave')
                            ->label('Type of Leave')
                            ->options([
                                'vacation_leave' => 'Vacation Leave',
                                'mandatory_forced_leave' => 'Mandatory/Forced Leave',
                                'sick_leave' => 'Sick Leave',
                                'maternity_leave' => 'Maternity Leave',
                                'paternity_leave' => 'Paternity Leave',
                                'special_privilege_leave' => 'Special Privilege Leave',
                                'solo_parent_leave' => 'Solo Parent Leave',
                                'study_leave' => 'Study Leave',
                                '10_day_vawc_leave' => '10-Day VAWC Leave',
                                'rehabilitation_privilege' => 'Rehabilitation Privilege',
                                'special_leave_benefits_for_women' => 'Special Leave Benefits for Women',
                                'special_emergency_leave' => 'Special Emergency Leave',
                                'adoption_leave' => 'Adoption Leave',
                                'others' => 'Others',
                            ])
                            ->required()
                            ->reactive(),

                        Forms\Components\TextInput::make('other_leave_type')
                            ->label('Specify Other Leave Type')
                            ->visible(fn(Forms\Get $get) => $get('type_of_leave') === 'others')
                            ->required(fn(Forms\Get $get) => $get('type_of_leave') === 'others'),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                // Leave Details & Duration
                Forms\Components\Section::make('Leave Details & Duration')
                    ->description('Specify the duration and specific details of your leave')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            // Left Column
                            Forms\Components\Group::make([
                                Forms\Components\TextInput::make('number_of_working_days')
                                    ->label('Number of Working Days')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0.5)
                                    ->step(0.5)
                                    ->suffix('days'),

                                Forms\Components\FileUpload::make('supporting_document')
                                    ->label('Supporting Document (Required if Sick Leave > 3 Days)')
                                    ->visible(fn(Forms\Get $get) => $get('type_of_leave') === 'sick_leave' && ($get('number_of_working_days') ?? 0) > 3)
                                    ->required(fn(Forms\Get $get) => $get('type_of_leave') === 'sick_leave' && ($get('number_of_working_days') ?? 0) > 3)
                                    ->acceptedFileTypes(['image/*', 'application/pdf'])
                                    ->directory('leave-documents')
                                    ->preserveFilenames()
                                    ->maxSize(5120),

                                Forms\Components\DatePicker::make('leave_date_from')
                                    ->label('Leave Date From')
                                    ->required()
                                    ->reactive()
                                    ->minDate(function (Forms\Get $get) {
                                        if ($get('type_of_leave') === 'vacation_leave') {
                                            return now()->addDays(5)->startOfDay();
                                        }
                                        return now();
                                    })
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        if ($state && $get('leave_date_to')) {
                                            $from = \Carbon\Carbon::parse($state);
                                            $to = \Carbon\Carbon::parse($get('leave_date_to'));
                                            $set('number_of_working_days', $from->diffInWeekdays($to) + 1);
                                        }
                                    }),

                                Forms\Components\DatePicker::make('leave_date_to')
                                    ->label('Leave Date To')
                                    ->required()
                                    ->reactive()
                                    ->minDate(fn(Forms\Get $get) => $get('leave_date_from'))
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        if ($state && $get('leave_date_from')) {
                                            $from = \Carbon\Carbon::parse($get('leave_date_from'));
                                            $to = \Carbon\Carbon::parse($state);
                                            $set('number_of_working_days', $from->diffInWeekdays($to) + 1);
                                        }
                                    }),

                                Forms\Components\TextInput::make('inclusive_dates')
                                    ->label('Inclusive Dates')
                                    ->disabled()
                                    ->reactive(),

                                Forms\Components\Radio::make('commutation')
                                    ->label('Commutation')
                                    ->options([
                                        'not_requested' => 'Not Requested',
                                        'requested' => 'Requested',
                                    ])
                                    ->default('not_requested')
                                    ->inline(),
                            ]),

                            // Right Column - Specific Leave Details
                            Forms\Components\Group::make([
                                Forms\Components\Fieldset::make('Vacation/Special Privilege Leave Details')
                                    ->schema([
                                        Forms\Components\Radio::make('vacation_location')
                                            ->label('Location')
                                            ->options([
                                                'within_philippines' => 'Within the Philippines',
                                                'abroad' => 'Abroad',
                                            ])
                                            ->inline(),
                                        Forms\Components\TextInput::make('abroad_specify')
                                            ->label('Specify Abroad')
                                            ->visible(fn(Forms\Get $get) => $get('vacation_location') === 'abroad')
                                            ->required(fn(Forms\Get $get) => $get('vacation_location') === 'abroad'),
                                    ])
                                    ->visible(fn(Forms\Get $get) => in_array($get('type_of_leave'), ['vacation_leave', 'special_privilege_leave'])),

                                Forms\Components\Fieldset::make('Sick Leave Details')
                                    ->schema([
                                        Forms\Components\Radio::make('sick_leave_location')
                                            ->label('Treatment Location')
                                            ->options([
                                                'in_hospital' => 'In Hospital',
                                                'out_patient' => 'Out Patient',
                                            ])
                                            ->inline(),
                                        Forms\Components\TextInput::make('hospital_illness_specify')
                                            ->label('Specify Illness (Hospital)')
                                            ->visible(fn(Forms\Get $get) => $get('sick_leave_location') === 'in_hospital')
                                            ->required(fn(Forms\Get $get) => $get('sick_leave_location') === 'in_hospital'),
                                        Forms\Components\TextInput::make('outpatient_illness_specify')
                                            ->label('Specify Illness (Outpatient)')
                                            ->visible(fn(Forms\Get $get) => $get('sick_leave_location') === 'out_patient')
                                            ->required(fn(Forms\Get $get) => $get('sick_leave_location') === 'out_patient'),
                                    ])
                                    ->visible(fn(Forms\Get $get) => $get('type_of_leave') === 'sick_leave'),
                            ]),
                        ]),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                // Auto-fill Date of Filing
                Forms\Components\DatePicker::make('date_of_filing')
                    ->label('Date of Filing')
                    ->default(fn() => now())
                    ->disabled(),

                Forms\Components\Placeholder::make('previous_leave_action')
                    ->label('Previous Leave Action')
                    ->content(fn($get) => $get('previous_leave_action') ?? 'No previous leave action'),

                // Approval Section
                Forms\Components\Section::make('Approval & Processing Status')
                    ->description('Only admins can approve or disapprove')
                    ->icon('heroicon-o-check-circle')
                    ->schema([
                        Forms\Components\Placeholder::make('status_placeholder')
                            ->label('Processing Information')
                            ->content('Status will be updated by authorized officer.'),
                        Forms\Components\Hidden::make('employee_id')->default(auth()->id()),
                        Forms\Components\Hidden::make('status')->default('pending'),
                    ])
                    ->collapsible()
                    ->collapsed(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_name')
                    ->label('Employee Name')
                    ->getStateUsing(fn($record) => $record->employee?->name ?? 'N/A')
                    ->searchable(['employee.name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('type_of_leave')->label('Leave Type')->sortable(),
                Tables\Columns\TextColumn::make('number_of_working_days')->label('Days')->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'disapproved' => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_of_filing')->label('Filed Date')->date()->sortable(),
                Tables\Columns\TextColumn::make('authorized_officer')
                    ->label('Processed By')
                    ->formatStateUsing(fn($state) => $state ?: 'Not yet processed'),

                Tables\Columns\TextColumn::make('date_approved_disapproved')->label('Date Processed')->date()->placeholder('Not yet processed'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'disapproved' => 'Disapproved',
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make()
                    ->visible(
                        fn($record) =>
                        auth()->user()->role === 'employee'
                        && $record->status === 'pending'
                    ),

                Tables\Actions\DeleteAction::make()
                    ->visible(
                        fn($record) =>
                        auth()->user()->role === 'employee'
                        && $record->status === 'pending'
                    ),

                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn($record) => route('leave_application.print', $record))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->status === 'approved'),
            ])

            ->bulkActions([])
            ->defaultSort('date_of_filing', 'desc')
            ->modifyQueryUsing(fn(Builder $query) => auth()->user()->role === 'admin' ? $query : $query->where('employee_id', auth()->id()));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationBadge(): ?string
    {
        if (auth()->user()?->role !== 'admin') {
            return null;
        }

        $count = LeaveApplication::where('status', 'pending')->count();

        return $count > 0 ? (string) $count : null;
    }


    public static function getNavigationBadgeColor(): ?string
    {
        if (auth()->user()?->role !== 'admin') {
            return null;
        }

        $count = LeaveApplication::where('status', 'pending')->count();

        return $count > 0 ? 'warning' : 'success';
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveApplications::route('/'),
            'create' => Pages\CreateLeaveApplication::route('/create'),
            'edit' => Pages\EditLeaveApplication::route('/{record}/edit'),
            'view' => Pages\ViewLeaveApplication::route('/{record}'),
        ];
    }
}
