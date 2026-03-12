<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SALN — {{ $saln->declarant_family_name }}, {{ $saln->declarant_first_name }}</title>
    <style>
        @page { size: 8.5in 13in; margin: 0.40in 0.50in; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 9pt; line-height: 1.3; color: #000; }

        .print-btn {
            position: fixed; top: 15px; right: 15px;
            padding: 10px 20px; background: #1e40af; color: #fff;
            border: none; border-radius: 6px; cursor: pointer;
            font-size: 13px; font-weight: 600; z-index: 9999;
        }
        .print-btn:hover { background: #1e3a8a; }
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }

        /* Each page is a fixed-height flex column */
        .saln-page {
            width: 100%;
            height: 12.2in;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            page-break-after: always;
        }
        .saln-page:last-child { page-break-after: auto; }

        /* ── Typography ── */
        .annex-label   { font-size: 14pt; font-weight: bold; }
        .form-title    { font-weight: bold; font-size: 12pt; text-align: center; text-decoration: underline; margin: 2px 0; }
        .form-subtitle { text-align: center; font-size: 8pt; }
        .form-italic   { text-align: center; font-style: italic; font-size: 8pt; }
        .page-ref      { text-align: right; font-size: 7pt; font-weight: bold; }
        .res-ref       { font-size: 7pt; line-height: 1.4; }
        .sec-hdr       { font-weight: bold; text-align: center; text-decoration: underline; font-size: 9.5pt; margin: 4px 0 2px; }
        .sec-sub       { text-align: center; font-style: italic; font-size: 7.5pt; margin-bottom: 2px; }
        .asset-hdr     { font-weight: bold; font-size: 9.5pt; margin: 4px 0 2px; }

        /* ── Compliance ── */
        .compliance-block { border: 1px solid #000; padding: 4px 8px; margin: 4px 0; }
        .compliance-title { font-weight: bold; font-size: 9pt; margin-bottom: 3px; }
        .compliance-opts  { display: flex; gap: 14px; flex-wrap: wrap; font-size: 9pt; }
        .compliance-opts span { white-space: nowrap; }

        /* ── Checkbox ── */
        .chkbox {
            display: inline-block; width: 11px; height: 11px;
            border: 1.5px solid #000; text-align: center; vertical-align: middle;
            font-size: 8pt; line-height: 10px; font-weight: bold; margin-right: 3px;
        }
        .chkbox.on { background: #000; color: #fff; font-size: 7pt; }

        /* ── Declarant rows ── */
        .prow   { display: flex; align-items: flex-end; gap: 4px; margin-bottom: 3px; }
        .plabel { font-weight: bold; font-size: 8pt; white-space: nowrap; padding-bottom: 1px; flex-shrink: 0; }
        .pval   { border-bottom: 1px solid #000; flex-grow: 1; min-height: 16px; padding: 1px 4px; font-size: 9pt; position: relative; }
        .pval-pad { padding-bottom: 11px; }
        .phint  { font-size: 5.5pt; font-style: italic; text-align: center; position: absolute; bottom: -9px; left: 0; right: 0; }

        /* ── Multiple marriages ── */
        .mm-label { font-weight: bold; font-size: 8pt; margin-bottom: 2px; }
        .mm-line  { border-bottom: 1px solid #000; min-height: 15px; padding: 1px 4px; margin-bottom: 3px; font-size: 9pt; }

        /* ── Filing note ── */
        .filing-note { font-size: 8pt; font-weight: bold; margin-bottom: 2px; }

        /* ── Dividers ── */
        .hdiv  { border-top: 1px solid #000; margin: 4px 0; }
        .hdiv2 { border-top: 2px solid #000; margin: 5px 0; }

        /* ── Tables — key trick: tbody fills remaining space ── */
        table { width: 100%; border-collapse: collapse; }
        th, td {
            border: 1px solid #000;
            padding: 2px 3px;
            text-align: center;
            vertical-align: middle;
            font-size: 8pt;
            line-height: 1.2;
        }
        th { background: #d0d0d0; font-weight: bold; font-size: 7pt; }
        td.la { text-align: left; }

        /* Standard data row */
        tr.dr { height: 22px; }

        /* Expandable row — grows to fill available space inside flex-table wrapper */
        tr.dr-grow { height: auto; }

        tr.sub-r { background: #e4e4e4; font-weight: bold; font-size: 8pt; }

        /* Flex-table wrapper: the table expands to fill the wrapper */
        .flex-table-wrap {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }
        .flex-table-wrap table { height: 100%; }
        .flex-table-wrap tbody { height: 100%; }
        .flex-table-wrap tbody tr.dr-grow { height: auto; }

        /* ── Totals ── */
        .total-bar {
            text-align: right; font-weight: bold; font-size: 10pt;
            border-top: 2px solid #000; padding: 3px 6px; margin: 3px 0;
        }
        .net-worth-box {
            border: 2px solid #000; padding: 5px 10px; margin: 5px 0;
            text-align: center; font-weight: bold; font-size: 10pt; background: #f0f0f0;
        }

        /* ── Underline field ── */
        .ufield { display: inline-block; border-bottom: 1px solid #000; min-width: 120px; padding: 1px 4px; vertical-align: bottom; }

        /* ── Signatures ── */
        .sig-wrap  { display: flex; justify-content: space-between; gap: 20px; margin-top: 8px; }
        .sig-box   { width: 48%; }
        .sig-line  { border-bottom: 1.5px solid #000; height: 28px; margin-bottom: 3px; }
        .sig-label { text-align: center; font-size: 8pt; font-weight: bold; }
        .id-block  { font-size: 8pt; margin-top: 3px; }
        .id-field  { border-bottom: 1px solid #000; min-height: 13px; padding: 1px 3px; margin: 2px 0 4px; }

        /* ── Oath ── */
        .oath-text { text-align: justify; text-decoration: underline; font-weight: bold; font-size: 8.5pt; line-height: 1.6; }
        .oath-sig       { text-align: center; margin-top: 16px; }
        .oath-sig-line  { border-top: 1.5px solid #000; width: 260px; margin: 0 auto 4px; }
        .oath-sig-label { font-weight: bold; font-size: 8pt; }

        /* ── Certification ── */
        .cert { font-size: 8.5pt; text-align: justify; line-height: 1.45; margin: 4px 0; }

        /* ── Footnotes / notes ── */
        .footnotes { border-top: 1px solid #000; margin-top: 5px; padding-top: 4px; font-size: 6.5pt; font-style: italic; line-height: 1.3; }
        .notes     { border-top: 2px solid #000; padding-top: 4px; margin-top: 5px; font-size: 6.5pt; line-height: 1.25; }
        .notes p   { margin: 2px 0; }

        /* ── Bottom stamp ── */
        .page-stamp { display: flex; justify-content: space-between; margin-top: 5px; padding-top: 4px; border-top: 1px solid #ccc; font-size: 8pt; }

        /* ── Flex spacer ── */
        .spacer { flex: 1; min-height: 0; }

        .decl-initial { text-align: right; font-size: 8pt; font-style: italic; border-top: 1px solid #000; padding-top: 3px; margin-top: 5px; }
        .no-break { page-break-inside: avoid; }

        /* Section label inside asset areas */
        .sub-section-label { font-size: 9pt; font-weight: bold; margin: 4px 0 2px; }
    </style>
</head>
<body>

<button class="print-btn no-print" onclick="window.print()">🖨️ Print SALN</button>

{{-- ══════════════ ANNEX A — PAGE 1 ══════════════ --}}
<div class="saln-page">

    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:3px;">
        <div class="annex-label">ANNEX A</div>
        <div style="text-align:right;">
            <div class="page-ref">Page 1 of 2</div>
            <div class="res-ref">2025 SALN Form<br>Per CSC Resolution No. ____________<br>Promulgated on ____________</div>
        </div>
    </div>

    <div class="form-title">SWORN STATEMENT OF ASSETS, LIABILITIES, AND NET WORTH</div>
    <div class="form-subtitle">(As required by R.A. No. 6713)</div>

    {{-- Compliance --}}
    <div class="compliance-block no-break">
        <div class="compliance-title">COMPLIANCE FOR:</div>
        <div class="compliance-opts">
            <span><span class="chkbox {{ $saln->compliance_assumption ? 'on':'' }}">{{ $saln->compliance_assumption ? '✓':'' }}</span> Assumption of office as of <span class="ufield">{{ $saln->compliance_assumption && $saln->as_of_date ? \Carbon\Carbon::parse($saln->as_of_date)->format('F d, Y') : '' }}</span></span>
            <span><span class="chkbox {{ $saln->compliance_annual ? 'on':'' }}">{{ $saln->compliance_annual ? '✓':'' }}</span> Annual filing as of December 31, <span class="ufield" style="min-width:50px;">{{ $saln->compliance_annual && $saln->as_of_date ? \Carbon\Carbon::parse($saln->as_of_date)->format('Y') : '' }}</span></span>
            <span><span class="chkbox {{ $saln->compliance_exit ? 'on':'' }}">{{ $saln->compliance_exit ? '✓':'' }}</span> Exit as of <span class="ufield">{{ $saln->compliance_exit && $saln->as_of_date ? \Carbon\Carbon::parse($saln->as_of_date)->format('F d, Y') : '' }}</span></span>
        </div>
    </div>

    {{-- Declarant / Spouse --}}
    <div class="no-break" style="margin:5px 0;">
        <div class="prow">
            <span class="plabel" style="width:84px;">DECLARANT:</span>
            <div class="pval pval-pad" style="width:22%;">{{ $saln->declarant_family_name }}<div class="phint">(Family Name)</div></div>
            <div class="pval pval-pad" style="width:22%;">{{ $saln->declarant_first_name }}<div class="phint">(First Name)</div></div>
            <div class="pval pval-pad" style="width:7%;">{{ $saln->declarant_middle_initial }}<div class="phint">(M.I.)</div></div>
            <span class="plabel" style="width:62px; margin-left:6px;">POSITION:</span>
            <div class="pval">{{ $saln->declarant_position }}</div>
        </div>
        <div style="height:10px;"></div>
        <div class="prow">
            <span class="plabel" style="width:84px;">AGENCY/OFFICE:</span>
            <div class="pval" style="width:44%;">{{ $saln->declarant_agency_office }}</div>
            <span class="plabel" style="width:92px; margin-left:6px;">OFFICE ADDRESS:</span>
            <div class="pval">{{ $saln->declarant_office_address }}</div>
        </div>
        <div class="hdiv" style="margin:5px 0;"></div>
        <div class="prow">
            <span class="plabel" style="width:84px;">SPOUSE:</span>
            <div class="pval pval-pad" style="width:22%;">{{ $saln->spouse_family_name }}<div class="phint">(Family Name)</div></div>
            <div class="pval pval-pad" style="width:22%;">{{ $saln->spouse_first_name }}<div class="phint">(First Name)</div></div>
            <div class="pval pval-pad" style="width:7%;">{{ $saln->spouse_middle_initial }}<div class="phint">(M.I.)</div></div>
            <span class="plabel" style="width:62px; margin-left:6px;">POSITION:</span>
            <div class="pval">{{ $saln->spouse_position }}</div>
        </div>
        <div style="height:10px;"></div>
        <div class="prow">
            <span class="plabel" style="width:84px;"></span><div style="width:54%;"></div>
            <span class="plabel" style="width:92px; margin-left:6px;">AGENCY/OFFICE:</span>
            <div class="pval">{{ $saln->spouse_agency_office }}</div>
        </div>
        <div style="height:4px;"></div>
        <div class="prow">
            <span class="plabel" style="width:84px;"></span><div style="width:54%;"></div>
            <span class="plabel" style="width:92px; margin-left:6px;">OFFICE ADDRESS:</span>
            <div class="pval">{{ $saln->spouse_office_address }}</div>
        </div>
    </div>

    <div class="hdiv2"></div>

    {{-- Filing type --}}
    <div class="no-break" style="margin:4px 0;">
        <div class="filing-note">SPOUSES, WHO ARE BOTH PUBLIC OFFICIALS OR EMPLOYEES, MAY FILE THE SALN JOINTLY OR SEPARATELY. THE DECLARANT SHALL CHECK THE APPROPRIATE BOX</div>
        <div style="font-size:9pt; margin-top:3px;">
            <span class="chkbox {{ $saln->joint_filing ? 'on':'' }}">{{ $saln->joint_filing ? '✓':'' }}</span> Joint Filing &nbsp;&nbsp;
            <span class="chkbox {{ $saln->separate_filing ? 'on':'' }}">{{ $saln->separate_filing ? '✓':'' }}</span> Separate Filing &nbsp;&nbsp;
            <span class="chkbox {{ $saln->not_applicable ? 'on':'' }}">{{ $saln->not_applicable ? '✓':'' }}</span> Not Applicable
        </div>
    </div>

    {{-- Multiple marriages --}}
    <div class="no-break" style="margin:4px 0;">
        <div class="mm-label">IF WITH MULTIPLE MARRIAGES, INDICATE NAME(S) OF SPOUSES, OTHERWISE CHECK THE "NOT APPLICABLE" BOX.</div>
        @if($saln->multiple_marriages_not_applicable)
            <span class="chkbox on">✓</span> Not Applicable
        @else
            @php $mmNames = array_filter(array_map('trim', explode("\n", $saln->multiple_marriages_names ?? ''))); @endphp
            <div class="mm-line">{{ $mmNames[0] ?? '' }}</div>
            <div class="mm-line">{{ $mmNames[1] ?? '' }}</div>
            <span class="chkbox {{ blank($saln->multiple_marriages_names) ? 'on':'' }}">{{ blank($saln->multiple_marriages_names) ? '✓':'' }}</span> Not Applicable
        @endif
    </div>

    <div class="hdiv2"></div>

    {{-- Children --}}
    <div class="sec-hdr">UNMARRIED CHILDREN BELOW EIGHTEEN (18) YEARS OF AGE LIVING IN DECLARANT'S HOUSEHOLD</div>
    <table class="no-break">
        <thead><tr><th style="width:75%;">NAME OF CHILD</th><th style="width:25%;">AGE</th></tr></thead>
        <tbody>
            @php $cc = $saln->children->count(); @endphp
            @foreach($saln->children as $c)<tr class="dr"><td class="la">{{ $c->name }}</td><td>{{ $c->age }}</td></tr>@endforeach
            @for($i=$cc; $i<3; $i++)<tr class="dr"><td></td><td></td></tr>@endfor
        </tbody>
    </table>

    <div class="hdiv2"></div>

    <div class="sec-hdr">ASSETS, LIABILITIES AND NETWORTH<sup>ii</sup></div>
    <div class="sec-sub">(Including those of the spouse and unmarried children below eighteen (18) years of age living in declarant's household)<sup>iii</sup></div>

    <div class="asset-hdr">1. &nbsp;ASSETS</div>
    <div class="sub-section-label">a. &nbsp;Real Properties</div>

    {{-- Real Properties table — fixed rows --}}
    <table class="no-break">
        <thead>
            <tr>
                <th rowspan="2" style="width:14%;">DESCRIPTION<br><span style="font-weight:normal;font-size:6pt;font-style:italic;">(e.g. lot, house and lot, condominium, and improvements)</span></th>
                <th rowspan="2" style="width:10%;">KIND<br><span style="font-weight:normal;font-size:6pt;font-style:italic;">(e.g. residential, commercial, industrial, agricultural and mixed use)</span></th>
                <th rowspan="2" style="width:15%;">EXACT LOCATION</th>
                <th rowspan="2" style="width:12%;">ASSESSED VALUE<br><span style="font-weight:normal;font-size:6pt;font-style:italic;">(As found in the Tax Declaration of Real Property, if available)</span></th>
                <th rowspan="2" style="width:13%;">CURRENT FAIR MARKET VALUE</th>
                <th colspan="3">ACQUISITION</th>
            </tr>
            <tr><th style="width:8%;">YEAR</th><th style="width:14%;">MODE</th><th style="width:14%;">COST</th></tr>
        </thead>
        <tbody>
            @php $rpc=$saln->realProperties->count(); $rpt=$saln->realProperties->sum('current_fair_market_value'); @endphp
            @foreach($saln->realProperties as $p)
            <tr class="dr"><td class="la">{{ $p->description }}</td><td>{{ $p->kind }}</td><td class="la">{{ $p->exact_location }}</td><td>₱{{ number_format($p->assessed_value,2) }}</td><td>₱{{ number_format($p->current_fair_market_value,2) }}</td><td>{{ $p->acquisition_year }}</td><td>{{ $p->mode_of_acquisition }}</td><td>₱{{ number_format($p->acquisition_cost,2) }}</td></tr>
            @endforeach
            @for($i=$rpc; $i<3; $i++)<tr class="dr"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>@endfor
            <tr class="sub-r"><td colspan="4" style="text-align:right;">Subtotal:</td><td>₱{{ number_format($rpt,2) }}</td><td colspan="3"></td></tr>
        </tbody>
    </table>

    <div class="sub-section-label" style="margin-top:5px;">b. &nbsp;Personal Properties</div>

    {{-- Personal Properties — flex-grow tbody fills remaining space --}}
    <div class="flex-table-wrap">
        <table style="height:100%;">
            <thead>
                <tr>
                    <th style="width:60%;">DESCRIPTION</th>
                    <th style="width:20%;">ACQUISITION YEAR</th>
                    <th style="width:20%;">ACQUISITION COST/ AMOUNT</th>
                </tr>
            </thead>
            <tbody style="height:100%;">
                @php $ppc=$saln->personalProperties->count(); $ppt=$saln->personalProperties->sum('acquisition_cost'); @endphp
                @foreach($saln->personalProperties as $p)
                <tr class="dr-grow"><td class="la">{{ $p->description }}</td><td>{{ $p->year_acquired }}</td><td>₱{{ number_format($p->acquisition_cost,2) }}</td></tr>
                @endforeach
                @for($i=$ppc; $i<3; $i++)<tr class="dr-grow" style="height:100%;"><td></td><td></td><td></td></tr>@endfor
                <tr class="sub-r"><td colspan="2" style="text-align:right;">Subtotal:</td><td>₱{{ number_format($ppt,2) }}</td></tr>
            </tbody>
        </table>
    </div>

    <div class="total-bar">TOTAL ASSETS: &nbsp;₱{{ number_format($saln->total_assets,2) }}</div>
    <div class="decl-initial">Signature/Initial of Declarant: ___________________________</div>
    <div style="text-align:center; font-size:8pt; margin-top:4px;">Page 1 of ___</div>
</div>

{{-- ══════════════ ANNEX A — PAGE 2 ══════════════ --}}
<div class="saln-page">

    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
        <div class="res-ref">2025 SALN Form<br>Per CSC Resolution No. ____________<br>Promulgated on ____________</div>
        <div class="page-ref">Page 2 of 2</div>
    </div>

    <div class="asset-hdr">2. &nbsp;LIABILITIES</div>
    <table class="no-break">
        <thead><tr><th style="width:35%;">NATURE</th><th style="width:40%;">NAME OF CREDITORS</th><th style="width:25%;">OUTSTANDING BALANCE</th></tr></thead>
        <tbody>
            @php $lc=$saln->liabilities->count(); $lt=$saln->liabilities->sum('outstanding_balance'); @endphp
            @foreach($saln->liabilities as $l)<tr class="dr"><td class="la">{{ $l->nature }}</td><td class="la">{{ $l->name_of_creditors }}</td><td>₱{{ number_format($l->outstanding_balance,2) }}</td></tr>@endforeach
            @for($i=$lc; $i<5; $i++)<tr class="dr"><td></td><td></td><td></td></tr>@endfor
            <tr class="sub-r"><td colspan="2" style="text-align:right;font-weight:bold;">TOTAL LIABILITIES:</td><td>₱{{ number_format($lt,2) }}</td></tr>
        </tbody>
    </table>

    <div class="net-worth-box">NET WORTH: Total Assets less Total Liabilities = ₱{{ number_format($saln->net_worth,2) }}</div>

    <div class="hdiv2"></div>

    <div class="sec-hdr">BUSINESS INTERESTS AND FINANCIAL CONNECTIONS</div>
    <div class="sec-sub">(of Declarant / Declarant's spouse / Unmarried Children Below Eighteen (18) years of Age Living in Declarant's Household)</div>
    <div style="margin:3px 0 4px; font-size:9pt;">
        <span class="chkbox {{ $saln->no_business_interests ? 'on':'' }}">{{ $saln->no_business_interests ? '✓':'' }}</span> I/ We do not have any business interest or financial connection.
    </div>
    <table class="no-break">
        <thead><tr><th style="width:25%;">NAME OF ENTITY/<br>BUSINESS ENTERPRISE</th><th style="width:25%;">BUSINESS ADDRESS</th><th style="width:25%;">NATURE OF BUSINESS INTEREST &amp;/OR FINANCIAL CONNECTION</th><th style="width:25%;">DATE OF ACQUISITION OF INTEREST OR CONNECTION</th></tr></thead>
        <tbody>
            @php $bc=$saln->businessInterests->count(); @endphp
            @foreach($saln->businessInterests as $b)<tr class="dr"><td class="la">{{ $b->name_of_entity }}</td><td class="la">{{ $b->business_address }}</td><td class="la">{{ $b->nature_of_business_interest }}</td><td>{{ $b->date_of_acquisition ? \Carbon\Carbon::parse($b->date_of_acquisition)->format('m/d/Y'):'' }}</td></tr>@endforeach
            @for($i=$bc; $i<3; $i++)<tr class="dr"><td></td><td></td><td></td><td></td></tr>@endfor
        </tbody>
    </table>

    <div class="hdiv2"></div>

    <div class="sec-hdr">RELATIVES IN THE GOVERNMENT SERVICE</div>
    <div class="sec-sub">(Within the Fourth Degree of Consanguinity or Affinity. Include also Bilas, Balae and Inso<sup>iv</sup>)</div>
    <div style="margin:3px 0 4px; font-size:9pt;">
        <span class="chkbox {{ $saln->no_relatives_in_government ? 'on':'' }}">{{ $saln->no_relatives_in_government ? '✓':'' }}</span> I/ We do not know of any relative/s in the government service.
    </div>
    <table class="no-break">
        <thead><tr><th style="width:25%;">NAME OF RELATIVE</th><th style="width:20%;">RELATIONSHIP</th><th style="width:25%;">POSITION</th><th style="width:30%;">NAME OF AGENCY/OFFICE AND ADDRESS</th></tr></thead>
        <tbody>
            @php $rc=$saln->relativesInGovernment->count(); @endphp
            @foreach($saln->relativesInGovernment as $r)<tr class="dr"><td class="la">{{ $r->name_of_relative }}</td><td>{{ $r->relationship }}</td><td class="la">{{ $r->position }}</td><td class="la">{{ $r->name_of_agency_office_address }}</td></tr>@endforeach
            @for($i=$rc; $i<4; $i++)<tr class="dr"><td></td><td></td><td></td><td></td></tr>@endfor
        </tbody>
    </table>

    <div class="hdiv2"></div>

    <div class="cert">I hereby certify that these are true and correct statements of my assets, liabilities, net worth, business interests and financial connections, including those of my spouse and unmarried children below eighteen (18) years of age living in my household, and that to the best of my knowledge, the above-enumerated are names of my relatives in the government within the fourth civil degree of consanguinity or affinity.</div>
    <div class="cert" style="margin-top:5px;">I hereby authorize the Ombudsman or his/her duly authorized representative to obtain and secure from all appropriate government agencies, including the Bureau of Internal Revenue such documents that may show my assets, liabilities, net worth, business interests and financial connections, to include those of my spouse and unmarried children below 18 years of age living with me in my household covering previous years to include the year I first assumed office in government.</div>

    <div style="margin-top:8px; font-size:9pt;">Date: <span class="ufield">{{ $saln->date_signed ? \Carbon\Carbon::parse($saln->date_signed)->format('F d, Y') : '' }}</span></div>

    <div class="sig-wrap no-break">
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-label">(Signature of Declarant)</div>
            <div class="id-block"><strong>Government Issued ID:</strong><div class="id-field"></div><strong>ID No.:</strong><div class="id-field"></div><strong>Date Issued:</strong><div class="id-field"></div></div>
        </div>
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-label">(Signature of Declarant)</div>
            <div class="id-block"><strong>Government Issued ID:</strong><div class="id-field"></div><strong>ID No.:</strong><div class="id-field"></div><strong>Date Issued:</strong><div class="id-field"></div></div>
        </div>
    </div>

    <div class="no-break" style="margin-top:10px;">
        <div class="oath-text">
            SUBSCRIBED AND SWORN to before me this
            <span class="ufield" style="min-width:40px;text-align:center;">{{ $saln->subscribed_sworn_date ? \Carbon\Carbon::parse($saln->subscribed_sworn_date)->format('jS') : '' }}</span>
            day of
            <span class="ufield" style="min-width:100px;text-align:center;">{{ $saln->subscribed_sworn_date ? \Carbon\Carbon::parse($saln->subscribed_sworn_date)->format('F Y') : '' }}</span>,
            affiant exhibiting to me the above-stated government-issued identification card.
        </div>
        <div class="oath-sig">
            <div class="oath-sig-line"></div>
            <div class="oath-sig-label">{{ $saln->person_administering_oath ?? '(Person Administering Oath)' }}</div>
        </div>
    </div>

    <div class="spacer"></div>

    <div class="footnotes no-break">
        <p><sup>i</sup> Position, Agency, and Address shall only be declared if the spouse is a public official or employee.</p>
        <p><sup>ii</sup> Additional sheets may be used by the declarant, if necessary.</p>
        <p><sup>iii</sup> Capital or paraphernal assets, and liabilities of the declarant's spouse, and properties of children below 18 years of age and living in the declarant's household shall be disclosed using the additional sheets provided.</p>
        <p><sup>iv</sup> Balae refers to the parent of one's son or daughter-in-law; Bilas refers to a brother-in-law's wife or sister-in-law's husband; Inso refers to the appellation for the wife of an elder brother or male cousin.</p>
    </div>
    <div class="notes no-break">
        <p><strong>NOTE:</strong> Violation of this law is punishable by a fine not exceeding five thousand pesos (₱5,000) or imprisonment not exceeding one (1) year, or both, at the discretion of the court (Section 11, R.A. 6713).</p>
        <p style="margin-top:2px;"><strong>REMINDER:</strong> Any misrepresentation or non-disclosure of any material fact required to be stated herein shall constitute perjury under Article 183 of the Revised Penal Code and shall be punished accordingly.</p>
    </div>
    <div class="page-stamp">
        <div>Page 2 of ___</div>
        <div style="font-style:italic;">Signature/Initial of Declarant: ___________________________</div>
    </div>
</div>

{{-- ══════════════ ANNEX B ══════════════ --}}
<div class="saln-page">

    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:4px;">
        <div class="annex-label">ANNEX B</div>
        <div class="res-ref" style="text-align:right;">SALN Form AS-1 (Declarant)<br>Per CSC Resolution No. ____________<br>Promulgated on ____________</div>
    </div>
    <div class="form-title">SWORN STATEMENT OF ASSETS, LIABILITIES AND NET WORTH</div>
    <div class="form-subtitle">As of <span class="ufield" style="min-width:120px;">{{ $saln->as_of_date ? \Carbon\Carbon::parse($saln->as_of_date)->format('F d, Y') : '' }}</span></div>
    <div class="form-italic">(Additional sheet/s for the declarant)</div>

    <div style="margin:5px 0;">
        <div class="prow">
            <span class="plabel" style="width:50px;">NAME:</span>
            <div class="pval pval-pad" style="width:22%;">{{ $saln->declarant_family_name }}<div class="phint">(Family Name)</div></div>
            <div class="pval pval-pad" style="width:22%;">{{ $saln->declarant_first_name }}<div class="phint">(First Name)</div></div>
            <div class="pval pval-pad" style="width:7%;">{{ $saln->declarant_middle_initial }}<div class="phint">(M.I.)</div></div>
            <span class="plabel" style="width:62px; margin-left:6px;">POSITION:</span>
            <div class="pval">{{ $saln->declarant_position }}</div>
        </div>
        <div style="height:10px;"></div>
        <div class="prow">
            <span class="plabel" style="width:50px;"></span><div style="flex:1;"></div>
            <span class="plabel" style="width:92px;">AGENCY/OFFICE:</span>
            <div class="pval">{{ $saln->declarant_agency_office }}</div>
        </div>
    </div>

    <div class="hdiv2"></div>
    <div class="sec-hdr">ASSETS, LIABILITIES AND NET WORTH</div>
    <div class="asset-hdr">1. &nbsp;ASSETS</div>
    <div class="sub-section-label">a. &nbsp;Real Properties</div>

    <table class="no-break">
        <thead>
            <tr>
                <th rowspan="2" style="width:14%;">DESCRIPTION<br><span style="font-weight:normal;font-size:6pt;font-style:italic;">(e.g. lot, house and lot, condominium and improvements)</span></th>
                <th rowspan="2" style="width:10%;">KIND<br><span style="font-weight:normal;font-size:6pt;font-style:italic;">(e.g. residential, commercial, industrial, agricultural and mixed use)</span></th>
                <th rowspan="2" style="width:15%;">EXACT LOCATION</th>
                <th rowspan="2" style="width:12%;">ASSESSED VALUE<br><span style="font-weight:normal;font-size:6pt;font-style:italic;">(As found in the Tax Declaration of Real Property)</span></th>
                <th rowspan="2" style="width:13%;">CURRENT FAIR MARKET VALUE</th>
                <th colspan="3">ACQUISITION</th>
            </tr>
            <tr><th style="width:8%;">YEAR</th><th style="width:14%;">MODE</th><th style="width:14%;">COST</th></tr>
        </thead>
        <tbody>
            @for($i=0;$i<4;$i++)<tr class="dr"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>@endfor
            <tr class="sub-r"><td colspan="4" style="text-align:right;">Subtotal:</td><td></td><td colspan="3"></td></tr>
        </tbody>
    </table>

    <div class="sub-section-label" style="margin-top:5px;">b. &nbsp;Personal Properties</div>
    <table class="no-break">
        <thead><tr><th style="width:60%;">DESCRIPTION</th><th style="width:20%;">ACQUISITION YEAR</th><th style="width:20%;">ACQUISITION COST/ AMOUNT</th></tr></thead>
        <tbody>
            @for($i=0;$i<3;$i++)<tr class="dr"><td></td><td></td><td></td></tr>@endfor
            <tr class="sub-r"><td colspan="2" style="text-align:right;">Subtotal:</td><td></td></tr>
            <tr class="sub-r"><td colspan="2" style="text-align:right;font-weight:bold;">TOTAL ASSETS:</td><td></td></tr>
        </tbody>
    </table>

    <div class="asset-hdr" style="margin-top:6px;">2. &nbsp;LIABILITIES</div>
    <table class="no-break">
        <thead><tr><th style="width:35%;">NATURE</th><th style="width:40%;">NAME OF CREDITORS</th><th style="width:25%;">OUTSTANDING BALANCE</th></tr></thead>
        <tbody>
            @for($i=0;$i<3;$i++)<tr class="dr"><td></td><td></td><td></td></tr>@endfor
            <tr class="sub-r"><td colspan="2" style="text-align:right;font-weight:bold;">TOTAL LIABILITIES:</td><td></td></tr>
        </tbody>
    </table>

    <div class="hdiv2"></div>
    <div class="sec-hdr">BUSINESS INTERESTS AND FINANCIAL CONNECTIONS</div>

    {{-- Business interests table grows to fill rest of page --}}
    <div class="flex-table-wrap">
        <table style="height:100%;">
            <thead><tr><th style="width:25%;">NAME OF ENTITY/<br>BUSINESS ENTERPRISE</th><th style="width:25%;">BUSINESS ADDRESS</th><th style="width:25%;">NATURE OF BUSINESS INTEREST &amp;/OR FINANCIAL CONNECTION</th><th style="width:25%;">DATE OF ACQUISITION OF INTEREST OR CONNECTION</th></tr></thead>
            <tbody style="height:100%;">
                <tr class="dr-grow" style="height:33%;"><td></td><td></td><td></td><td></td></tr>
                <tr class="dr-grow" style="height:33%;"><td></td><td></td><td></td><td></td></tr>
                <tr class="dr-grow" style="height:34%;"><td></td><td></td><td></td><td></td></tr>
            </tbody>
        </table>
    </div>

    <div class="page-stamp">
        <div>Page ___ of ___</div>
        <div style="font-style:italic;">Signature/Initial of Declarant: ___________________________</div>
    </div>
</div>

{{-- ══════════════ ANNEX C ══════════════ --}}
<div class="saln-page">

    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:4px;">
        <div class="annex-label">ANNEX C</div>
        <div class="res-ref" style="text-align:right;">2025 SALN Form AS-2 (Spouse &amp; Children)<br>Per CSC Resolution No. ____________<br>Promulgated on ____________</div>
    </div>
    <div class="form-title">STATEMENT OF ASSETS, LIABILITIES AND NET WORTH</div>
    <div class="form-subtitle">As of <span class="ufield" style="min-width:120px;">{{ $saln->as_of_date ? \Carbon\Carbon::parse($saln->as_of_date)->format('F d, Y') : '' }}</span></div>
    <div class="form-italic">(Additional sheet/s for the exclusive properties of the declarant's spouse and unmarried children<br>below eighteen (18) years of age living in declarant's household)</div>

    <div style="margin:5px 0;">
        <div class="prow">
            <span class="plabel" style="width:50px;">NAME:</span>
            <div class="pval pval-pad" style="width:22%;">{{ $saln->spouse_family_name }}<div class="phint">(Family Name)</div></div>
            <div class="pval pval-pad" style="width:22%;">{{ $saln->spouse_first_name }}<div class="phint">(First Name)</div></div>
            <div class="pval pval-pad" style="width:7%;">{{ $saln->spouse_middle_initial }}<div class="phint">(M.I.)</div></div>
            <span class="plabel" style="width:62px; margin-left:6px;">POSITION:</span>
            <div class="pval">{{ $saln->spouse_position }}</div>
        </div>
        <div style="height:10px;"></div>
        <div class="prow">
            <span class="plabel" style="width:50px;"></span><div style="flex:1;"></div>
            <span class="plabel" style="width:92px;">AGENCY/OFFICE:</span>
            <div class="pval">{{ $saln->spouse_agency_office }}</div>
        </div>
    </div>

    <div class="hdiv2"></div>
    <div class="sec-hdr">ASSETS, LIABILITIES AND NET WORTH</div>
    <div class="asset-hdr">1. &nbsp;ASSETS</div>
    <div class="sub-section-label">a. &nbsp;Real Properties</div>

    <table class="no-break">
        <thead>
            <tr>
                <th rowspan="2" style="width:14%;">DESCRIPTION<br><span style="font-weight:normal;font-size:6pt;font-style:italic;">(e.g. lot, house and lot, condominium and improvements)</span></th>
                <th rowspan="2" style="width:10%;">KIND<br><span style="font-weight:normal;font-size:6pt;font-style:italic;">(e.g. residential, commercial, industrial, agricultural and mixed use)</span></th>
                <th rowspan="2" style="width:15%;">EXACT LOCATION</th>
                <th rowspan="2" style="width:12%;">ASSESSED VALUE<br><span style="font-weight:normal;font-size:6pt;font-style:italic;">(As found in the Tax Declaration of Real Property)</span></th>
                <th rowspan="2" style="width:13%;">CURRENT FAIR MARKET VALUE</th>
                <th colspan="3">ACQUISITION</th>
            </tr>
            <tr><th style="width:8%;">YEAR</th><th style="width:14%;">MODE</th><th style="width:14%;">COST</th></tr>
        </thead>
        <tbody>
            @for($i=0;$i<4;$i++)<tr class="dr"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>@endfor
            <tr class="sub-r"><td colspan="4" style="text-align:right;">Subtotal:</td><td></td><td colspan="3"></td></tr>
        </tbody>
    </table>

    <div class="sub-section-label" style="margin-top:5px;">b. &nbsp;Personal Properties</div>
    <table class="no-break">
        <thead><tr><th style="width:60%;">DESCRIPTION</th><th style="width:20%;">ACQUISITION YEAR</th><th style="width:20%;">ACQUISITION COST/ AMOUNT</th></tr></thead>
        <tbody>
            @for($i=0;$i<3;$i++)<tr class="dr"><td></td><td></td><td></td></tr>@endfor
            <tr class="sub-r"><td colspan="2" style="text-align:right;">Subtotal:</td><td></td></tr>
            <tr class="sub-r"><td colspan="2" style="text-align:right;font-weight:bold;">TOTAL ASSETS:</td><td></td></tr>
        </tbody>
    </table>

    <div class="asset-hdr" style="margin-top:6px;">2. &nbsp;LIABILITIES</div>
    <table class="no-break">
        <thead><tr><th style="width:35%;">NATURE</th><th style="width:40%;">NAME OF CREDITORS</th><th style="width:25%;">OUTSTANDING BALANCE</th></tr></thead>
        <tbody>
            @for($i=0;$i<3;$i++)<tr class="dr"><td></td><td></td><td></td></tr>@endfor
            <tr class="sub-r"><td colspan="2" style="text-align:right;font-weight:bold;">TOTAL LIABILITIES:</td><td></td></tr>
        </tbody>
    </table>

    <div class="hdiv2"></div>
    <div class="sec-hdr">BUSINESS INTERESTS AND FINANCIAL CONNECTIONS</div>

    {{-- Grows to fill remaining space --}}
    <div class="flex-table-wrap">
        <table style="height:100%;">
            <thead><tr><th style="width:25%;">NAME OF ENTITY/<br>BUSINESS ENTERPRISE</th><th style="width:25%;">BUSINESS ADDRESS</th><th style="width:25%;">NATURE OF BUSINESS INTEREST &amp;/OR FINANCIAL CONNECTION</th><th style="width:25%;">DATE OF ACQUISITION OF INTEREST OR CONNECTION</th></tr></thead>
            <tbody style="height:100%;">
                <tr class="dr-grow" style="height:33%;"><td></td><td></td><td></td><td></td></tr>
                <tr class="dr-grow" style="height:33%;"><td></td><td></td><td></td><td></td></tr>
                <tr class="dr-grow" style="height:34%;"><td></td><td></td><td></td><td></td></tr>
            </tbody>
        </table>
    </div>

    <div class="page-stamp">
        <div>Page ___ of ___</div>
        <div style="font-style:italic;">Signature/Initial of Declarant: ___________________________</div>
    </div>
</div>

</body>
</html>
