{{-- resources/views/filament/tables/cells/dtr-details.blade.php --}}
{{-- Slide-over content for the "View Details" action in DailyTimeRecordResource --}}

@php
    use Illuminate\Support\Facades\Storage;
    use Carbon\Carbon;
    use App\Services\DtrCalculator;

    $filePath   = is_array($record->file_path) ? ($record->file_path[0] ?? '') : $record->file_path;
    $fullPath   = Storage::disk('public')->path($filePath);
    $calculated = [];
    $error      = null;

    if (!file_exists($fullPath)) {
        $error = 'The source CSV file could not be found on disk.';
    } else {
        try {
            $calculated = app(DtrCalculator::class)->calculateFromCsv($fullPath);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
    }

    // Stats
    $totalLate         = 0;
    $lateCount         = 0;
    $totalUndertime    = 0;
    $utCount           = 0;
    $totalOvertimeMins = 0;
    $presentDays       = 0;
    $totalWorkedMins   = 0;

    foreach ($calculated as $r) {
        if ($r['IsWeekend'] ?? false) continue;
        $late = (int)($r['Late']      ?? 0);
        $ut   = (int)($r['Undertime'] ?? 0);
        $ot   = (int)($r['Overtime']  ?? 0);
        if ($late > 0) { $totalLate      += $late; $lateCount++; }
        if ($ut   > 0) { $totalUndertime += $ut;   $utCount++; }
        $totalOvertimeMins += $ot;
        if (!empty($r['MorningIn']) || !empty($r['AfternoonIn'])) $presentDays++;
        if (!empty($r['WorkedHours']) && $r['WorkedHours'] !== '0:00') {
            [$h, $m] = explode(':', $r['WorkedHours']);
            $totalWorkedMins += ((int)$h * 60) + (int)$m;
        }
    }

    // Date range info
    $firstRecord = collect($calculated)->first();
    $firstDate   = $firstRecord ? Carbon::parse($firstRecord['Date']) : now();
    $monthStart  = $firstDate->copy()->startOfMonth();
    $daysInMonth = (int) $monthStart->daysInMonth;
    $monthLabel  = $firstDate->format('F Y');
    $periodFrom  = $monthStart->format('Y/m/d');
    $periodTo    = $monthStart->copy()->endOfMonth()->format('m/d');

    // Working days in month
    $workingDays = 0;
    for ($d = 1; $d <= $daysInMonth; $d++) {
        if (!$monthStart->copy()->addDays($d - 1)->isWeekend()) $workingDays++;
    }
    $absentDays = max(0, $workingDays - $presentDays);

    $roleLabel = strtolower($record->employee->role ?? '') === 'job_order' ? 'JOB ORDER' : 'REGULAR';
    $empNo     = $record->employee->employee_id ?? '—';

    // Group by day
    $byDay = [];
    foreach ($calculated as $r) {
        $day = (int) Carbon::parse($r['Date'])->format('j');
        $byDay[$day] = $r;
    }
@endphp

<div class="space-y-4 p-1 text-sm">

    @if ($error)
        <div class="p-4 bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300 text-sm">
            <strong>Error:</strong> {{ $error }}
        </div>
    @else

    {{-- ── INFO HEADER BLOCK (mirrors screenshot layout) ── --}}
    <div class="rounded-lg border border-blue-300 dark:border-blue-700 overflow-hidden text-xs font-mono">

        {{-- Row 1: Period | Dept | Role | Name label | Name value --}}
        <div class="flex border-b border-blue-300 dark:border-blue-700">
            <div class="flex-1 px-2 py-1 border-r border-blue-300 dark:border-blue-700">
                <div class="text-[10px] text-blue-600 dark:text-blue-400">Period :</div>
                <div class="font-bold text-gray-800 dark:text-gray-100 text-xs">{{ $periodFrom }} ~ {{ $periodTo }}</div>
            </div>
            <div class="w-16 px-2 py-1 border-r border-blue-300 dark:border-blue-700">
                <div class="text-[10px] text-blue-600 dark:text-blue-400">Depart<br>ment</div>
            </div>
            <div class="w-24 px-2 py-1 border-r border-blue-300 dark:border-blue-700 flex items-center justify-center">
                <span class="font-bold text-blue-700 dark:text-blue-300 text-sm">{{ $roleLabel }}</span>
            </div>
            <div class="w-12 px-2 py-1 border-r border-blue-300 dark:border-blue-700 flex items-center">
                <span class="text-[10px] text-blue-600 dark:text-blue-400">Name</span>
            </div>
            <div class="flex-1 px-2 py-1 flex items-center">
                <span class="font-bold text-blue-700 dark:text-blue-300 text-sm truncate">{{ strtoupper($record->employee->name) }}</span>
            </div>
        </div>

        {{-- Row 2: Date label | date value | spacer | No label | No value --}}
        <div class="flex border-b border-blue-300 dark:border-blue-700">
            <div class="w-12 px-2 py-1 border-r border-blue-300 dark:border-blue-700">
                <div class="text-[10px] text-blue-600 dark:text-blue-400">Date</div>
            </div>
            <div class="w-40 px-2 py-1 border-r border-blue-300 dark:border-blue-700">
                <div class="font-bold text-gray-800 dark:text-gray-100">{{ $periodFrom }} ~ {{ $periodTo }}</div>
            </div>
            <div class="flex-1 border-r border-blue-300 dark:border-blue-700"></div>
            <div class="w-10 px-2 py-1 border-r border-blue-300 dark:border-blue-700">
                <div class="text-[10px] text-blue-600 dark:text-blue-400">No</div>
            </div>
            <div class="w-16 px-2 py-1">
                <div class="font-bold text-blue-700 dark:text-blue-300">{{ $empNo }}</div>
            </div>
        </div>

        {{-- Row 3: Stats grid — AB | L | BT | I/O(dd) | Over(hh)[Over/Sp] | Late[(ts)/(min)] | Early Leave[(ts)/(min)] --}}
        <div class="flex">
            {{-- AB --}}
            <div class="w-12 border-r border-blue-300 dark:border-blue-700 text-center">
                <div class="text-[9px] text-blue-600 dark:text-blue-400 border-b border-blue-200 dark:border-blue-700 px-1 py-0.5">AB</div>
                <div class="font-bold py-1 text-gray-800 dark:text-gray-100">{{ $absentDays ?: '' }}</div>
            </div>
            {{-- L --}}
            <div class="w-10 border-r border-blue-300 dark:border-blue-700 text-center">
                <div class="text-[9px] text-blue-600 dark:text-blue-400 border-b border-blue-200 dark:border-blue-700 px-1 py-0.5">L</div>
                <div class="font-bold py-1">&nbsp;</div>
            </div>
            {{-- BT --}}
            <div class="w-10 border-r border-blue-300 dark:border-blue-700 text-center">
                <div class="text-[9px] text-blue-600 dark:text-blue-400 border-b border-blue-200 dark:border-blue-700 px-1 py-0.5">BT</div>
                <div class="font-bold py-1 text-gray-800 dark:text-gray-100">{{ $workingDays }}</div>
            </div>
            {{-- I/O(dd) --}}
            <div class="w-14 border-r border-blue-300 dark:border-blue-700 text-center">
                <div class="text-[9px] text-blue-600 dark:text-blue-400 border-b border-blue-200 dark:border-blue-700 px-1 py-0.5">I/O<br>(dd)</div>
                <div class="font-bold py-1 text-gray-800 dark:text-gray-100">{{ $presentDays }}</div>
            </div>
            {{-- Over(hh) --}}
            <div class="flex-1 border-r border-blue-300 dark:border-blue-700 text-center">
                <div class="text-[9px] text-blue-600 dark:text-blue-400 border-b border-blue-200 dark:border-blue-700 px-1 py-0.5">Over(hh)</div>
                <div class="flex">
                    <div class="flex-1 border-r border-blue-200 dark:border-blue-700">
                        <div class="text-[8px] text-blue-500 border-b border-blue-200 dark:border-blue-700 py-0.5">Over</div>
                        <div class="font-bold py-0.5 text-gray-800 dark:text-gray-100">{{ $totalOvertimeMins > 0 ? floor($totalOvertimeMins/60) : '' }}</div>
                    </div>
                    <div class="flex-1">
                        <div class="text-[8px] text-blue-500 border-b border-blue-200 dark:border-blue-700 py-0.5">Sp</div>
                        <div class="font-bold py-0.5">&nbsp;</div>
                    </div>
                </div>
            </div>
            {{-- Late --}}
            <div class="flex-1 border-r border-blue-300 dark:border-blue-700 text-center">
                <div class="text-[9px] text-blue-600 dark:text-blue-400 border-b border-blue-200 dark:border-blue-700 px-1 py-0.5">Late</div>
                <div class="flex">
                    <div class="flex-1 border-r border-blue-200 dark:border-blue-700">
                        <div class="text-[8px] text-blue-500 border-b border-blue-200 dark:border-blue-700 py-0.5">(ts)</div>
                        <div class="font-bold py-0.5 text-red-600 dark:text-red-400">{{ $lateCount ?: '' }}</div>
                    </div>
                    <div class="flex-1">
                        <div class="text-[8px] text-blue-500 border-b border-blue-200 dark:border-blue-700 py-0.5">(min)</div>
                        <div class="font-bold py-0.5 text-red-600 dark:text-red-400">{{ $totalLate ?: '' }}</div>
                    </div>
                </div>
            </div>
            {{-- Early Leave --}}
            <div class="flex-1 text-center">
                <div class="text-[9px] text-blue-600 dark:text-blue-400 border-b border-blue-200 dark:border-blue-700 px-1 py-0.5">Early Leave</div>
                <div class="flex">
                    <div class="flex-1 border-r border-blue-200 dark:border-blue-700">
                        <div class="text-[8px] text-blue-500 border-b border-blue-200 dark:border-blue-700 py-0.5">(ts)</div>
                        <div class="font-bold py-0.5 text-amber-600 dark:text-amber-400">{{ $utCount ?: '' }}</div>
                    </div>
                    <div class="flex-1">
                        <div class="text-[8px] text-blue-500 border-b border-blue-200 dark:border-blue-700 py-0.5">(min)</div>
                        <div class="font-bold py-0.5 text-amber-600 dark:text-amber-400">{{ $totalUndertime ?: '' }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /info header --}}

    {{-- ── SCHEDULE BANNER ── --}}
    <div class="text-xs text-center text-blue-600 dark:text-blue-400 italic border border-blue-200 dark:border-blue-700 rounded py-1">
        1.&nbsp; 08:00-12:00,&nbsp;&nbsp; 13:00-17:00
    </div>

    {{-- ── ATTENDANCE TABLE ── --}}
    <div class="rounded-lg border border-blue-300 dark:border-blue-700 overflow-hidden">
        <table class="w-full text-xs border-collapse">
            {{-- Title row --}}
            <thead>
                <tr>
                    <th colspan="7" class="text-center py-1.5 text-blue-700 dark:text-blue-300 font-bold text-sm tracking-wide border-b border-blue-300 dark:border-blue-700 bg-white dark:bg-gray-900">
                        Attendance Table
                    </th>
                </tr>
                {{-- Group headers --}}
                <tr class="bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-300 text-[11px] font-bold">
                    <th class="border border-blue-200 dark:border-blue-700 px-1 py-1 w-14" rowspan="2">dd/ww</th>
                    <th class="border border-blue-200 dark:border-blue-700 px-1 py-1" colspan="2">AM</th>
                    <th class="border border-blue-200 dark:border-blue-700 px-1 py-1" colspan="2">PM</th>
                    <th class="border border-blue-200 dark:border-blue-700 px-1 py-1" colspan="2">Over</th>
                </tr>
                {{-- Sub-headers --}}
                <tr class="bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 text-[10px] font-semibold">
                    <th class="border border-blue-200 dark:border-blue-700 px-1 py-0.5 min-w-[56px]">In</th>
                    <th class="border border-blue-200 dark:border-blue-700 px-1 py-0.5 min-w-[56px]">Out</th>
                    <th class="border border-blue-200 dark:border-blue-700 px-1 py-0.5 min-w-[56px]">In</th>
                    <th class="border border-blue-200 dark:border-blue-700 px-1 py-0.5 min-w-[56px]">Out</th>
                    <th class="border border-blue-200 dark:border-blue-700 px-1 py-0.5 min-w-[44px]">In</th>
                    <th class="border border-blue-200 dark:border-blue-700 px-1 py-0.5 min-w-[44px]">Out</th>
                </tr>
            </thead>
            <tbody>
                @for ($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $date     = $monthStart->copy()->addDays($day - 1);
                        $isWeek   = $date->isWeekend();
                        $r        = $byDay[$day] ?? null;
                        $dow      = strtoupper(substr($date->format('D'), 0, 2));
                        $dayStr   = str_pad($day, 2, '0', STR_PAD_LEFT) . ' ' . $dow;
                        $inRange  = ($r !== null);
                        $hasPunch = $inRange && (
                            !empty($r['MorningIn']) || !empty($r['MorningOut']) ||
                            !empty($r['AfternoonIn']) || !empty($r['AfternoonOut'])
                        );
                        $isAbsent = !$isWeek && $inRange && !$hasPunch;
                        $mi = $r ? ($r['MorningIn']    ?? '') : '';
                        $mo = $r ? ($r['MorningOut']   ?? '') : '';
                        $ai = $r ? ($r['AfternoonIn']  ?? '') : '';
                        $ao = $r ? ($r['AfternoonOut'] ?? '') : '';
                    @endphp
                    <tr class="{{ $isWeek
                        ? 'bg-gray-50 dark:bg-gray-800/30'
                        : ($isAbsent ? 'bg-red-50/40 dark:bg-red-950/10' : 'bg-white dark:bg-gray-900') }}">

                        {{-- Day cell --}}
                        <td class="border border-blue-200 dark:border-blue-700 px-1.5 py-0.5 text-left font-medium
                            {{ $isWeek ? 'text-gray-500 dark:text-gray-400' : 'text-gray-800 dark:text-gray-200' }}">
                            {{ $dayStr }}
                        </td>

                        @if ($isWeek)
                            <td class="border border-blue-200 dark:border-blue-700 py-0.5" colspan="6"></td>
                        @elseif ($isAbsent)
                            <td colspan="6" class="border border-blue-200 dark:border-blue-700 py-0.5 text-center italic text-red-500 dark:text-red-400 text-[11px]">
                                Absence
                            </td>
                        @else
                            <td class="border border-blue-200 dark:border-blue-700 px-1 py-0.5 text-center text-blue-700 dark:text-blue-300">{{ $mi }}</td>
                            <td class="border border-blue-200 dark:border-blue-700 px-1 py-0.5 text-center text-blue-700 dark:text-blue-300">{{ $mo }}</td>
                            <td class="border border-blue-200 dark:border-blue-700 px-1 py-0.5 text-center text-blue-700 dark:text-blue-300">{{ $ai }}</td>
                            <td class="border border-blue-200 dark:border-blue-700 px-1 py-0.5 text-center text-blue-700 dark:text-blue-300">{{ $ao }}</td>
                            <td class="border border-blue-200 dark:border-blue-700 px-1 py-0.5 text-center"></td>
                            <td class="border border-blue-200 dark:border-blue-700 px-1 py-0.5 text-center"></td>
                        @endif
                    </tr>
                @endfor
            </tbody>
            {{-- Footer totals --}}
            <tfoot>
                <tr class="bg-blue-50 dark:bg-blue-950/30 font-bold text-blue-700 dark:text-blue-300 text-[11px]">
                    <td colspan="5" class="border border-blue-200 dark:border-blue-700 px-2 py-1 text-right italic text-blue-600 dark:text-blue-400">
                        TOTAL for the month
                    </td>
                    <td class="border border-blue-200 dark:border-blue-700 px-1 py-1 text-center">
                        {{ $totalWorkedMins > 0 ? floor($totalWorkedMins/60) : '' }}
                    </td>
                    <td class="border border-blue-200 dark:border-blue-700 px-1 py-1 text-center">
                        {{ $totalWorkedMins > 0 ? ($totalWorkedMins % 60) : '' }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- ── NOTES ── --}}
    @if ($record->notes)
        <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-300">
            <span class="font-semibold text-blue-600 dark:text-blue-400">Notes:</span> {{ $record->notes }}
        </div>
    @endif

    {{-- ── LEGEND ── --}}
    <div class="text-[11px] text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700 pt-2 grid grid-cols-2 gap-x-4 gap-y-0.5">
        <div><strong>AB</strong> – Absences &nbsp; <strong>L</strong> – Leaves</div>
        <div><strong>BT</strong> – Working Days &nbsp; <strong>I/O</strong> – Present Days</div>
        <div><strong>ts</strong> – Count (occurrences) &nbsp; <strong>min</strong> – Minutes</div>
        <div><strong>Over</strong> – Overtime &nbsp; <strong>Sp</strong> – Special OT</div>
    </div>

    @endif
</div>
