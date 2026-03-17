{{-- resources/views/filament/tables/cells/dtr-details.blade.php --}}
{{--
    HEADER — 2 rows, NO rowspan on left column:
      Row 1: [Department] [    REGULAR    ] [Name] [Employee Name]
      Row 2: [Date      ] [2026/02/01~02/28] [No  ] [Employee ID  ]
--}}

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

    $firstRecord = collect($calculated)->first();
    $firstDate   = $firstRecord ? Carbon::parse($firstRecord['Date']) : now();
    $monthStart  = $firstDate->copy()->startOfMonth();
    $daysInMonth = (int) $monthStart->daysInMonth;

    $periodFrom = $monthStart->format('Y/m/d');
    $periodTo   = $monthStart->copy()->endOfMonth()->format('m/d');
    $periodFull = $periodFrom . ' ~ ' . $periodTo;

    $byDay = [];
    foreach ($calculated as $r) {
        $byDay[(int) Carbon::parse($r['Date'])->format('j')] = $r;
    }

    $ioVal = $lateCount = $lateMins = $earlyCount = $earlyMins = 0;

    for ($d = 1; $d <= $daysInMonth; $d++) {
        $r = $byDay[$d] ?? null;
        if (!$r) continue;
        $mi = trim($r['MorningIn']   ?? '');
        $mo = trim($r['MorningOut']  ?? '');
        $ai = trim($r['AfternoonIn'] ?? '');
        if ($mi !== '' && ($mo !== '' || $ai !== '')) { $ioVal++; }
        $lm = (int)($r['LateMinutes']      ?? 0);
        $em = (int)($r['UndertimeMinutes'] ?? 0);
        if ($lm > 0) { $lateCount++;  $lateMins  += $lm; }
        if ($em > 0) { $earlyCount++; $earlyMins += $em; }
    }

    $abVal   = $daysInMonth - $ioVal;
    $empNo   = $record->employee->employee_id ?? '—';
    $empRole = strtolower($record->employee->role ?? '') === 'job_order' ? 'JOB ORDER' : 'REGULAR';

    $fSong  = "'SimSun','宋体','Songti SC','Noto Serif CJK SC','Times New Roman',serif";
    $fArial = "Arial,Helvetica,sans-serif";
    $bdr    = "border:1px solid #3949ab;";
    $bdrL   = "border:1px solid #9fa8da;";
@endphp

