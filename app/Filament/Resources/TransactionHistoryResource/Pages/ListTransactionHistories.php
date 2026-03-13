<?php

namespace App\Filament\Resources\TransactionHistoryResource\Pages;

use App\Filament\Resources\TransactionHistoryResource;
use App\Models\TransactionHistory;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

class ListTransactionHistories extends ListRecords
{
    protected static string $resource = TransactionHistoryResource::class;

    protected static string $view = 'filament.resources.transaction-history-resource.pages.list-transaction-histories';

    public function getViewData(): array
    {
        $user = Auth::user();
        $isAdmin = $user?->isAdmin();

        $query = TransactionHistory::with('user')->latest();

        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

        $transactions = $query->paginate(50);

        $grouped = $transactions->getCollection()->groupBy(function ($item) {
            return $item->created_at->setTimezone('Asia/Manila')->format('Y-m-d');
        });

        $statsQuery = TransactionHistory::selectRaw('module, COUNT(*) as total');
        if (!$isAdmin) {
            $statsQuery->where('user_id', $user->id);
        }
        $stats = $statsQuery->groupBy('module')->pluck('total', 'module');

        $todayQuery = TransactionHistory::whereDate('created_at', today());
        if (!$isAdmin) {
            $todayQuery->where('user_id', $user->id);
        }
        $todayCount = $todayQuery->count();

        return [
            'transactions' => $transactions,
            'grouped' => $grouped,
            'stats' => $stats,
            'todayCount' => $todayCount,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(fn() => $this->redirect(static::getResource()::getUrl('index'))),
        ];
    }
}
