<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalDataSheetResource\Pages;
use App\Models\PersonalDataSheet;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Collection;
use App\Notifications\PDSStatusUpdated;
use App\Notifications\PDSRemarksAdded;

use Filament\Forms\Components\{
    View as ViewComponent,
    Section,
    Grid,
    TextInput,
    DatePicker,
    Select,
    Radio,
    Checkbox,
    Repeater,
    Textarea
};

class PersonalDataSheetResource extends Resource
{
    protected static ?string $model = PersonalDataSheet::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Personal Data Sheet';
    protected static ?string $pluralModelLabel = 'Personal Data Sheet';
    protected static ?string $slug = 'pds';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'Documents';

    public static function form(Form $form): Form
    {
        $isLocked = fn($record) =>
            Auth::user()->role === 'employee' &&
            $record?->status === 'approved';

        return $form->schema([

            // Admin Remarks (if any)
            Textarea::make('remarks')
                ->label('Remarks from Admin')
                ->rows(4)
                ->columnSpanFull()
                ->disabled()
                ->hidden(fn($record) => blank($record?->remarks)),

            // ============================================================
            // CUSTOM PDS LAYOUT VIEW - THE ONLY VISIBLE FORM
            // ============================================================
            ViewComponent::make('filament.resources.personal-data-sheet.pds-form')
                ->columnSpanFull(),

            // ============================================================
            // ALL FORM FIELDS - HIDDEN (for validation and data binding)
            // ============================================================

            Section::make('Form Fields (Do Not Edit Directly - Use Form Above)')
                ->description('⚠️ These fields are automatically filled when you use the CSC format form above.')
                ->schema([
                    // SECTION 1: PERSONAL INFORMATION
                    TextInput::make('surname')->required()->disabled($isLocked),
                    TextInput::make('first_name')->required()->disabled($isLocked),
                    TextInput::make('middle_name')->disabled($isLocked),
                    TextInput::make('name_extension')->disabled($isLocked),
                    DatePicker::make('date_of_birth')->required()->disabled($isLocked),
                    TextInput::make('place_of_birth')->disabled($isLocked),
                    Select::make('sex')->options(['Male' => 'Male', 'Female' => 'Female'])->disabled($isLocked),
                    Select::make('civil_status')->options([
                        'Single' => 'Single',
                        'Married' => 'Married',
                        'Widowed' => 'Widowed',
                        'Separated' => 'Separated',
                    ])->disabled($isLocked),
                    TextInput::make('height')->numeric()->step(0.01)->disabled($isLocked),
                    TextInput::make('weight')->numeric()->step(0.1)->disabled($isLocked),
                    TextInput::make('blood_type')->disabled($isLocked),
                    TextInput::make('gsis_id_no')->disabled($isLocked),
                    TextInput::make('pag_ibig_id_no')->disabled($isLocked),
                    TextInput::make('philhealth_no')->disabled($isLocked),
                    TextInput::make('sss_no')->disabled($isLocked),
                    TextInput::make('tin_no')->disabled($isLocked),
                    TextInput::make('agency_employee_no')->disabled($isLocked),
                    Checkbox::make('filipino')->disabled($isLocked),
                    Checkbox::make('dual_citizenship')->reactive()->disabled($isLocked),
                    TextInput::make('dual_citizenship_country')
                        ->visible(fn($get) => $get('dual_citizenship'))
                        ->disabled($isLocked),

                    // SECTION 2: ADDRESSES & CONTACT
                    TextInput::make('res_house_block_lot_no')->disabled($isLocked),
                    TextInput::make('res_street')->disabled($isLocked),
                    TextInput::make('res_subdivision_village')->disabled($isLocked),
                    TextInput::make('res_barangay')->disabled($isLocked),
                    TextInput::make('res_city_municipality')->disabled($isLocked),
                    TextInput::make('res_province')->disabled($isLocked),
                    TextInput::make('res_zip_code')->disabled($isLocked),

                    Checkbox::make('same_as_residential')
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            if ($state) {
                                $set('perm_house_block_lot_no', $get('res_house_block_lot_no'));
                                $set('perm_street', $get('res_street'));
                                $set('perm_subdivision_village', $get('res_subdivision_village'));
                                $set('perm_barangay', $get('res_barangay'));
                                $set('perm_city_municipality', $get('res_city_municipality'));
                                $set('perm_province', $get('res_province'));
                                $set('perm_zip_code', $get('res_zip_code'));
                            }
                        })
                        ->disabled($isLocked),

                    TextInput::make('perm_house_block_lot_no')->disabled($isLocked),
                    TextInput::make('perm_street')->disabled($isLocked),
                    TextInput::make('perm_subdivision_village')->disabled($isLocked),
                    TextInput::make('perm_barangay')->disabled($isLocked),
                    TextInput::make('perm_city_municipality')->disabled($isLocked),
                    TextInput::make('perm_province')->disabled($isLocked),
                    TextInput::make('perm_zip_code')->disabled($isLocked),
                    TextInput::make('telephone_no')->disabled($isLocked),
                    TextInput::make('mobile')->disabled($isLocked),
                    TextInput::make('email')->email()->disabled($isLocked),

                    // SECTION 3: FAMILY BACKGROUND
                    TextInput::make('spouse_surname')->disabled($isLocked),
                    TextInput::make('spouse_first_name')->disabled($isLocked),
                    TextInput::make('spouse_middle_name')->disabled($isLocked),
                    TextInput::make('spouse_name_extension')->disabled($isLocked),
                    TextInput::make('spouse_occupation')->disabled($isLocked),
                    TextInput::make('spouse_employer_business_name')->disabled($isLocked),
                    TextInput::make('spouse_business_address')->disabled($isLocked),
                    TextInput::make('spouse_telephone_no')->disabled($isLocked),

                    TextInput::make('father_surname')->disabled($isLocked),
                    TextInput::make('father_first_name')->disabled($isLocked),
                    TextInput::make('father_middle_name')->disabled($isLocked),
                    TextInput::make('father_name_extension')->disabled($isLocked),

                    TextInput::make('mother_surname')->disabled($isLocked),
                    TextInput::make('mother_first_name')->disabled($isLocked),
                    TextInput::make('mother_middle_name')->disabled($isLocked),

                    Repeater::make('children')
                        ->schema([
                            TextInput::make('name')->required(),
                            DatePicker::make('birthdate')->required(),
                        ])
                        ->defaultItems(0)
                        ->disabled($isLocked),

                    // SECTION 4: EDUCATIONAL BACKGROUND
                    Repeater::make('education')
                        ->schema([
                            Select::make('level')
                                ->options([
                                    'ELEMENTARY' => 'Elementary',
                                    'SECONDARY' => 'Secondary',
                                    'VOCATIONAL/TRADE COURSE' => 'Vocational/Trade Course',
                                    'COLLEGE' => 'College',
                                    'GRADUATE STUDIES' => 'Graduate Studies',
                                ])
                                ->required(),
                            TextInput::make('school_name')->required(),
                            TextInput::make('degree')->required(),
                            TextInput::make('from_year')->maxLength(4),
                            TextInput::make('to_year')->maxLength(4),
                            TextInput::make('honors'),
                        ])
                        ->defaultItems(0)
                        ->disabled($isLocked),

                    // SECTION 5: CIVIL SERVICE ELIGIBILITY
                    Repeater::make('civil_service_eligibility')
                        ->schema([
                            TextInput::make('career_service'),
                            TextInput::make('rating')->numeric(),
                            DatePicker::make('exam_date'),
                            TextInput::make('place'),
                            TextInput::make('license_no'),
                            DatePicker::make('validity'),
                        ])
                        ->defaultItems(0)
                        ->disabled($isLocked),

                    // SECTION 6: WORK EXPERIENCE
                    Repeater::make('work_experience')
                        ->schema([
                            DatePicker::make('from')->required(),
                            DatePicker::make('to')->required(),
                            TextInput::make('position')->required(),
                            TextInput::make('agency')->required(),
                            TextInput::make('salary')->numeric(),
                            TextInput::make('salary_grade'),
                            TextInput::make('status'),
                            Radio::make('is_government')->boolean()->inline(),
                        ])
                        ->defaultItems(0)
                        ->disabled($isLocked),

                    // SECTION 7: VOLUNTARY WORK
                    Repeater::make('voluntary_work')
                        ->schema([
                            TextInput::make('organization_name'),
                            DatePicker::make('from_date'),
                            DatePicker::make('to_date'),
                            TextInput::make('hours')->numeric(),
                            TextInput::make('position'),
                        ])
                        ->defaultItems(0)
                        ->disabled($isLocked),

                    // SECTION 8: LEARNING & DEVELOPMENT
                    Repeater::make('learning_development')
                        ->schema([
                            TextInput::make('training_title'),
                            DatePicker::make('from_date'),
                            DatePicker::make('to_date'),
                            TextInput::make('hours')->numeric(),
                            TextInput::make('type'),
                            TextInput::make('conducted_by'),
                        ])
                        ->defaultItems(0)
                        ->disabled($isLocked),

                    // SECTION 9: OTHER INFORMATION
                    Repeater::make('special_skills')
                        ->simple(TextInput::make('skill'))
                        ->defaultItems(0)
                        ->disabled($isLocked),

                    Repeater::make('non_academic_distinctions')
                        ->simple(TextInput::make('distinction'))
                        ->defaultItems(0)
                        ->disabled($isLocked),

                    Repeater::make('membership_association')
                        ->simple(TextInput::make('organization'))
                        ->defaultItems(0)
                        ->disabled($isLocked),

                    // SECTION 10: QUESTIONS
                    Radio::make('related_third_degree')->boolean()->inline()->reactive()->disabled($isLocked),
                    Textarea::make('related_third_degree_details')
                        ->visible(fn($get) => $get('related_third_degree') === true)
                        ->disabled($isLocked),

                    Radio::make('related_fourth_degree')->boolean()->inline()->reactive()->disabled($isLocked),
                    Textarea::make('related_fourth_degree_details')
                        ->visible(fn($get) => $get('related_fourth_degree') === true)
                        ->disabled($isLocked),

                    Radio::make('has_admin_case')->boolean()->inline()->reactive()->disabled($isLocked),
                    Textarea::make('admin_case_details')
                        ->visible(fn($get) => $get('has_admin_case') === true)
                        ->disabled($isLocked),

                    Radio::make('has_criminal_case')->boolean()->inline()->reactive()->disabled($isLocked),
                    DatePicker::make('criminal_case_date_filed')
                        ->visible(fn($get) => $get('has_criminal_case') === true)
                        ->disabled($isLocked),
                    TextInput::make('criminal_case_status')
                        ->visible(fn($get) => $get('has_criminal_case') === true)
                        ->disabled($isLocked),

                    Radio::make('has_conviction')->boolean()->inline()->reactive()->disabled($isLocked),
                    Textarea::make('conviction_details')
                        ->visible(fn($get) => $get('has_conviction') === true)
                        ->disabled($isLocked),

                    Radio::make('has_been_separated')->boolean()->inline()->reactive()->disabled($isLocked),
                    Textarea::make('separation_details')
                        ->visible(fn($get) => $get('has_been_separated') === true)
                        ->disabled($isLocked),

                    Radio::make('has_election_candidacy')->boolean()->inline()->reactive()->disabled($isLocked),
                    Textarea::make('election_candidacy_details')
                        ->visible(fn($get) => $get('has_election_candidacy') === true)
                        ->disabled($isLocked),

                    Checkbox::make('is_indigenous')->reactive()->disabled($isLocked),
                    TextInput::make('indigenous_details')
                        ->visible(fn($get) => $get('is_indigenous'))
                        ->disabled($isLocked),

                    Checkbox::make('has_disability')->reactive()->disabled($isLocked),
                    TextInput::make('disability_details')
                        ->visible(fn($get) => $get('has_disability'))
                        ->disabled($isLocked),

                    Checkbox::make('is_solo_parent')->reactive()->disabled($isLocked),
                    TextInput::make('solo_parent_details')
                        ->visible(fn($get) => $get('is_solo_parent'))
                        ->disabled($isLocked),

                    // SECTION 11: REFERENCES
                    Repeater::make('references')
                        ->schema([
                            TextInput::make('name')->required(),
                            TextInput::make('address')->required(),
                            TextInput::make('tel'),
                        ])
                        ->defaultItems(3)
                        ->minItems(3)
                        ->maxItems(3)
                        ->addable(false)
                        ->deletable(false)
                        ->disabled($isLocked),

                    // SECTION 12: GOVERNMENT ID & DECLARATION
                    TextInput::make('gov_id_type')->disabled($isLocked),
                    TextInput::make('gov_id_no')->disabled($isLocked),
                    TextInput::make('gov_id_issued')->disabled($isLocked),
                    DatePicker::make('date_accomplished')->default(now())->required()->disabled($isLocked),
                ])
                ->collapsed()
                ->collapsible()
                ->columnSpanFull(),

        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        $isAdmin = Auth::user()->role === 'admin';

        return $table
            ->columns(self::getCardLayoutColumns())
            ->filters(self::getEnhancedFilters(), layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(2)
            ->filtersFormWidth('2xl')
            ->actions(self::getContextualActions($isAdmin))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Bulk Approve (Admin only)
                    Tables\Actions\BulkAction::make('bulkApprove')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn() => $isAdmin)
                        ->requiresConfirmation()
                        ->modalHeading('Approve Multiple PDS')
                        ->modalDescription('Are you sure you want to approve all selected Personal Data Sheets?')
                        ->action(function (Collection $records) {
                            $count = 0;

                            foreach ($records as $record) {
                                if ($record->status !== 'approved') {
                                    $record->update(['status' => 'approved']);
                                    $record->user->notify(new PDSStatusUpdated($record));
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->success()
                                ->title('Bulk Approval Complete')
                                ->body("{$count} PDS record(s) have been approved.")
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // Bulk Disapprove (Admin only)
                    Tables\Actions\BulkAction::make('bulkDisapprove')
                        ->label('Disapprove Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn() => $isAdmin)
                        ->form([
                            Textarea::make('remarks')
                                ->label('Reason for Bulk Disapproval')
                                ->required()
                                ->rows(4)
                                ->placeholder('This reason will be applied to all selected records...'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $count = 0;

                            foreach ($records as $record) {
                                if ($record->status !== 'disapproved') {
                                    $record->update([
                                        'status' => 'disapproved',
                                        'remarks' => $data['remarks'],
                                    ]);
                                    $record->user->notify(new PDSStatusUpdated($record));
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->danger()
                                ->title('Bulk Disapproval Complete')
                                ->body("{$count} PDS record(s) have been disapproved.")
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // Delete Action
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => $isAdmin)
                        ->requiresConfirmation()
                        ->modalHeading('Delete Selected PDS')
                        ->modalDescription('Are you sure you want to delete these records? This action cannot be undone.'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->striped()
            ->emptyStateHeading('No Personal Data Sheets yet')
            ->emptyStateDescription('Once employees submit their PDS, they will appear here.')
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create PDS')
                    ->icon('heroicon-o-plus')
                    ->visible(fn() => Auth::user()->role === 'employee'),
            ])
            ->poll('30s');
    }

    /**
     * Card-Style Layout Columns (Similar to SALN)
     */
    protected static function getCardLayoutColumns(): array
    {
        return [
            // Header: Employee Name & Info
            Tables\Columns\Layout\Stack::make([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Employee')
                    ->getStateUsing(fn($record) => self::getFullName($record))
                    ->searchable(['surname', 'first_name', 'middle_name'])
                    ->weight(FontWeight::Bold)
                    ->size(Tables\Columns\TextColumn\TextColumnSize::Large)
                    ->icon('heroicon-o-user-circle')
                    ->iconColor('primary'),

                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('place_of_birth')
                        ->label('Place of Birth')
                        ->icon('heroicon-o-map-pin')
                        ->iconColor('gray')
                        ->size(Tables\Columns\TextColumn\TextColumnSize::Small)
                        ->color('gray')
                        ->limit(30)
                        ->default('Not specified'),

                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Submitted')
                        ->dateTime('M d, Y')
                        ->icon('heroicon-o-calendar-days')
                        ->iconColor('gray')
                        ->size(Tables\Columns\TextColumn\TextColumnSize::Small)
                        ->color('gray'),
                ]),
            ])->space(1),

            // Personal Info Summary
            Tables\Columns\Layout\Split::make([
                // Left: Contact Info
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('email')
                        ->label('Email')
                        ->icon('heroicon-o-envelope')
                        ->iconColor('blue')
                        ->color('blue')
                        ->size(Tables\Columns\TextColumn\TextColumnSize::Small)
                        ->copyable()
                        ->copyMessage('Email copied!')
                        ->default('No email'),

                    Tables\Columns\TextColumn::make('mobile')
                        ->label('Mobile')
                        ->icon('heroicon-o-phone')
                        ->iconColor('green')
                        ->color('green')
                        ->size(Tables\Columns\TextColumn\TextColumnSize::Small)
                        ->copyable()
                        ->copyMessage('Mobile copied!')
                        ->default('No mobile'),
                ])->space(1),

                // Right: Status & Completion
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('status')
                        ->label('Status')
                        ->badge()
                        ->colors([
                            'warning' => 'submitted',
                            'success' => 'approved',
                            'danger' => 'disapproved',
                        ])
                        ->icons([
                            'heroicon-m-clock' => 'submitted',
                            'heroicon-m-check-circle' => 'approved',
                            'heroicon-m-x-circle' => 'disapproved',
                        ])
                        ->size(Tables\Columns\TextColumn\TextColumnSize::Medium)
                        ->weight(FontWeight::SemiBold),

                    Tables\Columns\TextColumn::make('completion_rate')
                        ->label('Completion')
                        ->getStateUsing(fn($record) => self::calculateCompletionRate($record))
                        ->formatStateUsing(fn($state) => $state . '%')
                        ->badge()
                        ->color(fn($state) => match (true) {
                            $state >= 90 => 'success',
                            $state >= 70 => 'warning',
                            default => 'danger',
                        })
                        ->icon(fn($state) => match (true) {
                            $state >= 90 => 'heroicon-o-check-circle',
                            $state >= 70 => 'heroicon-o-exclamation-circle',
                            default => 'heroicon-o-x-circle',
                        })
                        ->size(Tables\Columns\TextColumn\TextColumnSize::Small),
                ])->space(1)->alignment('end'),
            ])->from('md'),

            // Admin Remarks Panel
            Tables\Columns\Layout\Panel::make([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\IconColumn::make('has_remarks')
                        ->label('Remarks')
                        ->boolean()
                        ->getStateUsing(fn($record) => !blank($record->remarks))
                        ->trueIcon('heroicon-o-chat-bubble-left-ellipsis')
                        ->falseIcon('heroicon-o-check-circle')
                        ->trueColor('warning')
                        ->falseColor('success')
                        ->size(Tables\Columns\IconColumn\IconColumnSize::Large),

                    Tables\Columns\TextColumn::make('remarks')
                        ->label('Admin Remarks')
                        ->limit(100)
                        ->wrap()
                        ->default('No remarks - All good!')
                        ->color(fn($record) => blank($record->remarks) ? 'success' : 'warning')
                        ->icon(fn($record) => blank($record->remarks) ? 'heroicon-o-check' : 'heroicon-o-exclamation-triangle'),
                ]),
            ])
                ->collapsible()
                ->collapsed(fn($record) => blank($record->remarks)),
        ];
    }

