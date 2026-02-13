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

            Wizard::make([

                /* =========================================================
                 | STEP 1: C1 – PERSONAL INFORMATION
                 |=========================================================*/
                Step::make('C1. PERSONAL INFORMATION')
                    ->description('Personal Information, Family Background & Education')
                    ->icon('heroicon-o-user')
                    ->schema([

                        // I. PERSONAL INFORMATION
                        Section::make('I. PERSONAL INFORMATION')
                            ->description('CS Form No. 212 (Revised 2020) - Items 1-23')
                            ->schema([

                                // Name Fields (1-4)
                                Grid::make(4)->schema([
                                    TextInput::make('surname')
                                        ->label('1. SURNAME')
                                        ->required()
                                        ->disabled($isLocked),
                                    TextInput::make('first_name')
                                        ->label('2. FIRST NAME')
                                        ->required()
                                        ->disabled($isLocked),
                                    TextInput::make('middle_name')
                                        ->label('3. MIDDLE NAME')
                                        ->disabled($isLocked),
                                    TextInput::make('name_extension')
                                        ->label('4. NAME EXTENSION (JR., SR)')
                                        ->disabled($isLocked),
                                ]),

                                // Birth Details (5-6)
                                Grid::make(2)->schema([
                                    DatePicker::make('date_of_birth')
                                        ->label('5. DATE OF BIRTH (mm/dd/yyyy)')
                                        ->required()
                                        ->disabled($isLocked),
                                    TextInput::make('place_of_birth')
                                        ->label('6. PLACE OF BIRTH')
                                        ->disabled($isLocked),
                                ]),

                                // Sex & Civil Status (7-8)
                                Grid::make(2)->schema([
                                    Select::make('sex')
                                        ->label('7. SEX')
                                        ->options([
                                            'Male' => 'Male',
                                            'Female' => 'Female'
                                        ])
                                        ->disabled($isLocked),
                                    Select::make('civil_status')
                                        ->label('8. CIVIL STATUS')
                                        ->options([
                                            'Single' => 'Single',
                                            'Married' => 'Married',
                                            'Widowed' => 'Widowed',
                                            'Separated' => 'Separated',
                                        ])
                                        ->disabled($isLocked),
                                ]),

                                // Physical Info (9-11)
                                Grid::make(3)->schema([
                                    TextInput::make('height')
                                        ->label('9. HEIGHT (cm)')
                                        ->numeric()
                                        ->step(0.01)
                                        ->disabled($isLocked),
                                    TextInput::make('weight')
                                        ->label('10. WEIGHT (kg)')
                                        ->numeric()
                                        ->step(0.1)
                                        ->disabled($isLocked),
                                    TextInput::make('blood_type')
                                        ->label('11. BLOOD TYPE')
                                        ->disabled($isLocked),
                                ]),

                                // Government IDs (12-17)
                                Grid::make(3)->schema([
                                    TextInput::make('gsis_id_no')
                                        ->label('12. GSIS ID NO.')
                                        ->disabled($isLocked),
                                    TextInput::make('pag_ibig_id_no')
                                        ->label('13. PAG-IBIG ID NO.')
                                        ->disabled($isLocked),
                                    TextInput::make('philhealth_no')
                                        ->label('14. PHILHEALTH NO.')
                                        ->disabled($isLocked),
                                ]),

                                Grid::make(3)->schema([
                                    TextInput::make('sss_no')
                                        ->label('15. SSS NO.')
                                        ->disabled($isLocked),
                                    TextInput::make('tin_no')
                                        ->label('16. TIN NO.')
                                        ->disabled($isLocked),
                                    TextInput::make('agency_employee_no')
                                        ->label('17. AGENCY EMPLOYEE NO.')
                                        ->disabled($isLocked),
                                ]),

                                // Citizenship (18)
                                Section::make('18. CITIZENSHIP')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            Checkbox::make('filipino')
                                                ->label('Filipino')
                                                ->disabled($isLocked),
                                            Checkbox::make('dual_citizenship')
                                                ->label('Dual Citizenship')
                                                ->disabled($isLocked),
                                            TextInput::make('dual_citizenship_country')
                                                ->label('If holder of dual citizenship, indicate country')
                                                ->disabled($isLocked),
                                        ]),
                                    ])
                                    ->compact(),

                                // Residential Address (19)
                                Section::make('19. RESIDENTIAL ADDRESS')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextInput::make('res_house_block_lot_no')
                                                ->label('House/Block/Lot No.')
                                                ->disabled($isLocked),
                                            TextInput::make('res_street')
                                                ->label('Street')
                                                ->disabled($isLocked),
                                            TextInput::make('res_subdivision_village')
                                                ->label('Subdivision/Village')
                                                ->disabled($isLocked),
                                        ]),
                                        Grid::make(4)->schema([
                                            TextInput::make('res_barangay')
                                                ->label('Barangay')
                                                ->disabled($isLocked),
                                            TextInput::make('res_city_municipality')
                                                ->label('City/Municipality')
                                                ->disabled($isLocked),
                                            TextInput::make('res_province')
                                                ->label('Province')
                                                ->disabled($isLocked),
                                            TextInput::make('res_zip_code')
                                                ->label('ZIP Code')
                                                ->disabled($isLocked),
                                        ]),
                                    ])
                                    ->compact(),

                                // Permanent Address (20)
                                Section::make('20. PERMANENT ADDRESS')
                                    ->schema([

                                        Checkbox::make('same_as_residential')
                                            ->label('Same as Residential Address')
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $get, callable $set) {

                                                if ($state) {
                                                    // Copy Residential → Permanent
                                                    $set('perm_house_block_lot_no', $get('res_house_block_lot_no'));
                                                    $set('perm_street', $get('res_street'));
                                                    $set('perm_subdivision_village', $get('res_subdivision_village'));
                                                    $set('perm_barangay', $get('res_barangay'));
                                                    $set('perm_city_municipality', $get('res_city_municipality'));
                                                    $set('perm_province', $get('res_province'));
                                                    $set('perm_zip_code', $get('res_zip_code'));
                                                } else {
                                                    // Optional: Clear permanent address if unchecked
                                                    $set('perm_house_block_lot_no', null);
                                                    $set('perm_street', null);
                                                    $set('perm_subdivision_village', null);
                                                    $set('perm_barangay', null);
                                                    $set('perm_city_municipality', null);
                                                    $set('perm_province', null);
                                                    $set('perm_zip_code', null);
                                                }
                                            })
                                            ->disabled($isLocked)
                                            ->columnSpanFull(),

                                        Grid::make(3)->schema([
                                            TextInput::make('perm_house_block_lot_no')
                                                ->label('House/Block/Lot No.')
                                                ->disabled($isLocked),
                                            TextInput::make('perm_street')
                                                ->label('Street')
                                                ->disabled($isLocked),
                                            TextInput::make('perm_subdivision_village')
                                                ->label('Subdivision/Village')
                                                ->disabled($isLocked),
                                        ]),

                                        Grid::make(4)->schema([
                                            TextInput::make('perm_barangay')
                                                ->label('Barangay')
                                                ->disabled($isLocked),
                                            TextInput::make('perm_city_municipality')
                                                ->label('City/Municipality')
                                                ->disabled($isLocked),
                                            TextInput::make('perm_province')
                                                ->label('Province')
                                                ->disabled($isLocked),
                                            TextInput::make('perm_zip_code')
                                                ->label('ZIP Code')
                                                ->disabled($isLocked),
                                        ]),
                                    ])
                                    ->compact(),

                                // Contact Info (21-23)
                                Section::make('CONTACT INFORMATION')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextInput::make('telephone_no')
                                                ->label('21. TELEPHONE NO.')
                                                ->disabled($isLocked),
                                            TextInput::make('mobile')
                                                ->label('22. MOBILE NO.')
                                                ->disabled($isLocked),
                                            TextInput::make('email')
                                                ->label('23. E-MAIL ADDRESS (if any)')
                                                ->email()
                                                ->disabled($isLocked),
                                        ]),
                                    ])
                                    ->compact(),
                            ])
                            ->columns(1)
                            ->compact(),

                        // II. FAMILY BACKGROUND
                        Section::make('II. FAMILY BACKGROUND')
                            ->description('Items 24-27')
                            ->schema([

                                // Spouse Information (24)
                                Grid::make(4)->schema([
                                    TextInput::make('spouse_surname')
                                        ->label('24. SPOUSE\'S SURNAME')
                                        ->disabled($isLocked),
                                    TextInput::make('spouse_first_name')
                                        ->label('FIRST NAME')
                                        ->disabled($isLocked),
                                    TextInput::make('spouse_middle_name')
                                        ->label('MIDDLE NAME')
                                        ->disabled($isLocked),
                                    TextInput::make('spouse_name_extension')
                                        ->label('NAME EXTENSION (JR., SR)')
                                        ->disabled($isLocked),
                                ]),

                                Grid::make(3)->schema([
                                    TextInput::make('spouse_occupation')
                                        ->label('OCCUPATION')
                                        ->disabled($isLocked),
                                    TextInput::make('spouse_employer_business_name')
                                        ->label('EMPLOYER/BUSINESS NAME')
                                        ->disabled($isLocked),
                                    TextInput::make('spouse_business_address')
                                        ->label('BUSINESS ADDRESS')
                                        ->disabled($isLocked),
                                ]),

                                TextInput::make('spouse_telephone_no')
                                    ->label('TELEPHONE NO.')
                                    ->disabled($isLocked),

                                // Father Information (25)
                                Grid::make(4)->schema([
                                    TextInput::make('father_surname')
                                        ->label('25. FATHER\'S SURNAME')
                                        ->disabled($isLocked),
                                    TextInput::make('father_first_name')
                                        ->label('FIRST NAME')
                                        ->disabled($isLocked),
                                    TextInput::make('father_middle_name')
                                        ->label('MIDDLE NAME')
                                        ->disabled($isLocked),
                                    TextInput::make('father_name_extension')
                                        ->label('NAME EXTENSION (JR., SR)')
                                        ->disabled($isLocked),
                                ]),

                                // Mother Information (26)
                                Grid::make(3)->schema([
                                    TextInput::make('mother_surname')
                                        ->label('26. MOTHER\'S MAIDEN SURNAME')
                                        ->disabled($isLocked),
                                    TextInput::make('mother_first_name')
                                        ->label('FIRST NAME')
                                        ->disabled($isLocked),
                                    TextInput::make('mother_middle_name')
                                        ->label('MIDDLE NAME')
                                        ->disabled($isLocked),
                                ]),

                                // Children (27)
                                Repeater::make('children')
                                    ->label('27. NAME OF CHILDREN (Write full name and list all)')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('name')
                                                ->label('NAME OF CHILDREN')
                                                ->required(),
                                            DatePicker::make('birthdate')
                                                ->label('DATE OF BIRTH (mm/dd/yyyy)')
                                                ->required(),
                                        ]),
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Child')
                                    ->reorderable(false)
                                    ->columnSpanFull()
                                    ->disabled($isLocked),
                            ])
                            ->columns(1)
                            ->compact(),

                        // III. EDUCATIONAL BACKGROUND
                        Section::make('III. EDUCATIONAL BACKGROUND')
                            ->description('Item 28')
                            ->schema([
                                Repeater::make('education')
                                    ->label('28. ELEMENTARY/SECONDARY/VOCATIONAL/COLLEGE/GRADUATE STUDIES')
                                    ->schema([
                                        Grid::make(6)->schema([
                                            Select::make('level')
                                                ->label('LEVEL')
                                                ->options([
                                                    'ELEMENTARY' => 'ELEMENTARY',
                                                    'SECONDARY' => 'SECONDARY',
                                                    'VOCATIONAL/TRADE COURSE' => 'VOCATIONAL/TRADE COURSE',
                                                    'COLLEGE' => 'COLLEGE',
                                                    'GRADUATE STUDIES' => 'GRADUATE STUDIES',
                                                ])
                                                ->required(),
                                            TextInput::make('school_name')
                                                ->label('NAME OF SCHOOL (Write in full)')
                                                ->required(),
                                            TextInput::make('degree')
                                                ->label('BASIC EDUCATION/DEGREE/COURSE')
                                                ->required(),
                                            TextInput::make('from_year')
                                                ->label('FROM (YEAR)')
                                                ->placeholder('YYYY')
                                                ->maxLength(4),
                                            TextInput::make('to_year')
                                                ->label('TO (YEAR)')
                                                ->placeholder('YYYY')
                                                ->maxLength(4),
                                            TextInput::make('honors')
                                                ->label('SCHOLARSHIP/ACADEMIC HONORS'),
                                        ]),
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Education')
                                    ->reorderable(false)
                                    ->columnSpanFull()
                                    ->disabled($isLocked),
                            ])
                            ->compact(),

                    ]),

                /* =========================================================
                 | STEP 2: C2 – CIVIL SERVICE ELIGIBILITY & WORK EXPERIENCE
                 |=========================================================*/
                Step::make('C2. ELIGIBILITY & WORK EXPERIENCE')
                    ->description('Civil Service Eligibility and Work Experience')
                    ->icon('heroicon-o-briefcase')
                    ->schema([

                        // IV. CIVIL SERVICE ELIGIBILITY
                        Section::make('IV. CIVIL SERVICE ELIGIBILITY')
                            ->description('Item 29')
                            ->schema([
                                Repeater::make('civil_service_eligibility')
                                    ->label('29. CAREER SERVICE/RA 1080/BOARD/BAR/SPECIAL LAWS/CES/CSEE/DRIVER\'S LICENSE')
                                    ->schema([
                                        Grid::make(7)->schema([
                                            TextInput::make('career_service')
                                                ->label('CAREER SERVICE/RA 1080 (BOARD/BAR) UNDER SPECIAL LAWS/CES/CSEE BARANGAY ELIGIBILITY/DRIVER\'S LICENSE')
                                                ->columnSpan(2),
                                            TextInput::make('rating')
                                                ->label('RATING (If Applicable)')
                                                ->numeric(),
                                            DatePicker::make('exam_date')
                                                ->label('DATE OF EXAM/CONFERMENT'),
                                            TextInput::make('place')
                                                ->label('PLACE OF EXAM/CONFERMENT'),
                                            TextInput::make('license_no')
                                                ->label('LICENSE NUMBER (if applicable)'),
                                            DatePicker::make('validity')
                                                ->label('DATE OF VALIDITY'),
                                        ]),
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Eligibility')
                                    ->reorderable(false)
                                    ->columnSpanFull()
                                    ->disabled($isLocked),
                            ])
                            ->compact(),

                        // V. WORK EXPERIENCE
                        Section::make('V. WORK EXPERIENCE')
                            ->description('Item 30 - (Include private employment. Start from your recent work)')
                            ->schema([
                                Repeater::make('work_experience')
                                    ->label('30. WORK EXPERIENCE')
                                    ->schema([
                                        Grid::make(9)->schema([
                                            DatePicker::make('from')
                                                ->label('FROM (mm/dd/yyyy)')
                                                ->required(),
                                            DatePicker::make('to')
                                                ->label('TO (mm/dd/yyyy)')
                                                ->required(),
                                            TextInput::make('position')
                                                ->label('POSITION TITLE (Write in full/Do not abbreviate)')
                                                ->columnSpan(2)
                                                ->required(),
                                            TextInput::make('agency')
                                                ->label('DEPT/AGENCY/OFFICE/COMPANY (Write in full/Do not abbreviate)')
                                                ->columnSpan(2)
                                                ->required(),
                                            TextInput::make('salary')
                                                ->label('MONTHLY SALARY')
                                                ->numeric()
                                                ->prefix('₱'),
                                            TextInput::make('salary_grade')
                                                ->label('SALARY GRADE/STEP'),
                                            TextInput::make('status')
                                                ->label('STATUS OF APPOINTMENT'),
                                            Radio::make('is_government')
                                                ->label('GOV\'T SERVICE (Y/N)')
                                                ->boolean()
                                                ->inline(),
                                        ]),
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Work Experience')
                                    ->reorderable(false)
                                    ->columnSpanFull()
                                    ->disabled($isLocked),
                            ])
                            ->compact(),

                    ]),

                /* =========================================================
                 | STEP 3: C3 – VOLUNTARY WORK, L&D & OTHER INFORMATION
                 |=========================================================*/
                Step::make('C3. TRAINING & OTHER INFO')
                    ->description('Voluntary Work, Learning & Development, Other Information')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([

                        // VI. VOLUNTARY WORK
                        Section::make('VI. VOLUNTARY WORK OR INVOLVEMENT')
                            ->description('Item 31 - In Civic/Non-Government/People/Voluntary Organizations')
                            ->schema([
                                Repeater::make('voluntary_work')
                                    ->label('31. VOLUNTARY WORK')
                                    ->schema([
                                        Grid::make(6)->schema([
                                            TextInput::make('organization_name')
                                                ->label('NAME & ADDRESS OF ORGANIZATION (Write in full)')
                                                ->columnSpan(2),
                                            DatePicker::make('from_date')
                                                ->label('FROM (mm/dd/yyyy)'),
                                            DatePicker::make('to_date')
                                                ->label('TO (mm/dd/yyyy)'),
                                            TextInput::make('hours')
                                                ->label('NUMBER OF HOURS')
                                                ->numeric(),
                                            TextInput::make('position')
                                                ->label('POSITION/NATURE OF WORK'),
                                        ]),
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Voluntary Work')
                                    ->reorderable(false)
                                    ->columnSpanFull()
                                    ->disabled($isLocked),
                            ])
                            ->compact(),

                        // VII. LEARNING AND DEVELOPMENT
                        Section::make('VII. LEARNING AND DEVELOPMENT (L&D)')
                            ->description('Item 32 - Interventions/Training Programs Attended')
                            ->schema([
                                Repeater::make('learning_development')
                                    ->label('32. L&D INTERVENTIONS/TRAINING PROGRAMS')
                                    ->schema([
                                        Grid::make(7)->schema([
                                            TextInput::make('training_title')
                                                ->label('TITLE OF L&D INTERVENTIONS/TRAINING (Write in full)')
                                                ->columnSpan(2),
                                            DatePicker::make('from_date')
                                                ->label('FROM (mm/dd/yyyy)'),
                                            DatePicker::make('to_date')
                                                ->label('TO (mm/dd/yyyy)'),
                                            TextInput::make('hours')
                                                ->label('NUMBER OF HOURS')
                                                ->numeric(),
                                            TextInput::make('type')
                                                ->label('TYPE OF LD (Managerial/Supervisory/Technical/etc)'),
                                            TextInput::make('conducted_by')
                                                ->label('CONDUCTED/SPONSORED BY (Write in full)'),
                                        ]),
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Training/L&D')
                                    ->reorderable(false)
                                    ->columnSpanFull()
                                    ->disabled($isLocked),
                            ])
                            ->compact(),

                        // VIII. OTHER INFORMATION
                        Section::make('VIII. OTHER INFORMATION')
                            ->description('Items 33-35')
                            ->schema([
                                Grid::make(3)->schema([

                                    Repeater::make('special_skills')
                                        ->label('33. SPECIAL SKILLS and HOBBIES')
                                        ->simple(
                                            TextInput::make('skill')
                                                ->label('')
                                        )
                                        ->defaultItems(0)
                                        ->addActionLabel('Add Skill/Hobby')
                                        ->disabled($isLocked),

                                    Repeater::make('non_academic_distinctions')
                                        ->label('34. NON-ACADEMIC DISTINCTIONS/RECOGNITION (Write in full)')
                                        ->simple(
                                            TextInput::make('distinction')
                                                ->label('')
                                        )
                                        ->defaultItems(0)
                                        ->addActionLabel('Add Recognition')
                                        ->disabled($isLocked),

                                    Repeater::make('membership_association')
                                        ->label('35. MEMBERSHIP IN ASSOCIATION/ORGANIZATION (Write in full)')
                                        ->simple(
                                            TextInput::make('organization')
                                                ->label('')
                                        )
                                        ->defaultItems(0)
                                        ->addActionLabel('Add Membership')
                                        ->disabled($isLocked),
                                ]),
                            ])
                            ->compact(),

                    ]),

                /* =========================================================
                 | STEP 4: C4 – QUESTIONS, REFERENCES & DECLARATION
                 |=========================================================*/
                Step::make('C4. QUESTIONS & DECLARATION')
                    ->description('Answer Questions, Provide References & Declaration')
                    ->icon('heroicon-o-exclamation-circle')
                    ->schema([

                        // IX. QUESTIONS
                        Section::make('IX. ANSWER THE FOLLOWING QUESTIONS')
                            ->description('36. Are you related by consanguinity or affinity to the appointing or recommending authority, or to the chief of bureau or office or to the person who has immediate supervision over you in the Office, Bureau or Department where you will be appointed:')
                            ->schema([

                                // Question 36a
                                Radio::make('related_third_degree')
                                    ->label('a. within the third degree?')
                                    ->boolean()
                                    ->inline()
                                    ->reactive()
                                    ->disabled($isLocked),
                                Textarea::make('related_third_degree_details')
                                    ->label('If YES, give details:')
                                    ->rows(2)
                                    ->visible(fn($get) => $get('related_third_degree') === true)
                                    ->disabled($isLocked),

                                // Question 36b
                                Radio::make('related_fourth_degree')
                                    ->label('b. within the fourth degree (for Local Government Unit - Career Employees)?')
                                    ->boolean()
                                    ->inline()
                                    ->reactive()
                                    ->disabled($isLocked),
                                Textarea::make('related_fourth_degree_details')
                                    ->label('If YES, give details:')
                                    ->rows(2)
                                    ->visible(fn($get) => $get('related_fourth_degree') === true)
                                    ->disabled($isLocked),

                                // Question 37
                                Radio::make('has_admin_case')
                                    ->label('37. a. Have you ever been found guilty of any administrative offense?')
                                    ->boolean()
                                    ->inline()
                                    ->reactive()
                                    ->disabled($isLocked),
                                Textarea::make('admin_case_details')
                                    ->label('If YES, give details:')
                                    ->rows(2)
                                    ->visible(fn($get) => $get('has_admin_case') === true)
                                    ->disabled($isLocked),

                                // Question 38
                                Radio::make('has_criminal_case')
                                    ->label('38. Have you been criminally charged before any court?')
                                    ->boolean()
                                    ->inline()
                                    ->reactive()
                                    ->disabled($isLocked),
                                Grid::make(2)
                                    ->schema([
                                        DatePicker::make('criminal_case_date_filed')
                                            ->label('If YES, Date Filed:')
                                            ->disabled($isLocked),
                                        TextInput::make('criminal_case_status')
                                            ->label('Status of Case/s:')
                                            ->disabled($isLocked),
                                    ])
                                    ->visible(fn($get) => $get('has_criminal_case') === true),

                                // Question 39
                                Radio::make('has_conviction')
                                    ->label('39. Have you ever been convicted of any crime or violation of any law, decree, ordinance or regulation by any court or tribunal?')
                                    ->boolean()
                                    ->inline()
                                    ->reactive()
                                    ->disabled($isLocked),
                                Textarea::make('conviction_details')
                                    ->label('If YES, give details:')
                                    ->rows(2)
                                    ->visible(fn($get) => $get('has_conviction') === true)
                                    ->disabled($isLocked),

                                // Question 40
                                Radio::make('has_been_separated')
                                    ->label('40. Have you ever been separated from the service in any of the following modes: resignation, retirement, dropped from the rolls, dismissal, termination, end of term, finished contract or phased out (abolition) in the public or private sector?')
                                    ->boolean()
                                    ->inline()
                                    ->reactive()
                                    ->disabled($isLocked),
                                Textarea::make('separation_details')
                                    ->label('If YES, give details:')
                                    ->rows(2)
                                    ->visible(fn($get) => $get('has_been_separated') === true)
                                    ->disabled($isLocked),

                                // Question 41
                                Radio::make('has_election_candidacy')
                                    ->label('41. a. Have you ever been a candidate in a national or local election held within the last year (except Barangay election)?')
                                    ->boolean()
                                    ->inline()
                                    ->reactive()
                                    ->disabled($isLocked),
                                Textarea::make('election_candidacy_details')
                                    ->label('If YES, give details:')
                                    ->rows(2)
                                    ->visible(fn($get) => $get('has_election_candidacy') === true)
                                    ->disabled($isLocked),

                                // Question 42a
                                Checkbox::make('is_indigenous')
                                    ->label('42. a. Are you a member of any indigenous group?')
                                    ->reactive()
                                    ->disabled($isLocked),
                                TextInput::make('indigenous_details')
                                    ->label('If YES, please specify:')
                                    ->visible(fn($get) => $get('is_indigenous'))
                                    ->disabled($isLocked),

                                // Question 42b
                                Checkbox::make('has_disability')
                                    ->label('b. Are you a person with disability?')
                                    ->reactive()
                                    ->disabled($isLocked),
                                TextInput::make('disability_details')
                                    ->label('If YES, please specify ID No.:')
                                    ->visible(fn($get) => $get('has_disability'))
                                    ->disabled($isLocked),

                                // Question 42c
                                Checkbox::make('is_solo_parent')
                                    ->label('c. Are you a solo parent?')
                                    ->reactive()
                                    ->disabled($isLocked),
                                TextInput::make('solo_parent_details')
                                    ->label('If YES, please specify ID No.:')
                                    ->visible(fn($get) => $get('is_solo_parent'))
                                    ->disabled($isLocked),

                            ])
                            ->columns(1)
                            ->compact(),

                        // REFERENCES
                        Section::make('43. REFERENCES')
                            ->description('(Person not related by consanguinity or affinity to applicant/appointee)')
                            ->schema([
                                Repeater::make('references')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextInput::make('name')
                                                ->label('NAME')
                                                ->required(),
                                            TextInput::make('address')
                                                ->label('ADDRESS')
                                                ->required(),
                                            TextInput::make('tel')
                                                ->label('TEL. NO.'),
                                        ]),
                                    ])
                                    ->defaultItems(3)
                                    ->minItems(3)
                                    ->maxItems(3)
                                    ->addable(false)
                                    ->deletable(false)
                                    ->columnSpanFull()
                                    ->disabled($isLocked),
                            ])
                            ->compact(),

                        // GOVERNMENT ID & DECLARATION
                        Section::make('44. DECLARATION')
                            ->description('I declare under oath that I have personally accomplished this Personal Data Sheet which is a true, correct and complete statement pursuant to the provisions of pertinent laws, rules and regulations of the Republic of the Philippines. I authorize the agency head/authorized representative to verify/validate the contents stated herein. I agree that any misrepresentation made in this document and its attachments shall cause the filing of administrative/criminal case/s against me.')
                            ->schema([
                                Grid::make(3)->schema([
                                    TextInput::make('gov_id_type')
                                        ->label('Government Issued ID (i.e. Passport, GSIS, SSS, PRC, Driver\'s License, etc.)')
                                        ->disabled($isLocked),
                                    TextInput::make('gov_id_no')
                                        ->label('ID/License/Passport No.')
                                        ->disabled($isLocked),
                                    TextInput::make('gov_id_issued')
                                        ->label('Date/Place of Issuance')
                                        ->disabled($isLocked),
                                ]),

                                DatePicker::make('date_accomplished')
                                    ->label('Date Accomplished')
                                    ->default(now())
                                    ->required()
                                    ->disabled($isLocked),
                            ])
                            ->compact(),

                    ]),

            ])
                ->columnSpanFull()
                ->persistStepInQueryString()
                ->skippable(Auth::user()->role === 'admin'),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Employee')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('surname')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('first_name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->date()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'submitted',
                        'success' => 'approved',
                        'danger' => 'disapproved',
                    ])
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'submitted' => 'Submitted',
                        'approved' => 'Approved',
                        'disapproved' => 'Disapproved',
                    ]),
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
                    ->requiresConfirmation()
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
                        $record->update(['remarks' => $data['remarks']]);
                    }),

                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->visible(fn($record) => $record->status === 'approved')
                    ->url(fn($record) => route('pds.print', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make()
                    ->visible(
                        fn($record) =>
                        Auth::user()->role === 'employee' &&
                        $record->status !== 'approved'
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()->role === 'admin'),
                ]),
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
