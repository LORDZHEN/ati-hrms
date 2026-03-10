<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\LeaveApplication;
use App\Models\TravelOrder;
use App\Models\LocatorSlip;
use App\Models\Saln;
use App\Models\PersonalDataSheet;
use App\Models\User;
use App\Models\DailyTimeRecord;

use App\Observers\LeaveApplicationObserver;
use App\Observers\TravelOrderObserver;
use App\Observers\LocatorSlipObserver;
use App\Observers\SalnObserver;
use App\Observers\PersonalDataSheetObserver;
use App\Observers\UserObserver;
use App\Observers\DailyTimeRecordObserver;
use App\Models\EmployeeDtr;
use App\Observers\DtrObserver;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        LeaveApplication::observe(LeaveApplicationObserver::class);
        TravelOrder::observe(TravelOrderObserver::class);
        LocatorSlip::observe(LocatorSlipObserver::class);
        Saln::observe(SalnObserver::class);
        PersonalDataSheet::observe(PersonalDataSheetObserver::class);
        User::observe(UserObserver::class);
        EmployeeDtr::observe(DtrObserver::class);
    }
}
