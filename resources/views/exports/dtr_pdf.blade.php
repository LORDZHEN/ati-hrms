<!DOCTYPE html>
<html>
<head>
    <title>DTR - {{ $employee->name }}</title>
    <style>
        @page {
            size: A4;
            margin: 8mm 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 0;
            color: #000;
            line-height: 1.3;
        }

        .page-container {
            min-height: 281mm; /* A4 height minus margins - fill entire page */
            display: flex;
            flex-direction: column;
        }

        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .table-wrapper {
            flex: 1;
        }

        .header {
            text-align: center;
            margin-bottom: 8px;
        }

        .header h4 {
            font-size: 11px;
            margin: 1.5px 0;
            font-weight: bold;
        }

        .header h5 {
            font-size: 9px;
            margin: 1.5px 0;
            font-weight: bold;
        }

        .header p {
            font-size: 7.5px;
            margin: 0.5px 0;
            font-style: italic;
        }

        .info-section {
            margin: 8px 0;
        }

        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            padding: 3px 6px;
            font-size: 8.5px;
        }

        .info-label {
            font-weight: bold;
            width: 95px;
        }

        .info-value {
            border-bottom: 1px solid #333;
        }

        table.dtr-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 12px;
            font-size: 9px;
        }

        table.dtr-table th,
        table.dtr-table td {
            border: 0.5px solid #000;
            padding: 5px 3px;
            text-align: center;
            vertical-align: middle;
        }

        table.dtr-table th {
            background-color: #e8e8e8;
            font-weight: bold;
            font-size: 9px;
            padding: 6px 3px;
        }

        table.dtr-table .date-col {
            text-align: left;
            padding-left: 6px;
            font-size: 8.5px;
        }

        .summary-footer {
            display: table;
            width: 100%;
            margin-top: 12px;
            margin-bottom: 12px;
            font-size: 8px;
        }

        .summary-left {
            display: table-cell;
            width: 55%;
            vertical-align: top;
            padding-right: 12px;
        }

        .summary-right {
            display: table-cell;
            width: 45%;
            vertical-align: top;
            border-left: 1px solid #ccc;
            padding-left: 12px;
        }

        .summary-row {
            margin: 3px 0;
            line-height: 1.5;
        }

        .legend {
            font-size: 7.5px;
            margin-top: 4px;
            padding: 6px 8px;
            background-color: #f5f5f5;
            border-left: 2px solid #333;
            line-height: 1.6;
        }

        .legend-title {
            font-weight: bold;
            font-size: 8px;
            margin-bottom: 3px;
        }

        .legend-item {
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 2px;
        }

        .certification {
            font-size: 7.5px;
            text-align: justify;
            margin: 8px 0;
            line-height: 1.5;
            padding: 4px 0;
        }

        .signatures {
            display: table;
            width: 100%;
            margin-top: 15px;
            margin-bottom: 10px;
            font-size: 8px;
        }

        .sig-cell {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 5px;
        }

        .sig-line {
            border-top: 1px solid #000;
            width: 85%;
            margin: 30px auto 4px;
            padding-top: 4px;
            font-weight: bold;
            font-size: 8.5px;
        }

        .sig-label {
            font-size: 7px;
            font-style: italic;
            color: #555;
            margin-top: 3px;
            line-height: 1.3;
        }

        .footer-note {
            text-align: center;
            font-size: 7px;
            color: #666;
            margin-top: 12px;
            padding-top: 6px;
            border-top: 0.5px solid #ccc;
            line-height: 1.4;
        }

        /* Compact table styling */
        .compact-td {
            font-size: 8.5px;
        }

        /* Weekend highlighting */
        .weekend-row {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="page-container">
    <div class="content-wrapper">
    <!-- Compact Header -->
    <div class="header">
        <h5 style="text-transform: uppercase;">Republic of the Philippines</h5>
        <h5>Department of Agriculture</h5>
        <h4>AGRICULTURAL TRAINING INSTITUTE - REGION XI</h4>
        <p>Km. 7, Bangkal, Davao City</p>
        <h4 style="margin-top: 4px;">DAILY TIME RECORD</h4>
        <p>(Civil Service Commission Form No. 48)</p>
    </div>

    <!-- Employee Info - Compact Grid -->
    <div class="info-section">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell info-label">Name:</div>
                <div class="info-cell info-value" style="width: 35%;">{{ strtoupper($employee->name) }}</div>
                <div class="info-cell info-label" style="padding-left: 20px;">For the month of:</div>
                <div class="info-cell info-value" style="width: 20%;">{{ \Carbon\Carbon::parse($records[0]['Date'] ?? now())->format('F Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Position:</div>
                <div class="info-cell info-value" style="width: 35%;">{{ $employee->position ?? 'N/A' }}</div>
                <div class="info-cell info-label" style="padding-left: 20px;">Official Hours:</div>
                <div class="info-cell info-value" style="width: 20%;">8:00 AM - 12:00 NN / 1:00 PM - 5:00 PM</div>
            </div>
        </div>
    </div>

    <!-- DTR Table - Full Page Coverage -->
    <div class="table-wrapper">
    <table class="dtr-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 70px;">Date</th>
                <th colspan="2">A.M.</th>
                <th colspan="2">P.M.</th>
                <th colspan="2">Undertime</th>
                <th rowspan="2" style="width: 50px;">Hours<br>Worked</th>
            </tr>
            <tr>
                <th style="width: 42px;">In</th>
                <th style="width: 42px;">Out</th>
                <th style="width: 42px;">In</th>
                <th style="width: 42px;">Out</th>
                <th style="width: 36px;">Late</th>
                <th style="width: 36px;">U.T.</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalLate = 0;
                $totalUndertime = 0;
                $totalWorkedMinutes = 0;
                $daysPresent = 0;
            @endphp

            @foreach($records as $r)
                @php
                    $date = \Carbon\Carbon::parse($r['Date']);
                    $isWeekend = $date->isWeekend();

                    // Count working days
                    if (!$isWeekend) {
                        $daysPresent++;
                    }

                    // Parse worked hours
                    if (isset($r['WorkedHours']) && $r['WorkedHours'] !== '0:00') {
                        list($hours, $minutes) = explode(':', $r['WorkedHours']);
                        $totalWorkedMinutes += ($hours * 60) + $minutes;
                    }

                    $totalLate += $r['Late'] ?? 0;
                    $totalUndertime += $r['Undertime'] ?? 0;
                @endphp

                <tr class="{{ $isWeekend ? 'weekend-row' : '' }}">
                    <td class="date-col compact-td">{{ $date->format('M d (D)') }}</td>
                    <td class="compact-td">{{ $r['MorningIn'] ?? '' }}</td>
                    <td class="compact-td">{{ $r['MorningOut'] ?? '' }}</td>
                    <td class="compact-td">{{ $r['AfternoonIn'] ?? '' }}</td>
                    <td class="compact-td">{{ $r['AfternoonOut'] ?? '' }}</td>
                    <td class="compact-td">{{ ($r['Late'] ?? 0) > 0 ? $r['Late'] : '' }}</td>
                    <td class="compact-td">{{ ($r['Undertime'] ?? 0) > 0 ? $r['Undertime'] : '' }}</td>
                    <td class="compact-td">{{ $r['WorkedHours'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #d8d8d8;">
                <td colspan="5" style="text-align: right; padding-right: 8px; font-size: 9px;">TOTAL:</td>
                <td style="font-size: 9px;">{{ $totalLate }}</td>
                <td style="font-size: 9px;">{{ $totalUndertime }}</td>
                <td style="font-size: 9px;">
                    @php
                        $totalHours = floor($totalWorkedMinutes / 60);
                        $totalMins = $totalWorkedMinutes % 60;
                        echo $totalHours . ':' . str_pad($totalMins, 2, '0', STR_PAD_LEFT);
                    @endphp
                </td>
            </tr>
        </tfoot>
    </table>
    </div>

    <!-- Compact Summary & Certification -->
    <div class="summary-footer">
        <div class="summary-left">
            <!-- Legend -->
            <div class="legend">
                <div class="legend-title">LEGEND:</div>
                <span class="legend-item"><strong>UT</strong> - Undertime</span>
                <span class="legend-item"><strong>L</strong> - Late</span>
                <span class="legend-item"><strong>AB</strong> - Absent</span>
                <span class="legend-item"><strong>OB</strong> - Official Business</span><br>
                <span class="legend-item"><strong>SL</strong> - Sick Leave</span>
                <span class="legend-item"><strong>VL</strong> - Vacation Leave</span>
                <span class="legend-item"><strong>CTO</strong> - Compensatory Time Off</span>
                <span class="legend-item"><strong>SPL</strong> - Special Privilege Leave</span>
            </div>

            <!-- Certification -->
            <div class="certification">
                I certify on my honor that the above is a true and correct report of the hours of work performed,
                record of which was made daily at the time of arrival and departure from office.
            </div>
        </div>

        <div class="summary-right">
            <div class="summary-row"><strong>Days Present:</strong> {{ $daysPresent }} working day(s)</div>
            <div class="summary-row"><strong>Total Late:</strong> {{ $totalLate }} mins ({{ number_format($totalLate / 60, 2) }} hrs)</div>
            <div class="summary-row"><strong>Total Undertime:</strong> {{ $totalUndertime }} mins ({{ number_format($totalUndertime / 60, 2) }} hrs)</div>
            <div class="summary-row"><strong>Total Hours Worked:</strong> {{ floor($totalWorkedMinutes / 60) }} hrs {{ $totalWorkedMinutes % 60 }} mins</div>
            @php
                $expectedHours = $daysPresent * 8 * 60; // in minutes
                $deficiency = $expectedHours - $totalWorkedMinutes;
            @endphp
            <div class="summary-row" style="margin-top: 3px; padding-top: 3px; border-top: 1px solid #ccc;">
                <strong>Expected Hours:</strong> {{ floor($expectedHours / 60) }} hrs<br>
                <strong>Deficiency:</strong> {{ $deficiency > 0 ? floor($deficiency / 60) . ' hrs ' . ($deficiency % 60) . ' mins' : 'None' }}
            </div>
        </div>
    </div>

    <!-- Compact Signatures -->
    <div class="signatures">
        <div class="sig-cell">
            <div class="sig-line">{{ strtoupper($employee->name) }}</div>
            <div class="sig-label">Employee Signature</div>
        </div>
        <div class="sig-cell">
            <div class="sig-line">{{ $employee->immediate_supervisor ?? '' }}</div>
            <div class="sig-label">Verified by: Immediate Supervisor</div>
        </div>
        <div class="sig-cell">
            <div class="sig-line">{{ $employee->center_director ?? '' }}</div>
            <div class="sig-label">Noted by: Center Director</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer-note">
        ATI-XI-DTR-{{ date('Y') }} | Generated: {{ now()->format('M d, Y h:i A') }} | Official copy filed at ATI-XI Administrative Division
    </div>
    </div>
    </div>
</body>
</html>
