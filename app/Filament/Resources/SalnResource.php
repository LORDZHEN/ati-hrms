<?php

namespace App\Filament\Resources;

use App\Models\Saln;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SalnResource\Pages;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\View as ViewComponent;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Notifications\SalnRemarksAdded;
use Carbon\Carbon;

class SalnResource extends Resource
{
    protected static ?string $model = Saln::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'SALN';
    protected static ?string $slug = 'salns';
    protected static ?string $modelLabel = 'SALN';
    protected static ?string $pluralModelLabel = 'Statement of Assets, Liabilities and Net Worth';
    protected static ?string $navigationGroup = 'Documents';
    protected static ?int $navigationSort = 5;

    // =========================================================================
    //  AUTHORIZATION
    // =========================================================================

    public static function canCreate(): bool
    {
        return Auth::user()->role === 'employee';
    }

    public static function canEdit($record): bool
    {
        $user = Auth::user();
        if ($user->role === 'admin')
            return false;
        return $record->user_id === $user->id;
    }

    public static function canView($record): bool
    {
        $user = Auth::user();
        if ($user->role === 'admin')
            return true;
        return $record->user_id === $user->id;
    }

    // =========================================================================
    //  FORM
    // =========================================================================

    public static function form(Form $form): Form
    {
        $isAdmin = fn() => Auth::user()?->role === 'admin';

        return $form->schema([

            self::buildAdminRemarksSection($isAdmin),

            ViewComponent::make('filament.resources.saln-resource.saln-form')
                ->columnSpanFull(),

            Section::make('Form Fields (For Validation Only)')
                ->description('⚠️ Please fill out the official SALN form above. These fields are for data binding.')
                ->schema([

                    // -----------------------------------------------------------------------
                    //  as_of_date: default(now()) ensures the date is pre-filled on create.
                    //  The Blade input reads wire:model="data.as_of_date" — because Filament
                    //  binds this field to the Livewire $data array, setting a default here
                    //  means the native <input type="date"> in the Blade will have a value
                    //  on first load rather than showing "dd/mm/yyyy".
                    // -----------------------------------------------------------------------
                    Forms\Components\DatePicker::make('as_of_date')
                        ->label('As of Date')
                        ->required()
                        ->default(fn() => now()->toDateString())
                        ->native(false),

                    Grid::make(3)->schema([
                        Forms\Components\Checkbox::make('joint_filing')
                            ->label('Joint Filing')
                            ->live()
                            ->afterStateUpdated(fn($state, callable $set) =>
                                $state ? ($set('separate_filing', false) && $set('not_applicable', false)) : null),
                        Forms\Components\Checkbox::make('separate_filing')
                            ->label('Separate Filing')
                            ->live()
                            ->afterStateUpdated(fn($state, callable $set) =>
                                $state ? ($set('joint_filing', false) && $set('not_applicable', false)) : null),
                        Forms\Components\Checkbox::make('not_applicable')
                            ->label('Not Applicable')
                            ->live()
                            ->afterStateUpdated(fn($state, callable $set) =>
                                $state ? ($set('joint_filing', false) && $set('separate_filing', false)) : null),
                    ]),

                    Grid::make(4)->schema([
                        Forms\Components\TextInput::make('declarant_family_name')->label('Family Name')->required()->default(fn() => Auth::user()?->last_name),
                        Forms\Components\TextInput::make('declarant_first_name')->label('First Name')->required()->default(fn() => Auth::user()?->first_name)->columnSpan(2),
                        Forms\Components\TextInput::make('declarant_middle_initial')->label('M.I.')->maxLength(5)->default(fn() => substr(Auth::user()?->middle_name ?? '', 0, 1)),
                    ]),

                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('declarant_position')->label('Position')->required()->default(fn() => Auth::user()?->position),
                        Forms\Components\TextInput::make('declarant_agency_office')->label('Agency/Office')->required()->default(fn() => Auth::user()?->department),
                    ]),

                    Forms\Components\Textarea::make('declarant_office_address')->label('Office Address')->required()->rows(2)
                        ->default(fn() => implode(', ', array_filter([Auth::user()?->purok_street, Auth::user()?->city_municipality, Auth::user()?->province]))),

                    Grid::make(4)->schema([
                        Forms\Components\TextInput::make('spouse_family_name')->label('Spouse Family Name'),
                        Forms\Components\TextInput::make('spouse_first_name')->label('Spouse First Name')->columnSpan(2),
                        Forms\Components\TextInput::make('spouse_middle_initial')->label('M.I.')->maxLength(5),
                    ]),

                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('spouse_position')->label('Spouse Position'),
                        Forms\Components\TextInput::make('spouse_agency_office')->label('Spouse Agency/Office'),
                    ]),

                    Forms\Components\Textarea::make('spouse_office_address')->label('Spouse Office Address')->rows(2),

                    Repeater::make('children')
                        ->relationship('children')
                        ->label('Children')
                        ->schema([
                            Grid::make(3)->schema([
                                Forms\Components\TextInput::make('name')->label('Full Name')->required()->columnSpan(2),
                                // ---------------------------------------------------------------
                                //  date_of_birth: DatePicker with afterStateHydrated to
                                //  normalize the raw DB value.  The DB stores dates as full
                                //  ISO datetimes ("2015-06-16T00:00:00.000000Z").
                                //  afterStateHydrated runs once on form load and converts
                                //  the value to a plain Y-m-d string — the only format
                                //  Filament's DatePicker and the Blade <input type="date">
                                //  both accept without errors.
                                // ---------------------------------------------------------------
                                Forms\Components\DatePicker::make('date_of_birth')
                                    ->label('Date of Birth')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('M d, Y')
                                    ->maxDate(now())
                                    ->afterStateHydrated(function ($state, callable $set) {
                                        if ($state) {
                                            try {
                                                $set('date_of_birth', Carbon::parse($state)->format('Y-m-d'));
                                            } catch (\Exception $e) {
                                            }
                                        }
                                    })
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            try {
                                                $set('age', (int) Carbon::parse($state)->age);
                                            } catch (\Exception $e) {
                                                $set('age', null);
                                            }
                                        } else {
                                            $set('age', null);
                                        }
                                    }),
                            ]),
                            Forms\Components\TextInput::make('age')
                                ->label('Age')
                                ->integer()
                                ->disabled()
                                ->dehydrated()
                                ->suffix('years old'),
                        ])
                        ->columns(1)->addActionLabel('Add Child')->reorderable(false)->collapsible()
                        ->itemLabel(fn(array $state): ?string => $state['name'] ?? 'Child')->defaultItems(0),

                    Repeater::make('realProperties')
                        ->relationship('realProperties')
                        ->label('Real Properties')
                        ->schema([
                            Grid::make(4)->schema([
                                Forms\Components\Textarea::make('description')->label('Description')->required()->rows(2)->columnSpan(2),
                                Forms\Components\TextInput::make('kind')->label('Kind')->required(),
                                Forms\Components\TextInput::make('exact_location')->label('Location')->required(),
                            ]),
                            Grid::make(4)->schema([
                                Forms\Components\TextInput::make('assessed_value')->label('Assessed Value')->numeric()->prefix('₱')->required(),
                                Forms\Components\TextInput::make('current_fair_market_value')->label('Fair Market Value')->numeric()->prefix('₱')->required(),
                                Forms\Components\TextInput::make('acquisition_year')->label('Year Acquired')->numeric()->minValue(1900)->maxValue(date('Y'))->required(),
                                Forms\Components\TextInput::make('mode_of_acquisition')->label('How Acquired')->required(),
                            ]),
                            Forms\Components\TextInput::make('acquisition_cost')->label('Acquisition Cost')->numeric()->prefix('₱')->required(),
                        ])
                        ->columns(1)->addActionLabel('Add Real Property')->reorderable(false)->collapsible()
                        ->itemLabel(fn(array $state): ?string => $state['description'] ?? 'Property')->defaultItems(0),

                    Repeater::make('personalProperties')
                        ->relationship('personalProperties')
                        ->label('Personal Properties')
                        ->schema([
                            Grid::make(3)->schema([
                                Forms\Components\Textarea::make('description')->label('Description')->required()->rows(2)->columnSpan(2),
                                Forms\Components\TextInput::make('year_acquired')->label('Year Acquired')->numeric()->minValue(1900)->maxValue(date('Y'))->required(),
                            ]),
                            Forms\Components\TextInput::make('acquisition_cost')->label('Acquisition Cost/Amount')->numeric()->prefix('₱')->required(),
                        ])
                        ->columns(1)->addActionLabel('Add Personal Property')->reorderable(false)->collapsible()
                        ->itemLabel(fn(array $state): ?string => $state['description'] ?? 'Property')->defaultItems(0),

                    Repeater::make('liabilities')
                        ->relationship('liabilities')
                        ->label('Liabilities')
                        ->schema([
                            Grid::make(3)->schema([
                                Forms\Components\TextInput::make('nature')->label('Nature')->required(),
                                Forms\Components\TextInput::make('name_of_creditors')->label('Creditor')->required(),
                                Forms\Components\TextInput::make('outstanding_balance')->label('Outstanding Balance')->numeric()->prefix('₱')->required(),
                            ]),
                        ])
                        ->columns(1)->addActionLabel('Add Liability')->reorderable(false)->collapsible()
                        ->itemLabel(fn(array $state): ?string => $state['nature'] ?? 'Liability')->defaultItems(0),

                    Grid::make(2)->schema([
                        Forms\Components\Checkbox::make('has_business_interests')->label('I/We have business interest or financial connection')->live()
                            ->afterStateUpdated(fn($state, callable $set) => $state ? $set('no_business_interests', false) : null),
                        Forms\Components\Checkbox::make('no_business_interests')->label('I/We do not have any business interest or financial connection')->live()
                            ->afterStateUpdated(fn($state, callable $set) => $state ? $set('has_business_interests', false) : null),
                    ]),

                    Repeater::make('businessInterests')
                        ->relationship('businessInterests')
                        ->label('Business Interests')
                        ->schema([
                            Grid::make(2)->schema([
                                Forms\Components\TextInput::make('name_of_entity')->label('Business Name')->required(),
                                Forms\Components\Textarea::make('business_address')->label('Business Address')->required()->rows(2),
                            ]),
                            Grid::make(2)->schema([
                                Forms\Components\TextInput::make('nature_of_business_interest')->label('Nature of Interest')->required(),
                                Forms\Components\DatePicker::make('date_of_acquisition')->label('Date of Acquisition')->required()->native(false),
                            ]),
                        ])
                        ->columns(1)->addActionLabel('Add Business Interest')->reorderable(false)->collapsible()
                        ->visible(fn($get) => $get('has_business_interests'))
                        ->itemLabel(fn(array $state): ?string => $state['name_of_entity'] ?? 'Business')->defaultItems(0),

                    Grid::make(2)->schema([
                        Forms\Components\Checkbox::make('has_relatives_in_government')->label('I have relatives in the government service')->live()
                            ->afterStateUpdated(fn($state, callable $set) => $state ? $set('no_relatives_in_government', false) : null),
                        Forms\Components\Checkbox::make('no_relatives_in_government')->label('I/We do not know of any relative in the government service')->live()
                            ->afterStateUpdated(fn($state, callable $set) => $state ? $set('has_relatives_in_government', false) : null),
                    ]),

                    Repeater::make('relativesInGovernment')
                        ->relationship('relativesInGovernment')
                        ->label('Relatives in Government')
                        ->schema([
                            Grid::make(2)->schema([
                                Forms\Components\TextInput::make('name_of_relative')->label('Name of Relative')->required(),
                                Forms\Components\TextInput::make('relationship')->label('Relationship')->required(),
                            ]),
                            Grid::make(2)->schema([
                                Forms\Components\TextInput::make('position')->label('Position')->required(),
                                Forms\Components\Textarea::make('name_of_agency_office_address')->label('Agency/Office and Address')->required()->rows(2),
                            ]),
                        ])
                        ->columns(1)->addActionLabel('Add Relative')->reorderable(false)->collapsible()
                        ->visible(fn($get) => $get('has_relatives_in_government'))
                        ->itemLabel(fn(array $state): ?string => $state['name_of_relative'] ?? 'Relative')->defaultItems(0),

                    Grid::make(2)->schema([
                        Forms\Components\DatePicker::make('date_signed')->label('Date Signed')->default(now())->native(false),
                        Forms\Components\DatePicker::make('subscribed_sworn_date')->label('Subscribed and Sworn Date')->default(now())->native(false)
                            ->visible(fn() => $isAdmin())->required(fn() => $isAdmin()),
                    ]),

                    Forms\Components\TextInput::make('person_administering_oath')->label('Person Administering Oath')
                        ->visible(fn() => $isAdmin())->required(fn() => $isAdmin())
                        ->disabled(fn() => !$isAdmin())->dehydrated(fn() => $isAdmin()),

                    Grid::make(2)->schema([
                        Forms\Components\FileUpload::make('declarant_id_presented')->label('Declarant Government Issued ID')->image()->directory('saln/declarant-ids')->visibility('private')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])->maxSize(5120)->imageEditor(),
                        Forms\Components\FileUpload::make('spouse_id_presented')->label('Spouse Government Issued ID')->image()->directory('saln/spouse-ids')->visibility('private')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])->maxSize(5120)->imageEditor(),
                    ]),

                    Forms\Components\Hidden::make('total_assets'),
                    Forms\Components\Hidden::make('total_liabilities'),
                    Forms\Components\Hidden::make('net_worth'),
                ])
                ->collapsed()
                ->collapsible()
                ->columnSpanFull(),

        ])->columns(1);
    }

    // =========================================================================
    //  ADMIN REMARKS SECTION BUILDER
    // =========================================================================

    protected static function buildAdminRemarksSection($isAdmin): Section
    {
        return Section::make('Admin Remarks')
            ->description('Administrative notes and comments on this SALN submission')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->schema([
                Forms\Components\Textarea::make('remarks')
                    ->label('Remarks')
                    ->rows(3)
                    ->disabled(fn() => !$isAdmin())
                    ->dehydrated(true)
                    ->placeholder($isAdmin() ? 'Add administrative remarks here...' : 'No remarks from administrator')
                    ->columnSpanFull(),
            ])
            ->visible(fn($record) => $isAdmin() || !blank($record?->remarks))
            ->collapsible()
            ->collapsed(fn($record) => blank($record?->remarks))
            ->compact();
    }

    // =========================================================================
    //  TABLE
    // =========================================================================

    public static function table(Table $table): Table
    {
        $isAdmin = Auth::user()->role === 'admin';

        return $table
            ->columns(self::getTableColumns($isAdmin))
            ->filters(
                self::getEnhancedFilters($isAdmin),
                layout: FiltersLayout::AboveContentCollapsible
            )
            ->filtersFormColumns($isAdmin ? 3 : 2)
            ->filtersFormWidth(\Filament\Support\Enums\MaxWidth::FourExtraLarge)
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->actions(self::getContextualActions($isAdmin))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => $isAdmin),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->striped()
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading('No SALN records found')
            ->emptyStateDescription('File your first SALN to get started.')
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('File SALN')
                    ->icon('heroicon-o-plus')
                    ->visible(fn() => Auth::user()->role === 'employee'),
            ]);
    }

    // =========================================================================
    //  TABLE COLUMNS
    // =========================================================================

    protected static function getTableColumns(bool $isAdmin): array
    {
        return [
            Tables\Columns\TextColumn::make('user.name')
                ->label('Employee')
                ->formatStateUsing(fn($record) => $record->user->first_name . ' ' . $record->user->last_name)
                ->searchable(['user.first_name', 'user.last_name'])
                ->sortable()
                ->weight(FontWeight::Bold)
                ->icon('heroicon-o-user-circle')
                ->iconColor('primary')
                ->visible($isAdmin),

            Tables\Columns\TextColumn::make('as_of_date')
                ->label('As of')
                ->date('F d, Y')
                ->sortable()
                ->icon('heroicon-o-calendar-days')
                ->iconColor('info'),

            Tables\Columns\TextColumn::make('total_assets')
                ->label('Total Assets')
                ->money('PHP')
                ->sortable()
                ->color('success')
                ->icon('heroicon-o-building-office')
                ->iconColor('success'),

            Tables\Columns\TextColumn::make('total_liabilities')
                ->label('Liabilities')
                ->money('PHP')
                ->sortable()
                ->color('danger')
                ->icon('heroicon-o-credit-card')
                ->iconColor('danger'),

            Tables\Columns\TextColumn::make('net_worth')
                ->label('Net Worth')
                ->money('PHP')
                ->sortable()
                ->badge()
                ->color(fn($state) => ($state ?? 0) >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-banknotes'),

            // -----------------------------------------------------------------------
            //  "Filed" column — reads created_at (stamped on every resubmission).
            //  Shows "X ago" with the exact datetime on hover.
            // -----------------------------------------------------------------------
            Tables\Columns\TextColumn::make('created_at')
                ->label('Filed')
                ->since()
                ->sortable()
                ->tooltip(fn($record) => $record->created_at->format('M d, Y h:i A'))
                ->color('gray')
                ->icon('heroicon-o-paper-airplane')
                ->iconColor('gray'),

            // -----------------------------------------------------------------------
            //  "Resubmitted" column — null until the employee edits and saves.
            //  Admins can see at a glance if/when a SALN was updated after filing.
            //  Shows a yellow "RESUBMITTED" badge + the timestamp.
            // -----------------------------------------------------------------------
            Tables\Columns\TextColumn::make('resubmitted_at')
                ->label('Resubmitted')
                ->sortable()
                // formatStateUsing receives the raw Carbon|null value.
                // ->since() + ->badge() together cause the badge to render even for null;
                // we handle both cases here so null shows a plain dash (no badge).
                ->formatStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->diffForHumans() : null)
                ->placeholder('—')
                ->tooltip(fn($record) => $record->resubmitted_at?->format('M d, Y h:i A'))
                ->badge()
                ->color(fn($state) => $state ? 'warning' : 'gray')
                ->icon(fn($state) => $state ? 'heroicon-o-arrow-path' : null)
                ->iconColor('warning')
                ->visible($isAdmin),

            Tables\Columns\TextColumn::make('remarks')
                ->label('Remarks')
                ->limit(50)
                ->wrap()
                ->placeholder('—')
                ->color(fn($record) => filled($record?->remarks) ? 'warning' : 'gray')
                ->icon(fn($record) => filled($record?->remarks) ? 'heroicon-o-chat-bubble-left-ellipsis' : null)
                ->iconColor('warning')
                ->tooltip(fn($record) => $record?->remarks)
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    // =========================================================================
    //  FILTERS
    // =========================================================================

    protected static function getEnhancedFilters(bool $isAdmin): array
    {
        $filters = [];

        if ($isAdmin) {
            $filters[] = Tables\Filters\Filter::make('employee_and_remarks')
                ->label('Employee & Remarks')
                ->columnSpan(1)
                ->form([
                    \Filament\Forms\Components\Select::make('user_id')
                        ->label('Employee')
                        ->options(
                            fn() => User::where('role', 'employee')
                                ->orderBy('first_name')
                                ->get()
                                ->mapWithKeys(fn($u) => [$u->id => $u->first_name . ' ' . $u->last_name])
                                ->toArray()
                        )
                        ->searchable()
                        ->native(false)
                        ->placeholder('All employees'),

                    \Filament\Forms\Components\Select::make('has_remarks')
                        ->label('Admin Remarks')
                        ->native(false)
                        ->placeholder('All records')
                        ->options([
                            'with' => 'With remarks',
                            'without' => 'Without remarks',
                        ]),

                    // -------------------------------------------------------
                    //  New: filter by resubmission status
                    // -------------------------------------------------------
                    \Filament\Forms\Components\Select::make('resubmitted')
                        ->label('Resubmission')
                        ->native(false)
                        ->placeholder('All records')
                        ->options([
                            'yes' => '🔄 Resubmitted',
                            'no' => '📄 Original only',
                        ]),
                ])
                ->query(
                    fn(Builder $query, array $data) => $query
                        ->when($data['user_id'] ?? null, fn($q, $v) => $q->where('user_id', $v))
                        ->when(($data['has_remarks'] ?? null) === 'with', fn($q) => $q->whereNotNull('remarks')->where('remarks', '!=', ''))
                        ->when(($data['has_remarks'] ?? null) === 'without', fn($q) => $q->where(fn($q2) => $q2->whereNull('remarks')->orWhere('remarks', '')))
                        ->when(($data['resubmitted'] ?? null) === 'yes', fn($q) => $q->whereNotNull('resubmitted_at'))
                        ->when(($data['resubmitted'] ?? null) === 'no', fn($q) => $q->whereNull('resubmitted_at'))
                )
                ->indicateUsing(function (array $data) use ($isAdmin): array {
                    $indicators = [];
                    if ($data['user_id'] ?? null) {
                        $name = User::find($data['user_id']);
                        $label = $name ? $name->first_name . ' ' . $name->last_name : 'Employee';
                        $indicators[] = Tables\Filters\Indicator::make('Employee: ' . $label)->removeField('user_id');
                    }
                    if ($data['has_remarks'] ?? null) {
                        $label = $data['has_remarks'] === 'with' ? 'With remarks' : 'Without remarks';
                        $indicators[] = Tables\Filters\Indicator::make('Remarks: ' . $label)->removeField('has_remarks');
                    }
                    if ($data['resubmitted'] ?? null) {
                        $label = $data['resubmitted'] === 'yes' ? 'Resubmitted' : 'Original only';
                        $indicators[] = Tables\Filters\Indicator::make('Resubmission: ' . $label)->removeField('resubmitted');
                    }
                    return $indicators;
                });
        }

        $filters[] = Tables\Filters\Filter::make('year_and_period')
            ->label('Year & Period')
            ->columnSpan(1)
            ->form([
                \Filament\Forms\Components\Select::make('year')
                    ->label('Year')
                    ->options(
                        fn() => Saln::selectRaw('YEAR(as_of_date) as year')
                            ->distinct()
                            ->orderByDesc('year')
                            ->pluck('year', 'year')
                            ->toArray()
                    )
                    ->native(false)
                    ->placeholder('All years'),

                \Filament\Forms\Components\Select::make('preset')
                    ->label('Quick Select')
                    ->placeholder('— pick a period —')
                    ->native(false)
                    ->options([
                        'this_month' => '📅  This Month',
                        'last_month' => '📅  Last Month',
                        'this_year' => '📅  This Year',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        [$from, $to] = match ($state) {
                            'this_month' => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
                            'last_month' => [now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()],
                            'this_year' => [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()],
                            default => [null, null],
                        };
                        $set('from', $from);
                        $set('to', $to);
                    }),

                \Filament\Forms\Components\Grid::make(2)->schema([
                    \Filament\Forms\Components\DatePicker::make('from')->label('From')->native(false)->displayFormat('M d, Y'),
                    \Filament\Forms\Components\DatePicker::make('to')->label('To')->native(false)->displayFormat('M d, Y'),
                ]),
            ])
            ->query(
                fn(Builder $query, array $data) => $query
                    ->when($data['year'] ?? null, fn($q, $v) => $q->whereYear('as_of_date', $v))
                    ->when($data['from'] ?? null, fn($q, $d) => $q->whereDate('as_of_date', '>=', $d))
                    ->when($data['to'] ?? null, fn($q, $d) => $q->whereDate('as_of_date', '<=', $d))
            )
            ->indicateUsing(function (array $data): array {
                $presetLabels = ['this_month' => 'This Month', 'last_month' => 'Last Month', 'this_year' => 'This Year'];
                $indicators = [];
                if ($data['year'] ?? null) {
                    $indicators[] = Tables\Filters\Indicator::make('Year: ' . $data['year'])->removeField('year');
                }
                $preset = $data['preset'] ?? null;
                if (($data['from'] ?? null) || ($data['to'] ?? null)) {
                    if ($preset && isset($presetLabels[$preset])) {
                        $indicators[] = Tables\Filters\Indicator::make('Period: ' . $presetLabels[$preset])->removeField('preset');
                    } else {
                        if ($data['from'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('From: ' . Carbon::parse($data['from'])->format('M d, Y'))->removeField('from');
                        }
                        if ($data['to'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('To: ' . Carbon::parse($data['to'])->format('M d, Y'))->removeField('to');
                        }
                    }
                }
                return $indicators;
            });

        $filters[] = Tables\Filters\Filter::make('net_worth_range')
            ->label('Net Worth Range')
            ->columnSpan(1)
            ->form([
                \Filament\Forms\Components\TextInput::make('min_net_worth')->label('Minimum Net Worth')->numeric()->prefix('₱'),
                \Filament\Forms\Components\TextInput::make('max_net_worth')->label('Maximum Net Worth')->numeric()->prefix('₱'),
            ])
            ->query(
                fn(Builder $query, array $data) => $query
                    ->when($data['min_net_worth'] ?? null, fn($q, $v) => $q->where('net_worth', '>=', $v))
                    ->when($data['max_net_worth'] ?? null, fn($q, $v) => $q->where('net_worth', '<=', $v))
            )
            ->indicateUsing(function (array $data): array {
                $indicators = [];
                if ($data['min_net_worth'] ?? null) {
                    $indicators[] = Tables\Filters\Indicator::make('Min Net Worth: ₱' . number_format($data['min_net_worth']))->removeField('min_net_worth');
                }
                if ($data['max_net_worth'] ?? null) {
                    $indicators[] = Tables\Filters\Indicator::make('Max Net Worth: ₱' . number_format($data['max_net_worth']))->removeField('max_net_worth');
                }
                return $indicators;
            });

        return $filters;
    }

    // =========================================================================
    //  CONTEXTUAL ACTIONS
    // =========================================================================

    protected static function getContextualActions(bool $isAdmin): array
    {
        return [
            Tables\Actions\ActionGroup::make([

                Tables\Actions\ViewAction::make()
                    ->label('View SALN')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn() => $isAdmin),

                Tables\Actions\Action::make('print')
                    ->label('Print SALN')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn(Saln $record): string => route('saln.print', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('remarks')
                    ->label(fn($record) => blank($record->remarks) ? 'Add Remarks' : 'Edit Remarks')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('warning')
                    ->visible(fn() => $isAdmin)
                    ->fillForm(fn($record) => ['remarks' => $record->remarks])
                    ->form([
                        Forms\Components\Textarea::make('remarks')
                            ->label('Admin Remarks')
                            ->rows(4)
                            ->required()
                            ->placeholder('Enter administrative remarks or comments...'),
                    ])
                    ->action(function (Saln $record, array $data) {
                        $record->update(['remarks' => $data['remarks']]);
                        $record->user?->notify(new SalnRemarksAdded($record));
                        Notification::make()
                            ->title('Remarks Updated')
                            ->body('Administrative remarks have been saved and the employee has been notified.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make()
                    ->label('Edit SALN')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(
                        fn(Saln $record) =>
                        Auth::user()->role === 'employee' &&
                        $record->user_id === Auth::id()
                    ),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->visible(fn() => $isAdmin),

            ])
                ->label('Actions')
                ->icon('heroicon-o-ellipsis-vertical')
                ->size(\Filament\Support\Enums\ActionSize::Small)
                ->color('gray')
                ->button(),
        ];
    }

    // =========================================================================
    //  QUERY / NAVIGATION
    // =========================================================================

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalns::route('/'),
            'create' => Pages\CreateSaln::route('/create'),
            'view' => Pages\ViewSaln::route('/{record}'),
            'edit' => Pages\EditSaln::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['user', 'children', 'realProperties', 'personalProperties', 'liabilities', 'businessInterests', 'relativesInGovernment']);

        if (Auth::user()?->role !== 'admin') {
            $query->where('user_id', Auth::id());
        }
        return $query;
    }

    // -----------------------------------------------------------------------
    //  Navigation badge — count of SALNs resubmitted but not yet remarked,
    //  PLUS original submissions with no remarks (unreviewed).
    //  This gives admins a single number representing "needs attention".
    // -----------------------------------------------------------------------
    public static function getNavigationBadge(): ?string
    {
        if (Auth::user()?->role !== 'admin')
            return null;

        // Unreviewed = no remarks AND (original or resubmitted)
        $count = Saln::whereNull('remarks')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        if (Auth::user()?->role !== 'admin')
            return null;
        return Saln::whereNull('remarks')->count() > 0 ? 'warning' : 'success';
    }
}
