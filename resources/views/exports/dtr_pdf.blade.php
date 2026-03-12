{{-- resources/views/exports/dtr_pdf.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    @page {
        size: A4 portrait;
        margin: 8mm 6mm 8mm 6mm;
    }

    html, body {
        width: 100%;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 8.5pt;
        color: #00008B;
    }

    /* ── Title ──────────────────────────────────────────────────── */
    .header-title {
        text-align: center;
        font-size: 12pt;
        font-weight: bold;
        margin-bottom: 1px;
        letter-spacing: 1px;
    }
    .header-sub {
        text-align: center;
        font-size: 7.5pt;
        margin-bottom: 4px;
    }

    /* ── Employee meta ──────────────────────────────────────────── */
    .header-meta {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 3px;
        table-layout: fixed;
    }
    .header-meta td {
        padding: 2px 4px;
        border: 1px solid #00008B;
        font-size: 8.5pt;
        overflow: hidden;
    }
    .header-meta td.lbl {
        font-weight: bold;
        background: #eeeeff;
        white-space: nowrap;
        width: 13%;
    }
    .header-meta td.val { width: 37%; }

    /* ── Summary table ──────────────────────────────────────────── */
    .summary-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        margin-bottom: 2px;
    }
    .summary-table th, .summary-table td {
        border: 1px solid #00008B;
        text-align: center;
        padding: 2px 0;
        overflow: hidden;
    }
    .summary-table thead tr:first-child th {
        background: #d0d0ff;
        font-weight: bold;
        font-size: 7pt;
        padding: 2px 0;
    }
    .summary-table thead tr:last-child th {
        background: #e8e8ff;
        font-size: 6.5pt;
        padding: 1px 0;
    }
    .summary-table tbody td {
        font-size: 9pt;
        font-weight: bold;
        padding: 3px 0;
    }

    /* ── Schedule note ──────────────────────────────────────────── */
    .schedule-note {
        text-align: center;
        font-size: 7.5pt;
        margin: 2px 0;
        font-style: italic;
    }

    /* ── Attendance table ───────────────────────────────────────── */
    /*
     * 7 columns — NO overflow columns.
     * dd/ww(8%) | AM-In(14%) | AM-Out(14%) | PM-In(14%) | PM-Out(14%) | Late-min(18%) | UT/OT-min(18%)
     * Total: 8+14+14+14+14+18+18 = 100%
     */
    .attendance-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }
    .attendance-table th {
        border: 1px solid #00008B;
        text-align: center;
        padding: 2px 1px;
        font-weight: bold;
        overflow: hidden;
        font-size: 8pt;
    }
    .attendance-table th.grp { background: #d0d0ff; font-size: 8.5pt; }
    .attendance-table th.sub { background: #e8e8ff; font-size: 7.5pt; }
    .attendance-table td {
        border: 1px solid #00008B;
        text-align: center;
        padding: 2px 1px;
        font-size: 8.5pt;
        overflow: hidden;
    }
    .attendance-table tr.weekend td { background: #f0f0f0; color: #aaa; }
    .attendance-table tr.absent  td { background: #fff0f0; }
    .attendance-table tr.late    td { background: #fff8f0; }
    .attendance-table tr.ot      td { background: #f0fff0; }
    .attendance-table td.day-col { font-weight: bold; background: #f5f5ff; font-size: 8pt; }
    .attendance-table td.num     { font-size: 8pt; }
    .attendance-table td.lv      { color: #cc0000; font-weight: bold; font-size: 8pt; }
    .attendance-table td.ov      { color: #006600; font-weight: bold; font-size: 8pt; }

    /* ── Signature ──────────────────────────────────────────────── */
    .sig-section {
        margin-top: 6px;
        width: 100%;
        border-collapse: collapse;
    }
    .sig-section td {
        padding: 2px 4px;
        vertical-align: top;
        font-size: 8pt;
    }
    .sig-line {
        display: block;
        border-bottom: 1px solid #00008B;
        margin-top: 22px;
        margin-bottom: 2px;
    }
    .sig-name  { text-align: center; font-weight: bold; font-size: 8.5pt; }
    .sig-label { text-align: center; font-size: 7pt; }

    /* ── Legend ─────────────────────────────────────────────────── */
    .legend-box {
        margin-top: 6px;
        border: 1px solid #bbbbdd;
        border-radius: 2px;
        padding: 4px 6px;
        background: #f8f8ff;
    }
    .legend-title {
        font-size: 7pt;
        font-weight: bold;
        margin-bottom: 3px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .legend-grid {
        width: 100%;
        border-collapse: collapse;
    }
    .legend-grid td {
        font-size: 7pt;
        padding: 1px 4px 1px 0;
        vertical-align: top;
        border: none;
        line-height: 1.4;
    }
    .legend-grid td.term {
        font-weight: bold;
        white-space: nowrap;
        width: 10%;
        color: #00008B;
    }
    .legend-grid td.sep  { width: 1%; color: #888; }
    .legend-grid td.def  { width: 39%; color: #333; }
</style>
</head>
<body>

    <div class="header-title">DAILY TIME RECORD</div>
    <div class="header-sub">Civil Service Form No. 48</div>

    {{-- ── Employee meta ─────────────────────────────────────── --}}
    @php
        $allRows    = collect($records);
        $wdRows     = $allRows->where('IsWeekend', false);
        $firstRow   = $wdRows->first() ?? $allRows->first();
        $lastRow    = $wdRows->last()  ?? $allRows->last();
        $periodFrom = $firstRow ? \Carbon\Carbon::parse($firstRow['Date'])->format('Y/m/d') : '—';
        $periodTo   = $lastRow  ? \Carbon\Carbon::parse($lastRow['Date'])->format('Y/m/d')  : '—';
        $empName    = $employee->name         ?? ($firstRow['Name'] ?? '—');
        $empId      = $firstRow['EmployeeID'] ?? '—';
        $empPos     = $employee->position     ?? 'N/A';
    @endphp

    <table class="header-meta">
        <tr>
            <td class="lbl">Name:</td>
            <td class="val">{{ $empName }}</td>
            <td class="lbl">No.:</td>
            <td class="val">{{ $empId }}</td>
        </tr>
        <tr>
            <td class="lbl">Period:</td>
            <td class="val">{{ $periodFrom }} ~ {{ $periodTo }}</td>
            <td class="lbl">Position:</td>
            <td class="val">{{ $empPos }}</td>
        </tr>
    </table>

    {{-- ── Summary row ─────────────────────────────────────────── --}}
    @php
        $s           = $summary ?? [];
        $ab          = number_format($s['absent_days_total']        ?? 0, 1);
        $lateDays    = $s['late_days']                              ?? 0;
        $btDays      = $s['total_working_days']                     ?? 0;
        $utDays      = $s['undertime_days']                         ?? 0;
        $otHrs       = $s['overtime_hours']                         ?? 0;
        $otMinRem    = $s['overtime_minutes_remainder']             ?? 0;
        $lateHrs     = $s['late_hours']                             ?? 0;
        $lateTotMin  = $s['late_total_minutes']                     ?? 0;
        $earlyHrs    = $s['undertime_hours']                        ?? 0;
        $earlyTotMin = $s['undertime_total_minutes']                ?? 0;
    @endphp

    <table class="summary-table">
        <colgroup>
            <col style="width:8.5%">  {{-- AB --}}
            <col style="width:7%">    {{-- L --}}
            <col style="width:7%">    {{-- BT --}}
            <col style="width:8%">    {{-- U/O Over --}}
            <col style="width:7%">    {{-- U/O h --}}
            <col style="width:7%">    {{-- OT h --}}
            <col style="width:8%">    {{-- OT min --}}
            <col style="width:7%">    {{-- Sp --}}
            <col style="width:9.5%">  {{-- Late h --}}
            <col style="width:9.5%">  {{-- Late min --}}
            <col style="width:9.5%">  {{-- EL h --}}
            <col style="width:9.5%">  {{-- EL min --}}
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">AB</th>
                <th rowspan="2">L</th>
                <th rowspan="2">BT</th>
                <th colspan="2">U/O (dit)</th>
                <th colspan="2">Over(hh)</th>
                <th rowspan="2">Sp</th>
                <th colspan="2">Late</th>
                <th colspan="2">Early Leave</th>
            </tr>
            <tr>
                <th>Over</th><th>(h)</th>
                <th>(h)</th><th>(min)</th>
                <th>(h)</th><th>(min)</th>
                <th>(h)</th><th>(min)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $ab }}</td>
                <td>{{ $lateDays }}</td>
                <td>{{ $btDays }}</td>
                <td>{{ $utDays }}</td>
                <td>{{ $otHrs }}</td>
                <td>{{ $otHrs }}</td>
                <td>{{ str_pad($otMinRem, 2, '0', STR_PAD_LEFT) }}</td>
                <td>0</td>
                <td>{{ $lateHrs }}</td>
                <td>{{ $lateTotMin }}</td>
                <td>{{ $earlyHrs }}</td>
                <td>{{ $earlyTotMin }}</td>
            </tr>
        </tbody>
    </table>

    <div class="schedule-note">1. 08:00-12:00, 13:00-17:00</div>

    {{-- ── Attendance table ────────────────────────────────────── --}}
    <table class="attendance-table">
        <colgroup>
            <col style="width:8%">   {{-- dd/ww --}}
            <col style="width:14%">  {{-- AM In --}}
            <col style="width:14%">  {{-- AM Out --}}
            <col style="width:14%">  {{-- PM In --}}
            <col style="width:14%">  {{-- PM Out --}}
            <col style="width:18%">  {{-- Late (min) --}}
            <col style="width:18%">  {{-- U/T or OT (min) --}}
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2" class="sub" style="vertical-align:middle;">dd/ww</th>
                <th colspan="2" class="grp">AM</th>
                <th colspan="2" class="grp">PM</th>
                <th rowspan="2" class="sub" style="vertical-align:middle; line-height:1.3;">Late<br>(min)</th>
                <th rowspan="2" class="sub" style="vertical-align:middle; line-height:1.3; font-size:7pt;">U/T or OT<br>(min)</th>
            </tr>
            <tr>
                <th class="sub">In</th>
                <th class="sub">Out</th>
                <th class="sub">In</th>
                <th class="sub">Out</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $row)
                @php
                    $date         = \Carbon\Carbon::parse($row['Date']);
                    $dayNum       = str_pad($date->day, 2, '0', STR_PAD_LEFT);
                    $dayCode      = strtoupper(substr($date->format('D'), 0, 2));
                    $ddww         = $dayNum . ' ' . $dayCode;

                    $isWeekend    = $row['IsWeekend']         ?? false;
                    $isFullAbsent = $row['IsFullAbsent']      ?? false;
                    $lateMin      = (int)($row['LateMinutes']      ?? 0);
                    $utMin        = (int)($row['UndertimeMinutes']  ?? 0);
                    $otMin        = (int)($row['OvertimeMinutes']   ?? 0);

                    if ($isWeekend)        $rc = 'weekend';
                    elseif ($isFullAbsent) $rc = 'absent';
                    elseif ($lateMin > 0)  $rc = 'late';
                    elseif ($otMin > 0)    $rc = 'ot';
                    else                   $rc = '';

                    // U/T or OT column: OT is green (+N), undertime is red (-N)
                    if ($otMin > 0) {
                        $utotVal = '+' . $otMin;
                        $utotCss = 'ov';
                    } elseif ($utMin > 0) {
                        $utotVal = '-' . $utMin;
                        $utotCss = 'lv';
                    } else {
                        $utotVal = '';
                        $utotCss = 'num';
                    }
                @endphp
                <tr class="{{ $rc }}">
                    <td class="day-col">{{ $ddww }}</td>
                    @if ($isWeekend)
                        <td></td><td></td><td></td><td></td>
                        <td class="num"></td>
                        <td class="num"></td>
                    @else
                        <td>{{ $row['MorningIn']    ?? '' }}</td>
                        <td>{{ $row['MorningOut']   ?? '' }}</td>
                        <td>{{ $row['AfternoonIn']  ?? '' }}</td>
                        <td>{{ $row['AfternoonOut'] ?? '' }}</td>
                        <td class="{{ $lateMin > 0 ? 'lv' : 'num' }}">{{ $lateMin > 0 ? $lateMin : '' }}</td>
                        <td class="{{ $utotCss }}">{{ $utotVal }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ── Signature ────────────────────────────────────────────── --}}
    <table class="sig-section">
        <tr>
            <td style="width:58%; line-height:1.5;">
                I certify on my honor that the above is a true and correct report of the
                hours of work performed, record of which was made daily at the time of
                arrival and departure from office.
            </td>
            <td style="width:42%; text-align:center;">
                <span class="sig-line"></span>
                <div class="sig-name">{{ $empName }}</div>
                <div class="sig-label">Signature over Printed Name</div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top:5px; font-size:8pt;">
                Verified as to the prescribed office hours:
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span style="display:inline-block; border-bottom:1px solid #00008B;
                      width:200px; margin-top:20px; margin-bottom:2px;"></span>
                <div style="font-size:7.5pt;">In Charge of Attendance</div>
            </td>
        </tr>
    </table>

    {{-- ── Legend ───────────────────────────────────────────────── --}}
    <div class="legend-box">
        <div class="legend-title">&#9432; Legends / Acronyms</div>
        <table class="legend-grid">
            <tr>
                {{-- Column 1 --}}
                <td class="term">AB</td>
                <td class="sep">—</td>
                <td class="def">Absences (full day = 1, half day = 0.5)</td>
                {{-- Column 2 --}}
                <td class="term">L</td>
                <td class="sep">—</td>
                <td class="def">Number of days with late arrival</td>
            </tr>
            <tr>
                <td class="term">BT</td>
                <td class="sep">—</td>
                <td class="def">Total business/working days in period</td>
                <td class="term">U/O (dit)</td>
                <td class="sep">—</td>
                <td class="def">Undertime / Overtime days</td>
            </tr>
            <tr>
                <td class="term">Over(hh)</td>
                <td class="sep">—</td>
                <td class="def">Overtime hours &amp; minutes (after 17:00)</td>
                <td class="term">Sp</td>
                <td class="sep">—</td>
                <td class="def">Special days / holidays worked</td>
            </tr>
            <tr>
                <td class="term">Late (h/min)</td>
                <td class="sep">—</td>
                <td class="def">Total late: hours &amp; cumulative minutes</td>
                <td class="term">Early Leave</td>
                <td class="sep">—</td>
                <td class="def">Total undertime: hours &amp; cumulative minutes</td>
            </tr>
            <tr>
                <td class="term" style="color:#006600;">+N (OT)</td>
                <td class="sep">—</td>
                <td class="def">Overtime minutes earned that day</td>
                <td class="term" style="color:#cc0000;">−N (U/T)</td>
                <td class="sep">—</td>
                <td class="def">Undertime minutes (left before end of shift)</td>
            </tr>
            <tr>
                <td class="term">dd/ww</td>
                <td class="sep">—</td>
                <td class="def">Day number / weekday (MO TU WE TH FR SA SU)</td>
                <td class="term">AM / PM</td>
                <td class="sep">—</td>
                <td class="def">Morning (08:00-12:00) / Afternoon (13:00-17:00)</td>
            </tr>
        </table>
    </div>

</body>
</html>
