<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TravelOrderResource\Pages;
use App\Models\TravelOrder;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TravelOrderResource extends Resource
{
    protected static ?string $model = TravelOrder::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $slug = 'travel-orders';
    protected static ?string $navigationLabel = 'Travel Orders';
    protected static ?string $modelLabel = 'Travel Order';
    protected static ?string $pluralModelLabel = 'Travel Orders';
    protected static ?string $navigationGroup = 'Documents';
    protected static ?int $navigationSort = 4;

    // =========================================================================
    //  ACCESS CONTROL — Hide entirely from Job Order users
    // =========================================================================

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->role !== User::ROLE_JOB_ORDER;
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->role !== User::ROLE_JOB_ORDER;
    }

    // =========================================================================
    //  AUTHORIZATION
    // =========================================================================

    public static function canCreate(): bool
    {
        return Auth::user()->role === User::ROLE_REGULAR;
    }

    public static function canEdit($record): bool
    {
        $user = Auth::user();
        if ($user->role === 'admin')
            return $record->status === 'pending';
        return $record->created_by === $user->id && $record->status === 'rejected';
    }

    public static function canDelete($record): bool
    {
        $user = Auth::user();
        if ($user->role === 'admin')
            return true;
        return $record->created_by === $user->id && $record->status === 'pending';
    }

    // =========================================================================
    //  FORM
    // =========================================================================

    public static function form(Form $form): Form
    {
        return $form->schema([
            self::buildInfoSection(),
            self::buildTravelerSection(),
            self::buildItinerarySection(),
            self::buildBatchTravelersSection(),
            self::buildApprovalSection(),
        ]);
    }

    // ── Form Sections ─────────────────────────────────────────────────────────

    protected static function buildInfoSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Travel Order Information')
            ->description('Basic information about this travel order')
            ->icon('heroicon-o-document-text')
            ->schema([
                Forms\Components\TextInput::make('travel_order_no')
                    ->label('Travel Order No.')
                    ->placeholder('Auto-generated')
                    ->disabled()
                    ->prefixIcon('heroicon-o-hashtag')
                    ->helperText('Will be generated automatically upon creation')
                    ->columnSpan(1),

                Forms\Components\DatePicker::make('date')
                    ->label('Date Issued')
                    ->required()
                    ->default(now())
                    ->native(false)
                    ->displayFormat('M j, Y')
                    ->columnSpan(1),

                Forms\Components\Select::make('status')
                    ->label('Current Status')
                    ->options([
                        'pending' => 'Pending Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->disabled(fn() => Auth::user()->role === User::ROLE_REGULAR)
                    ->native(false)
                    ->prefixIcon('heroicon-o-information-circle')
                    ->columnSpan(1),

                Forms\Components\Radio::make('travel_type')
                    ->label('Travel Type')
                    ->options([
                        'solo' => 'Solo Travel - Single Employee',
                        'batch' => 'Batch Travel - Multiple Employees',
                    ])
                    ->descriptions([
                        'solo' => 'Travel order for one employee only',
                        'batch' => 'Travel order for multiple employees',
                    ])
                    ->default('solo')
                    ->required()
                    ->live()
                    ->columnSpanFull(),
            ])
            ->columns(3)
            ->collapsible()
            ->collapsed(false);
    }

    protected static function buildTravelerSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Traveler Information')
            ->description('Your personal travel details')
            ->icon('heroicon-o-user-circle')
            ->schema([
                Forms\Components\Placeholder::make('employee_name_display')
                    ->label('Employee Name')
                    ->content(fn() => auth()->user()->name)
                    ->columnSpan(1),

                Forms\Components\Placeholder::make('position_display')
                    ->label('Position')
                    ->content(fn() => auth()->user()->position ?? '—')
                    ->columnSpan(1),

                Forms\Components\Hidden::make('created_by')
                    ->default(fn() => auth()->id()),

                Forms\Components\Hidden::make('name')
                    ->default(fn() => auth()->user()->name),

                Forms\Components\Hidden::make('position')
                    ->default(fn() => auth()->user()->position),

                Forms\Components\TextInput::make('station')
                    ->label('Work Station/Office')
                    ->placeholder('e.g., Main Office, Branch Office')
                    ->required()
                    ->prefixIcon('heroicon-o-building-office')
                    ->columnSpan(1),

                Forms\Components\TextInput::make('salary_per_annum')
                    ->label('Annual Salary')
                    ->numeric()
                    ->prefix('₱')
                    ->placeholder('0.00')
                    ->helperText('Enter your annual salary')
                    ->columnSpan(1),
            ])
            ->columns(2)
            ->collapsible()
            ->collapsed(false);
    }

    protected static function buildItinerarySection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Travel Itinerary & Purpose')
            ->description('Specify travel dates, destination, and purpose')
            ->icon('heroicon-o-calendar-days')
            ->schema([
                Forms\Components\DatePicker::make('departure_date')
                    ->label('Departure Date')
                    ->required()
                    ->native(false)
                    ->displayFormat('M d, Y')
                    ->columnSpan(1),

                Forms\Components\DatePicker::make('return_date')
                    ->label('Return Date')
                    ->required()
                    ->native(false)
                    ->displayFormat('M d, Y')
                    ->minDate(fn(callable $get) => $get('departure_date'))
                    ->columnSpan(1),

                Forms\Components\TextInput::make('destination')
                    ->label('Destination')
                    ->placeholder('e.g., Manila City, Cebu Province')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(1),

                Forms\Components\TextInput::make('report_to')
                    ->label('Report To (Authority/Office)')
                    ->placeholder('e.g., Regional Director, Main Office')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(1),

                Forms\Components\Textarea::make('purpose_of_trip')
                    ->label('Purpose of Travel')
                    ->placeholder('Provide detailed information about the purpose, activities, and expected outcomes of this travel...')
                    ->helperText('Be specific and comprehensive in describing the travel purpose')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
            ])
            ->columns(2)
            ->collapsible()
            ->collapsed(false);
    }

    protected static function buildBatchTravelersSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Batch Travelers')
            ->description('Select employees included in this travel order')
            ->icon('heroicon-o-user-group')
            ->schema([
                Forms\Components\Select::make('employee_ids')
                    ->label('Select Employees')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->options(
                        fn() => \App\Models\User::where('role', User::ROLE_REGULAR)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->required()
                    ->native(false)
                    ->helperText('Employees selected here will see this order in their Tagged Travel Orders tab.')
                    ->columnSpanFull(),
            ])
            ->visible(fn(callable $get) => $get('travel_type') === 'batch')
            ->collapsible()
            ->collapsed(false);
    }

    protected static function buildApprovalSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Administrative Approval')
            ->description('Review and process this travel order')
            ->icon('heroicon-o-shield-check')
            ->schema([
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required()
                    ->native(false)
                    ->live(),

                Forms\Components\TextInput::make('approved_by')
                    ->label('Processed By')
                    ->disabled()
                    ->afterStateHydrated(fn($component) => $component->state(auth()->user()->name))
                    ->dehydrated(),

                Forms\Components\Textarea::make('rejection_remark')
                    ->label('Reason for Rejection')
                    ->placeholder('Provide a clear reason for rejecting this travel order...')
                    ->rows(3)
                    ->visible(fn(callable $get) => $get('status') === 'rejected'),
            ])
            ->visible(fn() => Auth::user()->role === 'admin')
            ->collapsible()
            ->collapsed(true);
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
            ->filtersFormColumns($isAdmin ? 3 : 1)
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
            ->modifyQueryUsing(fn(Builder $query) => self::applyScope($query, $isAdmin))
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->striped()
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading('No travel orders found')
            ->emptyStateDescription('Submit your first travel order to get started.')
            ->emptyStateIcon('heroicon-o-map')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('New Travel Order')
                    ->icon('heroicon-o-plus')
                    ->visible(fn() => !$isAdmin),
            ]);
    }

    // =========================================================================
    //  TABLE COLUMNS
    // =========================================================================

    protected static function getTableColumns(bool $isAdmin): array
    {
        return [
            Tables\Columns\TextColumn::make('travel_order_no')
                ->label('Order No.')
                ->searchable()
                ->sortable()
                ->weight(FontWeight::Bold)
                ->color('primary')
                ->icon('heroicon-o-hashtag')
                ->iconColor('primary')
                ->copyable()
                ->copyMessage('Copied!')
                ->copyMessageDuration(1200),

            Tables\Columns\TextColumn::make('name')
                ->label('Traveler(s)')
                ->searchable()
                ->limit(30)
                ->tooltip(fn($record) => $record->name)
                ->icon('heroicon-o-user-circle')
                ->iconColor('primary')
                ->visible($isAdmin),

            Tables\Columns\TextColumn::make('destination')
                ->label('Destination')
                ->searchable()
                ->limit(28)
                ->tooltip(fn($record) => $record->destination)
                ->icon('heroicon-o-map-pin')
                ->iconColor('warning'),

            Tables\Columns\TextColumn::make('departure_date')
                ->label('Travel Period')
                ->sortable()
                ->icon('heroicon-o-calendar-days')
                ->iconColor('info')
                ->formatStateUsing(
                    fn($state, $record) =>
                    Carbon::parse($record->departure_date)->format('M d, Y')
                    . ' → '
                    . Carbon::parse($record->return_date)->format('M d, Y')
                ),

            Tables\Columns\TextColumn::make('travel_type')
                ->label('Type')
                ->badge()
                ->color(fn(string $state) => match ($state) {
                    'solo' => 'info',
                    'batch' => 'warning',
                    default => 'gray',
                })
                ->icon(fn(string $state) => match ($state) {
                    'solo' => 'heroicon-o-user',
                    'batch' => 'heroicon-o-user-group',
                    default => null,
                })
                ->formatStateUsing(fn(string $state) => match ($state) {
                    'solo' => 'Solo',
                    'batch' => 'Batch',
                    default => ucfirst($state),
                }),

            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->sortable()
                ->color(fn(string $state) => match ($state) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    default => 'gray',
                })
                ->icon(fn(string $state) => match ($state) {
                    'pending' => 'heroicon-m-clock',
                    'approved' => 'heroicon-m-check-circle',
                    'rejected' => 'heroicon-m-x-circle',
                    default => null,
                })
                ->formatStateUsing(fn(string $state) => ucfirst($state)),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Submitted')
                ->since()
                ->sortable()
                ->tooltip(fn($record) => $record->created_at->format('M d, Y h:i A'))
                ->color('gray')
                ->icon('heroicon-o-paper-airplane')
                ->iconColor('gray'),

            Tables\Columns\TextColumn::make('approver.name')
                ->label('Processed By')
                ->placeholder('Awaiting Review')
                ->color('gray')
                ->icon('heroicon-o-check-badge')
                ->iconColor('gray')
                ->limit(22)
                ->tooltip(fn($record) => $record->approver?->name),

            Tables\Columns\TextColumn::make('rejection_remark')
                ->label('Rejection Reason')
                ->limit(40)
                ->wrap()
                ->placeholder('—')
                ->color(fn($record) => filled($record?->rejection_remark) ? 'danger' : 'gray')
                ->icon(fn($record) => filled($record?->rejection_remark) ? 'heroicon-o-exclamation-triangle' : null)
                ->iconColor('danger')
                ->tooltip(fn($record) => $record?->rejection_remark)
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    // =========================================================================
    //  FILTERS
    // =========================================================================

    protected static function getEnhancedFilters(bool $isAdmin): array
    {
        if (!$isAdmin) {
            return [
                Tables\Filters\Filter::make('all_filters')
                    ->label('Filters')
                    ->columnSpanFull()
                    ->form([
                        Forms\Components\Grid::make(4)->schema([
                            Forms\Components\Select::make('status')
                                ->label('Status')
                                ->native(false)
                                ->placeholder('All statuses')
                                ->options([
                                    'pending' => '🕐  Pending',
                                    'approved' => '✅  Approved',
                                    'rejected' => '❌  Rejected',
                                ]),

                            Forms\Components\Select::make('travel_type')
                                ->label('Travel Type')
                                ->native(false)
                                ->placeholder('All types')
                                ->options([
                                    'solo' => '👤  Solo Travel',
                                    'batch' => '👥  Batch Travel',
                                ]),

                            Forms\Components\Select::make('preset')
                                ->label('Quick Select')
                                ->placeholder('— pick a period —')
                                ->native(false)
                                ->options([
                                    'this_week' => '📅  This Week',
                                    'this_month' => '📅  This Month',
                                    'last_month' => '📅  Last Month',
                                    'this_year' => '📅  This Year',
                                ])
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    [$from, $to] = match ($state) {
                                        'this_week' => [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()],
                                        'this_month' => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
                                        'last_month' => [now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()],
                                        'this_year' => [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()],
                                        default => [null, null],
                                    };
                                    $set('from', $from);
                                    $set('to', $to);
                                }),

                            Forms\Components\Placeholder::make('')->label('')->columnSpan(1),

                            Forms\Components\DatePicker::make('from')
                                ->label('From')
                                ->native(false)
                                ->displayFormat('M d, Y')
                                ->maxDate(fn(callable $get) => $get('to') ?? now()),

                            Forms\Components\DatePicker::make('to')
                                ->label('To')
                                ->native(false)
                                ->displayFormat('M d, Y')
                                ->minDate(fn(callable $get) => $get('from')),
                        ]),
                    ])
                    ->query(
                        fn(Builder $query, array $data) => $query
                            ->when($data['status'] ?? null, fn($q, $v) => $q->where('status', $v))
                            ->when($data['travel_type'] ?? null, fn($q, $v) => $q->where('travel_type', $v))
                            ->when($data['from'] ?? null, fn($q, $d) => $q->whereDate('departure_date', '>=', $d))
                            ->when($data['to'] ?? null, fn($q, $d) => $q->whereDate('departure_date', '<=', $d))
                    )
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        $presetLabels = [
                            'this_week' => 'This Week',
                            'this_month' => 'This Month',
                            'last_month' => 'Last Month',
                            'this_year' => 'This Year',
                        ];

                        if ($data['status'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Status: ' . ucfirst($data['status']))->removeField('status');
                        }
                        if ($data['travel_type'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Type: ' . ucfirst($data['travel_type']))->removeField('travel_type');
                        }
                        $preset = $data['preset'] ?? null;
                        if (($data['from'] ?? null) || ($data['to'] ?? null)) {
                            if ($preset && isset($presetLabels[$preset])) {
                                $indicators[] = Tables\Filters\Indicator::make('Departure: ' . $presetLabels[$preset])->removeField('preset');
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
                    }),
            ];
        }

        // ── ADMIN: 3 separate cards ────────────────────────────────────────────

        $employeeFilter = Tables\Filters\Filter::make('employee_filter')
            ->label('Employee')
            ->columnSpan(1)
            ->form([
                Forms\Components\Select::make('traveler_name')
                    ->label('Employee')
                    ->options(
                        fn() => \App\Models\User::where('role', User::ROLE_REGULAR)
                            ->orderBy('name')
                            ->pluck('name', 'name')
                            ->toArray()
                    )
                    ->searchable()
                    ->native(false)
                    ->placeholder('All employees'),
            ])
            ->query(
                fn(Builder $query, array $data) => $query
                    ->when($data['traveler_name'] ?? null, fn($q, $v) => $q->where('name', 'like', "%{$v}%"))
            )
            ->indicateUsing(function (array $data): array {
                if (!($data['traveler_name'] ?? null))
                    return [];
                return [
                    Tables\Filters\Indicator::make('Employee: ' . $data['traveler_name'])
                        ->removeField('traveler_name'),
                ];
            });

        $statusTypeFilter = Tables\Filters\Filter::make('status_and_type')
            ->label('Status & Type')
            ->columnSpan(1)
            ->form([
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->native(false)
                    ->placeholder('All statuses')
                    ->options([
                        'pending' => '🕐  Pending',
                        'approved' => '✅  Approved',
                        'rejected' => '❌  Rejected',
                    ]),

                Forms\Components\Select::make('travel_type')
                    ->label('Travel Type')
                    ->native(false)
                    ->placeholder('All types')
                    ->options([
                        'solo' => '👤  Solo Travel',
                        'batch' => '👥  Batch Travel',
                    ]),
            ])
            ->query(
                fn(Builder $query, array $data) => $query
                    ->when($data['status'] ?? null, fn($q, $v) => $q->where('status', $v))
                    ->when($data['travel_type'] ?? null, fn($q, $v) => $q->where('travel_type', $v))
            )
            ->indicateUsing(function (array $data): array {
                $indicators = [];
                if ($data['status'] ?? null) {
                    $indicators[] = Tables\Filters\Indicator::make('Status: ' . ucfirst($data['status']))->removeField('status');
                }
                if ($data['travel_type'] ?? null) {
                    $indicators[] = Tables\Filters\Indicator::make('Type: ' . ucfirst($data['travel_type']))->removeField('travel_type');
                }
                return $indicators;
            });

        $periodFilter = Tables\Filters\Filter::make('departure_period')
            ->label('Departure Period')
            ->columnSpan(1)
            ->form([
                Forms\Components\Select::make('preset')
                    ->label('Quick Select')
                    ->placeholder('— pick a period —')
                    ->native(false)
                    ->options([
                        'this_week' => '📅  This Week',
                        'this_month' => '📅  This Month',
                        'last_month' => '📅  Last Month',
                        'this_year' => '📅  This Year',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        [$from, $to] = match ($state) {
                            'this_week' => [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()],
                            'this_month' => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
                            'last_month' => [now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()],
                            'this_year' => [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()],
                            default => [null, null],
                        };
                        $set('from', $from);
                        $set('to', $to);
                    }),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\DatePicker::make('from')
                        ->label('From')
                        ->native(false)
                        ->displayFormat('M d, Y')
                        ->maxDate(fn(callable $get) => $get('to') ?? now()),
                    Forms\Components\DatePicker::make('to')
                        ->label('To')
                        ->native(false)
                        ->displayFormat('M d, Y')
                        ->minDate(fn(callable $get) => $get('from')),
                ]),
            ])
            ->query(
                fn(Builder $query, array $data) => $query
                    ->when($data['from'] ?? null, fn($q, $d) => $q->whereDate('departure_date', '>=', $d))
                    ->when($data['to'] ?? null, fn($q, $d) => $q->whereDate('departure_date', '<=', $d))
            )
            ->indicateUsing(function (array $data): array {
                $presetLabels = [
                    'this_week' => 'This Week',
                    'this_month' => 'This Month',
                    'last_month' => 'Last Month',
                    'this_year' => 'This Year',
                ];
                $indicators = [];
                $preset = $data['preset'] ?? null;

                if (($data['from'] ?? null) || ($data['to'] ?? null)) {
                    if ($preset && isset($presetLabels[$preset])) {
                        $indicators[] = Tables\Filters\Indicator::make('Departure: ' . $presetLabels[$preset])->removeField('preset');
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

        return [$employeeFilter, $statusTypeFilter, $periodFilter];
    }

    // =========================================================================
    //  ACTIONS
    // =========================================================================

    protected static function getContextualActions(bool $isAdmin): array
    {
        return [
            Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info'),

                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn($record) => route('travel-order.print', $record->id))
                    ->openUrlInNewTab()
                    ->visible(fn(TravelOrder $record) => $record->status === 'approved'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->visible(
                        fn(TravelOrder $record) =>
                        $isAdmin ||
                        ($record->created_by === Auth::id() && $record->status === 'pending')
                    ),
            ])
                ->label('Actions')
                ->icon('heroicon-o-ellipsis-vertical')
                ->size(\Filament\Support\Enums\ActionSize::Small)
                ->color('gray')
                ->button(),
        ];
    }

    // =========================================================================
    //  QUERY SCOPING
    // =========================================================================

    protected static function applyScope(Builder $query, bool $isAdmin): Builder
    {
        return $isAdmin
            ? $query->with(['creator', 'approver'])
            : $query->with(['creator', 'approver'])->where('created_by', Auth::id());
    }

    // =========================================================================
    //  NAVIGATION BADGE
    // =========================================================================

    public static function getNavigationBadge(): ?string
    {
        if (auth()->user()?->role !== 'admin')
            return null;
        $count = TravelOrder::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        if (auth()->user()?->role !== 'admin')
            return null;
        return TravelOrder::where('status', 'pending')->count() > 0 ? 'warning' : 'success';
    }

    // =========================================================================
    //  PAGES
    // =========================================================================

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTravelOrders::route('/'),
            'create' => Pages\CreateTravelOrder::route('/create'),
            'edit' => Pages\EditTravelOrder::route('/{record}/edit'),
            'view' => Pages\ViewTravelOrder::route('/{record}'),
        ];
    }
}
