<?php

namespace App\Observers;

use App\Models\PersonalDataSheet;
use App\Models\TransactionHistory;
use App\Models\User;

/**
 * Observer: PersonalDataSheetObserver
 *
 * FIX: $pds->user is null at observer fire time because:
 *  - On created(): the model was just INSERT'd; the relationship is not
 *    eager-loaded on the fresh instance returned by Eloquent.
 *  - On updated(): the loaded instance may not have the relation cached.
 *
 * Solution: always resolve the name via User::find($pds->user_id) directly.
 * This is one extra query per event but guarantees the correct name.
 *
 * Register in AppServiceProvider::boot():
 *   PersonalDataSheet::observe(PersonalDataSheetObserver::class);
 */
class PersonalDataSheetObserver
{
    public function created(PersonalDataSheet $pds): void
    {
        TransactionHistory::log([
            'user_id' => $pds->user_id ?? null,
            'employee_name' => $this->resolveName($pds),
            'transaction_type' => 'PDS SUBMISSION',
            'module' => 'PDS',
            'description' => 'Personal Data Sheet submitted for review.',
            'status' => $pds->status ?? 'submitted',
            'icon' => 'heroicon-o-identification',
            'color' => 'teal',
            'record_id' => $pds->id,
            'record_url' => self::safeRoute(
                'filament.hrms.resources.pds.view',
                $pds->id
            ),
        ]);
    }

    public function updated(PersonalDataSheet $pds): void
    {
        if (!$pds->isDirty('status')) {
            return;
        }

        TransactionHistory::log([
            'user_id' => $pds->user_id ?? null,
            'employee_name' => $this->resolveName($pds),
            'transaction_type' => 'PDS SUBMISSION',
            'module' => 'PDS',
            'description' => "Personal Data Sheet status updated to {$pds->status}.",
            'status' => $pds->status,
            'icon' => 'heroicon-o-identification',
            'color' => match (strtolower($pds->status)) {
                'approved' => 'green',
                'disapproved' => 'red',
                default => 'teal',
            },
            'record_id' => $pds->id,
            'record_url' => self::safeRoute(
                'filament.hrms.resources.pds.view',
                $pds->id
            ),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Resolve the employee name from a PDS record.
     *
     * Priority:
     *   1. PDS own name fields (surname + first_name already saved to DB)
     *   2. User model looked up by user_id (one extra query, guaranteed fresh)
     *   3. Hard fallback
     *
     * We deliberately avoid $pds->user because the relationship is not loaded
     * on the model instance that Eloquent passes to the observer, and calling
     * it triggers a lazy-load that may return null on a brand-new record
     * before the connection transaction fully commits.
     */
    private function resolveName(PersonalDataSheet $pds): string
    {
        // 1. PDS itself has name fields already persisted
        $fromPds = trim(implode(' ', array_filter([
            $pds->first_name,
            $pds->middle_name,
            $pds->surname,
            $pds->name_extension,
        ])));

        if (filled($fromPds)) {
            return $fromPds;
        }

        // 2. Look up the User directly — avoids stale relationship cache
        if ($pds->user_id) {
            $user = User::find($pds->user_id);
            if ($user) {
                return $user->full_name ?? $user->name ?? "Employee #{$pds->user_id}";
            }
        }

        // 3. Hard fallback
        return 'Unknown Employee';
    }

    private static function safeRoute(string $name, mixed $params): ?string
    {
        try {
            return route($name, $params);
        } catch (\Exception) {
            return null;
        }
    }
}
