<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SALN - Statement of Assets, Liabilities and Net Worth</title>
    <style>
        @page {
            size: A4;
            margin: 0.1in;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header-date {
            text-align: right;
            font-size: 9px;
            margin-bottom: 10px;
        }

        .form-title {
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            margin: 10px 0;
            text-decoration: underline;
        }

        .as-of-date {
            text-align: center;
            margin: 8px 0;
            font-size: 12px;
            border-bottom: 1px solid #000;
            width: 300px;
            margin-left: auto;
            margin-right: auto;
            padding-bottom: 2px;
        }

        .required-by {
            text-align: center;
            font-size: 10px;
            margin-bottom: 15px;
        }

        .checkbox-section {
            margin: 10px 0;
            font-size: 10px;
        }

        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            text-align: center;
            margin-right: 5px;
            vertical-align: middle;
            font-size: 9px;
        }

        .checked {
            background-color: #000;
        }

        .personal-info {
            margin: 15px 0;
            font-size: 9px;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
            align-items: baseline;
        }

        .info-label {
            font-weight: bold;
            width: 120px;
            flex-shrink: 0;
        }

        .info-value {
            border-bottom: 1px solid #000;
            flex-grow: 1;
            min-height: 18px;
            padding: 2px 5px;
            margin-right: 20px;
        }

        .name-fields {
            display: flex;
            gap: 10px;
            margin-top: 5px;
        }

        .name-field {
            flex: 1;
            text-align: center;
        }

        .name-field-label {
            font-size: 9px;
            margin-bottom: 2px;
        }

        .name-field-value {
            border-bottom: 1px solid #000;
            min-height: 18px;
            padding: 2px;
        }

        .section-header {
            font-weight: bold;
            text-align: center;
            text-decoration: underline;
            margin: 20px 0 10px 0;
            font-size: 10px;
        }

        .section-subtitle {
            text-align: center;
            font-style: italic;
            margin-bottom: 10px;
            font-size: 9px;
        }

        .children-table, .assets-table, .liabilities-table, .business-table, .relatives-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .children-table th, .children-table td,
        .assets-table th, .assets-table td,
        .liabilities-table th, .liabilities-table td,
        .business-table th, .business-table td,
        .relatives-table th, .relatives-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
            font-size: 9px;
        }

        .children-table th, .assets-table th, .liabilities-table th, .business-table th, .relatives-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 9px;
        }

        .assets-section {
            margin: 15px 0;
        }

        .subsection {
            margin: 15px 0;
        }

        .subsection-title {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 9px;
        }

        .subtotal-row {
            font-weight: bold;
            text-align: right;
        }

        .total-row {
            font-weight: bold;
            font-size: 10px;
            text-align: right;
            margin: 10px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }

        .net-worth-section {
            border: 2px solid #000;
            padding: 8px;
            margin: 15px 0;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
        }

        .certification {
            text-align: justify;
            font-size: 10px;
            margin: 15px 0;
            line-height: 1.3;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .signature-left, .signature-right {
            width: 45%;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            margin: 20px 0 5px 0;
            text-align: center;
            height: 30px;
        }

        .signature-label {
            text-align: center;
            font-size: 10px;
            margin-bottom: 10px;
        }

        .id-info {
            margin-top: 10px;
            font-size: 10px;
        }

        .id-line {
            border-bottom: 1px solid #000;
            margin: 3px 0;
            min-height: 14px;
            padding: 2px;
        }

        .oath-section {
            margin-top: 25px;
            text-align: center;
        }

        .oath-signature {
            margin-top: 25px;
            text-align: center;
        }

        .page-number {
            text-align: right;
            margin-top: 15px;
            font-size: 10px;
        }

        .note-section {
            margin-top: 20px;
            border-top: 2px solid #000;
            padding-top: 10px;
            font-size: 9px;
        }

        .page-break {
            page-break-before: always;
        }

        .compact-row {
            height: 22px;
        }

        .left-align {
            text-align: left !important;
        }

        .checkbox-group {
            margin: 8px 0;
        }

        .filled-value {
            font-weight: bold;
            color: #000;
        }

        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }

        .print-button {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }

        .print-button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function printPage() {
            window.print();
        }
    </script>
