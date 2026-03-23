<?php

namespace App\Filament\Pages;

use App\Models\BiometricEmployeeMapping;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * BiometricMappingManager — Bulk workflow accelerator.
 *
 * ── ROLE IN THE HYBRID ARCHITECTURE ──────────────────────────────────────────
 * This page handles BULK operations only. It is NOT a replacement for
 * BiometricEmployeeMappingResource. The Resource owns CRUD and data integrity;
 * this page owns speed and HR workflow ergonomics.
 *
 * Use this page for:
 *   • Initial setup — assign device IDs to all 50+ employees in one session
 *   • Batch updates — re-map multiple employees after a device reset
 *
 * Use the Resource for:
 *   • Creating / editing / deleting individual mapping records
 *   • Viewing audit history and active/inactive status
 *   • Any operation that touches one record at a time
 *
 * ── WHAT THIS PAGE DOES ──────────────────────────────────────────────────────
 * 1. Shows ALL employees (mapped + unmapped) in one table
 * 2. Allows inline device ID editing per row
 * 3. Saves all changes in a single DB transaction
 * 4. Detects duplicate device IDs before saving
 * 5. Filter tabs: All / Mapped / Unmapped
 * 6. History drawer: read-only audit trail per employee
 */
class BiometricMappingManager extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Bulk Mapping';
    protected static ?string $navigationGroup = 'People & Access';
    protected static ?string $title = 'Bulk Biometric Mapping';
    protected static ?int $navigationSort = 4;
    protected static string $view = 'filament.pages.biometric-mapping-manager';

    // ── Public Livewire state ─────────────────────────────────────────────────

    /** All employees + their active mapping (if any), mirrored to Alpine */
    public array $rows = [];

    /** Full mapping history for the drawer — active + inactive, one employee */
    public array $historyRows = [];

    /** Name of the employee whose history drawer is currently open */
    public string $historyEmployeeName = '';

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    /**
     * Gate-level permission check — called by Filament before rendering the page.
     *
     * This is the declarative complement to the abort_unless() guard in mount().
     * canAccess() prevents the page from appearing in navigation for non-admins
     * and returns a 403 response when the URL is accessed directly.
     * The mount() guard is a belt-and-suspenders fallback for Livewire requests.
     *
     * If your system adds an HR_MANAGER role in the future, expand both conditions
     * identically: e.g. auth()->user()->isAdmin() || auth()->user()->isHrManager()
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function mount(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);
        $this->loadRows();
    }

    // ── Data loading ──────────────────────────────────────────────────────────

    public function loadRows(): void
    {
        $users = User::whereIn('role', [User::ROLE_REGULAR, User::ROLE_JOB_ORDER])
            ->orderBy('name')
            ->get();

        // Only active mappings are shown inline.
        // Inactive (historical) records surface in the history drawer.
        $activeMappings = BiometricEmployeeMapping::active()
            ->get()
            ->keyBy('user_id');

        $this->rows = $users->map(function (User $user) use ($activeMappings) {
            $mapping = $activeMappings->get($user->id);

            return [
                'user_id' => $user->id,
                'name' => $user->name,
                'employee_id' => $user->employee_id ?? '—',
                'role' => $user->role === User::ROLE_JOB_ORDER ? 'Job Order' : 'Regular',
                'device_id' => $mapping?->device_id ?? '',
                'mapping_id' => $mapping?->id,
                'is_active' => (bool) ($mapping?->is_active ?? false),
            ];
        })->values()->toArray();
    }

    // ── History drawer ────────────────────────────────────────────────────────

    /**
     * Load all mapping records (active + inactive) for one employee.
     * Called from Blade via $wire.loadHistory(userId).
     * Read-only — editing/deactivation is handled by the Resource.
     */
    public function loadHistory(int $userId): void
    {
        $this->historyRows = [];   // clear stale data immediately
        $this->historyEmployeeName = '';   // prevents showing old name during load

        $user = User::find($userId);
        if (!$user) {
            return;
        }

        $this->historyEmployeeName = $user->name;

        $this->historyRows = BiometricEmployeeMapping::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($m) => [
                'device_id' => $m->device_id,
                'device_name' => $m->device_name ?? '—',
                'is_active' => $m->is_active,
                'created_at' => $m->created_at->format('M d, Y h:i A'),
                'updated_at' => $m->updated_at->format('M d, Y h:i A'),
            ])
            ->toArray();
    }

    // ── Bulk save ─────────────────────────────────────────────────────────────

    /**
     * Validate and persist all device ID changes in one DB transaction.
     *
     * Per-row logic:
     *   blank + no mapping  → skip
     *   blank + has mapping → soft-deactivate (not delete — preserves audit trail)
     *   non-blank, unchanged active mapping → skip (no unnecessary write)
     *   non-blank, new or changed → upsert
     *   duplicate device_id across two employees → reject entire batch
     *
     * Individual validation (required, unique) is enforced by the Resource's
     * form. This method handles BULK validation only (duplicate detection across
     * the batch being saved).
     */
    public function save(): void
    {
        // Re-validate user_ids against the database before processing.
        // Prevents client-side manipulation of the rows array.
        $validUserIds = User::whereIn('role', [User::ROLE_REGULAR, User::ROLE_JOB_ORDER])
            ->pluck('id')
            ->flip()
            ->toArray(); // flip for O(1) isset() lookup

        foreach ($this->rows as $row) {
            if (!isset($validUserIds[$row['user_id']])) {
                Notification::make()->danger()->title('Invalid request.')->send();
                Log::warning('[BiometricMapping] Invalid user_id in save payload', [
                    'user_id' => $row['user_id'],
                    'by' => Auth::id(),
                ]);
                return;
            }
        }
        // ── Duplicate device ID guard across the current batch ────────────────
        $filled = collect($this->rows)->filter(fn($r) => trim($r['device_id']) !== '');

        $duplicates = $filled
            ->groupBy(fn($r) => trim($r['device_id']))
            ->filter(fn($group) => $group->count() > 1);

        if ($duplicates->isNotEmpty()) {
            $conflicts = $duplicates->map(function ($group, $deviceId) {
                $names = $group->pluck('name')->join(', ');
                return "Device ID \"{$deviceId}\" → {$names}";
            })->join(' | ');

            Notification::make()
                ->danger()
                ->title('Duplicate Device IDs Found')
                ->body("Fix these conflicts before saving: {$conflicts}")
                ->persistent()
                ->send();
            return;
        }

        // ── Persist in a transaction ──────────────────────────────────────────
        $saved = 0;
        $deactivated = 0;
        $skipped = 0;

        try {
            DB::transaction(function () use (&$saved, &$deactivated, &$skipped) {
                foreach ($this->rows as $row) {
                    $deviceId = trim($row['device_id']);

                    if ($deviceId === '') {
                        if ($row['mapping_id']) {
                            // Soft-deactivate — do NOT hard-delete (audit trail)
                            BiometricEmployeeMapping::where('id', $row['mapping_id'])
                                ->update(['is_active' => false]);
                            $deactivated++;

                            Log::info('[BiometricMapping] Deactivated via bulk manager', [
                                'mapping_id' => $row['mapping_id'],
                                'user_id' => $row['user_id'],
                                'by' => Auth::id(),
                            ]);
                        } else {
                            $skipped++; // already unmapped — nothing to do
                        }
                        continue;
                    }

                    // Deactivate any OTHER employee's active mapping for this device_id.
                    // Prevents a device number being shared by two people in the DB
                    // even if the duplicate guard above is bypassed via direct DB edit.
                    BiometricEmployeeMapping::active()
                        ->where('device_id', $deviceId)
                        ->lockForUpdate()
                        ->where('user_id', '!=', $row['user_id'])
                        ->update(['is_active' => false]);

                    if ($row['mapping_id']) {
                        // Existing mapping — skip write if nothing actually changed
                        $existing = BiometricEmployeeMapping::find($row['mapping_id']);
                        if ($existing && $existing->device_id === $deviceId && $existing->is_active) {
                            $skipped++;
                            continue;
                        }

                        BiometricEmployeeMapping::where('id', $row['mapping_id'])
                            ->update(['device_id' => $deviceId, 'is_active' => true]);
                    } else {
                        // New mapping for a previously unmapped employee
                        BiometricEmployeeMapping::create([
                            'user_id' => $row['user_id'],
                            'device_id' => $deviceId,
                            'is_active' => true,
                        ]);
                    }

                    $saved++;
                }
            });

            $this->loadRows();

            $parts = [];
            if ($saved > 0)
                $parts[] = "{$saved} saved";
            if ($deactivated > 0)
                $parts[] = "{$deactivated} deactivated";
            if ($skipped > 0)
                $parts[] = "{$skipped} unchanged";

            Notification::make()
                ->success()
                ->title('Mappings Updated')
                ->body(implode(' · ', $parts) ?: 'No changes detected.')
                ->send();

            Log::info('[BiometricMapping] Bulk save complete', [
                'saved' => $saved,
                'deactivated' => $deactivated,
                'skipped' => $skipped,
                'by' => Auth::id(),
            ]);

        } catch (\Throwable $e) {
            Log::error('[BiometricMapping] Bulk save failed', [
                'error' => $e->getMessage(),
                'by' => Auth::id(),
            ]);

            Notification::make()
                ->danger()
                ->title('Save Failed')
                ->body($e->getMessage())
                ->send();
        }
    }

    // ── Header actions ────────────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [

            Action::make('reload')
                ->label('Reload')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action('loadRows'),

            // ── Link back to the Resource for individual CRUD ─────────────────
            // Closes the navigation loop: admin can jump between bulk page
            // and the resource list without using the sidebar.
            Action::make('back_to_list')
                ->label('Mapping Records')
                ->icon('heroicon-o-table-cells')
                ->color('gray')
                ->url(\App\Filament\Resources\BiometricEmployeeMappingResource::getUrl('index'))
                ->openUrlInNewTab(false),
        ];
    }
}
