<?php

namespace App\Filament\Concerns;

use App\Services\FilingSeasonService;
use Illuminate\Database\Eloquent\Model;

/**
 * WorkflowHelper
 *
 * Shared helper methods used by both SalnResource and PersonalDataSheetResource
 * (and their page controllers) to determine editing eligibility.
 *
 * Centralising the logic here means a single place to update when the rules change.
 */
trait WorkflowHelper
{
    // -------------------------------------------------------------------------
    //  Core gate: can the CURRENT user edit this record?
    // -------------------------------------------------------------------------

    /**
     * Returns true when editing should be allowed for the given role + record.
     *
     * Rules:
     *  - Admins: always yes (they use a separate unlock action).
     *  - Employees:
     *      • status != approved             → allowed
     *      • status == approved + unlocked  → allowed ONLY when filing season is open
     *      • status == approved + locked    → denied
     */
    public static function canEmployeeEdit(Model $record): bool
    {
        $user = auth()->user();

        if ($user?->role === 'admin') {
            return true;
        }

        // Not the owner → deny (belt-and-suspenders; policy handles this too)
        if ($record->user_id !== $user?->id) {
            return false;
        }

        if ($record->status !== 'approved') {
            return true;
        }

        // Approved record: only editable when admin explicitly unlocked it
        // AND the filing season is currently open.
        if ($record->editing_unlocked && app(FilingSeasonService::class)->isEnabled()) {
            return true;
        }

        return false;
    }

    // -------------------------------------------------------------------------
    //  Form-level disable guard (use inside form schema closures)
    // -------------------------------------------------------------------------

    /**
     * Returns a closure suitable for ->disabled() on a Filament form component.
     *
     * Usage:
     *   TextInput::make('surname')->disabled(WorkflowHelper::formDisabledClosure())
     */
    public static function formDisabledClosure(): \Closure
    {
        return function ($record) {
            if (! $record) {
                return false; // create form — never disable
            }

            $user = auth()->user();

            if ($user?->role === 'admin') {
                return false; // admins are never disabled in the form
            }

            return ! static::canEmployeeEdit($record);
        };
    }

    // -------------------------------------------------------------------------
    //  Convenience booleans (use in visible() / disabled() closures on actions)
    // -------------------------------------------------------------------------

    public static function isApprovedAndLocked(Model $record): bool
    {
        return $record->status === 'approved' && ! $record->editing_unlocked;
    }

    public static function isUnlocked(Model $record): bool
    {
        return $record->status === 'approved' && $record->editing_unlocked;
    }

    public static function filingSeasonEnabled(): bool
    {
        return app(FilingSeasonService::class)->isEnabled();
    }
}
