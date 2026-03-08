<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocatorSlipResource\Pages;
use App\Models\LocatorSlip;
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

class LocatorSlipResource extends Resource
{
    protected static ?string $model = LocatorSlip::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $slug = 'locator-slips';
    protected static ?string $navigationLabel = 'Locator Slips';
    protected static ?string $modelLabel = 'Locator Slip';
    protected static ?string $pluralModelLabel = 'Locator Slips';
    protected static ?string $navigationGroup = 'Documents';
    protected static ?int $navigationSort = 3;

    // =========================================================================
    //  FORM
    // =========================================================================

    public static function form(Form $form): Form
    {
        return $form->schema([
            self::buildMainSection(),
            self::buildApprovalSection(),
            Forms\Components\Hidden::make('user_id')
                ->default(fn() => auth()->id()),
        ]);
    }

    // =========================================================================
    //  TABLE
    // =========================================================================

    public static function table(Table $table): Table
    {
        // Compute once — prevents repeated auth lookups in every closure.
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
            ->modifyQueryUsing(
                fn(Builder $query) => $isAdmin
                ? $query->with('user')
                : $query->with('user')->where('user_id', Auth::id())
            )
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->striped()
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading('No locator slips found')
            ->emptyStateDescription('Create your first locator slip to get started.')
            ->emptyStateIcon('heroicon-o-map-pin')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('New Locator Slip')
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
            // ── Employee — admin only ─────────────────────────────────────────
            Tables\Columns\TextColumn::make('employee_name')
                ->label('Employee')
                ->searchable()
                ->sortable()
                ->weight(FontWeight::Bold)
                ->icon('heroicon-o-user-circle')
                ->iconColor('primary')
                ->visible($isAdmin),

            // ── Position • Department ─────────────────────────────────────────
            Tables\Columns\TextColumn::make('position')
                ->label('Position')
                ->color('gray')
                ->icon('heroicon-o-briefcase')
                ->iconColor('gray')
                ->formatStateUsing(
                    fn($record) =>
                    $record->position . ' • ' . ($record->office_department ?? 'N/A')
                )
                ->toggleable(isToggledHiddenByDefault: true),

            // ── Transaction Type badge ────────────────────────────────────────
            Tables\Columns\TextColumn::make('transaction_type')
                ->label('Type')
                ->badge()
                ->color(fn(string $state) => match ($state) {
                    'official' => 'info',
                    'personal' => 'gray',
                    default => 'gray',
                })
                ->icon(fn(string $state) => match ($state) {
                    'official' => 'heroicon-o-building-office',
                    'personal' => 'heroicon-o-user',
                    default => null,
                })
                ->formatStateUsing(fn(string $state): string => match ($state) {
                    'official' => 'Official Business',
                    'personal' => 'Personal Transaction',
                    default => $state,
                }),

            // ── Destination ───────────────────────────────────────────────────
            Tables\Columns\TextColumn::make('destination')
                ->label('Destination')
                ->searchable()
                ->limit(30)
                ->tooltip(fn($record) => $record->destination)
                ->icon('heroicon-o-map-pin')
                ->iconColor('warning'),

            // ── Purpose ───────────────────────────────────────────────────────
            Tables\Columns\TextColumn::make('purpose')
                ->label('Purpose')
                ->color('gray')
                ->limit(35)
                ->placeholder('—')
                ->tooltip(fn($record) => $record->purpose)
                ->toggleable(isToggledHiddenByDefault: true),

            // ── Trip Date ─────────────────────────────────────────────────────
            Tables\Columns\TextColumn::make('inclusive_date')
                ->label('Trip Date')
                ->date('M d, Y')
                ->sortable()
                ->icon('heroicon-o-calendar-days')
                ->iconColor('info'),

            // ── Status badge ──────────────────────────────────────────────────
            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->sortable()
                ->color(fn(string $state) => match ($state) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'disapproved' => 'danger',
                    default => 'gray',
                })
                ->icon(fn(string $state) => match ($state) {
                    'pending' => 'heroicon-m-clock',
                    'approved' => 'heroicon-m-check-circle',
                    'disapproved' => 'heroicon-m-x-circle',
                    default => null,
                })
                ->formatStateUsing(fn(string $state): string => ucfirst($state)),

            // ── Submitted ─────────────────────────────────────────────────────
            Tables\Columns\TextColumn::make('created_at')
                ->label('Submitted')
                ->since()
                ->sortable()
                ->tooltip(fn($record) => $record->created_at->format('M d, Y h:i A'))
                ->color('gray')
                ->icon('heroicon-o-paper-airplane')
                ->iconColor('gray'),

            // ── Processed By ──────────────────────────────────────────────────
            Tables\Columns\TextColumn::make('approved_by')
                ->label('Processed By')
                ->color('gray')
                ->placeholder('Awaiting Review')
                ->icon('heroicon-o-shield-check')
                ->iconColor('gray')
                ->limit(22)
                ->tooltip(fn($record) => $record->approved_by),

