{{-- resources/views/exports/dtr_pdf.blade.php --}}
{{--
    Philippine Government DTR — Civil Service Form No. 48

    HEADER — exactly 2 rows, matching right-image reference:
      Row 1: "Department" label | REGULAR (colspan 3)
      Row 2: "Date" label       | Date value | "No" label | Employee ID

    Wait — re-reading the right image:
      Col 1 narrow label | Col 2 REGULAR wide | Col 3 "Name" label | Col 4 Employee Name
      Col 1 "Date" label | Col 2 date value   | Col 3 "No" label   | Col 4 Employee ID

    So: Department cell is ONLY on row 1 (no rowspan).
        Date cell is ONLY on row 2 (no rowspan).
        Each row has its OWN left label cell.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
@page { size: A4 portrait; margin: 10mm 8mm 10mm 8mm; }

html, body {
    width: 100%;
    font-family: 'SimSun', '宋体', 'Songti SC', 'Noto Serif CJK SC', 'Times New Roman', serif;
    font-size: 8pt;
    color: #00008B;
}

table { border-collapse: collapse; width: 100%; }
td, th { border: 1px solid #00008B; padding: 1px 3px; vertical-align: middle; overflow: hidden; }

.page-title {
    text-align: center; font-size: 18pt; font-weight: bold;
    letter-spacing: 3px; color: #00008B; padding-bottom: 1px;
    font-family: 'SimSun', '宋体', 'Songti SC', 'Noto Serif CJK SC', 'Times New Roman', serif;
}
.page-subtitle {
    text-align: center; font-size: 7.5pt; color: #00008B; margin-bottom: 3px;
    font-family: 'SimSun', '宋体', 'Songti SC', 'Noto Serif CJK SC', serif;
}

/* ── Meta header ── */
.meta-tbl { table-layout: fixed; }
/* Left label cells: "Department" row 1, "Date" row 2 */
.meta-tbl .lbl-left {
    font-size: 8pt; font-weight: normal; background: #eeeeff;
    text-align: center; vertical-align: middle; white-space: normal;
    font-family: 'SimSun', '宋体', 'Songti SC', 'Noto Serif CJK SC', serif;
}
/* "Name" / "No" middle label cells */
.meta-tbl .lbl-mid {
    font-size: 8pt; font-weight: normal; background: #eeeeff;
    text-align: center; vertical-align: middle; white-space: nowrap;
    font-family: 'SimSun', '宋体', 'Songti SC', 'Noto Serif CJK SC', serif;
}
/* REGULAR / JOB ORDER */
.meta-tbl .role-cell {
    font-size: 16pt; font-weight: bold; text-align: center;
    letter-spacing: 2px; padding: 4px 3px;
    font-family: 'SimSun', '宋体', 'Songti SC', 'Noto Serif CJK SC', serif;
}
/* Employee name */
.meta-tbl .name-val {
    font-size: 14pt; font-weight: bold; text-align: center; padding: 4px 3px;
    font-family: 'SimSun', '宋体', 'Songti SC', 'Noto Serif CJK SC', serif;
}
/* Date value */
.meta-tbl .date-val {
    font-size: 11pt; font-weight: bold; text-align: center; padding: 3px;
    font-family: 'SimSun', '宋体', 'Songti SC', 'Noto Serif CJK SC', serif;
}
/* Employee ID */
.meta-tbl .id-val {
    font-size: 14pt; font-weight: bold; text-align: center; padding: 3px;
    font-family: 'SimSun', '宋体', 'Songti SC', 'Noto Serif CJK SC', serif;
}

/* ── Summary table ── */
.sum-tbl { table-layout: fixed; }
.sum-tbl th {
    text-align: center; font-size: 8pt; font-weight: normal;
    padding: 2px 1px; background: #d0d0ff;
    font-family: 'SimSun', '宋体', 'Songti SC', 'Noto Serif CJK SC', serif;
}
.sum-tbl th.sub {
    background: #e4e4ff; font-size: 7pt; font-weight: normal; padding: 1px 0;
    font-family: 'SimSun', '宋体', 'Songti SC', 'Noto Serif CJK SC', serif;
}
.sum-tbl td {
    font-size: 12pt; font-weight: normal; text-align: center; padding: 3px 1px;
    font-family: 'SimSun', '宋体', 'Songti SC', 'Noto Serif CJK SC', serif;
}

/* ── Schedule ── */
.sched-tbl td {
    font-size: 8.5pt; padding: 3px 5px; border: 1px solid #00008B; text-align: center;
    font-family: 'SimSun', '宋体', 'Songti SC', 'Noto Serif CJK SC', serif;
}

/* ── Attendance table ── */
.att-tbl { table-layout: fixed; }
.att-tbl th.ttl {
    font-size: 12pt; font-weight: bold; letter-spacing: 1px;
    text-align: center; background: #ffffff; padding: 4px 0;
    font-family: 'SimSun', '宋体', 'Songti SC', 'Noto Serif CJK SC', serif;
}
.att-tbl th.grp {
    font-size: 9pt; font-weight: normal; text-align: center;
    background: #d0d0ff; padding: 2px 0;
    font-family: 'SimSun', '宋体', 'Songti SC', 'Noto Serif CJK SC', serif;
}
.att-tbl th.ddww {
    font-size: 8pt; font-weight: normal; text-align: center;
    background: #e4e4ff; vertical-align: middle;
    font-family: Arial, Helvetica, sans-serif;
}
.att-tbl th.sub {
    font-size: 7.5pt; font-weight: normal; text-align: center;
    background: #e4e4ff; padding: 2px 0;
    font-family: Arial, Helvetica, sans-serif;
}
.att-tbl td {
    font-size: 8pt; text-align: center; padding: 1px;
    font-family: Arial, Helvetica, sans-serif;
}
.att-tbl td.dc {
    font-size: 8pt; font-weight: normal; text-align: left; padding-left: 3px;
    background: #f5f5ff;
    font-family: Arial, Helvetica, sans-serif;
}
.att-tbl tr.wknd td    { background: #f0f0f0; color: #aaaaaa; }
.att-tbl tr.wknd td.dc { background: #e8e8ee; color: #999999; }
.att-tbl tr.absent td  { background: #fff5f5; }

/* ── Signature ── */
.sig-tbl { margin-top: 7px; border: none; }
.sig-tbl td {
    border: none; vertical-align: top; padding: 2px 3px;
    font-family: 'SimSun', '宋体', 'Songti SC', 'Noto Serif CJK SC', serif;
}
.sig-line  { display: block; border-bottom: 1px solid #00008B; margin-top: 26px; margin-bottom: 2px; }
.sig-name  { text-align: center; font-weight: bold; font-size: 8.5pt; }
.sig-label { text-align: center; font-size: 7pt; }

/* ── Legend ── */
.legend {
    margin-top: 5px; border: 1px solid #bbbbdd; padding: 3px 5px; background: #f9f9ff;
    font-family: 'SimSun', '宋体', 'Songti SC', 'Noto Serif CJK SC', serif;
}
.legend-title { font-size: 6.5pt; font-weight: bold; text-transform: uppercase; margin-bottom: 2px; }
.legend table { border: none; }
.legend td    { border: none; font-size: 6.5pt; padding: 0 3px 0 0; line-height: 1.5; color: #333; }
.legend td.t  { font-weight: bold; white-space: nowrap; color: #00008B; width: 10%; }
.legend td.s  { width: 1%; color: #888; }
.legend td.d  { width: 39%; }
</style>
</head>
<body>

@php
    use Carbon\Carbon;

    $allRows     = collect($records);
    $firstRecord = $allRows->first();

    $monthStart  = $firstRecord
        ? Carbon::parse($firstRecord['Date'])->startOfMonth()
        : now()->startOfMonth();
    $daysInMonth = (int) $monthStart->daysInMonth;

    $periodFrom = $monthStart->format('Y/m/d');
    $periodTo   = $monthStart->copy()->endOfMonth()->format('m/d');
    $periodFull = $periodFrom . ' ~ ' . $periodTo;

    $empName = $employee->name        ?? ($firstRecord['Name']       ?? '—');
    $empId   = $employee->employee_id ?? ($firstRecord['EmployeeID'] ?? '—');
    $empRole = strtolower($employee->role ?? '') === 'job_order' ? 'JOB ORDER' : 'REGULAR';

    $byDay = [];
    foreach ($records as $row) {
        $byDay[(int) Carbon::parse($row['Date'])->format('j')] = $row;
    }

    $ioVal = $lateCount = $lateMins = $earlyCount = $earlyMins = 0;

    for ($d = 1; $d <= $daysInMonth; $d++) {
        $row = $byDay[$d] ?? null;
        if (!$row) continue;
        $mi = trim($row['MorningIn']   ?? '');
        $mo = trim($row['MorningOut']  ?? '');
        $ai = trim($row['AfternoonIn'] ?? '');
        if ($mi !== '' && ($mo !== '' || $ai !== '')) { $ioVal++; }
        $lm = (int)($row['LateMinutes']      ?? 0);
        $em = (int)($row['UndertimeMinutes'] ?? 0);
        if ($lm > 0) { $lateCount++;  $lateMins  += $lm; }
        if ($em > 0) { $earlyCount++; $earlyMins += $em; }
    }

    $abVal = $daysInMonth - $ioVal;
@endphp

{{-- ══ TITLE ══ --}}
<div class="page-title">DAILY TIME RECORD</div>
<div class="page-subtitle">Civil Service Form No. 48</div>

{{-- ══ HEADER
     Row 1: [Department] [    REGULAR    ] [Name] [Employee Name]
     Row 2: [Date      ] [2026/02/01~02/28] [No  ] [Employee ID  ]
     NO rowspan — each row has its own left label cell.
══ --}}
<table class="meta-tbl">
    <colgroup>
        <col style="width:11%">
        <col style="width:40%">
        <col style="width:8%">
        <col style="width:41%">
    </colgroup>
    {{-- Row 1 --}}
    <tr>
        <td class="lbl-left">Department</td>
        <td class="role-cell">{{ $empRole }}</td>
        <td class="lbl-mid">Name</td>
        <td class="name-val">{{ strtoupper($empName) }}</td>
    </tr>
    {{-- Row 2 --}}
    <tr>
        <td class="lbl-left">
            <span style="font-size:7pt;">Date</span>
        </td>
        <td class="date-val">{{ $periodFull }}</td>
        <td class="lbl-mid">No</td>
        <td class="id-val">{{ $empId }}</td>
    </tr>
</table>

{{-- ══ SUMMARY TABLE ══ --}}
<table class="sum-tbl">
    <colgroup>
        <col style="width:7%">
        <col style="width:6%">
        <col style="width:7%">
        <col style="width:8%">
        <col style="width:8.5%">
        <col style="width:7%">
        <col style="width:9.5%">
        <col style="width:9.5%">
        <col style="width:9.5%">
        <col style="width:9.5%">
    </colgroup>
    <thead>
        <tr>
            <th rowspan="2">AB</th>
            <th rowspan="2">L</th>
            <th rowspan="2">BT</th>
            <th rowspan="2" style="font-size:7pt; line-height:1.3;">I/O<br>(dd)</th>
            <th colspan="2">Over(hh)</th>
            <th colspan="2">Late</th>
            <th colspan="2">Early Leave</th>
        </tr>
        <tr>
            <th class="sub">Over</th>
            <th class="sub">Sp</th>
            <th class="sub">(ts)</th>
            <th class="sub">(min)</th>
            <th class="sub">(ts)</th>
            <th class="sub">(min)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $abVal      ?: '' }}</td>
            <td></td>
            <td></td>
            <td>{{ $ioVal      ?: '' }}</td>
            <td></td>
            <td></td>
            <td>{{ $lateCount  ?: '' }}</td>
            <td>{{ $lateMins   ?: '' }}</td>
            <td>{{ $earlyCount ?: '' }}</td>
            <td>{{ $earlyMins  ?: '' }}</td>
        </tr>
    </tbody>
</table>

{{-- ══ SCHEDULE ══ --}}
<table class="sched-tbl">
    <tr>
        <td>1.&nbsp;&nbsp; 08:00-12:00,&nbsp;&nbsp; 13:00-17:00</td>
    </tr>
</table>

{{-- ══ ATTENDANCE TABLE ══ --}}
<table class="att-tbl">
    <colgroup>
        <col style="width:9%">
        <col style="width:13.5%">
        <col style="width:13.5%">
        <col style="width:13.5%">
        <col style="width:13.5%">
        <col style="width:18.5%">
        <col style="width:18.5%">
    </colgroup>
    <thead>
        <tr><th colspan="7" class="ttl">Attendance Table</th></tr>
        <tr>
            <th rowspan="2" class="ddww">dd/ww</th>
            <th colspan="2" class="grp">AM</th>
            <th colspan="2" class="grp">PM</th>
            <th colspan="2" class="grp">Over</th>
        </tr>
        <tr>
            <th class="sub">In</th><th class="sub">Out</th>
            <th class="sub">In</th><th class="sub">Out</th>
            <th class="sub">In</th><th class="sub">Out</th>
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
                $hasPunch  = ($mi !== '' || $mo !== '' || $ai !== '' || $ao !== '');
                $isAbsent  = !$isWeekend && !$hasPunch;
                $trClass   = $isWeekend ? 'wknd' : ($isAbsent ? 'absent' : '');
            @endphp
            <tr class="{{ $trClass }}">
                <td class="dc">{{ $ddww }}</td>
                @if ($isWeekend)
                    <td></td><td></td><td></td><td></td><td></td><td></td>
                @elseif ($isAbsent)
                    <td colspan="6" style="text-align:center; font-style:italic; font-size:7.5pt; color:#555588;">Absence</td>
                @else
                    <td>{{ $mi }}</td><td>{{ $mo }}</td>
                    <td>{{ $ai }}</td><td>{{ $ao }}</td>
                    <td></td><td></td>
                @endif
            </tr>
        @endfor
    </tbody>
</table>

{{-- ══ SIGNATURE ══ --}}
<table class="sig-tbl">
    <tr>
        <td style="width:57%; font-size:7.5pt; line-height:1.7;">
            I certify on my honor that the above is a true and correct report of the hours of
            work performed, record of which was made daily at the time of arrival and departure
            from office.
        </td>
        <td style="width:43%; text-align:center;">
            <span class="sig-line"></span>
            <div class="sig-name">{{ strtoupper($empName) }}</div>
            <div class="sig-label">Signature over Printed Name</div>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="padding-top:5px; font-size:7.5pt;">
            Verified as to the prescribed office hours:
        </td>
    </tr>
    <tr>
        <td colspan="2" style="padding-top:3px;">
            <span style="display:inline-block; border-bottom:1px solid #00008B; width:220px; margin-bottom:2px;"></span>
            <div style="font-size:7pt; padding-left:3px;">In Charge of Attendance</div>
        </td>
    </tr>
</table>

{{-- ══ LEGEND ══ --}}
<div class="legend">
    <div class="legend-title">&#9432; Legends / Acronyms</div>
    <table>
        <tr>
            <td class="t">AB</td><td class="s">–</td><td class="d">Absent days (daysInMonth − I/O)</td>
            <td class="t">L</td><td class="s">–</td><td class="d">Leave days (officially filed)</td>
        </tr>
        <tr>
            <td class="t">BT</td><td class="s">–</td><td class="d">Blank per CSF-48 format</td>
            <td class="t">I/O (dd)</td><td class="s">–</td><td class="d">Days with valid biometric punch</td>
        </tr>
        <tr>
            <td class="t">Over(hh)</td><td class="s">–</td><td class="d">Official overtime hours (per OT order)</td>
            <td class="t">Sp</td><td class="s">–</td><td class="d">Special day / holiday overtime</td>
        </tr>
        <tr>
            <td class="t">Late (ts)</td><td class="s">–</td><td class="d">No. of late arrivals</td>
            <td class="t">(min)</td><td class="s">–</td><td class="d">Total late minutes</td>
        </tr>
        <tr>
            <td class="t">Early Leave</td><td class="s">–</td><td class="d">No. of early departures</td>
            <td class="t">(min)</td><td class="s">–</td><td class="d">Total undertime minutes</td>
        </tr>
        <tr>
            <td class="t">dd/ww</td><td class="s">–</td><td class="d">Day / weekday abbreviation</td>
            <td class="t">AM / PM</td><td class="s">–</td><td class="d">08:00-12:00 / 13:00-17:00</td>
        </tr>
    </table>
</div>

</body>
</html>
