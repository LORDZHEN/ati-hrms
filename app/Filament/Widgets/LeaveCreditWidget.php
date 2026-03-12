<?php

namespace App\Filament\Widgets;

use App\Models\LeaveCredit;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class LeaveCreditWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->role === User::ROLE_REGULAR;
    }

    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        // Fixed: now includes vacation_leave_max and sick_leave_max defaults
        $credit = LeaveCredit::firstOrCreate(
            ['user_id' => $user->id],
            [
                'vacation_leave_balance' => 0,
                'sick_leave_balance' => 0,
                'special_leave_balance' => 0,
                'mandatory_leave_balance' => 0,
                'vacation_leave_max' => 30,
                'sick_leave_max' => 30,
            ]
        );

        $fmt = fn(float $val): string => number_format($val, 3) . ' days';

        $color = function (float $balance, float $max): string {
            if ($max <= 0)
                return 'gray';
            $pct = $balance / $max;
            return match (true) {
                $pct <= 0.05 => 'danger',
                $pct <= 0.20 => 'warning',
                default => 'success',
            };
        };

        $chart = function (float $balance, float $max): array {
            if ($max <= 0)
                return [0, 0];
            $used = max(0, $max - $balance);
            return array_map(
                fn($i) => round($used * ($i / 6), 2),
                range(0, 6)
            );
        };

        $vBalance = (float) $credit->vacation_leave_balance;
        $vMax = (float) $credit->vacation_leave_max;
        $sBalance = (float) $credit->sick_leave_balance;
        $sMax = (float) $credit->sick_leave_max;
        $spBalance = (float) $credit->special_leave_balance;
        $mBalance = (float) $credit->mandatory_leave_balance;

        return [
            Stat::make('Vacation Leave', $fmt($vBalance))
                ->description('Max: ' . number_format($vMax, 0) . ' days')
                ->descriptionIcon('heroicon-o-sun')
                ->icon('heroicon-o-sun')
                ->color($color($vBalance, $vMax))
                ->chart($chart($vBalance, $vMax)),

            Stat::make('Sick Leave', $fmt($sBalance))
                ->description('Max: ' . number_format($sMax, 0) . ' days')
                ->descriptionIcon('heroicon-o-heart')
                ->icon('heroicon-o-heart')
                ->color($color($sBalance, $sMax))
                ->chart($chart($sBalance, $sMax)),

            Stat::make('Special Privilege Leave', $fmt($spBalance))
                ->description('Resets every January 1')
                ->descriptionIcon('heroicon-o-star')
                ->icon('heroicon-o-star')
                ->color('primary'),

            Stat::make('Mandatory / Forced Leave', $fmt($mBalance))
                ->description('Resets every January 1')
                ->descriptionIcon('heroicon-o-exclamation-circle')
                ->icon('heroicon-o-exclamation-circle')
                ->color('warning'),
        ];
    }
}
