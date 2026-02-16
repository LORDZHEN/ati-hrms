<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SALN - {{ $saln->declarant_family_name }}, {{ $saln->declarant_first_name }}</title>
    <style>
        @page {
            size: 8.5in 13in; /* Legal size */
            margin: 0.5in 0.75in;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.2;
            color: #000;
        }

        /* Header Styles */
        .header-date {
            text-align: right;
            font-size: 7pt;
            margin-bottom: 5px;
            line-height: 1.1;
        }

        .form-title {
            font-weight: bold;
            font-size: 13pt;
            text-align: center;
            margin: 5px 0;
            text-decoration: underline;
            letter-spacing: 0.3px;
        }

        .as-of-date {
            text-align: center;
            margin: 4px auto;
            font-size: 11pt;
            border-bottom: 1.5px solid #000;
            width: 300px;
            padding-bottom: 2px;
            font-weight: bold;
        }

        .required-by {
            text-align: center;
            font-size: 8pt;
            margin-bottom: 8px;
        }

        /* Checkbox Section */
        .checkbox-section {
            margin: 8px 0;
            font-size: 8.5pt;
            line-height: 1.4;
        }

        .checkbox-note {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 8pt;
        }

        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1.5px solid #000;
            text-align: center;
            margin-right: 4px;
            vertical-align: middle;
            font-size: 9pt;
            line-height: 10px;
            font-weight: bold;
        }

        .checked {
            background-color: #000;
            color: #fff;
        }

        /* Personal Information */
        .personal-info {
            margin: 10px 0;
            font-size: 8.5pt;
        }

        .info-row {
            display: flex;
            margin-bottom: 5px;
            align-items: baseline;
        }

        .info-label {
            font-weight: bold;
            width: 105px;
            flex-shrink: 0;
            font-size: 8pt;
        }

        .info-value {
            border-bottom: 1px solid #000;
            flex-grow: 1;
            min-height: 18px;
            padding: 1px 4px;
            margin-right: 12px;
            position: relative;
        }

        .name-labels {
            position: absolute;
            bottom: -12px;
            left: 0;
            right: 0;
            display: flex;
            gap: 6px;
            font-size: 6.5pt;
            font-style: italic;
        }

        .name-labels span {
            flex: 1;
            text-align: center;
        }

        /* Section Headers */
        .section-header {
            font-weight: bold;
            text-align: center;
            text-decoration: underline;
            margin: 10px 0 6px 0;
            font-size: 9.5pt;
        }

        .section-subtitle {
            text-align: center;
            font-style: italic;
            margin-bottom: 6px;
            font-size: 8pt;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
        }

        th, td {
            border: 1px solid #000;
            padding: 2px 3px;
            text-align: center;
            vertical-align: middle;
            font-size: 7.5pt;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 7pt;
            line-height: 1.1;
        }

        .left-align {
            text-align: left !important;
        }

        .compact-row {
            height: 20px;
        }

        .filled-value {
            font-weight: 500;
        }

        /* Subtotal and Total Rows */
        .subtotal-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .total-row {
            font-weight: bold;
            font-size: 9pt;
            text-align: right;
            margin: 5px 0;
            padding: 3px 0;
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
        }

        /* Net Worth Box */
        .net-worth-section {
            border: 2px solid #000;
            padding: 6px;
            margin: 8px 0;
            text-align: center;
            font-weight: bold;
            font-size: 9.5pt;
            background-color: #f5f5f5;
        }

        /* Certification */
        .certification {
            text-align: justify;
            font-size: 8pt;
            margin: 6px 0;
            line-height: 1.3;
        }

        /* Signature Section */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            gap: 25px;
        }

        .signature-box {
            width: 48%;
        }

        .signature-line {
            border-bottom: 1.5px solid #000;
            margin: 15px 0 4px 0;
            text-align: center;
            height: 20px;
        }

        .signature-label {
            text-align: center;
            font-size: 8pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .id-info {
            margin-top: 5px;
            font-size: 7.5pt;
        }

        .id-line {
            border-bottom: 1px solid #000;
            margin: 1px 0;
            min-height: 12px;
            padding: 1px 2px;
        }

        /* Oath Section */
        .oath-section {
            margin-top: 10px;
            text-align: center;
        }

        .oath-text {
            text-decoration: underline;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 8pt;
            line-height: 1.3;
        }

        .oath-signature {
            margin-top: 10px;
            text-align: center;
        }

        /* Page Number */
        .page-number {
            text-align: right;
            margin-top: 8px;
            font-size: 8pt;
            font-weight: bold;
        }

        /* Notes Section */
        .note-section {
            margin-top: 10px;
            border-top: 2px solid #000;
            padding-top: 5px;
            font-size: 7pt;
            line-height: 1.2;
        }

        /* Page Break */
        .page-break {
            page-break-before: always;
            page-break-inside: avoid;
        }

        /* Prevent page breaks inside important sections */
        .no-page-break {
            page-break-inside: avoid;
        }

        /* Print Button */
        .print-button {
            position: fixed;
            top: 15px;
            right: 15px;
            padding: 10px 20px;
            background-color: #1e40af;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: background-color 0.2s;
        }

        .print-button:hover {
            background-color: #1e3a8a;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
            }
        }

        /* Asset subsection styling */
        .asset-subsection {
            margin-top: 6px;
            margin-bottom: 5px;
        }

        .subsection-title {
            font-size: 8.5pt;
            font-weight: bold;
        }

        .subsection-description {
            font-size: 7.5pt;
            font-style: italic;
        }

        /* Spacing utilities */
        .mb-2 { margin-bottom: 2px; }
        .mb-4 { margin-bottom: 4px; }
        .mt-2 { margin-top: 2px; }
        .mt-4 { margin-top: 4px; }
    </style>