    /**
     * Enhanced Filters
     */
    protected static function getEnhancedFilters(): array
    {
        $isAdmin = Auth::user()->role === 'admin';

        return [
            // Status Filter
            Tables\Filters\SelectFilter::make('status')
                ->options([
                    'submitted' => 'Submitted',
                    'approved' => 'Approved',
                    'disapproved' => 'Disapproved',
                ])
                ->label('Status')
                ->multiple()
                ->placeholder('All statuses')
                ->indicator('Status')
                ->native(false),

            // Date Range Filter
            Tables\Filters\Filter::make('date_range')
                ->form([
                    Grid::make(2)->schema([
                        DatePicker::make('submitted_from')
                            ->label('From Date')
                            ->placeholder('Select start date')
                            ->native(false),
                        DatePicker::make('submitted_until')
                            ->label('To Date')
                            ->placeholder('Select end date')
                            ->native(false),
                    ]),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['submitted_from'],
                            fn($q, $date) => $q->whereDate('created_at', '>=', $date)
                        )
                        ->when(
                            $data['submitted_until'],
                            fn($q, $date) => $q->whereDate('created_at', '<=', $date)
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
                    if ($data['submitted_from'] ?? null) {
                        $indicators[] = Tables\Filters\Indicator::make('From: ' .
                            \Carbon\Carbon::parse($data['submitted_from'])->format('M d, Y'))
                            ->removeField('submitted_from');
                    }
                    if ($data['submitted_until'] ?? null) {
                        $indicators[] = Tables\Filters\Indicator::make('To: ' .
                            \Carbon\Carbon::parse($data['submitted_until'])->format('M d, Y'))
                            ->removeField('submitted_until');
                    }
                    return $indicators;
                }),

            // Has Remarks Filter
            Tables\Filters\TernaryFilter::make('has_remarks')
                ->label('Admin Remarks')
                ->placeholder('All records')
                ->trueLabel('With remarks')
                ->falseLabel('Without remarks')
                ->queries(
                    true: fn(Builder $query) => $query->whereNotNull('remarks')
                        ->where('remarks', '!=', ''),
                    false: fn(Builder $query) => $query->where(fn($q) =>
                        $q->whereNull('remarks')->orWhere('remarks', '')),
                )
                ->visible($isAdmin)
                ->indicator('Remarks'),

            // Completion Rate Filter
            Tables\Filters\SelectFilter::make('completion_level')
                ->label('Completion Level')
                ->options([
                    'complete' => 'High (90%+)',
                    'moderate' => 'Moderate (70-89%)',
                    'incomplete' => 'Low (<70%)',
                ])
                ->placeholder('All levels')
                ->native(false)
                ->indicator('Completion'),
        ];
    }

