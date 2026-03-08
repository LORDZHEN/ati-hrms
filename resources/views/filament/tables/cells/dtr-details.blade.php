{{-- resources/views/filament/tables/cells/dtr-details.blade.php --}}
{{-- Slide-over content for the "View Details" action in DailyTimeRecordResource --}}

@php
    use Illuminate\Support\Facades\Storage;
    use App\Services\DtrCalculator;

    $filePath = is_array($record->file_path) ? ($record->file_path[0] ?? '') : $record->file_path;
    $fullPath = Storage::disk('public')->path($filePath);
    $calculated = [];
    $error = null;

    if (!file_exists($fullPath)) {
        $error = 'The source CSV file could not be found on disk.';
    } else {
        try {
            $calculated = app(DtrCalculator::class)->calculateFromCsv($fullPath);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
    }

    $totalLate      = collect($calculated)->sum('Late');
    $totalUndertime = collect($calculated)->sum('Undertime');
    $totalOvertime  = collect($calculated)->sum('Overtime');
    $workedRows     = collect($calculated)->filter(fn($r) => !($r['IsWeekend'] ?? false) && !empty($r['WorkedHours']) && $r['WorkedHours'] !== '0:00');
    $totalWorkedMins = $workedRows->reduce(function ($carry, $r) {
        [$h, $m] = explode(':', $r['WorkedHours']);
        return $carry + ((int)$h * 60) + (int)$m;
    }, 0);
@endphp

<div class="space-y-4 p-1">

    {{-- Employee Info Card --}}
    <div class="flex items-center gap-4 p-4 bg-primary-50 dark:bg-primary-950 rounded-xl border border-primary-200 dark:border-primary-800">
        <div class="flex items-center justify-center w-14 h-14 rounded-full bg-primary-500 text-white font-bold text-xl shadow">
            {{ strtoupper(substr($record->employee->name, 0, 2)) }}
        </div>
        <div>
            <div class="text-base font-bold text-gray-900 dark:text-white">{{ $record->employee->name }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $record->employee->email }}</div>
            <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                Uploaded {{ $record->created_at->format('M d, Y h:i A') }}
                ({{ $record->created_at->diffForHumans() }})
            </div>
        </div>
    </div>

    @if ($error)
        <div class="p-4 bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300 text-sm">
            <strong>Error:</strong> {{ $error }}
        </div>
    @else

        {{-- Summary Stats --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="p-3 bg-blue-50 dark:bg-blue-950/40 rounded-lg border border-blue-200 dark:border-blue-800 text-center">
                <div class="text-lg font-bold text-blue-700 dark:text-blue-300">
                    {{ floor($totalWorkedMins / 60) }}h {{ $totalWorkedMins % 60 }}m
                </div>
                <div class="text-xs text-blue-500 mt-1">Hours Worked</div>
            </div>
            <div class="p-3 bg-red-50 dark:bg-red-950/40 rounded-lg border border-red-200 dark:border-red-800 text-center">
                <div class="text-lg font-bold text-red-700 dark:text-red-300">{{ $totalLate }} mins</div>
                <div class="text-xs text-red-500 mt-1">Total Late</div>
            </div>
            <div class="p-3 bg-amber-50 dark:bg-amber-950/40 rounded-lg border border-amber-200 dark:border-amber-800 text-center">
                <div class="text-lg font-bold text-amber-700 dark:text-amber-300">{{ $totalUndertime }} mins</div>
                <div class="text-xs text-amber-500 mt-1">Total Undertime</div>
            </div>
            <div class="p-3 bg-green-50 dark:bg-green-950/40 rounded-lg border border-green-200 dark:border-green-800 text-center">
                <div class="text-lg font-bold text-green-700 dark:text-green-300">{{ $totalOvertime }} mins</div>
                <div class="text-xs text-green-500 mt-1">Total Overtime</div>
            </div>
        </div>

        {{-- Notes --}}
        @if ($record->notes)
            <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-300">
                <span class="font-semibold">Notes:</span> {{ $record->notes }}
            </div>
        @endif

        {{-- DTR Table --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full text-xs text-left">
                <thead class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 uppercase text-xs">
                    <tr>
                        <th class="px-3 py-2">Date</th>
                        <th class="px-3 py-2 text-center">AM In</th>
                        <th class="px-3 py-2 text-center">AM Out</th>
                        <th class="px-3 py-2 text-center">PM In</th>
                        <th class="px-3 py-2 text-center">PM Out</th>
                        <th class="px-3 py-2 text-center">Late</th>
                        <th class="px-3 py-2 text-center">UT</th>
                        <th class="px-3 py-2 text-center">OT</th>
                        <th class="px-3 py-2 text-center">Worked</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($calculated as $row)
                        <tr class="{{ ($row['IsWeekend'] ?? false) ? 'bg-gray-50 dark:bg-gray-900/50 text-gray-400' : 'bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200' }}">
                            <td class="px-3 py-1.5 font-medium whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($row['Date'])->format('M d, D') }}
                                @if($row['IsWeekend'] ?? false)
                                    <span class="text-xs text-gray-400">(weekend)</span>
                                @endif
                            </td>
                            <td class="px-3 py-1.5 text-center">{{ $row['MorningIn']    ?: '—' }}</td>
                            <td class="px-3 py-1.5 text-center">{{ $row['MorningOut']   ?: '—' }}</td>
                            <td class="px-3 py-1.5 text-center">{{ $row['AfternoonIn']  ?: '—' }}</td>
                            <td class="px-3 py-1.5 text-center">{{ $row['AfternoonOut'] ?: '—' }}</td>
                            <td class="px-3 py-1.5 text-center {{ $row['Late'] > 0 ? 'text-red-500 font-semibold' : '' }}">
                                {{ $row['Late'] > 0 ? $row['Late'].'m' : '—' }}
                            </td>
                            <td class="px-3 py-1.5 text-center {{ $row['Undertime'] > 0 ? 'text-amber-500 font-semibold' : '' }}">
                                {{ $row['Undertime'] > 0 ? $row['Undertime'].'m' : '—' }}
                            </td>
                            <td class="px-3 py-1.5 text-center {{ $row['Overtime'] > 0 ? 'text-green-500 font-semibold' : '' }}">
                                {{ $row['Overtime'] > 0 ? $row['Overtime'].'m' : '—' }}
                            </td>
                            <td class="px-3 py-1.5 text-center font-medium">
                                {{ $row['WorkedHours'] ?: '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-3 py-6 text-center text-gray-400">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @endif
</div>
