<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>PDS Report</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5px;
            line-height: 1.5;
            color: #1f2937;
            background: #ffffff;
        }

        .page { padding: 22px 28px 46px; }

        /* ── Running footer ──────────────────────────────────────────── */
        .page-footer {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            padding: 4px 28px;
            border-top: 1px solid #d1d5db;
        }

        .page-footer table { width: 100%; }
        .page-footer td { border: none; padding: 0; font-size: 8px; color: #6b7280; }

        /* ── Report header ───────────────────────────────────────────── */
        .report-header {
            border-bottom: 3px solid #1a3a5c;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .republic-label {
            text-align: center;
            font-size: 8px;
            letter-spacing: 0.07em;
            color: #4b5563;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .header-layout { width: 100%; }
        .header-layout td { border: none; padding: 0; vertical-align: middle; }

        .logo-cell { width: 58px; text-align: center; }
        .logo-cell img { height: 52px; width: auto; }

        .title-cell { text-align: center; padding: 0 10px; }

        .org-name {
            font-size: 15px;
            font-weight: bold;
            color: #1a3a5c;
            letter-spacing: 0.03em;
            line-height: 1.2;
        }

        .org-branch {
            font-size: 10px;
            font-weight: bold;
            color: #2c5f8a;
            letter-spacing: 0.04em;
            margin-top: 1px;
        }

        .header-contact {
            text-align: center;
            font-size: 8.5px;
            color: #4b5563;
            margin-top: 5px;
            line-height: 1.5;
        }

        /* ── Report title ────────────────────────────────────────────── */
        .title-block { text-align: center; margin: 10px 0 9px; }

        .title-block h1 {
            font-size: 14px;
            font-weight: bold;
            color: #1a3a5c;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .title-hr {
            width: 160px;
            border: none;
            border-top: 1.5px solid #b8860b;
            margin: 4px auto 0;
        }

        /* ── Meta bar ────────────────────────────────────────────────── */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border: 1px solid #d1d5db;
        }

        .meta-table td {
            background: #f8f9fb;
            padding: 5px 8px;
            text-align: center;
            border-right: 1px solid #d1d5db;
            vertical-align: middle;
        }

        .meta-table td:last-child { border-right: none; }

        .ml { display: block; font-size: 7px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.08em; color: #6b7280; margin-bottom: 1px; }
        .mv { font-size: 9px; font-weight: bold; color: #1a3a5c; }

        /* ── Summary block ───────────────────────────────────────────── */
        .summary-block {
            background: #f8f9fb;
            border-left: 4px solid #1a3a5c;
            padding: 8px 11px;
            margin-bottom: 10px;
            font-size: 9.5px;
            line-height: 1.7;
        }

        .summary-block strong { color: #1a3a5c; }

        /* ── Stats row ───────────────────────────────────────────────── */
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .stats-table td {
            border: 1px solid #d1d5db;
            padding: 8px 6px;
            text-align: center;
            background: #ffffff;
            vertical-align: middle;
        }

        .stats-table td.gap { border: none; width: 7px; background: transparent; }

        .s-num             { font-size: 20px; font-weight: bold; color: #1a3a5c; line-height: 1; }
        .s-num.submitted   { color: #7a4f00; }
        .s-num.approved    { color: #1a6b3a; }
        .s-num.disapproved { color: #8b1a1a; }

        .s-lbl { font-size: 7.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-top: 3px; }

        /* ── Section header ──────────────────────────────────────────── */
        .sec-hdr { width: 100%; border-collapse: collapse; border-bottom: 2px solid #1a3a5c; margin-bottom: 6px; }
        .sec-hdr td { border: none; padding: 0 0 3px; vertical-align: bottom; }
        .sec-title { font-size: 11px; font-weight: bold; color: #1a3a5c; text-transform: uppercase; letter-spacing: 0.04em; }
        .sec-count { font-size: 8.5px; font-weight: bold; color: #6b7280; text-align: right; }

        /* ── PDS table ───────────────────────────────────────────────── */
        .pds-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-bottom: 20px;
        }

        .pds-table thead tr { background-color: #1a3a5c; }

        .pds-table thead th {
            color: #ffffff;
            padding: 5px 6px;
            text-align: left;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }

        .pds-table tbody tr { border-bottom: 1px solid #e5e7eb; }
        .pds-table tbody tr.even { background-color: #f8f9fb; }
        .pds-table tbody tr.odd  { background-color: #ffffff; }
        .pds-table tbody tr:last-child { border-bottom: 2px solid #1a3a5c; }

        .pds-table td { padding: 5px 6px; vertical-align: middle; }

        .row-num  { color: #9ca3af; font-size: 7.5px; }
        .emp-name { font-weight: bold; color: #1f2937; }

        /* ── Completion bar ──────────────────────────────────────────── */
        .completion-wrap { display: inline-block; width: 60px; background: #e5e7eb; height: 6px; border-radius: 3px; vertical-align: middle; margin-right: 3px; }
        .completion-fill { height: 6px; border-radius: 3px; }

        /* ── Badges ──────────────────────────────────────────────────── */
        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 8px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            white-space: nowrap;
        }

        .b-submitted   { background: #fff3cd; color: #7a4f00;  border: 1px solid #e8b84b; }
        .b-approved    { background: #e8f5ee; color: #1a6b3a;  border: 1px solid #9dd0b2; }
        .b-disapproved { background: #fde8e8; color: #8b1a1a;  border: 1px solid #f5b8b8; }

        /* ── No data ─────────────────────────────────────────────────── */
        .no-data {
            text-align: center;
            padding: 24px 20px;
            color: #6b7280;
            font-size: 10px;
            border: 1px dashed #d1d5db;
            margin-bottom: 20px;
        }

        /* ── Signatures ──────────────────────────────────────────────── */
        .sig-section { margin-top: 24px; border-top: 1px solid #d1d5db; padding-top: 16px; page-break-inside: avoid; }
        .sig-table { width: 100%; }
        .sig-table td { width: 33.33%; text-align: center; padding: 0 8px; vertical-align: top; border: none; }

        .sig-label { display: block; font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.07em; color: #6b7280; margin-bottom: 30px; }
        .sig-line  { border-top: 1.5px solid #374151; padding-top: 4px; margin: 0 6px; }
        .sig-name  { font-size: 10px; font-weight: bold; color: #1a3a5c; }
        .sig-title { font-size: 8.5px; color: #4b5563; margin-top: 2px; }
    </style>
</head>
<body>

{{-- Running footer --}}
<div class="page-footer">
    <table>
        <tr>
            <td>ATI-RTC XI &bull; Human Resource Management System</td>
            <td style="text-align:center;">Generated on {{ now()->format('F d, Y \a\t h:i A') }}</td>
            <td style="text-align:right;">Confidential &bull; For Official Use Only</td>
        </tr>
    </table>
</div>

<div class="page">

    {{-- HEADER --}}
    <div class="report-header">
        <div class="republic-label">Republic of the Philippines &bull; Department of Agriculture</div>

        <table class="header-layout">
            <tr>
                <td class="title-cell">
                    <div class="org-name">Agricultural Training Institute</div>
                    <div class="org-branch">Regional Training Center XI</div>
                </td>
            </tr>
        </table>

        <div class="header-contact">
            Brgy. Data Abdul Dadia, Panabo City, Davao del Norte 8105
            &nbsp;&bull;&nbsp; (084) 217-3345
            &nbsp;&bull;&nbsp; ati11.addp4@gmail.com
            &nbsp;&bull;&nbsp; ati.da.gov.ph/region11
        </div>
    </div>

    {{-- TITLE --}}
    <div class="title-block">
        <h1>Personal Data Sheet Report</h1>
        <hr class="title-hr">
    </div>

    {{-- META BAR --}}
    @if($from && $to)
    <table class="meta-table">
        <tr>
            <td>
                <span class="ml">Status Filter</span>
                <span class="mv">{{ ($status === 'all' || !$status) ? 'All Records' : ucfirst($status) }}</span>
            </td>
            <td>
                <span class="ml">Period</span>
                <span class="mv">{{ ucfirst($period ?? 'Custom') }}</span>
            </td>
            <td>
                <span class="ml">From</span>
                <span class="mv">{{ \Carbon\Carbon::parse($from)->format('M d, Y') }}</span>
            </td>
            <td>
                <span class="ml">To</span>
                <span class="mv">{{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</span>
            </td>
            <td>
                <span class="ml">Generated</span>
                <span class="mv">{{ now()->format('M d, Y') }}</span>
            </td>
        </tr>
    </table>
    @endif

    {{-- COUNTS --}}
    @php
        $total          = $personalDataSheets->count();
        $submittedCnt   = $personalDataSheets->where('status', 'submitted')->count();
        $approvedCnt    = $personalDataSheets->where('status', 'approved')->count();
        $disapprovedCnt = $personalDataSheets->where('status', 'disapproved')->count();
    @endphp

    {{-- SUMMARY --}}
    <div class="summary-block">
        @if($status === 'approved')
            Between <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> and
            <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>, a total of
            <strong>{{ $approvedCnt }}</strong> Personal Data Sheet(s) were approved.
            This report provides details of all approved PDS records during the selected period.
        @elseif($status === 'submitted')
            Between <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> and
            <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>, a total of
            <strong>{{ $submittedCnt }}</strong> Personal Data Sheet(s) are currently pending review.
            This report provides details of all submitted PDS records during the selected period.
        @elseif($status === 'disapproved')
            Between <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> and
            <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>, a total of
            <strong>{{ $disapprovedCnt }}</strong> Personal Data Sheet(s) were disapproved.
            This report provides details of all disapproved PDS records during the selected period.
        @else
            Between <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> and
            <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>, a total of
            <strong>{{ $total }}</strong> Personal Data Sheet(s) were submitted.
            Of these, <strong>{{ $approvedCnt }}</strong> were approved,
            <strong>{{ $submittedCnt }}</strong> are pending review, and
            <strong>{{ $disapprovedCnt }}</strong> were disapproved.
        @endif
    </div>

    {{-- STATS --}}
    <table class="stats-table">
        <tr>
            <td>
                <div class="s-num">{{ $total }}</div>
                <div class="s-lbl">Total</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num submitted">{{ $submittedCnt }}</div>
                <div class="s-lbl">Submitted</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num approved">{{ $approvedCnt }}</div>
                <div class="s-lbl">Approved</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num disapproved">{{ $disapprovedCnt }}</div>
                <div class="s-lbl">Disapproved</div>
            </td>
        </tr>
    </table>

    {{-- SECTION HEADER --}}
    <table class="sec-hdr">
        <tr>
            <td class="sec-title">Personal Data Sheet Records</td>
            <td class="sec-count">{{ $total }} record(s) found</td>
        </tr>
    </table>

    {{-- PDS TABLE --}}
    @if($total > 0)
    <table class="pds-table">
        <thead>
            <tr>
                <th style="width:18px;">#</th>
                <th>Employee Name</th>
                <th style="width:55px;">Sex</th>
                <th style="width:75px;">Date of Birth</th>
                <th style="width:80px;">Mobile</th>
                <th>Email</th>
                <th style="width:55px;">Completion</th>
                <th style="width:65px;">Status</th>
                <th style="width:70px;">Date Submitted</th>
            </tr>
        </thead>
        <tbody>
            @foreach($personalDataSheets as $index => $pds)
            @php
                // Build full name from PDS fields directly (no employee relation needed)
                $nameParts = array_filter([
                    $pds->surname,
                    $pds->first_name,
                    $pds->middle_name,
                    $pds->name_extension,
                ]);
                $fullName = implode(' ', $nameParts) ?: ($pds->user?->name ?? '—');

                // Completion rate calculation (mirrors PersonalDataSheetResource::calculateCompletionRate)
                $basicFields   = ['surname','first_name','date_of_birth','place_of_birth','sex','civil_status','height','weight','blood_type','mobile','email'];
                $addressFields = ['res_house_block_lot_no','res_street','res_barangay','res_city_municipality','res_province','res_zip_code'];
                $jsonFields    = ['children','education','work_experience','references'];

                $totalFields  = count($basicFields) + count($addressFields) + count($jsonFields) + 1; // +1 gov ID
                $filledFields = 0;

                foreach ($basicFields as $f)   { if (!blank($pds->$f)) $filledFields++; }
                foreach ($addressFields as $f)  { if (!blank($pds->$f)) $filledFields++; }
                foreach ($jsonFields as $f) {
                    $val = $pds->$f;
                    $data = is_array($val) ? $val : (is_string($val) ? json_decode($val, true) : []);
                    if (is_array($data) && count($data) > 0) $filledFields++;
                }
                if (!blank($pds->gov_id_type) && !blank($pds->gov_id_no)) $filledFields++;

                $completion = $totalFields > 0 ? round(($filledFields / $totalFields) * 100) : 0;

                $barColor = $completion >= 90 ? '#16a34a' : ($completion >= 70 ? '#d97706' : '#dc2626');
            @endphp
            <tr class="{{ $index % 2 === 0 ? 'even' : 'odd' }}">
                <td class="row-num">{{ $index + 1 }}</td>
                <td><span class="emp-name">{{ $fullName }}</span></td>
                <td>{{ $pds->sex ?? '—' }}</td>
                <td>{{ $pds->date_of_birth ? \Carbon\Carbon::parse($pds->date_of_birth)->format('M d, Y') : '—' }}</td>
                <td>{{ $pds->mobile ?? '—' }}</td>
                <td>{{ $pds->email ?? '—' }}</td>
                <td>
                    {{-- Simple text percentage for PDF compatibility --}}
                    <span style="font-weight:bold; color:{{ $barColor }};">{{ $completion }}%</span>
                </td>
                <td>
                    @if($pds->status === 'approved')
                        <span class="badge b-approved">Approved</span>
                    @elseif($pds->status === 'submitted')
                        <span class="badge b-submitted">Submitted</span>
                    @elseif($pds->status === 'disapproved')
                        <span class="badge b-disapproved">Disapproved</span>
                    @else
                        <span class="badge b-submitted">{{ ucfirst($pds->status ?? '—') }}</span>
                    @endif
                </td>
                <td>{{ $pds->created_at?->format('M d, Y') ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        No Personal Data Sheet records found for the selected filters and date range.
    </div>
    @endif

    {{-- SIGNATURES --}}
    <div class="sig-section">
        <table class="sig-table">
            <tr>
                <td>
                    <span class="sig-label">Prepared by:</span>
                    <div class="sig-line">
                        <div class="sig-name">{{ auth()->user()->name }}</div>
                        <div class="sig-title">System Administrator</div>
                    </div>
                </td>
                <td>
                    <span class="sig-label">Noted by:</span>
                    <div class="sig-line">
                        <div class="sig-name">&nbsp;</div>
                        <div class="sig-title">HR Officer</div>
                    </div>
                </td>
                <td>
                    <span class="sig-label">Approved by:</span>
                    <div class="sig-line">
                        <div class="sig-name">&nbsp;</div>
                        <div class="sig-title">Regional Director</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>
