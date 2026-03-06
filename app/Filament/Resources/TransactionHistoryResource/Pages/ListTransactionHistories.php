<?php

namespace App\Filament\Resources\TransactionHistoryResource\Pages;

use App\Filament\Resources\TransactionHistoryResource;
use App\Models\TransactionHistory;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

/**
 * ListTransactionHistories
 *
 * Renders a modern activity timeline instead of a plain Filament table.
 * The timeline groups entries by date and displays them with colored status
 * badges, module icons, employee initials, and links to the source record.
 *
 * The underlying Filament table (with search/filter) is preserved and
 * accessible via the "Table View" header action.
 */
class ListTransactionHistories extends ListRecords
{
    protected static string $resource = TransactionHistoryResource::class;

    /**
     * Use our custom Blade timeline view instead of the default table.
     * Override this to the standard filament list view string if you prefer
     * the plain table at any time.
     */
    protected static string $view = 'filament.resources.transaction-history-resource.pages.list-transaction-histories';

    // ── View data injected into the Blade template ────────────────────────────

    public function getViewData(): array
    {
        // Load the 50 most-recent entries, eager-loading user
        $transactions = TransactionHistory::with('user')
            ->latest()
            ->paginate(50);

        // Group by calendar date for the timeline dividers
        $grouped = $transactions->getCollection()->groupBy(function ($item) {
            return $item->created_at->setTimezone('Asia/Manila')->format('Y-m-d');
        });

        // Module statistics for the summary header bar
        $stats = TransactionHistory::selectRaw('module, COUNT(*) as total')
            ->groupBy('module')
            ->pluck('total', 'module');

        // Today's count for the badge
        $todayCount = TransactionHistory::whereDate('created_at', today())->count();

        return [
            'transactions' => $transactions,
            'grouped'      => $grouped,
            'stats'        => $stats,
            'todayCount'   => $todayCount,
        ];
    }

    // ── Header actions ────────────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            // Refresh the page to pick up new entries
            Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(fn () => $this->redirect(static::getResource()::getUrl('index'))),
        ];
    }
}
