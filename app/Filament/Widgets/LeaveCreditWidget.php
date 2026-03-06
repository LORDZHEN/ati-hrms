<?php

namespace App\Filament\Widgets;

use App\Models\LeaveCredit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * Displays the authenticated employee's leave credit balances.
 * Reads directly from the leave_credits table (managed by LeaveCreditService).
 * Only visible to users with role = 'employee'.
 */
class LeaveCreditWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->role === 'employee';
    }

    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        $credit = LeaveCredit::firstOrCreate(
            ['user_id' => $user->id],
            [
                'vacation_leave_balance' => 0,
                'sick_leave_balance' => 0,
                'special_leave_balance' => 0,
                'mandatory_leave_balance' => 0,
            ]
        );

        $fmt = fn(float $val): string => number_format($val, 3) . ' days';

        return [
            Stat::make('Vacation Leave', $fmt((float) $credit->vacation_leave_balance))
                ->description('Max: ' . number_format((float) $credit->vacation_leave_max, 0) . ' days')
                ->descriptionIcon('heroicon-o-sun')
                ->icon('heroicon-o-sun')
                ->color('success'),

            Stat::make('Sick Leave', $fmt((float) $credit->sick_leave_balance))
                ->description('Max: ' . number_format((float) $credit->sick_leave_max, 0) . ' days')
                ->descriptionIcon('heroicon-o-heart')
                ->icon('heroicon-o-heart')
                ->color('danger'),

            Stat::make('Special Privilege Leave', $fmt((float) $credit->special_leave_balance))
                ->description('Resets every January 1')
                ->descriptionIcon('heroicon-o-star')
                ->icon('heroicon-o-star')
                ->color('primary'),

            Stat::make('Mandatory / Forced Leave', $fmt((float) $credit->mandatory_leave_balance))
                ->description('Resets every January 1')
                ->descriptionIcon('heroicon-o-exclamation-circle')
                ->icon('heroicon-o-exclamation-circle')
                ->color('warning'),
        ];
    }
}
