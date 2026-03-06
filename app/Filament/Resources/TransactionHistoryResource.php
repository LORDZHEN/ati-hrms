<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionHistoryResource\Pages;
use App\Models\TransactionHistory;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\Auth;

/**
 * TransactionHistoryResource
 *
 * Read-only Filament resource for the HRMS activity log.
 *
 * Design principles:
 *  • No create / edit / delete — admins can only VIEW.
 *  • Eager-loads the `user` relation to avoid N+1 on listing.
 *  • Custom list page renders a timeline instead of a plain table.
 *  • Filters: Module, Status, Date range.
 *  • Global search on employee_name, transaction_type, description.
 */
class TransactionHistoryResource extends Resource
{
    protected static ?string $model = TransactionHistory::class;

    // ── Navigation ────────────────────────────────────────────────────────────

    protected static ?string $navigationIcon  = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Transaction History';
    protected static ?string $navigationGroup = 'System';
    protected static ?int    $navigationSort  = 99;
    protected static ?string $slug            = 'transaction-histories';

    // ── Labels ────────────────────────────────────────────────────────────────

    protected static ?string $modelLabel       = 'Transaction';
    protected static ?string $pluralModelLabel = 'Transaction History';
    protected static ?string $recordTitleAttribute = 'transaction_type';

    // ─────────────────────────────────────────────────────────────────────────
    // Form  (not used — resource is read-only, but Filament requires it)
    // ─────────────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Table
    // ─────────────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            // Eager-load user to prevent N+1 queries on avatar display
            ->modifyQueryUsing(fn (Builder $query) => $query->with('user')->latest())

            ->columns([
                // ── Employee ──────────────────────────────────────────────
                TextColumn::make('employee_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (TransactionHistory $r) => $r->user?->email ?? '')
                    ->icon('heroicon-o-user-circle'),

                // ── Module icon + Transaction Type ────────────────────────
                TextColumn::make('transaction_type')
                    ->label('Transaction')
                    ->searchable()
                    ->sortable()
                    ->description(fn (TransactionHistory $r) => $r->module)
                    ->icon(fn (TransactionHistory $r) => $r->resolved_icon),

                // ── Description ───────────────────────────────────────────
                TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(60)
                    ->tooltip(fn (TransactionHistory $r) => $r->description),

                // ── Status Badge ──────────────────────────────────────────
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->color(fn (TransactionHistory $r) => TransactionHistory::statusColor($r->status)),

                // ── Date ──────────────────────────────────────────────────
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y  g:i A')
                    ->sortable()
                    ->since()
                    ->tooltip(fn (TransactionHistory $r) => $r->created_at->setTimezone('Asia/Manila')->format('F j, Y  g:i:s A')),
            ])

            // ── Filters ───────────────────────────────────────────────────────
            ->filters([
                // Filter by module
                SelectFilter::make('module')
                    ->label('Module')
                    ->options([
                        'Leave'    => 'Leave Applications',
                        'Travel'   => 'Travel Orders',
                        'Locator'  => 'Locator Slips',
                        'SALN'     => 'SALN',
                        'PDS'      => 'Personal Data Sheet',
                        'Employee' => 'Employee Registration',
                        'DTR'      => 'Daily Time Record',
                    ])
                    ->placeholder('All Modules'),

                // Filter by status
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'    => 'Pending',
                        'approved'   => 'Approved',
                        'rejected'   => 'Rejected',
                        'filed'      => 'Filed',
                        'uploaded'   => 'Uploaded',
                        'registered' => 'Registered',
                        'submitted'  => 'Submitted',
                        'cancelled'  => 'Cancelled',
                    ])
                    ->placeholder('All Statuses'),

                // Date range filter
                Filter::make('created_at')
                    ->label('Date Range')
                    ->form([
                        DatePicker::make('from')->label('From')->native(false),
                        DatePicker::make('until')->label('Until')->native(false),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'],  fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
                    }),
            ])

            // ── Actions (view-only — no edit/delete) ──────────────────────────
            ->actions([
                Tables\Actions\ViewAction::make()->label('View'),
            ])

            // Disable all bulk actions
            ->bulkActions([])

            // Default sort: newest first
            ->defaultSort('created_at', 'desc')

            // Striped rows for readability
            ->striped()

            ->emptyStateIcon(null)
            ->emptyStateHeading('No Transactions Logged')
            ->emptyStateDescription('Activities will appear here as employees use the HRMS modules.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Pages
    // ─────────────────────────────────────────────────────────────────────────

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactionHistories::route('/'),
            'view'  => Pages\ViewTransactionHistory::route('/{record}'),
            // NOTE: 'create' and 'edit' are intentionally omitted.
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Access Control — admin-only, read-only
    //
    // canAccess()     hides the nav item and blocks the index/view pages
    //                 entirely for non-admins (returns 403 if hit directly).
    // canViewAny()    guards the list page at the policy level.
    // canView()       guards the detail page at the policy level.
    // canCreate/Edit/Delete always return false — nobody can mutate logs.
    // ─────────────────────────────────────────────────────────────────────────

    /** Hide the nav item and block all page access for non-admins. */
    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->isAdmin();
    }

    /** Guard the list page. */
    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()->isAdmin();
    }

    /** Guard the detail/view page. */
    public static function canView($record): bool
    {
        return Auth::check() && Auth::user()->isAdmin();
    }

    // Write operations are always forbidden — logs are immutable.
    public static function canCreate(): bool        { return false; }
    public static function canEdit($record): bool   { return false; }
    public static function canDelete($record): bool { return false; }
    public static function canDeleteAny(): bool     { return false; }

    // ─────────────────────────────────────────────────────────────────────────
    // Navigation badge — shows today's transaction count (admins only)
    // ─────────────────────────────────────────────────────────────────────────

    public static function getNavigationBadge(): ?string
    {
        if (! Auth::check() || ! Auth::user()->isAdmin()) {
            return null;
        }

        $count = TransactionHistory::whereDate('created_at', today())->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
    }
}
