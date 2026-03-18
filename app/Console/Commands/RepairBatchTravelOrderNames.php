<?php

namespace App\Console\Commands;

use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Console\Command;

class RepairBatchTravelOrderNames extends Command
{
    protected $signature = 'travel-orders:repair-batch-names';
    protected $description = 'Fix batch travel orders where employee names were saved as empty/commas';

    public function handle(): void
    {
        $orders = TravelOrder::where('travel_type', 'batch')
            ->whereNotNull('employee_ids')
            ->with('creator')
            ->get();

        $fixed = 0;

        foreach ($orders as $order) {
            $ids = $order->employee_ids;

            if (empty($ids) || !is_array($ids)) {
                continue;
            }

            // Ensure the creator is always in the list
            $creatorId = $order->created_by;
            if ($creatorId && !in_array($creatorId, $ids)) {
                array_unshift($ids, $creatorId);
                $order->employee_ids = $ids;   // will be saved below
            }

            $employees = User::whereIn('id', $ids)
                ->get(['id', 'name', 'first_name', 'middle_name', 'last_name', 'suffix', 'position', 'role'])
                ->sortBy(fn($u) => array_search($u->id, $ids))
                ->map(fn($u) => [
                    'id' => $u->id,
                    'name' => filled($u->full_name) ? $u->full_name : $u->name,
                    'position' => $u->position ?? '',
                    'role' => $u->role ?? '',
                    'role_label' => User::getRoles()[$u->role] ?? ucwords(str_replace('_', ' ', $u->role ?? '')),
                ])
                ->values()
                ->toArray();

            $names = collect($employees)->pluck('name')->filter()->implode(', ');
            $currentName = trim($order->name ?? '');

            $isBroken = empty($currentName)
                || preg_match('/^[\s,]+$/', $currentName)
                || empty($order->employee_details);

            // Also fix if creator was missing from the list
            $creatorMissing = $creatorId && !collect($order->employee_details ?? [])->pluck('id')->contains($creatorId);

            if ($isBroken || $creatorMissing) {
                $order->update([
                    'employee_ids' => $ids,
                    'name' => $names,
                    'employee_details' => $employees,
                ]);
                $this->line("Fixed order #{$order->travel_order_no}: {$names}");
                $fixed++;
            }
        }

        $this->info("Done. Fixed {$fixed} batch travel order(s).");
    }
}
