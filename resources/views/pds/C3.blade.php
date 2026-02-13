{{-- resources/views/pds/C3.blade.php --}}
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
                <em>Page 3 of 4</em>
            </td>
        </tr>
    </table>

    {{-- VOLUNTARY WORK --}}
    <div class="section-title">VI. VOLUNTARY WORK OR INVOLVEMENT IN CIVIC / NON-GOVERNMENT / PEOPLE / VOLUNTARY ORGANIZATION/S</div>

    <table class="pds-table">
        <tr>
            <th class="header-row" rowspan="2" style="width:38%;">
                29. NAME & ADDRESS OF ORGANIZATION<br>
                <span style="font-size:6pt; font-weight:normal;">(Write in full)</span>
            </th>
            <th class="header-row" colspan="2" style="width:24%;">
                INCLUSIVE DATES<br>
                <span style="font-size:6pt; font-weight:normal;">(mm/dd/yyyy)</span>
            </th>
            <th class="header-row" rowspan="2" style="width:12%;">
                NUMBER OF<br>HOURS
            </th>
            <th class="header-row" rowspan="2" style="width:26%;">
                POSITION / NATURE OF WORK
            </th>
        </tr>
        <tr>
            <th class="header-row" style="font-size:6pt; width:12%;">From</th>
            <th class="header-row" style="font-size:6pt; width:12%;">To</th>
        </tr>

        @php
            $voluntaryWork = $pds->voluntary_work ?? [];
            $maxVoluntary = 7; // Standard PDS shows 7 rows
        @endphp

        @for($i = 0; $i < $maxVoluntary; $i++)
            @php
                $work = $voluntaryWork[$i] ?? null;
            @endphp
            <tr>
                <td class="data-cell">{{ $work['organization_name'] ?? '' }}</td>
                <td class="data-cell" style="text-align:center;">
                    @if(isset($work['from_date']) && !empty($work['from_date']))
                        {{ \Carbon\Carbon::parse($work['from_date'])->format('m/d/Y') }}
                    @endif
                </td>
                <td class="data-cell" style="text-align:center;">
                    @if(isset($work['to_date']) && !empty($work['to_date']))
                        {{ \Carbon\Carbon::parse($work['to_date'])->format('m/d/Y') }}
                    @endif
                </td>
                <td class="data-cell" style="text-align:center;">{{ $work['hours'] ?? '' }}</td>
                <td class="data-cell">{{ $work['position'] ?? '' }}</td>
            </tr>
        @endfor
    </table>

    <div class="note-text">(Continue on separate sheet if necessary)</div>

    {{-- LEARNING AND DEVELOPMENT --}}
    <div class="section-title" style="margin-top:8px;">VII. LEARNING AND DEVELOPMENT (L&D) INTERVENTIONS/TRAINING PROGRAMS ATTENDED</div>

    <div style="font-size:6pt; font-style:italic; margin:2px 0;">
        (Start from the most recent L&D/training program and include only the relevant L&D/training taken for the last five (5) years for Division Chief/Executive/Managerial positions)
    </div>

    <table class="pds-table">
        <tr>
            <th class="header-row" rowspan="2" style="width:35%;">
                30. TITLE OF LEARNING AND DEVELOPMENT<br>INTERVENTIONS/TRAINING PROGRAMS<br>
                <span style="font-size:6pt; font-weight:normal;">(Write in full)</span>
            </th>
            <th class="header-row" colspan="2" style="width:22%;">
                INCLUSIVE DATES<br>
                <span style="font-size:6pt; font-weight:normal;">(mm/dd/yyyy)</span>
            </th>
            <th class="header-row" rowspan="2" style="width:10%;">
                NUMBER OF<br>HOURS
            </th>
            <th class="header-row" rowspan="2" style="width:12%;">
                Type of LD<br>
                <span style="font-size:6pt; font-weight:normal;">(Managerial/<br>Supervisory/<br>Technical/etc)</span>
            </th>
            <th class="header-row" rowspan="2" style="width:21%;">
                CONDUCTED/ SPONSORED BY<br>
                <span style="font-size:6pt; font-weight:normal;">(Write in full)</span>
            </th>
        </tr>
        <tr>
            <th class="header-row" style="font-size:6pt; width:11%;">From</th>
            <th class="header-row" style="font-size:6pt; width:11%;">To</th>
        </tr>

        @php
            $learningDevelopment = $pds->learning_development ?? [];
            $maxLD = 21; // Standard PDS shows 21 rows
        @endphp

        @for($i = 0; $i < $maxLD; $i++)
            @php
                $ld = $learningDevelopment[$i] ?? null;
            @endphp
            <tr>
                <td class="data-cell">{{ $ld['training_title'] ?? '' }}</td>
                <td class="data-cell" style="text-align:center;">
                    @if(isset($ld['from_date']) && !empty($ld['from_date']))
                        {{ \Carbon\Carbon::parse($ld['from_date'])->format('m/d/Y') }}
                    @endif
                </td>
                <td class="data-cell" style="text-align:center;">
                    @if(isset($ld['to_date']) && !empty($ld['to_date']))
                        {{ \Carbon\Carbon::parse($ld['to_date'])->format('m/d/Y') }}
                    @endif
                </td>
                <td class="data-cell" style="text-align:center;">{{ $ld['hours'] ?? '' }}</td>
                <td class="data-cell">{{ $ld['type'] ?? '' }}</td>
                <td class="data-cell">{{ $ld['conducted_by'] ?? '' }}</td>
            </tr>
        @endfor
    </table>

    <div class="note-text">(Continue on separate sheet if necessary)</div>

    {{-- OTHER INFORMATION --}}
    <div class="section-title" style="margin-top:8px;">VIII. OTHER INFORMATION</div>

    <table class="pds-table">
        <tr>
            <th class="header-row" style="width:33.33%;">
                31. SPECIAL SKILLS and HOBBIES
            </th>
            <th class="header-row" style="width:33.33%;">
                32. NON-ACADEMIC DISTINCTIONS / RECOGNITION<br>
                <span style="font-size:6pt; font-weight:normal;">(Write in full)</span>
            </th>
            <th class="header-row" style="width:33.33%;">
                33. MEMBERSHIP IN ASSOCIATION/ORGANIZATION<br>
                <span style="font-size:6pt; font-weight:normal;">(Write in full)</span>
            </th>
        </tr>

        @php
            $specialSkills = $pds->special_skills ?? [];
            $distinctions = $pds->non_academic_distinctions ?? [];
            $memberships = $pds->membership_association ?? [];
            $maxOtherInfo = max(count($specialSkills), count($distinctions), count($memberships), 7); // Minimum 7 rows
        @endphp

        @for($i = 0; $i < $maxOtherInfo; $i++)
            <tr>
                <td class="data-cell">{{ $specialSkills[$i]['skill'] ?? '' }}</td>
                <td class="data-cell">{{ $distinctions[$i]['distinction'] ?? '' }}</td>
                <td class="data-cell">{{ $memberships[$i]['organization'] ?? '' }}</td>
            </tr>
        @endfor
    </table>

    <div class="note-text">(Continue on separate sheet if necessary)</div>

    <div style="text-align:right; font-size:7pt; font-style:italic; margin-top:5px;">
        SIGNATURE <span style="display:inline-block; width:150px; border-bottom:1px solid #000;"></span>
        DATE <span style="display:inline-block; width:100px; border-bottom:1px solid #000;"></span>
    </div>

    <div style="text-align:center; font-size:7pt; margin-top:15px;">
        <em>CS FORM 212 (Revised 2017), Page 3 of 4</em>
    </div>
</div>
