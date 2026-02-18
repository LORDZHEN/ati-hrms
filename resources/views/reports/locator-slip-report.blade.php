<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Locator Slip Report</title>
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
        .s-num.approved    { color: #1a6b3a; }
        .s-num.pending     { color: #7a4f00; }
        .s-num.disapproved { color: #8b1a1a; }

        .s-lbl { font-size: 7.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-top: 3px; }

        /* ── Section header ──────────────────────────────────────────── */
        .sec-hdr { width: 100%; border-collapse: collapse; border-bottom: 2px solid #1a3a5c; margin-bottom: 6px; }
        .sec-hdr td { border: none; padding: 0 0 3px; vertical-align: bottom; }
        .sec-title { font-size: 11px; font-weight: bold; color: #1a3a5c; text-transform: uppercase; letter-spacing: 0.04em; }
        .sec-count { font-size: 8.5px; font-weight: bold; color: #6b7280; text-align: right; }

        /* ── Slip table — portrait-optimised ─────────────────────────── */
        /*
         *  Portrait A4 usable width ≈ 555px at 96dpi  (595 - 2×20 margins)
         *  We drop Out/In time and merge them, truncate long text cells.
         */
        .slip-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-bottom: 20px;
        }

        .slip-table thead tr { background-color: #1a3a5c; }

        .slip-table thead th {
            color: #ffffff;
            padding: 5px 6px;
            text-align: left;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }

        .slip-table tbody tr { border-bottom: 1px solid #e5e7eb; }
        .slip-table tbody tr.even { background-color: #f8f9fb; }
        .slip-table tbody tr.odd  { background-color: #ffffff; }
        .slip-table tbody tr:last-child { border-bottom: 2px solid #1a3a5c; }

        .slip-table td { padding: 5px 6px; vertical-align: middle; }

        .row-num  { color: #9ca3af; font-size: 7.5px; }
        .emp-name { font-weight: bold; color: #1f2937; }

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

        .b-approved    { background: #e8f5ee; color: #1a6b3a; border: 1px solid #9dd0b2; }
        .b-pending     { background: #fff3cd; color: #7a4f00; border: 1px solid #e8b84b; }
        .b-disapproved { background: #fde8e8; color: #8b1a1a; border: 1px solid #f5b8b8; }
        .b-official    { background: #e0f0ff; color: #1a4a7a; border: 1px solid #90c0e8; }
        .b-personal    { background: #f5f5f5; color: #6b7280; border: 1px solid #d1d5db; }

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
                <td class="logo-cell">
                    <img src="{{ public_path('images/ati_logo.png') }}" alt="ATI Logo">
                </td>
                <td class="title-cell">
                    <div class="org-name">Agricultural Training Institute</div>
                    <div class="org-branch">Regional Training Center XI</div>
                </td>
                <td class="logo-cell">
                    <img src="{{ public_path('images/bagong-pilipinas-logo.png') }}" alt="Bagong Pilipinas">
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
        <h1>Locator Slip Report</h1>
        <hr class="title-hr">
    </div>

    {{-- META BAR --}}
    @if($from && $to)
    <table class="meta-table">
        <tr>
            <td>
                <span class="ml">Status Filter</span>
                <span class="mv">{{ ($status === 'all' || !$status) ? 'All Slips' : ucfirst($status) }}</span>
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
        $total          = $locatorSlips->count();
        $approvedCnt    = $locatorSlips->where('status', 'approved')->count();
        $pendingCnt     = $locatorSlips->where('status', 'pending')->count();
        $disapprovedCnt = $locatorSlips->where('status', 'disapproved')->count();
        $officialCnt    = $locatorSlips->where('transaction_type', 'official')->count();
        $personalCnt    = $locatorSlips->where('transaction_type', 'personal')->count();
    @endphp

    {{-- SUMMARY --}}
    <div class="summary-block">
        @if($status === 'approved')
            Between <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> and
            <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>, a total of
            <strong>{{ $approvedCnt }}</strong> locator slip(s) were approved.
            This report details all approved locator slips processed during the selected period.
        @elseif($status === 'disapproved')
            Between <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> and
            <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>, a total of
            <strong>{{ $disapprovedCnt }}</strong> locator slip(s) were disapproved.
            This report details all disapproved locator slips processed during the selected period.
        @elseif($status === 'pending')
            Between <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> and
            <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>, a total of
            <strong>{{ $pendingCnt }}</strong> locator slip(s) are currently pending review.
            This report details all pending locator slips during the selected period.
        @else
            Between <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> and
            <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>, a total of
            <strong>{{ $total }}</strong> locator slip(s) were submitted.
            Of these, <strong>{{ $approvedCnt }}</strong> were approved,
            <strong>{{ $pendingCnt }}</strong> are pending, and
            <strong>{{ $disapprovedCnt }}</strong> were disapproved.
            The breakdown includes <strong>{{ $officialCnt }}</strong> official business and
            <strong>{{ $personalCnt }}</strong> personal transaction slips.
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
                <div class="s-num approved">{{ $approvedCnt }}</div>
                <div class="s-lbl">Approved</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num pending">{{ $pendingCnt }}</div>
                <div class="s-lbl">Pending</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num disapproved">{{ $disapprovedCnt }}</div>
                <div class="s-lbl">Disapproved</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num">{{ $officialCnt }}</div>
                <div class="s-lbl">Official</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num">{{ $personalCnt }}</div>
                <div class="s-lbl">Personal</div>
            </td>
        </tr>
    </table>

    {{-- SECTION HEADER --}}
    <table class="sec-hdr">
        <tr>
            <td class="sec-title">Locator Slip Records</td>
            <td class="sec-count">{{ $total }} record(s) found</td>
        </tr>
    </table>

    {{-- SLIP TABLE --}}
    @if($total > 0)
    <table class="slip-table">
        <thead>
            <tr>
                <th style="width:18px;">#</th>
                <th style="width:95px;">Employee</th>
                <th style="width:55px;">Type</th>
                <th style="width:55px;">Trip Date</th>
                <th>Destination</th>
                <th>Purpose</th>
                <th style="width:52px;">Status</th>
                <th style="width:75px;">Approved By</th>
                <th style="width:70px;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($locatorSlips as $index => $slip)
            <tr class="{{ $index % 2 === 0 ? 'even' : 'odd' }}">
                <td class="row-num">{{ $index + 1 }}</td>
                <td><span class="emp-name">{{ $slip->employee_name }}</span></td>
                <td>
                    @if($slip->transaction_type === 'official')
                        <span class="badge b-official">Official</span>
                    @else
                        <span class="badge b-personal">Personal</span>
                    @endif
                </td>
                <td>{{ $slip->inclusive_date?->format('M d, Y') ?? '—' }}</td>
                <td>{{ $slip->destination }}</td>
                <td>{{ $slip->purpose ?? '—' }}</td>
                <td>
                    @if($slip->status === 'approved')
                        <span class="badge b-approved">Approved</span>
                    @elseif($slip->status === 'pending')
                        <span class="badge b-pending">Pending</span>
                    @else
                        <span class="badge b-disapproved">Disapproved</span>
                    @endif
                </td>
                <td>{{ $slip->approved_by ?? '—' }}</td>
                <td>{{ $slip->admin_remarks ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        No locator slip records found for the selected filters and date range.
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
