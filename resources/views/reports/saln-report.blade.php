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
            font-size: 11px;
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

        /* ── Report header ───────────────────────────────────────────── */
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

        /* ── Report title ────────────────────────────────────────────── */
        .title-block { text-align: center; margin: 12px 0 12px; }

        .title-block h1 {
            font-size: 14px;
            font-weight: bold;
            color: #1a3a5c;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .title-hr {
            width: 240px;
            border: none;
            border-top: 1.5px solid #b8860b;
            margin: 5px auto 0;
        }

        /* ── Meta bar ────────────────────────────────────────────────── */
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

        /* ── Summary block ───────────────────────────────────────────── */
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

        /* ── Section header ──────────────────────────────────────────── */
        .sec-hdr { width: 100%; border-collapse: collapse; border-bottom: 2px solid #1a3a5c; margin-bottom: 7px; }
        .sec-hdr td { border: none; padding: 0 0 4px; vertical-align: bottom; }
        .sec-title { font-size: 12.5px; font-weight: bold; color: #1a3a5c; text-transform: uppercase; letter-spacing: 0.04em; }
        .sec-count { font-size: 10px; font-weight: bold; color: #6b7280; text-align: right; }

        /* ── SALN table ──────────────────────────────────────────────── */
        .saln-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            margin-bottom: 20px;
        }

        .saln-table thead tr { background-color: #1a3a5c; }

        .saln-table thead th {
            color: #ffffff;
            padding: 6px 6px;
            text-align: left;
            font-size: 8.5px;
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

        .saln-table td { padding: 6px 6px; vertical-align: middle; }
        .saln-table td.num-col { text-align: right; font-family: 'Courier New', Courier, monospace; }

        .row-num  { color: #9ca3af; font-size: 8.5px; }
        .emp-name { font-weight: bold; color: #1f2937; font-size: 10px; }
        .emp-pos  { font-size: 8px; color: #6b7280; margin-top: 1px; }

        .amt-assets  { color: #1a6b3a; font-weight: bold; font-size: 9.5px; }
        .amt-liab    { color: #8b1a1a; font-size: 9.5px; }
        .amt-nw-pos  { color: #1a6b3a; font-weight: bold; font-size: 10px; }
        .amt-nw-neg  { color: #8b1a1a; font-weight: bold; font-size: 10px; }

        /* ── Badges ──────────────────────────────────────────────────── */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 8px;
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
            padding: 7px 6px;
            border-top: 2px solid #0f2740;
            font-size: 9.5px;
        }

        .totals-row td.num-col { text-align: right; color: #7dd3a8; }

        /* ── No data ─────────────────────────────────────────────────── */
        .no-data {
            text-align: center;
            padding: 28px 20px;
            color: #6b7280;
            font-size: 11.5px;
            border: 1px dashed #d1d5db;
            margin-bottom: 20px;
        }

        /* ── Signatures ──────────────────────────────────────────────── */
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
        $total            = $salns->count();
        $withRemarks      = $salns->filter(fn($s) => !blank($s->remarks))->count();
        $noRemarks        = $total - $withRemarks;
        $grandAssets      = $salns->sum('total_assets');
        $grandLiabilities = $salns->sum('total_liabilities');
        $grandNetWorth    = $salns->sum('net_worth');
        $jointCnt         = $salns->where('joint_filing', true)->count();
        $separateCnt      = $salns->where('separate_filing', true)->count();
        $naCnt            = $salns->where('not_applicable', true)->count();
        $positiveNW       = $salns->filter(fn($s) => $s->net_worth >= 0)->count();
        $negativeNW       = $total - $positiveNW;
    @endphp

    {{-- SUMMARY --}}
    <div class="summary-block">
        @if($remarks_filter === 'with_remarks')
            This report presents the consolidated Statement of Assets, Liabilities and Net Worth (SALN) records of personnel under the Agricultural Training Institute — Regional Training Center XI (ATI-RTC XI) for the reference period of <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>. For the said period, a total of <strong>{{ $withRemarks }}</strong> SALN record(s) have been identified as carrying <strong>administrative remarks</strong> that require attention, review, or corrective action by the concerned officers. The filing and disclosure of assets, liabilities, and net worth by public officials and employees is mandated under Republic Act No. 6713 (Code of Conduct and Ethical Standards for Public Officials and Employees). This document is issued for official records management, audit compliance, and administrative reference purposes.
        @elseif($remarks_filter === 'no_remarks')
            This report presents the consolidated Statement of Assets, Liabilities and Net Worth (SALN) records of personnel under the Agricultural Training Institute — Regional Training Center XI (ATI-RTC XI) for the reference period of <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>. For the said period, a total of <strong>{{ $noRemarks }}</strong> SALN record(s) have been reviewed and found to carry <strong>no administrative remarks</strong>, indicating that these declarations are in order and require no further corrective action. The filing and disclosure of assets, liabilities, and net worth by public officials and employees is mandated under Republic Act No. 6713. This document is issued for official reference and compliance monitoring purposes.
        @else
            This report presents the consolidated Statement of Assets, Liabilities and Net Worth (SALN) records of personnel under the Agricultural Training Institute — Regional Training Center XI (ATI-RTC XI) for the reference period of <strong>{{ \Carbon\Carbon::parse($from)->format('F d, Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($to)->format('F d, Y') }}</strong>. During the said period, a total of <strong>{{ $total }}</strong> SALN(s) were filed and recorded in the Human Resource Management System in compliance with Republic Act No. 6713 (Code of Conduct and Ethical Standards for Public Officials and Employees). The aggregate declared total assets of all filers amount to <strong>₱{{ number_format($grandAssets, 2) }}</strong>, with combined total liabilities of <strong>₱{{ number_format($grandLiabilities, 2) }}</strong>, yielding an aggregate net worth of <strong>₱{{ number_format($grandNetWorth, 2) }}</strong>. Of the total submissions, <strong>{{ $withRemarks }}</strong> record(s) carry administrative remarks requiring further action, while <strong>{{ $noRemarks }}</strong> have been cleared. Filing types include <strong>{{ $jointCnt }}</strong> joint, <strong>{{ $separateCnt }}</strong> separate, and <strong>{{ $naCnt }}</strong> not-applicable declarations. This document is prepared for official compliance monitoring, audit reference, and administrative records management purposes.
        @endif
    </div>

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
                <th style="width:18px;">#</th>
                <th>Employee</th>
                <th style="width:62px;">As of Date</th>
                <th style="width:50px;">Filing Type</th>
                <th style="width:88px;" class="num-col">Total Assets</th>
                <th style="width:88px;" class="num-col">Total Liabilities</th>
                <th style="width:88px;" class="num-col">Net Worth</th>
                <th style="width:60px;">Remarks</th>
                <th style="width:65px;">Date Filed</th>
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
                <td colspan="4" style="color:#fff; font-size:9px; font-weight:bold; text-align:right; padding-right:8px;">
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
