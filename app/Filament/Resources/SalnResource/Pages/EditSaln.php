<?php

namespace App\Filament\Resources\SalnResource\Pages;

use App\Filament\Resources\SalnResource;
use App\Models\User;
use App\Notifications\NewSalnFiled;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditSaln extends EditRecord
{
    protected static string $resource = SalnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Print SALN')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn() => route('saln.print', $this->record))
                ->openUrlInNewTab(),

            Actions\DeleteAction::make()
                ->visible(fn() => auth()->user()?->role === 'admin'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Strip admin-only fields for employees
        if (auth()->user()?->role !== 'admin') {
            unset($data['person_administering_oath']);
            unset($data['subscribed_sworn_date']);
        }

        // Normalize date_of_birth on every child — the DB may store a full ISO
        // datetime string ("2015-06-16T00:00:00.000000Z"); we always save Y-m-d.
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

        // Recalculate financial totals on every save
        $realPropertiesTotal = collect($data['realProperties'] ?? [])->sum('current_fair_market_value');
        $personalPropertiesTotal = collect($data['personalProperties'] ?? [])->sum('acquisition_cost');

        $data['total_assets'] = $realPropertiesTotal + $personalPropertiesTotal;
        $data['total_liabilities'] = collect($data['liabilities'] ?? [])->sum('outstanding_balance');
        $data['net_worth'] = $data['total_assets'] - $data['total_liabilities'];

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->calculateTotals();

        // -----------------------------------------------------------------------
        //  Stamp resubmission time on BOTH columns:
        //
        //  resubmitted_at  — dedicated column; used by the table "Last Resubmitted"
        //                    column and the admin notification badge.
        //                    Requires migration: $table->timestamp('resubmitted_at')->nullable();
        //
        //  created_at      — also updated so any legacy "Filed" sort still works.
        //                    updateQuietly() skips model events.
        // -----------------------------------------------------------------------
        $this->record->updateQuietly([
            'resubmitted_at' => now(),
            'created_at' => now(),
        ]);

        // Notify all admins of the updated SALN
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewSalnFiled($this->record));
        }

        Notification::make()
            ->title('SALN Resubmitted Successfully')
            ->body('Your Statement of Assets, Liabilities and Net Worth has been updated and filed.')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Resubmit SALN')
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

    // ============================================================
    // LIVEWIRE METHODS FOR CHILDREN
    // ============================================================

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

    // ============================================================
    // LIVEWIRE METHODS FOR REAL PROPERTIES
    // ============================================================

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

    // ============================================================
    // LIVEWIRE METHODS FOR PERSONAL PROPERTIES
    // ============================================================

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

    // ============================================================
    // LIVEWIRE METHODS FOR LIABILITIES
    // ============================================================

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

    // ============================================================
    // LIVEWIRE METHODS FOR BUSINESS INTERESTS
    // ============================================================

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

    // ============================================================
    // LIVEWIRE METHODS FOR RELATIVES IN GOVERNMENT
    // ============================================================

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
