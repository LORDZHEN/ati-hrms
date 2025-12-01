<?php

namespace App\Filament\Resources\TravelOrderResource\Pages;

use App\Filament\Resources\TravelOrderResource;
use App\Models\TravelOrder;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Notifications\TravelOrderStatusUpdated;
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

    protected function handleRecordCreation(array $data): TravelOrder
    {
        $adminUsers = User::where('role', 'admin')->get();

        // Batch travel
        if (isset($data['travel_type']) && $data['travel_type'] === 'batch' && !empty($data['employee_ids'])) {

            $records = [];

            foreach ($data['employee_ids'] as $employeeId) {
                $employee = User::find($employeeId);

                if (!$employee) {
                    continue;
                }

                $travelOrder = TravelOrder::create([
                    'travel_order_no' => $data['travel_order_no'], // optional: generate separate number if needed
                    'date' => $data['date'],
                    'status' => 'pending', // always start as pending
                    'travel_type' => 'solo', // treat each as solo for individual record
                    'solo_employee' => $employee->full_name ?? $employee->name,
                    'salary_per_annum' => $data['salary_per_annum'] ?? null,
                    'station' => $data['station'] ?? null,
                    'position' => $data['position'] ?? null,
                    'departure_date' => $data['departure_date'],
                    'return_date' => $data['return_date'],
                    'report_to' => $data['report_to'],
                    'destination' => $data['destination'],
                    'purpose_of_trip' => $data['purpose_of_trip'],
                    'created_by' => Auth::id(),
                ]);

                // Notify all admin users
                foreach ($adminUsers as $admin) {
                    $admin->notify(new TravelOrderStatusUpdated($travelOrder));
                }

                $records[] = $travelOrder;
            }

            Notification::make()
                ->title('Batch travel orders submitted successfully for ' . count($records) . ' employees')
                ->success()
                ->send();

            return $records[0]; // Filament expects a single record to return
        }

        // Solo travel
        $data['solo_employee'] = $data['solo_employee'] ?? Auth::user()->full_name;
        $travelOrder = parent::handleRecordCreation($data);

        // Notify all admin users for solo travel
        foreach ($adminUsers as $admin) {
            $admin->notify(new TravelOrderStatusUpdated($travelOrder));
        }

        Notification::make()
            ->title('Travel order submitted successfully')
            ->success()
            ->send();

        return $travelOrder;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
