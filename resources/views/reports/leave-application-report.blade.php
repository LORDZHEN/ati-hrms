<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Leave Application Report</title>
    <style>
        /*
        ┌──────────────────────────────────────────────────────────────────┐
        │  DomPDF CONSTRAINTS — landscape A4                               │
        │  · No flexbox / CSS Grid  →  <table> for layout                  │
        │  · Images via public_path(), not asset()                         │
        │  · Font: 'DejaVu Sans' (bundled with DomPDF)                    │
        │  · position:fixed repeats on every page                          │
        └──────────────────────────────────────────────────────────────────┘
        */

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10.5px;
            line-height: 1.5;
            color: #1f2937;
            background: #ffffff;
        }

        /* ── Page wrapper ────────────────────────────────────────────── */
        .page { padding: 24px 28px 50px; }

        /* ── Running page footer ────────────────────────────────────── */
        .page-footer {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            padding: 5px 28px;
            border-top: 1px solid #d1d5db;
        }

        .page-footer table { width: 100%; }
        .page-footer td { border: none; padding: 0; font-size: 8px; color: #6b7280; }

        /* ── Report header ──────────────────────────────────────────── */
        .report-header {
            border-bottom: 3px solid #1a3a5c;
            padding-bottom: 11px;
            margin-bottom: 12px;
        }

        .republic-label {
            text-align: center;
            font-size: 8px;
            letter-spacing: 0.07em;
            color: #4b5563;
            text-transform: uppercase;
            margin-bottom: 7px;
        }

        .header-layout { width: 100%; }
        .header-layout td { border: none; padding: 0; vertical-align: middle; }

        .logo-cell { width: 62px; text-align: center; }
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
            margin-top: 2px;
        }

        .header-contact {
            text-align: center;
            font-size: 8.5px;
            color: #4b5563;
            margin-top: 5px;
            line-height: 1.5;
        }

        /* ── Report title ───────────────────────────────────────────── */
        .title-block { text-align: center; margin: 10px 0 10px; }

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

        /* ── Meta info bar ──────────────────────────────────────────── */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 11px;
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

        /* ── Summary block ──────────────────────────────────────────── */
        .summary-block {
            background: #f8f9fb;
            border-left: 4px solid #1a3a5c;
            padding: 8px 11px;
            margin-bottom: 11px;
            font-size: 10px;
            line-height: 1.7;
        }

        .summary-block strong { color: #1a3a5c; }

        /* ── Stats row ──────────────────────────────────────────────── */
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

        .stats-table td.gap { border: none; width: 8px; background: transparent; }

        .s-num          { font-size: 20px; font-weight: bold; color: #1a3a5c; line-height: 1; }
        .s-num.approved { color: #1a6b3a; }
        .s-num.disapproved { color: #8b1a1a; }
        .s-num.pending  { color: #7a4f00; }

        .s-lbl { font-size: 7.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-top: 3px; }

        /* ── Section header ─────────────────────────────────────────── */
        .sec-hdr { width: 100%; border-collapse: collapse; border-bottom: 2px solid #1a3a5c; margin-bottom: 6px; }
        .sec-hdr td { border: none; padding: 0 0 3px; vertical-align: bottom; }
        .sec-title { font-size: 11px; font-weight: bold; color: #1a3a5c; text-transform: uppercase; letter-spacing: 0.04em; }
        .sec-count { font-size: 8.5px; font-weight: bold; color: #6b7280; text-align: right; }

        /* ── Leave applications table ───────────────────────────────── */
        /*  Portrait A4 ≈ 555px usable. 10 cols → trimmed to 8 cols.   */
        .leave-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-bottom: 20px;
        }

        .leave-table thead tr { background-color: #1a3a5c; }

        .leave-table thead th {
            color: #ffffff;
            padding: 5px 6px;
            text-align: left;
            font-size: 7.5px;
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
            padding: 5px 6px;
            vertical-align: middle;
        }

        .row-num   { color: #9ca3af; font-size: 7.5px; }
        .emp-name  { font-weight: bold; color: #1f2937; }
        .leave-type { color: #374151; }

        /* ── Badges ─────────────────────────────────────────────────── */
        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 8px;
            font-size: 7.5px;
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
            padding: 26px 20px;
            color: #6b7280;
            font-size: 10.5px;
            border: 1px dashed #d1d5db;
            margin-bottom: 20px;
        }

        /* ── Signature section ──────────────────────────────────────── */
        .sig-section { margin-top: 26px; border-top: 1px solid #d1d5db; padding-top: 16px; page-break-inside: avoid; }
        .sig-table { width: 100%; }
        .sig-table td { width: 33.33%; text-align: center; padding: 0 8px; vertical-align: top; border: none; }

        .sig-label { display: block; font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.07em; color: #6b7280; margin-bottom: 32px; }
        .sig-line  { border-top: 1.5px solid #374151; padding-top: 4px; margin: 0 6px; }
        .sig-name  { font-size: 10px; font-weight: bold; color: #1a3a5c; }
        .sig-title { font-size: 8.5px; color: #4b5563; margin-top: 2px; }
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
            &nbsp;&bull;&nbsp; <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f0918499c1c1de91949480c4b0979d91999cde939f9d">[email&#160;protected]</a>
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
            Between <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> and
            <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>, a total of
            <strong>{{ $approved }}</strong> leave application(s) were approved,
            covering <strong>{{ $totalDays }}</strong> working day(s) in aggregate.
            This report provides details of all approved leave requests processed during the selected period.
        @elseif($status === 'disapproved')
            Between <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> and
            <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>, a total of
            <strong>{{ $disapproved }}</strong> leave application(s) were disapproved.
            This report provides details of all disapproved leave requests processed during the selected period.
        @elseif($status === 'pending')
            Between <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> and
            <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>, a total of
            <strong>{{ $pending }}</strong> leave application(s) are currently pending review and approval.
        @else
            Between <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> and
            <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>, a total of
            <strong>{{ $total }}</strong> leave application(s) were submitted, covering
            <strong>{{ $totalDays }}</strong> working day(s) in aggregate.
            Of these, <strong>{{ $approved }}</strong> were approved,
            <strong>{{ $disapproved }}</strong> were disapproved, and
            <strong>{{ $pending }}</strong> remain pending.
        @endif
    </div>

    {{-- ─────────────────────────── STATS ─────────────────────────────── --}}
    <table class="stats-table">
        <tr>
            <td>
                <div class="s-num">{{ $total }}</div>
                <div class="s-lbl">Total</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num approved">{{ $approved }}</div>
                <div class="s-lbl">Approved</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num disapproved">{{ $disapproved }}</div>
                <div class="s-lbl">Disapproved</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num pending">{{ $pending }}</div>
                <div class="s-lbl">Pending</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num">{{ $totalDays }}</div>
                <div class="s-lbl">Total Days</div>
            </td>
        </tr>
    </table>

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
                <th style="width:110px;">Employee Name</th>
                <th style="width:100px;">Type of Leave</th>
                <th style="width:62px;">Leave From</th>
                <th style="width:62px;">Leave To</th>
                <th style="width:36px;">Days</th>
                <th style="width:58px;">Status</th>
                <th style="width:88px;">Processed By</th>
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
