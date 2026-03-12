<?php

namespace App\Filament\Resources\SalnResource\Pages;

use App\Filament\Resources\SalnResource;
use App\Models\User;
use App\Notifications\NewSalnFiled;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSaln extends EditRecord
{
    protected static string $resource = SalnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Print SALN')->icon('heroicon-o-printer')->color('success')
                ->url(fn() => route('saln.print', $this->record))->openUrlInNewTab(),

            Actions\DeleteAction::make()
                ->visible(fn() => auth()->user()?->role === 'admin'),
        ];
    }

    /**
     * Pre-populate Annex B & C data arrays when the edit form loads.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->record;

        // ── Annex B ───────────────────────────────────────────────────────────
        $data['annexBRealProperties']    = $record->annexBRealProperties->map->only([
            'id', 'description', 'kind', 'exact_location', 'assessed_value',
            'current_fair_market_value', 'acquisition_year', 'mode_of_acquisition', 'acquisition_cost',
        ])->values()->toArray();

        $data['annexBPersonalProperties'] = $record->annexBPersonalProperties->map->only([
            'id', 'description', 'year_acquired', 'acquisition_cost',
        ])->values()->toArray();

        $data['annexBLiabilities'] = $record->annexBLiabilities->map->only([
            'id', 'nature', 'name_of_creditors', 'outstanding_balance',
        ])->values()->toArray();

        $data['annexBBusinessInterests'] = $record->annexBBusinessInterests->map->only([
            'id', 'name_of_entity', 'business_address', 'nature_of_business_interest', 'date_of_acquisition',
        ])->values()->toArray();

        // ── Annex C ───────────────────────────────────────────────────────────
        $data['annexCRealProperties']    = $record->annexCRealProperties->map->only([
            'id', 'description', 'kind', 'exact_location', 'assessed_value',
            'current_fair_market_value', 'acquisition_year', 'mode_of_acquisition', 'acquisition_cost',
        ])->values()->toArray();

        $data['annexCPersonalProperties'] = $record->annexCPersonalProperties->map->only([
            'id', 'description', 'year_acquired', 'acquisition_cost',
        ])->values()->toArray();

        $data['annexCLiabilities'] = $record->annexCLiabilities->map->only([
            'id', 'nature', 'name_of_creditors', 'outstanding_balance',
        ])->values()->toArray();

        $data['annexCBusinessInterests'] = $record->annexCBusinessInterests->map->only([
            'id', 'name_of_entity', 'business_address', 'nature_of_business_interest', 'date_of_acquisition',
        ])->values()->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (auth()->user()?->role !== 'admin') {
            unset($data['person_administering_oath'], $data['subscribed_sworn_date']);
            $data['status'] = 'submitted';
        }

        // ── Annex A totals ────────────────────────────────────────────────────
        $data['total_assets'] =
            collect($data['realProperties'] ?? [])->sum('current_fair_market_value')
            + collect($data['personalProperties'] ?? [])->sum('acquisition_cost');
        $data['total_liabilities'] = collect($data['liabilities'] ?? [])->sum('outstanding_balance');
        $data['net_worth']         = $data['total_assets'] - $data['total_liabilities'];

        // ── Annex B totals ────────────────────────────────────────────────────
        $data['annex_b_total_assets'] =
            collect($data['annexBRealProperties'] ?? [])->sum('current_fair_market_value')
            + collect($data['annexBPersonalProperties'] ?? [])->sum('acquisition_cost');
        $data['annex_b_total_liabilities'] = collect($data['annexBLiabilities'] ?? [])->sum('outstanding_balance');
        $data['annex_b_net_worth']         = $data['annex_b_total_assets'] - $data['annex_b_total_liabilities'];

        // ── Annex C totals ────────────────────────────────────────────────────
        $data['annex_c_total_assets'] =
            collect($data['annexCRealProperties'] ?? [])->sum('current_fair_market_value')
            + collect($data['annexCPersonalProperties'] ?? [])->sum('acquisition_cost');
        $data['annex_c_total_liabilities'] = collect($data['annexCLiabilities'] ?? [])->sum('outstanding_balance');
        $data['annex_c_net_worth']         = $data['annex_c_total_assets'] - $data['annex_c_total_liabilities'];

        return $data;
    }

    protected function afterSave(): void
    {
        // Sync Annex B relationships
        $this->syncAnnexRelationships('annexBRealProperties',    'annexBRealProperties');
        $this->syncAnnexRelationships('annexBPersonalProperties','annexBPersonalProperties');
        $this->syncAnnexRelationships('annexBLiabilities',       'annexBLiabilities');
        $this->syncAnnexRelationships('annexBBusinessInterests', 'annexBBusinessInterests');

        // Sync Annex C relationships
        $this->syncAnnexRelationships('annexCRealProperties',    'annexCRealProperties');
        $this->syncAnnexRelationships('annexCPersonalProperties','annexCPersonalProperties');
        $this->syncAnnexRelationships('annexCLiabilities',       'annexCLiabilities');
        $this->syncAnnexRelationships('annexCBusinessInterests', 'annexCBusinessInterests');

        $this->record->calculateTotals();
        $this->record->updateQuietly(['resubmitted_at' => now()]);

        User::where('role', 'admin')->get()->each(
            fn($admin) => $admin->notify(new NewSalnFiled($this->record))
        );

        Notification::make()
            ->title('SALN Resubmitted Successfully')
            ->body('Your SALN has been updated and filed for review.')
            ->success()->send();
    }

    /**
     * Sync (delete-then-recreate) Annex B/C items for a given relationship.
     * Items with an 'id' are updated; new items (no 'id') are created; any
     * existing ids NOT present in the new data are deleted.
     */
    protected function syncAnnexRelationships(string $dataKey, string $relationshipMethod): void
    {
        $items      = $this->data[$dataKey] ?? [];
        $relation   = $this->record->{$relationshipMethod}();
        $existingIds = $relation->pluck('id')->toArray();
        $incomingIds = collect($items)->pluck('id')->filter()->toArray();

        // Delete removed records
        $relation->whereIn('id', array_diff($existingIds, $incomingIds))->delete();

        foreach ($items as $item) {
            $id = $item['id'] ?? null;
            unset($item['id']);

            if ($id) {
                $relation->where('id', $id)->update($item);
            } else {
                $filtered = array_filter($item, fn($v) => $v !== '' && $v !== null);
                if (!empty($filtered)) {
                    $relation->create($item);
                }
            }
        }
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Resubmit SALN')->submit('save')->color('primary')
                ->icon('heroicon-o-paper-airplane'),
            Actions\Action::make('cancel')
                ->label('Cancel')->url($this->getResource()::getUrl('index'))
                ->color('gray')->icon('heroicon-o-x-mark'),
        ];
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
            'description' => '', 'kind' => '', 'exact_location' => '',
            'assessed_value' => '', 'current_fair_market_value' => '',
            'acquisition_year' => '', 'mode_of_acquisition' => '', 'acquisition_cost' => '',
        ];
    }

    public function removeRealProperty(int $index): void
    {
        array_splice($this->data['realProperties'], $index, 1);
    }

    public function addPersonalProperty(): void
    {
        $this->data['personalProperties'][] = [
            'description' => '', 'year_acquired' => '', 'acquisition_cost' => '',
        ];
    }

    public function removePersonalProperty(int $index): void
    {
        array_splice($this->data['personalProperties'], $index, 1);
    }

    public function addLiability(): void
    {
        $this->data['liabilities'][] = [
            'nature' => '', 'name_of_creditors' => '', 'outstanding_balance' => '',
        ];
    }

    public function removeLiability(int $index): void
    {
        array_splice($this->data['liabilities'], $index, 1);
    }

    public function addBusinessInterest(): void
    {
        $this->data['businessInterests'][] = [
            'name_of_entity' => '', 'business_address' => '',
            'nature_of_business_interest' => '', 'date_of_acquisition' => '',
        ];
    }

    public function removeBusinessInterest(int $index): void
    {
        array_splice($this->data['businessInterests'], $index, 1);
    }

    public function addRelativeInGovernment(): void
    {
        $this->data['relativesInGovernment'][] = [
            'name_of_relative' => '', 'relationship' => '',
            'position' => '', 'name_of_agency_office_address' => '',
        ];
    }

    public function removeRelativeInGovernment(int $index): void
    {
        array_splice($this->data['relativesInGovernment'], $index, 1);
    }

    // =========================================================================
    //  LIVEWIRE HELPERS — ANNEX B
    // =========================================================================

    public function addAnnexBRealProperty(): void
    {
        $this->data['annexBRealProperties'][] = [
            'description' => '', 'kind' => '', 'exact_location' => '',
            'assessed_value' => '', 'current_fair_market_value' => '',
            'acquisition_year' => '', 'mode_of_acquisition' => '', 'acquisition_cost' => '',
        ];
    }

    public function removeAnnexBRealProperty(int $index): void
    {
        array_splice($this->data['annexBRealProperties'], $index, 1);
    }

    public function addAnnexBPersonalProperty(): void
    {
        $this->data['annexBPersonalProperties'][] = [
            'description' => '', 'year_acquired' => '', 'acquisition_cost' => '',
        ];
    }

    public function removeAnnexBPersonalProperty(int $index): void
    {
        array_splice($this->data['annexBPersonalProperties'], $index, 1);
    }

    public function addAnnexBLiability(): void
    {
        $this->data['annexBLiabilities'][] = [
            'nature' => '', 'name_of_creditors' => '', 'outstanding_balance' => '',
        ];
    }

    public function removeAnnexBLiability(int $index): void
    {
        array_splice($this->data['annexBLiabilities'], $index, 1);
    }

    public function addAnnexBBusinessInterest(): void
    {
        $this->data['annexBBusinessInterests'][] = [
            'name_of_entity' => '', 'business_address' => '',
            'nature_of_business_interest' => '', 'date_of_acquisition' => '',
        ];
    }

    public function removeAnnexBBusinessInterest(int $index): void
    {
        array_splice($this->data['annexBBusinessInterests'], $index, 1);
    }

    // =========================================================================
    //  LIVEWIRE HELPERS — ANNEX C
    // =========================================================================

    public function addAnnexCRealProperty(): void
    {
        $this->data['annexCRealProperties'][] = [
            'description' => '', 'kind' => '', 'exact_location' => '',
            'assessed_value' => '', 'current_fair_market_value' => '',
            'acquisition_year' => '', 'mode_of_acquisition' => '', 'acquisition_cost' => '',
        ];
    }

    public function removeAnnexCRealProperty(int $index): void
    {
        array_splice($this->data['annexCRealProperties'], $index, 1);
    }

    public function addAnnexCPersonalProperty(): void
    {
        $this->data['annexCPersonalProperties'][] = [
            'description' => '', 'year_acquired' => '', 'acquisition_cost' => '',
        ];
    }

    public function removeAnnexCPersonalProperty(int $index): void
    {
        array_splice($this->data['annexCPersonalProperties'], $index, 1);
    }

    public function addAnnexCLiability(): void
    {
        $this->data['annexCLiabilities'][] = [
            'nature' => '', 'name_of_creditors' => '', 'outstanding_balance' => '',
        ];
    }

    public function removeAnnexCLiability(int $index): void
    {
        array_splice($this->data['annexCLiabilities'], $index, 1);
    }

    public function addAnnexCBusinessInterest(): void
    {
        $this->data['annexCBusinessInterests'][] = [
            'name_of_entity' => '', 'business_address' => '',
            'nature_of_business_interest' => '', 'date_of_acquisition' => '',
        ];
    }

    public function removeAnnexCBusinessInterest(int $index): void
    {
        array_splice($this->data['annexCBusinessInterests'], $index, 1);
    }
}
