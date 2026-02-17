<?php

namespace App\Filament\Resources;

use App\Models\Saln;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
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

    /* ============================================================
       FORM DEFINITION
       ============================================================ */

    public static function form(Form $form): Form
    {
        $isAdmin = fn() => Auth::user()?->role === 'admin';

        return $form->schema([

            // Admin Remarks (visible at top when present)
            self::buildAdminRemarksSection($isAdmin),

            // ============================================================
            // CUSTOM SALN LAYOUT VIEW - THE ONLY VISIBLE FORM
            // ============================================================
            ViewComponent::make('filament.resources.saln-resource.saln-form')
                ->columnSpanFull(),

            // ============================================================
            // ALL FORM FIELDS - COLLAPSED (for data binding & validation)
            // Fields must NOT be hidden - just collapsed so the blade
            // view can render them via $this->form->getComponent()
            // ============================================================
            Section::make('Form Fields (For Validation Only)')
                ->description('⚠️ Please fill out the official SALN form above. These fields are for data binding.')
                ->schema([

                    // ---- FILING TYPE ----
                    Forms\Components\DatePicker::make('as_of_date')
                        ->label('As of Date')
                        ->required()
                        ->default(now())
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

                    // ---- DECLARANT INFORMATION ----
                    Grid::make(4)->schema([
                        Forms\Components\TextInput::make('declarant_family_name')
                            ->label('Family Name')
                            ->required()
                            ->default(fn() => Auth::user()?->last_name),
                        Forms\Components\TextInput::make('declarant_first_name')
                            ->label('First Name')
                            ->required()
                            ->default(fn() => Auth::user()?->first_name)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('declarant_middle_initial')
                            ->label('M.I.')
                            ->maxLength(5)
                            ->default(fn() => substr(Auth::user()?->middle_name ?? '', 0, 1)),
                    ]),

                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('declarant_position')
                            ->label('Position')
                            ->required()
                            ->default(fn() => Auth::user()?->position),
                        Forms\Components\TextInput::make('declarant_agency_office')
                            ->label('Agency/Office')
                            ->required()
                            ->default(fn() => Auth::user()?->department),
                    ]),

                    Forms\Components\Textarea::make('declarant_office_address')
                        ->label('Office Address')
                        ->required()
                        ->rows(2)
                        ->default(fn() => implode(', ', array_filter([
                            Auth::user()?->purok_street,
                            Auth::user()?->city_municipality,
                            Auth::user()?->province,
                        ]))),

                    // ---- SPOUSE INFORMATION ----
                    Grid::make(4)->schema([
                        Forms\Components\TextInput::make('spouse_family_name')->label('Spouse Family Name'),
                        Forms\Components\TextInput::make('spouse_first_name')->label('Spouse First Name')->columnSpan(2),
                        Forms\Components\TextInput::make('spouse_middle_initial')->label('M.I.')->maxLength(5),
                    ]),

                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('spouse_position')->label('Spouse Position'),
                        Forms\Components\TextInput::make('spouse_agency_office')->label('Spouse Agency/Office'),
                    ]),

                    Forms\Components\Textarea::make('spouse_office_address')
                        ->label('Spouse Office Address')
                        ->rows(2),

                    // ---- CHILDREN ----
                    Repeater::make('children')
                        ->relationship('children')
                        ->label('Children')
                        ->schema([
                            Grid::make(3)->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\DatePicker::make('date_of_birth')
                                    ->label('Date of Birth')
                                    ->required()
                                    ->native(false)
                                    ->maxDate(now())
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $set('age', (int) Carbon::parse($state)->age);
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
                        ->columns(1)
                        ->addActionLabel('Add Child')
                        ->reorderable(false)
                        ->collapsible()
                        ->itemLabel(fn(array $state): ?string => $state['name'] ?? 'Child')
                        ->defaultItems(0),

                    // ---- REAL PROPERTIES ----
                    Repeater::make('realProperties')
                        ->relationship('realProperties')
                        ->label('Real Properties')
                        ->schema([
                            Grid::make(4)->schema([
                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->required()
                                    ->rows(2)
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('kind')
                                    ->label('Kind')
                                    ->required(),
                                Forms\Components\TextInput::make('exact_location')
                                    ->label('Location')
                                    ->required(),
                            ]),
                            Grid::make(4)->schema([
                                Forms\Components\TextInput::make('assessed_value')
                                    ->label('Assessed Value')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->required(),
                                Forms\Components\TextInput::make('current_fair_market_value')
                                    ->label('Fair Market Value')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->required(),
                                Forms\Components\TextInput::make('acquisition_year')
                                    ->label('Year Acquired')
                                    ->numeric()
                                    ->minValue(1900)
                                    ->maxValue(date('Y'))
                                    ->required(),
                                Forms\Components\TextInput::make('mode_of_acquisition')
                                    ->label('How Acquired')
                                    ->required(),
                            ]),
                            Forms\Components\TextInput::make('acquisition_cost')
                                ->label('Acquisition Cost')
                                ->numeric()
                                ->prefix('₱')
                                ->required(),
                        ])
                        ->columns(1)
                        ->addActionLabel('Add Real Property')
                        ->reorderable(false)
                        ->collapsible()
                        ->itemLabel(fn(array $state): ?string => $state['description'] ?? 'Property')
                        ->defaultItems(0),

                    // ---- PERSONAL PROPERTIES ----
                    Repeater::make('personalProperties')
                        ->relationship('personalProperties')
                        ->label('Personal Properties')
                        ->schema([
                            Grid::make(3)->schema([
                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->required()
                                    ->rows(2)
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('year_acquired')
                                    ->label('Year Acquired')
                                    ->numeric()
                                    ->minValue(1900)
                                    ->maxValue(date('Y'))
                                    ->required(),
                            ]),
                            Forms\Components\TextInput::make('acquisition_cost')
                                ->label('Acquisition Cost/Amount')
                                ->numeric()
                                ->prefix('₱')
                                ->required(),
                        ])
                        ->columns(1)
                        ->addActionLabel('Add Personal Property')
                        ->reorderable(false)
                        ->collapsible()
                        ->itemLabel(fn(array $state): ?string => $state['description'] ?? 'Property')
                        ->defaultItems(0),

                    // ---- LIABILITIES ----
                    Repeater::make('liabilities')
                        ->relationship('liabilities')
                        ->label('Liabilities')
                        ->schema([
                            Grid::make(3)->schema([
                                Forms\Components\TextInput::make('nature')
                                    ->label('Nature')
                                    ->required(),
                                Forms\Components\TextInput::make('name_of_creditors')
                                    ->label('Creditor')
                                    ->required(),
                                Forms\Components\TextInput::make('outstanding_balance')
                                    ->label('Outstanding Balance')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->required(),
                            ]),
                        ])
                        ->columns(1)
                        ->addActionLabel('Add Liability')
                        ->reorderable(false)
                        ->collapsible()
                        ->itemLabel(fn(array $state): ?string => $state['nature'] ?? 'Liability')
                        ->defaultItems(0),

                    // ---- BUSINESS INTERESTS ----
                    Grid::make(2)->schema([
                        Forms\Components\Checkbox::make('has_business_interests')
                            ->label('I/We have business interest or financial connection')
                            ->live()
                            ->afterStateUpdated(fn($state, callable $set) =>
                                $state ? $set('no_business_interests', false) : null),
                        Forms\Components\Checkbox::make('no_business_interests')
                            ->label('I/We do not have any business interest or financial connection')
                            ->live()
                            ->afterStateUpdated(fn($state, callable $set) =>
                                $state ? $set('has_business_interests', false) : null),
                    ]),

                    Repeater::make('businessInterests')
                        ->relationship('businessInterests')
                        ->label('Business Interests')
                        ->schema([
                            Grid::make(2)->schema([
                                Forms\Components\TextInput::make('name_of_entity')
                                    ->label('Business Name')
                                    ->required(),
                                Forms\Components\Textarea::make('business_address')
                                    ->label('Business Address')
                                    ->required()
                                    ->rows(2),
                            ]),
                            Grid::make(2)->schema([
                                Forms\Components\TextInput::make('nature_of_business_interest')
                                    ->label('Nature of Interest')
                                    ->required(),
                                Forms\Components\DatePicker::make('date_of_acquisition')
                                    ->label('Date of Acquisition')
                                    ->required()
                                    ->native(false),
                            ]),
                        ])
                        ->columns(1)
                        ->addActionLabel('Add Business Interest')
                        ->reorderable(false)
                        ->collapsible()
                        ->visible(fn($get) => $get('has_business_interests'))
                        ->itemLabel(fn(array $state): ?string => $state['name_of_entity'] ?? 'Business')
                        ->defaultItems(0),

                    // ---- RELATIVES IN GOVERNMENT ----
                    Grid::make(2)->schema([
                        Forms\Components\Checkbox::make('has_relatives_in_government')
                            ->label('I have relatives in the government service')
                            ->live()
                            ->afterStateUpdated(fn($state, callable $set) =>
                                $state ? $set('no_relatives_in_government', false) : null),
                        Forms\Components\Checkbox::make('no_relatives_in_government')
                            ->label('I/We do not know of any relative in the government service')
                            ->live()
                            ->afterStateUpdated(fn($state, callable $set) =>
                                $state ? $set('has_relatives_in_government', false) : null),
                    ]),

                    Repeater::make('relativesInGovernment')
                        ->relationship('relativesInGovernment')
                        ->label('Relatives in Government')
                        ->schema([
                            Grid::make(2)->schema([
                                Forms\Components\TextInput::make('name_of_relative')
                                    ->label('Name of Relative')
                                    ->required(),
                                Forms\Components\TextInput::make('relationship')
                                    ->label('Relationship')
                                    ->required(),
                            ]),
                            Grid::make(2)->schema([
                                Forms\Components\TextInput::make('position')
                                    ->label('Position')
                                    ->required(),
                                Forms\Components\Textarea::make('name_of_agency_office_address')
                                    ->label('Agency/Office and Address')
                                    ->required()
                                    ->rows(2),
                            ]),
                        ])
                        ->columns(1)
                        ->addActionLabel('Add Relative')
                        ->reorderable(false)
                        ->collapsible()
                        ->visible(fn($get) => $get('has_relatives_in_government'))
                        ->itemLabel(fn(array $state): ?string => $state['name_of_relative'] ?? 'Relative')
                        ->defaultItems(0),

                    // ---- DECLARATION ----
                    Grid::make(2)->schema([
                        Forms\Components\DatePicker::make('date_signed')
                            ->label('Date Signed')
                            // ->required()
                            ->default(now())
                            ->native(false),
                        Forms\Components\DatePicker::make('subscribed_sworn_date')
                            ->label('Subscribed and Sworn Date')
                            ->default(now())
                            ->native(false)
                            ->visible(fn() => $isAdmin())
                            ->required(fn() => $isAdmin()),
                    ]),

                    Forms\Components\TextInput::make('person_administering_oath')
                        ->label('Person Administering Oath')
                        ->visible(fn() => $isAdmin())
                        ->required(fn() => $isAdmin())
                        ->disabled(fn() => !$isAdmin())
                        ->dehydrated(fn() => $isAdmin()),

                    Grid::make(2)->schema([
                        Forms\Components\FileUpload::make('declarant_id_presented')
                            ->label('Declarant Government Issued ID')
                            ->image()
                            ->directory('saln/declarant-ids')
                            ->visibility('private')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                            ->maxSize(5120)
                            ->imageEditor(),
                        Forms\Components\FileUpload::make('spouse_id_presented')
                            ->label('Spouse Government Issued ID')
                            ->image()
                            ->directory('saln/spouse-ids')
                            ->visibility('private')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                            ->maxSize(5120)
                            ->imageEditor(),
                    ]),

                    // Hidden calculated fields
                    Forms\Components\Hidden::make('total_assets'),
                    Forms\Components\Hidden::make('total_liabilities'),
                    Forms\Components\Hidden::make('net_worth'),

                ])
                ->collapsed()
                ->collapsible()
                ->columnSpanFull(),

        ])->columns(1);
    }

    /* ============================================================
       ADMIN REMARKS SECTION BUILDER
       ============================================================ */

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

    /* ============================================================
       TABLE DEFINITION
       ============================================================ */

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::getFinancialCardColumns())
            ->filters(self::getEnhancedFilters())
            ->actions(self::getContextualActions())
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()?->role === 'admin'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->striped()
            ->emptyStateHeading('No SALN records found')
            ->emptyStateDescription('File your first SALN to get started.')
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('File SALN')
                    ->icon('heroicon-o-plus'),
            ]);
    }

    /* ============================================================
       TABLE COLUMNS
       ============================================================ */

    protected static function getFinancialCardColumns(): array
    {
        return [
            Tables\Columns\Layout\Stack::make([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Employee')
                    ->formatStateUsing(fn($record) => $record->user->first_name . ' ' . $record->user->last_name)
                    ->searchable(['user.first_name', 'user.last_name'])
                    ->weight('bold')
                    ->size('lg')
                    ->icon('heroicon-o-user-circle')
                    ->iconColor('primary'),

                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('as_of_date')
                        ->label('As of')
                        ->date('F d, Y')
                        ->icon('heroicon-o-calendar')
                        ->iconColor('gray')
                        ->size('sm')
                        ->color('gray'),

                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Filed')
                        ->dateTime('M d, Y')
                        ->icon('heroicon-o-clock')
                        ->iconColor('gray')
                        ->size('sm')
                        ->color('gray'),
                ]),
            ])->space(1),

            Tables\Columns\Layout\Split::make([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('total_assets')
                        ->label('Total Assets')
                        ->money('PHP')
                        ->weight('medium')
                        ->icon('heroicon-o-building-office')
                        ->iconColor('success')
                        ->color('success'),

                    Tables\Columns\TextColumn::make('total_liabilities')
                        ->label('Total Liabilities')
                        ->money('PHP')
                        ->size('sm')
                        ->icon('heroicon-o-credit-card')
                        ->iconColor('danger')
                        ->color('danger'),
                ])->space(1),

                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('net_worth')
                        ->label('Net Worth')
                        ->money('PHP')
                        ->weight('bold')
                        ->size('lg')
                        ->icon('heroicon-o-banknotes')
                        ->color(fn($state) => $state >= 0 ? 'success' : 'danger')
                        ->badge(),
                ])->space(1)->alignment('end'),
            ])->from('md'),

            Tables\Columns\Layout\Panel::make([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\IconColumn::make('has_remarks')
                        ->label('Remarks')
                        ->boolean()
                        ->getStateUsing(fn($record) => !blank($record->remarks))
                        ->trueIcon('heroicon-o-chat-bubble-left-right')
                        ->falseIcon('heroicon-o-check-circle')
                        ->trueColor('warning')
                        ->falseColor('success')
                        ->size('lg'),

                    Tables\Columns\TextColumn::make('remarks')
                        ->label('Admin Remarks')
                        ->limit(100)
                        ->wrap()
                        ->default('No remarks')
                        ->color(fn($record) => blank($record->remarks) ? 'success' : 'warning')
                        ->icon(fn($record) => blank($record->remarks) ? 'heroicon-o-check' : 'heroicon-o-exclamation-triangle'),
                ]),
            ])
                ->collapsible()
                ->collapsed(fn($record) => blank($record->remarks)),
        ];
    }

    /* ============================================================
       FILTERS
       ============================================================ */

    protected static function getEnhancedFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('year')
                ->label('Year')
                ->options(fn() => Saln::selectRaw('YEAR(as_of_date) as year')
                    ->distinct()
                    ->orderByDesc('year')
                    ->pluck('year', 'year'))
                ->query(fn(Builder $query, array $data) =>
                    isset($data['value']) ? $query->whereYear('as_of_date', $data['value']) : $query)
                ->native(false)
                ->indicator('Year'),

            Tables\Filters\SelectFilter::make('user_id')
                ->label('Employee')
                ->relationship('user', 'first_name')
                ->getOptionLabelFromRecordUsing(fn($record) => $record->first_name . ' ' . $record->last_name)
                ->visible(fn() => Auth::user()?->role === 'admin')
                ->searchable()
                ->preload()
                ->native(false)
                ->indicator('Employee'),

            Tables\Filters\Filter::make('as_of_date')
                ->form([
                    Forms\Components\DatePicker::make('from')->label('From Date')->native(false),
                    Forms\Components\DatePicker::make('until')->label('Until Date')->native(false),
                ])
                ->query(fn(Builder $query, array $data) => $query
                    ->when($data['from'], fn($q, $date) => $q->whereDate('as_of_date', '>=', $date))
                    ->when($data['until'], fn($q, $date) => $q->whereDate('as_of_date', '<=', $date)))
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
                    if ($data['from'] ?? null) {
                        $indicators['from'] = 'From ' . Carbon::parse($data['from'])->toFormattedDateString();
                    }
                    if ($data['until'] ?? null) {
                        $indicators['until'] = 'Until ' . Carbon::parse($data['until'])->toFormattedDateString();
                    }
                    return $indicators;
                }),

            Tables\Filters\TernaryFilter::make('has_remarks')
                ->label('Has Admin Remarks')
                ->queries(
                    true: fn(Builder $query) => $query->whereNotNull('remarks'),
                    false: fn(Builder $query) => $query->whereNull('remarks'),
                )
                ->visible(fn() => Auth::user()?->role === 'admin')
                ->indicator('Remarks'),

            Tables\Filters\Filter::make('net_worth')
                ->form([
                    Forms\Components\TextInput::make('min_net_worth')->label('Minimum Net Worth')->numeric()->prefix('₱'),
                    Forms\Components\TextInput::make('max_net_worth')->label('Maximum Net Worth')->numeric()->prefix('₱'),
                ])
                ->query(fn(Builder $query, array $data) => $query
                    ->when($data['min_net_worth'], fn($q, $amount) => $q->where('net_worth', '>=', $amount))
                    ->when($data['max_net_worth'], fn($q, $amount) => $q->where('net_worth', '<=', $amount)))
                ->visible(fn() => Auth::user()?->role === 'admin'),
        ];
    }

    /* ============================================================
       ACTIONS
       ============================================================ */

    protected static function getContextualActions(): array
    {
        return [
            Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info'),

                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn() => Auth::user()?->role !== 'admin'),

                Tables\Actions\Action::make('print')
                    ->label('Print SALN')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn(Saln $record): string => route('saln.print', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('remarks')
                    ->label('Add/Edit Remarks')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('warning')
                    ->form([
                        Forms\Components\Textarea::make('remarks')
                            ->label('Admin Remarks')
                            ->rows(4)
                            ->required()
                            ->placeholder('Enter administrative remarks or comments...'),
                    ])
                    ->action(function (Saln $record, array $data) {
                        $record->update(['remarks' => $data['remarks']]);

                        // Notify the SALN owner — stored in their bell
                        $record->user->notify(new \App\Notifications\SalnRemarksAdded($record));

                        // Flash confirmation for the admin
                        Notification::make()
                            ->title('Remarks Updated')
                            ->body('Administrative remarks have been saved and the employee has been notified.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn() => Auth::user()?->role === 'admin'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->visible(fn() => Auth::user()?->role === 'admin'),
            ])
                ->label('Actions')
                ->icon('heroicon-o-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button(),
        ];
    }

    /* ============================================================
       RESOURCE CONFIGURATION
       ============================================================ */

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
            ->with([
                'user',
                'children',
                'realProperties',
                'personalProperties',
                'liabilities',
                'businessInterests',
                'relativesInGovernment',
            ]);

        if (Auth::user()?->role !== 'admin') {
            $query->where('user_id', Auth::id());
        }

        return $query;
    }

    public static function getNavigationBadge(): ?string
    {
        if (Auth::user()?->role !== 'admin') {
            return null;
        }

        $count = Saln::whereNull('remarks')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        if (Auth::user()?->role !== 'admin') {
            return null;
        }

        $count = Saln::whereNull('remarks')->count();
        return $count > 0 ? 'warning' : 'success';
    }
}
