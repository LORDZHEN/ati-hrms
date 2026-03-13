<?php

namespace App\Filament\Resources\SalnResource\Pages;

use App\Filament\Concerns\WorkflowHelper;
use App\Filament\Resources\SalnResource;
use App\Models\User;
use App\Notifications\NewSalnFiled;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSaln extends EditRecord
{
    use WorkflowHelper;

    protected static string $resource = SalnResource::class;

    // =========================================================================
    //  BOOT — enforce lock at page level
    //
    //  If the employee tries to reach this URL directly but the record is
    //  locked, redirect them to the view page immediately.
    // =========================================================================

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Policy already blocks the canEdit() gate, but this adds a
        // belt-and-suspenders redirect so the user sees a friendly message
        // instead of a 403 error page.
        if (auth()->user()?->role !== 'admin' && !static::canEmployeeEdit($this->record)) {
            Notification::make()
                ->title('Record is Locked')
                ->body('This SALN is approved and cannot be edited.')
                ->warning()
                ->send();

            $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
        }
    }

    // =========================================================================
    //  HEADER ACTIONS
    // =========================================================================

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn() => auth()->user()?->role === 'admin'),
        ];
    }

    // =========================================================================
    //  FORM ACTIONS — Hide save/resubmit for locked records
    // =========================================================================

    protected function getFormActions(): array
    {
        // If the record is locked (approved + not unlocked OR filing season off),
        // show no editable actions — only a back button.
        if (!static::canEmployeeEdit($this->record)) {
            return [
                Actions\Action::make('back')
                    ->label('Back to List')
                    ->url($this->getResource()::getUrl('index'))
                    ->color('gray')
                    ->icon('heroicon-o-arrow-left'),
            ];
        }

        $isEmployee = auth()->user()->role === 'employee';

        return [
            Actions\Action::make('save')
                ->label($isEmployee ? 'Resubmit SALN' : 'Save Changes')
                ->submit('save')
                ->color('primary')
                ->icon($isEmployee ? 'heroicon-o-paper-airplane' : 'heroicon-o-check'),

            Actions\Action::make('cancel')
                ->label('Cancel')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->icon('heroicon-o-x-mark'),
        ];
    }

    // =========================================================================
    //  MUTATIONS — only employees trigger re-submission flow
    // =========================================================================

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->record;

        // ── Annex B ───────────────────────────────────────────────────────────
        $data['annexBRealProperties'] = $record->annexBRealProperties->map->only([
            'id',
            'description',
            'kind',
            'exact_location',
            'assessed_value',
            'current_fair_market_value',
            'acquisition_year',
            'mode_of_acquisition',
            'acquisition_cost',
        ])->values()->toArray();

        $data['annexBPersonalProperties'] = $record->annexBPersonalProperties->map->only([
            'id',
            'description',
            'year_acquired',
            'acquisition_cost',
        ])->values()->toArray();

        $data['annexBLiabilities'] = $record->annexBLiabilities->map->only([
            'id',
            'nature',
            'name_of_creditors',
            'outstanding_balance',
        ])->values()->toArray();

        $data['annexBBusinessInterests'] = $record->annexBBusinessInterests->map->only([
            'id',
            'name_of_entity',
            'business_address',
            'nature_of_business_interest',
            'date_of_acquisition',
        ])->values()->toArray();

        // ── Annex C ───────────────────────────────────────────────────────────
        $data['annexCRealProperties'] = $record->annexCRealProperties->map->only([
            'id',
            'description',
            'kind',
            'exact_location',
            'assessed_value',
            'current_fair_market_value',
            'acquisition_year',
            'mode_of_acquisition',
            'acquisition_cost',
        ])->values()->toArray();

        $data['annexCPersonalProperties'] = $record->annexCPersonalProperties->map->only([
            'id',
            'description',
            'year_acquired',
            'acquisition_cost',
        ])->values()->toArray();

        $data['annexCLiabilities'] = $record->annexCLiabilities->map->only([
            'id',
            'nature',
            'name_of_creditors',
            'outstanding_balance',
        ])->values()->toArray();

        $data['annexCBusinessInterests'] = $record->annexCBusinessInterests->map->only([
            'id',
            'name_of_entity',
            'business_address',
            'nature_of_business_interest',
            'date_of_acquisition',
        ])->values()->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $isAdmin = auth()->user()?->role === 'admin';

        if (!$isAdmin) {
            // Guard: abort if the employee somehow bypassed the UI
            abort_unless(static::canEmployeeEdit($this->record), 403, 'This record is locked.');

            unset($data['person_administering_oath'], $data['subscribed_sworn_date']);
            $data['status'] = 'submitted';
            $data['editing_unlocked'] = false; // re-lock after employee resubmission
        }

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

    protected function afterSave(): void
    {
        // Sync Annex B relationships
        $this->syncAnnexRelationships('annexBRealProperties', 'annexBRealProperties');
        $this->syncAnnexRelationships('annexBPersonalProperties', 'annexBPersonalProperties');
        $this->syncAnnexRelationships('annexBLiabilities', 'annexBLiabilities');
        $this->syncAnnexRelationships('annexBBusinessInterests', 'annexBBusinessInterests');

        // Sync Annex C relationships
        $this->syncAnnexRelationships('annexCRealProperties', 'annexCRealProperties');
        $this->syncAnnexRelationships('annexCPersonalProperties', 'annexCPersonalProperties');
        $this->syncAnnexRelationships('annexCLiabilities', 'annexCLiabilities');
        $this->syncAnnexRelationships('annexCBusinessInterests', 'annexCBusinessInterests');

        $this->record->calculateTotals();
        $this->record->updateQuietly(['resubmitted_at' => now()]);

        // Only notify admins when an employee resubmits
        if (auth()->user()?->role !== 'admin') {
            User::where('role', 'admin')->get()->each(
                fn($admin) => $admin->notify(new NewSalnFiled($this->record))
            );

            Notification::make()
                ->title('SALN Resubmitted Successfully')
                ->body('Your SALN has been updated and filed for review.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('SALN Updated')
                ->body('Changes saved successfully.')
                ->success()
                ->send();
        }
    }

    // =========================================================================
    //  REDIRECT
    // =========================================================================

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // =========================================================================
    //  SYNC HELPER
    // =========================================================================

    protected function syncAnnexRelationships(string $dataKey, string $relationshipMethod): void
    {
        $items = $this->data[$dataKey] ?? [];
        $relation = $this->record->{$relationshipMethod}();
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

    // =========================================================================
    //  LIVEWIRE HELPERS — ANNEX A
    // =========================================================================

    public function addChild(): void
    {
        $this->data['children'][] = ['name' => '', 'age' => ''];
    }
    public function removeChild(int $i): void
    {
        array_splice($this->data['children'], $i, 1);
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
    public function removeRealProperty(int $i): void
    {
        array_splice($this->data['realProperties'], $i, 1);
    }

    public function addPersonalProperty(): void
    {
        $this->data['personalProperties'][] = ['description' => '', 'year_acquired' => '', 'acquisition_cost' => ''];
    }
    public function removePersonalProperty(int $i): void
    {
        array_splice($this->data['personalProperties'], $i, 1);
    }

    public function addLiability(): void
    {
        $this->data['liabilities'][] = ['nature' => '', 'name_of_creditors' => '', 'outstanding_balance' => ''];
    }
    public function removeLiability(int $i): void
    {
        array_splice($this->data['liabilities'], $i, 1);
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
    public function removeBusinessInterest(int $i): void
    {
        array_splice($this->data['businessInterests'], $i, 1);
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
    public function removeRelativeInGovernment(int $i): void
    {
        array_splice($this->data['relativesInGovernment'], $i, 1);
    }

    // =========================================================================
    //  LIVEWIRE HELPERS — ANNEX B
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
    public function removeAnnexBRealProperty(int $i): void
    {
        array_splice($this->data['annexBRealProperties'], $i, 1);
    }

    public function addAnnexBPersonalProperty(): void
    {
        $this->data['annexBPersonalProperties'][] = ['description' => '', 'year_acquired' => '', 'acquisition_cost' => ''];
    }
    public function removeAnnexBPersonalProperty(int $i): void
    {
        array_splice($this->data['annexBPersonalProperties'], $i, 1);
    }

    public function addAnnexBLiability(): void
    {
        $this->data['annexBLiabilities'][] = ['nature' => '', 'name_of_creditors' => '', 'outstanding_balance' => ''];
    }
    public function removeAnnexBLiability(int $i): void
    {
        array_splice($this->data['annexBLiabilities'], $i, 1);
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
    public function removeAnnexBBusinessInterest(int $i): void
    {
        array_splice($this->data['annexBBusinessInterests'], $i, 1);
    }

    // =========================================================================
    //  LIVEWIRE HELPERS — ANNEX C
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
    public function removeAnnexCRealProperty(int $i): void
    {
        array_splice($this->data['annexCRealProperties'], $i, 1);
    }

    public function addAnnexCPersonalProperty(): void
    {
        $this->data['annexCPersonalProperties'][] = ['description' => '', 'year_acquired' => '', 'acquisition_cost' => ''];
    }
    public function removeAnnexCPersonalProperty(int $i): void
    {
        array_splice($this->data['annexCPersonalProperties'], $i, 1);
    }

    public function addAnnexCLiability(): void
    {
        $this->data['annexCLiabilities'][] = ['nature' => '', 'name_of_creditors' => '', 'outstanding_balance' => ''];
    }
    public function removeAnnexCLiability(int $i): void
    {
        array_splice($this->data['annexCLiabilities'], $i, 1);
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
    public function removeAnnexCBusinessInterest(int $i): void
    {
        array_splice($this->data['annexCBusinessInterests'], $i, 1);
    }
}
