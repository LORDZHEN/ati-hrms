<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Employee Comprehensive Report</title>
    <style>
        /*
        ┌──────────────────────────────────────────────────────────────────┐
        │  DomPDF CSS CONSTRAINTS                                          │
        │  · No flexbox / CSS Grid  →  use <table> for multi-column layout │
        │  · No @import / web fonts  →  use 'DejaVu Sans' (bundled)        │
        │  · border-radius: partially supported                            │
        │  · position:fixed works for repeating headers/footers            │
        │  · Images: use public_path(), NOT asset()                        │
        └──────────────────────────────────────────────────────────────────┘
        */

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #1f2937;
            background: #ffffff;
        }

        /* ── Page wrapper ──────────────────────────────────────────── */
        .page { padding: 28px 32px 50px; }

        /* ── Running footer on every page ──────────────────────────── */
        .page-footer {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            padding: 5px 32px;
            border-top: 1px solid #d1d5db;
        }

        .page-footer table { width: 100%; }
        .page-footer td { border: none; padding: 0; font-size: 8.5px; color: #6b7280; }

        /* ── Report header ─────────────────────────────────────────── */
        .report-header {
            border-bottom: 3px solid #1a3a5c;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }

        .republic-label {
            text-align: center;
            font-size: 8.5px;
            letter-spacing: 0.07em;
            color: #4b5563;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        /* logo / title row */
        .header-layout { width: 100%; }
        .header-layout td { border: none; padding: 0; vertical-align: middle; }

        .logo-cell { width: 66px; text-align: center; }
        .logo-cell img { height: 56px; width: auto; }

        .title-cell { text-align: center; padding: 0 10px; }

        .org-name {
            font-size: 16px;
            font-weight: bold;
            color: #1a3a5c;
            letter-spacing: 0.03em;
            line-height: 1.2;
        }

        .org-branch {
            font-size: 10.5px;
            font-weight: bold;
            color: #2c5f8a;
            letter-spacing: 0.04em;
            margin-top: 2px;
        }

        .header-contact {
            text-align: center;
            font-size: 9px;
            color: #4b5563;
            margin-top: 6px;
            line-height: 1.5;
        }

        /* ── Report title ──────────────────────────────────────────── */
        .title-block { text-align: center; margin: 12px 0 11px; }

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
            margin-bottom: 12px;
            border: 1px solid #d1d5db;
        }

        .meta-table td {
            background: #f8f9fb;
            padding: 5px 9px;
            text-align: center;
            border-right: 1px solid #d1d5db;
            vertical-align: middle;
        }

        .meta-table td:last-child { border-right: none; }

        .ml { display: block; font-size: 7.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.08em; color: #6b7280; margin-bottom: 1px; }
        .mv { font-size: 9.5px; font-weight: bold; color: #1a3a5c; }

        /* ── Summary block ─────────────────────────────────────────── */
        .summary-block {
            background: #f8f9fb;
            border-left: 4px solid #1a3a5c;
            padding: 9px 12px;
            margin-bottom: 12px;
            font-size: 10.5px;
            line-height: 1.7;
        }

        .summary-block strong { color: #1a3a5c; }

        /* ── Stats row ─────────────────────────────────────────────── */
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .stats-table td {
            border: 1px solid #d1d5db;
            padding: 9px 6px;
            text-align: center;
            background: #ffffff;
            vertical-align: middle;
        }

        /* small spacing between cells via a gap column */
        .stats-table td.gap { border: none; width: 8px; background: transparent; }

        .s-num { font-size: 22px; font-weight: bold; color: #1a3a5c; line-height: 1; }
        .s-num.active   { color: #1a6b3a; }
        .s-num.pending  { color: #7a4f00; }
        .s-num.inactive { color: #8b1a1a; }

        .s-lbl { font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-top: 3px; }

        /* ── Section header ────────────────────────────────────────── */
        .sec-hdr { width: 100%; border-collapse: collapse; border-bottom: 2px solid #1a3a5c; margin-bottom: 7px; }
        .sec-hdr td { border: none; padding: 0 0 4px; vertical-align: bottom; }
        .sec-title { font-size: 11.5px; font-weight: bold; color: #1a3a5c; text-transform: uppercase; letter-spacing: 0.04em; }
        .sec-count { font-size: 9px; font-weight: bold; color: #6b7280; text-align: right; }

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
            font-size: 8.5px;
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

        .row-num  { color: #9ca3af; font-size: 8.5px; }
        .emp-id   { font-family: 'Courier New', Courier, monospace; font-size: 9px; color: #2c5f8a; font-weight: bold; }
        .emp-name { font-weight: bold; color: #1f2937; }

        /* ── Badges ────────────────────────────────────────────────── */
        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 8px;
            font-size: 8px;
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
            font-size: 11px;
            border: 1px dashed #d1d5db;
            margin-bottom: 22px;
        }

        /* ── Signature section ─────────────────────────────────────── */
        .sig-section { margin-top: 28px; border-top: 1px solid #d1d5db; padding-top: 18px; page-break-inside: avoid; }
        .sig-table { width: 100%; }
        .sig-table td { width: 33.33%; text-align: center; padding: 0 8px; vertical-align: top; border: none; }

        .sig-label { display: block; font-size: 8.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.07em; color: #6b7280; margin-bottom: 34px; }

        .sig-line { border-top: 1.5px solid #374151; padding-top: 4px; margin: 0 6px; }
        .sig-name  { font-size: 10.5px; font-weight: bold; color: #1a3a5c; }
        .sig-title { font-size: 9px; color: #4b5563; margin-top: 2px; }
    </style>
</head>
<body>

{{-- ── Running page footer ─────────────────────────────────────────── --}}
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

    {{-- ───────────────────────── HEADER ───────────────────────────── --}}
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

    {{-- ─────────────────────── REPORT TITLE ───────────────────────── --}}
    <div class="title-block">
        <h1>Employee Comprehensive Report</h1>
        <hr class="title-hr">
    </div>

    {{-- ──────────────────────── META BAR ──────────────────────────── --}}
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

    {{-- ──────────────────── COMPUTED COUNTS ───────────────────────── --}}
    @php
        $count         = $employees->count();
        $activeCount   = $employees->where('status', 'active')->count();
        $pendingCount  = $employees->where('status', 'pending')->count();
        $inactiveCount = $employees->where('status', 'inactive')->count();
        $verifiedCount = $employees->filter(fn($e) => !is_null($e->email_verified_at))->count();
    @endphp

    {{-- ──────────────────────── SUMMARY ───────────────────────────── --}}
    <div class="summary-block">
        @if($status === 'active')
            As of <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong>, there are a total of
            <strong>{{ $count }}</strong> active employee accounts on record.
            These personnel are currently engaged across various departments and positions within the organization.
            This report provides a detailed overview of the active workforce during the selected period.
        @elseif($status === 'inactive')
            As of <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong>, there are
            <strong>{{ $count }}</strong> inactive employee accounts in the system.
            These individuals are currently not active due to resignation, termination, or temporary deactivation.
            This report summarizes their status within the selected period.
        @elseif($status === 'pending')
            As of <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong>, there are
            <strong>{{ $count }}</strong> employee accounts pending approval.
            These accounts are awaiting administrative verification before system access can be granted.
        @else
            Between <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> and
            <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>,
            a total of <strong>{{ $count }}</strong> employee accounts are recorded in the system.
            Of these, <strong>{{ $activeCount }}</strong> are active,
            <strong>{{ $pendingCount }}</strong> are pending approval, and
            <strong>{{ $inactiveCount }}</strong> are inactive.
            The following section provides comprehensive details for each employee within this period.
        @endif
    </div>

    {{-- ────────────────────────── STATS ───────────────────────────── --}}
    <table class="stats-table">
        <tr>
            <td>
                <div class="s-num">{{ $count }}</div>
                <div class="s-lbl">Total</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num active">{{ $activeCount }}</div>
                <div class="s-lbl">Active</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num pending">{{ $pendingCount }}</div>
                <div class="s-lbl">Pending</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num inactive">{{ $inactiveCount }}</div>
                <div class="s-lbl">Inactive</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num">{{ $verifiedCount }}</div>
                <div class="s-lbl">Verified Email</div>
            </td>
        </tr>
    </table>

    {{-- ─────────────────────── SECTION HEADER ─────────────────────── --}}
    <table class="sec-hdr">
        <tr>
            <td class="sec-title">Employee Records</td>
            <td class="sec-count">{{ $count }} record(s) found</td>
        </tr>
    </table>

    {{-- ──────────────────────── EMPLOYEE TABLE ────────────────────── --}}
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

    {{-- ──────────────────────── SIGNATURES ───────────────────────── --}}
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

</div>{{-- /.page --}}
</body>
</html>