            // ── Admin Remarks ─────────────────────────────────────────────────
            Tables\Columns\TextColumn::make('admin_remarks')
                ->label('Remarks')
                ->limit(40)
                ->placeholder('—')
                ->color(fn($record) => filled($record?->admin_remarks) ? 'warning' : 'gray')
                ->icon(fn($record) => filled($record?->admin_remarks) ? 'heroicon-o-chat-bubble-left-ellipsis' : null)
                ->iconColor('warning')
                ->tooltip(fn($record) => $record?->admin_remarks)
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    // =========================================================================
    //  FILTERS
    //
    //  Old: 4 stacked fields in a side drawer — ugly and hard to use.
    //
    //  New:
    //    EMPLOYEE view (2 cols): Status & Type | Trip Date Period
    //    ADMIN view   (3 cols): Employee      | Status & Type | Trip Date Period
    //
    //  The side-drawer layout is replaced with AboveContentCollapsible so
    //  filters sit above the table, horizontally, matching DTR and Leave.
    // =========================================================================

    protected static function getEnhancedFilters(bool $isAdmin): array
    {
        $filters = [];

        // ── ADMIN ONLY — Column 1: Employee picker ────────────────────────────
        if ($isAdmin) {
            $filters[] = Tables\Filters\Filter::make('employee_filter')
                ->label('Employee')
                ->columnSpan(1)
                ->form([
                    Forms\Components\Select::make('employee_name')
                        ->label('Employee')
                        ->options(
                            fn() => \App\Models\User::where('role', 'employee')
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
                        ->when($data['employee_name'] ?? null, fn($q, $v) => $q->where('employee_name', $v))
                )
                ->indicateUsing(function (array $data): array {
                    if (!($data['employee_name'] ?? null))
                        return [];
                    return [
                        Tables\Filters\Indicator::make('Employee: ' . $data['employee_name'])
                            ->removeField('employee_name'),
                    ];
                });
        }

        // ── Column 1 (employee) / Column 2 (admin): Status & Type ────────────
        $filters[] = Tables\Filters\Filter::make('status_and_type')
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
                        'disapproved' => '❌  Disapproved',
                    ]),

                Forms\Components\Select::make('transaction_type')
                    ->label('Transaction Type')
                    ->native(false)
                    ->placeholder('All types')
                    ->options([
                        'official' => '🏢  Official Business',
                        'personal' => '👤  Personal Transaction',
                    ]),
            ])
            ->query(
                fn(Builder $query, array $data) => $query
                    ->when($data['status'] ?? null, fn($q, $v) => $q->where('status', $v))
                    ->when($data['transaction_type'] ?? null, fn($q, $v) => $q->where('transaction_type', $v))
            )
            ->indicateUsing(function (array $data): array {
                $indicators = [];
                if ($data['status'] ?? null) {
                    $indicators[] = Tables\Filters\Indicator::make('Status: ' . ucfirst($data['status']))
                        ->removeField('status');
                }
                if ($data['transaction_type'] ?? null) {
                    $label = $data['transaction_type'] === 'official' ? 'Official Business' : 'Personal Transaction';
                    $indicators[] = Tables\Filters\Indicator::make('Type: ' . $label)
                        ->removeField('transaction_type');
                }
                return $indicators;
            });

        // ── Column 2 (employee) / Column 3 (admin): Trip Date Period ──────────
        // Quick Select auto-fills the From/To pickers — same UX pattern as DTR.
        $filters[] = Tables\Filters\Filter::make('trip_period')
            ->label('Trip Date Period')
            ->columnSpan(1)
            ->form([
                Forms\Components\Select::make('preset')
                    ->label('Quick Select')
                    ->placeholder('— pick a period —')
                    ->native(false)
                    ->options([
                        'today' => '📅  Today',
                        'this_week' => '📅  This Week',
                        'this_month' => '📅  This Month',
                        'last_month' => '📅  Last Month',
                        'this_year' => '📅  This Year',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        [$from, $to] = match ($state) {
                            'today' => [today()->toDateString(), today()->toDateString()],
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
                        ->minDate(fn(callable $get) => $get('from'))
                        ->maxDate(now()),
                ]),
            ])
            ->query(
                fn(Builder $query, array $data) => $query
                    ->when($data['from'] ?? null, fn($q, $d) => $q->whereDate('inclusive_date', '>=', $d))
                    ->when($data['to'] ?? null, fn($q, $d) => $q->whereDate('inclusive_date', '<=', $d))
            )
            ->indicateUsing(function (array $data): array {
                $presetLabels = [
                    'today' => 'Today',
                    'this_week' => 'This Week',
                    'this_month' => 'This Month',
                    'last_month' => 'Last Month',
                    'this_year' => 'This Year',
                ];
                $indicators = [];
                $preset = $data['preset'] ?? null;

                if (($data['from'] ?? null) || ($data['to'] ?? null)) {
                    if ($preset && isset($presetLabels[$preset])) {
                        $indicators[] = Tables\Filters\Indicator::make('Trip: ' . $presetLabels[$preset])
                            ->removeField('preset');
                    } else {
                        if ($data['from'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make(
                                'From: ' . Carbon::parse($data['from'])->format('M d, Y')
                            )->removeField('from');
                        }
                        if ($data['to'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make(
                                'To: ' . Carbon::parse($data['to'])->format('M d, Y')
                            )->removeField('to');
                        }
                    }
                }
                return $indicators;
            });

        return $filters;
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

                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(
                        fn(LocatorSlip $record) =>
                        $record->status === 'pending' &&
                        ($isAdmin || Auth::id() === $record->user_id)
                    ),

                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn($record) => route('locator_slip.print', $record->id))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->status === 'approved'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->visible(
                        fn(LocatorSlip $record) =>
                        $record->status === 'pending' &&
                        ($isAdmin || Auth::id() === $record->user_id)
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
    //  FORM SECTIONS
    // =========================================================================

    protected static function buildMainSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Trip Information')
            ->description('Fill out your locator slip details')
            ->icon('heroicon-o-document-text')
            ->schema([
                self::buildTransactionTypeField(),
                self::buildHiddenEmployeeFields(),
                self::buildTripDetailsFields(),
                self::buildDateField(),
                self::buildHiddenRequestedByField(),
            ])
            ->collapsible()
            ->collapsed(false)
            ->columns(2);
    }

    protected static function buildTransactionTypeField(): Forms\Components\Radio
    {
        return Forms\Components\Radio::make('transaction_type')
            ->label('Transaction Type')
            ->options([
                'official' => 'Official Business',
                'personal' => 'Personal Transaction',
            ])
            ->default('official')
            ->inline()
            ->inlineLabel(false)
            ->required()
            ->columnSpan(2);
    }

    protected static function buildHiddenEmployeeFields(): Forms\Components\Group
    {
        return Forms\Components\Group::make([
            Forms\Components\Hidden::make('employee_name')
                ->default(fn() => auth()->user()->name),
            Forms\Components\Hidden::make('position')
                ->default(fn() => auth()->user()->position),
            Forms\Components\Hidden::make('office_department')
                ->default(fn() => auth()->user()->department),
        ]);
    }

    protected static function buildTripDetailsFields(): Forms\Components\Group
    {
        return Forms\Components\Group::make()->schema([
            Forms\Components\TextInput::make('destination')
                ->label('Destination')
                ->placeholder('e.g., Manila City Hall')
                ->required()
                ->maxLength(255)
                ->columnSpan(1),

            Forms\Components\Textarea::make('purpose')
                ->label('Purpose')
                ->placeholder('Brief description of your trip purpose')
                ->rows(3)
                ->columnSpan(1),
        ])->columns(2)->columnSpan(2);
    }

    protected static function buildDateField(): Forms\Components\DatePicker
    {
        return Forms\Components\DatePicker::make('inclusive_date')
            ->label('Date')
            ->required()
            ->default(now())
            ->native(false)
            ->displayFormat('F d, Y')
            ->columnSpan(2);
    }

    protected static function buildHiddenRequestedByField(): Forms\Components\Hidden
    {
        return Forms\Components\Hidden::make('requested_by')
            ->default(fn() => auth()->user()->name);
    }

    protected static function buildApprovalSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Administrative Approval')
            ->description('Review and approve/disapprove this request')
            ->icon('heroicon-o-shield-check')
            ->schema([
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'disapproved' => 'Disapproved',
                    ])
                    ->required()
                    ->native(false)
                    ->reactive(),

                Forms\Components\TextInput::make('approved_by')
                    ->label('Approved By')
                    ->disabled()
                    ->afterStateHydrated(fn($component) => $component->state(auth()->user()->name))
                    ->dehydrated(),

                Forms\Components\Textarea::make('admin_remarks')
                    ->label('Remarks / Reason for Disapproval')
                    ->placeholder('Provide detailed reason for disapproval')
                    ->rows(3)
                    ->visible(fn(callable $get) => $get('status') === 'disapproved'),
            ])
            ->visible(fn() => auth()->user()->role === 'admin')
            ->collapsible()
            ->collapsed(true);
    }

    // =========================================================================
    //  AUTHORIZATION
    // =========================================================================

    public static function canCreate(): bool
    {
        return Auth::user()->role === 'employee';
    }

    public static function canEdit($record): bool
    {
        return $record->status === 'pending' &&
            (Auth::user()->role === 'admin' || Auth::id() === $record->user_id);
    }

    public static function canDelete($record): bool
    {
        return $record->status === 'pending' &&
            (Auth::user()->role === 'admin' || Auth::id() === $record->user_id);
    }

    // =========================================================================
    //  NAVIGATION BADGE
    // =========================================================================

    public static function getNavigationBadge(): ?string
    {
        if (auth()->user()?->role !== 'admin')
            return null;
        $count = LocatorSlip::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        if (auth()->user()?->role !== 'admin')
            return null;
        return LocatorSlip::where('status', 'pending')->count() > 0 ? 'warning' : 'success';
    }

    // =========================================================================
    //  PAGES
    // =========================================================================

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocatorSlips::route('/'),
            'create' => Pages\CreateLocatorSlip::route('/create'),
            'edit' => Pages\EditLocatorSlip::route('/{record}/edit'),
            'view' => Pages\ViewLocatorSlip::route('/{record}'),
        ];
    }
}
