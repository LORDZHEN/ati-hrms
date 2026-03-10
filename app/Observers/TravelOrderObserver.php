<?php

namespace App\Observers;

use App\Models\TravelOrder;
use App\Models\TransactionHistory;
use App\Models\User;

class TravelOrderObserver
{
    public function created(TravelOrder $order): void
    {
        // created_by is the confirmed FK to users.id (see creator() relationship)
        // For batch orders, we log one entry per employee in employee_ids.
        // For solo orders, we log a single entry for the creator.

        if ($order->travel_type === 'batch' && !empty($order->employee_ids)) {
            $this->logBatch($order, 'created');
        } else {
            $this->logSolo($order, 'created');
        }
    }

    public function updated(TravelOrder $order): void
    {
        if (!$order->isDirty('status')) {
            return;
        }

        if ($order->travel_type === 'batch' && !empty($order->employee_ids)) {
            $this->logBatch($order, 'updated');
        } else {
            $this->logSolo($order, 'updated');
        }
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Log a single entry for the creator (solo travel order).
     */
    private function logSolo(TravelOrder $order, string $event): void
    {
        $userId = $order->created_by ?? null;
        $user = User::find($userId);

        $employeeName = $user?->full_name
            ?? $user?->name
            ?? 'Unknown Employee';

        TransactionHistory::log(
            $this->buildPayload($order, $event, $userId, $employeeName)
        );
    }

    /**
     * Log one entry per employee in a batch travel order.
     * Each employee gets their own transaction history row so they
     * can each see it in their own timeline.
     */
    private function logBatch(TravelOrder $order, string $event): void
    {
        $employees = User::whereIn('id', $order->employee_ids)->get()->keyBy('id');

        foreach ($order->employee_ids as $employeeId) {
            $user = $employees->get($employeeId);
            $employeeName = $user?->full_name ?? $user?->name ?? "Employee #{$employeeId}";

            TransactionHistory::log(
                $this->buildPayload($order, $event, $employeeId, $employeeName)
            );
        }
    }

    /**
     * Build the shared payload array for both solo and batch entries.
     */
    private function buildPayload(
        TravelOrder $order,
        string $event,
        ?int $userId,
        string $employeeName
    ): array {
        $isCreated = $event === 'created';

        $description = $isCreated
            ? 'Travel order created' .
            ($order->destination ? " to {$order->destination}" : '') .
            ($order->purpose_of_trip ? ": {$order->purpose_of_trip}" : '') . '.'
            : "Travel order status updated to {$order->status}.";

        return [
            'user_id' => $userId,
            'employee_name' => $employeeName,
            'transaction_type' => 'Travel Order',
            'module' => 'Travel',
            'description' => $description,
            'status' => $order->status ?? 'pending',
            'icon' => 'heroicon-o-briefcase',
            'color' => $isCreated ? 'amber' : match (strtolower($order->status)) {
                'approved' => 'green',
                'rejected' => 'red',
                'recommended' => 'blue',
                default => 'amber',
            },
            'record_id' => $order->id,
            'record_url' => self::safeRoute(
                'filament.hrms.resources.travel-orders.view',
                $order->id
            ),
        ];
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
