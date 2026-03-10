<?php

namespace App\Filament\Resources\SalnResource\Pages;

use App\Filament\Resources\SalnResource;
use App\Models\User;
use App\Notifications\NewSalnFiled;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSaln extends CreateRecord
{
    protected static string $resource = SalnResource::class;

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Submit SALN')->submit('create')->color('primary'),
            Actions\Action::make('cancel')
                ->label('Cancel')->url($this->getResource()::getUrl('index'))->color('gray'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status'] = 'submitted'; // always starts as submitted

        // Normalize children date_of_birth
        if (!empty($data['children'])) {
            foreach ($data['children'] as &$child) {
                if (!empty($child['date_of_birth'])) {
                    try {
                        $child['date_of_birth'] = Carbon::parse($child['date_of_birth'])->format('Y-m-d');
                    } catch (\Exception $e) {
                    }
                }
            }
            unset($child);
        }

        // Calculate totals
        $data['total_assets'] = collect($data['realProperties'] ?? [])->sum('current_fair_market_value')
            + collect($data['personalProperties'] ?? [])->sum('acquisition_cost');
        $data['total_liabilities'] = collect($data['liabilities'] ?? [])->sum('outstanding_balance');
        $data['net_worth'] = $data['total_assets'] - $data['total_liabilities'];

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->calculateTotals();

        // Notify all admins
        User::where('role', 'admin')->get()->each(
            fn($admin) => $admin->notify(new NewSalnFiled($this->record))
        );

        \Filament\Notifications\Notification::make()
            ->title('SALN Submitted Successfully')
            ->body('Your Statement of Assets, Liabilities and Net Worth has been filed.')
            ->success()->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // ── Livewire helpers (keep your existing ones below) ─────────────────────

    public function addChild(): void
    {
        $this->data['children'][] = ['name' => '', 'date_of_birth' => '', 'age' => ''];
    }
    public function removeChild(int $index): void
    {
        unset($this->data['children'][$index]);
        $this->data['children'] = array_values($this->data['children']);
    }

    public function addRealProperty(): void
    {
        $this->data['realProperties'][] = ['description' => '', 'kind' => '', 'exact_location' => '', 'assessed_value' => '', 'current_fair_market_value' => '', 'acquisition_year' => '', 'mode_of_acquisition' => '', 'acquisition_cost' => ''];
    }
    public function removeRealProperty(int $index): void
    {
        unset($this->data['realProperties'][$index]);
        $this->data['realProperties'] = array_values($this->data['realProperties']);
    }

    public function addPersonalProperty(): void
    {
        $this->data['personalProperties'][] = ['description' => '', 'year_acquired' => '', 'acquisition_cost' => ''];
    }
    public function removePersonalProperty(int $index): void
    {
        unset($this->data['personalProperties'][$index]);
        $this->data['personalProperties'] = array_values($this->data['personalProperties']);
    }

    public function addLiability(): void
    {
        $this->data['liabilities'][] = ['nature' => '', 'name_of_creditors' => '', 'outstanding_balance' => ''];
    }
    public function removeLiability(int $index): void
    {
        unset($this->data['liabilities'][$index]);
        $this->data['liabilities'] = array_values($this->data['liabilities']);
    }

    public function addBusinessInterest(): void
    {
        $this->data['businessInterests'][] = ['name_of_entity' => '', 'business_address' => '', 'nature_of_business_interest' => '', 'date_of_acquisition' => ''];
    }
    public function removeBusinessInterest(int $index): void
    {
        unset($this->data['businessInterests'][$index]);
        $this->data['businessInterests'] = array_values($this->data['businessInterests']);
    }

    public function addRelativeInGovernment(): void
    {
        $this->data['relativesInGovernment'][] = ['name_of_relative' => '', 'relationship' => '', 'position' => '', 'name_of_agency_office_address' => ''];
    }
    public function removeRelativeInGovernment(int $index): void
    {
        unset($this->data['relativesInGovernment'][$index]);
        $this->data['relativesInGovernment'] = array_values($this->data['relativesInGovernment']);
    }
}
