<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application for Leave - Print</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 8px;
            color: #000;
            font-size: 10px;
            line-height: 1.3;
            height: calc(100vh - 16px);
            display: flex;
            flex-direction: column;
        }

        .header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 10px;
            position: relative;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .civil-service-note {
            font-size: 9px;
            text-align: left;
            position: absolute;
            left: 0;
            top: 0;
        }

        .center-section {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-grow: 1;
        }

        .logo {
            width: 45px;
            height: 45px;
            margin-right: 10px;
            flex-shrink: 0;
            object-fit: contain;
        }

        .header-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .company-info {
            font-size: 10px;
            margin-bottom: 6px;
            text-align: center;
            line-height: 1.2;
        }

        .form-title {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            margin: 6px 0;
        }

        .form-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .fixed-table {
            flex-shrink: 0;
        }

        .details-table {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .details-table table {
            height: 100%;
        }

        .details-table .leave-details-row {
            height: 100%;
        }

        .action-table {
            flex-shrink: 0;
        }

        td, th {
            border: 1px solid #000;
            padding: 2px 4px;
            vertical-align: top;
            font-size: 9px;
        }

        .field-label {
            font-weight: bold;
            font-size: 9px;
        }

        .checkbox {
            width: 10px;
            height: 10px;
            border: 1px solid #000;
            display: inline-block;
            text-align: center;
            margin-right: 3px;
            font-size: 7px;
            line-height: 8px;
            vertical-align: middle;
        }

        .checkbox.checked::before {
            content: '✓';
        }

        .underline {
            border-bottom: 1px solid #000;
            min-height: 14px;
            display: inline-block;
            min-width: 80px;
            padding: 0 5px;
        }

        .leave-type-item {
            margin-bottom: 2px;
            font-size: 12px;
            line-height: 1.3;
        }

        .section-header {
            text-align: center;
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 10px;
            padding: 3px;
        }

        .credits-table {
            width: 100%;
            margin-top: 4px;
            font-size: 8px;
        }

        .credits-table td {
            text-align: center;
            padding: 2px;
        }

        .signature-area {
            text-align: center;
            padding: 4px;
            vertical-align: bottom;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            width: 100px;
            margin: 8px auto 2px;
            display: block;
        }

        .signature-label {
            font-size: 8px;
            margin-top: 2px;
        }

        .footer {
            flex-shrink: 0;
            text-align: center;
            font-size: 7px;
            margin-top: 3px;
        }

        .no-print-section {
            flex-shrink: 0;
            margin-top: 10px;
            text-align: center;
        }

        .main-sections .field-label {
            font-size: 10px;
            font-weight: bold;
        }

        .main-sections td {
            font-size: 10px;
            padding: 3px 4px;
        }

        .details-section-content {
            font-size: 12px;
        }

        .details-section-content .field-label {
            font-size: 13px;
            margin-bottom: 4px;
            font-weight: bold;
        }

        .details-section-content .leave-type-item {
            font-size: 12px;
            margin-bottom: 3px;
        }

        .details-section-content .leave-details-text {
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 8px;
        }

        .details-section-6b {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .action-section td {
            font-size: 8px;
            padding: 3px;
        }

        .action-section .field-label {
            font-size: 9px;
        }

        @page {
            size: A4;
            margin: 0.4in;
        }

        @media print {
            body {
                margin: 0;
                font-size: 9px;
                height: 100vh;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="civil-service-note">
            Civil Service Form No. 6<br>
            Revised 2020
        </div>
        <div class="center-section">
            <img src="{{ asset('images/ati_logo.png') }}" alt="ATI Logo" class="logo">
            <div class="header-content">
                <div class="company-info">
                    Republic of the Philippines<br>
                    <strong>AGRICULTURAL TRAINING INSTITUTE</strong><br>
                    Elliptical Road, Diliman, Quezon City 1101<br>
                    Email Address: ati@ati.da.gov.ph<br>
                    Fax No. 927-6373<br>
                    www.ati.da.gov.ph
                </div>
                <div class="form-title">APPLICATION FOR LEAVE</div>
            </div>
        </div>
    </div>

    <div class="form-content">
        <!-- Section 1 & 2: Office/Department and Name -->
        <div class="fixed-table main-sections">
            <table>
                <tr>
                    <td width="18%" class="field-label">1. OFFICE/DEPARTMENT</td>
                    <td width="27%">{{ $leaveApplication->office_department ?? '' }}</td>
                    <td width="8%" class="field-label">2. NAME:</td>
                    <td width="15%" class="field-label">(Last)</td>
                    <td width="16%" class="field-label">(First)</td>
                    <td width="16%" class="field-label">(Middle)</td>
                </tr>
                <tr>
                    <td style="height: 25px;"></td>
                    <td></td>
                    <td></td>
                    <td>{{ $leaveApplication->last_name ?? '' }}</td>
                    <td>{{ $leaveApplication->first_name ?? '' }}</td>
                    <td>{{ $leaveApplication->middle_name ?? '' }}</td>
                </tr>
            </table>
        </div>

        <!-- Section 3, 4, 5: Date, Position, Salary -->
        <div class="fixed-table main-sections">
            <table>
                <tr>
                    <td width="18%" class="field-label">3. DATE OF FILING</td>
                    <td width="27%" style="height: 25px;">{{ $leaveApplication->date_of_filing?->format('m/d/Y') ?? '' }}</td>
                    <td width="12%" class="field-label">4. POSITION</td>
                    <td width="23%">{{ $leaveApplication->position ?? '' }}</td>
                    <td width="10%" class="field-label">5. SALARY</td>
                    <td width="10%">{{ $leaveApplication->salary ? number_format($leaveApplication->salary, 2) : '' }}</td>
                </tr>
            </table>
        </div>

        <!-- Section 6: Details of Application - This will expand -->
        <div class="details-table">
            <table>
                <tr>
                    <td colspan="2" class="section-header">6. DETAILS OF APPLICATION</td>
                </tr>
                <tr class="leave-details-row">
                    <td width="50%" style="padding: 4px; vertical-align: top;" class="details-section-content">
                        <div class="field-label" style="margin-bottom: 3px;">6.A TYPE OF LEAVE TO BE AVAILED OF</div>

                        <div class="leave-type-item">
                            <span class="checkbox {{ $leaveApplication->type_of_leave == 'vacation_leave' ? 'checked' : '' }}"></span>
                            <strong>Vacation Leave</strong> (Sec 51, Rule XVI, Omnibus Rules Implementing E.O. No. 292)
                        </div>

                        <div class="leave-type-item">
                            <span class="checkbox {{ $leaveApplication->type_of_leave == 'mandatory_leave' ? 'checked' : '' }}"></span>
                            <strong>Mandatory/Forced Leave</strong> (Sec. 25, Rule XVI, Omnibus Rules Implementing E.O. No. 292)
                        </div>

                        <div class="leave-type-item">
                            <span class="checkbox {{ $leaveApplication->type_of_leave == 'sick_leave' ? 'checked' : '' }}"></span>
                            <strong>Sick Leave</strong> (Sec. 43, Rule XVI, Omnibus Rules Implementing E.O. No. 292)
                        </div>

                        <div class="leave-type-item">
                            <span class="checkbox {{ $leaveApplication->type_of_leave == 'maternity_leave' ? 'checked' : '' }}"></span>
                            <strong>Maternity Leave</strong> (R.A. No. 11210 / IRR issued by CSC, DOLE and SSS)
                        </div>

                        <div class="leave-type-item">
                            <span class="checkbox {{ $leaveApplication->type_of_leave == 'paternity_leave' ? 'checked' : '' }}"></span>
                            <strong>Paternity Leave</strong> (R.A. No. 8187 / CSC MC No. 71, s. 1998, as amended)
                        </div>

                        <div class="leave-type-item">
                            <span class="checkbox {{ $leaveApplication->type_of_leave == 'special_privilege_leave' ? 'checked' : '' }}"></span>
                            <strong>Special Privilege Leave</strong> (Sec. 21, Rule XVI, Omnibus Rules Implementing E.O. No. 292)
                        </div>

                        <div class="leave-type-item">
                            <span class="checkbox {{ $leaveApplication->type_of_leave == 'solo_parent_leave' ? 'checked' : '' }}"></span>
                            <strong>Solo Parent Leave</strong> (R.A. No. 8972 / CSC MC No. 8, s. 2004)
                        </div>

                        <div class="leave-type-item">
                            <span class="checkbox {{ $leaveApplication->type_of_leave == 'study_leave' ? 'checked' : '' }}"></span>
                            <strong>Study Leave</strong> (Sec. 68, Rule XVI, Omnibus Rules Implementing E.O. No. 292)
                        </div>

                        <div class="leave-type-item">
                            <span class="checkbox {{ $leaveApplication->type_of_leave == 'vawc_leave' ? 'checked' : '' }}"></span>
                            <strong>10-Day VAWC Leave</strong> (R.A. No. 9262 / CSC MC No. 15, s. 2005)
                        </div>

                        <div class="leave-type-item">
                            <span class="checkbox {{ $leaveApplication->type_of_leave == 'rehabilitation_privilege' ? 'checked' : '' }}"></span>
                            <strong>Rehabilitation Privilege</strong> (Sec. 55, Rule XVI, Omnibus Rules Implementing E.O. No. 292)
                        </div>

                        <div class="leave-type-item">
                            <span class="checkbox {{ $leaveApplication->type_of_leave == 'special_leave_women' ? 'checked' : '' }}"></span>
                            <strong>Special Leave Benefits for Women</strong> (R.A. No. 9710 / CSC MC No. 25, s. 2010)
                        </div>

                        <div class="leave-type-item">
                            <span class="checkbox {{ $leaveApplication->type_of_leave == 'emergency_leave' ? 'checked' : '' }}"></span>
                            <strong>Special Emergency (Calamity) Leave</strong> (CSC MC No. 2, s. 2012, as amended)
                        </div>

                        <div class="leave-type-item">
                            <span class="checkbox {{ $leaveApplication->type_of_leave == 'adoption_leave' ? 'checked' : '' }}"></span>
                            <strong>Adoption Leave</strong> (R.A. No. 8552)
                        </div>

                        <div style="margin-top: 5px; font-size: 12px;">
                            <strong>Others:</strong>
                            <span class="underline" style="width: 120px;">{{ $leaveApplication->others_specify ?? '' }}</span>
                        </div>
                    </td>
                    <td width="50%" style="padding: 6px; vertical-align: top;" class="details-section-content">
                        <div class="details-section-6b">
                            <div>
                                <div class="field-label" style="margin-bottom: 6px;">6.B DETAILS OF LEAVE</div>

                                <div class="leave-details-text">
                                    <strong>In case of Vacation/Special Privilege Leave:</strong><br>
                                    <span class="checkbox {{ $leaveApplication->vacation_location == 'within_philippines' ? 'checked' : '' }}"></span> Within the Philippines
                                    <span class="underline" style="width: 70px;">{{ $leaveApplication->vacation_location == 'within_philippines' ? ($leaveApplication->vacation_location_specify ?? '') : '' }}</span><br>
                                    <span class="checkbox {{ $leaveApplication->vacation_location == 'abroad' ? 'checked' : '' }}"></span> Abroad (Specify)
                                    <span class="underline" style="width: 70px;">{{ $leaveApplication->abroad_specify ?? '' }}</span>
                                </div>

                                <div class="leave-details-text">
                                    <strong>In case of Sick Leave:</strong><br>
                                    <span class="checkbox {{ $leaveApplication->sick_leave_location == 'hospital' ? 'checked' : '' }}"></span> In Hospital (Specify Illness)
                                    <span class="underline" style="width: 65px;">{{ $leaveApplication->sick_leave_location == 'hospital' ? ($leaveApplication->illness_specify ?? '') : '' }}</span><br>
                                    <span class="checkbox {{ $leaveApplication->sick_leave_location == 'outpatient' ? 'checked' : '' }}"></span> Out Patient (Specify Illness)
                                    <span class="underline" style="width: 65px;">{{ $leaveApplication->sick_leave_location == 'outpatient' ? ($leaveApplication->illness_specify ?? '') : '' }}</span>
                                </div>

                                <div class="leave-details-text">
                                    <strong>In case of Special Leave Benefits for Women:</strong><br>
                                    (Specify Illness) <span class="underline" style="width: 90px;">{{ $leaveApplication->women_illness_specify ?? '' }}</span>
                                </div>

                                <div class="leave-details-text">
                                    <strong>In case of Study Leave:</strong><br>
                                    <span class="checkbox {{ in_array('masters_degree', $leaveApplication->study_leave_purpose ?? []) ? 'checked' : '' }}"></span> Completion of Master's Degree<br>
                                    <span class="checkbox {{ in_array('bar_board_exam', $leaveApplication->study_leave_purpose ?? []) ? 'checked' : '' }}"></span> BAR/Board Examination Review
                                </div>

                                <div class="leave-details-text">
                                    <strong>Other purpose:</strong><br>
                                    <span class="checkbox {{ in_array('monetization', $leaveApplication->other_purpose ?? []) ? 'checked' : '' }}"></span> Monetization of Leave Credits<br>
                                    <span class="checkbox {{ in_array('terminal_leave', $leaveApplication->other_purpose ?? []) ? 'checked' : '' }}"></span> Terminal Leave
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Section 6.C and 6.D -->
        <div class="fixed-table main-sections">
            <table>
                <tr>
                    <td width="50%" class="field-label">6.C NUMBER OF WORKING DAYS APPLIED FOR</td>
                    <td width="50%" class="field-label">6.D COMMUTATION</td>
                </tr>
                <tr>
                    <td style="padding: 8px;">
                        <div style="text-align: center; border-bottom: 1px solid #000; min-height: 25px; margin-bottom: 10px; line-height: 25px;">
                            {{ $leaveApplication->number_of_working_days ?? '' }}
                        </div>
                        <div class="field-label" style="margin-bottom: 5px;">INCLUSIVE DATES</div>
                        <div style="text-align: center; border-bottom: 1px solid #000; min-height: 20px; line-height: 20px;">
                            @if($leaveApplication->leave_date_from && $leaveApplication->leave_date_to)
                                {{ $leaveApplication->leave_date_from->format('m/d/Y') }} - {{ $leaveApplication->leave_date_to->format('m/d/Y') }}
                            @endif
                        </div>
                    </td>
                    <td style="padding: 4px; font-size: 10px; vertical-align: top;">
                        <div style="margin-bottom: 8px;">
                            <span class="checkbox {{ $leaveApplication->commutation == 'not_requested' ? 'checked' : '' }}"></span> Not Requested
                        </div>
                        <div style="margin-bottom: 15px;">
                            <span class="checkbox {{ $leaveApplication->commutation == 'requested' ? 'checked' : '' }}"></span> Requested
                        </div>
                        <div class="signature-area" style="margin-top: 10px;">
                            <div class="signature-line" style="width: 120px;"></div>
                            <div class="signature-label">(Signature of Applicant)</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Section 7: Details of Action on Application -->
        <div class="action-table action-section">
            <table>
                <tr>
                    <td colspan="4" class="section-header">7. DETAILS OF ACTION ON APPLICATION</td>
                </tr>
                <tr>
                    <td width="35%" class="field-label">7.A CERTIFICATION OF LEAVE CREDITS</td>
                    <td width="20%" class="field-label">7.B RECOMMENDATION</td>
                    <td width="25%" class="field-label">7.C APPROVED FOR:</td>
                    <td width="20%" class="field-label">7.D DISAPPROVED DUE TO:</td>
                </tr>
                <tr>
                    <td rowspan="3" style="padding: 4px;">
                        As of <span class="underline" style="width: 60px;">{{ $leaveApplication->as_of_date?->format('m/d/Y') ?? '' }}</span>

                        <table class="credits-table" border="1">
                            <tr>
                                <td></td>
                                <td class="field-label">Vacation Leave</td>
                                <td class="field-label">Sick Leave</td>
                            </tr>
                            <tr>
                                <td class="field-label">Total Earned</td>
                                <td>{{ $leaveApplication->vacation_leave_total_earned ? number_format($leaveApplication->vacation_leave_total_earned, 1) : '' }}</td>
                                <td>{{ $leaveApplication->sick_leave_total_earned ? number_format($leaveApplication->sick_leave_total_earned, 1) : '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label">Less this application</td>
                                <td>{{ $leaveApplication->vacation_leave_less_application ? number_format($leaveApplication->vacation_leave_less_application, 1) : '' }}</td>
                                <td>{{ $leaveApplication->sick_leave_less_application ? number_format($leaveApplication->sick_leave_less_application, 1) : '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label">Balance</td>
                                <td>{{ $leaveApplication->vacation_leave_balance ? number_format($leaveApplication->vacation_leave_balance, 1) : '' }}</td>
                                <td>{{ $leaveApplication->sick_leave_balance ? number_format($leaveApplication->sick_leave_balance, 1) : '' }}</td>
                            </tr>
                        </table>

                        <div class="signature-area" style="margin-top: 15px;">
                            <div class="signature-line" style="width: 80px;"></div>
                            <div class="signature-label">(Authorized Officer)</div>
                        </div>
                    </td>
                    <td style="padding: 4px;">
                        <span class="checkbox {{ $leaveApplication->recommendation == 'approved' ? 'checked' : '' }}"></span> For approval<br><br>
                        <span class="checkbox {{ $leaveApplication->recommendation == 'disapproved' ? 'checked' : '' }}"></span> For disapproval due to<br>
                        <span class="underline" style="width: 100%; display: block; margin-top: 2px;">{{ $leaveApplication->disapproval_reason ?? '' }}</span>

                        <div class="signature-area" style="margin-top: 15px;">
                            <div class="signature-line" style="width: 80px;"></div>
                            <div class="signature-label">(Authorized Officer)</div>
                        </div>
                    </td>
                    <td style="padding: 4px;">
                        <span class="underline" style="width: 40px;">{{ $leaveApplication->approved_days_with_pay ?? '' }}</span> days with pay<br><br>
                        <span class="underline" style="width: 40px;">{{ $leaveApplication->approved_days_without_pay ?? '' }}</span> days without pay<br><br>
                        <span class="underline" style="width: 40px;">{{ $leaveApplication->approved_others ?? '' }}</span> others (Specify)<br>
                        <br><br>
                        <div class="signature-area" style="margin-top: 15px;">
                            <div class="signature-line" style="width: 80px;">{{ $leaveApplication->authorized_officer ?? '' }}</div>
                            <div class="signature-label">(Authorized Officer)</div>
                            <div class="signature-line" style="width: 60px; margin-top: 10px;">{{ $leaveApplication->date_approved_disapproved?->format('m/d/Y') ?? '' }}</div>
                            <div class="signature-label">(Date)</div>
                        </div>
                    </td>
                    <td rowspan="3" style="padding: 4px;">
                        <span class="underline" style="width: 100%; display: block; margin-bottom: 6px;">{{ $leaveApplication->disapproved_reason ?? '' }}</span>

                        <div class="signature-area" style="margin-top: 15px;">
                            <div class="signature-line" style="width: 80px;">{{ $leaveApplication->authorized_officer ?? '' }}</div>
                            <div class="signature-label">(Authorized Officer)</div>
                            <div class="signature-line" style="width: 60px; margin-top: 10px;">{{ $leaveApplication->date_approved_disapproved?->format('m/d/Y') ?? '' }}</div>
                            <div class="signature-label">(Date)</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footer">
        ATI-QF/AHRMO-09 Rev.03 Effectivity Date: July 09, 2021 Director
    </div>

    <div class="no-print no-print-section">
        <button onclick="window.print()" style="padding: 8px 16px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px;">
            Print Form
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; background-color: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Close
        </button>
    </div>
</body>
</html>
