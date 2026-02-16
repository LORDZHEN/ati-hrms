@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;

    // Handle file_path being array or string
    $filePath = $record->file_path;
    if (is_array($filePath)) {
        $filePath = $filePath[0] ?? '';
    }
@endphp

<div class="space-y-5">
    {{-- Hero Header --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-primary-500 via-primary-600 to-primary-700 dark:from-primary-800 dark:via-primary-900 dark:to-primary-950 rounded-2xl p-8 shadow-2xl">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-32 w-32 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-4 -ml-4 h-32 w-32 rounded-full bg-white/10 blur-3xl"></div>

        <div class="relative flex items-center justify-between">
            <div class="flex items-center gap-5">
                <div class="flex items-center justify-center w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-lg shadow-xl border-2 border-white/30">
                    <span class="text-3xl font-black text-white">
                        {{ strtoupper(substr($record->employee->name, 0, 2)) }}
                    </span>
                </div>
                <div>
                    <h2 class="text-3xl font-black text-white mb-2">
                        {{ $record->employee->name }}
                    </h2>
                    <p class="text-primary-100 flex items-center gap-2 text-lg">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        {{ $record->employee->email }}
                    </p>
                </div>
            </div>
            <div class="hidden md:flex items-center gap-2 px-5 py-3 bg-white/20 backdrop-blur-lg rounded-xl border-2 border-white/30">
                <div class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-300 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-400"></span>
                </div>
                <span class="text-white font-bold text-sm">ACTIVE</span>
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- File Info Card --}}
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-950 dark:to-blue-900 rounded-xl p-6 border-2 border-blue-200 dark:border-blue-800 shadow-lg">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-3 bg-blue-500 rounded-xl shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wide">DTR File</p>
                    <p class="text-lg font-black text-blue-900 dark:text-blue-100">CSV Document</p>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-blue-700 dark:text-blue-300 font-medium">Filename</span>
                    <span class="text-blue-900 dark:text-blue-100 font-mono font-bold text-xs">{{ Str::limit(basename($filePath), 20) }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-blue-700 dark:text-blue-300 font-medium">Size</span>
                    @php
                        $fullPath = Storage::disk('public')->path($filePath);
                        $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;
                        $size = $fileSize > 0 ? number_format($fileSize / 1024, 2) . ' KB' : 'N/A';
                    @endphp
                    <span class="text-blue-900 dark:text-blue-100 font-bold">{{ $size }}</span>
                </div>
            </div>
        </div>

        {{-- Upload Date Card --}}
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-950 dark:to-emerald-900 rounded-xl p-6 border-2 border-emerald-200 dark:border-emerald-800 shadow-lg">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-3 bg-emerald-500 rounded-xl shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide">Uploaded</p>
                    <p class="text-lg font-black text-emerald-900 dark:text-emerald-100">{{ $record->created_at->format('M d, Y') }}</p>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-emerald-700 dark:text-emerald-300 font-medium">Time</span>
                    <span class="text-emerald-900 dark:text-emerald-100 font-bold">{{ $record->created_at->format('h:i A') }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-emerald-700 dark:text-emerald-300 font-medium">Age</span>
                    <span class="text-emerald-900 dark:text-emerald-100 font-bold">{{ $record->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        {{-- Last Modified Card --}}
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-950 dark:to-purple-900 rounded-xl p-6 border-2 border-purple-200 dark:border-purple-800 shadow-lg">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-3 bg-purple-500 rounded-xl shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-purple-600 dark:text-purple-400 uppercase tracking-wide">Modified</p>
                    <p class="text-lg font-black text-purple-900 dark:text-purple-100">
                        @if($record->updated_at != $record->created_at)
                            {{ $record->updated_at->format('M d, Y') }}
                        @else
                            Never
                        @endif
                    </p>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-purple-700 dark:text-purple-300 font-medium">Status</span>
                    <span class="px-2 py-1 bg-purple-500 text-white rounded-lg text-xs font-bold">Original</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-purple-700 dark:text-purple-300 font-medium">Record ID</span>
                    <span class="text-purple-900 dark:text-purple-100 font-mono font-bold">{{ $record->id }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Notes Section --}}
    @if($record->notes)
    <div class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-950/50 dark:to-amber-900/30 rounded-xl p-6 border-2 border-amber-300 dark:border-amber-700 shadow-lg">
        <div class="flex items-start gap-4">
            <div class="p-3 bg-amber-500 rounded-xl shadow-lg flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-black text-amber-900 dark:text-amber-100 mb-3">Notes & Remarks</h3>
                <p class="text-amber-800 dark:text-amber-200 leading-relaxed whitespace-pre-wrap">{{ $record->notes }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Action Buttons --}}
    <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-xl p-6 border-2 border-gray-200 dark:border-gray-700 shadow-lg">
        <h3 class="text-lg font-black text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            Quick Actions
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <a href="{{ Storage::disk('public')->url($filePath) }}"
               target="_blank"
               class="group relative overflow-hidden flex items-center justify-center gap-3 px-6 py-4 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 rounded-xl font-bold text-gray-700 dark:text-gray-200 hover:border-primary-500 dark:hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-300 shadow-md hover:shadow-xl">
                <div class="absolute inset-0 bg-gradient-to-r from-primary-500/0 via-primary-500/0 to-primary-500/0 group-hover:from-primary-500/5 group-hover:via-primary-500/10 group-hover:to-primary-500/5 transition-all duration-300"></div>
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span class="relative">Download CSV</span>
            </a>
            <button type="button"
                    onclick="navigator.clipboard.writeText('{{ $filePath }}').then(() => {
                        const btn = event.target.closest('button');
                        btn.classList.add('!bg-green-500', '!text-white', '!border-green-500');
                        const span = btn.querySelector('span');
                        span.textContent = 'Copied!';
                        setTimeout(() => {
                            btn.classList.remove('!bg-green-500', '!text-white', '!border-green-500');
                            span.textContent = 'Copy File Path';
                        }, 2000);
                    })"
                    class="group relative overflow-hidden flex items-center justify-center gap-3 px-6 py-4 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 rounded-xl font-bold text-gray-700 dark:text-gray-200 hover:border-primary-500 dark:hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-300 shadow-md hover:shadow-xl cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-r from-primary-500/0 via-primary-500/0 to-primary-500/0 group-hover:from-primary-500/5 group-hover:via-primary-500/10 group-hover:to-primary-500/5 transition-all duration-300"></div>
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <span class="relative">Copy File Path</span>
            </button>
        </div>
    </div>

    {{-- Technical Details (Admin Only) --}}
    @if(Auth::user()->isAdmin())
    <div class="bg-gray-900 dark:bg-black rounded-xl p-6 border-2 border-gray-700 shadow-lg font-mono text-sm">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-3 h-3 rounded-full bg-red-500"></div>
            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
            <div class="w-3 h-3 rounded-full bg-green-500"></div>
            <span class="ml-2 text-gray-400 text-xs">Technical Details</span>
        </div>
        <div class="space-y-2 text-green-400">
            <div><span class="text-gray-500">$</span> record_id: <span class="text-yellow-400">{{ $record->id }}</span></div>
            <div><span class="text-gray-500">$</span> employee_id: <span class="text-yellow-400">{{ $record->employee_id }}</span></div>
            <div><span class="text-gray-500">$</span> file_path: <span class="text-blue-400">"{{ $filePath }}"</span></div>
            <div><span class="text-gray-500">$</span> created: <span class="text-purple-400">{{ $record->created_at->toIso8601String() }}</span></div>
        </div>
    </div>
    @endif
</div>
