<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Travel Order Report</title>
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

        .s-num              { font-size: 20px; font-weight: bold; color: #1a3a5c; line-height: 1; }
        .s-num.approved     { color: #1a6b3a; }
        .s-num.recommended  { color: #1a4a7a; }
        .s-num.pending      { color: #7a4f00; }
        .s-num.rejected     { color: #8b1a1a; }

        .s-lbl { font-size: 7.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-top: 3px; }

        /* ── Section header ──────────────────────────────────────────── */
        .sec-hdr { width: 100%; border-collapse: collapse; border-bottom: 2px solid #1a3a5c; margin-bottom: 6px; }
        .sec-hdr td { border: none; padding: 0 0 3px; vertical-align: bottom; }
        .sec-title { font-size: 11px; font-weight: bold; color: #1a3a5c; text-transform: uppercase; letter-spacing: 0.04em; }
        .sec-count { font-size: 8.5px; font-weight: bold; color: #6b7280; text-align: right; }

        /* ── Travel order table ──────────────────────────────────────── */
        .to-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-bottom: 20px;
        }

        .to-table thead tr { background-color: #1a3a5c; }

        .to-table thead th {
            color: #ffffff;
            padding: 5px 6px;
            text-align: left;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }

        .to-table tbody tr { border-bottom: 1px solid #e5e7eb; }
        .to-table tbody tr.even { background-color: #f8f9fb; }
        .to-table tbody tr.odd  { background-color: #ffffff; }
        .to-table tbody tr:last-child { border-bottom: 2px solid #1a3a5c; }

        .to-table td { padding: 5px 6px; vertical-align: middle; }

        .row-num   { color: #9ca3af; font-size: 7.5px; }
        .order-no  { font-family: 'Courier New', Courier, monospace; font-size: 8px; color: #2c5f8a; font-weight: bold; }
        .emp-name  { font-weight: bold; color: #1f2937; }

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

        .b-draft       { background: #f3f4f6; color: #374151;  border: 1px solid #d1d5db; }
        .b-pending     { background: #fff3cd; color: #7a4f00;  border: 1px solid #e8b84b; }
        .b-recommended { background: #e0f0ff; color: #1a4a7a;  border: 1px solid #90c0e8; }
        .b-approved    { background: #e8f5ee; color: #1a6b3a;  border: 1px solid #9dd0b2; }
        .b-rejected    { background: #fde8e8; color: #8b1a1a;  border: 1px solid #f5b8b8; }
        .b-solo        { background: #f0f4ff; color: #1a3a7a;  border: 1px solid #a0b4e8; }
        .b-batch       { background: #e8f5ee; color: #1a5c3a;  border: 1px solid #80c9a0; }

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
        <h1>Travel Order Report</h1>
        <hr class="title-hr">
    </div>

    {{-- META BAR --}}
    @if($from && $to)
    <table class="meta-table">
        <tr>
            <td>
                <span class="ml">Status Filter</span>
                <span class="mv">{{ ($status === 'all' || !$status) ? 'All Orders' : ucfirst($status) }}</span>
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
        $total          = $travelOrders->count();
        $approvedCnt    = $travelOrders->where('status', 'approved')->count();
        $recommendedCnt = $travelOrders->where('status', 'recommended')->count();
        $pendingCnt     = $travelOrders->where('status', 'pending')->count();
        $rejectedCnt    = $travelOrders->where('status', 'rejected')->count();
        $soloCnt        = $travelOrders->where('travel_type', 'solo')->count();
        $batchCnt       = $travelOrders->where('travel_type', 'batch')->count();
    @endphp

    {{-- SUMMARY --}}
    <div class="summary-block">
        @if($status === 'approved')
            Between <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> and
            <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>, a total of
            <strong>{{ $approvedCnt }}</strong> travel order(s) were approved.
            This report provides details of all approved travel orders during the selected period.
        @elseif($status === 'recommended')
            Between <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> and
            <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>, a total of
            <strong>{{ $recommendedCnt }}</strong> travel order(s) have been recommended by the Assistant Director.
            This report provides details of all recommended travel orders during the selected period.
        @elseif($status === 'rejected')
            Between <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> and
            <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>, a total of
            <strong>{{ $rejectedCnt }}</strong> travel order(s) were rejected.
            This report provides details of all rejected travel orders during the selected period.
        @elseif($status === 'pending')
            Between <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> and
            <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>, a total of
            <strong>{{ $pendingCnt }}</strong> travel order(s) are currently pending review.
            This report provides details of all pending travel orders during the selected period.
        @else
            Between <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> and
            <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>, a total of
            <strong>{{ $total }}</strong> travel order(s) were submitted.
            Of these, <strong>{{ $approvedCnt }}</strong> were approved,
            <strong>{{ $recommendedCnt }}</strong> were recommended,
            <strong>{{ $pendingCnt }}</strong> are pending, and
            <strong>{{ $rejectedCnt }}</strong> were rejected.
            The breakdown includes <strong>{{ $soloCnt }}</strong> solo and
            <strong>{{ $batchCnt }}</strong> batch travel orders.
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
                <div class="s-num recommended">{{ $recommendedCnt }}</div>
                <div class="s-lbl">Recommended</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num pending">{{ $pendingCnt }}</div>
                <div class="s-lbl">Pending</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num rejected">{{ $rejectedCnt }}</div>
                <div class="s-lbl">Rejected</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num">{{ $soloCnt }}</div>
                <div class="s-lbl">Solo</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num">{{ $batchCnt }}</div>
                <div class="s-lbl">Batch</div>
            </td>
        </tr>
    </table>

    {{-- SECTION HEADER --}}
    <table class="sec-hdr">
        <tr>
            <td class="sec-title">Travel Order Records</td>
            <td class="sec-count">{{ $total }} record(s) found</td>
        </tr>
    </table>

    {{-- TRAVEL ORDER TABLE --}}
    @if($total > 0)
    <table class="to-table">
        <thead>
            <tr>
                <th style="width:18px;">#</th>
                <th style="width:64px;">Order No.</th>
                <th style="width:48px;">Type</th>
                <th>Traveler(s)</th>
                <th>Destination</th>
                <th style="width:56px;">Departure</th>
                <th style="width:56px;">Return</th>
                <th style="width:58px;">Status</th>
                <th style="width:75px;">Recommended By</th>
                <th style="width:75px;">Approved By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($travelOrders as $index => $order)
            @php
                // Resolve traveler name(s)
                if ($order->travel_type === 'batch' && !empty($order->employee_ids)) {
                    $names = \App\Models\User::whereIn('id', $order->employee_ids)->pluck('name')->toArray();
                    $nameDisplay = count($names) <= 2
                        ? implode(', ', $names)
                        : implode(', ', array_slice($names, 0, 2)) . ' +' . (count($names) - 2) . ' more';
                } else {
                    $nameDisplay = $order->name ?? $order->solo_employee ?? '—';
                }
            @endphp
            <tr class="{{ $index % 2 === 0 ? 'even' : 'odd' }}">
                <td class="row-num">{{ $index + 1 }}</td>
                <td><span class="order-no">{{ $order->travel_order_no ?? '—' }}</span></td>
                <td>
                    @if($order->travel_type === 'batch')
                        <span class="badge b-batch">Batch</span>
                    @else
                        <span class="badge b-solo">Solo</span>
                    @endif
                </td>
                <td><span class="emp-name">{{ $nameDisplay }}</span></td>
                <td>{{ $order->destination ?? '—' }}</td>
                <td>{{ $order->departure_date?->format('M d, Y') ?? '—' }}</td>
                <td>{{ $order->return_date?->format('M d, Y') ?? '—' }}</td>
                <td>
                    @if($order->status === 'approved')
                        <span class="badge b-approved">Approved</span>
                    @elseif($order->status === 'recommended')
                        <span class="badge b-recommended">Recommended</span>
                    @elseif($order->status === 'pending')
                        <span class="badge b-pending">Pending</span>
                    @elseif($order->status === 'rejected')
                        <span class="badge b-rejected">Rejected</span>
                    @else
                        <span class="badge b-draft">{{ ucfirst($order->status) }}</span>
                    @endif
                </td>
                <td>{{ $order->recommender?->name ?? '—' }}</td>
                <td>{{ $order->approver?->name ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        No travel order records found for the selected filters and date range.
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
