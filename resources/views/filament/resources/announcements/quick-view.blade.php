{{-- Announcement Quick View Modal --}}
<div class="space-y-6">

    {{-- Header Section with Status & Priority --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-750 rounded-xl p-6 border border-blue-100 dark:border-gray-700">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ $record->title }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Posted by <strong class="text-gray-700 dark:text-gray-300 ml-1">{{ $record->creator?->name ?? 'Unknown' }}</strong>
                    &nbsp;·&nbsp;
                    {{ $record->created_at->format('M d, Y h:i A') }}
                </p>
            </div>

            <div class="flex flex-col items-end gap-2 shrink-0">
                {{-- Active / Inactive badge --}}
                @if($record->is_active)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300 ring-2 ring-green-400">
                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Active
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 ring-2 ring-gray-400">
                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        Inactive
                    </span>
                @endif

                {{-- Priority badge --}}
                @php
                    $priorityConfig = [
                        'high'   => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300 ring-red-400',
                        'medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300 ring-yellow-400',
                        'low'    => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300 ring-green-400',
                    ];
                    $priorityClass = $priorityConfig[$record->priority] ?? $priorityConfig['medium'];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ring-2 {{ $priorityClass }}">
                    🚩 {{ ucfirst($record->priority) }} Priority
                </span>
            </div>
        </div>
    </div>

    {{-- Message Body --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-center mb-4">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Message</h3>
        </div>
        <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-4">
            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $record->message }}</p>
        </div>
    </div>

    {{-- Schedule Information --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-center mb-4">
            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Schedule</h3>
        </div>
        <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-3">
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Publish Date</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $record->publish_date ? \Carbon\Carbon::parse($record->publish_date)->format('M d, Y') : 'Immediately' }}
                </dd>
            </div>

            <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-3">
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Expiry Date</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $record->expiry_date ? \Carbon\Carbon::parse($record->expiry_date)->format('M d, Y') : 'No expiry' }}
                </dd>
            </div>

            <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-3">
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Auto-Expires</dt>
                <dd class="mt-1 text-sm font-semibold {{ $record->expires_at?->isPast() ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                    @if($record->expires_at)
                        @if($record->expires_at->isPast())
                            ⛔ Expired ({{ $record->expires_at->format('M d, Y g:i A') }})
                        @else
                            ⏱ {{ $record->expires_at->diffForHumans() }}
                            <span class="block text-xs text-gray-500 dark:text-gray-400 font-normal mt-0.5">{{ $record->expires_at->format('M d, Y g:i A') }}</span>
                        @endif
                    @else
                        —
                    @endif
                </dd>
            </div>
        </dl>
    </div>

    {{-- Footer Timestamps --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400 pt-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Created: <strong class="text-gray-700 dark:text-gray-300">{{ $record->created_at->format('M d, Y h:i A') }}</strong></span>
        </div>
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span>Last Updated: <strong class="text-gray-700 dark:text-gray-300">{{ $record->updated_at->diffForHumans() }}</strong></span>
        </div>
    </div>

</div>
