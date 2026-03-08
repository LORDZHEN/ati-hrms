<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Leave Application Report</title>
    <style>
        /*
        ┌──────────────────────────────────────────────────────────────────┐
        │  DomPDF CONSTRAINTS — portrait A4                                │
        │  · No flexbox / CSS Grid  →  <table> for layout                  │
        │  · Images via public_path(), not asset()                         │
        │  · Font: 'DejaVu Sans' (bundled with DomPDF)                    │
        │  · position:fixed repeats on every page                          │
        └──────────────────────────────────────────────────────────────────┘
        */

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #1f2937;
            background: #ffffff;
        }

        /* ── Page wrapper ────────────────────────────────────────────── */
        .page { padding: 28px 32px 60px; }

        /* ── Running page footer ────────────────────────────────────── */
        .page-footer {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            padding: 6px 32px;
            border-top: 1px solid #d1d5db;
        }

        .page-footer table { width: 100%; }
        .page-footer td { border: none; padding: 0; font-size: 9px; color: #6b7280; }

        /* ── Report header ──────────────────────────────────────────── */
        .report-header {
            border-bottom: 3px solid #1a3a5c;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }

        .republic-label {
            text-align: center;
            font-size: 9.5px;
            letter-spacing: 0.07em;
            color: #4b5563;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .header-layout { width: 100%; }
        .header-layout td { border: none; padding: 0; vertical-align: middle; }

        .logo-cell { width: 62px; text-align: center; }
        .logo-cell img { height: 56px; width: auto; }

        .title-cell { text-align: center; padding: 0 10px; }

        .org-name {
            font-size: 17px;
            font-weight: bold;
            color: #1a3a5c;
            letter-spacing: 0.03em;
            line-height: 1.2;
        }

        .org-branch {
            font-size: 12px;
            font-weight: bold;
            color: #2c5f8a;
            letter-spacing: 0.04em;
            margin-top: 3px;
        }

        .header-contact {
            text-align: center;
            font-size: 10px;
            color: #4b5563;
            margin-top: 6px;
            line-height: 1.6;
        }

        /* ── Report title ───────────────────────────────────────────── */
        .title-block { text-align: center; margin: 12px 0 12px; }

        .title-block h1 {
            font-size: 15px;
            font-weight: bold;
            color: #1a3a5c;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .title-hr {
            width: 180px;
            border: none;
            border-top: 1.5px solid #b8860b;
            margin: 5px auto 0;
        }

        /* ── Meta info bar ──────────────────────────────────────────── */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 13px;
            border: 1px solid #d1d5db;
        }

        .meta-table td {
            background: #f8f9fb;
            padding: 6px 10px;
            text-align: center;
            border-right: 1px solid #d1d5db;
            vertical-align: middle;
        }

        .meta-table td:last-child { border-right: none; }

        .ml { display: block; font-size: 8.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.08em; color: #6b7280; margin-bottom: 2px; }
        .mv { font-size: 10.5px; font-weight: bold; color: #1a3a5c; }

        /* ── Summary block ──────────────────────────────────────────── */
        .summary-block {
            background: #f8f9fb;
            border-left: 4px solid #1a3a5c;
            padding: 10px 13px;
            margin-bottom: 13px;
            font-size: 11.5px;
            line-height: 1.85;
            text-align: justify;
        }

        .summary-block strong { color: #1a3a5c; }

        /* ── Section header ─────────────────────────────────────────── */
        .sec-hdr { width: 100%; border-collapse: collapse; border-bottom: 2px solid #1a3a5c; margin-bottom: 7px; }
        .sec-hdr td { border: none; padding: 0 0 4px; vertical-align: bottom; }
        .sec-title { font-size: 12.5px; font-weight: bold; color: #1a3a5c; text-transform: uppercase; letter-spacing: 0.04em; }
        .sec-count { font-size: 10px; font-weight: bold; color: #6b7280; text-align: right; }

        /* ── Leave applications table ───────────────────────────────── */
        .leave-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 20px;
        }

        .leave-table thead tr { background-color: #1a3a5c; }

        .leave-table thead th {
            color: #ffffff;
            padding: 6px 7px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }

        .leave-table tbody tr { border-bottom: 1px solid #e5e7eb; }
        .leave-table tbody tr.even { background-color: #f8f9fb; }
        .leave-table tbody tr.odd  { background-color: #ffffff; }
        .leave-table tbody tr:last-child { border-bottom: 2px solid #1a3a5c; }

        .leave-table td {
            padding: 6px 7px;
            vertical-align: middle;
        }

        .row-num   { color: #9ca3af; font-size: 9px; }
        .emp-name  { font-weight: bold; color: #1f2937; }
        .leave-type { color: #374151; }

        /* ── Badges ─────────────────────────────────────────────────── */
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 8px;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            white-space: nowrap;
        }

        .b-approved    { background: #e8f5ee; color: #1a6b3a; border: 1px solid #9dd0b2; }
        .b-disapproved { background: #fde8e8; color: #8b1a1a; border: 1px solid #f5b8b8; }
        .b-pending     { background: #fff3cd; color: #7a4f00; border: 1px solid #e8b84b; }

        /* leave type chips */
        .b-vacation   { background: #e0f7e9; color: #1a5c3a; border: 1px solid #80c9a0; }
        .b-sick       { background: #fde8e8; color: #8b1a1a; border: 1px solid #f5b8b8; }
        .b-maternity  { background: #f0e8ff; color: #5a1a8b; border: 1px solid #c0a0e8; }
        .b-paternity  { background: #e0f0ff; color: #1a4a7a; border: 1px solid #90c0e8; }
        .b-mandatory  { background: #fff3e0; color: #7a4000; border: 1px solid #e8a84b; }
        .b-other      { background: #f0f4ff; color: #1a3a7a; border: 1px solid #a0b4e8; }

        /* ── No data ────────────────────────────────────────────────── */
        .no-data {
            text-align: center;
            padding: 28px 20px;
            color: #6b7280;
            font-size: 11.5px;
            border: 1px dashed #d1d5db;
            margin-bottom: 20px;
        }

        /* ── Signature section ──────────────────────────────────────── */
        .sig-section { margin-top: 30px; border-top: 1px solid #d1d5db; padding-top: 18px; page-break-inside: avoid; }
        .sig-table { width: 100%; }
        .sig-table td { width: 33.33%; text-align: center; padding: 0 10px; vertical-align: top; border: none; }

        .sig-label { display: block; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.07em; color: #6b7280; margin-bottom: 36px; }
        .sig-line  { border-top: 1.5px solid #374151; padding-top: 5px; margin: 0 6px; }
        .sig-name  { font-size: 11.5px; font-weight: bold; color: #1a3a5c; }
        .sig-title { font-size: 10px; color: #4b5563; margin-top: 2px; }
    </style>
</head>
<body>

{{-- ── Running page footer ──────────────────────────────────────────── --}}
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

    {{-- ──────────────────────────── HEADER ────────────────────────────── --}}
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
            &nbsp;&bull;&nbsp; rtc11@ati.da.gov.ph
            &nbsp;&bull;&nbsp; ati.da.gov.ph/region11
        </div>
    </div>

    {{-- ─────────────────────────── TITLE ──────────────────────────────── --}}
    <div class="title-block">
        <h1>Leave Application Report</h1>
        <hr class="title-hr">
    </div>

    {{-- ──────────────────────────── META BAR ──────────────────────────── --}}
    @if($from && $to)
    <table class="meta-table">
        <tr>
            <td>
                <span class="ml">Status Filter</span>
                <span class="mv">{{ ($status === 'all' || !$status) ? 'All Applications' : ucfirst($status) }}</span>
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

    {{-- ─────────────────────── COMPUTED COUNTS ────────────────────────── --}}
    @php
        $total        = $leaveApplications->count();
        $approved     = $leaveApplications->where('status', 'approved')->count();
        $disapproved  = $leaveApplications->where('status', 'disapproved')->count();
        $pending      = $leaveApplications->where('status', 'pending')->count();
        $totalDays    = $leaveApplications->sum('number_of_working_days');
    @endphp

    {{-- ─────────────────────────── SUMMARY ───────────────────────────── --}}
    <div class="summary-block">
        @if($status === 'approved')
            This report presents the consolidated leave application records of personnel under the Agricultural Training Institute — Regional Training Center XI (ATI-RTC XI) for the period covering <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>. For the reference period, a total of <strong>{{ $approved }}</strong> leave application(s) were duly reviewed and subsequently <strong>approved</strong> by the authorized approving officer, collectively accounting for an aggregate of <strong>{{ $totalDays }}</strong> working day(s) of leave availed. This document is intended for official reference, monitoring, and compliance purposes in accordance with existing Civil Service Commission rules and regulations governing leave administration.
        @elseif($status === 'disapproved')
            This report presents the consolidated leave application records of personnel under the Agricultural Training Institute — Regional Training Center XI (ATI-RTC XI) for the period covering <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>. For the reference period, a total of <strong>{{ $disapproved }}</strong> leave application(s) were reviewed and <strong>disapproved</strong> by the authorized approving officer. The disapproval of the herein enumerated leave applications may be attributed to various grounds including, but not limited to, exigency of service, incomplete documentation, or non-compliance with prescribed leave procedures. This document is issued for official reference and records management purposes.
        @elseif($status === 'pending')
            This report presents the consolidated leave application records of personnel under the Agricultural Training Institute — Regional Training Center XI (ATI-RTC XI) for the period covering <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>. As of the date of generation of this report, a total of <strong>{{ $pending }}</strong> leave application(s) remain <strong>pending</strong> final action and have not yet been acted upon by the authorized approving officer. The concerned Human Resource Management Officer is hereby advised to expedite the processing of all pending applications in accordance with established timelines under Civil Service Commission regulations.
        @else
            This report presents the consolidated leave application records of personnel under the Agricultural Training Institute — Regional Training Center XI (ATI-RTC XI) for the reference period of <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>. During the said period, a total of <strong>{{ $total }}</strong> leave application(s) were filed and recorded in the Human Resource Management System, covering an aggregate of <strong>{{ $totalDays }}</strong> working day(s). Of the total applications received, <strong>{{ $approved }}</strong> were duly approved by the authorized approving officer, <strong>{{ $disapproved }}</strong> were disapproved on grounds evaluated by the approving authority, and <strong>{{ $pending }}</strong> remain pending final action as of the date of this report. This document is prepared for official monitoring, compliance, and administrative reference purposes consistent with the leave administration policies of the Civil Service Commission.
        @endif
    </div>

    {{-- ─────────────────────── SECTION HEADER ────────────────────────── --}}
    <table class="sec-hdr">
        <tr>
            <td class="sec-title">Leave Application Records</td>
            <td class="sec-count">{{ $total }} record(s) found</td>
        </tr>
    </table>

    {{-- ──────────────────────── LEAVE TABLE ──────────────────────────── --}}
    @if($total > 0)
    <table class="leave-table">
        <thead>
            <tr>
                <th style="width:18px;">#</th>
                <th style="width:115px;">Employee Name</th>
                <th style="width:105px;">Type of Leave</th>
                <th style="width:65px;">Leave From</th>
                <th style="width:65px;">Leave To</th>
                <th style="width:38px;">Days</th>
                <th style="width:62px;">Status</th>
                <th style="width:90px;">Processed By</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leaveApplications as $index => $leave)
            @php
                $employeeName = trim(
                    ($leave->employee?->first_name ?? '') . ' ' .
                    ($leave->employee?->middle_name ? $leave->employee->middle_name . ' ' : '') .
                    ($leave->employee?->last_name ?? '')
                ) ?: ($leave->employee?->name ?? '—');

                $leaveTypeRaw = $leave->type_of_leave ?? '';
                $leaveTypeLabel = $leave->type_of_leave === 'others'
                    ? ($leave->other_leave_type ?? 'Others')
                    : ucwords(str_replace('_', ' ', $leaveTypeRaw));

                $leaveTypeBadge = match(true) {
                    str_contains($leaveTypeRaw, 'vacation')  => 'b-vacation',
                    str_contains($leaveTypeRaw, 'sick')      => 'b-sick',
                    str_contains($leaveTypeRaw, 'maternity') => 'b-maternity',
                    str_contains($leaveTypeRaw, 'paternity') => 'b-paternity',
                    str_contains($leaveTypeRaw, 'mandatory') => 'b-mandatory',
                    default                                  => 'b-other',
                };
            @endphp
            <tr class="{{ $index % 2 === 0 ? 'even' : 'odd' }}">
                <td class="row-num">{{ $index + 1 }}</td>
                <td><span class="emp-name">{{ $employeeName }}</span></td>
                <td><span class="badge {{ $leaveTypeBadge }}">{{ $leaveTypeLabel }}</span></td>
                <td>{{ $leave->leave_date_from?->format('M d, Y') ?? '—' }}</td>
                <td>{{ $leave->leave_date_to?->format('M d, Y') ?? '—' }}</td>
                <td style="text-align:center;">{{ $leave->number_of_working_days ?? '—' }}</td>
                <td>
                    @if($leave->status === 'approved')
                        <span class="badge b-approved">Approved</span>
                    @elseif($leave->status === 'disapproved')
                        <span class="badge b-disapproved">Disapproved</span>
                    @else
                        <span class="badge b-pending">Pending</span>
                    @endif
                </td>
                <td>{{ $leave->authorized_officer ?? 'Not yet processed' }}</td>
                <td>{{ $leave->disapproval_reason ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        No leave application records found for the selected filters and date range.
    </div>
    @endif

    {{-- ─────────────────────────── SIGNATURES ────────────────────────── --}}
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
