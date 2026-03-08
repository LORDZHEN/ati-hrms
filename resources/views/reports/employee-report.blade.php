<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Employee Comprehensive Report</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #1f2937;
            background: #ffffff;
        }

        .page { padding: 28px 32px 60px; }

        /* ── Running footer ──────────────────────────────────────────── */
        .page-footer {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            padding: 6px 32px;
            border-top: 1px solid #d1d5db;
        }

        .page-footer table { width: 100%; }
        .page-footer td { border: none; padding: 0; font-size: 9px; color: #6b7280; }

        /* ── Report header ─────────────────────────────────────────── */
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

        /* ── Report title ──────────────────────────────────────────── */
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

        /* ── Meta info bar ─────────────────────────────────────────── */
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

        /* ── Summary block ─────────────────────────────────────────── */
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

        /* ── Section header ────────────────────────────────────────── */
        .sec-hdr { width: 100%; border-collapse: collapse; border-bottom: 2px solid #1a3a5c; margin-bottom: 7px; }
        .sec-hdr td { border: none; padding: 0 0 4px; vertical-align: bottom; }
        .sec-title { font-size: 12.5px; font-weight: bold; color: #1a3a5c; text-transform: uppercase; letter-spacing: 0.04em; }
        .sec-count { font-size: 10px; font-weight: bold; color: #6b7280; text-align: right; }

        /* ── Employee table ────────────────────────────────────────── */
        .emp-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 22px;
        }

        .emp-table thead tr { background-color: #1a3a5c; }

        .emp-table thead th {
            color: #ffffff;
            padding: 6px 8px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }

        .emp-table tbody tr { border-bottom: 1px solid #e5e7eb; }
        .emp-table tbody tr.even { background-color: #f8f9fb; }
        .emp-table tbody tr.odd  { background-color: #ffffff; }
        .emp-table tbody tr:last-child { border-bottom: 2px solid #1a3a5c; }

        .emp-table td { padding: 6px 8px; vertical-align: middle; }

        .row-num  { color: #9ca3af; font-size: 9px; }
        .emp-id   { font-family: 'Courier New', Courier, monospace; font-size: 9px; color: #2c5f8a; font-weight: bold; }
        .emp-name { font-weight: bold; color: #1f2937; }

        /* ── Badges ────────────────────────────────────────────────── */
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

        .b-active     { background: #e8f5ee; color: #1a6b3a; border: 1px solid #9dd0b2; }
        .b-pending    { background: #fff3cd; color: #7a4f00; border: 1px solid #e8b84b; }
        .b-inactive   { background: #fde8e8; color: #8b1a1a; border: 1px solid #f5b8b8; }
        .b-verified   { background: #e0f0ff; color: #1a4a7a; border: 1px solid #90c0e8; }
        .b-unverified { background: #f5f5f5; color: #6b7280; border: 1px solid #d1d5db; }
        .b-admin      { background: #fff0f0; color: #8b1a1a; border: 1px solid #f5b8b8; }
        .b-employee   { background: #f0f4ff; color: #1a3a7a; border: 1px solid #a0b4e8; }

        /* ── No-data notice ────────────────────────────────────────── */
        .no-data {
            text-align: center;
            padding: 28px 20px;
            color: #6b7280;
            font-size: 11.5px;
            border: 1px dashed #d1d5db;
            margin-bottom: 22px;
        }

        /* ── Signature section ─────────────────────────────────────── */
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
        <h1>Employee Comprehensive Report</h1>
        <hr class="title-hr">
    </div>

    {{-- META BAR --}}
    @if($from && $to)
    <table class="meta-table">
        <tr>
            <td>
                <span class="ml">Status Filter</span>
                <span class="mv">{{ ($status === 'all' || !$status) ? 'All Employees' : ucfirst($status) }}</span>
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

    {{-- COMPUTED COUNTS --}}
    @php
        $count         = $employees->count();
        $activeCount   = $employees->where('status', 'active')->count();
        $pendingCount  = $employees->where('status', 'pending')->count();
        $inactiveCount = $employees->where('status', 'inactive')->count();
        $verifiedCount = $employees->filter(fn($e) => !is_null($e->email_verified_at))->count();
    @endphp

    {{-- SUMMARY --}}
    <div class="summary-block">
        @if($status === 'active')
            This report presents the consolidated roster of active personnel under the Agricultural Training Institute — Regional Training Center XI (ATI-RTC XI) for the reference period of <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>. As of the date of this report, a total of <strong>{{ $count }}</strong> employee accounts are recorded with an <strong>active</strong> status in the Human Resource Management System. These personnel are currently engaged and performing their respective duties and responsibilities across various departments and functional units within the organization. This document is prepared for official workforce monitoring, administrative reference, and compliance purposes.
        @elseif($status === 'inactive')
            This report presents the consolidated records of inactive personnel under the Agricultural Training Institute — Regional Training Center XI (ATI-RTC XI) for the reference period of <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>. A total of <strong>{{ $count }}</strong> employee account(s) currently carry an <strong>inactive</strong> status in the Human Resource Management System. These individuals are no longer actively engaged due to resignation, retirement, termination, or administrative deactivation. This report is issued for official records management, audit trail, and human resource planning purposes.
        @elseif($status === 'pending')
            This report presents the consolidated records of employee accounts pending administrative approval under the Agricultural Training Institute — Regional Training Center XI (ATI-RTC XI) for the reference period of <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>. As of the date of generation of this report, a total of <strong>{{ $count }}</strong> employee account(s) remain in <strong>pending</strong> status, awaiting verification and activation by the designated system administrator. The concerned Human Resource Management Officer is hereby advised to process these accounts in a timely manner in accordance with established onboarding procedures.
        @else
            This report presents the comprehensive roster of personnel on record under the Agricultural Training Institute — Regional Training Center XI (ATI-RTC XI) for the reference period of <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>. During the said period, a total of <strong>{{ $count }}</strong> employee accounts are recorded in the Human Resource Management System. Of these, <strong>{{ $activeCount }}</strong> are currently active and performing their assigned duties, <strong>{{ $pendingCount }}</strong> are pending administrative verification and activation, and <strong>{{ $inactiveCount }}</strong> have been deactivated due to separation, retirement, or other administrative grounds. Furthermore, <strong>{{ $verifiedCount }}</strong> accounts have verified email addresses on record. This document is prepared for official workforce monitoring, compliance, and administrative reference purposes.
        @endif
    </div>

    {{-- SECTION HEADER --}}
    <table class="sec-hdr">
        <tr>
            <td class="sec-title">Employee Records</td>
            <td class="sec-count">{{ $count }} record(s) found</td>
        </tr>
    </table>

    {{-- EMPLOYEE TABLE --}}
    @if($count > 0)
    <table class="emp-table">
        <thead>
            <tr>
                <th style="width:24px;">#</th>
                <th style="width:86px;">Employee ID</th>
                <th>Full Name</th>
                <th>Department</th>
                <th>Position</th>
                <th style="width:68px;">Role</th>
                <th style="width:64px;">Status</th>
                <th style="width:76px;">Email</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $index => $emp)
            <tr class="{{ $index % 2 === 0 ? 'even' : 'odd' }}">
                <td class="row-num">{{ $index + 1 }}</td>
                <td><span class="emp-id">{{ $emp->employee_id ?? '—' }}</span></td>
                <td><span class="emp-name">{{ $emp->name }}</span></td>
                <td>{{ $emp->department ?? '—' }}</td>
                <td>{{ $emp->position ?? '—' }}</td>
                <td>
                    @if($emp->role === 'admin')
                        <span class="badge b-admin">Admin</span>
                    @else
                        <span class="badge b-employee">Employee</span>
                    @endif
                </td>
                <td>
                    @if($emp->status === 'active')
                        <span class="badge b-active">Active</span>
                    @elseif($emp->status === 'pending')
                        <span class="badge b-pending">Pending</span>
                    @else
                        <span class="badge b-inactive">Inactive</span>
                    @endif
                </td>
                <td>
                    @if($emp->email_verified_at)
                        <span class="badge b-verified">Verified</span>
                    @else
                        <span class="badge b-unverified">Not Verified</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        No employee records found for the selected filters and date range.
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
