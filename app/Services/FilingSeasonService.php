<?php

namespace App\Services;

use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

/**
 * FilingSeasonService
 *
 * Central authority for whether filing season is currently active.
 * The flag is stored in the `settings` table (or config) and can be
 * toggled by an admin via the Settings panel.
 *
 * Anywhere in the app you need to check filing season:
 *   app(FilingSeasonService::class)->isEnabled()
 */
class FilingSeasonService
{
    /**
     * Returns true when filing season is currently open.
     *
     * Reads from the `filing_season_enabled` key in the `settings` table
     * (via the helper model below).  Falls back to the config value so
     * local environments can override without touching the DB.
     */
    public function isEnabled(): bool
    {
        if (class_exists(\App\Models\Setting::class)) {
            return (bool) \App\Models\Setting::getValue('filing_season_enabled', false);
        }

        return (bool) config('app.filing_season_enabled', false);
    }

    /**
     * Enable filing season.
     *
     * @param  bool $notify  Send in-app notifications to all regular employees.
     *                       Defaults to false so seeders/tests are unaffected.
     */
    public function enable(bool $notify = false): void
    {
        $this->setSetting(true);

        if ($notify) {
            $this->notifyEmployeesOpened();
        }
    }

    /**
     * Disable filing season.
     *
     * @param  bool $notify  Send in-app notifications to all regular employees.
     *                       Defaults to false so seeders/tests are unaffected.
     */
    public function disable(bool $notify = false): void
    {
        $this->setSetting(false);

        if ($notify) {
            $this->notifyEmployeesClosed();
        }
    }

    /**
     * Toggle the current state and return the new state.
     * Called by SystemSettings::toggleFilingSeason() via the UI button.
     *
     * @return bool  true = filing season just opened, false = just closed
     */
    public function toggle(bool $notify = true): bool
    {
        if ($this->isEnabled()) {
            $this->disable($notify);
            return false;
        }

        $this->enable($notify);
        return true;
    }

    // -------------------------------------------------------------------------

    private function setSetting(bool $value): void
    {
        if (class_exists(\App\Models\Setting::class)) {
            \App\Models\Setting::setValue('filing_season_enabled', $value);
        }
    }

    /**
     * Send "Filing Season Opened" to all regular employees via Filament's
     * database notification channel (appears in the bell icon).
     */
    private function notifyEmployeesOpened(): void
    {
        $employees = User::where('role', User::ROLE_REGULAR)->get();

        foreach ($employees as $employee) {
            Notification::make()
                ->title('📂 Filing Season is Now Open')
                ->body('The administrator has opened the filing season. You may now edit and resubmit your SALN and PDS if they have been unlocked.')
                ->icon('heroicon-o-lock-open')
                ->iconColor('success')
                ->actions([
                    Action::make('viewSaln')
                        ->label('View SALN')
                        ->url('/hrms/salns'),
                    Action::make('viewPds')
                        ->label('View PDS')
                        ->url('/hrms/pds'),
                ])
                ->sendToDatabase($employee);
        }
    }

    /**
     * Send "Filing Season Closed" to all regular employees via Filament's
     * database notification channel (appears in the bell icon).
     */
    private function notifyEmployeesClosed(): void
    {
        $employees = User::where('role', User::ROLE_REGULAR)->get();

        foreach ($employees as $employee) {
            Notification::make()
                ->title('🔒 Filing Season Has Closed')
                ->body('The administrator has closed the filing season. SALN and PDS editing is no longer available until the next filing season.')
                ->icon('heroicon-o-lock-closed')
                ->iconColor('danger')
                ->actions([
                    Action::make('viewDocs')
                        ->label('View My Documents')
                        ->url('/hrms/salns'),
                ])
                ->sendToDatabase($employee);
        }
    }
}
