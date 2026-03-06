<?php

namespace App\Observers;

use App\Models\Saln;
use App\Models\TransactionHistory;

/**
 * Observer: SalnObserver
 * Register: Saln::observe(SalnObserver::class);
 */
class SalnObserver
{
    public function created(Saln $saln): void
    {
        // SALN stores name in separate columns — assemble best available label
        $employeeName = $saln->user?->full_name
            ?? trim(implode(' ', array_filter([
                $saln->declarant_first_name    ?? null,
                $saln->declarant_middle_initial ?? null,
                $saln->declarant_family_name   ?? null,
            ])))
            ?: 'Unknown Employee';

        $year = $saln->year ?? now()->year;

        TransactionHistory::log([
            'user_id'          => $saln->user_id ?? null,
            'employee_name'    => $employeeName,
            'transaction_type' => 'SALN Submission',
            'module'           => 'SALN',
            'description'      => "SALN for the year {$year} was uploaded and submitted.",
            'status'           => 'filed',
            'icon'             => 'heroicon-o-document-text',
            'color'            => 'rose',
            'record_id'        => $saln->id,
            'record_url'       => self::safeRoute(
                'filament.hrms.resources.salns.view', $saln->id
            ),
        ]);
    }

    private static function safeRoute(string $name, mixed $params): ?string
    {
        try { return route($name, $params); } catch (\Exception) { return null; }
    }
}
