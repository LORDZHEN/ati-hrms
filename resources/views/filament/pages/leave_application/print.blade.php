<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <title>Application for Leave - Print</title> --}}
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            width: 210mm;
            height: 297mm;
            font-family: Arial, sans-serif;
            color: #000;
            background: #fff;
        }

        .page {
            width: 210mm;
            height: 297mm;
            padding: 8mm 10mm;
            position: relative;
        }

        /* ─── HEADER ─── */
        .header {
            position: relative;
            margin-bottom: 3mm;
        }

        .cs-note {
            position: absolute;
            left: 0;
            top: 0;
            font-size: 7.5pt;
            line-height: 1.3;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding-top: 3px;
        }

        .logo {
            width: 50px;
            height: 50px;
            flex-shrink: 0;
            object-fit: contain;
        }

        .agency-info {
            text-align: center;
        }

        .agency-text {
            font-size: 8.5pt;
            line-height: 1.35;
        }

        .form-title {
            font-size: 13pt;
            font-weight: bold;
            letter-spacing: 1.2px;
            margin-top: 3px;
        }

        /* ─── TABLES ─── */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            border: 1pt solid #000;
            padding: 3px 5px;
            vertical-align: top;
            font-size: 8.5pt;
            line-height: 1.3;
        }

        /* ─── LABELS ─── */
        .label { font-weight: bold; font-size: 8pt; }
        .value { font-weight: normal; }

        /* ─── SECTION BANDS ─── */
        .section-band {
            background: #d9d9d9;
            font-weight: bold;
            font-size: 9pt;
            text-align: center;
            padding: 3px;
            letter-spacing: 0.3px;
        }

        /* ─── CHECKBOX - SOLID BLACK WHEN CHECKED ─── */
        .checkbox {
            display: inline-block;
            width: 10pt;
            height: 10pt;
            border: 1.5pt solid #000;
            margin-right: 3px;
            vertical-align: middle;
            position: relative;
            background: #fff;
        }

        .checkbox.checked {
            background-color: #000 !important; /* Solid black fill */
            border: 1.5pt solid #000 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        .checkbox.checked::after {
            content: '✓';
            color: #fff !important;
            font-size: 8pt;
            font-weight: 900;
            position: absolute;
            top: -2px;
            left: 0.5px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* ─── UNDERLINES ─── */
        .underline {
            display: inline-block;
            border-bottom: 0.8pt solid #000;
            min-width: 45px;
            padding: 0 2px;
        }

        .underline-full {
            display: block;
            border-bottom: 0.8pt solid #000;
            width: 100%;
            margin-top: 3px;
            min-height: 12px;
        }

        /* ─── ROW HEIGHTS - REDUCED TO FIT ONE PAGE ─── */
        .row-header td { min-height: 16mm; }
        .row-info td { min-height: 12mm; }
        .row-section6-main td { min-height: 88mm; vertical-align: top; }
        .row-section6-bottom td { min-height: 22mm; }
        .row-section7-main td { min-height: 62mm; vertical-align: top; }
        .row-section7-bottom td { min-height: 24mm; }
        .row-final td { min-height: 14mm; text-align: center; vertical-align: middle; }

        /* ─── LEAVE TYPES ─── */
        .leave-item {
            margin-bottom: 1.5px;
            font-size: 7.5pt;
            line-height: 1.3;
            display: flex;
            align-items: flex-start;
        }

        /* ─── DETAILS BLOCKS ─── */
        .detail-section {
            margin-bottom: 6px;
            font-size: 7.5pt;
            line-height: 1.35;
        }

        .detail-section em {
            font-style: italic;
        }

        /* ─── CREDITS TABLE ─── */
        .credits-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .credits-table td {
            border: 0.8pt solid #000;
            padding: 2px 3px;
            text-align: center;
            font-size: 7.5pt;
        }

        .credits-table .label-cell {
            text-align: left;
            font-style: italic;
        }

        .credits-table .header-cell {
            font-weight: bold;
            background: #e9e9e9;
        }

        /* ─── SIGNATURES ─── */
        .signature-area {
            text-align: center;
            margin-top: 10px;
        }

        .signature-line {
            display: inline-block;
            border-bottom: 0.8pt solid #000;
            width: 140px;
            min-height: 12px;
        }

        .signature-label {
            font-size: 6.5pt;
            margin-top: 1px;
            display: block;
        }

        /* ─── FOOTER ─── */
        .footer {
            margin-top: 3mm;
            text-align: center;
            font-size: 6.5pt;
        }

        @page { size: A4; margin: 0; }

        @media print {
            .no-print { display: none !important; }
            html, body {
                width: 210mm;
                height: 297mm;
                overflow: hidden;
            }
            .page {
                page-break-after: avoid;
                page-break-inside: avoid;
            }

            /* Force checkbox colors to print */
            .checkbox.checked {
                background-color: #000 !important;
                background: #000 !important;
                border: 1.5pt solid #000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            .checkbox.checked::after {
                color: #fff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>
<div class="page">

    {{-- HEADER --}}
    <div class="header">
        <div class="cs-note">
            Civil Service Form No. 6<br>
            Revised 2020
        </div>
        <div class="header-content">
            <img src="{{ asset('images/ati_logo.png') }}" alt="Logo" class="logo">
            <div class="agency-info">
                <div class="agency-text">
                    Republic of the Philippines<br>
                    <strong>AGRICULTURAL TRAINING INSTITUTE</strong><br>
                    Elliptical Road, Diliman, Quezon City 1101<br>
                    Email Address: ati@ati.da.gov.ph | Fax No. 927-6373 | www.ati.da.gov.ph
                </div>
                <div class="form-title">APPLICATION FOR LEAVE</div>
            </div>
        </div>
    </div>

    {{-- SECTION 1 & 2 --}}
    <table>
        <tr class="row-header">
            <td width="25%">
                <span class="label">1. OFFICE/DEPARTMENT</span><br>
                <span class="value">{{ $leaveApplication->employee?->department ?? '' }}</span>
            </td>
            <td width="8%" style="text-align:center; vertical-align:middle;">
                <span class="label">2.<br>NAME:</span>
            </td>
            <td width="22%">
                <span class="label">(Last)</span><br>
                <span class="value">{{ $leaveApplication->employee?->last_name ?? '' }}</span>
            </td>
            <td width="22%">
                <span class="label">(First)</span><br>
                <span class="value">{{ $leaveApplication->employee?->first_name ?? '' }}</span>
            </td>
            <td width="23%">
                <span class="label">(Middle)</span><br>
                <span class="value">{{ $leaveApplication->employee?->middle_name ?? '' }}</span>
            </td>
        </tr>
    </table>

    {{-- SECTION 3, 4, 5 --}}
    <table>
        <tr class="row-info">
            <td width="25%">
                <span class="label">3. DATE OF FILING</span><br>
                <span class="value">{{ $leaveApplication->date_of_filing?->format('m/d/Y') ?? '' }}</span>
            </td>
            <td width="37.5%">
                <span class="label">4. POSITION</span><br>
                <span class="value">{{ $leaveApplication->employee?->position ?? '' }}</span>
            </td>
            <td width="37.5%">
                <span class="label">5. SALARY</span><br>
                <span class="value">{{ $leaveApplication->employee?->salary ? number_format($leaveApplication->employee->salary, 2) : '' }}</span>
            </td>
        </tr>
    </table>

    {{-- SECTION 6 HEADER --}}
    <table><tr><td class="section-band">6. DETAILS OF APPLICATION</td></tr></table>

    {{-- SECTION 6.A & 6.B --}}
    <table>
        <tr class="row-section6-main">
            <td width="50%">
                <div class="label" style="margin-bottom: 4px;">6.A TYPE OF LEAVE TO BE AVAILED OF</div>

                @php
                $types = [
                    'vacation_leave' => 'Vacation Leave (Sec 51, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                    'mandatory_forced_leave' => 'Mandatory/Forced Leave(Sec. 25, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                    'sick_leave' => 'Sick Leave (Sec. 43, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                    'maternity_leave' => 'Maternity Leave (R.A. No. 11210 / IRR issued by CSC, DOLE and SSS)',
                    'paternity_leave' => 'Paternity Leave (R.A. No. 8187 / CSC MC No. 71, s. 1998, as amended)',
                    'special_privilege_leave' => 'Special Privilege Leave (Sec. 21, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                    'solo_parent_leave' => 'Solo Parent Leave (R.A. No. 8972 / CSC MC No. 8, s. 2004)',
                    'study_leave' => 'Study Leave (Sec. 68, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                    '10_day_vawc_leave' => '10-Day VAWC Leave (R.A. No. 9262 / CSC MC No. 15, s. 2005)',
                    'rehabilitation_privilege' => 'Rehabilitation Privilege (Sec. 55, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                    'special_leave_benefits_women' => 'Special Leave Benefits for Women (R.A. No. 9710 / CSC MC No. 25, s. 2010)',
                    'special_emergency_leave' => 'Special Emergency (Calamity) Leave (CSC MC No. 2, s. 2012, as amended)',
                    'adoption_leave' => 'Adoption Leave (R.A. No. 8552)',
                    'others' => 'Others:',
                ];
                @endphp

                @foreach($types as $key => $text)
                <div class="leave-item">
                    <span class="checkbox {{ $leaveApplication->type_of_leave == $key ? 'checked' : '' }}"></span>
                    <span>{{ $text }}</span>
                </div>
                @endforeach

                <div style="margin-top: 3px;">
                    <span class="underline" style="min-width: 150px;">{{ $leaveApplication->other_leave_type ?? '' }}</span>
                </div>
            </td>

            <td width="50%">
                <div class="label" style="margin-bottom: 4px;">6.B DETAILS OF LEAVE</div>

                <div class="detail-section">
                    <em>In case of Vacation/Special Privilege Leave:</em><br>
                    <span class="checkbox {{ $leaveApplication->vacation_location == 'within_philippines' ? 'checked' : '' }}"></span>
                    Within the Philippines
                    <span class="underline" style="min-width: 55px;">{{ $leaveApplication->within_philippines_specify ?? '' }}</span><br>
                    <span class="checkbox {{ $leaveApplication->vacation_location == 'abroad' ? 'checked' : '' }}"></span>
                    Abroad (Specify)
                    <span class="underline" style="min-width: 75px;">{{ $leaveApplication->abroad_specify ?? '' }}</span>
                </div>

                <div class="detail-section">
                    <em>In case of Sick Leave:</em><br>
                    <span class="checkbox {{ $leaveApplication->sick_leave_location == 'in_hospital' ? 'checked' : '' }}"></span>
                    In Hospital (Specify Illness)
                    <span class="underline" style="min-width: 50px;">{{ $leaveApplication->hospital_illness_specify ?? '' }}</span><br>
                    <span class="checkbox {{ $leaveApplication->sick_leave_location == 'out_patient' ? 'checked' : '' }}"></span>
                    Out Patient (Specify Illness)
                    <span class="underline" style="min-width: 50px;">{{ $leaveApplication->outpatient_illness_specify ?? '' }}</span>
                </div>

                <div class="detail-section">
                    <em>In case of Special Leave Benefits for Women:</em><br>
                    (Specify Illness)
                    <span class="underline" style="min-width: 100px;">{{ $leaveApplication->women_illness_specify ?? '' }}</span>
                </div>

                <div class="detail-section">
                    <em>In case of Study Leave:</em><br>
                    <span class="checkbox {{ $leaveApplication->study_leave_purpose == 'masters_degree' ? 'checked' : '' }}"></span>
                    Completion of Master's Degree<br>
                    <span class="checkbox {{ $leaveApplication->study_leave_purpose == 'bar_board_review' ? 'checked' : '' }}"></span>
                    BAR/Board Examination Review
                </div>

                <div class="detail-section" style="margin-bottom: 0;">
                    <em>Other purpose:</em><br>
                    <span class="checkbox {{ $leaveApplication->other_purpose == 'monetization' ? 'checked' : '' }}"></span>
                    Monetization of Leave Credits<br>
                    <span class="checkbox {{ $leaveApplication->other_purpose == 'terminal_leave' ? 'checked' : '' }}"></span>
                    Terminal Leave
                </div>
            </td>
        </tr>
    </table>

    {{-- SECTION 6.C & 6.D --}}
    <table>
        <tr class="row-section6-bottom">
            <td width="50%">
                <div class="label">6.C NUMBER OF WORKING DAYS APPLIED FOR</div>
                <div style="margin-top: 3px;">
                    <span class="underline" style="min-width: 65px;">{{ $leaveApplication->number_of_working_days ?? '' }}</span>
                </div>
                <div style="margin-top: 6px; font-weight: bold; font-size: 8pt;">
                    INCLUSIVE DATES
                </div>
                <div style="margin-top: 2px;">
                    <span class="underline" style="min-width: 140px;">{{ $leaveApplication->inclusive_dates ?? '' }}</span>
                </div>
            </td>
            <td width="50%">
                <div class="label">6.D COMMUTATION</div>
                <div style="margin-top: 5px;">
                    <span class="checkbox {{ $leaveApplication->commutation == 'not_requested' ? 'checked' : '' }}"></span>
                    Not Requested
                </div>
                <div style="margin-top: 2px;">
                    <span class="checkbox {{ $leaveApplication->commutation == 'requested' ? 'checked' : '' }}"></span>
                    Requested
                </div>
                <div style="text-align: right; padding-right: 12px; margin-top: 8px;">
                    <span class="signature-line" style="width: 135px;"></span><br>
                    <span class="signature-label">(Signature of Applicant)</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- SECTION 7 HEADER --}}
    <table><tr><td class="section-band">7. DETAILS OF ACTION ON APPLICATION</td></tr></table>

    {{-- SECTION 7.A & 7.B --}}
    <table>
        <tr class="row-section7-main">
            <td width="50%">
                <div class="label">7.A CERTIFICATION OF LEAVE CREDITS</div>
                <div style="margin-top: 3px; font-size: 8pt;">
                    As of <span class="underline" style="min-width: 75px;">{{ $leaveApplication->as_of_date ?? '' }}</span>
                </div>

                <table class="credits-table">
                    <tr>
                        <td class="header-cell" style="width: 45%;"></td>
                        <td class="header-cell">Vacation Leave</td>
                        <td class="header-cell">Sick Leave</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Total Earned</td>
                        <td>{{ $leaveApplication->vl_total_earned ?? '' }}</td>
                        <td>{{ $leaveApplication->sl_total_earned ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Less this application</td>
                        <td>{{ $leaveApplication->vl_less_application ?? '' }}</td>
                        <td>{{ $leaveApplication->sl_less_application ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Balance</td>
                        <td>{{ $leaveApplication->employee?->vl_balance ?? '' }}</td>
                        <td>{{ $leaveApplication->employee?->sl_balance ?? '' }}</td>
                    </tr>
                </table>

                <div class="signature-area">
                    <span class="signature-line"></span><br>
                    <span class="signature-label">(Authorized Officer)</span>
                </div>
            </td>

            <td width="50%">
                <div class="label">7.B RECOMMENDATION</div>
                <div style="margin-top: 6px;">
                    <span class="checkbox {{ $leaveApplication->status === 'approved' ? 'checked' : '' }}"></span>
                    For approval
                </div>
                <div style="margin-top: 4px;">
                    <span class="checkbox {{ $leaveApplication->status === 'disapproved' ? 'checked' : '' }}"></span>
                    For disapproval due to
                </div>
                <span class="underline-full">{{ $leaveApplication->disapproval_reason ?? '' }}</span>
                <span class="underline-full"></span>
                <span class="underline-full"></span>

                <div class="signature-area">
                    <span class="signature-line"></span><br>
                    <span class="signature-label">(Authorized Officer)</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- SECTION 7.C & 7.D --}}
    <table>
        <tr class="row-section7-bottom">
            <td width="50%">
                <div class="label">7.C APPROVED FOR:</div>
                <div style="margin-top: 3px;">
                    <span class="underline" style="min-width: 45px;">{{ $leaveApplication->approved_days_with_pay ?? '' }}</span> days with pay
                </div>
                <div style="margin-top: 2px;">
                    <span class="underline" style="min-width: 45px;">{{ $leaveApplication->approved_days_without_pay ?? '' }}</span> days without pay
                </div>
                <div style="margin-top: 2px;">
                    <span class="underline" style="min-width: 45px;">{{ $leaveApplication->approved_others ?? '' }}</span> others (Specify)
                </div>
            </td>
            <td width="50%">
                <div class="label">7.D DISAPPROVED DUE TO:</div>
                <span class="underline-full">{{ $leaveApplication->disapproval_detail ?? '' }}</span>
                <span class="underline-full"></span>
                <span class="underline-full"></span>
            </td>
        </tr>
    </table>

    {{-- FINAL SIGNATURE --}}
    <table>
        <tr class="row-final">
            <td>
                <span class="signature-line" style="width: 200px;">{{ $leaveApplication->authorized_officer ?? '' }}</span><br>
                <span class="signature-label">(Head of Agency / Authorized Official)</span>
            </td>
        </tr>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        ATI-QF/AHRMO-09 &nbsp;&nbsp; Rev.03 &nbsp;&nbsp; Effectivity Date: July 09, 2021 &nbsp;&nbsp; Director
    </div>

    {{-- PRINT BUTTONS --}}
    <div class="no-print" style="text-align: center; margin-top: 6mm;">
        <button onclick="window.print()"
            style="padding: 10px 25px; background: #28a745; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; margin-right: 10px;">
            Print Form
        </button>
        <button onclick="window.close()"
            style="padding: 10px 25px; background: #6c757d; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">
            Close
        </button>
    </div>

</div>
</body>
</html>