</head>
<body>
    <!-- Print Button -->
    <button class="print-button no-print" onclick="window.print()">🖨️ Print SALN</button>

    <!-- ===== PAGE 1 START ===== -->

    <!-- Header -->
    <div class="header-date">
        Revised as of January 2015<br>
        Per CSC Resolution No. 1500958<br>
        Promulgated on January 21, 2015
    </div>

    <div class="form-title">SWORN STATEMENT OF ASSETS, LIABILITIES AND NET WORTH</div>

    <div class="as-of-date">
        As of {{ $saln->as_of_date ? \Carbon\Carbon::parse($saln->as_of_date)->format('F d, Y') : '____________________' }}
    </div>

    <div class="required-by">(Required by R.A. 6713)</div>

    <!-- Filing Type -->
    <div class="checkbox-section">
        <div class="checkbox-note">
            Note: Husband and wife who are both public officials and employees may file the required statements jointly or separately.
        </div>
        <div>
            <span class="checkbox {{ $saln->joint_filing ? 'checked' : '' }}">{{ $saln->joint_filing ? '✓' : '' }}</span> Joint Filing &nbsp;&nbsp;
            <span class="checkbox {{ $saln->separate_filing ? 'checked' : '' }}">{{ $saln->separate_filing ? '✓' : '' }}</span> Separate Filing &nbsp;&nbsp;
            <span class="checkbox {{ $saln->not_applicable ? 'checked' : '' }}">{{ $saln->not_applicable ? '✓' : '' }}</span> Not Applicable
        </div>
    </div>

    <!-- Personal Information -->
    <div class="personal-info no-page-break">
        <!-- Declarant Name and Position -->
        <div class="info-row">
            <div class="info-label">DECLARANT:</div>
            <div class="info-value filled-value">
                {{ $saln->declarant_family_name }}, {{ $saln->declarant_first_name }} {{ $saln->declarant_middle_initial }}
                <div class="name-labels">
                    <span>(Family Name)</span>
                    <span>(First Name)</span>
                    <span>(M.I.)</span>
                </div>
            </div>
            <div class="info-label">POSITION:</div>
            <div class="info-value filled-value">{{ $saln->declarant_position ?? '' }}</div>
        </div>

        <!-- Address and Agency -->
        <div class="info-row" style="margin-top: 15px;">
            <div class="info-label">ADDRESS:</div>
            <div class="info-value filled-value">{{ $saln->user->full_address ?? '' }}</div>
            <div class="info-label">AGENCY/OFFICE:</div>
            <div class="info-value filled-value">{{ $saln->declarant_agency_office ?? '' }}</div>
        </div>

        <!-- Office Address -->
        <div class="info-row">
            <div style="width: 105px;"></div>
            <div style="flex-grow: 1; margin-right: 12px;"></div>
            <div class="info-label">OFFICE ADDRESS:</div>
            <div class="info-value filled-value">{{ $saln->declarant_office_address ?? '' }}</div>
        </div>

        <div style="margin-top: 8px;"></div>

        <!-- Spouse Name and Position -->
        <div class="info-row">
            <div class="info-label">SPOUSE:</div>
            <div class="info-value filled-value">
                {{ $saln->spouse_family_name ? $saln->spouse_family_name . ', ' . $saln->spouse_first_name . ' ' . $saln->spouse_middle_initial : 'N/A' }}
                <div class="name-labels">
                    <span>(Family Name)</span>
                    <span>(First Name)</span>
                    <span>(M.I.)</span>
                </div>
            </div>
            <div class="info-label">POSITION:</div>
            <div class="info-value filled-value">{{ $saln->spouse_position ?? '' }}</div>
        </div>

        <!-- Spouse Agency and Office Address -->
        <div class="info-row" style="margin-top: 15px;">
            <div style="width: 105px;"></div>
            <div style="flex-grow: 1; margin-right: 12px;"></div>
            <div class="info-label">AGENCY/OFFICE:</div>
            <div class="info-value filled-value">{{ $saln->spouse_agency_office ?? '' }}</div>
        </div>

        <div class="info-row">
            <div style="width: 105px;"></div>
            <div style="flex-grow: 1; margin-right: 12px;"></div>
            <div class="info-label">OFFICE ADDRESS:</div>
            <div class="info-value filled-value">{{ $saln->spouse_office_address ?? '' }}</div>
        </div>
    </div>

    <!-- Children Section -->
    <div class="section-header">UNMARRIED CHILDREN BELOW EIGHTEEN (18) YEARS OF AGE LIVING IN DECLARANT'S HOUSEHOLD</div>

    <table class="no-page-break">
        <thead>
            <tr>
                <th style="width: 60%;">NAME</th>
                <th style="width: 25%;">DATE OF BIRTH</th>
                <th style="width: 15%;">AGE</th>
            </tr>
        </thead>
        <tbody>
            @php
                $childrenCount = $saln->children->count();
                $minRows = 3;
            @endphp

            @forelse($saln->children as $child)
                <tr class="compact-row">
                    <td class="left-align filled-value">{{ $child->name }}</td>
                    <td class="filled-value">{{ \Carbon\Carbon::parse($child->date_of_birth)->format('m/d/Y') }}</td>
                    <td class="filled-value">{{ $child->age }}</td>
                </tr>
            @empty
                @for($i = 0; $i < $minRows; $i++)
                    <tr class="compact-row">
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            @endforelse

            @if($childrenCount > 0 && $childrenCount < $minRows)
                @for($i = $childrenCount; $i < $minRows; $i++)
                    <tr class="compact-row">
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>

    <!-- Assets Section Header -->
    <div class="section-header">ASSETS, LIABILITIES AND NETWORTH</div>
    <div class="section-subtitle">
        (Including those of the spouse and unmarried children below eighteen (18) years of age living in declarant's household)
    </div>

    <div class="mt-2">
        <strong style="font-size: 9pt;">1. ASSETS</strong>

        <!-- Real Properties -->
        <div class="asset-subsection">
            <div>
                <span class="subsection-title">a. Real Properties</span>
                <span class="subsection-description">(Land, Buildings, and other Real Estate)</span>
            </div>

            <table>
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 14%;">DESCRIPTION</th>
                        <th rowspan="2" style="width: 9%;">KIND</th>
                        <th rowspan="2" style="width: 16%;">EXACT LOCATION</th>
                        <th rowspan="2" style="width: 11%;">ASSESSED VALUE</th>
                        <th rowspan="2" style="width: 13%;">CURRENT FAIR MARKET VALUE</th>
                        <th colspan="3" style="width: 37%;">ACQUISITION</th>
                    </tr>
                    <tr>
                        <th style="width: 9%;">YEAR</th>
                        <th style="width: 14%;">MODE</th>
                        <th style="width: 14%;">COST</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $realPropertiesCount = $saln->realProperties->count();
                        $minRealRows = 2;
                        $realSubtotal = $saln->realProperties->sum('current_fair_market_value');
                    @endphp

                    @forelse($saln->realProperties as $property)
                        <tr class="compact-row">
                            <td class="left-align filled-value">{{ $property->description }}</td>
                            <td class="filled-value">{{ $property->kind }}</td>
                            <td class="left-align filled-value">{{ $property->exact_location }}</td>
                            <td class="filled-value">₱{{ number_format($property->assessed_value, 2) }}</td>
                            <td class="filled-value">₱{{ number_format($property->current_fair_market_value, 2) }}</td>
                            <td class="filled-value">{{ $property->acquisition_year }}</td>
                            <td class="filled-value">{{ $property->mode_of_acquisition }}</td>
                            <td class="filled-value">₱{{ number_format($property->acquisition_cost, 2) }}</td>
                        </tr>
                    @empty
                        @for($i = 0; $i < $minRealRows; $i++)
                            <tr class="compact-row">
                                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                            </tr>
                        @endfor
                    @endforelse

                    @if($realPropertiesCount > 0 && $realPropertiesCount < $minRealRows)
                        @for($i = $realPropertiesCount; $i < $minRealRows; $i++)
                            <tr class="compact-row">
                                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                            </tr>
                        @endfor
                    @endif

                    <tr class="subtotal-row">
                        <td colspan="4" style="text-align: right;">Subtotal:</td>
                        <td>₱{{ number_format($realSubtotal, 2) }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Personal Properties -->
        <div class="asset-subsection">
            <div>
                <span class="subsection-title">b. Personal Properties</span>
                <span class="subsection-description">(Vehicles, Jewelry, Cash, Bank Deposits, etc.)</span>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 60%;">DESCRIPTION</th>
                        <th style="width: 20%;">YEAR ACQUIRED</th>
                        <th style="width: 20%;">ACQUISITION COST/AMOUNT</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $personalPropertiesCount = $saln->personalProperties->count();
                        $minPersonalRows = 3;
                        $personalSubtotal = $saln->personalProperties->sum('acquisition_cost');
                    @endphp

                    @forelse($saln->personalProperties as $property)
                        <tr class="compact-row">
                            <td class="left-align filled-value">{{ $property->description }}</td>
                            <td class="filled-value">{{ $property->year_acquired }}</td>
                            <td class="filled-value">₱{{ number_format($property->acquisition_cost, 2) }}</td>
                        </tr>
                    @empty
                        @for($i = 0; $i < $minPersonalRows; $i++)
                            <tr class="compact-row">
                                <td></td><td></td><td></td>
                            </tr>
                        @endfor
                    @endforelse

                    @if($personalPropertiesCount > 0 && $personalPropertiesCount < $minPersonalRows)
                        @for($i = $personalPropertiesCount; $i < $minPersonalRows; $i++)
                            <tr class="compact-row">
                                <td></td><td></td><td></td>
                            </tr>
                        @endfor
                    @endif

                    <tr class="subtotal-row">
                        <td colspan="2" style="text-align: right;">Subtotal:</td>
                        <td>₱{{ number_format($personalSubtotal, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="total-row">
            <strong>TOTAL ASSETS (a+b):</strong> ₱{{ number_format($saln->total_assets, 2) }}
        </div>
    </div>

    <!-- ===== PAGE BREAK ===== -->
    <div class="page-break"></div>

    <!-- ===== PAGE 2 START ===== -->

    <!-- Liabilities Section -->
    <div class="mt-2">
        <strong style="font-size: 9pt;">2. LIABILITIES</strong> <span style="font-size: 7.5pt; font-style: italic;">(Loans, Mortgages, and other Obligations)</span>

        <table style="margin-top: 6px;">
            <thead>
                <tr>
                    <th style="width: 35%;">NATURE</th>
                    <th style="width: 40%;">NAME OF CREDITORS</th>
                    <th style="width: 25%;">OUTSTANDING BALANCE</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $liabilitiesCount = $saln->liabilities->count();
                    $minLiabilityRows = 4;
                    $totalLiabilities = $saln->liabilities->sum('outstanding_balance');
                @endphp

                @forelse($saln->liabilities as $liability)
                    <tr class="compact-row">
                        <td class="left-align filled-value">{{ $liability->nature }}</td>
                        <td class="left-align filled-value">{{ $liability->name_of_creditors }}</td>
                        <td class="filled-value">₱{{ number_format($liability->outstanding_balance, 2) }}</td>
                    </tr>
                @empty
                    @for($i = 0; $i < $minLiabilityRows; $i++)
                        <tr class="compact-row">
                            <td></td><td></td><td></td>
                        </tr>
                    @endfor
                @endforelse

                @if($liabilitiesCount > 0 && $liabilitiesCount < $minLiabilityRows)
                    @for($i = $liabilitiesCount; $i < $minLiabilityRows; $i++)
                        <tr class="compact-row">
                            <td></td><td></td><td></td>
                        </tr>
                    @endfor
                @endif

                <tr class="subtotal-row">
                    <td colspan="2" style="text-align: right;">TOTAL LIABILITIES:</td>
                    <td>₱{{ number_format($totalLiabilities, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Net Worth Section -->
    <div class="net-worth-section no-page-break">
        NET WORTH = Total Assets less Total Liabilities = ₱{{ number_format($saln->net_worth, 2) }}
    </div>

    <!-- Business Interests -->
    <div class="section-header">BUSINESS INTERESTS AND FINANCIAL CONNECTIONS</div>
    <div class="section-subtitle">
        (of Declarant/Declarant's spouse; Ownership/Owning Shareholder (10 percent of total))
    </div>

    <div style="margin: 6px 0; font-size: 8.5pt;">
        <span class="checkbox {{ $saln->has_business_interests ? 'checked' : '' }}">{{ $saln->has_business_interests ? '✓' : '' }}</span> I/We have business interest or financial connection.<br>
        <span class="checkbox {{ $saln->no_business_interests ? 'checked' : '' }}">{{ $saln->no_business_interests ? '✓' : '' }}</span> I/We do not have any business interest or financial connection.
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25%;">NAME OF ENTITY/BUSINESS ENTERPRISE</th>
                <th style="width: 25%;">BUSINESS ADDRESS</th>
                <th style="width: 25%;">NATURE OF BUSINESS INTEREST &/OR FINANCIAL CONNECTION</th>
                <th style="width: 25%;">DATE OF ACQUISITION OF INTEREST OR CONNECTION</th>
            </tr>
        </thead>
        <tbody>
            @php
                $businessCount = $saln->businessInterests->count();
                $minBusinessRows = 2;
            @endphp

            @forelse($saln->businessInterests as $business)
                <tr class="compact-row">
                    <td class="left-align filled-value">{{ $business->name_of_entity }}</td>
                    <td class="left-align filled-value">{{ $business->business_address }}</td>
                    <td class="left-align filled-value">{{ $business->nature_of_business_interest }}</td>
                    <td class="filled-value">{{ \Carbon\Carbon::parse($business->date_of_acquisition)->format('m/d/Y') }}</td>
                </tr>
            @empty
                @for($i = 0; $i < $minBusinessRows; $i++)
                    <tr class="compact-row">
                        <td></td><td></td><td></td><td></td>
                    </tr>
                @endfor
            @endforelse

            @if($businessCount > 0 && $businessCount < $minBusinessRows)
                @for($i = $businessCount; $i < $minBusinessRows; $i++)
                    <tr class="compact-row">
                        <td></td><td></td><td></td><td></td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>

    <!-- Relatives in Government -->
    <div class="section-header">RELATIVES IN THE GOVERNMENT SERVICE</div>
    <div class="section-subtitle">
        (Within the Fourth Degree of Consanguinity or Affinity)
    </div>

    <div style="margin: 6px 0; font-size: 8.5pt;">
        <span class="checkbox {{ $saln->no_relatives_in_government ? 'checked' : '' }}">{{ $saln->no_relatives_in_government ? '✓' : '' }}</span> I/We do not know of any relative in the government service.
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25%;">NAME OF RELATIVE</th>
                <th style="width: 20%;">RELATIONSHIP</th>
                <th style="width: 25%;">POSITION</th>
                <th style="width: 30%;">NAME OF AGENCY/OFFICE AND ADDRESS</th>
            </tr>
        </thead>
        <tbody>
            @php
                $relativesCount = $saln->relativesInGovernment->count();
                $minRelativesRows = 2;
            @endphp

            @forelse($saln->relativesInGovernment as $relative)
                <tr class="compact-row">
                    <td class="left-align filled-value">{{ $relative->name_of_relative }}</td>
                    <td class="filled-value">{{ $relative->relationship }}</td>
                    <td class="left-align filled-value">{{ $relative->position }}</td>
                    <td class="left-align filled-value">{{ $relative->name_of_agency_office_address }}</td>
                </tr>
            @empty
                @for($i = 0; $i < $minRelativesRows; $i++)
                    <tr class="compact-row">
                        <td></td><td></td><td></td><td></td>
                    </tr>
                @endfor
            @endforelse

            @if($relativesCount > 0 && $relativesCount < $minRelativesRows)
                @for($i = $relativesCount; $i < $minRelativesRows; $i++)
                    <tr class="compact-row">
                        <td></td><td></td><td></td><td></td>
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

    <!-- Date Signed -->
    <div style="margin: 8px 0 10px 0; font-size: 8.5pt;">
        Date: <span style="border-bottom: 1px solid #000; padding: 2px 70px; display: inline-block;">{{ $saln->date_signed ? \Carbon\Carbon::parse($saln->date_signed)->format('F d, Y') : '' }}</span>
    </div>

    <!-- Signature Section -->
    <div class="signature-section no-page-break">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">(Signature of Declarant)</div>
            <div class="id-info">
                <strong>Government Issued ID:</strong>
                <div class="id-line">{{ $saln->declarant_id_type ?? '' }}</div>
                <strong>ID/License/Passport No.:</strong>
                <div class="id-line">{{ $saln->declarant_id_number ?? '' }}</div>
                <strong>Date/Place Issued:</strong>
                <div class="id-line">{{ $saln->declarant_id_issued ?? '' }}</div>
            </div>
        </div>

        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">(Signature of Co-Declarant/Spouse)</div>
            <div class="id-info">
                <strong>Government Issued ID:</strong>
                <div class="id-line">{{ $saln->spouse_id_type ?? '' }}</div>
                <strong>ID/License/Passport No.:</strong>
                <div class="id-line">{{ $saln->spouse_id_number ?? '' }}</div>
                <strong>Date/Place Issued:</strong>
                <div class="id-line">{{ $saln->spouse_id_issued ?? '' }}</div>
            </div>
        </div>
    </div>

    <!-- Oath Section -->
    <div class="oath-section no-page-break">
        <div class="oath-text">
            SUBSCRIBED AND SWORN TO before me this
            <span style="border-bottom: 1px solid #000; padding: 0 25px;">{{ $saln->subscribed_sworn_date ? \Carbon\Carbon::parse($saln->subscribed_sworn_date)->format('jS') : '_____' }}</span>
            day of
            <span style="border-bottom: 1px solid #000; padding: 0 50px;">{{ $saln->subscribed_sworn_date ? \Carbon\Carbon::parse($saln->subscribed_sworn_date)->format('F Y') : '_____________' }}</span>,
            affiant exhibiting to me the above-stated government issued identification card.
        </div>

        <div class="oath-signature">
            <div style="height: 20px;"></div>
            <div style="border-top: 1.5px solid #000; width: 250px; margin: 0 auto;"></div>
            <div style="margin-top: 4px; font-weight: bold; font-size: 8pt;">
                {{ $saln->person_administering_oath ?? '(Person Administering Oath)' }}
            </div>
        </div>
    </div>

    <!-- Notes Section -->
    <div class="note-section no-page-break">
        <p style="margin: 3px 0;"><strong>NOTE:</strong> Violation of this law is punishable by a fine not exceeding five thousand pesos (₱5,000) or imprisonment not exceeding one (1) year, or both, at the discretion of the court (Section 11, R.A. 6713).</p>
        <p style="margin: 3px 0;"><strong>REMINDER:</strong> Any misrepresentation or non-disclosure of any material fact required to be stated herein shall constitute perjury under Article 183 of the Revised Penal Code and shall be punished accordingly.</p>
    </div>

    <div class="page-number">Page 2 of 2</div>
</body>
</html>
