{{-- resources/views/pds/C2.blade.php --}}
<style>
    .pds-container {
        font-family: Arial, sans-serif;
        font-size: 9pt;
        line-height: 1.2;
        color: #000;
        max-width: 8.5in;
        margin: 0 auto;
        padding: 0.5in;
    }

    .pds-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }

    .pds-table td,
    .pds-table th {
        border: 1px solid #000;
        padding: 2px 4px;
        vertical-align: top;
        font-size: 8pt;
    }

    .pds-table th {
        background-color: #d9d9d9;
        font-weight: bold;
        text-align: center;
    }

    .label-cell {
        background-color: #d9d9d9;
        font-weight: normal;
        font-size: 7pt;
    }

    .data-cell {
        font-size: 7pt;
        min-height: 18px;
    }

    .header-row {
        background-color: #d9d9d9;
        font-weight: bold;
        text-align: center;
        font-size: 7pt;
        padding: 4px;
    }

    .section-title {
        background-color: #969696;
        color: #fff;
        font-weight: bold;
        font-size: 9pt;
        padding: 3px 5px;
        margin: 8px 0 2px 0;
    }

    .note-text {
        font-size: 7pt;
        font-style: italic;
        text-align: center;
        margin: 3px 0;
    }

    .page-break {
        page-break-after: always;
    }
</style>

