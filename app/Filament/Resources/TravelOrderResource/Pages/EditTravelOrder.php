<?php

namespace App\Filament\Resources\TravelOrderResource\Pages;

use App\Filament\Resources\TravelOrderResource;
use App\Models\User;
use App\Notifications\TravelOrderSubmitted;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditTravelOrder extends EditRecord
{
    protected static string $resource = TravelOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(
                    fn() =>
                    Auth::user()->role === User::ROLE_REGULAR &&
                    $this->record->created_by === Auth::id() &&
                    $this->record->status === 'rejected'
                ),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Reset workflow state when a rejected record is resubmitted
        $data['status'] = 'pending';
        $data['rejection_remark'] = null;

        // ── Re-sync employee details in case the selection changed ──────────
        if (($data['travel_type'] ?? 'solo') === 'batch') {
            $ids = $data['employee_ids'] ?? [];

            // Always ensure the original creator stays in the list
            $creatorId = $this->record->created_by ?? Auth::id();
            if (!in_array($creatorId, $ids)) {
                array_unshift($ids, $creatorId);
            }
            $data['employee_ids'] = $ids;

            $employees = \App\Models\User::whereIn('id', $ids)
                ->get(['id', 'name', 'first_name', 'middle_name', 'last_name', 'suffix', 'position', 'role'])
                ->sortBy(fn($u) => array_search($u->id, $ids))
                ->map(fn($u) => [
                    'id' => $u->id,
                    'name' => filled($u->full_name) ? $u->full_name : $u->name,
                    'position' => $u->position ?? '',
                    'role' => $u->role ?? '',
                    'role_label' => \App\Models\User::getRoles()[$u->role] ?? ucwords(str_replace('_', ' ', $u->role ?? '')),
                ])
                ->values()
                ->toArray();

            $data['employee_details'] = $employees;
            $data['name'] = collect($employees)->pluck('name')->implode(', ');
            $data['position'] = null;
        } else {
            $user = Auth::user();
            $data['name'] = filled($user->full_name) ? $user->full_name : $user->name;
            $data['position'] = $user->position ?? null;
            $data['employee_ids'] = null;
            $data['employee_details'] = null;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $admins = User::where('role', User::ROLE_ADMIN)->get();

        foreach ($admins as $admin) {
            $admin->notify(new TravelOrderSubmitted($this->record));
        }

        Notification::make()
            ->title('Travel Order Resubmitted')
            ->body('Your travel order has been updated and resubmitted for review.')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Resubmit')
                ->submit('save')
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
