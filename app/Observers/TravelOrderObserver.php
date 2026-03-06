<?php

namespace App\Observers;

use App\Models\TravelOrder;
use App\Models\TransactionHistory;

/**
 * Observer: TravelOrderObserver
 *
 * Register in AppServiceProvider::boot():
 *   TravelOrder::observe(TravelOrderObserver::class);
 */
class TravelOrderObserver
{
    public function created(TravelOrder $order): void
    {
        $employeeName = $order->creator?->full_name
            ?? $order->creator?->name
            ?? $order->name
            ?? 'Unknown Employee';

        TransactionHistory::log([
            'user_id'          => $order->created_by ?? $order->user_id ?? null,
            'employee_name'    => $employeeName,
            'transaction_type' => 'Travel Order',
            'module'           => 'Travel',
            'description'      => "Travel order created" .
                                  ($order->destination ? " to {$order->destination}" : '') .
                                  ($order->purpose     ? ": {$order->purpose}"       : '') . '.',
            'status'           => $order->status ?? 'pending',
            'icon'             => 'heroicon-o-briefcase',
            'color'            => 'amber',
            'record_id'        => $order->id,
            'record_url'       => self::safeRoute(
                'filament.hrms.resources.travel-orders.view', $order->id
            ),
        ]);
    }

    public function updated(TravelOrder $order): void
    {
        if (! $order->isDirty('status')) {
            return;
        }

        $employeeName = $order->creator?->full_name
            ?? $order->creator?->name
            ?? $order->name
            ?? 'Unknown Employee';

        TransactionHistory::log([
            'user_id'          => $order->created_by ?? $order->user_id ?? null,
            'employee_name'    => $employeeName,
            'transaction_type' => 'Travel Order',
            'module'           => 'Travel',
            'description'      => "Travel order status updated to {$order->status}.",
            'status'           => $order->status,
            'icon'             => 'heroicon-o-briefcase',
            'color'            => match(strtolower($order->status)) {
                'approved' => 'green',
                'rejected' => 'red',
                default    => 'amber',
            },
            'record_id'        => $order->id,
            'record_url'       => self::safeRoute(
                'filament.hrms.resources.travel-orders.view', $order->id
            ),
        ]);
    }

    private static function safeRoute(string $name, mixed $params): ?string
    {
        try { return route($name, $params); } catch (\Exception) { return null; }
    }
}