<div class="pds-container page-break">
    {{-- PAGE HEADER --}}
    <table style="width:100%; margin-bottom: 5px; border: none;">
        <tr>
            <td style="width:20%; font-size:8pt; border: none;">
                <em>CS Form No. 212</em><br>
                <em>Revised 2017</em>
            </td>
            <td style="width:60%; text-align:center; border: none;">
                <div style="font-size:11pt; font-weight:bold;">PERSONAL DATA SHEET</div>
            </td>
            <td style="width:20%; text-align:right; font-size:8pt; border: none;">
                <em>Page 2 of 4</em>
            </td>
        </tr>
    </table>

    {{-- CIVIL SERVICE ELIGIBILITY --}}
    <div class="section-title">IV. CIVIL SERVICE ELIGIBILITY</div>

    <table class="pds-table">
        <tr>
            <th class="header-row" rowspan="2" style="width:28%;">
                27. CAREER SERVICE/ RA 1080 (BOARD/ BAR) UNDER<br>
                SPECIAL LAWS/ CES/ CSEE<br>
                BARANGAY ELIGIBILITY / DRIVER'S LICENSE
            </th>
            <th class="header-row" rowspan="2" style="width:8%;">
                RATING<br>
                <span style="font-size:6pt; font-weight:normal;">(If Applicable)</span>
            </th>
            <th class="header-row" rowspan="2" style="width:12%;">
                DATE OF<br>EXAMINATION /<br>CONFERMENT
            </th>
            <th class="header-row" rowspan="2" style="width:20%;">
                PLACE OF EXAMINATION /<br>CONFERMENT
            </th>
            <th class="header-row" colspan="2" style="width:32%;">
                LICENSE (if applicable)
            </th>
        </tr>
        <tr>
            <th class="header-row" style="font-size:6pt;">NUMBER</th>
            <th class="header-row" style="font-size:6pt;">
                Date of<br>Validity
            </th>
        </tr>

        @php
            $eligibilities = $pds->civil_service_eligibility ?? [];
            $maxEligibility = 7; // Standard PDS shows 7 rows
        @endphp

        @for($i = 0; $i < $maxEligibility; $i++)
            @php
                $el = $eligibilities[$i] ?? null;
            @endphp
            <tr>
                <td class="data-cell">{{ $el['career_service'] ?? '' }}</td>
                <td class="data-cell" style="text-align:center;">{{ $el['rating'] ?? '' }}</td>
                <td class="data-cell" style="text-align:center;">
                    @if(isset($el['exam_date']) && !empty($el['exam_date']))
                        {{ \Carbon\Carbon::parse($el['exam_date'])->format('m/d/Y') }}
                    @endif
                </td>
                <td class="data-cell">{{ $el['place'] ?? '' }}</td>
                <td class="data-cell" style="text-align:center;">{{ $el['license_no'] ?? '' }}</td>
                <td class="data-cell" style="text-align:center;">
                    @if(isset($el['validity']) && !empty($el['validity']))
                        {{ \Carbon\Carbon::parse($el['validity'])->format('m/d/Y') }}
                    @endif
                </td>
            </tr>
        @endfor
    </table>

    <div class="note-text">(Continue on separate sheet if necessary)</div>

    {{-- WORK EXPERIENCE --}}
    <div class="section-title" style="margin-top:8px;">V. WORK EXPERIENCE</div>

    <div style="font-size:6pt; font-style:italic; margin:2px 0;">
        (Include private employment. Start from your recent work) Description of duties should be indicated in the attached Work Experience sheet.
    </div>

    <table class="pds-table">
        <tr>
            <th class="header-row" colspan="2" rowspan="2" style="width:18%;">
                28. INCLUSIVE DATES<br>
                <span style="font-size:6pt; font-weight:normal;">(mm/dd/yyyy)</span>
            </th>
            <th class="header-row" rowspan="2" style="width:22%;">
                POSITION TITLE<br>
                <span style="font-size:6pt; font-weight:normal;">(Write in full/Do not abbreviate)</span>
            </th>
            <th class="header-row" rowspan="2" style="width:25%;">
                DEPARTMENT / AGENCY / OFFICE / COMPANY<br>
                <span style="font-size:6pt; font-weight:normal;">(Write in full/Do not abbreviate)</span>
            </th>
            <th class="header-row" rowspan="2" style="width:10%;">
                MONTHLY<br>SALARY
            </th>
            <th class="header-row" rowspan="2" style="width:8%;">
                SALARY/<br>JOB/<br>PAY<br>GRADE
            </th>
            <th class="header-row" rowspan="2" style="width:10%;">
                STATUS OF<br>APPOINTMENT
            </th>
            <th class="header-row" rowspan="2" style="width:7%;">
                GOV'T<br>SERVICE<br>
                <span style="font-size:6pt; font-weight:normal;">(Y/ N)</span>
            </th>
        </tr>
        <tr></tr>
        <tr>
            <th class="header-row" style="font-size:6pt; width:9%;">From</th>
            <th class="header-row" style="font-size:6pt; width:9%;">To</th>
            <th class="header-row" style="font-size:6pt;"></th>
            <th class="header-row" style="font-size:6pt;"></th>
            <th class="header-row" style="font-size:6pt;"></th>
            <th class="header-row" style="font-size:6pt;"></th>
            <th class="header-row" style="font-size:6pt;"></th>
            <th class="header-row" style="font-size:6pt;"></th>
        </tr>

        @php
            $workExperience = $pds->work_experience ?? [];
            $maxWork = 28; // Standard PDS shows 28 rows for work experience
        @endphp

        @for($i = 0; $i < $maxWork; $i++)
            @php
                $work = $workExperience[$i] ?? null;
            @endphp
            <tr>
                <td class="data-cell" style="text-align:center;">
                    @if(isset($work['from']) && !empty($work['from']))
                        {{ \Carbon\Carbon::parse($work['from'])->format('m/d/Y') }}
                    @endif
                </td>
                <td class="data-cell" style="text-align:center;">
                    @if(isset($work['to']) && !empty($work['to']))
                        {{ \Carbon\Carbon::parse($work['to'])->format('m/d/Y') }}
                    @endif
                </td>
                <td class="data-cell">{{ $work['position'] ?? '' }}</td>
                <td class="data-cell">{{ $work['agency'] ?? '' }}</td>
                <td class="data-cell" style="text-align:right;">{{ $work['salary'] ?? '' }}</td>
                <td class="data-cell" style="text-align:center;">{{ $work['salary_grade'] ?? '' }}</td>
                <td class="data-cell">{{ $work['status'] ?? '' }}</td>
                <td class="data-cell" style="text-align:center;">
                    @if(isset($work['is_government']))
                        {{ $work['is_government'] ? 'Y' : 'N' }}
                    @endif
                </td>
            </tr>
        @endfor
    </table>

    <div class="note-text">(Continue on separate sheet if necessary)</div>

    <div style="text-align:right; font-size:7pt; font-style:italic; margin-top:5px;">
        SIGNATURE <span style="display:inline-block; width:150px; border-bottom:1px solid #000;"></span>
        DATE <span style="display:inline-block; width:100px; border-bottom:1px solid #000;"></span>
    </div>

    <div style="text-align:center; font-size:7pt; margin-top:15px;">
        <em>CS FORM 212 (Revised 2017), Page 2 of 4</em>
    </div>
</div>