    /**
     * Contextual Actions (Similar to SALN)
     */
    protected static function getContextualActions($isAdmin): array
    {
        return [
            Tables\Actions\ActionGroup::make([
                // Quick View Action
                Tables\Actions\Action::make('quickView')
                    ->label('Quick View')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->modalHeading(fn($record) => 'PDS: ' . self::getFullName($record))
                    ->modalContent(fn($record) => view('filament.resources.personal-data-sheet.quick-view', ['record' => $record]))
                    ->modalWidth('5xl')
                    ->modalFooterActions(fn() => [])
                    ->slideOver(),

                // Approve Action (Admin only)
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $isAdmin && $record->status !== 'approved')
                    ->requiresConfirmation()
                    ->modalHeading('Approve PDS')
                    ->modalDescription(fn($record) => 'Are you sure you want to approve the PDS for ' . self::getFullName($record) . '?')
                    ->action(function ($record) {
                        $record->update(['status' => 'approved']);

                        Notification::make()
                            ->success()
                            ->title('PDS Approved')
                            ->body('The Personal Data Sheet has been approved successfully.')
                            ->send();
                    }),

                // Disapprove Action (Admin only)
                Tables\Actions\Action::make('disapprove')
                    ->label('Disapprove')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => $isAdmin && $record->status !== 'disapproved')
                    ->form([
                        Textarea::make('remarks')
                            ->label('Reason for Disapproval')
                            ->required()
                            ->rows(4)
                            ->placeholder('Please provide a clear reason for disapproval...'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update(['remarks' => $data['remarks']]);

                        // Notify the employee
                        $record->user->notify(new PDSRemarksAdded($record));

                        // Flash for admin
                        Notification::make()
                            ->success()
                            ->title('Remarks Updated')
                            ->body('Admin remarks have been saved and the employee has been notified.')
                            ->send();
                    }),

                // Add/Edit Remarks Action (Admin only)
                Tables\Actions\Action::make('remarks')
                    ->label(fn($record) => blank($record->remarks) ? 'Add Remarks' : 'Edit Remarks')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('warning')
                    ->visible(fn() => $isAdmin)
                    ->fillForm(fn($record) => ['remarks' => $record->remarks])
                    ->form([
                        Textarea::make('remarks')
                            ->label('Admin Remarks')
                            ->rows(5)
                            ->required()
                            ->placeholder('Add notes or feedback for the employee...'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update(['remarks' => $data['remarks']]);

                        Notification::make()
                            ->success()
                            ->title('Remarks Updated')
                            ->body('Admin remarks have been saved.')
                            ->send();
                    }),

                // Reset to Submitted Action (Admin only)
                Tables\Actions\Action::make('resetToSubmitted')
                    ->label('Reset Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->visible(fn($record) => $isAdmin && $record->status !== 'submitted')
                    ->requiresConfirmation()
                    ->modalHeading('Reset to Submitted')
                    ->modalDescription('This will reset the status back to "Submitted" and clear any remarks.')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'submitted',
                            'remarks' => null,
                        ]);

                        // Notify the employee
                        $record->user->notify(new PDSStatusUpdated($record));

                        // Flash for admin
                        Notification::make()
                            ->info()
                            ->title('Status Reset')
                            ->body('PDS status has been reset to submitted.')
                            ->send();
                    }),

                // Print Action
                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'approved')
                    ->url(fn($record) => route('pds.print', $record))
                    ->openUrlInNewTab(),

