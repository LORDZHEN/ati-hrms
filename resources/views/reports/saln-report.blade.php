<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>SALN Report</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
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
            font-size: 13px;
            font-weight: bold;
            color: #1a3a5c;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .title-hr {
            width: 200px;
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
            font-size: 9px;
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

        .s-num          { font-size: 18px; font-weight: bold; color: #1a3a5c; line-height: 1; }
        .s-num.positive { color: #1a6b3a; }
        .s-num.negative { color: #8b1a1a; }
        .s-num.remarks  { color: #7a4f00; }

        .s-lbl { font-size: 7px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-top: 3px; }
        .s-sub { font-size: 7px; color: #9ca3af; margin-top: 1px; }

        /* ── Section header ──────────────────────────────────────────── */
        .sec-hdr { width: 100%; border-collapse: collapse; border-bottom: 2px solid #1a3a5c; margin-bottom: 6px; }
        .sec-hdr td { border: none; padding: 0 0 3px; vertical-align: bottom; }
        .sec-title { font-size: 11px; font-weight: bold; color: #1a3a5c; text-transform: uppercase; letter-spacing: 0.04em; }
        .sec-count { font-size: 8.5px; font-weight: bold; color: #6b7280; text-align: right; }

        /* ── SALN table ──────────────────────────────────────────────── */
        .saln-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            margin-bottom: 20px;
        }

        .saln-table thead tr { background-color: #1a3a5c; }

        .saln-table thead th {
            color: #ffffff;
            padding: 5px 5px;
            text-align: left;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }

        .saln-table thead th.num-col { text-align: right; }

        .saln-table tbody tr { border-bottom: 1px solid #e5e7eb; }
        .saln-table tbody tr.even { background-color: #f8f9fb; }
        .saln-table tbody tr.odd  { background-color: #ffffff; }
        .saln-table tbody tr:last-child { border-bottom: 2px solid #1a3a5c; }

        .saln-table td { padding: 5px 5px; vertical-align: middle; }
        .saln-table td.num-col { text-align: right; font-family: 'Courier New', Courier, monospace; }

        .row-num  { color: #9ca3af; font-size: 7px; }
        .emp-name { font-weight: bold; color: #1f2937; font-size: 8.5px; }
        .emp-pos  { font-size: 7px; color: #6b7280; margin-top: 1px; }

        .amt-assets  { color: #1a6b3a; font-weight: bold; font-size: 8px; }
        .amt-liab    { color: #8b1a1a; font-size: 8px; }
        .amt-nw-pos  { color: #1a6b3a; font-weight: bold; font-size: 8.5px; }
        .amt-nw-neg  { color: #8b1a1a; font-weight: bold; font-size: 8.5px; }

        /* ── Badges ──────────────────────────────────────────────────── */
        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 8px;
            font-size: 6.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            white-space: nowrap;
        }

        .b-remarks    { background: #fff3cd; color: #7a4f00; border: 1px solid #e8b84b; }
        .b-no-remarks { background: #e8f5ee; color: #1a6b3a; border: 1px solid #9dd0b2; }
        .b-joint      { background: #e0f0ff; color: #1a4a7a; border: 1px solid #90c0e8; }
        .b-separate   { background: #f0f4ff; color: #1a3a7a; border: 1px solid #a0b4e8; }
        .b-na         { background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }

        /* ── Totals row ──────────────────────────────────────────────── */
        .totals-row td {
            background: #1a3a5c !important;
            color: #ffffff;
            font-weight: bold;
            padding: 6px 5px;
            border-top: 2px solid #0f2740;
        }

        .totals-row td.num-col { text-align: right; color: #7dd3a8; }

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
        <h1>Statement of Assets, Liabilities and Net Worth Report</h1>
        <hr class="title-hr">
    </div>

    {{-- META BAR --}}
    @if($from && $to)
    <table class="meta-table">
        <tr>
            <td>
                <span class="ml">Remarks Filter</span>
                <span class="mv">
                    @if($remarks_filter === 'with_remarks') With Remarks
                    @elseif($remarks_filter === 'no_remarks') Without Remarks
                    @else All Records
                    @endif
                </span>
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

    {{-- AGGREGATE COMPUTATIONS --}}
    @php
        $total          = $salns->count();
        $withRemarks    = $salns->filter(fn($s) => !blank($s->remarks))->count();
        $noRemarks      = $total - $withRemarks;

        $grandAssets      = $salns->sum('total_assets');
        $grandLiabilities = $salns->sum('total_liabilities');
        $grandNetWorth    = $salns->sum('net_worth');

        $jointCnt    = $salns->where('joint_filing', true)->count();
        $separateCnt = $salns->where('separate_filing', true)->count();
        $naCnt       = $salns->where('not_applicable', true)->count();

        $avgNetWorth = $total > 0 ? $salns->avg('net_worth') : 0;
        $positiveNW  = $salns->filter(fn($s) => $s->net_worth >= 0)->count();
        $negativeNW  = $total - $positiveNW;
    @endphp

    {{-- SUMMARY --}}
    <div class="summary-block">
        @if($remarks_filter === 'with_remarks')
            Between <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> and
            <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>,
            <strong>{{ $withRemarks }}</strong> SALN record(s) have administrative remarks requiring attention.
            This report covers only those records with remarks during the selected period.
        @elseif($remarks_filter === 'no_remarks')
            Between <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> and
            <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>,
            <strong>{{ $noRemarks }}</strong> SALN record(s) have been filed without any administrative remarks.
            This report covers only cleared records during the selected period.
        @else
            Between <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> and
            <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>, a total of
            <strong>{{ $total }}</strong> SALN(s) were filed.
            The combined total assets amount to <strong>₱{{ number_format($grandAssets, 2) }}</strong>,
            with total liabilities of <strong>₱{{ number_format($grandLiabilities, 2) }}</strong>,
            yielding a combined net worth of <strong>₱{{ number_format($grandNetWorth, 2) }}</strong>.
            Of these, <strong>{{ $withRemarks }}</strong> record(s) have administrative remarks.
        @endif
    </div>

    {{-- STATS --}}
    <table class="stats-table">
        <tr>
            <td>
                <div class="s-num">{{ $total }}</div>
                <div class="s-lbl">Total Filed</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num positive">₱{{ number_format($grandAssets / 1000, 1) }}K</div>
                <div class="s-lbl">Total Assets</div>
                <div class="s-sub">₱{{ number_format($grandAssets, 2) }}</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num negative">₱{{ number_format($grandLiabilities / 1000, 1) }}K</div>
                <div class="s-lbl">Total Liabilities</div>
                <div class="s-sub">₱{{ number_format($grandLiabilities, 2) }}</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num {{ $grandNetWorth >= 0 ? 'positive' : 'negative' }}">₱{{ number_format($grandNetWorth / 1000, 1) }}K</div>
                <div class="s-lbl">Combined Net Worth</div>
                <div class="s-sub">₱{{ number_format($grandNetWorth, 2) }}</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num remarks">{{ $withRemarks }}</div>
                <div class="s-lbl">With Remarks</div>
            </td>
            <td class="gap"></td>
            <td>
                <div class="s-num positive">{{ $noRemarks }}</div>
                <div class="s-lbl">Cleared</div>
            </td>
        </tr>
    </table>

    {{-- SECTION HEADER --}}
    <table class="sec-hdr">
        <tr>
            <td class="sec-title">SALN Records</td>
            <td class="sec-count">{{ $total }} record(s) found</td>
        </tr>
    </table>

    {{-- SALN TABLE --}}
    @if($total > 0)
    <table class="saln-table">
        <thead>
            <tr>
                <th style="width:16px;">#</th>
                <th>Employee</th>
                <th style="width:55px;">As of Date</th>
                <th style="width:45px;">Filing Type</th>
                <th style="width:80px;" class="num-col">Total Assets</th>
                <th style="width:80px;" class="num-col">Total Liabilities</th>
                <th style="width:80px;" class="num-col">Net Worth</th>
                <th style="width:55px;">Remarks</th>
                <th style="width:60px;">Date Filed</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salns as $index => $saln)
            @php
                $filingType = $saln->joint_filing
                    ? 'joint'
                    : ($saln->separate_filing ? 'separate' : 'n/a');
                $nwClass = $saln->net_worth >= 0 ? 'amt-nw-pos' : 'amt-nw-neg';
            @endphp
            <tr class="{{ $index % 2 === 0 ? 'even' : 'odd' }}">
                <td class="row-num">{{ $index + 1 }}</td>
                <td>
                    <div class="emp-name">{{ $saln->user->first_name }} {{ $saln->user->last_name }}</div>
                    @if($saln->declarant_position)
                        <div class="emp-pos">{{ $saln->declarant_position }}</div>
                    @elseif($saln->user->position)
                        <div class="emp-pos">{{ $saln->user->position }}</div>
                    @endif
                </td>
                <td>{{ $saln->as_of_date?->format('M d, Y') ?? '—' }}</td>
                <td>
                    @if($filingType === 'joint')
                        <span class="badge b-joint">Joint</span>
                    @elseif($filingType === 'separate')
                        <span class="badge b-separate">Separate</span>
                    @else
                        <span class="badge b-na">N/A</span>
                    @endif
                </td>
                <td class="num-col amt-assets">₱{{ number_format($saln->total_assets ?? 0, 2) }}</td>
                <td class="num-col amt-liab">₱{{ number_format($saln->total_liabilities ?? 0, 2) }}</td>
                <td class="num-col {{ $nwClass }}">₱{{ number_format($saln->net_worth ?? 0, 2) }}</td>
                <td>
                    @if(!blank($saln->remarks))
                        <span class="badge b-remarks">Has Remarks</span>
                    @else
                        <span class="badge b-no-remarks">Cleared</span>
                    @endif
                </td>
                <td>{{ $saln->created_at?->format('M d, Y') ?? '—' }}</td>
            </tr>
            @endforeach

            {{-- Totals row --}}
            <tr class="totals-row">
                <td colspan="4" style="color:#fff; font-size:8px; font-weight:bold; text-align:right; padding-right:8px;">
                    TOTALS ({{ $total }} records)
                </td>
                <td class="num-col">₱{{ number_format($grandAssets, 2) }}</td>
                <td class="num-col">₱{{ number_format($grandLiabilities, 2) }}</td>
                <td class="num-col" style="color:{{ $grandNetWorth >= 0 ? '#7dd3a8' : '#fca5a5' }};">
                    ₱{{ number_format($grandNetWorth, 2) }}
                </td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
    @else
    <div class="no-data">
        No SALN records found for the selected filters and date range.
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
