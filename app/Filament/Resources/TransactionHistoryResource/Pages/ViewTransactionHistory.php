<?php

namespace App\Filament\Resources\TransactionHistoryResource\Pages;

use App\Filament\Resources\TransactionHistoryResource;
use App\Models\TransactionHistory;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Filament\Actions\Action;

/**
 * ViewTransactionHistory
 *
 * Detailed view of a single transaction log entry.
 * Uses Filament Infolist to render structured, read-only fields.
 * Provides a "View Original Record" action that links back to the source.
 */
class ViewTransactionHistory extends ViewRecord
{
    protected static string $resource = TransactionHistoryResource::class;

    // ─────────────────────────────────────────────────────────────────────────
    // Infolist Layout
    // ─────────────────────────────────────────────────────────────────────────

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // ── Transaction Details ────────────────────────────────────
                Section::make('Transaction Details')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('employee_name')
                                ->label('Employee')
                                ->weight('bold')
                                ->icon('heroicon-o-user-circle'),

                            TextEntry::make('transaction_type')
                                ->label('Transaction Type')
                                ->icon(fn (TransactionHistory $r) => $r->resolved_icon),

                            TextEntry::make('module')
                                ->label('Module')
                                ->badge()
                                ->color('info'),

                            TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->formatStateUsing(fn (string $state) => ucfirst($state))
                                ->color(fn (TransactionHistory $r) => TransactionHistory::statusColor($r->status)),
                        ]),

                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull()
                            ->prose(),
                    ]),

                // ── Actor ──────────────────────────────────────────────────
                Section::make('Actor Information')
                    ->icon('heroicon-o-user')
                    ->collapsed()
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('user.name')
                                ->label('System User')
                                ->default('System / Unknown'),

                            TextEntry::make('user.email')
                                ->label('Email')
                                ->default('—'),
                        ]),
                    ]),

                // ── Record Reference ───────────────────────────────────────
                Section::make('Record Reference')
                    ->icon('heroicon-o-link')
                    ->collapsed()
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('record_id')
                                ->label('Record ID')
                                ->default('—'),

                            TextEntry::make('record_url')
                                ->label('Record URL')
                                ->default('—')
                                ->limit(80),
                        ]),
                    ]),

                // ── Timestamps ────────────────────────────────────────────
                Section::make('Timestamps')
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('created_at')
                                ->label('Logged At')
                                ->dateTime('F j, Y  g:i:s A')
                                ->timezone('Asia/Manila'),

                            TextEntry::make('updated_at')
                                ->label('Last Updated')
                                ->dateTime('F j, Y  g:i:s A')
                                ->timezone('Asia/Manila'),
                        ]),
                    ]),
            ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Header Actions
    // ─────────────────────────────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            // Link to the original Filament resource record if URL is stored
            Action::make('view_record')
                ->label('View Original Record')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('primary')
                ->url(fn () => $this->record->record_url ?? null)
                ->openUrlInNewTab()
                ->visible(fn () => filled($this->record->record_url)),

            // Back to timeline
            Action::make('back')
                ->label('Back to Timeline')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(static::getResource()::getUrl('index')),
        ];
    }
}