                // Edit Action (Employees only for non-approved)
                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning')
                    ->visible(
                        fn($record) =>
                        Auth::user()->role === 'employee' &&
                        $record->status !== 'approved'
                    ),

                // Delete Action (Admin only)
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->visible(fn() => $isAdmin),
            ])
                ->label('Actions')
                ->icon('heroicon-o-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button(),
        ];
    }

    /**
     * Get formatted full name
     */
    protected static function getFullName($record): string
    {
        $parts = array_filter([
            $record->surname,
            $record->first_name,
            $record->middle_name,
            $record->name_extension,
        ]);

        return implode(' ', $parts);
    }

    /**
     * Calculate completion rate based on filled fields
     */
    protected static function calculateCompletionRate($record): int
    {
        $totalFields = 0;
        $filledFields = 0;

        // Basic info fields
        $basicFields = [
            'surname',
            'first_name',
            'date_of_birth',
            'place_of_birth',
            'sex',
            'civil_status',
            'height',
            'weight',
            'blood_type',
            'mobile',
            'email'
        ];

        foreach ($basicFields as $field) {
            $totalFields++;
            if (!blank($record->$field))
                $filledFields++;
        }

        // Address fields
        $addressFields = [
            'res_house_block_lot_no',
            'res_street',
            'res_barangay',
            'res_city_municipality',
            'res_province',
            'res_zip_code'
        ];

        foreach ($addressFields as $field) {
            $totalFields++;
            if (!blank($record->$field))
                $filledFields++;
        }

        // JSON fields (repeaters)
        $jsonFields = [
            'children',
            'education',
            'work_experience',
            'references'
        ];

        foreach ($jsonFields as $field) {
            $totalFields++;
            $value = $record->$field;
            $data = is_array($value) ? $value : (is_string($value) ? json_decode($value, true) : []);
            if (is_array($data) && count($data) > 0)
                $filledFields++;
        }

        // Government IDs
        if (!blank($record->gov_id_type) && !blank($record->gov_id_no)) {
            $filledFields++;
        }
        $totalFields++;

        return $totalFields > 0 ? round(($filledFields / $totalFields) * 100) : 0;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::user()->role === 'employee') {
            $query->where('user_id', Auth::id());
        }

        return $query;
    }

    public static function getNavigationBadge(): ?string
    {
        if (auth()->user()?->role !== 'admin') {
            return null;
        }

        $count = PersonalDataSheet::where('status', 'submitted')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        if (auth()->user()?->role !== 'admin') {
            return null;
        }

        $count = PersonalDataSheet::where('status', 'submitted')->count();

        return $count > 0 ? 'warning' : 'success';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonalDataSheets::route('/'),
            'create' => Pages\CreatePersonalDataSheet::route('/create'),
            'edit' => Pages\EditPersonalDataSheet::route('/{record}/edit'),
        ];
    }
}
