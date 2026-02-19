<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocatorSlipResource\Pages;
use App\Models\LocatorSlip;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
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

    /* ============================================================
       FORM DEFINITION
       ============================================================ */

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                self::buildMainSection(),
                self::buildApprovalSection(),
                Forms\Components\Hidden::make('user_id')
                    ->default(fn() => auth()->id()),
            ]);
    }

    /* ============================================================
       TABLE DEFINITION
       ============================================================ */

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::getModernTableColumns())
            ->filters(self::getEnhancedFilters())
            ->actions(self::getContextualActions())
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()->role === 'admin'),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => self::applyUserScope($query))
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->striped()
            ->emptyStateHeading('No locator slips found')
            ->emptyStateDescription('Create your first locator slip to get started.')
            ->emptyStateIcon('heroicon-o-map-pin')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create Locator Slip')
                    ->icon('heroicon-o-plus')
                    ->visible(fn() => Auth::user()->role === 'employee'),
            ]);
    }

    /* ============================================================
       PAGES
       ============================================================ */

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocatorSlips::route('/'),
            'create' => Pages\CreateLocatorSlip::route('/create'),
            'edit' => Pages\EditLocatorSlip::route('/{record}/edit'),
            'view' => Pages\ViewLocatorSlip::route('/{record}'),
        ];
    }

    /* ============================================================
       AUTHORIZATION
       ============================================================ */

    public static function canCreate(): bool
    {
        return Auth::user()->role === 'employee';
    }

    public static function canEdit($record): bool
    {
        return $record->status === 'pending' &&
            (Auth::user()->role === 'admin' || Auth::user()->id === $record->user_id);
    }

    public static function canDelete($record): bool
    {
        return $record->status === 'pending' &&
            (Auth::user()->role === 'admin' || Auth::user()->id === $record->user_id);
    }

    /* ============================================================
       NAVIGATION BADGE
       ============================================================ */

    public static function getNavigationBadge(): ?string
    {
        if (auth()->user()?->role !== 'admin') {
            return null;
        }

        $count = LocatorSlip::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        if (auth()->user()?->role !== 'admin') {
            return null;
        }

        $count = LocatorSlip::where('status', 'pending')->count();
        return $count > 0 ? 'warning' : 'success';
    }

    /* ============================================================
       FORM SECTIONS
       ============================================================ */

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
        return Forms\Components\Group::make()
            ->schema([
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
            ])
            ->columns(2)
            ->columnSpan(2);
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

    /* ============================================================
       MODERN CARD-STYLE TABLE COLUMNS
       ============================================================ */

    protected static function getModernTableColumns(): array
    {
        return [
            // Main Content Card Layout
            Tables\Columns\Layout\Split::make([
                // Left Side - Employee & Destination Info
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('employee_name')
                        ->label('Employee')
                        ->searchable()
                        ->sortable()
                        ->weight('bold')
                        ->size('md')
                        ->icon('heroicon-o-user-circle')
                        ->iconColor('primary'),

                    Tables\Columns\TextColumn::make('position')
                        ->label('Position')
                        ->size('sm')
                        ->color('gray')
                        ->icon('heroicon-o-briefcase')
                        ->iconColor('gray')
                        ->formatStateUsing(fn($record) =>
                            $record->position . ' • ' . ($record->office_department ?? 'N/A')
                        ),

                    Tables\Columns\BadgeColumn::make('transaction_type')
                        ->label('Type')
                        ->colors([
                            'info'  => 'official',
                            'gray'  => 'personal',
                        ])
                        ->icons([
                            'heroicon-o-building-office' => 'official',
                            'heroicon-o-user'            => 'personal',
                        ])
                        ->formatStateUsing(fn(string $state): string => match ($state) {
                            'official' => 'Official Business',
                            'personal' => 'Personal Transaction',
                            default    => $state,
                        }),
                ])->space(2),

                // Middle - Destination & Purpose
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('destination')
                        ->label('Destination')
                        ->searchable()
                        ->weight('medium')
                        ->icon('heroicon-o-map-pin')
                        ->iconColor('warning')
                        ->limit(40)
                        ->tooltip(fn($record) => $record->destination),

                    Tables\Columns\TextColumn::make('purpose')
                        ->label('Purpose')
                        ->size('sm')
                        ->color('gray')
                        ->limit(60)
                        ->default('No purpose specified')
                        ->tooltip(fn($record) => $record->purpose),

                    Tables\Columns\TextColumn::make('inclusive_date')
                        ->label('Trip Date')
                        ->date('M d, Y')
                        ->sortable()
                        ->size('sm')
                        ->icon('heroicon-o-calendar-days')
                        ->iconColor('info'),
                ])->space(1),

                // Right Side - Status & Filing Info
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\BadgeColumn::make('status')
                        ->label('Status')
                        ->colors([
                            'warning' => 'pending',
                            'success' => 'approved',
                            'danger'  => 'disapproved',
                        ])
                        ->icons([
                            'heroicon-m-clock'        => 'pending',
                            'heroicon-m-check-circle' => 'approved',
                            'heroicon-m-x-circle'     => 'disapproved',
                        ])
                        ->formatStateUsing(fn(string $state): string => ucfirst($state))
                        ->size('md'),

                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Submitted')
                        ->date('M d, Y')
                        ->size('sm')
                        ->color('gray')
                        ->icon('heroicon-o-paper-airplane')
                        ->iconColor('gray'),

                    Tables\Columns\TextColumn::make('approved_by')
                        ->label('Processed By')
                        ->size('sm')
                        ->color('gray')
                        ->default('Awaiting Review')
                        ->icon('heroicon-o-user')
                        ->iconColor('gray')
                        ->limit(20)
                        ->tooltip(fn($record) => $record->approved_by),
                ])->space(2)->alignment('end'),
            ])->from('md'),

            // Collapsible Admin Remarks Panel
            Tables\Columns\Layout\Panel::make([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\IconColumn::make('has_remarks')
                        ->label('Remarks')
                        ->boolean()
                        ->getStateUsing(fn($record) => !blank($record->admin_remarks))
                        ->trueIcon('heroicon-o-chat-bubble-left-ellipsis')
                        ->falseIcon('heroicon-o-check-circle')
                        ->trueColor('warning')
                        ->falseColor('success'),

                    Tables\Columns\TextColumn::make('admin_remarks')
                        ->label('Admin Remarks')
                        ->limit(100)
                        ->wrap()
                        ->default('No remarks')
                        ->color(fn($record) => blank($record->admin_remarks) ? 'success' : 'warning')
                        ->icon(fn($record) => blank($record->admin_remarks)
                            ? 'heroicon-o-check'
                            : 'heroicon-o-exclamation-triangle'
                        ),
                ]),
            ])
                ->collapsible()
                ->collapsed(fn($record) => blank($record->admin_remarks)),
        ];
    }

    /* ============================================================
       ENHANCED FILTERS
       ============================================================ */

    protected static function getEnhancedFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('status')
                ->label('Status')
                ->options([
                    'pending'     => 'Pending',
                    'approved'    => 'Approved',
                    'disapproved' => 'Disapproved',
                ])
                ->native(false)
                ->indicator('Status'),

            Tables\Filters\SelectFilter::make('transaction_type')
                ->label('Type')
                ->options([
                    'official' => 'Official Business',
                    'personal' => 'Personal Transaction',
                ])
                ->native(false)
                ->indicator('Type'),

            Tables\Filters\Filter::make('inclusive_date')
                ->form([
                    Forms\Components\DatePicker::make('from')
                        ->label('From Date')
                        ->native(false),
                    Forms\Components\DatePicker::make('until')
                        ->label('Until Date')
                        ->native(false),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['from'],
                            fn(Builder $query, $date): Builder => $query->whereDate('inclusive_date', '>=', $date),
                        )
                        ->when(
                            $data['until'],
                            fn(Builder $query, $date): Builder => $query->whereDate('inclusive_date', '<=', $date),
                        );
                })
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
        ];
    }

    /* ============================================================
       CONTEXTUAL ACTIONS
       ============================================================ */

    protected static function getContextualActions(): array
    {
        return [
            Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info'),

                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->visible(fn(LocatorSlip $record) =>
                        $record->status === 'pending' &&
                        (Auth::user()->role === 'admin' || Auth::user()->id === $record->user_id)
                    ),

                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn($record) => route('locator_slip.print', $record->id))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->status === 'approved'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->visible(fn(LocatorSlip $record) =>
                        $record->status === 'pending' &&
                        (Auth::user()->role === 'admin' || Auth::user()->id === $record->user_id)
                    ),
            ])
                ->label('Actions')
                ->icon('heroicon-o-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button(),
        ];
    }

    /* ============================================================
       QUERY SCOPING
       ============================================================ */

    protected static function applyUserScope(Builder $query): Builder
    {
        return Auth::user()->role === 'admin'
            ? $query
            : $query->where('user_id', Auth::id());
    }
}
