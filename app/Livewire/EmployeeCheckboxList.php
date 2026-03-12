<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;

/**
 * EmployeeCheckboxList
 *
 * Renders a batch-limited employee checkbox list for the biometric import modal.
 *
 * ── ARCHITECTURE (why session storage) ───────────────────────────────────────
 *
 * The central problem across all previous approaches:
 *
 *   1. JS bridge (Livewire.find(parentId).set(path, selected))
 *      BROKEN — Filament v3 stores action data under unpredictable paths
 *      (mountedTableActionsData vs mountedActionsData etc.) that can't be
 *      reliably targeted from a child component's JS.
 *
 *   2. Livewire::find($componentId)->selected at submit time
 *      BROKEN — Livewire v3 does not support reading a component's live
 *      state via a static finder during a different HTTP request. Each
 *      Livewire request is isolated; component state is not shared across
 *      requests via Livewire::find().
 *
 *   3. Session storage (this implementation)
 *      WORKS — When the admin toggles a checkbox or clicks Select Batch,
 *      the component writes the current selection to a session key. When
 *      the admin clicks Submit (a separate HTTP request handled by Filament),
 *      BiometricImportAction reads the same session key. No JS, no finders,
 *      no path guessing.
 *
 * ── TYPE SAFETY ───────────────────────────────────────────────────────────────
 *
 * All user IDs are cast to string on entry (toggle, selectBatch) and stored
 * as strings. The Blade template compares with strict string equality.
 * This prevents the (string)'5' !== (int)5 mismatch that caused checkboxes
 * to appear unchecked even when $selected was populated.
 *
 * ── SESSION KEY ───────────────────────────────────────────────────────────────
 *
 * Key: 'biometric_import_selected_ids'
 * Cleared: on clearSelection(), on mount if employees is empty, and by
 *           BiometricImportAction after a successful or failed submit.
 */
class EmployeeCheckboxList extends Component
{
    private const SESSION_KEY = 'biometric_import_selected_ids';

    // ── Props ─────────────────────────────────────────────────────────────

    /** ['userId' => 'Label string', ...] — keys are always strings */
    public array $employees = [];

    /** Max selectable employees per batch. */
    public int $limit = 25;

    /** Display string e.g. "2026/02/01 ~ 02/28" */
    public string $period = '';

    // ── State ─────────────────────────────────────────────────────────────

    /**
     * Currently selected user IDs — always strings.
     * This is the in-memory copy; the session is the persistent copy.
     */
    public array $selected = [];

    // ── Lifecycle ─────────────────────────────────────────────────────────

    public function mount(): void
    {
        // On fresh mount (new scan), clear any stale session selection.
        // If employees were passed in, restore from session only if the
        // stored IDs are a subset of the current employee list — avoids
        // carrying over selections from a previous scan of a different file.
        $stored = Session::get(self::SESSION_KEY, []);

        if (!empty($stored) && !empty($this->employees)) {
            $validIds = array_map('strval', array_keys($this->employees));
            $this->selected = array_values(
                array_filter($stored, fn($id) => in_array((string) $id, $validIds, true))
            );
        } else {
            $this->selected = [];
        }

        $this->syncSession();
    }

    /**
     * Called by Livewire when the $employees prop is updated from the parent.
     * Treat a new employee list as a new scan — clear selection.
     */
    public function updatedEmployees(): void
    {
        $this->selected = [];
        $this->syncSession();
    }

    // ── Public actions ────────────────────────────────────────────────────

    /**
     * Toggle a single employee. IDs are always coerced to string.
     */
    public function toggle(string $userId): void
    {
        $userId = (string) $userId;

        if (in_array($userId, $this->selected, true)) {
            $this->selected = array_values(
                array_filter($this->selected, fn($id) => $id !== $userId)
            );
        } elseif (count($this->selected) < $this->limit) {
            $this->selected[] = $userId;
        }
        // At limit and not already selected: silently ignore.
        // The UI disables the checkbox so this shouldn't be reachable,
        // but we guard here as a server-side safety net.

        $this->syncSession();
    }

    /**
     * Select the first $limit employees in one click.
     */
    public function selectBatch(): void
    {
        $this->selected = array_map(
            'strval',
            array_slice(array_keys($this->employees), 0, $this->limit)
        );
        $this->syncSession();
    }

    /**
     * Clear all selections.
     */
    public function clearSelection(): void
    {
        $this->selected = [];
        $this->syncSession();
    }

    // ── Session helper ────────────────────────────────────────────────────

    /**
     * Write the current selection to the session.
     * Called after every state change so the session is always current.
     */
    private function syncSession(): void
    {
        Session::put(self::SESSION_KEY, $this->selected);
    }

    /**
     * Public static helper for BiometricImportAction to read the selection.
     * Returns an array of string user IDs, or [] if nothing is stored.
     */
    public static function getSessionSelection(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    /**
     * Public static helper for BiometricImportAction to clear the session
     * after a successful or failed submit, preventing stale data on re-open.
     */
    public static function clearSessionSelection(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    // ── Render ────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        return view('livewire.employee-checkbox-list');
    }
}
