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
                    <td width="27%">{{ $leaveApplication->employee?->department ?? '' }}</td>
                    <td width="8%" class="field-label">2. NAME:</td>
                    <td width="15%" class="field-label">(Last)</td>
                    <td width="16%" class="field-label">(First)</td>
                    <td width="16%" class="field-label">(Middle)</td>
                </tr>
                <tr>
                    <td style="height: 25px;"></td>
                    <td></td>
                    <td></td>
                    <td>{{ $leaveApplication->employee?->last_name ?? '' }}</td>
                    <td>{{ $leaveApplication->employee?->first_name ?? '' }}</td>
                    <td>{{ $leaveApplication->employee?->middle_name ?? '' }}</td>
                </tr>
            </table>
        </div>

        <!-- Section 3, 4, 5: Date, Position, Salary -->
        <div class="fixed-table main-sections">
            <table>
                <tr>
                    <td width="18%" class="field-label">3. DATE OF FILING</td>
                    <td width="27%">{{ $leaveApplication->date_of_filing?->format('m/d/Y') ?? '' }}</td>
                    <td width="12%" class="field-label">4. POSITION</td>
                    <td width="23%">{{ $leaveApplication->employee?->position ?? '' }}</td>
                    <td width="10%" class="field-label">5. SALARY</td>
                    <td width="10%">{{ $leaveApplication->employee?->salary ? number_format($leaveApplication->employee->salary, 2) : '' }}</td>
                </tr>
            </table>
        </div>

        <!-- Section 6 -->
        <div class="details-table">
            <table>
                <tr>
                    <td colspan="2" class="section-header">6. DETAILS OF APPLICATION</td>
                </tr>
                <tr class="leave-details-row">
                    <!-- 6.A -->
                    <td width="50%" style="padding: 4px; vertical-align: top;" class="details-section-content">
                        <div class="field-label" style="margin-bottom: 3px;">6.A TYPE OF LEAVE TO BE AVAILED OF</div>

                        @php
                        $leaveTypes = [
                            'vacation_leave' => 'Vacation Leave (Sec 51, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                            'mandatory_forced_leave' => 'Mandatory/Forced Leave (Sec. 25, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                            'sick_leave' => 'Sick Leave (Sec. 43, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                            'maternity_leave' => 'Maternity Leave (R.A. No. 11210 / IRR issued by CSC, DOLE and SSS)',
                            'paternity_leave' => 'Paternity Leave (R.A. No. 8187 / CSC MC No. 71, s. 1998, as amended)',
                            'special_privilege_leave' => 'Special Privilege Leave (Sec. 21, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                            'solo_parent_leave' => 'Solo Parent Leave (R.A. No. 8972 / CSC MC No. 8, s. 2004)',
                            'study_leave' => 'Study Leave (Sec. 68, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                            '10_day_vawc_leave' => '10-Day VAWC Leave (R.A. No. 9262 / CSC MC No. 15, s. 2005)',
                            'rehabilitation_privilege' => 'Rehabilitation Privilege (Sec. 55, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                            'special_leave_benefits_for_women' => 'Special Leave Benefits for Women (R.A. No. 9710 / CSC MC No. 25, s. 2010)',
                            'special_emergency_leave' => 'Special Emergency (Calamity) Leave (CSC MC No. 2, s. 2012, as amended)',
                            'adoption_leave' => 'Adoption Leave (R.A. No. 8552)',
                            'others' => 'Others',
                        ];
                        @endphp

                        @foreach($leaveTypes as $key => $label)
                            <div class="leave-type-item">
                                <span class="checkbox {{ $leaveApplication->type_of_leave == $key ? 'checked' : '' }}"></span>
                                <strong>{{ $label }}</strong>
                            </div>
                        @endforeach

                        <div style="margin-top: 5px; font-size: 12px;">
                            <strong>Specify Other:</strong>
                            <span class="underline" style="width: 120px;">{{ $leaveApplication->other_leave_type ?? '' }}</span>
                        </div>
                    </td>

                    <!-- 6.B -->
                    <td width="50%" style="padding: 6px; vertical-align: top;" class="details-section-content">
                        <div class="details-section-6b">
                            <div>
                                <div class="field-label" style="margin-bottom: 6px;">6.B DETAILS OF LEAVE</div>

                                @if(in_array($leaveApplication->type_of_leave, ['vacation_leave', 'special_privilege_leave']))
                                <div class="leave-details-text">
                                    <strong>Vacation/Special Privilege Leave:</strong><br>
                                    <span class="checkbox {{ $leaveApplication->vacation_location == 'within_philippines' ? 'checked' : '' }}"></span> Within the Philippines<br>
                                    <span class="checkbox {{ $leaveApplication->vacation_location == 'abroad' ? 'checked' : '' }}"></span> Abroad (Specify)
                                    <span class="underline" style="width: 70px;">{{ $leaveApplication->abroad_specify ?? '' }}</span>
                                </div>
                                @endif

                                @if($leaveApplication->type_of_leave === 'sick_leave')
                                <div class="leave-details-text">
                                    <strong>Sick Leave:</strong><br>
                                    <span class="checkbox {{ $leaveApplication->sick_leave_location == 'in_hospital' ? 'checked' : '' }}"></span> In Hospital
                                    <span class="underline" style="width: 65px;">{{ $leaveApplication->hospital_illness_specify ?? '' }}</span><br>
                                    <span class="checkbox {{ $leaveApplication->sick_leave_location == 'out_patient' ? 'checked' : '' }}"></span> Out Patient
                                    <span class="underline" style="width: 65px;">{{ $leaveApplication->outpatient_illness_specify ?? '' }}</span>
                                </div>
                                @endif

                                <div class="leave-details-text">
                                    <strong>Commutation:</strong><br>
                                    <span class="checkbox {{ $leaveApplication->commutation == 'requested' ? 'checked' : '' }}"></span> Requested
                                    <span class="checkbox {{ $leaveApplication->commutation == 'not_requested' ? 'checked' : '' }}"></span> Not Requested
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Section 6.D: Leave Credits -->
        <table>
            <tr>
                <td width="50%" style="padding: 4px; vertical-align: top;">
                    <strong>6.D CERTIFICATION OF LEAVE CREDITS</strong><br>
                    <table class="credits-table">
                        <tr>
                            <td>Vacation Leave</td>
                            <td>{{ $leaveApplication->employee?->vl_balance ?? '' }}</td>
                        </tr>
                        <tr>
                            <td>Sick Leave</td>
                            <td>{{ $leaveApplication->employee?->sl_balance ?? '' }}</td>
                        </tr>
                    </table>
                </td>

                <td width="50%" style="padding: 4px; vertical-align: top;">
                    <strong>Previous Leave Action</strong><br>
                    <span class="underline" style="width: 90%;">{{ $leaveApplication->previous_leave_action ?? '' }}</span>
                </td>
            </tr>
        </table>

        <!-- Section 7 -->
        <table>
            <tr>
                <td style="padding: 4px; vertical-align: top;">
                    <strong>7. RECOMMENDING APPROVAL / DISAPPROVAL</strong><br>
                    <div class="signature-area">
                        <span class="signature-line">{{ $leaveApplication->authorized_officer ?? '' }}</span>
                        <div class="signature-label">Immediate Supervisor
                            @if($leaveApplication->status === 'disapproved')
                                <br><small>Reason: {{ $leaveApplication->disapproval_reason }}</small>
                            @endif
                        </div>
                    </div>
                    <div class="signature-area" style="margin-top: 15px;">
                        <span class="signature-line"></span>
                        <div class="signature-label">Head of Office</div>
                    </div>
                </td>
            </tr>
        </table>
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
