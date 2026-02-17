<?php

namespace App\Filament\Resources\TravelOrderResource\Pages;

use App\Filament\Resources\TravelOrderResource;
use App\Models\TravelOrder;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use App\Notifications\TravelOrderSubmitted;
use Filament\Actions;
use Illuminate\Support\Str;

class CreateTravelOrder extends CreateRecord
{
    protected static string $resource = TravelOrderResource::class;

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Send')
                ->submit('create')
                ->color('primary'),

            Actions\Action::make('cancel')
                ->label('Cancel')
                ->url($this->getResource()::getUrl('index'))
                ->color('secondary'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate unique travel order number
        $data['travel_order_no'] = $this->generateUniqueTravelOrderNumber($data['date'] ?? now());

        // Ensure created_by is set
        $data['created_by'] = Auth::id();

        return $data;
    }

    protected function handleRecordCreation(array $data): TravelOrder
    {
        $adminUsers = User::where('role', 'admin')->get();

        // Batch travel - Create one main record + tagged copies for employees
        if (isset($data['travel_type']) && $data['travel_type'] === 'batch' && !empty($data['employee_ids'])) {
            return $this->handleBatchTravelCreation($data, $adminUsers);
        }

        // Solo travel - Single record
        return $this->handleSoloTravelCreation($data, $adminUsers);
    }

    /**
     * Handle batch travel order creation
     * Creates ONE main record for creator + individual tagged copies for each employee
     */
    protected function handleBatchTravelCreation(array $data, $adminUsers): TravelOrder
    {
        // Generate unique batch ID to link all records
        $batchId = time() . rand(1000, 9999); // Using integer instead of UUID

        // Get all employee names for the main record
        $employeeNames = User::whereIn('id', $data['employee_ids'])
            ->get()
            ->map(fn($user) => $user->full_name ?? $user->name)
            ->toArray();

        // Create MAIN batch record (shown to creator and admin)
        $mainTravelOrder = TravelOrder::create([
            'travel_order_no' => $data['travel_order_no'],
            'date' => $data['date'],
            'status' => 'pending',
            'travel_type' => 'batch',
            'name' => implode(', ', $employeeNames),
            'salary_per_annum' => $data['salary_per_annum'] ?? null,
            'station' => $data['station'] ?? null,
            'position' => $data['position'] ?? null,
            'departure_date' => $data['departure_date'],
            'return_date' => $data['return_date'],
            'report_to' => $data['report_to'],
            'destination' => $data['destination'],
            'purpose_of_trip' => $data['purpose_of_trip'],
            'created_by' => Auth::id(),
            'employee_ids' => $data['employee_ids'],
            'batch_id' => $batchId,
        ]);

        // Create TAGGED copies for each employee (shown in their "Tagged" tab)
        foreach ($data['employee_ids'] as $employeeId) {
            $employee = User::find($employeeId);

            if (!$employee) {
                continue;
            }

            // Generate unique order number for each tagged copy
            $taggedOrderNumber = $this->generateUniqueTravelOrderNumber($data['date']);

            TravelOrder::create([
                'travel_order_no' => $taggedOrderNumber,
                'date' => $data['date'],
                'status' => 'pending',
                'travel_type' => 'batch',
                'name' => $employee->full_name ?? $employee->name,
                'salary_per_annum' => $employee->salary ?? null,
                'station' => $data['station'] ?? null,
                'position' => $employee->position ?? $data['position'] ?? null,
                'departure_date' => $data['departure_date'],
                'return_date' => $data['return_date'],
                'report_to' => $data['report_to'],
                'destination' => $data['destination'],
                'purpose_of_trip' => $data['purpose_of_trip'],
                'created_by' => Auth::id(),
                'employee_ids' => [$employeeId],
                'batch_id' => $batchId,
            ]);
        }

        // Notify admins about the main batch order
        $this->notifyAdmins($adminUsers, $mainTravelOrder);

        Notification::make()
            ->title('Batch Travel Order Created')
            ->body('Successfully created batch travel order for ' . count($data['employee_ids']) . ' employee(s).')
            ->success()
            ->send();

        return $mainTravelOrder;
    }

    /**
     * Handle solo travel order creation
     */
    protected function handleSoloTravelCreation(array $data, $adminUsers): TravelOrder
    {
        // Solo employee name is auto-populated by model
        $data['name'] = Auth::user()->full_name ?? Auth::user()->name;

        $travelOrder = parent::handleRecordCreation($data);

        // Notify admins
        $this->notifyAdmins($adminUsers, $travelOrder);

        Notification::make()
            ->title('Travel Order Submitted')
            ->body('Your travel order has been submitted successfully.')
            ->success()
            ->send();

        return $travelOrder;
    }

    /**
     * Generate unique travel order number with database locking
     * Format: MM-YYYY-XXX (e.g., 02-2026-001, 02-2026-002)
     */
    protected function generateUniqueTravelOrderNumber($date): string
    {
        $date = \Carbon\Carbon::parse($date);
        $month = $date->format('m'); // 01, 02, 03, etc.
        $year = $date->format('Y');  // 2026

        return DB::transaction(function () use ($month, $year) {
            // Get the last travel order number for this month/year with lock
            $lastOrder = TravelOrder::where('travel_order_no', 'like', "{$month}-{$year}-%")
                ->lockForUpdate()
                ->orderByRaw('CAST(SUBSTRING_INDEX(travel_order_no, "-", -1) AS UNSIGNED) DESC')
                ->first();

            if ($lastOrder) {
                // Extract the sequence number from the last order
                $parts = explode('-', $lastOrder->travel_order_no);
                $lastSequence = (int) end($parts);
                $nextSequence = $lastSequence + 1;
            } else {
                $nextSequence = 1;
            }

            // Pad sequence to 3 digits (001, 002, 003, etc.)
            $sequence = str_pad($nextSequence, 3, '0', STR_PAD_LEFT);

            // Return format: MM-YYYY-XXX
            return "{$month}-{$year}-{$sequence}";
        });
    }

    /**
     * Notify admin users about new travel order submission
     */
    protected function notifyAdmins($adminUsers, TravelOrder $travelOrder): void
    {
        foreach ($adminUsers as $admin) {
            $admin->notify(new \App\Notifications\TravelOrderSubmitted($travelOrder));
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