<div class="p-1 text-xs" style="font-family:{{ $fSong }}; color:#1a237e;">

    @if ($error)
        <div class="p-3 bg-red-50 dark:bg-red-950 border border-red-300 rounded text-red-700 dark:text-red-300 text-xs">
            <strong>Error:</strong> {{ $error }}
        </div>
    @else

    {{-- ══════════════════════════════════════════════════════
         HEADER — 2 rows, NO rowspan on left column
         Row 1: [Department] [ REGULAR ] [Name] [Emp Name]
         Row 2: [Date      ] [date val ] [No  ] [Emp ID  ]
         ══════════════════════════════════════════════════════ --}}
    <table style="border-collapse:collapse; width:100%; table-layout:fixed; font-family:{{ $fSong }};">
        <colgroup>
            <col style="width:11%">
            <col style="width:40%">
            <col style="width:8%">
            <col style="width:41%">
        </colgroup>

        {{-- Row 1: Department | REGULAR | Name | Employee Name --}}
        <tr>
            <td style="{{ $bdr }} text-align:center; font-size:9px; font-weight:normal;
                       vertical-align:middle; background:#eef0ff; padding:3px 2px;
                       color:#1a237e; font-family:{{ $fSong }};">
                Department
            </td>
            <td style="{{ $bdr }} text-align:center; font-size:20px; font-weight:bold;
                       letter-spacing:3px; padding:5px 3px; color:#1a237e;
                       font-family:{{ $fSong }};">
                {{ $empRole }}
            </td>
            <td style="{{ $bdr }} text-align:center; font-size:9px; font-weight:normal;
                       background:#eef0ff; color:#1a237e; padding:2px 1px;
                       font-family:{{ $fSong }};">
                Name
            </td>
            <td style="{{ $bdr }} text-align:center; font-size:16px; font-weight:bold;
                       padding:5px 3px; color:#1a237e; font-family:{{ $fSong }};">
                {{ strtoupper($record->employee->name) }}
            </td>
        </tr>

        {{-- Row 2: Date (label) | date value | No | Employee ID --}}
        <tr>
            <td style="{{ $bdr }} text-align:center; font-size:9px; font-weight:normal;
                       vertical-align:middle; background:#eef0ff; padding:3px 2px;
                       color:#1a237e; font-family:{{ $fSong }};">
                Date
            </td>
            <td style="{{ $bdr }} text-align:center; font-size:13px; font-weight:bold;
                       padding:4px 3px; color:#1a237e; font-family:{{ $fSong }};">
                {{ $periodFull }}
            </td>
            <td style="{{ $bdr }} text-align:center; font-size:9px; font-weight:normal;
                       background:#eef0ff; color:#1a237e; padding:2px 1px;
                       font-family:{{ $fSong }};">
                No
            </td>
            <td style="{{ $bdr }} text-align:center; font-size:16px; font-weight:bold;
                       padding:4px 3px; color:#1a237e; font-family:{{ $fSong }};">
                {{ $empNo }}
            </td>
        </tr>
    </table>

    {{-- ══ SUMMARY TABLE ══ --}}
    <table style="border-collapse:collapse; width:100%; table-layout:fixed; font-family:{{ $fSong }};">
        <colgroup>
            <col style="width:7%"><col style="width:6%"><col style="width:7%">
            <col style="width:8%"><col style="width:8.5%"><col style="width:7%">
            <col style="width:9.5%"><col style="width:9.5%">
            <col style="width:9.5%"><col style="width:9.5%">
        </colgroup>
        <thead>
            <tr style="background:#c5cae9; text-align:center; font-size:9px; color:#1a237e; font-family:{{ $fSong }};">
                <th style="{{ $bdr }} padding:2px 1px; font-weight:normal;" rowspan="2">AB</th>
                <th style="{{ $bdr }} padding:2px 1px; font-weight:normal;" rowspan="2">L</th>
                <th style="{{ $bdr }} padding:2px 1px; font-weight:normal;" rowspan="2">BT</th>
                <th style="{{ $bdr }} padding:1px 0; font-size:8px; line-height:1.3; font-weight:normal;" rowspan="2">I/O<br>(dd)</th>
                <th style="{{ $bdr }} padding:2px 1px; font-weight:normal;" colspan="2">Over(hh)</th>
                <th style="{{ $bdr }} padding:2px 1px; font-weight:normal;" colspan="2">Late</th>
                <th style="{{ $bdr }} padding:2px 1px; font-weight:normal;" colspan="2">Early Leave</th>
            </tr>
            <tr style="background:#e8eaf6; text-align:center; font-size:8px; color:#3949ab; font-family:{{ $fSong }};">
                <th style="{{ $bdr }} padding:1px 0; font-weight:normal;">Over</th>
                <th style="{{ $bdr }} padding:1px 0; font-weight:normal;">Sp</th>
                <th style="{{ $bdr }} padding:1px 0; font-weight:normal;">(ts)</th>
                <th style="{{ $bdr }} padding:1px 0; font-weight:normal;">(min)</th>
                <th style="{{ $bdr }} padding:1px 0; font-weight:normal;">(ts)</th>
                <th style="{{ $bdr }} padding:1px 0; font-weight:normal;">(min)</th>
            </tr>
        </thead>
        <tbody>
            <tr style="text-align:center; font-size:14px; color:#1a237e; font-family:{{ $fSong }};">
                <td style="{{ $bdr }} padding:3px 1px;">{{ $abVal      ?: '' }}</td>
                <td style="{{ $bdr }} padding:3px 1px;"></td>
                <td style="{{ $bdr }} padding:3px 1px;"></td>
                <td style="{{ $bdr }} padding:3px 1px;">{{ $ioVal      ?: '' }}</td>
                <td style="{{ $bdr }} padding:3px 1px;"></td>
                <td style="{{ $bdr }} padding:3px 1px;"></td>
                <td style="{{ $bdr }} padding:3px 1px;">{{ $lateCount  ?: '' }}</td>
                <td style="{{ $bdr }} padding:3px 1px;">{{ $lateMins   ?: '' }}</td>
                <td style="{{ $bdr }} padding:3px 1px;">{{ $earlyCount ?: '' }}</td>
                <td style="{{ $bdr }} padding:3px 1px;">{{ $earlyMins  ?: '' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ══ SCHEDULE — centered ══ --}}
    <table style="border-collapse:collapse; width:100%; font-family:{{ $fSong }};">
        <tr>
            <td style="{{ $bdr }} font-size:9px; padding:3px 5px; color:#1a237e;
                       text-align:center; font-family:{{ $fSong }};">
                1.&nbsp;&nbsp; 08:00-12:00,&nbsp;&nbsp; 13:00-17:00
            </td>
        </tr>
    </table>

    {{-- ══ ATTENDANCE TABLE ══ --}}
    <table style="border-collapse:collapse; width:100%; table-layout:fixed; font-family:{{ $fArial }};">
        <colgroup>
            <col style="width:9%">
            <col style="width:13.5%"><col style="width:13.5%">
            <col style="width:13.5%"><col style="width:13.5%">
            <col style="width:18.5%"><col style="width:18.5%">
        </colgroup>
        <thead>
            <tr>
                <th colspan="7"
                    style="{{ $bdr }} background:#fff; text-align:center; font-size:14px;
                           font-weight:bold; letter-spacing:1px; padding:5px 0;
                           color:#1a237e; font-family:{{ $fSong }};">
                    Attendance Table
                </th>
            </tr>
            <tr style="background:#c5cae9; text-align:center; color:#1a237e; font-size:10px; font-family:{{ $fSong }};">
                <th rowspan="2"
                    style="{{ $bdr }} padding:2px 1px; background:#e8eaf6; font-size:8px;
                           vertical-align:middle; font-weight:normal; font-family:{{ $fArial }};">
                    dd/ww
                </th>
                <th colspan="2" style="{{ $bdr }} padding:2px 0; font-weight:normal;">AM</th>
                <th colspan="2" style="{{ $bdr }} padding:2px 0; font-weight:normal;">PM</th>
                <th colspan="2" style="{{ $bdr }} padding:2px 0; font-weight:normal;">Over</th>
            </tr>
            <tr style="background:#e8eaf6; text-align:center; color:#3949ab; font-size:8px; font-family:{{ $fArial }};">
                <th style="{{ $bdr }} padding:2px 0; font-weight:normal;">In</th>
                <th style="{{ $bdr }} padding:2px 0; font-weight:normal;">Out</th>
                <th style="{{ $bdr }} padding:2px 0; font-weight:normal;">In</th>
                <th style="{{ $bdr }} padding:2px 0; font-weight:normal;">Out</th>
                <th style="{{ $bdr }} padding:2px 0; font-weight:normal;">In</th>
                <th style="{{ $bdr }} padding:2px 0; font-weight:normal;">Out</th>
            </tr>
        </thead>
        <tbody>
            @for ($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $date      = $monthStart->copy()->addDays($day - 1);
                    $isWeekend = $date->isWeekend();
                    $row       = $byDay[$day] ?? null;
                    $dow       = ucfirst(strtolower(substr($date->format('D'), 0, 2)));
                    $ddww      = str_pad($day, 2, '0', STR_PAD_LEFT) . ' ' . $dow;
                    $mi = trim($row['MorningIn']    ?? '');
                    $mo = trim($row['MorningOut']   ?? '');
                    $ai = trim($row['AfternoonIn']  ?? '');
                    $ao = trim($row['AfternoonOut'] ?? '');
                    $hasPunch      = ($mi !== '' || $mo !== '' || $ai !== '' || $ao !== '');
                    $isAbsent      = !$isWeekend && !$hasPunch;
                    // Red AfternoonOut: punch recorded but employee left before 17:00
                    $aoIsUndertime = ($ao !== '' && $ao < '17:00');
                @endphp

                @if ($isWeekend)
                <tr style="background:#f0f0f0; color:#aaaaaa;">
                    <td style="{{ $bdrL }} font-size:8px; text-align:left; padding-left:3px; background:#e8e8ee; color:#999; font-family:{{ $fArial }};">{{ $ddww }}</td>
                    <td style="{{ $bdrL }}"></td><td style="{{ $bdrL }}"></td>
                    <td style="{{ $bdrL }}"></td><td style="{{ $bdrL }}"></td>
                    <td style="{{ $bdrL }}"></td><td style="{{ $bdrL }}"></td>
                </tr>

                @elseif ($isAbsent)
                <tr style="background:#fff5f5;">
                    <td style="{{ $bdrL }} font-size:8px; text-align:left; padding-left:3px; background:#f5f5ff; color:#1a237e; font-family:{{ $fArial }};">{{ $ddww }}</td>
                    <td colspan="6" style="{{ $bdrL }} text-align:center; font-style:italic; font-size:8px; color:#5c6bc0; font-family:{{ $fArial }};">Absence</td>
                </tr>

                @else
                <tr style="background:#ffffff;">
                    <td style="{{ $bdrL }} font-size:8px; text-align:left; padding-left:3px; background:#f5f5ff; color:#1a237e; font-family:{{ $fArial }};">{{ $ddww }}</td>

                    {{--
                        ── LATE TIME-IN HIGHLIGHT ────────────────────────────────────────────
                        MorningIn is shown in red when the employee arrived after 08:00.
                        HH:MM string comparison is safe for zero-padded 24-hour times:
                          '08:01' > '08:00' = true  ✓
                          '07:59' > '08:00' = false ✓
                          '08:00' > '08:00' = false ✓  (on time — no red)
                        Only MorningIn is highlighted; MorningOut, AfternoonIn, and
                        AfternoonOut are never coloured red (they are not "arrival" slots).
                        ─────────────────────────────────────────────────────────────────────
                    --}}
                    <td style="{{ $bdrL }} text-align:center; font-size:8px; padding:1px; font-family:{{ $fArial }};
                               {{ ($mi !== '' && $mi > '08:00') ? 'color:#dc2626; font-weight:600;' : 'color:#1a237e;' }}">
                        {{ $mi }}
                    </td>

                    <td style="{{ $bdrL }} text-align:center; font-size:8px; color:#1a237e; padding:1px; font-family:{{ $fArial }};">{{ $mo }}</td>
                    <td style="{{ $bdrL }} text-align:center; font-size:8px; color:#1a237e; padding:1px; font-family:{{ $fArial }};">{{ $ai }}</td>
                    {{-- AfternoonOut: red when employee punched out before 17:00 (undertime) --}}
                    <td style="{{ $bdrL }} text-align:center; font-size:8px; padding:1px; font-family:{{ $fArial }};
                               {{ $aoIsUndertime ? 'color:#dc2626; font-weight:600;' : 'color:#1a237e;' }}">
                        {{ $ao }}
                    </td>
                    <td style="{{ $bdrL }} padding:1px;"></td>
                    <td style="{{ $bdrL }} padding:1px;"></td>
                </tr>
                @endif
            @endfor
        </tbody>
    </table>

    @if ($record->notes)
        <div style="margin-top:6px; padding:6px 8px; background:#f9f9ff;
                    border:1px solid #9fa8da; border-radius:4px;
                    font-size:9px; color:#333; font-family:{{ $fSong }};">
            <strong style="color:#1a237e;">Notes:</strong> {{ $record->notes }}
        </div>
    @endif

    {{-- Legend --}}
    <div style="margin-top:6px; padding:4px 6px; border-top:1px solid #c5cae9; font-family:{{ $fSong }}; color:#444;">
        <table style="border:none; width:100%; border-collapse:collapse;">
            <tr>
                <td style="border:none; font-size:9px; padding:0 4px 0 0; line-height:1.6;">
                    <strong style="color:#1a237e;">AB</strong> – Absent days &nbsp;&nbsp;
                    <strong style="color:#1a237e;">BT</strong> – Blank per CSF-48
                </td>
                <td style="border:none; font-size:9px; padding:0; line-height:1.6;">
                    <strong style="color:#1a237e;">Late (ts)</strong> – Count &nbsp;&nbsp;
                    <strong style="color:#1a237e;">(min)</strong> – Total late minutes
                </td>
            </tr>
            <tr>
                <td style="border:none; font-size:9px; padding:0 4px 0 0; line-height:1.6;">
                    <strong style="color:#1a237e;">I/O(dd)</strong> – Days with valid punch &nbsp;&nbsp;
                    <strong style="color:#1a237e;">Over(hh)</strong> – Official OT only
                </td>
                <td style="border:none; font-size:9px; padding:0; line-height:1.6;">
                    <strong style="color:#1a237e;">Early Leave (ts)</strong> – Count &nbsp;&nbsp;
                    <strong style="color:#1a237e;">(min)</strong> – Total undertime
                </td>
            </tr>
        </table>
    </div>

    @endif
</div>
