<div class="space-y-3">

    {{--
        EmployeeCheckboxList Blade view.

        Selection state lives in $selected (Livewire property, always string[]).
        All comparisons use strict string equality to prevent int/string type
        mismatches that caused checkboxes to appear unchecked when selected.

        No JS bridge required — selection is persisted to the Laravel session
        by the component on every toggle/selectBatch/clearSelection call.
        BiometricImportAction reads the session at submit time.
    --}}

    {{-- ── Status bar ─────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between flex-wrap gap-2 px-3 py-2
                bg-gray-50 dark:bg-gray-800
                border border-gray-200 dark:border-gray-700
                rounded-lg text-xs">

        <div class="flex items-center gap-2 flex-wrap">

            {{-- Period badge --}}
            @if($period)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded
                             bg-blue-50 dark:bg-blue-950
                             border border-blue-200 dark:border-blue-700
                             text-blue-700 dark:text-blue-300 font-medium">
                    📅 {{ $period }}
                </span>
            @endif

            {{-- Batch note --}}
            @if(count($employees) > $limit)
                <span class="text-amber-600 dark:text-amber-400 font-medium">
                    ⚠️ {{ count($employees) }} pending — ~{{ (int) ceil(count($employees) / $limit) }} batches needed
                </span>
            @else
                <span class="text-green-600 dark:text-green-400 font-medium">
                    ✅ All {{ count($employees) }} fit in one batch
                </span>
            @endif

        </div>

        {{-- Selection counter --}}
        <span @class([
            'font-semibold',
            'text-amber-600 dark:text-amber-400' => count($selected) >= $limit,
            'text-gray-500 dark:text-gray-400'   => count($selected) < $limit,
        ])>
            {{ count($selected) }} / {{ $limit }} selected
        </span>
    </div>

    {{-- ── Batch action buttons ────────────────────────────────────────────── --}}
    <div class="flex items-center gap-2">

        <button
            type="button"
            wire:click="selectBatch"
            wire:loading.attr="disabled"
            wire:target="selectBatch"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium
                   bg-primary-600 hover:bg-primary-700 active:bg-primary-800
                   disabled:opacity-60 disabled:cursor-wait
                   text-white transition-colors duration-150
                   focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1
                   dark:focus:ring-offset-gray-900">
            <svg wire:loading.remove wire:target="selectBatch"
                 class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <svg wire:loading wire:target="selectBatch"
                 class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            Select Batch ({{ $limit }})
        </button>

        <button
            type="button"
            wire:click="clearSelection"
            wire:loading.attr="disabled"
            wire:target="clearSelection"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium
                   bg-gray-100 hover:bg-gray-200 active:bg-gray-300
                   dark:bg-gray-700 dark:hover:bg-gray-600 dark:active:bg-gray-500
                   disabled:opacity-60 disabled:cursor-wait
                   text-gray-700 dark:text-gray-200 transition-colors duration-150
                   focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-1
                   dark:focus:ring-offset-gray-900">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Clear
        </button>

    </div>

    {{-- ── Employee list ────────────────────────────────────────────────────── --}}
    <div class="max-h-80 overflow-y-auto space-y-1 pr-1" wire:loading.class="opacity-50" wire:target="toggle,selectBatch,clearSelection">
        @forelse($employees as $userId => $label)
            @php
                // CRITICAL: always compare as strings to prevent int/string mismatch.
                // array_keys() on an array with numeric-string keys returns integers
                // in PHP, so '5' === 5 fails with strict comparison.
                $uid        = (string) $userId;
                $isChecked  = in_array($uid, $selected, true);
                $isDisabled = !$isChecked && count($selected) >= $limit;
            @endphp

            <label
                for="emp-{{ $uid }}"
                @class([
                    'flex items-center gap-3 px-3 py-2.5 rounded-lg border transition-colors duration-100 select-none',
                    // ── Checked ──
                    'bg-blue-50 border-blue-300 dark:bg-blue-950/60 dark:border-blue-600 cursor-pointer'
                        => $isChecked,
                    // ── Normal ──
                    'bg-white border-gray-200 hover:bg-gray-50 dark:bg-gray-900 dark:border-gray-700 dark:hover:bg-gray-800/80 cursor-pointer'
                        => !$isChecked && !$isDisabled,
                    // ── Disabled (batch full, not selected) ──
                    'bg-gray-50 border-gray-200 dark:bg-gray-900/40 dark:border-gray-700 opacity-40 cursor-not-allowed pointer-events-none'
                        => $isDisabled,
                ])>

                {{-- Checkbox --}}
                <input
                    type="checkbox"
                    id="emp-{{ $uid }}"
                    value="{{ $uid }}"
                    @checked($isChecked)
                    @disabled($isDisabled)
                    wire:click.prevent="toggle('{{ $uid }}')"
                    class="w-4 h-4 flex-shrink-0 rounded
                           text-blue-600 accent-blue-600
                           border-gray-300 dark:border-gray-600
                           focus:ring-blue-500 focus:ring-2
                           cursor-pointer disabled:cursor-not-allowed"
                />

                {{-- Label text --}}
                <span @class([
                    'text-sm leading-snug min-w-0 flex-1 break-words',
                    'text-blue-800 dark:text-blue-200 font-medium' => $isChecked,
                    'text-gray-700 dark:text-gray-300'             => !$isChecked,
                ])>
                    {{ $label }}
                </span>

                {{-- Check icon (only when selected, gives extra visual confirmation) --}}
                @if($isChecked)
                    <svg class="w-4 h-4 flex-shrink-0 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                @endif

            </label>
        @empty
            <p class="text-sm text-gray-400 dark:text-gray-500 italic px-2 py-4 text-center">
                No employees to display. Upload a file and click Scan.
            </p>
        @endforelse
    </div>

    {{-- ── Footer hint ─────────────────────────────────────────────────────── --}}
    <p class="text-xs text-gray-400 dark:text-gray-500 leading-relaxed">
        Use <strong class="font-semibold text-gray-500 dark:text-gray-400">Select Batch</strong>
        to pick the first {{ $limit }} at once.
        After Submit, re-upload and scan again for the next batch.
    </p>

</div>
