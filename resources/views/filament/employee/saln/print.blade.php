<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SALN - {{ $saln->declarant_family_name }}, {{ $saln->declarant_first_name }}</title>
    <style>
        @page {
            size: 8.5in 13in;
            margin: 0.5in 0.65in;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #000;
        }

        /* ── PRINT BUTTON ── */
        .print-button {
            position: fixed; top: 15px; right: 15px;
            padding: 10px 20px; background: #1e40af; color: #fff;
            border: none; border-radius: 6px; cursor: pointer;
            font-size: 13px; font-weight: 600; z-index: 1000;
        }
        .print-button:hover { background: #1e3a8a; }

        /* ── HEADER ── */
        .header-meta {
            text-align: right;
            font-size: 7pt;
            line-height: 1.4;
            margin-bottom: 4px;
        }
        .page-num-top {
            text-align: right;
            font-size: 8pt;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .form-title {
            font-weight: bold;
            font-size: 13pt;
            text-align: center;
            text-decoration: underline;
            letter-spacing: 0.3px;
            margin: 4px 0;
        }
        .as-of-line {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            border-bottom: 1.5px solid #000;
            width: 320px;
            margin: 4px auto 2px;
            padding-bottom: 2px;
        }
        .required-by {
            text-align: center;
            font-size: 8pt;
            margin-bottom: 6px;
        }

        /* ── FILING TYPE ── */
        .filing-note {
            font-size: 8pt;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .filing-checkboxes {
            font-size: 9pt;
            margin-bottom: 8px;
        }
        .chkbox {
            display: inline-block;
            width: 11px; height: 11px;
            border: 1.5px solid #000;
            text-align: center; vertical-align: middle;
            font-size: 9pt; line-height: 10px;
            font-weight: bold; margin-right: 3px;
        }
        .chkbox.checked { background: #000; color: #fff; }

        /* ── PERSONAL INFO ── */
        .pinfo { margin: 6px 0; font-size: 8.5pt; }
        .pinfo-row {
            display: flex;
            margin-bottom: 4px;
            align-items: flex-end;
            gap: 6px;
        }
        .pinfo-label {
            font-weight: bold;
            font-size: 8pt;
            white-space: nowrap;
            padding-bottom: 2px;
        }
        .pinfo-val {
            border-bottom: 1px solid #000;
            flex-grow: 1;
            min-height: 18px;
            padding: 1px 4px 1px;
            font-size: 9pt;
            position: relative;
        }
        .name-hint {
            display: flex;
            font-size: 6pt;
            font-style: italic;
            position: absolute;
            bottom: -11px; left: 0; right: 0;
        }
        .name-hint span { flex: 1; text-align: center; }
        .spacer-w { width: 105px; flex-shrink: 0; }

        /* ── SECTION HEADERS ── */
        .sec-hdr {
            font-weight: bold;
            text-align: center;
            text-decoration: underline;
            font-size: 9.5pt;
            margin: 8px 0 3px;
        }
        .sec-sub {
            text-align: center;
            font-style: italic;
            font-size: 8pt;
            margin-bottom: 4px;
        }

        /* ── TABLES ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 3px 0;
            font-size: 8pt;
        }
        th, td {
            border: 1px solid #000;
            padding: 2px 4px;
            text-align: center;
            vertical-align: middle;
            font-size: 8pt;
            line-height: 1.25;
        }
        th {
            background: #e8e8e8;
            font-weight: bold;
            font-size: 7.5pt;
        }
        td.la { text-align: left; }
        tr.dr { height: 20px; }

        /* ── SUBTOTAL / TOTAL ROWS ── */
        tr.sub-r { background: #f0f0f0; font-weight: bold; }
        .total-assets-bar {
            text-align: right;
            font-weight: bold;
            font-size: 9.5pt;
            border-top: 2px solid #000;
            border-bottom: 1.5px solid #000;
            padding: 3px 4px;
            margin: 4px 0;
        }
        .net-worth-box {
            border: 2px solid #000;
            padding: 6px 10px;
            margin: 6px 0;
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
            background: #f5f5f5;
        }

        /* ── ASSET SUBSECTION ── */
        .sub-hdr { font-size: 9pt; font-weight: bold; }
        .sub-desc { font-size: 7.5pt; font-style: italic; }

        /* ── CERTIFICATION ── */
        .cert {
            font-size: 8.5pt;
            text-align: justify;
            line-height: 1.4;
            margin: 5px 0;
        }

        /* ── DATE LINE ── */
        .date-line {
            font-size: 8.5pt;
            margin: 6px 0 8px;
        }
        .underline-field {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 180px;
            padding: 1px 4px;
            vertical-align: bottom;
        }

        /* ── SIGNATURE SECTION ── */
        .sig-wrap {
            display: flex;
            justify-content: space-between;
            gap: 30px;
            margin-top: 8px;
        }
        .sig-box { width: 48%; }
        .sig-line {
            border-bottom: 1.5px solid #000;
            height: 28px;
            margin-bottom: 3px;
        }
        .sig-label {
            text-align: center;
            font-size: 8pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .id-block { font-size: 8pt; margin-top: 4px; }
        .id-field {
            border-bottom: 1px solid #000;
            min-height: 14px;
            padding: 1px 3px;
            margin: 1px 0 4px;
        }

        /* ── OATH ── */
        .oath-wrap { margin-top: 8px; }
        .oath-text {
            text-align: center;
            text-decoration: underline;
            font-weight: bold;
            font-size: 8.5pt;
            line-height: 1.5;
        }
        .oath-sig { text-align: center; margin-top: 12px; }
        .oath-sig-line {
            border-top: 1.5px solid #000;
            width: 260px;
            margin: 0 auto 4px;
        }
        .oath-sig-label { font-weight: bold; font-size: 8pt; }

        /* ── NOTES ── */
        .notes {
            border-top: 2px solid #000;
            padding-top: 5px;
            font-size: 7pt;
            line-height: 1.3;
            margin-top: 8px;
        }
        .notes p { margin: 3px 0; }

        /* ── PAGE BREAK ── */
        .page-break { page-break-before: always; }
        .no-break { page-break-inside: avoid; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<button class="print-button no-print" onclick="window.print()">🖨️ Print SALN</button>

{{-- ══════════════════════════════════════════
     PAGE 1
══════════════════════════════════════════ --}}

<div class="no-break">
    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
        <div style="font-size:7pt; line-height:1.4;">
            Revised as of January 2015<br>
            Per CSC Resolution No. 1500088<br>
            Promulgated on January 23, 2015
        </div>
        <div style="text-align:right; font-size:8pt; font-weight:bold;">
            Page 1 of 2
        </div>
    </div>

    <div class="form-title">SWORN STATEMENT OF ASSETS, LIABILITIES AND NET WORTH</div>
    <div class="as-of-line">
        As of {{ $saln->as_of_date ? \Carbon\Carbon::parse($saln->as_of_date)->format('F d, Y') : '____________________' }}
    </div>
    <div class="required-by">(Required by R.A. 6713)</div>
</div>

{{-- FILING TYPE --}}
<div class="no-break">
    <div class="filing-note">Note: Husband and wife who are both public officials and employees may file the required statements jointly or separately.</div>
    <div class="filing-checkboxes">
        <span class="chkbox {{ $saln->joint_filing    ? 'checked' : '' }}">{{ $saln->joint_filing    ? '✓' : '' }}</span> Joint Filing &nbsp;&nbsp;&nbsp;
        <span class="chkbox {{ $saln->separate_filing ? 'checked' : '' }}">{{ $saln->separate_filing ? '✓' : '' }}</span> Separate Filing &nbsp;&nbsp;&nbsp;
        <span class="chkbox {{ $saln->not_applicable  ? 'checked' : '' }}">{{ $saln->not_applicable  ? '✓' : '' }}</span> Not Applicable
    </div>
</div>

{{-- PERSONAL INFORMATION --}}
<div class="pinfo no-break">
    {{-- Declarant row --}}
    <div class="pinfo-row">
        <span class="pinfo-label" style="width:90px; flex-shrink:0;">DECLARANT:</span>
        <div class="pinfo-val" style="position:relative; padding-bottom:12px;">
            {{ $saln->declarant_family_name }}, {{ $saln->declarant_first_name }} {{ $saln->declarant_middle_initial }}
            <div class="name-hint">
                <span>(Family Name)</span><span>(First Name)</span><span>(M.I.)</span>
            </div>
        </div>
        <span class="pinfo-label" style="width:70px; flex-shrink:0;">POSITION:</span>
        <div class="pinfo-val">{{ $saln->declarant_position ?? '' }}</div>
    </div>

    {{-- Address / Agency --}}
    <div class="pinfo-row" style="margin-top:12px;">
        <span class="pinfo-label" style="width:90px; flex-shrink:0;">ADDRESS:</span>
        <div class="pinfo-val">{{ $saln->user->full_address ?? '' }}</div>
        <span class="pinfo-label" style="width:100px; flex-shrink:0;">AGENCY/OFFICE:</span>
        <div class="pinfo-val">{{ $saln->declarant_agency_office ?? '' }}</div>
    </div>

    {{-- Office Address (right side only) --}}
    <div class="pinfo-row">
        <div style="width:90px; flex-shrink:0;"></div>
        <div style="flex-grow:1;"></div>
        <span class="pinfo-label" style="width:100px; flex-shrink:0;">OFFICE ADDRESS:</span>
        <div class="pinfo-val">{{ $saln->declarant_office_address ?? '' }}</div>
    </div>

    <div style="height:8px;"></div>

    {{-- Spouse row --}}
    <div class="pinfo-row">
        <span class="pinfo-label" style="width:90px; flex-shrink:0;">SPOUSE:</span>
        <div class="pinfo-val" style="position:relative; padding-bottom:12px;">
            {{ $saln->spouse_family_name ? $saln->spouse_family_name.', '.$saln->spouse_first_name.' '.$saln->spouse_middle_initial : 'N/A' }}
            <div class="name-hint">
                <span>(Family Name)</span><span>(First Name)</span><span>(M.I.)</span>
            </div>
        </div>
        <span class="pinfo-label" style="width:70px; flex-shrink:0;">POSITION:</span>
        <div class="pinfo-val">{{ $saln->spouse_position ?? '' }}</div>
    </div>

    {{-- Spouse Agency / Office Address --}}
    <div class="pinfo-row" style="margin-top:12px;">
        <div style="width:90px; flex-shrink:0;"></div>
        <div style="flex-grow:1;"></div>
        <span class="pinfo-label" style="width:100px; flex-shrink:0;">AGENCY/OFFICE:</span>
        <div class="pinfo-val">{{ $saln->spouse_agency_office ?? '' }}</div>
    </div>
    <div class="pinfo-row">
        <div style="width:90px; flex-shrink:0;"></div>
        <div style="flex-grow:1;"></div>
        <span class="pinfo-label" style="width:100px; flex-shrink:0;">OFFICE ADDRESS:</span>
        <div class="pinfo-val">{{ $saln->spouse_office_address ?? '' }}</div>
    </div>
</div>

{{-- CHILDREN --}}
<div class="sec-hdr">UNMARRIED CHILDREN BELOW EIGHTEEN (18) YEARS OF AGE LIVING IN DECLARANT'S HOUSEHOLD</div>

<table class="no-break">
    <thead>
        <tr>
            <th style="width:60%;">NAME</th>
            <th style="width:25%;">DATE OF BIRTH</th>
            <th style="width:15%;">AGE</th>
        </tr>
    </thead>
    <tbody>
        @php $minChild = 3; $childCount = $saln->children->count(); @endphp
        @forelse($saln->children as $c)
            <tr class="dr"><td class="la">{{ $c->name }}</td><td>{{ \Carbon\Carbon::parse($c->date_of_birth)->format('m/d/Y') }}</td><td>{{ $c->age }}</td></tr>
        @empty
            @for($i=0;$i<$minChild;$i++)<tr class="dr"><td></td><td></td><td></td></tr>@endfor
        @endforelse
        @if($childCount > 0 && $childCount < $minChild)
            @for($i=$childCount;$i<$minChild;$i++)<tr class="dr"><td></td><td></td><td></td></tr>@endfor
        @endif
    </tbody>
</table>

{{-- ASSETS --}}
<div class="sec-hdr">ASSETS, LIABILITIES AND NETWORTH</div>
<div class="sec-sub">(Including those of the spouse and unmarried children below eighteen (18) years of age living in declarant's household)</div>

<div style="margin-top:4px;">
    <strong style="font-size:9.5pt;">1. ASSETS</strong>

    {{-- Real Properties --}}
    <div style="margin:5px 0 3px;">
        <span class="sub-hdr">a. Real Properties*</span>
        <span class="sub-desc">&nbsp;(Land, Buildings, and other Real Estate)</span>
    </div>

    <table class="no-break">
        <thead>
            <tr>
                <th rowspan="2" style="width:14%;">DESCRIPTION<br><span style="font-weight:normal; font-size:6.5pt; font-style:italic;">(e.g. lot, house and lot, condominium and improvements)</span></th>
                <th rowspan="2" style="width:9%;">KIND<br><span style="font-weight:normal; font-size:6.5pt; font-style:italic;">(e.g. residential, commercial, industrial, agricultural and mixed use)</span></th>
                <th rowspan="2" style="width:15%;">EXACT LOCATION</th>
                <th rowspan="2" style="width:12%;">ASSESSED VALUE<br><span style="font-weight:normal; font-size:6.5pt; font-style:italic;">(As found in the Tax Declaration of Real Property)</span></th>
                <th rowspan="2" style="width:13%;">CURRENT FAIR<br>MARKET VALUE</th>
                <th colspan="3">ACQUISITION</th>
            </tr>
            <tr>
                <th style="width:9%;">YEAR</th>
                <th style="width:14%;">MODE</th>
                <th style="width:14%;">COST</th>
            </tr>
        </thead>
        <tbody>
            @php $rpCount = $saln->realProperties->count(); $minRP = 2; $rpTotal = $saln->realProperties->sum('current_fair_market_value'); @endphp
            @forelse($saln->realProperties as $p)
                <tr class="dr">
                    <td class="la">{{ $p->description }}</td>
                    <td>{{ $p->kind }}</td>
                    <td class="la">{{ $p->exact_location }}</td>
                    <td>₱{{ number_format($p->assessed_value,2) }}</td>
                    <td>₱{{ number_format($p->current_fair_market_value,2) }}</td>
                    <td>{{ $p->acquisition_year }}</td>
                    <td>{{ $p->mode_of_acquisition }}</td>
                    <td>₱{{ number_format($p->acquisition_cost,2) }}</td>
                </tr>
            @empty
                @for($i=0;$i<$minRP;$i++)<tr class="dr"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>@endfor
            @endforelse
            @if($rpCount>0 && $rpCount<$minRP)
                @for($i=$rpCount;$i<$minRP;$i++)<tr class="dr"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>@endfor
            @endif
            <tr class="sub-r">
                <td colspan="4" style="text-align:right;">Subtotal:</td>
                <td>₱{{ number_format($rpTotal,2) }}</td>
                <td colspan="3"></td>
            </tr>
        </tbody>
    </table>

    {{-- Personal Properties --}}
    <div style="margin:6px 0 3px;">
        <span class="sub-hdr">b. Personal Properties*</span>
        <span class="sub-desc">&nbsp;(Vehicles, Jewelry, Cash, Bank Deposits, etc.)</span>
    </div>

    <table class="no-break">
        <thead>
            <tr>
                <th style="width:60%;">DESCRIPTION</th>
                <th style="width:20%;">YEAR ACQUIRED</th>
                <th style="width:20%;">ACQUISITION COST/AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @php $ppCount = $saln->personalProperties->count(); $minPP = 3; $ppTotal = $saln->personalProperties->sum('acquisition_cost'); @endphp
            @forelse($saln->personalProperties as $p)
                <tr class="dr"><td class="la">{{ $p->description }}</td><td>{{ $p->year_acquired }}</td><td>₱{{ number_format($p->acquisition_cost,2) }}</td></tr>
            @empty
                @for($i=0;$i<$minPP;$i++)<tr class="dr"><td></td><td></td><td></td></tr>@endfor
            @endforelse
            @if($ppCount>0 && $ppCount<$minPP)
                @for($i=$ppCount;$i<$minPP;$i++)<tr class="dr"><td></td><td></td><td></td></tr>@endfor
            @endif
            <tr class="sub-r">
                <td colspan="2" style="text-align:right;">Subtotal:</td>
                <td>₱{{ number_format($ppTotal,2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-assets-bar">TOTAL ASSETS (a+b):&nbsp;&nbsp; ₱{{ number_format($saln->total_assets,2) }}</div>
    <div style="font-size:7pt; font-style:italic;">* Additional sheet/s may be used, if necessary.</div>
</div>


{{-- ══════════════════════════════════════════
     PAGE 2
══════════════════════════════════════════ --}}
<div class="page-break"></div>

<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:4px;">
    <div style="font-size:7pt; line-height:1.4;">
        Revised as of January 2015<br>
        Per CSC Resolution No. 1500088<br>
        Promulgated on January 23, 2015
    </div>
    <div style="text-align:right; font-size:8pt; font-weight:bold;">Page 2 of 2</div>
</div>

<div class="form-title" style="font-size:12pt; margin-bottom:4px;">SWORN STATEMENT OF ASSETS, LIABILITIES AND NET WORTH</div>
<div class="as-of-line" style="font-size:9.5pt; margin-bottom:6px;">
    As of {{ $saln->as_of_date ? \Carbon\Carbon::parse($saln->as_of_date)->format('F d, Y') : '____________________' }}
</div>

{{-- LIABILITIES --}}
<div style="margin-top:4px;">
    <strong style="font-size:9.5pt;">2. LIABILITIES*</strong>
    <span style="font-size:8pt; font-style:italic;">&nbsp;(Loans, Mortgages, and other Obligations)</span>

    <table style="margin-top:5px;" class="no-break">
        <thead>
            <tr>
                <th style="width:35%;">NATURE</th>
                <th style="width:40%;">NAME OF CREDITORS</th>
                <th style="width:25%;">OUTSTANDING BALANCE</th>
            </tr>
        </thead>
        <tbody>
            @php $liabCount = $saln->liabilities->count(); $minLiab = 4; $liabTotal = $saln->liabilities->sum('outstanding_balance'); @endphp
            @forelse($saln->liabilities as $l)
                <tr class="dr"><td class="la">{{ $l->nature }}</td><td class="la">{{ $l->name_of_creditors }}</td><td>₱{{ number_format($l->outstanding_balance,2) }}</td></tr>
            @empty
                @for($i=0;$i<$minLiab;$i++)<tr class="dr"><td></td><td></td><td></td></tr>@endfor
            @endforelse
            @if($liabCount>0 && $liabCount<$minLiab)
                @for($i=$liabCount;$i<$minLiab;$i++)<tr class="dr"><td></td><td></td><td></td></tr>@endfor
            @endif
            <tr class="sub-r">
                <td colspan="2" style="text-align:right;">TOTAL LIABILITIES:</td>
                <td>₱{{ number_format($liabTotal,2) }}</td>
            </tr>
        </tbody>
    </table>
    <div style="font-size:7pt; font-style:italic; margin-top:2px;">* Additional sheet/s may be used, if necessary.</div>
</div>

{{-- NET WORTH --}}
<div class="net-worth-box no-break">
    NET WORTH = Total Assets less Total Liabilities = ₱{{ number_format($saln->net_worth,2) }}
</div>

{{-- BUSINESS INTERESTS --}}
<div class="sec-hdr">BUSINESS INTERESTS AND FINANCIAL CONNECTIONS</div>
<div class="sec-sub">(of Declarant/Declarant's spouse/Unmarried Children Below Eighteen (18) years of Age Living in Declarant's Household)</div>

<div style="margin: 4px 0 5px; font-size:9pt;">
    <div style="margin-bottom:3px;">
        <span class="chkbox {{ $saln->has_business_interests ? 'checked' : '' }}">{{ $saln->has_business_interests ? '✓' : '' }}</span>
        I/We have business interest or financial connection.
    </div>
    <div>
        <span class="chkbox {{ $saln->no_business_interests ? 'checked' : '' }}">{{ $saln->no_business_interests ? '✓' : '' }}</span>
        I/We do not have any business interest or financial connection.
    </div>
</div>

<table class="no-break">
    <thead>
        <tr>
            <th style="width:25%;">NAME OF ENTITY/<br>BUSINESS ENTERPRISE</th>
            <th style="width:25%;">BUSINESS ADDRESS</th>
            <th style="width:25%;">NATURE OF BUSINESS INTEREST &amp;/OR FINANCIAL CONNECTION</th>
            <th style="width:25%;">DATE OF ACQUISITION OF<br>INTEREST OR CONNECTION</th>
        </tr>
    </thead>
    <tbody>
        @php $bizCount = $saln->businessInterests->count(); $minBiz = 2; @endphp
        @forelse($saln->businessInterests as $b)
            <tr class="dr"><td class="la">{{ $b->name_of_entity }}</td><td class="la">{{ $b->business_address }}</td><td class="la">{{ $b->nature_of_business_interest }}</td><td>{{ \Carbon\Carbon::parse($b->date_of_acquisition)->format('m/d/Y') }}</td></tr>
        @empty
            @for($i=0;$i<$minBiz;$i++)<tr class="dr"><td></td><td></td><td></td><td></td></tr>@endfor
        @endforelse
        @if($bizCount>0 && $bizCount<$minBiz)
            @for($i=$bizCount;$i<$minBiz;$i++)<tr class="dr"><td></td><td></td><td></td><td></td></tr>@endfor
        @endif
    </tbody>
</table>

{{-- RELATIVES IN GOVERNMENT --}}
<div class="sec-hdr">RELATIVES IN THE GOVERNMENT SERVICE</div>
<div class="sec-sub">(Within the Fourth Degree of Consanguinity or Affinity. Include also Biles, Balae and Inso)</div>

<div style="margin: 4px 0 5px; font-size:9pt;">
    <span class="chkbox {{ $saln->no_relatives_in_government ? 'checked' : '' }}">{{ $saln->no_relatives_in_government ? '✓' : '' }}</span>
    I/We do not know of any relative/s in the government service.
</div>

<table class="no-break">
    <thead>
        <tr>
            <th style="width:25%;">NAME OF RELATIVE</th>
            <th style="width:20%;">RELATIONSHIP</th>
            <th style="width:25%;">POSITION</th>
            <th style="width:30%;">NAME OF AGENCY/OFFICE AND ADDRESS</th>
        </tr>
    </thead>
    <tbody>
        @php $relCount = $saln->relativesInGovernment->count(); $minRel = 2; @endphp
        @forelse($saln->relativesInGovernment as $r)
            <tr class="dr"><td class="la">{{ $r->name_of_relative }}</td><td>{{ $r->relationship }}</td><td class="la">{{ $r->position }}</td><td class="la">{{ $r->name_of_agency_office_address }}</td></tr>
        @empty
            @for($i=0;$i<$minRel;$i++)<tr class="dr"><td></td><td></td><td></td><td></td></tr>@endfor
        @endforelse
        @if($relCount>0 && $relCount<$minRel)
            @for($i=$relCount;$i<$minRel;$i++)<tr class="dr"><td></td><td></td><td></td><td></td></tr>@endfor
        @endif
    </tbody>
</table>

{{-- CERTIFICATION --}}
<div class="cert" style="margin-top:8px;">
    I hereby certify that these are true and correct statements of my assets, liabilities, net worth, business interests and financial connections, including those of my spouse and unmarried children below eighteen (18) years of age living in my household, and that to the best of my knowledge, the above-enumerated are names of my relatives in the government within the fourth civil degree of consanguinity or affinity.
</div>
<div class="cert" style="margin-top:5px;">
    I hereby authorize the Ombudsman or his/her duly authorized representative to obtain and secure from all appropriate government agencies, including the Bureau of Internal Revenue such documents that may show my assets, liabilities, net worth, business interests and financial connections, to include those of my spouse and unmarried children below 18 years of age living with me in my household covering previous years to include the year I first assumed office in government.
</div>

<div class="date-line" style="margin-top:8px;">
    Date: <span class="underline-field">{{ $saln->date_signed ? \Carbon\Carbon::parse($saln->date_signed)->format('F d, Y') : '' }}</span>
</div>

{{-- SIGNATURES --}}
<div class="sig-wrap no-break">
    <div class="sig-box">
        <div class="sig-line"></div>
        <div class="sig-label">(Signature of Declarant)</div>
        <div class="id-block">
            <strong>Government Issued ID:</strong>
            <div class="id-field">{{ $saln->declarant_id_type ?? '' }}</div>
            <strong>ID No.:</strong>
            <div class="id-field">{{ $saln->declarant_id_number ?? '' }}</div>
            <strong>Date Issued:</strong>
            <div class="id-field">{{ $saln->declarant_id_issued ?? '' }}</div>
        </div>
    </div>
    <div class="sig-box">
        <div class="sig-line"></div>
        <div class="sig-label">(Signature of Co-Declarant/Spouse)</div>
        <div class="id-block">
            <strong>Government Issued ID:</strong>
            <div class="id-field">{{ $saln->spouse_id_type ?? '' }}</div>
            <strong>ID No.:</strong>
            <div class="id-field">{{ $saln->spouse_id_number ?? '' }}</div>
            <strong>Date Issued:</strong>
            <div class="id-field">{{ $saln->spouse_id_issued ?? '' }}</div>
        </div>
    </div>
</div>

{{-- OATH --}}
<div class="oath-wrap no-break" style="margin-top:10px;">
    <div class="oath-text">
        SUBSCRIBED AND SWORN TO before me this
        <span class="underline-field" style="min-width:50px; text-align:center;">{{ $saln->subscribed_sworn_date ? \Carbon\Carbon::parse($saln->subscribed_sworn_date)->format('jS') : '' }}</span>
        day of
        <span class="underline-field" style="min-width:120px; text-align:center;">{{ $saln->subscribed_sworn_date ? \Carbon\Carbon::parse($saln->subscribed_sworn_date)->format('F Y') : '' }}</span>,
        affiant exhibiting to me the above-stated government issued identification card.
    </div>
    <div class="oath-sig" style="margin-top:16px;">
        <div class="oath-sig-line"></div>
        <div class="oath-sig-label">{{ $saln->person_administering_oath ?? '(Person Administering Oath)' }}</div>
    </div>
</div>

{{-- NOTES --}}
<div class="notes no-break">
    <p><strong>NOTE:</strong> Violation of this law is punishable by a fine not exceeding five thousand pesos (₱5,000) or imprisonment not exceeding one (1) year, or both, at the discretion of the court (Section 11, R.A. 6713).</p>
    <p style="margin-top:3px;"><strong>REMINDER:</strong> Any misrepresentation or non-disclosure of any material fact required to be stated herein shall constitute perjury under Article 183 of the Revised Penal Code and shall be punished accordingly.</p>
</div>

</body>
</html>
