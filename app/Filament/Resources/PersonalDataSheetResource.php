<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalDataSheetResource\Pages;
use App\Models\PersonalDataSheet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class PersonalDataSheetResource extends Resource
{
    protected static ?string $model = PersonalDataSheet::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Personal Data Sheet';
    protected static ?string $title = 'Personal Data Sheets';
    protected static ?string $slug = 'personal-data-sheet';
    protected static ?string $navigationGroup = 'Manage';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('remarks')
                    ->label('Remarks from Admin')
                    ->disabled()
                    ->rows(5)
                    ->columnSpanFull()
                    ->visible(fn($record) => filled($record?->remarks)),
                Forms\Components\Wizard::make([
                    // Step 1: Personal Information
                    Forms\Components\Wizard\Step::make('Personal Information')
                        ->icon('heroicon-o-user')
                        ->description('Basic personal details and contact information')
                        ->schema([
                            // Basic Personal Information
                            Forms\Components\Section::make('Basic Information')
                                ->schema([
                                    Forms\Components\TextInput::make('surname')
                                        ->label('SURNAME')
                                        ->required()
                                        ->maxLength(255)
                                        ->afterStateHydrated(function (TextInput $component, $state) {
                                            if (!$state) {
                                                $user = Auth::user();
                                                $component->state(Auth::user()?->last_name);
                                            }
                                        }),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('first_name')
                                                ->label('FIRST NAME')
                                                ->required()
                                                ->maxLength(255)
                                                ->afterStateHydrated(function (TextInput $component, $state) {
                                                    if (!$state) {
                                                        $user = Auth::user();
                                                        $component->state(Auth::user()?->first_name);
                                                    }
                                                }),
                                            Forms\Components\TextInput::make('name_extension')
                                                ->label('NAME EXTENSION (JR., SR.)')
                                                ->maxLength(255),
                                        ]),

                                    Forms\Components\TextInput::make('middle_name')
                                        ->label('MIDDLE NAME')
                                        ->maxLength(255),

                                    Forms\Components\DatePicker::make('date_of_birth')
                                        ->label('DATE OF BIRTH (mm/dd/yyyy)')
                                        ->required()
                                        ->afterStateHydrated(function (DatePicker $component, $state) {
                                            if (!$state) {
                                                $user = Auth::user();
                                                $component->state(Auth::user()?->birthday);
                                            }
                                        }),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Radio::make('sex')
                                                ->label('SEX')
                                                ->options([
                                                    'male' => 'Male',
                                                    'female' => 'Female',
                                                ])
                                                ->inline()
                                                ->required(),

                                            Forms\Components\Radio::make('civil_status')
                                                ->label('CIVIL STATUS')
                                                ->options([
                                                    'single' => 'Single',
                                                    'married' => 'Married',
                                                    'widowed' => 'Widowed',
                                                    'separated' => 'Separated',
                                                    'others' => 'Others',
                                                ])
                                                ->required(),
                                        ]),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('height')
                                                ->label('HEIGHT (cm)')
                                                ->numeric()
                                                ->step(0.01),
                                            Forms\Components\TextInput::make('weight')
                                                ->label('WEIGHT (kg)')
                                                ->numeric()
                                                ->step(0.1),
                                        ]),

                                    Forms\Components\TextInput::make('blood_type')
                                        ->label('BLOOD TYPE')
                                        ->maxLength(10),
                                ])
                                ->columns(1),

                            // Government IDs
                            Forms\Components\Section::make('Government IDs')
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('gsis_id_no')
                                                ->label('GSIS ID NO.')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('pag_ibig_id_no')
                                                ->label('PAG-IBIG ID NO.')
                                                ->maxLength(255),
                                        ]),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('philhealth_no')
                                                ->label('PHILHEALTH NO.')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('sss_no')
                                                ->label('SSS NO.')
                                                ->maxLength(255),
                                        ]),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('tin_no')
                                                ->label('TIN NO.')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('agency_employee_no')
                                                ->label('AGENCY EMPLOYEE NO.')
                                                ->maxLength(255),
                                        ]),
                                ]),

                            // Citizenship Section
                            Forms\Components\Section::make('Citizenship')
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Checkbox::make('filipino')
                                                ->label('Filipino'),
                                            Forms\Components\Checkbox::make('dual_citizenship')
                                                ->label('Dual Citizenship')
                                                ->reactive(),
                                        ]),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Checkbox::make('by_birth')
                                                ->label('by birth')
                                                ->visible(fn($get) => $get('dual_citizenship')),
                                            Forms\Components\Checkbox::make('by_naturalization')
                                                ->label('by naturalization')
                                                ->visible(fn($get) => $get('dual_citizenship')),
                                        ]),

                                    Forms\Components\TextInput::make('country')
                                        ->label('If holder of dual citizenship, Pls. indicate country:')
                                        ->visible(fn($get) => $get('dual_citizenship'))
                                        ->maxLength(255),
                                ]),

                            // Residential Address Section
                            Forms\Components\Section::make('Residential Address')
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('res_house_block_lot_no')
                                                ->label('House/Block/Lot No.')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('res_street')
                                                ->label('Street')
                                                ->maxLength(255),
                                        ]),

                                    Forms\Components\Grid::make(3)
                                        ->schema([
                                            Forms\Components\TextInput::make('res_subdivision_village')
                                                ->label('Subdivision/Village')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('res_barangay')
                                                ->label('Barangay')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('res_city_municipality')
                                                ->label('City/Municipality')
                                                ->maxLength(255),
                                        ]),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('res_province')
                                                ->label('Province')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('res_zip_code')
                                                ->label('ZIP CODE')
                                                ->maxLength(10),
                                        ]),
                                ]),

                            // Permanent Address Section
                            Forms\Components\Section::make('Permanent Address')
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('perm_house_block_lot_no')
                                                ->label('House/Block/Lot No.')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('perm_street')
                                                ->label('Street')
                                                ->maxLength(255),
                                        ]),

                                    Forms\Components\Grid::make(3)
                                        ->schema([
                                            Forms\Components\TextInput::make('perm_subdivision_village')
                                                ->label('Subdivision/Village')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('perm_barangay')
                                                ->label('Barangay')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('perm_city_municipality')
                                                ->label('City/Municipality')
                                                ->maxLength(255),
                                        ]),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('perm_province')
                                                ->label('Province')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('perm_zip_code')
                                                ->label('ZIP CODE')
                                                ->maxLength(10),
                                        ]),
                                ]),

                            // Contact Information
                            Forms\Components\Section::make('Contact Information')
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('telephone_no')
                                                ->label('TELEPHONE NO.')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('mobile_no')
                                                ->label('MOBILE NO.')
                                                ->maxLength(255),
                                        ]),

                                    Forms\Components\TextInput::make('email_address')
                                        ->label('E-MAIL ADDRESS (if any)')
                                        ->email()
                                        ->maxLength(255),
                                ]),
                        ]),

                    // Step 2: Family Background
                    Forms\Components\Wizard\Step::make('Family Background')
                        ->icon('heroicon-o-users')
                        ->description('Information about spouse, parents, and children')
                        ->schema([
                            // Spouse Information
                            Forms\Components\Section::make('Spouse Information')
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('spouse_surname')
                                                ->label("SPOUSE'S SURNAME")
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('spouse_first_name')
                                                ->label('FIRST NAME')
                                                ->maxLength(255),
                                        ]),

                                    Forms\Components\Grid::make(3)
                                        ->schema([
                                            Forms\Components\TextInput::make('spouse_name_extension')
                                                ->label('NAME EXTENSION (JR., SR.)')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('spouse_middle_name')
                                                ->label('MIDDLE NAME')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('spouse_occupation')
                                                ->label('OCCUPATION')
                                                ->maxLength(255),
                                        ]),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('spouse_employer_business_name')
                                                ->label('EMPLOYER/BUSINESS NAME')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('spouse_business_address')
                                                ->label('BUSINESS ADDRESS')
                                                ->maxLength(255),
                                        ]),

                                    Forms\Components\TextInput::make('spouse_telephone_no')
                                        ->label('TELEPHONE NO.')
                                        ->maxLength(255),
                                ]),

                            // Father Information
                            Forms\Components\Section::make('Father\'s Information')
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('father_surname')
                                                ->label('SURNAME')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('father_first_name')
                                                ->label('FIRST NAME')
                                                ->maxLength(255),
                                        ]),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('father_name_extension')
                                                ->label('NAME EXTENSION (JR., SR.)')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('father_middle_name')
                                                ->label('MIDDLE NAME')
                                                ->maxLength(255),
                                        ]),
                                ]),

                            // Mother Information
                            Forms\Components\Section::make('Mother\'s Maiden Name')
                                ->schema([
                                    Forms\Components\Grid::make(3)
                                        ->schema([
                                            Forms\Components\TextInput::make('mother_surname')
                                                ->label('SURNAME')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('mother_first_name')
                                                ->label('FIRST NAME')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('mother_middle_name')
                                                ->label('MIDDLE NAME')
                                                ->maxLength(255),
                                        ]),
                                ]),

                            // Children Information
                            Forms\Components\Section::make('Children Information')
                                ->schema([
                                    Forms\Components\Repeater::make('children')
                                        ->label('NAME OF CHILDREN (Write full name and list all)')
                                        ->schema([
                                            Forms\Components\Grid::make(2)
                                                ->schema([
                                                    Forms\Components\TextInput::make('child_name')
                                                        ->label('Full Name')
                                                        ->required()
                                                        ->maxLength(255),
                                                    Forms\Components\DatePicker::make('child_date_of_birth')
                                                        ->label('Date of Birth (mm/dd/yyyy)')
                                                        ->required(),
                                                ]),
                                        ])
                                        ->collapsed()
                                        ->itemLabel(fn(array $state): ?string => $state['child_name'] ?? null)
                                        ->addActionLabel('Add Child')
                                        ->maxItems(10),
                                ]),
                        ]),

                    // Step 3: Educational Background
                    Forms\Components\Wizard\Step::make('Educational Background')
                        ->icon('heroicon-o-academic-cap')
                        ->description('Educational history and qualifications')
                        ->schema([
                            Forms\Components\Section::make('Educational Records')
                                ->schema([
                                    Forms\Components\Repeater::make('education')
                                        ->schema([
                                            Forms\Components\Grid::make(2)
                                                ->schema([
                                                    Forms\Components\Select::make('level')
                                                        ->label('LEVEL')
                                                        ->options([
                                                            'elementary' => 'ELEMENTARY',
                                                            'junior high scool' => 'JUNIOR HIGH SCHOOL',
                                                            'senior high school' => 'SENIOR HIGH SCHOOL',
                                                            'vocational' => 'VOCATIONAL/TRADE COURSE',
                                                            'college' => 'COLLEGE',
                                                            'graduate' => 'GRADUATE STUDIES',
                                                        ])
                                                        ->required(),

                                                    Forms\Components\TextInput::make('school_name')
                                                        ->label('NAME OF SCHOOL (Write in full)')
                                                        ->required()
                                                        ->maxLength(255),
                                                ]),

                                            Forms\Components\TextInput::make('basic_education_degree_course')
                                                ->label('BASIC EDUCATION/DEGREE/COURSE (Write in full)')
                                                ->maxLength(255),

                                            Forms\Components\Grid::make(3)
                                                ->schema([
                                                    Forms\Components\TextInput::make('period_from')
                                                        ->label('Period From')
                                                        ->maxLength(4)
                                                        ->placeholder('e.g., 2015'),

                                                    Forms\Components\TextInput::make('period_to')
                                                        ->label('Period To')
                                                        ->maxLength(4)
                                                        ->placeholder('e.g., 2019'),

                                                    Forms\Components\TextInput::make('year_graduated')
                                                        ->label('YEAR GRADUATED')
                                                        ->maxLength(4),
                                                ]),

                                            Forms\Components\TextInput::make('highest_level_units_earned')
                                                ->label('HIGHEST LEVEL/UNITS EARNED (if not graduated)')
                                                ->maxLength(255),

                                            Forms\Components\TextInput::make('scholarship_academic_honors')
                                                ->label('SCHOLARSHIP/ACADEMIC HONORS RECEIVED')
                                                ->maxLength(255),
                                        ])
                                        ->collapsed()
                                        ->itemLabel(fn(array $state): ?string => $state['school_name'] ?? 'Educational Record')
                                        ->addActionLabel('Add Educational Record')
                                        ->defaultItems(3)
                                        ->maxItems(10)
                                        ->columnSpanFull(),
                                ]),

                        ]),
                    // Step 4: Civil Service Eligibility
                    Forms\Components\Wizard\Step::make('Civil Service Eligibility')
                        ->icon('heroicon-o-check-badge')
                        ->description('Civil service exams and eligibility records')
                        ->schema([
                            Forms\Components\Section::make('Eligibility Details')
                                ->schema([
                                    Forms\Components\Repeater::make('eligibilities')
                                        ->label('Eligibility Information')
                                        ->schema([
                                            Forms\Components\Grid::make(2)->schema([
                                                Forms\Components\TextInput::make('eligibility')
                                                    ->label('CAREER SERVICE/RA 1080 (BOARD/BAR) UNDER SPECIAL LAWS/CES/CSEE BARANGAY ELIGIBILITY / DRIVER\'S LICENSE')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('rating')
                                                    ->label('RATING (If Applicable)')
                                                    ->maxLength(100),
                                            ]),
                                            Forms\Components\Grid::make(2)->schema([
                                                Forms\Components\DatePicker::make('date_of_examination')
                                                    ->label('Date of Examination / Conferment')
                                                    ->required(),
                                                Forms\Components\TextInput::make('place_of_examination')
                                                    ->label('Place of Examination / Conferment')
                                                    ->required()
                                                    ->maxLength(255),
                                            ]),
                                            Forms\Components\Grid::make(2)->schema([
                                                Forms\Components\TextInput::make('license_number')
                                                    ->label('License Number (if applicable)')
                                                    ->maxLength(100),
                                                Forms\Components\DatePicker::make('license_validity')
                                                    ->label('Date of Validity (if applicable)'),
                                            ]),
                                        ])
                                        ->collapsed()
                                        ->addActionLabel('Add Eligibility')
                                        ->itemLabel(fn(array $state): ?string => $state['eligibility'] ?? null)
                                        ->maxItems(10)
                                        ->columnSpanFull(),
                                ]),
                        ]),
                    // Step 5: Work Experience
                    Forms\Components\Wizard\Step::make('Work Experience')
                        ->icon('heroicon-o-briefcase')
                        ->description('Work history in public and private sectors')
                        ->schema([
                            Forms\Components\Section::make('Work History')
                                ->schema([
                                    Forms\Components\Repeater::make('work_experience')
                                        ->label('Work Experience')
                                        ->schema([
                                            Forms\Components\Grid::make(2)->schema([
                                                Forms\Components\TextInput::make('position_title')
                                                    ->label('Position Title')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('department_agency')
                                                    ->label('Department / Agency / Office / Company')
                                                    ->required()
                                                    ->maxLength(255),
                                            ]),
                                            Forms\Components\Grid::make(3)->schema([
                                                Forms\Components\DatePicker::make('from')
                                                    ->label('From')
                                                    ->required(),
                                                Forms\Components\DatePicker::make('to')
                                                    ->label('To')
                                                    ->required(),
                                                Forms\Components\TextInput::make('monthly_salary')
                                                    ->label('Monthly Salary')
                                                    ->numeric()
                                                    ->maxLength(100),
                                            ]),
                                            Forms\Components\Grid::make(2)->schema([
                                                Forms\Components\TextInput::make('salary_grade')
                                                    ->label('Salary Grade & Step (if applicable)')
                                                    ->maxLength(100),
                                                Forms\Components\TextInput::make('status_of_appointment')
                                                    ->label('Status of Appointment')
                                                    ->maxLength(100),
                                            ]),
                                            Forms\Components\TextInput::make('is_gov_service')
                                                ->label('Gov’t Service (Y/N)')
                                                ->maxLength(1),
                                        ])
                                        ->collapsed()
                                        ->addActionLabel('Add Work Experience')
                                        ->itemLabel(fn(array $state): ?string => $state['position_title'] ?? 'Work Record')
                                        ->maxItems(20)
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ])
                    ->columnSpanFull()
                    ->persistStepInQueryString()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year')
                    ->label('Year')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('surname')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('first_name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('middle_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sex')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'male' => 'blue',
                        'female' => 'pink',
                    }),

                Tables\Columns\TextColumn::make('civil_status')
                    ->badge(),

                Tables\Columns\TextColumn::make('email_address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->options(
                        fn() => PersonalDataSheet::select('year')->distinct()->pluck('year', 'year')->sortDesc()
                    )
                    ->label('Filter by Year'),

                Tables\Filters\SelectFilter::make('sex')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ]),

                Tables\Filters\SelectFilter::make('civil_status')
                    ->options([
                        'single' => 'Single',
                        'married' => 'Married',
                        'widowed' => 'Widowed',
                        'separated' => 'Separated',
                        'others' => 'Others',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonalDataSheets::route('/'),
            'create' => Pages\CreatePersonalDataSheet::route('/create'),
            //'view' => Pages\ViewPersonalDataSheet::route('/{record}'),
            'edit' => Pages\EditPersonalDataSheet::route('/{record}/edit'),
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();

        // Only show badge for admins
        if ($user && $user->is_admin) {
            $count = PersonalDataSheet::where('status', 'pending')->count();
            return $count > 0 ? (string) $count : null;
        }

        return null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $user = auth()->user();

        if ($user && $user->is_admin) {
            $count = PersonalDataSheet::where('status', 'pending')->count();
            return $count > 0 ? 'warning' : 'success';
        }

        return null;
    }
}
