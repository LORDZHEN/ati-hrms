<?php

namespace App\Filament\Resources\SalnResource\Pages;

use App\Filament\Resources\SalnResource;
use Filament\Resources\Pages\CreateRecord;
use App\Notifications\NewSalnFiled;
use App\Models\User;
use Filament\Actions;
use Illuminate\Support\Facades\Notification;

class CreateSaln extends CreateRecord
{
    protected static string $resource = SalnResource::class;

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Submit SALN')
                ->submit('create')
                ->color('primary'),

            Actions\Action::make('cancel')
                ->label('Cancel')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        // Normalize date_of_birth on every child so we always store Y-m-d,
        // never the full ISO datetime Filament may pass through.
        if (!empty($data['children'])) {
            foreach ($data['children'] as &$child) {
                if (!empty($child['date_of_birth'])) {
                    try {
                        $child['date_of_birth'] = \Carbon\Carbon::parse($child['date_of_birth'])->format('Y-m-d');
                    } catch (\Exception $e) {
                    }
                }
            }
            unset($child);
        }

        $realPropertiesTotal = collect($data['realProperties'] ?? [])->sum('current_fair_market_value');
        $personalPropertiesTotal = collect($data['personalProperties'] ?? [])->sum('acquisition_cost');

        $data['total_assets'] = $realPropertiesTotal + $personalPropertiesTotal;
        $data['total_liabilities'] = collect($data['liabilities'] ?? [])->sum('outstanding_balance');
        $data['net_worth'] = $data['total_assets'] - $data['total_liabilities'];

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->calculateTotals();

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewSalnFiled($this->record));
        }

        \Filament\Notifications\Notification::make()
            ->title('SALN Submitted Successfully')
            ->body('Your Statement of Assets, Liabilities and Net Worth has been filed.')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function addChild(): void
    {
        $children = $this->data['children'] ?? [];
        $children[] = ['name' => '', 'date_of_birth' => '', 'age' => ''];
        $this->data['children'] = $children;
    }

    public function removeChild(int $index): void
    {
        $children = $this->data['children'] ?? [];
        unset($children[$index]);
        $this->data['children'] = array_values($children);
    }

    public function addRealProperty(): void
    {
        $items = $this->data['realProperties'] ?? [];
        $items[] = ['description' => '', 'kind' => '', 'exact_location' => '', 'assessed_value' => '', 'current_fair_market_value' => '', 'acquisition_year' => '', 'mode_of_acquisition' => '', 'acquisition_cost' => ''];
        $this->data['realProperties'] = $items;
    }

    public function removeRealProperty(int $index): void
    {
        $items = $this->data['realProperties'] ?? [];
        unset($items[$index]);
        $this->data['realProperties'] = array_values($items);
    }

    public function addPersonalProperty(): void
    {
        $items = $this->data['personalProperties'] ?? [];
        $items[] = ['description' => '', 'year_acquired' => '', 'acquisition_cost' => ''];
        $this->data['personalProperties'] = $items;
    }

    public function removePersonalProperty(int $index): void
    {
        $items = $this->data['personalProperties'] ?? [];
        unset($items[$index]);
        $this->data['personalProperties'] = array_values($items);
    }

    public function addLiability(): void
    {
        $items = $this->data['liabilities'] ?? [];
        $items[] = ['nature' => '', 'name_of_creditors' => '', 'outstanding_balance' => ''];
        $this->data['liabilities'] = $items;
    }

    public function removeLiability(int $index): void
    {
        $items = $this->data['liabilities'] ?? [];
        unset($items[$index]);
        $this->data['liabilities'] = array_values($items);
    }

    public function addBusinessInterest(): void
    {
        $items = $this->data['businessInterests'] ?? [];
        $items[] = ['name_of_entity' => '', 'business_address' => '', 'nature_of_business_interest' => '', 'date_of_acquisition' => ''];
        $this->data['businessInterests'] = $items;
    }

    public function removeBusinessInterest(int $index): void
    {
        $items = $this->data['businessInterests'] ?? [];
        unset($items[$index]);
        $this->data['businessInterests'] = array_values($items);
    }

    public function addRelativeInGovernment(): void
    {
        $items = $this->data['relativesInGovernment'] ?? [];
        $items[] = ['name_of_relative' => '', 'relationship' => '', 'position' => '', 'name_of_agency_office_address' => ''];
        $this->data['relativesInGovernment'] = $items;
    }

    public function removeRelativeInGovernment(int $index): void
    {
        $items = $this->data['relativesInGovernment'] ?? [];
        unset($items[$index]);
        $this->data['relativesInGovernment'] = array_values($items);
    }
}
