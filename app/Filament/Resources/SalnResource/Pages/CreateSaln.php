<?php

namespace App\Filament\Resources\SalnResource\Pages;

use App\Filament\Resources\SalnResource;
use App\Models\User;
use App\Notifications\NewSalnFiled;
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
        $data['status'] = 'submitted';

        // ── Annex A totals ────────────────────────────────────────────────────
        $data['total_assets'] =
            collect($data['realProperties'] ?? [])->sum('current_fair_market_value')
            + collect($data['personalProperties'] ?? [])->sum('acquisition_cost');
        $data['total_liabilities'] = collect($data['liabilities'] ?? [])->sum('outstanding_balance');
        $data['net_worth'] = $data['total_assets'] - $data['total_liabilities'];

        // ── Annex B totals ────────────────────────────────────────────────────
        $data['annex_b_total_assets'] =
            collect($data['annexBRealProperties'] ?? [])->sum('current_fair_market_value')
            + collect($data['annexBPersonalProperties'] ?? [])->sum('acquisition_cost');
        $data['annex_b_total_liabilities'] = collect($data['annexBLiabilities'] ?? [])->sum('outstanding_balance');
        $data['annex_b_net_worth'] = $data['annex_b_total_assets'] - $data['annex_b_total_liabilities'];

        // ── Annex C totals ────────────────────────────────────────────────────
        $data['annex_c_total_assets'] =
            collect($data['annexCRealProperties'] ?? [])->sum('current_fair_market_value')
            + collect($data['annexCPersonalProperties'] ?? [])->sum('acquisition_cost');
        $data['annex_c_total_liabilities'] = collect($data['annexCLiabilities'] ?? [])->sum('outstanding_balance');
        $data['annex_c_net_worth'] = $data['annex_c_total_assets'] - $data['annex_c_total_liabilities'];

        return $data;
    }

    protected function afterCreate(): void
    {
        // Save Annex B relationships
        $this->saveAnnexRelationships('annexBRealProperties', 'annexBRealProperties');
        $this->saveAnnexRelationships('annexBPersonalProperties', 'annexBPersonalProperties');
        $this->saveAnnexRelationships('annexBLiabilities', 'annexBLiabilities');
        $this->saveAnnexRelationships('annexBBusinessInterests', 'annexBBusinessInterests');

        // Save Annex C relationships
        $this->saveAnnexRelationships('annexCRealProperties', 'annexCRealProperties');
        $this->saveAnnexRelationships('annexCPersonalProperties', 'annexCPersonalProperties');
        $this->saveAnnexRelationships('annexCLiabilities', 'annexCLiabilities');
        $this->saveAnnexRelationships('annexCBusinessInterests', 'annexCBusinessInterests');

        // Recalculate all totals from DB
        $this->record->calculateTotals();

        User::where('role', 'admin')->get()->each(
            fn($admin) => $admin->notify(new NewSalnFiled($this->record))
        );

        \Filament\Notifications\Notification::make()
            ->title('SALN Submitted Successfully')
            ->body('Your Statement of Assets, Liabilities and Net Worth has been filed.')
            ->success()->send();
    }

    /**
     * Persist Annex B/C array data from $this->data into the record relationship.
     */
    protected function saveAnnexRelationships(string $dataKey, string $relationshipMethod): void
    {
        $items = $this->data[$dataKey] ?? [];
        if (empty($items))
            return;

        foreach ($items as $item) {
            $filtered = array_filter($item, fn($v) => $v !== '' && $v !== null);
            if (!empty($filtered)) {
                $this->record->{$relationshipMethod}()->create($item);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // =========================================================================
    //  LIVEWIRE HELPERS — ANNEX A
    // =========================================================================

    public function addChild(): void
    {
        $this->data['children'][] = ['name' => '', 'age' => ''];
    }

    public function removeChild(int $index): void
    {
        array_splice($this->data['children'], $index, 1);
    }

    public function addRealProperty(): void
    {
        $this->data['realProperties'][] = [
            'description' => '',
            'kind' => '',
            'exact_location' => '',
            'assessed_value' => '',
            'current_fair_market_value' => '',
            'acquisition_year' => '',
            'mode_of_acquisition' => '',
            'acquisition_cost' => '',
        ];
    }

    public function removeRealProperty(int $index): void
    {
        array_splice($this->data['realProperties'], $index, 1);
    }

    public function addPersonalProperty(): void
    {
        $this->data['personalProperties'][] = [
            'description' => '',
            'year_acquired' => '',
            'acquisition_cost' => '',
        ];
    }

    public function removePersonalProperty(int $index): void
    {
        array_splice($this->data['personalProperties'], $index, 1);
    }

    public function addLiability(): void
    {
        $this->data['liabilities'][] = [
            'nature' => '',
            'name_of_creditors' => '',
            'outstanding_balance' => '',
        ];
    }

    public function removeLiability(int $index): void
    {
        array_splice($this->data['liabilities'], $index, 1);
    }

    public function addBusinessInterest(): void
    {
        $this->data['businessInterests'][] = [
            'name_of_entity' => '',
            'business_address' => '',
            'nature_of_business_interest' => '',
            'date_of_acquisition' => '',
        ];
    }

    public function removeBusinessInterest(int $index): void
    {
        array_splice($this->data['businessInterests'], $index, 1);
    }

    public function addRelativeInGovernment(): void
    {
        $this->data['relativesInGovernment'][] = [
            'name_of_relative' => '',
            'relationship' => '',
            'position' => '',
            'name_of_agency_office_address' => '',
        ];
    }

    public function removeRelativeInGovernment(int $index): void
    {
        array_splice($this->data['relativesInGovernment'], $index, 1);
    }

    // =========================================================================
    //  LIVEWIRE HELPERS — ANNEX B (Declarant's exclusive properties)
    // =========================================================================

    public function addAnnexBRealProperty(): void
    {
        $this->data['annexBRealProperties'][] = [
            'description' => '',
            'kind' => '',
            'exact_location' => '',
            'assessed_value' => '',
            'current_fair_market_value' => '',
            'acquisition_year' => '',
            'mode_of_acquisition' => '',
            'acquisition_cost' => '',
        ];
    }

    public function removeAnnexBRealProperty(int $index): void
    {
        array_splice($this->data['annexBRealProperties'], $index, 1);
    }

    public function addAnnexBPersonalProperty(): void
    {
        $this->data['annexBPersonalProperties'][] = [
            'description' => '',
            'year_acquired' => '',
            'acquisition_cost' => '',
        ];
    }

    public function removeAnnexBPersonalProperty(int $index): void
    {
        array_splice($this->data['annexBPersonalProperties'], $index, 1);
    }

    public function addAnnexBLiability(): void
    {
        $this->data['annexBLiabilities'][] = [
            'nature' => '',
            'name_of_creditors' => '',
            'outstanding_balance' => '',
        ];
    }

    public function removeAnnexBLiability(int $index): void
    {
        array_splice($this->data['annexBLiabilities'], $index, 1);
    }

    public function addAnnexBBusinessInterest(): void
    {
        $this->data['annexBBusinessInterests'][] = [
            'name_of_entity' => '',
            'business_address' => '',
            'nature_of_business_interest' => '',
            'date_of_acquisition' => '',
        ];
    }

    public function removeAnnexBBusinessInterest(int $index): void
    {
        array_splice($this->data['annexBBusinessInterests'], $index, 1);
    }

    // =========================================================================
    //  LIVEWIRE HELPERS — ANNEX C (Spouse & children's exclusive properties)
    // =========================================================================

    public function addAnnexCRealProperty(): void
    {
        $this->data['annexCRealProperties'][] = [
            'description' => '',
            'kind' => '',
            'exact_location' => '',
            'assessed_value' => '',
            'current_fair_market_value' => '',
            'acquisition_year' => '',
            'mode_of_acquisition' => '',
            'acquisition_cost' => '',
        ];
    }

    public function removeAnnexCRealProperty(int $index): void
    {
        array_splice($this->data['annexCRealProperties'], $index, 1);
    }

    public function addAnnexCPersonalProperty(): void
    {
        $this->data['annexCPersonalProperties'][] = [
            'description' => '',
            'year_acquired' => '',
            'acquisition_cost' => '',
        ];
    }

    public function removeAnnexCPersonalProperty(int $index): void
    {
        array_splice($this->data['annexCPersonalProperties'], $index, 1);
    }

    public function addAnnexCLiability(): void
    {
        $this->data['annexCLiabilities'][] = [
            'nature' => '',
            'name_of_creditors' => '',
            'outstanding_balance' => '',
        ];
    }

    public function removeAnnexCLiability(int $index): void
    {
        array_splice($this->data['annexCLiabilities'], $index, 1);
    }

    public function addAnnexCBusinessInterest(): void
    {
        $this->data['annexCBusinessInterests'][] = [
            'name_of_entity' => '',
            'business_address' => '',
            'nature_of_business_interest' => '',
            'date_of_acquisition' => '',
        ];
    }

    public function removeAnnexCBusinessInterest(int $index): void
    {
        array_splice($this->data['annexCBusinessInterests'], $index, 1);
    }
}
