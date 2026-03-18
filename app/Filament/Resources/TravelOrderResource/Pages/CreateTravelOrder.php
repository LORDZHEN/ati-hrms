<?php

namespace App\Filament\Resources\TravelOrderResource\Pages;

use App\Filament\Resources\TravelOrderResource;
use App\Models\TravelOrder;
use App\Models\User;
use App\Notifications\TravelOrderSubmitted;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTravelOrder extends CreateRecord
{
    protected static string $resource = TravelOrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // ── 1. Generate travel order number ────────────────────────────────
        $date  = \Carbon\Carbon::parse($data['date'] ?? now());
        $month = $date->format('m');
        $year  = $date->format('Y');

        $count = TravelOrder::where('travel_order_no', 'like', "{$month}-{$year}-%")
                ->count() + 1;

        $data['travel_order_no'] = $month . '-' . $year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        $data['status']          = 'pending';
        $data['created_by']      = auth()->id();

        // ── 2. Populate name / employee_details based on travel type ────────
        if (($data['travel_type'] ?? 'solo') === 'batch') {
            $ids = $data['employee_ids'] ?? [];

            // Always ensure the creator is included in the employee list
            $creatorId = auth()->id();
            if (! in_array($creatorId, $ids)) {
                array_unshift($ids, $creatorId);   // prepend so creator appears first
            }
            $data['employee_ids'] = $ids;

            $employees = User::whereIn('id', $ids)
                ->get(['id', 'name', 'first_name', 'middle_name', 'last_name', 'suffix', 'position', 'role'])
                ->sortBy(fn ($u) => array_search($u->id, $ids))
                ->map(fn ($u) => [
                    'id'         => $u->id,
                    'name'       => filled($u->full_name) ? $u->full_name : $u->name,
                    'position'   => $u->position ?? '',
                    'role'       => $u->role ?? '',
                    'role_label' => User::getRoles()[$u->role] ?? ucwords(str_replace('_', ' ', $u->role ?? '')),
                ])
                ->values()
                ->toArray();

            $data['employee_details'] = $employees;
            $data['name']             = collect($employees)->pluck('name')->implode(', ');
            $data['position']         = null;
        } else {
            // Solo: ensure employee_ids / employee_details are cleared
            $user                     = auth()->user();
            $data['name']             = filled($user->full_name) ? $user->full_name : $user->name;
            $data['position']         = $user->position ?? null;
            $data['employee_ids']     = null;
            $data['employee_details'] = null;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(new TravelOrderSubmitted($this->record));
        }
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Send')
                ->submit('create')
                ->color('primary')
                ->icon('heroicon-o-paper-airplane'),

            Actions\Action::make('cancel')
                ->label('Cancel')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->icon('heroicon-o-x-mark'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