</head>
<body>
    <!-- Print Button -->
    <button class="print-button no-print" onclick="printPage()">Print SALN</button>

    <!-- Header -->
    <div class="header-date">
        Revised as of January 2015<br>
        Per CSC Resolution No. 1500958<br>
        Promulgated on January 21, 2015
    </div>

    <div class="form-title">SWORN STATEMENT OF ASSETS, LIABILITIES AND NET WORTH</div>
    <div class="as-of-date">As of {{ $saln->as_of_date ? \Carbon\Carbon::parse($saln->as_of_date)->format('F d, Y') : '____________________' }}</div>
    <div class="required-by">(Required by R.A. 6713)</div>

    <!-- Filing Type -->
    <div class="checkbox-section">
        <strong>Note:</strong> Husband and wife who are both public officials and employees may file the required statements jointly or separately.<br><br>
        <span class="checkbox {{ $saln->filing_type === 'joint' ? 'checked' : '' }}">{{ $saln->filing_type === 'joint' ? '✓' : '' }}</span> Joint Filing &nbsp;&nbsp;&nbsp;&nbsp;
        <span class="checkbox {{ $saln->filing_type === 'separate' ? 'checked' : '' }}">{{ $saln->filing_type === 'separate' ? '✓' : '' }}</span> Separate Filing &nbsp;&nbsp;&nbsp;&nbsp;
        <span class="checkbox {{ $saln->filing_type === 'not_applicable' ? 'checked' : '' }}">{{ $saln->filing_type === 'not_applicable' ? '✓' : '' }}</span> Not Applicable
    </div>

    <!-- Personal Information -->
    <div class="personal-info">
        <div class="info-row">
            <div class="info-label">DECLARANT:</div>
            <div class="info-value filled-value" style="position: relative;">
                {{ $saln->user->name ?? '' }}
                <div style="position: absolute; bottom: -15px; left: 0; right: 0; display: flex; gap: 5px; font-size: 9px;">
                    <span style="flex: 2; text-align: center;">(Family Name)</span>
                    <span style="flex: 2; text-align: center;">(First Name)</span>
                    <span style="flex: 1; text-align: center;">(M.I.)</span>
                </div>
            </div>
            <div class="info-label">POSITION:</div>
            <div class="info-value filled-value">{{ $saln->position ?? '' }}</div>
        </div>

        <div class="info-row" style="margin-top: 20px;">
            <div class="info-label">ADDRESS:</div>
            <div class="info-value filled-value">{{ $saln->address ?? '' }}</div>
            <div class="info-label">AGENCY/OFFICE:</div>
            <div class="info-value filled-value">{{ $saln->agency_office ?? '' }}</div>
        </div>

        <div class="info-row">
            <div style="width: 120px;"></div>
            <div style="flex-grow: 1; margin-right: 20px;"></div>
            <div class="info-label">OFFICE ADDRESS:</div>
            <div class="info-value filled-value">{{ $saln->office_address ?? '' }}</div>
        </div>

        <br>

        <div class="info-row">
            <div class="info-label">SPOUSE:</div>
            <div class="info-value filled-value" style="position: relative;">
                {{ $saln->spouse_name ?? '' }}
                <div style="position: absolute; bottom: -15px; left: 0; right: 0; display: flex; gap: 5px; font-size: 9px;">
                    <span style="flex: 2; text-align: center;">(Family Name)</span>
                    <span style="flex: 2; text-align: center;">(First Name)</span>
                    <span style="flex: 1; text-align: center;">(M.I.)</span>
                </div>
            </div>
            <div class="info-label">POSITION:</div>
            <div class="info-value filled-value">{{ $saln->spouse_position ?? '' }}</div>
        </div>

        <div class="info-row" style="margin-top: 20px;">
            <div style="width: 120px;"></div>
            <div style="flex-grow: 1; margin-right: 20px;"></div>
            <div class="info-label">AGENCY/OFFICE:</div>
            <div class="info-value filled-value">{{ $saln->spouse_agency_office ?? '' }}</div>
        </div>

        <div class="info-row">
            <div style="width: 120px;"></div>
            <div style="flex-grow: 1; margin-right: 20px;"></div>
            <div class="info-label">OFFICE ADDRESS:</div>
            <div class="info-value filled-value">{{ $saln->spouse_office_address ?? '' }}</div>
        </div>
    </div>

    <!-- Children Section -->
    <div class="section-header">UNMARRIED CHILDREN BELOW EIGHTEEN (18) YEARS OF AGE LIVING IN DECLARANT'S HOUSEHOLD</div>
    <table class="children-table">
        <thead>
            <tr>
                <th style="width: 60%;">NAME</th>
                <th style="width: 20%;">DATE OF BIRTH</th>
                <th style="width: 20%;">AGE</th>
            </tr>
        </thead>
        <tbody>
            @forelse($saln->children as $child)
                <tr class="compact-row">
                    <td class="left-align filled-value">{{ $child->name }}</td>
                    <td class="filled-value">{{ \Carbon\Carbon::parse($child->date_of_birth)->format('m/d/Y') }}</td>
                    <td class="filled-value">{{ $child->age }}</td>
                </tr>
            @empty
                @for($i = 0; $i < 3; $i++)
                    <tr class="compact-row">
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            @endforelse

            @if($saln->children->count() > 0 && $saln->children->count() < 3)
                @for($i = $saln->children->count(); $i < 3; $i++)
                    <tr class="compact-row">
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>

    <!-- Assets Section -->
    <div class="section-header">ASSETS, LIABILITIES AND NETWORTH</div>
    <div class="section-subtitle">
        (Including those of the spouse and unmarried children below eighteen (18) years of age living in declarant's household)
    </div>

    <div class="assets-section">
        <div class="subsection-title">1. ASSETS</div>

        <!-- Real Properties -->
        <div class="subsection">
            <strong>a. Real Properties*</strong>
            <table class="assets-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 12%;">DESCRIPTION</th>
                        <th rowspan="2" style="width: 8%;">KIND</th>
                        <th rowspan="2" style="width: 15%;">EXACT LOCATION</th>
                        <th rowspan="2" style="width: 10%;">ASSESSED VALUE</th>
                        <th rowspan="2" style="width: 12%;">CURRENT FAIR MARKET VALUE</th>
                        <th colspan="3" style="width: 25%;">ACQUISITION</th>
                    </tr>
                    <tr>
                        <th style="width: 8%;">YEAR</th>
                        <th style="width: 8%;">MODE</th>
                        <th style="width: 9%;">COST</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($saln->realProperties as $property)
                        <tr class="compact-row">
                            <td class="left-align filled-value">{{ $property->description }}</td>
                            <td class="filled-value">{{ $property->kind }}</td>
                            <td class="left-align filled-value">{{ $property->exact_location }}</td>
                            <td class="filled-value">₱{{ number_format($property->assessed_value, 2) }}</td>
                            <td class="filled-value">₱{{ number_format($property->current_fair_market_value, 2) }}</td>
                            <td class="filled-value">{{ $property->acquisition_year }}</td>
                            <td class="filled-value">{{ $property->acquisition_mode }}</td>
                            <td class="filled-value">₱{{ number_format($property->acquisition_cost, 2) }}</td>
                        </tr>
                    @empty
                        @for($i = 0; $i < 3; $i++)
                            <tr class="compact-row">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endfor
                    @endforelse

                    @if($saln->realProperties->count() > 0 && $saln->realProperties->count() < 3)
                        @for($i = $saln->realProperties->count(); $i < 3; $i++)
                            <tr class="compact-row">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endfor
                    @endif

                    <tr class="subtotal-row">
                        <td colspan="4" style="text-align: right; font-weight: bold;">Subtotal:</td>
                        <td style="font-weight: bold;">₱{{ number_format($saln->realProperties->sum('current_fair_market_value'), 2) }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Personal Properties -->
        <div class="subsection">
            <strong>b. Personal Properties*</strong>
            <table class="assets-table">
                <thead>
                    <tr>
                        <th style="width: 60%;">DESCRIPTION</th>
                        <th style="width: 20%;">YEAR ACQUIRED</th>
                        <th style="width: 20%;">ACQUISITION COST/AMOUNT</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($saln->personalProperties as $property)
                        <tr class="compact-row">
                            <td class="left-align filled-value">{{ $property->description }}</td>
                            <td class="filled-value">{{ $property->year_acquired }}</td>
                            <td class="filled-value">₱{{ number_format($property->acquisition_cost, 2) }}</td>
                        </tr>
                    @empty
                        @for($i = 0; $i < 4; $i++)
                            <tr class="compact-row">
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endfor
                    @endforelse

                    @if($saln->personalProperties->count() > 0 && $saln->personalProperties->count() < 4)
                        @for($i = $saln->personalProperties->count(); $i < 4; $i++)
                            <tr class="compact-row">
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endfor
                    @endif

                    <tr class="subtotal-row">
                        <td colspan="2" style="text-align: right; font-weight: bold;">Subtotal:</td>
                        <td style="font-weight: bold;">₱{{ number_format($saln->personalProperties->sum('acquisition_cost'), 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="total-row">
            TOTAL ASSETS (a+b): ₱{{ number_format($saln->total_assets, 2) }}
        </div>

        <div class="page-number">Page 1 of 2</div>
    </div>

    <!-- Page Break -->
    <div class="page-break"></div>

    <!-- Liabilities Section -->
    <div class="subsection-title">2. LIABILITIES*</div>
    <table class="liabilities-table">
        <thead>
            <tr>
                <th style="width: 40%;">NATURE</th>
                <th style="width: 40%;">NAME OF CREDITORS</th>
                <th style="width: 20%;">OUTSTANDING BALANCE</th>
            </tr>
        </thead>
        <tbody>
            @forelse($saln->liabilities as $liability)
                <tr class="compact-row">
                    <td class="left-align filled-value">{{ $liability->nature }}</td>
                    <td class="left-align filled-value">{{ $liability->name_of_creditors }}</td>
                    <td class="filled-value">₱{{ number_format($liability->outstanding_balance, 2) }}</td>
                </tr>
            @empty
                @for($i = 0; $i < 4; $i++)
                    <tr class="compact-row">
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            @endforelse

            @if($saln->liabilities->count() > 0 && $saln->liabilities->count() < 4)
                @for($i = $saln->liabilities->count(); $i < 4; $i++)
                    <tr class="compact-row">
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            @endif

            <tr class="subtotal-row">
                <td colspan="2" style="text-align: right; font-weight: bold;">TOTAL LIABILITIES:</td>
                <td style="font-weight: bold;">₱{{ number_format($saln->total_liabilities, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Net Worth Section -->
    <div class="net-worth-section">
        <strong>NET WORTH = Total Assets less Total Liabilities = ₱{{ number_format($saln->net_worth, 2) }}</strong>
    </div>

    <!-- Business Interests -->
    <div class="section-header">BUSINESS INTERESTS AND FINANCIAL CONNECTIONS</div>
    <div class="section-subtitle">
        (of Declarant/Declarant's spouse; Ownership/Owning Shareholder (10 percent of total))
    </div>

    <div class="checkbox-group">
        <span class="checkbox {{ $saln->has_business_interests ? 'checked' : '' }}">{{ $saln->has_business_interests ? '✓' : '' }}</span> I/We have business interest or financial connection.<br>
        <span class="checkbox {{ !$saln->has_business_interests ? 'checked' : '' }}">{{ !$saln->has_business_interests ? '✓' : '' }}</span> I/We do not have any business interest or financial connection.
    </div>

    <table class="business-table">
        <thead>
            <tr>
                <th style="width: 25%;">NAME OF ENTITY/BUSINESS ENTERPRISE</th>
                <th style="width: 25%;">BUSINESS ADDRESS</th>
                <th style="width: 25%;">NATURE OF BUSINESS INTEREST &/OR FINANCIAL CONNECTION</th>
                <th style="width: 25%;">DATE OF ACQUISITION OF INTEREST OR CONNECTION</th>
            </tr>
        </thead>
        <tbody>
            @forelse($saln->businessInterests as $business)
                <tr class="compact-row">
                    <td class="left-align filled-value">{{ $business->name_of_entity }}</td>
                    <td class="left-align filled-value">{{ $business->business_address }}</td>
                    <td class="left-align filled-value">{{ $business->nature_of_business_interest }}</td>
                    <td class="filled-value">{{ \Carbon\Carbon::parse($business->date_of_acquisition)->format('m/d/Y') }}</td>
                </tr>
            @empty
                @for($i = 0; $i < 2; $i++)
                    <tr class="compact-row">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            @endforelse

            @if($saln->businessInterests->count() > 0 && $saln->businessInterests->count() < 2)
                @for($i = $saln->businessInterests->count(); $i < 2; $i++)
                    <tr class="compact-row">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>

    <!-- Relatives in Government -->
    <div class="section-header">RELATIVES IN THE GOVERNMENT SERVICE</div>
    <div class="section-subtitle">
        (Within the Fourth Degree of Consanguinity or Affinity, include also Batas Batas and Pare)
    </div>

    <div class="checkbox-group">
        <span class="checkbox {{ !$saln->has_relatives_in_government ? 'checked' : '' }}">{{ !$saln->has_relatives_in_government ? '✓' : '' }}</span> I/We do not know of any relative in the government service.
    </div>

    <table class="relatives-table">
        <thead>
            <tr>
                <th style="width: 25%;">NAME OF RELATIVE</th>
                <th style="width: 20%;">RELATIONSHIP</th>
                <th style="width: 25%;">POSITION</th>
                <th style="width: 30%;">NAME OF AGENCY/OFFICE AND ADDRESS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($saln->relativesInGovernment as $relative)
                <tr class="compact-row">
                    <td class="left-align filled-value">{{ $relative->name_of_relative }}</td>
                    <td class="filled-value">{{ $relative->relationship }}</td>
                    <td class="left-align filled-value">{{ $relative->position }}</td>
                    <td class="left-align filled-value">{{ $relative->name_of_agency_office_and_address }}</td>
                </tr>
            @empty
                @for($i = 0; $i < 2; $i++)
                    <tr class="compact-row">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            @endforelse

            @if($saln->relativesInGovernment->count() > 0 && $saln->relativesInGovernment->count() < 2)
                @for($i = $saln->relativesInGovernment->count(); $i < 2; $i++)
                    <tr class="compact-row">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>

    <!-- Certification -->
    <div class="certification">
        I hereby certify that these are true and correct statements of my assets, liabilities, net worth, business interests and financial connections, including those of my spouse and unmarried children below eighteen (18) years of age living in my household, and that to the best of my knowledge, the above-enumerated are names of my relatives in the government within the fourth civil degree of consanguinity or affinity.
    </div>

    <div class="certification">
        I hereby authorize the Ombudsman or his/her duly authorized representative to obtain and secure from all appropriate government agencies, including the Bureau of Internal Revenue such documents that may show my assets, liabilities, net worth, business interests and financial connections, to include those of my spouse and unmarried children below 18 years of age living with me in my household covering previous years to include the year I first assumed office in government.
    </div>

    <div style="margin: 15px 0; font-size: 9px;">
        Date: <span style="border-bottom: 1px solid #000; padding: 2px 50px;">{{ $saln->created_at ? $saln->created_at->format('F d, Y') : '' }}</span>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-left">
            <div class="signature-line"></div>
            <div class="signature-label">(Signature of Declarant)</div>
            <div class="id-info">
                Government Issued ID: <div class="id-line">{{ $saln->government_issued_id ?? '' }}</div>
                ID No.: <div class="id-line">{{ $saln->id_number ?? '' }}</div>
                Date Issued: <div class="id-line">{{ $saln->date_issued ? \Carbon\Carbon::parse($saln->date_issued)->format('F d, Y') : '' }}</div>
            </div>
        </div>
        <div class="signature-right">
            <div class="signature-line"></div>
            <div class="signature-label">(Signature of Co-Declarant/Spouse)</div>
            <div class="id-info">
                Government Issued ID: <div class="id-line">{{ $saln->spouse_government_issued_id ?? '' }}</div>
                ID No.: <div class="id-line">{{ $saln->spouse_id_number ?? '' }}</div>
                Date Issued: <div class="id-line">{{ $saln->spouse_date_issued ? \Carbon\Carbon::parse($saln->spouse_date_issued)->format('F d, Y') : '' }}</div>
            </div>
        </div>
    </div>

    <!-- Oath Section -->
    <div class="oath-section">
        <div style="text-decoration: underline; font-weight: bold; margin-bottom: 15px; font-size: 10px;">
            SUBSCRIBED AND SWORN TO before me this _____ day of ________, affiant exhibiting to me the above-stated government issued identification card.
        </div>
        <div class="oath-signature">
            <div style="height: 25px;"></div>
            <div style="border-top: 1px solid #000; width: 250px; margin: 0 auto;"></div>
            <div style="margin-top: 5px; font-size: 10px;"><strong>(Person Administering Oath)</strong></div>
        </div>
    </div>

    <div class="page-number">Page 2 of 2</div>

    <!-- Notes Section -->
    <div class="note-section">
        <p><strong>NOTE:</strong> Violation of this law is punishable by a fine not exceeding five thousand pesos (₱5,000) or imprisonment not exceeding one (1) year, or both, at the discretion of the court (Section 11, R.A. 6713).</p>
        <p><strong>REMINDER:</strong> Any misrepresentation or non-disclosure of any material fact required to be stated herein shall constitute perjury under Article 183 of the Revised Penal Code and shall be punished accordingly.</p>
    </div>
</body>
</html>
