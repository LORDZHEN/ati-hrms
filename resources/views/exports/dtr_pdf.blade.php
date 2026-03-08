<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>DTR - {{ $employee->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 20mm 14mm 20mm;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            font-size: 8.5px;
            color: #000;
            line-height: 1.3;
        }

        /* ── CS FORM label above border ── */
        .cs-form-label {
            font-size: 7.5px;
            text-align: right;
            margin-bottom: 2px;
            padding-right: 2px;
        }

        /* ── OUTER BORDER wrapping everything ── */
        .page-border {
            border: 1.5px solid #000;
            padding: 6px 10px 8px 10px;
        }

        /* ── AGENCY HEADER ── */
        .agency-header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 4px;
        }
        .agency-republic { font-size: 7.5px; margin-bottom: 1px; }
        .agency-name     { font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 1px; }
        .agency-address  { font-size: 7px; color: #222; margin-top: 1px; }

        /* ── FORM TITLE ── */
        .form-title-wrap { text-align: center; margin: 4px 0 5px; }
        .form-title      { font-size: 13px; font-weight: bold; letter-spacing: 4px; text-transform: uppercase; }
        .form-divider    { font-size: 8.5px; letter-spacing: 2px; margin-top: 3px; }

        /* ── DTR TABLE ── */
        .dtr-wrap { margin: 0 2px; }

        table.dtr {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        table.dtr th,
        table.dtr td {
            border: 0.75px solid #000;
            text-align: center;
            vertical-align: middle;
            padding: 1px 1px;
        }

        table.dtr thead tr.header-top th {
            font-weight: bold;
            font-size: 7.5px;
            background: #fff;
            padding: 2px 1px;
            line-height: 1.3;
        }

        table.dtr thead tr.header-sub th {
            font-weight: bold;
            font-size: 7px;
            background: #fff;
            padding: 1.5px 1px;
        }

        .col-day  { width: 30px; }
        .col-time { width: 48px; }
        .col-ut   { width: 34px; }

        table.dtr tbody td { height: 12.5px; font-size: 8px; }
        table.dtr tbody tr.weekend td { background-color: #ebebeb; }
        table.dtr tfoot td {
            font-weight: bold; font-size: 8px;
            background-color: #d8d8d8; padding: 2.5px 2px;
        }

        /* ── CERTIFICATION & VERIFICATION ── */
        .cert-section {
            display: table;
            width: 100%;
            margin: 4px 2px 0;
        }
        .cert-left {
            display: table-cell; width: 55%;
            vertical-align: top; padding-right: 12px;
        }
        .cert-right {
            display: table-cell; width: 45%;
            vertical-align: top;
            border-left: 0.75px solid #000;
            padding-left: 12px;
        }
        .cert-heading {
            font-size: 7.5px; font-weight: bold;
            text-transform: uppercase; margin-bottom: 4px;
        }
        .cert-body {
            font-size: 7.5px; text-align: justify;
            line-height: 1.6; margin-bottom: 22px;
        }
        .sig-block  { text-align: center; }
        .sig-line {
            border-top: 0.75px solid #000; padding-top: 2px;
            font-weight: bold; font-size: 8.5px;
            text-transform: uppercase; text-decoration: underline;
            display: inline-block; min-width: 160px;
        }
        .sig-sub { font-size: 7px; font-style: italic; color: #333; margin-top: 2px; }

        /* ── LEGEND ── */
        .legend-wrap {
            margin: 6px 2px 0;
            border-top: 0.75px solid #000;
            padding-top: 4px;
        }
        .legend-title { font-size: 7.5px; font-weight: bold; margin-bottom: 2px; }
        .legend-items { display: table; width: 100%; }
        .legend-col   { display: table-cell; font-size: 7px; vertical-align: top; }

        /* ── PAGE FOOTER ── */
        .page-footer {
            text-align: center; font-size: 6px; color: #777;
            margin-top: 5px; padding-top: 3px; border-top: 0.5px solid #ccc;
        }
    </style>
</head>
<body>

@php
    $totalUndertime = 0;
    $byDay = [];

    foreach ($records as $r) {
        $day = (int) \Carbon\Carbon::parse($r['Date'])->format('j');
        $byDay[$day] = $r;
    }

    $firstDate   = \Carbon\Carbon::parse($records[0]['Date'] ?? now());
    $monthStart  = $firstDate->copy()->startOfMonth();
    $daysInMonth = (int) $monthStart->daysInMonth;
    $monthLabel  = $firstDate->format('F Y');

    foreach ($records as $r) {
        $d = \Carbon\Carbon::parse($r['Date']);
        if ($d->isWeekend()) continue;
        $totalUndertime += (int)($r['Undertime'] ?? 0);
    }

    $utTotalH = $totalUndertime > 0 ? (int) floor($totalUndertime / 60) : '';
    $utTotalM = $totalUndertime > 0 ? ($totalUndertime % 60)            : '';
@endphp

<div class="cs-form-label">CS Form No. 48</div>

<div class="page-border">

    {{-- AGENCY HEADER --}}
    <div class="agency-header">
        <div class="agency-republic">Republic of the Philippines</div>
        <div class="agency-name">Agricultural Training Institute</div>
        <div class="agency-address">Datu Abdul Dadia, Panabo City, Davao Del Norte, Philippines 8105</div>
        <div class="agency-address">Email: atixI.davao@gmail.com &nbsp;|&nbsp; Tel: (084) 823-0557 &nbsp;|&nbsp; www.ati.da.gov.ph</div>
    </div>

    {{-- FORM TITLE --}}
    <div class="form-title-wrap">
        <div class="form-title">Daily Time Record</div>
        <div class="form-divider">- - - - - - o 0 o - - - - - -</div>
    </div>

    {{-- EMPLOYEE INFO --}}
    <div style="margin: 0 2px 6px;">
        <div style="display:table; width:100%; margin-bottom:1px;">
            <div style="display:table-cell; width:5px;"></div>
            <div style="display:table-cell; width:44%; border-bottom:0.75px solid #000; font-weight:bold; font-size:9px; padding-bottom:1px; padding-left:4px;">{{ strtoupper($employee->name) }}</div>
            <div style="display:table-cell; width:12px;"></div>
            <div style="display:table-cell; font-size:7.5px; vertical-align:bottom; white-space:nowrap; padding-bottom:1px; padding-right:4px;">For the month of</div>
            <div style="display:table-cell; border-bottom:0.75px solid #000; font-weight:bold; font-size:9px; padding-bottom:1px; padding-left:4px;">{{ $monthLabel }}</div>
        </div>
        <div style="display:table; width:100%; margin-bottom:5px;">
            <div style="display:table-cell; width:5px;"></div>
            <div style="display:table-cell; font-size:6.5px; font-style:italic; color:#444; padding-left:4px;">(Name)</div>
        </div>
        <div style="display:table; width:100%;">
            <div style="display:table-cell; width:5px;"></div>
            <div style="display:table-cell; width:110px; font-size:7.5px; vertical-align:bottom; padding-bottom:1px;">Official hours for arrival<br>and departure</div>
            <div style="display:table-cell; width:36%; border-bottom:0.75px solid #000; font-weight:bold; font-size:8px; padding-bottom:1px; padding-left:4px;">8:00 A.M. – 12:00 N.N. &nbsp;/&nbsp; 1:00 P.M. – 5:00 P.M.</div>
            <div style="display:table-cell; font-size:7.5px; vertical-align:bottom; padding-bottom:1px; padding-left:10px; white-space:nowrap; padding-right:4px;">Regular days</div>
            <div style="display:table-cell; border-bottom:0.75px solid #000; font-weight:bold; font-size:8px; padding-bottom:1px; padding-left:4px;">Monday – Friday</div>
        </div>
    </div>

    {{-- DTR TABLE --}}
    <div class="dtr-wrap">
        <table class="dtr">
            <thead>
                <tr class="header-top">
                    <th rowspan="2" class="col-day">Day</th>
                    <th colspan="2">A.M.</th>
                    <th colspan="2">P.M.</th>
                    <th colspan="2">Undertime</th>
                </tr>
                <tr class="header-sub">
                    <th class="col-time">Arrival</th>
                    <th class="col-time">Departure</th>
                    <th class="col-time">Arrival</th>
                    <th class="col-time">Departure</th>
                    <th class="col-ut">Hours</th>
                    <th class="col-ut">Minutes</th>
                </tr>
            </thead>
            <tbody>
                @for ($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $date   = $monthStart->copy()->addDays($day - 1);
                        $isWeek = $date->isWeekend();
                        $r      = $byDay[$day] ?? null;
                        $ut     = (!$isWeek && $r) ? (int)($r['Undertime'] ?? 0) : 0;
                        $utH    = $ut > 0 ? (int) floor($ut / 60) : '';
                        $utM    = $ut > 0 ? ($ut % 60)            : '';
                    @endphp
                    <tr class="{{ $isWeek ? 'weekend' : '' }}">
                        <td class="col-day" style="font-size:7.5px; line-height:1.2;">
                            {{ $day }}<br>
                            <span style="font-size:6px; font-weight:normal;">{{ $date->format('D') }}</span>
                        </td>
                        <td class="col-time">{{ (!$isWeek && $r) ? ($r['MorningIn']    ?? '') : '' }}</td>
                        <td class="col-time">{{ (!$isWeek && $r) ? ($r['MorningOut']   ?? '') : '' }}</td>
                        <td class="col-time">{{ (!$isWeek && $r) ? ($r['AfternoonIn']  ?? '') : '' }}</td>
                        <td class="col-time">{{ (!$isWeek && $r) ? ($r['AfternoonOut'] ?? '') : '' }}</td>
                        <td class="col-ut">{{ $utH }}</td>
                        <td class="col-ut">{{ $utM }}</td>
                    </tr>
                @endfor
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align:right; padding-right:8px; font-size:7.5px; font-style:italic;">TOTAL for the month</td>
                    <td class="col-ut">{{ $utTotalH }}</td>
                    <td class="col-ut">{{ $utTotalM }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- CERTIFICATION & VERIFICATION --}}
    <div class="cert-section">
        <div class="cert-left">
            <div class="cert-heading">Certification of Employee:</div>
            <div class="cert-body">
                I certify on my honor that the above is a true and correct report of the hours of work
                performed, record of which was made daily at the time of arrival at and departure from office.
            </div>
            <div class="sig-block">
                <span class="sig-line">{{ $employee->name }}</span>
                <div class="sig-sub">(Signature over printed name of employee)</div>
            </div>
        </div>
        <div class="cert-right">
            <div class="cert-heading">Verified as to the prescribed office hours:</div>
            <div style="height:22px;"></div>
            <div class="sig-block">
                <span class="sig-line">{{ strtoupper($employee->immediate_supervisor ?? '') }}</span>
                <div class="sig-sub">In Charge</div>
            </div>
        </div>
    </div>

    {{-- LEGEND --}}
    <div class="legend-wrap">
        <div class="legend-title">Legends / Abbreviations:</div>
        <div class="legend-items">
            <div class="legend-col" style="width:30%;">
                <strong>WD</strong> – Working Day<br>
                <strong>SH</strong> – Special (Non-Working) Holiday
            </div>
            <div class="legend-col" style="width:30%;">
                <strong>RH</strong> – Regular Holiday<br>
                <strong>RL</strong> – Rest Day with Pay
            </div>
            <div class="legend-col" style="width:40%;">
                <strong>VL</strong> – Vacation Leave &nbsp;
                <strong>SL</strong> – Sick Leave<br>
                <strong>UL</strong> – Undertime Leave &nbsp;
                <strong>CTO</strong> – Compensatory Time Off
            </div>
        </div>
    </div>

</div>

{{-- PAGE FOOTER --}}
<div class="page-footer">
    Civil Service Form No. 48 &nbsp;|&nbsp; ATI – Panabo City, Davao Del Norte &nbsp;|&nbsp;
    Generated: {{ now()->format('M d, Y h:i A') }}
</div>

</body>
</html>
