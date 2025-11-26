<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveApplicationResource\Pages;
use App\Models\LeaveApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeaveApplicationResource extends Resource
{
    protected static ?string $model = LeaveApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $slug = 'my-leave-applications';
    protected static ?string $navigationLabel = 'Leave Application';
    protected static ?string $title = 'Leave Applications';
    protected static ?string $navigationGroup = 'My Account';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
                        $employeeId = auth()->user()?->employee_id;
        return $form
            ->schema([
                // Section 1: Employee Information
Forms\Components\Section::make('Employee Information')
    ->description('Auto-filled personal details')
    ->icon('heroicon-o-user')
    ->schema([
        Forms\Components\Grid::make(2)->schema([
            Forms\Components\TextInput::make('office_department')
                ->label('Office/Department')
                ->default(fn() => auth()->user()?->department)
                ->disabled()
                ->dehydrated(false)
                ->prefixIcon('heroicon-o-building-office'),

            Forms\Components\TextInput::make('position')
                ->label('Position')
                ->default(fn() => auth()->user()?->position)
                ->disabled()
                ->dehydrated(false)
                ->prefixIcon('heroicon-o-briefcase'),
        ]),

        Forms\Components\DatePicker::make('date_of_filing')
            ->label('Date of Filing')
            ->default(now())
            ->required()
            ->prefixIcon('heroicon-o-calendar'),

        Forms\Components\TextInput::make('first_name')
            ->label('First Name')
            ->default(function () {
                $latest = \App\Models\LeaveApplication::where('employee_id', auth()->id())
                    ->latest('created_at')
                    ->first();
                return $latest?->first_name ?? auth()->user()?->first_name;
            })
            ->disabled(fn() => \App\Models\LeaveApplication::where('employee_id', auth()->id())->exists())
            ->dehydrated()
            ->required(),

        Forms\Components\TextInput::make('middle_name')
            ->label('Middle Name')
            ->default(function () {
                $latest = \App\Models\LeaveApplication::where('employee_id', auth()->id())
                    ->latest('created_at')
                    ->first();
                return $latest?->middle_name ?? auth()->user()?->middle_name;
            })
            ->disabled(fn() => \App\Models\LeaveApplication::where('employee_id', auth()->id())->exists())
            ->dehydrated()
            ->required(),

        Forms\Components\TextInput::make('last_name')
            ->label('Last Name')
            ->default(function () {
                $latest = \App\Models\LeaveApplication::where('employee_id', auth()->id())
                    ->latest('created_at')
                    ->first();
                return $latest?->last_name ?? auth()->user()?->last_name;
            })
            ->disabled(fn() => \App\Models\LeaveApplication::where('employee_id', auth()->id())->exists())
            ->dehydrated()
            ->required(),
    ])
    ->collapsible()
    ->collapsed(false),

                // Section 2: Leave Type Selection
                Forms\Components\Section::make('Leave Type Selection')
                    ->description('Choose the type of leave you want to apply for')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        Forms\Components\Select::make('type_of_leave')
                            ->label('Type of Leave')
                            ->options([
                                'vacation_leave' => 'Vacation Leave (Sec 51, Rule XVI, Omnibus Rules Implementing E.O. No.292)',
                                'mandatory_forced_leave' => 'Mandatory/Forced Leave (Sec. 25, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                                'sick_leave' => 'Sick Leave (Sec. 43, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                                'maternity_leave' => 'Maternity Leave (R.A. No. 11210/IRR issued by CSC, DOLE and SSS)',
                                'paternity_leave' => 'Paternity Leave (R.A. No. 8187/CSC MC No. 71.s 1998, as amended)',
                                'special_privilege_leave' => 'Special Privilege Leave (Sec. 21, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                                'solo_parent_leave' => 'Solo Parent Leave (R.A. No. 8972/CSC MC No. 8.s 2004)',
                                'study_leave' => 'Study Leave (Sec. 68, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                                '10_day_vawc_leave' => '10-Day VAWC Leave (R.A. 9262/CSC MC No. 15.s 2005)',
                                'rehabilitation_privilege' => 'Rehabilitation Privilege (Sec. 55, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                                'special_leave_benefits_for_women' => 'Special Leave Benefits for Women (R.A. 9710/CSC MC No. 25.s 2010)',
                                'special_emergency_leave' => 'Special Emergency (Calamity) Leave (CSC MC No. 2.s 2012, as amended)',
                                'adoption_leave' => 'Adoption Leave (R.A. No. 8552)',
                                'others' => 'Others (Specify)',
                            ])
                            ->required()
                            ->reactive()
                            ->prefixIcon('heroicon-o-document-text'),
                        Forms\Components\TextInput::make('other_leave_type')
                            ->label('Specify Other Leave Type')
                            ->visible(fn(Forms\Get $get) => $get('type_of_leave') === 'others')
                            ->required(fn(Forms\Get $get) => $get('type_of_leave') === 'others')
                            ->prefixIcon('heroicon-o-pencil'),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                // Section 3: Leave Details & Duration
                Forms\Components\Section::make('Leave Details & Duration')
                    ->description('Specify the duration and specific details of your leave')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                // Left Column - Duration
                                Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('number_of_working_days')
                                        ->label('Number of Working Days')
                                        ->numeric()
                                        ->required()
                                        ->minValue(0.5)
                                        ->step(0.5)
                                        ->suffix('days')
                                        ->prefixIcon('heroicon-o-clock'),
                                    Forms\Components\FileUpload::make('supporting_document')
                                        ->label('Supporting Document (Required if Sick Leave > 3 Days)')
                                        ->visible(
                                            fn(Forms\Get $get) =>
                                            $get('type_of_leave') === 'sick_leave' && ($get('number_of_working_days') ?? 0) > 3
                                        )
                                        ->required(
                                            fn(Forms\Get $get) =>
                                            $get('type_of_leave') === 'sick_leave' && ($get('number_of_working_days') ?? 0) > 3
                                        )
                                        ->acceptedFileTypes(['image/*', 'application/pdf'])
                                        ->directory('leave-documents')
                                        ->preserveFilenames()
                                        ->maxSize(5120) // max 5MB
                                        ->columnSpanFull(),
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\DatePicker::make('leave_date_from')
                                                ->label('Leave Date From')
                                                ->required()
                                                ->reactive()
                                                ->prefixIcon('heroicon-o-calendar')
                                                ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                                    if ($state && $get('leave_date_to')) {
                                                        $from = \Carbon\Carbon::parse($state);
                                                        $to = \Carbon\Carbon::parse($get('leave_date_to'));
                                                        $days = $from->diffInWeekdays($to) + 1;
                                                        $set('number_of_working_days', $days);
                                                    }
                                                })
                                                ->maxDate(
                                                    fn(Forms\Get $get) =>
                                                    $get('type_of_leave') === 'sick_leave'
                                                    ? now()->subDay()->toDateString()
                                                    : null
                                                ),

                                            Forms\Components\DatePicker::make('leave_date_to')
                                                ->label('Leave Date To')
                                                ->required()
                                                ->reactive()
                                                ->prefixIcon('heroicon-o-calendar')
                                                ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                                    if ($state && $get('leave_date_from')) {
                                                        $from = \Carbon\Carbon::parse($get('leave_date_from'));
                                                        $to = \Carbon\Carbon::parse($state);
                                                        $days = $from->diffInWeekdays($to) + 1;
                                                        $set('number_of_working_days', $days);
                                                    }
                                                })
                                                ->minDate(fn(Forms\Get $get) => $get('leave_date_from'))
                                                ->maxDate(
                                                    fn(Forms\Get $get) =>
                                                    $get('type_of_leave') === 'sick_leave'
                                                    ? now()->subDay()->toDateString()
                                                    : null
                                                ),
                                        ]),

                                    Forms\Components\Radio::make('commutation')
                                        ->label('Commutation')
                                        ->options([
                                            'not_requested' => 'Not Requested',
                                            'requested' => 'Requested',
                                        ])
                                        ->default('not_requested')
                                        ->required()
                                        ->inline(),
                                ]),

                                // Right Column - Specific Leave Details
                                Forms\Components\Group::make([
                                    // Vacation/Special Privilege Leave Details
                                    Forms\Components\Fieldset::make('Vacation/Special Privilege Leave Details')
                                        ->schema([
                                            Forms\Components\Radio::make('vacation_location')
                                                ->label('Location')
                                                ->options([
                                                    'within_philippines' => 'Within the Philippines',
                                                    'abroad' => 'Abroad',
                                                ])
                                                ->reactive()
                                                ->inline(),
                                            Forms\Components\TextInput::make('abroad_specify')
                                                ->label('Specify Location Abroad')
                                                ->visible(fn(Forms\Get $get) => $get('vacation_location') === 'abroad')
                                                ->required(fn(Forms\Get $get) => $get('vacation_location') === 'abroad')
                                                ->prefixIcon('heroicon-o-map-pin'),
                                        ])
                                        ->visible(fn(Forms\Get $get) => in_array($get('type_of_leave'), ['vacation_leave', 'special_privilege_leave'])),

                                    // Sick Leave Details
                                    Forms\Components\Fieldset::make('Sick Leave Details')
                                        ->schema([
                                            Forms\Components\Radio::make('sick_leave_location')
                                                ->label('Treatment Location')
                                                ->options([
                                                    'in_hospital' => 'In Hospital',
                                                    'out_patient' => 'Out Patient',
                                                ])
                                                ->reactive()
                                                ->inline(),
                                            Forms\Components\TextInput::make('hospital_illness_specify')
                                                ->label('Specify Illness (Hospital)')
                                                ->visible(fn(Forms\Get $get) => $get('sick_leave_location') === 'in_hospital')
                                                ->required(fn(Forms\Get $get) => $get('sick_leave_location') === 'in_hospital')
                                                ->prefixIcon('heroicon-o-heart'),
                                            Forms\Components\TextInput::make('outpatient_illness_specify')
                                                ->label('Specify Illness (Outpatient)')
                                                ->visible(fn(Forms\Get $get) => $get('sick_leave_location') === 'out_patient')
                                                ->required(fn(Forms\Get $get) => $get('sick_leave_location') === 'out_patient')
                                                ->prefixIcon('heroicon-o-heart'),
                                        ])
                                        ->visible(fn(Forms\Get $get) => $get('type_of_leave') === 'sick_leave'),

                                    // Special Leave Benefits for Women
                                    Forms\Components\Fieldset::make('Special Leave Benefits for Women')
                                        ->schema([
                                            Forms\Components\TextInput::make('women_illness_specify')
                                                ->label('Specify Illness')
                                                ->required()
                                                ->prefixIcon('heroicon-o-heart'),
                                        ])
                                        ->visible(fn(Forms\Get $get) => $get('type_of_leave') === 'special_leave_benefits_for_women'),

                                    // Study Leave Details
                                    Forms\Components\Fieldset::make('Study Leave Details')
                                        ->schema([
                                            Forms\Components\Radio::make('study_leave_purpose')
                                                ->label('Purpose')
                                                ->options([
                                                    'completion_of_masters_degree' => 'Completion of Master\'s Degree',
                                                    'bar_board_examination_review' => 'BAR/Board Examination Review',
                                                ])
                                                ->inline(),
                                        ])
                                        ->visible(fn(Forms\Get $get) => $get('type_of_leave') === 'study_leave'),

                                    // Other Purpose
                                    Forms\Components\Fieldset::make('Other Purpose')
                                        ->schema([
                                            Forms\Components\Radio::make('other_purpose')
                                                ->label('Purpose')
                                                ->options([
                                                    'monetization_of_leave_credits' => 'Monetization of Leave Credits',
                                                    'terminal_leave' => 'Terminal Leave',
                                                ])
                                                ->inline(),
                                        ])
                                        ->visible(fn(Forms\Get $get) => in_array($get('type_of_leave'), ['others'])),
                                ]),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                // Section 4: Approval & Processing
                Forms\Components\Section::make('Approval & Processing Status')
                    ->description('Information about the approval process and current status')
                    ->icon('heroicon-o-check-circle')
                    ->schema([
                        Forms\Components\Placeholder::make('recommendation_note')
                            ->label('Processing Information')
                            ->content('This section will be completed by the authorized officer during the approval process. You can view the status and any comments here once your application has been reviewed.')
                            ->extraAttributes(['class' => 'text-gray-600 bg-gray-50 p-4 rounded-lg border']),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('recommendation')
                                    ->label('Recommendation Status')
                                    ->options([
                                        'for_approval' => 'For Approval',
                                        'for_disapproval' => 'For Disapproval',
                                    ])
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->prefixIcon('heroicon-o-clipboard-document-check'),
                                Forms\Components\TextInput::make('authorized_officer_recommendation')
                                    ->label('Processed By')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->prefixIcon('heroicon-o-user-circle'),
                            ]),

                        Forms\Components\Textarea::make('disapproval_reason')
                            ->label('Comments/Reason for Disapproval')
                            ->disabled()
                            ->dehydrated(false)
                            ->rows(3)
                            ->placeholder('Any comments or reasons will appear here'),
                    ])
                    ->collapsible()
                    ->collapsed(true),

                // Hidden fields for system use
                Forms\Components\Hidden::make('employee_id')
                    ->default(auth()->id()),
                Forms\Components\Hidden::make('status')
                    ->default('pending'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Employee Name')
                    ->getStateUsing(fn($record) => trim($record->first_name . ' ' . ($record->middle_name ? $record->middle_name . ' ' : '') . $record->last_name))
                    ->searchable(['first_name', 'middle_name', 'last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('office_department')
                    ->label('Department')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('position')
                    ->label('Position')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('type_of_leave')
                    ->label('Leave Type')
                    ->formatStateUsing(fn(string $state): string => str_replace('_', ' ', ucwords($state, '_')))
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('leave_date_from')
                    ->label('From')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('leave_date_to')
                    ->label('To')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('number_of_working_days')
                    ->label('Days')
                    ->numeric()
                    ->suffix(' days')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'disapproved' => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_of_filing')
                    ->label('Filed Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('authorized_officer')
                    ->label('Processed By')
                    ->placeholder('Not yet processed')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('date_approved_disapproved')
                    ->label('Date Processed')
                    ->date()
                    ->placeholder('Not yet processed')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'disapproved' => 'Disapproved',
                    ]),
                Tables\Filters\SelectFilter::make('type_of_leave')
                    ->label('Leave Type')
                    ->options([
                        'vacation_leave' => 'Vacation Leave',
                        'sick_leave' => 'Sick Leave',
                        'maternity_leave' => 'Maternity Leave',
                        'paternity_leave' => 'Paternity Leave',
                        'special_privilege_leave' => 'Special Privilege Leave',
                    ]),
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('filed_from')
                            ->label('Filed From'),
                        Forms\Components\DatePicker::make('filed_until')
                            ->label('Filed Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['filed_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date_of_filing', '>=', $date),
                            )
                            ->when(
                                $data['filed_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date_of_filing', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => $record->status === 'pending'),
                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->visible(fn($record) => in_array($record->status, ['approved', 'disapproved']))
                    ->url(fn($record) => route('leave-application.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => $record->status === 'pending'),

                // <-- INSERT APPROVE/DISAPPROVE ACTIONS HERE
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn($record) => auth()->user()->role === 'admin' && $record->status === 'pending')
                    ->action(function (LeaveApplication $record) {
                        $record->update([
                            'status' => 'approved',
                            'authorized_officer' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                            'date_approved_disapproved' => now(),
                        ]);
                    }),

                Tables\Actions\Action::make('disapprove')
                    ->label('Disapprove')
                    ->icon('heroicon-o-x')
                    ->color('danger')
                    ->visible(fn($record) => auth()->user()->role === 'admin' && $record->status === 'pending')
                    ->action(function (LeaveApplication $record, array $data, Forms\Components\Form $form) {
                        $record->update([
                            'status' => 'disapproved',
                            'authorized_officer' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                            'disapproval_reason' => $data['disapproval_reason'] ?? null,
                            'date_approved_disapproved' => now(),
                        ]);
                    })
                    ->form([
                        Forms\Components\Textarea::make('disapproval_reason')
                            ->label('Reason for Disapproval')
                            ->required(),
                    ]),
            ])
            ->bulkActions([])
            ->defaultSort('date_of_filing', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->role === 'admin') {
                    return $query; // Admin sees all leave applications
                }
                return $query->where('employee_id', auth()->id()); // Employee sees only theirs
            });

    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveApplications::route('/'),
            'create' => Pages\CreateLeaveApplication::route('/create'),
            'edit' => Pages\EditLeaveApplication::route('/{record}/edit'),
            //'view' => Pages\ViewLeaveApplication::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('employee_id', auth()->id())
            ->orderBy('created_at', 'desc');
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        return $record->status === 'pending' && $record->employee_id === auth()->id();
    }

    public static function canDelete($record): bool
    {
        return $record->status === 'pending' && $record->employee_id === auth()->id();
    }
}
