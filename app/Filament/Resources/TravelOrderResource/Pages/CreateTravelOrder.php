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
        $data['travel_order_no'] = $this->generateUniqueTravelOrderNumber($data['date'] ?? now());
        $data['created_by']      = Auth::id();
        $data['status']          = 'pending';

        return $data;
    }

    protected function handleRecordCreation(array $data): TravelOrder
    {
        $adminUsers = User::where('role', 'admin')->get();

        if (isset($data['travel_type']) && $data['travel_type'] === 'batch' && !empty($data['employee_ids'])) {
            return $this->handleBatchTravelCreation($data, $adminUsers);
        }

        return $this->handleSoloTravelCreation($data, $adminUsers);
    }

    /**
     * Handle batch travel order creation.
     * Creates one main record for the creator + individual tagged copies per employee.
     */
    protected function handleBatchTravelCreation(array $data, $adminUsers): TravelOrder
    {
        $batchId = time() . rand(1000, 9999);

        $employeeNames = User::whereIn('id', $data['employee_ids'])
            ->get()
            ->map(fn($user) => $user->full_name ?? $user->name)
            ->toArray();

        // Main batch record (visible to creator and admin)
        $mainTravelOrder = TravelOrder::create([
            'travel_order_no'  => $data['travel_order_no'],
            'date'             => $data['date'],
            'status'           => 'pending',
            'travel_type'      => 'batch',
            'name'             => implode(', ', $employeeNames),
            'salary_per_annum' => $data['salary_per_annum'] ?? null,
            'station'          => $data['station'] ?? null,
            'position'         => $data['position'] ?? null,
            'departure_date'   => $data['departure_date'],
            'return_date'      => $data['return_date'],
            'report_to'        => $data['report_to'],
            'destination'      => $data['destination'],
            'purpose_of_trip'  => $data['purpose_of_trip'],
            'created_by'       => Auth::id(),
            'employee_ids'     => $data['employee_ids'],
            'batch_id'         => $batchId,
        ]);

        // Tagged copies for each employee (visible in their "Tagged" tab)
        foreach ($data['employee_ids'] as $employeeId) {
            $employee = User::find($employeeId);

            if (!$employee) continue;

            TravelOrder::create([
                'travel_order_no'  => $this->generateUniqueTravelOrderNumber($data['date']),
                'date'             => $data['date'],
                'status'           => 'pending',
                'travel_type'      => 'batch',
                'name'             => $employee->full_name ?? $employee->name,
                'salary_per_annum' => $employee->salary ?? null,
                'station'          => $data['station'] ?? null,
                'position'         => $employee->position ?? $data['position'] ?? null,
                'departure_date'   => $data['departure_date'],
                'return_date'      => $data['return_date'],
                'report_to'        => $data['report_to'],
                'destination'      => $data['destination'],
                'purpose_of_trip'  => $data['purpose_of_trip'],
                'created_by'       => Auth::id(),
                'employee_ids'     => [$employeeId],
                'batch_id'         => $batchId,
            ]);
        }

        $this->notifyAdmins($adminUsers, $mainTravelOrder);

        Notification::make()
            ->title('Batch Travel Order Submitted')
            ->body('Successfully submitted batch travel order for ' . count($data['employee_ids']) . ' employee(s).')
            ->success()
            ->send();

        return $mainTravelOrder;
    }

    /**
     * Handle solo travel order creation.
     */
    protected function handleSoloTravelCreation(array $data, $adminUsers): TravelOrder
    {
        $data['name']   = Auth::user()->full_name ?? Auth::user()->name;
        $data['status'] = 'pending';

        $travelOrder = parent::handleRecordCreation($data);

        $this->notifyAdmins($adminUsers, $travelOrder);

        Notification::make()
            ->title('Travel Order Submitted')
            ->body('Your travel order has been submitted successfully.')
            ->success()
            ->send();

        return $travelOrder;
    }

    /**
     * Generate a unique travel order number with database locking.
     * Format: MM-YYYY-XXX (e.g., 02-2026-001)
     */
    protected function generateUniqueTravelOrderNumber($date): string
    {
        $date  = \Carbon\Carbon::parse($date);
        $month = $date->format('m');
        $year  = $date->format('Y');

        return DB::transaction(function () use ($month, $year) {
            $lastOrder = TravelOrder::where('travel_order_no', 'like', "{$month}-{$year}-%")
                ->lockForUpdate()
                ->orderByRaw('CAST(SUBSTRING_INDEX(travel_order_no, "-", -1) AS UNSIGNED) DESC')
                ->first();

            $parts        = explode('-', $lastOrder->travel_order_no ?? '');
            $nextSequence = $lastOrder
                ? ((int) end($parts)) + 1
                : 1;

            return "{$month}-{$year}-" . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Notify all admin users about a new travel order submission.
     */
    protected function notifyAdmins($adminUsers, TravelOrder $travelOrder): void
    {
        foreach ($adminUsers as $admin) {
            $admin->notify(new TravelOrderSubmitted($travelOrder));
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
