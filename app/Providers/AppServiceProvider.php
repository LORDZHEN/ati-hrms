<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

// ── Models ────────────────────────────────────────────────────────────────────
use App\Models\DailyTimeRecord;
use App\Models\EmployeeDtr;
use App\Models\LeaveApplication;
use App\Models\LocatorSlip;
use App\Models\PersonalDataSheet;
use App\Models\Saln;
use App\Models\TravelOrder;
use App\Models\User;

// ── Observers ─────────────────────────────────────────────────────────────────
use App\Observers\DailyTimeRecordObserver;
use App\Observers\DtrObserver;
use App\Observers\LeaveApplicationObserver;
use App\Observers\LocatorSlipObserver;
use App\Observers\PersonalDataSheetObserver;
use App\Observers\SalnObserver;
use App\Observers\TravelOrderObserver;
use App\Observers\UserObserver;

// ── Policies ──────────────────────────────────────────────────────────────────
use App\Policies\PersonalDataSheetPolicy;
use App\Policies\SalnPolicy;

// ── Services ──────────────────────────────────────────────────────────────────
use App\Services\FilingSeasonService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * - Binds FilingSeasonService as a singleton so the DB is only queried
     *   once per request when checking the filing season flag.
     */
    public function register(): void
    {
        $this->app->singleton(FilingSeasonService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * Registers:
     *   1. All model observers (unchanged from your original)
     *   2. Laravel authorization policies for Saln and PersonalDataSheet
     */
    public function boot(): void
    {
        // ── Model Observers ───────────────────────────────────────────────────
        LeaveApplication::observe(LeaveApplicationObserver::class);
        TravelOrder::observe(TravelOrderObserver::class);
        LocatorSlip::observe(LocatorSlipObserver::class);
        Saln::observe(SalnObserver::class);
        PersonalDataSheet::observe(PersonalDataSheetObserver::class);
        User::observe(UserObserver::class);
        EmployeeDtr::observe(DtrObserver::class);

        // ── Authorization Policies ────────────────────────────────────────────
        //
        // These two lines wire the Laravel Gate to your custom policies so
        // Filament's canEdit() / canView() / canCreate() checks are enforced
        // at the HTTP level.
        //
        // SalnPolicy::update()            → controls employee edit access
        // PersonalDataSheetPolicy::update → same for PDS
        //
        // Admins bypass all checks via the policy's before() method.
        Gate::policy(Saln::class, SalnPolicy::class);
        Gate::policy(PersonalDataSheet::class, PersonalDataSheetPolicy::class);
    }
}
