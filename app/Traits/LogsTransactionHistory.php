<?php

namespace App\Traits;

use App\Models\TransactionHistory;
use Illuminate\Support\Facades\Auth;

/**
 * Trait: LogsTransactionHistory
 *
 * Mix this trait into any Filament Resource, Livewire component,
 * or service class that needs to log HRMS transactions.
 *
 * Usage inside a resource's mutateFormDataBeforeCreate():
 *
 *   $this->logTransaction('Leave Application', 'Leave', [
 *       'description' => 'Filed a sick leave for 3 days',
 *       'status'      => 'pending',
 *       'record_id'   => $record->id,
 *       'record_url'  => route('filament.hrms.resources.leave-applications.view', $record->id),
 *   ]);
 */
trait LogsTransactionHistory
{
    /**
     * Log a transaction with automatic actor resolution.
     *
     * @param  string  $transactionType  Human-readable type: "Leave Application", "Travel Order" …
     * @param  string  $module           Module slug: Leave | Travel | Locator | SALN | PDS | Employee | DTR
     * @param  array   $extras           Optional overrides: description, status, icon, color, record_id, record_url
     */
    protected function logTransaction(
        string $transactionType,
        string $module,
        array  $extras = []
    ): void {
        $user = Auth::user();

        TransactionHistory::log(array_merge([
            'user_id'          => $user?->id,
            'employee_name'    => $user?->full_name ?? $user?->name ?? 'System',
            'transaction_type' => $transactionType,
            'module'           => $module,
            'description'      => $extras['description'] ?? "{$transactionType} was submitted.",
            'status'           => $extras['status']      ?? 'pending',
            'icon'             => $extras['icon']        ?? TransactionHistory::moduleIcon($module),
            'color'            => $extras['color']       ?? 'gray',
            'record_id'        => $extras['record_id']   ?? null,
            'record_url'       => $extras['record_url']  ?? null,
        ], $extras));
    }
}
