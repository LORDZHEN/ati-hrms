<?php

namespace App\Observers;

use App\Models\PersonalDataSheet;
use App\Models\TransactionHistory;

/**
 * Observer: PersonalDataSheetObserver
 * Register: PersonalDataSheet::observe(PersonalDataSheetObserver::class);
 */
class PersonalDataSheetObserver
{
    public function created(PersonalDataSheet $pds): void
    {
        $employeeName = $pds->user?->full_name
            ?? $pds->user?->name
            ?? 'Unknown Employee';

        TransactionHistory::log([
            'user_id'          => $pds->user_id ?? null,
            'employee_name'    => $employeeName,
            'transaction_type' => 'PDS Submission',
            'module'           => 'PDS',
            'description'      => "Personal Data Sheet submitted for review.",
            'status'           => $pds->status ?? 'submitted',
            'icon'             => 'heroicon-o-identification',
            'color'            => 'teal',
            'record_id'        => $pds->id,
            'record_url'       => self::safeRoute(
                'filament.hrms.resources.pds.view', $pds->id
            ),
        ]);
    }

    public function updated(PersonalDataSheet $pds): void
    {
        if (! $pds->isDirty('status')) return;

        TransactionHistory::log([
            'user_id'          => $pds->user_id ?? null,
            'employee_name'    => $pds->user?->full_name ?? 'Unknown',
            'transaction_type' => 'PDS Submission',
            'module'           => 'PDS',
            'description'      => "Personal Data Sheet status updated to {$pds->status}.",
            'status'           => $pds->status,
            'icon'             => 'heroicon-o-identification',
            'color'            => match(strtolower($pds->status)) {
                'approved' => 'green',
                'rejected' => 'red',
                default    => 'teal',
            },
            'record_id'  => $pds->id,
            'record_url' => self::safeRoute('filament.hrms.resources.pds.view', $pds->id),
        ]);
    }

    private static function safeRoute(string $name, mixed $params): ?string
    {
        try { return route($name, $params); } catch (\Exception) { return null; }
    }
}
