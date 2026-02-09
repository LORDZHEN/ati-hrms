<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalDataSheetResource\Pages;
use App\Models\PersonalDataSheet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

use Filament\Forms\Components\{
    Wizard,
    Wizard\Step,
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
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'Manage';

    /* -----------------------------------------------------------------
     | FORM
     |-----------------------------------------------------------------*/
    public static function form(Form $form): Form
    {
        $isLocked = fn($record) =>
            Auth::user()->role === 'employee' &&
            $record?->status === 'approved';

        return $form->schema([

            /* ADMIN REMARKS (READ ONLY) */
            Textarea::make('remarks')
                ->label('Remarks from Admin')
                ->rows(4)
                ->columnSpanFull()
                ->disabled()
                ->hidden(fn($record) => blank($record?->remarks)),

            Wizard::make([

                /* =========================================================
                | C1 – PERSONAL INFORMATION (Items 1–26)
                |=========================================================*/
                Step::make('C1. PERSONAL INFORMATION')
                    ->icon('heroicon-o-user')
                    ->schema([

                        /* -------------------------------------------------
                         | I. PERSONAL INFORMATION (1–26)
                         |-------------------------------------------------*/
                        Section::make('I. PERSONAL INFORMATION')
                            ->schema([

                                // 1–3
                                Grid::make(3)->schema([
                                    TextInput::make('surname')->label('1. SURNAME')->required()
                                        ->disabled($isLocked),
                                    TextInput::make('first_name')->label('2. FIRST NAME')->required()
                                        ->disabled($isLocked),
                                    TextInput::make('middle_name')->label('3. MIDDLE NAME')
                                        ->disabled($isLocked),
                                ]),

                                // 4–5
                                Grid::make(2)->schema([
                                    TextInput::make('name_extension')->label('4. NAME EXTENSION (JR., SR.)')
                                        ->disabled($isLocked),
                                    DatePicker::make('date_of_birth')->label('5. DATE OF BIRTH')->required()
                                        ->disabled($isLocked),
                                ]),

                                // 6–8
                                Grid::make(3)->schema([
                                    TextInput::make('place_of_birth')->label('6. PLACE OF BIRTH')
                                        ->disabled($isLocked),
                                    Radio::make('sex')->label('7. SEX')->options([
                                        'Male' => 'Male',
                                        'Female' => 'Female',
                                    ])->inline()
                                        ->disabled($isLocked),
                                    Radio::make('civil_status')->label('8. CIVIL STATUS')->options([
                                        'Single' => 'Single',
                                        'Married' => 'Married',
                                        'Widowed' => 'Widowed',
                                        'Separated' => 'Separated',
                                        'Others' => 'Others',
                                    ])
                                        ->disabled($isLocked),
                                ]),

                                // 9–10
                                Grid::make(2)->schema([
                                    TextInput::make('height')->label('9. HEIGHT (m)'),
                                    TextInput::make('weight')->label('10. WEIGHT (kg)'),
                                ]),

                                // 11–15
                                Grid::make(3)->schema([
                                    TextInput::make('blood_type')->label('11. BLOOD TYPE'),
                                    TextInput::make('gsis_id_no')->label('12. GSIS ID NO.'),
                                    TextInput::make('pag_ibig_id_no')->label('13. PAG-IBIG ID NO.'),
                                ]),
                                Grid::make(3)->schema([
                                    TextInput::make('philhealth_no')->label('14. PHILHEALTH NO.'),
                                    TextInput::make('sss_no')->label('15. SSS NO.'),
                                    TextInput::make('tin_no')->label('TIN'),
                                ]),

                                // 16–18 Citizenship
                                Section::make('16. CITIZENSHIP')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Checkbox::make('filipino')->label('Filipino'),
                                            Checkbox::make('dual_citizenship')->label('Dual Citizenship'),
                                        ]),
                                        Grid::make(2)->schema([
                                            Checkbox::make('by_birth')->label('By Birth'),
                                            Checkbox::make('by_naturalization')->label('By Naturalization'),
                                        ]),
                                        TextInput::make('citizenship_country')
                                            ->label('18. If holder of dual citizenship, indicate country'),
                                    ]),

                                // 19–22 Residential Address
                                Section::make('17. RESIDENTIAL ADDRESS')
                                    ->schema([
                                        TextInput::make('res_house_no')->label('House/Block/Lot No.'),
                                        TextInput::make('res_street')->label('Street'),
                                        TextInput::make('res_barangay')->label('Barangay'),
                                        Grid::make(2)->schema([
                                            TextInput::make('res_city')->label('City/Municipality'),
                                            TextInput::make('res_province')->label('Province'),
                                        ]),
                                        TextInput::make('res_zip')->label('ZIP CODE'),
                                    ]),

                                // 23–26 Permanent + Contact
                                Section::make('18. PERMANENT ADDRESS & CONTACT')
                                    ->schema([
                                        TextInput::make('perm_house_no')->label('House/Block/Lot No.'),
                                        TextInput::make('perm_street')->label('Street'),
                                        TextInput::make('perm_barangay')->label('Barangay'),
                                        Grid::make(2)->schema([
                                            TextInput::make('perm_city')->label('City/Municipality'),
                                            TextInput::make('perm_province')->label('Province'),
                                        ]),
                                        TextInput::make('perm_zip')->label('ZIP CODE'),
                                        Grid::make(2)->schema([
                                            TextInput::make('telephone_no')->label('19. TELEPHONE NO.'),
                                            TextInput::make('mobile_no')->label('20. MOBILE NO.'),
                                        ]),
                                        TextInput::make('email')->label('21. E-MAIL ADDRESS (if any)'),
                                    ]),
                            ]),

                        /* -------------------------------------------------
                         | II. FAMILY BACKGROUND
                         |-------------------------------------------------*/
                        Section::make('II. FAMILY BACKGROUND')
                            ->schema([
                                TextInput::make('spouse_surname')->label('21. SPOUSE SURNAME'),
                                TextInput::make('spouse_first_name')->label('FIRST NAME'),
                                TextInput::make('spouse_middle_name')->label('MIDDLE NAME'),
                                TextInput::make('spouse_occupation')->label('OCCUPATION'),
                                TextInput::make('spouse_employer')->label('EMPLOYER/BUSINESS NAME'),
                                TextInput::make('spouse_business_address')->label('BUSINESS ADDRESS'),
                                TextInput::make('spouse_telephone')->label('TELEPHONE NO.'),

                                Repeater::make('children')
                                    ->label('NAME OF CHILDREN')
                                    ->schema([
                                        TextInput::make('name')->label('NAME OF CHILD'),
                                        DatePicker::make('birthdate')->label('DATE OF BIRTH'),
                                    ])
                                    ->defaultItems(1)
                                    ->minItems(1)
                                    ->maxItems(12)
                                    ->addable(true)
                                    ->deletable(false),
                            ]),

                        /* -------------------------------------------------
                         | III. EDUCATIONAL BACKGROUND
                         |-------------------------------------------------*/
                        Section::make('III. EDUCATIONAL BACKGROUND')
                            ->schema([
                                Repeater::make('education')
                                    ->schema([
                                        Select::make('level')->label('26. LEVEL')->options([
                                            'Elementary' => 'Elementary',
                                            'Secondary' => 'Secondary',
                                            'Vocational' => 'Vocational / Trade Course',
                                            'College' => 'College',
                                            'Graduate Studies' => 'Graduate Studies',
                                        ]),
                                        TextInput::make('school_name')->label('NAME OF SCHOOL'),
                                        TextInput::make('degree')->label('BASIC EDUCATION / DEGREE'),
                                        Grid::make(2)->schema([
                                            TextInput::make('from')->label('FROM'),
                                            TextInput::make('to')->label('TO'),
                                        ]),
                                        TextInput::make('units_earned')->label('HIGHEST LEVEL / UNITS EARNED'),
                                        TextInput::make('year_graduated')->label('YEAR GRADUATED'),
                                        TextInput::make('honors')->label('SCHOLARSHIP / HONORS'),
                                    ])
                                    ->defaultItems(3)
                                    ->minItems(3)
                                    ->maxItems(10)
                                    ->addable(true)
                                    ->deletable(false),
                            ]),
                    ]),


                /* =========================================================
                 | C2 – CIVIL SERVICE ELIGIBILITY
                 |=========================================================*/
                Step::make('C2. CIVIL SERVICE ELIGIBILITY & WORK EXPERIENCE')
                    ->icon('heroicon-o-briefcase')
                    ->schema([

                        /* =================================================
                         | IV. CIVIL SERVICE ELIGIBILITY
                         |=================================================*/
                        Section::make('IV. CIVIL SERVICE ELIGIBILITY')
                            ->schema([
                                Repeater::make('civil_service_eligibility')
                                    ->schema([
                                        TextInput::make('career_service')
                                            ->label('CAREER SERVICE / RA 1080 (BOARD/BAR) UNDER SPECIAL LAWS / CES / CSEE')
                                            ->columnSpan(2),

                                        TextInput::make('rating')
                                            ->label('RATING'),

                                        DatePicker::make('exam_date')
                                            ->label('DATE OF EXAM / CONFERMENT'),

                                        TextInput::make('exam_place')
                                            ->label('PLACE OF EXAM / CONFERMENT')
                                            ->columnSpan(2),

                                        TextInput::make('license_no')
                                            ->label('LICENSE NUMBER'),

                                        DatePicker::make('validity_date')
                                            ->label('DATE OF VALIDITY'),
                                    ])
                                    ->columns(7)
                                    ->defaultItems(1)   // ✅ CSC fixed rows
                                    ->minItems(0)
                                    ->maxItems(7)
                                    ->addable(true)
                                    ->deletable(true)
                                    ->reorderable(false),
                            ]),

                        /* =================================================
                         | V. WORK EXPERIENCE
                         |=================================================*/
                        Section::make('V. WORK EXPERIENCE (Include private employment. Start from your most recent work)')
                            ->schema([
                                Repeater::make('work_experience')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            DatePicker::make('from_date')
                                                ->label('INCLUSIVE DATES (FROM)'),

                                            DatePicker::make('to_date')
                                                ->label('INCLUSIVE DATES (TO)'),
                                        ]),

                                        TextInput::make('position_title')
                                            ->label('POSITION TITLE')
                                            ->columnSpan(2),

                                        TextInput::make('department_agency')
                                            ->label('DEPARTMENT / AGENCY / OFFICE / COMPANY')
                                            ->columnSpan(2),

                                        TextInput::make('monthly_salary')
                                            ->label('MONTHLY SALARY')
                                            ->numeric(),

                                        TextInput::make('salary_grade_step')
                                            ->label('SALARY GRADE & STEP (if applicable)'),

                                        TextInput::make('status_of_appointment')
                                            ->label('STATUS OF APPOINTMENT'),

                                        Radio::make('government_service')
                                            ->label('GOV’T SERVICE (Y / N)')
                                            ->options([
                                                'Y' => 'Y',
                                                'N' => 'N',
                                            ])
                                            ->inline(),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(1)  // ✅ CSC fixed continuation rows
                                    ->minItems(0)
                                    ->maxItems(28)
                                    ->addable(true)
                                    ->deletable(true)
                                    ->reorderable(false),
                            ]),
                    ]),


                /* =========================================================
                | C3 – VOLUNTARY WORK, L&D, OTHER INFORMATION
                |=========================================================*/
                Step::make('C3. VOLUNTARY WORK, L&D & OTHER INFORMATION')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([

                        /* =================================================
                         | VI. VOLUNTARY WORK OR INVOLVEMENT
                         |=================================================*/
                        Section::make('VI. VOLUNTARY WORK OR INVOLVEMENT IN CIVIC / NON-GOVERNMENT / PEOPLE / VOLUNTARY ORGANIZATION/S')
                            ->schema([
                                Repeater::make('voluntary_work')
                                    ->schema([
                                        TextInput::make('organization_name')
                                            ->label('NAME & ADDRESS OF ORGANIZATION')
                                            ->columnSpan(2),

                                        Grid::make(2)->schema([
                                            DatePicker::make('from_date')
                                                ->label('INCLUSIVE DATES (FROM)'),

                                            DatePicker::make('to_date')
                                                ->label('INCLUSIVE DATES (TO)'),
                                        ]),

                                        TextInput::make('hours')
                                            ->label('NUMBER OF HOURS')
                                            ->numeric(),

                                        TextInput::make('position')
                                            ->label('POSITION / NATURE OF WORK'),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(1)   // ✅ CSC fixed rows
                                    ->minItems(0)
                                    ->maxItems(7)
                                    ->addable(true)
                                    ->deletable(true)
                                    ->reorderable(false),
                            ]),

                        /* =================================================
                         | VII. LEARNING AND DEVELOPMENT (L&D)
                         |=================================================*/
                        Section::make('VII. LEARNING AND DEVELOPMENT (L&D) INTERVENTIONS / TRAINING PROGRAMS ATTENDED')
                            ->schema([
                                Repeater::make('learning_development')
                                    ->schema([
                                        TextInput::make('training_title')
                                            ->label('TITLE OF LEARNING AND DEVELOPMENT INTERVENTIONS / TRAINING PROGRAMS')
                                            ->columnSpan(2),

                                        Grid::make(2)->schema([
                                            DatePicker::make('from_date')
                                                ->label('INCLUSIVE DATES (FROM)'),

                                            DatePicker::make('to_date')
                                                ->label('INCLUSIVE DATES (TO)'),
                                        ]),

                                        TextInput::make('hours')
                                            ->label('NUMBER OF HOURS')
                                            ->numeric(),

                                        TextInput::make('type')
                                            ->label('TYPE OF LD (Managerial / Supervisory / Technical / etc.)'),

                                        TextInput::make('conducted_by')
                                            ->label('CONDUCTED / SPONSORED BY')
                                            ->columnSpan(2),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(1)  // ✅ CSC continuation rows
                                    ->minItems(0)
                                    ->maxItems(21)
                                    ->addable(true)
                                    ->deletable(true)
                                    ->reorderable(false),
                            ]),

                        /* =================================================
                         | VIII. OTHER INFORMATION
                         |=================================================*/
                        Section::make('VIII. OTHER INFORMATION')
                            ->schema([
                                Grid::make(3)->schema([

                                    Repeater::make('special_skills')
                                        ->label('31. SPECIAL SKILLS AND HOBBIES')
                                        ->schema([
                                            TextInput::make('skill')->label(''),
                                        ])
                                        ->defaultItems(1)
                                        ->minItems(0)
                                        ->maxItems(7)
                                        ->addable(true)
                                        ->deletable(true),

                                    Repeater::make('non_academic_distinctions')
                                        ->label('32. NON-ACADEMIC DISTINCTIONS / RECOGNITION')
                                        ->schema([
                                            TextInput::make('distinction')->label(''),
                                        ])
                                        ->defaultItems(1)
                                        ->minItems(0)
                                        ->maxItems(7)
                                        ->addable(true)
                                        ->deletable(true),

                                    Repeater::make('membership_association')
                                        ->label('33. MEMBERSHIP IN ASSOCIATION / ORGANIZATION')
                                        ->schema([
                                            TextInput::make('organization')->label(''),
                                        ])
                                        ->defaultItems(1)
                                        ->minItems(0)
                                        ->maxItems(7)
                                        ->addable(true)
                                        ->deletable(true),
                                ]),
                            ]),
                    ]),

                /* =========================================================
| C4 – OTHER INFORMATION (Items 34–40)
|=========================================================*/
                Step::make('C4. OTHER INFORMATION')
                    ->icon('heroicon-o-exclamation-circle')
                    ->schema([

                        Section::make('IX. OTHER INFORMATION')
                            ->description('Answer the following questions truthfully. If YES, give details.')
                            ->schema([

                                /* 34 */
                                Section::make('34. Are you related by consanguinity or affinity to any of the following:')
                                    ->schema([
                                        Checkbox::make('related_third_degree')
                                            ->label('a. Within the third degree (for National Government Employees)')
                                            ->reactive(),

                                        Textarea::make('related_third_degree_details')
                                            ->label('If YES, give details')
                                            ->rows(2)
                                            ->visible(fn($get) => $get('related_third_degree')),

                                        Checkbox::make('related_fourth_degree')
                                            ->label('b. Within the fourth degree (for Local Government Employees)')
                                            ->reactive(),

                                        Textarea::make('related_fourth_degree_details')
                                            ->label('If YES, give details')
                                            ->rows(2)
                                            ->visible(fn($get) => $get('related_fourth_degree')),
                                    ]),

                                /* 35 */
                                Section::make('35. Have you ever been found guilty of any administrative offense?')
                                    ->schema([
                                        Radio::make('has_admin_case')
                                            ->options(['Yes' => 'Yes', 'No' => 'No'])
                                            ->inline()
                                            ->reactive(),

                                        Textarea::make('admin_case_details')
                                            ->label('If YES, give details')
                                            ->rows(2)
                                            ->visible(fn($get) => $get('has_admin_case') === 'Yes'),
                                    ]),

                                /* 36 */
                                Section::make('36. Have you been criminally charged before any court?')
                                    ->schema([
                                        Radio::make('has_criminal_case')
                                            ->options(['Yes' => 'Yes', 'No' => 'No'])
                                            ->inline()
                                            ->reactive(),

                                        Grid::make(2)
                                            ->visible(fn($get) => $get('has_criminal_case') === 'Yes')
                                            ->schema([
                                                TextInput::make('criminal_case_status')
                                                    ->label('Status of Case/s'),

                                                DatePicker::make('criminal_case_date_filed')
                                                    ->label('Date Filed'),
                                            ]),
                                    ]),

                                /* 37 */
                                Section::make('37. Have you ever been convicted of any crime or violation of any law?')
                                    ->schema([
                                        Radio::make('has_conviction')
                                            ->options(['Yes' => 'Yes', 'No' => 'No'])
                                            ->inline()
                                            ->reactive(),

                                        Textarea::make('conviction_details')
                                            ->label('If YES, give details')
                                            ->rows(2)
                                            ->visible(fn($get) => $get('has_conviction') === 'Yes'),
                                    ]),

                                /* 38 */
                                Section::make('38. Have you ever been separated from the service?')
                                    ->schema([
                                        Radio::make('has_been_separated')
                                            ->options(['Yes' => 'Yes', 'No' => 'No'])
                                            ->inline()
                                            ->reactive(),

                                        Textarea::make('separation_details')
                                            ->label('If YES, give details')
                                            ->rows(2)
                                            ->visible(fn($get) => $get('has_been_separated') === 'Yes'),
                                    ]),

                                /* 39 */
                                Section::make('39. Have you ever been a candidate in a national or local election?')
                                    ->schema([
                                        Radio::make('has_election_candidacy')
                                            ->options(['Yes' => 'Yes', 'No' => 'No'])
                                            ->inline()
                                            ->reactive(),

                                        Textarea::make('election_candidacy_details')
                                            ->label('If YES, give details')
                                            ->rows(2)
                                            ->visible(fn($get) => $get('has_election_candidacy') === 'Yes'),
                                    ]),

                                /* 40 */
                                Section::make('40. Do you belong to any of the following?')
                                    ->schema([
                                        Checkbox::make('is_indigenous')
                                            ->label('a. Indigenous Group')
                                            ->reactive(),

                                        TextInput::make('indigenous_details')
                                            ->label('Please specify')
                                            ->visible(fn($get) => $get('is_indigenous')),

                                        Checkbox::make('has_disability')
                                            ->label('b. Person with Disability')
                                            ->reactive(),

                                        TextInput::make('disability_details')
                                            ->label('Please specify')
                                            ->visible(fn($get) => $get('has_disability')),

                                        Checkbox::make('is_solo_parent')
                                            ->label('c. Solo Parent')
                                            ->reactive(),

                                        TextInput::make('solo_parent_details')
                                            ->label('Please specify')
                                            ->visible(fn($get) => $get('is_solo_parent')),
                                    ]),
                                /* =================================================
                                | 41. REFERENCES
                                |=================================================*/
                                Section::make('41. REFERENCES (Person not related by consanguinity or affinity)')
                                    ->schema([
                                        Repeater::make('references')
                                            ->schema([
                                                TextInput::make('name')->label('NAME')->required(),
                                                TextInput::make('address')->label('ADDRESS')->required(),
                                                TextInput::make('tel')->label('TEL. NO.'),
                                            ])
                                            ->columns(3)
                                            ->defaultItems(3)     // CSC requires 3 references
                                            ->minItems(3)
                                            ->maxItems(3)
                                            ->addable(false)
                                            ->deletable(false),
                                    ]),

                                /* =================================================
                                | 42. GOVERNMENT ISSUED ID
                                |=================================================*/
                                Section::make('42. GOVERNMENT ISSUED ID')
                                    ->schema([
                                        Select::make('gov_id_type')
                                            ->label('Government Issued ID')
                                            ->options([
                                                'Passport' => 'Passport',
                                                'GSIS' => 'GSIS',
                                                'SSS' => 'SSS',
                                                'PRC' => 'PRC',
                                                'Driver’s License' => 'Driver’s License',
                                                'Others' => 'Others',
                                            ]),

                                        TextInput::make('gov_id_no')
                                            ->label('ID / License / Passport No.'),

                                        TextInput::make('gov_id_issued')
                                            ->label('Date / Place of Issuance'),
                                    ]),

                                /* =================================================
                                | SIGNATURE & DATE (DATA ONLY – IMAGE NOT REQUIRED)
                                |=================================================*/
                                Section::make('DECLARATION')
                                    ->schema([
                                        DatePicker::make('date_accomplished')
                                            ->label('Date Accomplished'),
                                    ]),
                            ]),
                    ]),

            ])
                ->columnSpanFull()
                ->persistStepInQueryString(),

        ]);


    }

    /* -----------------------------------------------------------------
     | TABLE
     |-----------------------------------------------------------------*/
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Employee')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('surname')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('first_name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('created_at')->date()->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'submitted',
                        'success' => 'approved',
                        'danger' => 'disapproved',
                    ])
                    ->sortable(),

            ])

            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(
                        fn($record) =>
                        Auth::user()->role === 'admin' &&
                        $record->status !== 'approved'
                    )
                    ->action(
                        fn($record) =>
                        $record->update(['status' => 'approved'])
                    ),

                Tables\Actions\Action::make('disapprove')
                    ->label('Disapprove')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(
                        fn($record) =>
                        Auth::user()->role === 'admin' &&
                        $record->status !== 'disapproved'
                    )
                    ->form([
                        Textarea::make('remarks')
                            ->label('Reason for Disapproval')
                            ->rows(4)
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'disapproved',
                            'remarks' => $data['remarks'],
                        ]);
                    }),


                Tables\Actions\Action::make('remarks')
                    ->label('Add Remarks')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('warning')
                    ->visible(fn($record) => Auth::user()->role === 'admin')
                    ->form([
                        Textarea::make('remarks')
                            ->label('Admin Remarks')
                            ->rows(5)
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'remarks' => $data['remarks'],
                        ]);
                    }),


                Tables\Actions\Action::make('print')
                    ->label('Print PDS')
                    ->icon('heroicon-o-printer')
                    ->visible(fn($record) => $record->status === 'approved')
                    ->url(
                        fn($record) =>
                        route('pds.print', $record)
                    )
                    ->openUrlInNewTab(),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(
                        fn($record) =>
                        Auth::user()->role === 'employee' &&
                        $record->status !== 'approved'
                    )
                    ->visible(fn() => Auth::user()->role === 'employee'),
            ]);
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
