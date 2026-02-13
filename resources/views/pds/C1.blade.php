{{-- resources/views/pds/C1.blade.php --}}
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
        width: 25%;
    }

    .data-cell {
        font-size: 8pt;
        min-height: 18px;
    }

    .header-row {
        background-color: #d9d9d9;
        font-weight: bold;
        text-align: center;
        font-size: 9pt;
        padding: 4px;
    }

    .checkbox-group {
        display: inline-block;
        margin-right: 10px;
    }

    .checkbox {
        display: inline-block;
        width: 12px;
        height: 12px;
        border: 1px solid #000;
        text-align: center;
        line-height: 12px;
        margin-right: 3px;
        vertical-align: middle;
    }

    .page-break {
        page-break-after: always;
    }

    .warning-text {
        font-size: 8pt;
        text-align: justify;
        margin: 5px 0;
        line-height: 1.3;
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
</style>

<div class="pds-container page-break">
    {{-- HEADER --}}
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
                <em>Page 1 of 4</em>
            </td>
        </tr>
    </table>

    {{-- WARNING --}}
    <div class="warning-text">
        <strong>WARNING:</strong> Any misrepresentation made in the Personal Data Sheet and the Work Experience Sheet shall cause the filing of administrative/criminal case/s against the person concerned.
    </div>

    <div class="warning-text">
        <strong>READ THE ATTACHED GUIDE TO FILLING OUT THE PERSONAL DATA SHEET (PDS) BEFORE ACCOMPLISHING THE PDS FORM.</strong>
    </div>

    <div class="warning-text">
        Print legibly. Tick appropriate boxes (☑) and use separate sheet if necessary. Indicate N/A if not applicable. <strong>DO NOT ABBREVIATE.</strong>
    </div>

    {{-- SECTION HEADER --}}
    <div class="section-title">I. PERSONAL INFORMATION</div>

    {{-- PERSONAL INFORMATION TABLE --}}
    <table class="pds-table">
        <tr>
            <td class="label-cell">2. SURNAME</td>
            <td class="data-cell" colspan="3">{{ strtoupper($pds->surname ?? '') }}</td>
        </tr>
        <tr>
            <td class="label-cell">FIRST NAME</td>
            <td class="data-cell" colspan="2">{{ strtoupper($pds->first_name ?? '') }}</td>
            <td class="data-cell" style="text-align:right; font-size:7pt; background-color:#d9d9d9;">
                NAME EXTENSION (JR., SR)<br>
                <span style="font-size:8pt; background-color:#fff; display:block; margin-top:2px;">
                    {{ strtoupper($pds->name_extension ?? '') }}
                </span>
            </td>
        </tr>
        <tr>
            <td class="label-cell">MIDDLE NAME</td>
            <td class="data-cell" colspan="3">{{ strtoupper($pds->middle_name ?? '') }}</td>
        </tr>

        <tr>
            <td class="label-cell">3. DATE OF BIRTH<br><span style="font-size:6pt;">(mm/dd/yyyy)</span></td>
            <td class="data-cell">{{ optional($pds->date_of_birth)->format('m/d/Y') }}</td>
            <td class="label-cell">16. PLACE OF BIRTH</td>
            <td class="data-cell">{{ strtoupper($pds->place_of_birth ?? '') }}</td>
        </tr>

        <tr>
            <td class="label-cell">4. SEX</td>
            <td class="data-cell">
                <div class="checkbox-group">
                    <span class="checkbox">{{ $pds->sex === 'Male' ? '✓' : '' }}</span> Male
                </div>
                <div class="checkbox-group">
                    <span class="checkbox">{{ $pds->sex === 'Female' ? '✓' : '' }}</span> Female
                </div>
            </td>
            <td class="label-cell">17. CIVIL STATUS</td>
            <td class="data-cell">
                @foreach(['Single','Married','Widowed','Separated','Other/s'] as $status)
                    <div class="checkbox-group">
                        <span class="checkbox">{{ $pds->civil_status === $status ? '✓' : '' }}</span>
                        {{ $status }}
                    </div>
                @endforeach
            </td>
        </tr>

        <tr>
            <td class="label-cell">5. HEIGHT (m)</td>
            <td class="data-cell">{{ $pds->height ?? '' }}</td>
            <td class="label-cell">18. CITIZENSHIP</td>
            <td class="data-cell" rowspan="2">
                <div class="checkbox-group">
                    <span class="checkbox">{{ ($pds->filipino ?? false) ? '✓' : '' }}</span> Filipino
                </div>
                <div class="checkbox-group">
                    <span class="checkbox">{{ ($pds->dual_citizenship ?? false) ? '✓' : '' }}</span> Dual Citizenship
                </div>
                @if($pds->dual_citizenship ?? false)
                <div style="font-size:7pt; margin-top:2px;">
                    Pls. indicate country: <strong>{{ $pds->dual_citizenship_country ?? '' }}</strong>
                </div>
                @endif
            </td>
        </tr>

        <tr>
            <td class="label-cell">6. WEIGHT (kg)</td>
            <td class="data-cell">{{ $pds->weight ?? '' }}</td>
            <td class="label-cell"></td>
        </tr>

        <tr>
            <td class="label-cell">7. BLOOD TYPE</td>
            <td class="data-cell">{{ $pds->blood_type ?? '' }}</td>
            <td class="label-cell">19. RESIDENTIAL ADDRESS</td>
            <td class="data-cell">{{ $pds->residential_address ?? '' }}</td>
        </tr>

        <tr>
            <td class="label-cell">8. GSIS ID NO.</td>
            <td class="data-cell">{{ $pds->gsis_id_no ?? '' }}</td>
            <td class="label-cell" style="font-size:7pt;">House/Block/Lot No.</td>
            <td class="data-cell">{{ $pds->res_house_block_lot_no ?? '' }}</td>
        </tr>

        <tr>
            <td class="label-cell">9. PAG-IBIG ID NO.</td>
            <td class="data-cell">{{ $pds->pag_ibig_id_no ?? '' }}</td>
            <td class="label-cell">Street</td>
            <td class="data-cell">{{ $pds->res_street ?? '' }}</td>
        </tr>

        <tr>
            <td class="label-cell">10. PHILHEALTH NO.</td>
            <td class="data-cell">{{ $pds->philhealth_no ?? '' }}</td>
            <td class="label-cell">Subdivision/Village</td>
            <td class="data-cell">{{ $pds->res_subdivision_village ?? '' }}</td>
        </tr>

        <tr>
            <td class="label-cell">11. SSS NO.</td>
            <td class="data-cell">{{ $pds->sss_no ?? '' }}</td>
            <td class="label-cell">Barangay</td>
            <td class="data-cell">{{ $pds->res_barangay ?? '' }}</td>
        </tr>

        <tr>
            <td class="label-cell">12. TIN NO.</td>
            <td class="data-cell">{{ $pds->tin_no ?? '' }}</td>
            <td class="label-cell">City/Municipality</td>
            <td class="data-cell">{{ $pds->res_city_municipality ?? '' }}</td>
        </tr>

        <tr>
            <td class="label-cell">13. AGENCY EMPLOYEE NO.</td>
            <td class="data-cell">{{ $pds->agency_employee_no ?? '' }}</td>
            <td class="label-cell">Province</td>
            <td class="data-cell">{{ $pds->res_province ?? '' }}</td>
        </tr>

        <tr>
            <td class="label-cell">14. TELEPHONE NO.</td>
            <td class="data-cell">{{ $pds->telephone_no ?? '' }}</td>
            <td class="label-cell">ZIP CODE</td>
            <td class="data-cell">{{ $pds->res_zip_code ?? '' }}</td>
        </tr>

        <tr>
            <td class="label-cell">15. MOBILE NO.</td>
            <td class="data-cell">{{ $pds->mobile ?? '' }}</td>
            <td class="label-cell">20. PERMANENT ADDRESS</td>
            <td class="data-cell">{{ $pds->permanent_address ?? '' }}</td>
        </tr>

        <tr>
            <td class="label-cell">16. E-MAIL ADDRESS (if any)</td>
            <td class="data-cell">{{ $pds->email ?? '' }}</td>
            <td class="label-cell" style="font-size:7pt;">House/Block/Lot No.</td>
            <td class="data-cell">{{ $pds->perm_house_block_lot_no ?? '' }}</td>
        </tr>

        <tr>
            <td class="label-cell"></td>
            <td class="data-cell"></td>
            <td class="label-cell">Street</td>
            <td class="data-cell">{{ $pds->perm_street ?? '' }}</td>
        </tr>

        <tr>
            <td class="label-cell"></td>
            <td class="data-cell"></td>
            <td class="label-cell">Subdivision/Village</td>
            <td class="data-cell">{{ $pds->perm_subdivision_village ?? '' }}</td>
        </tr>

        <tr>
            <td class="label-cell"></td>
            <td class="data-cell"></td>
            <td class="label-cell">Barangay</td>
            <td class="data-cell">{{ $pds->perm_barangay ?? '' }}</td>
        </tr>

        <tr>
            <td class="label-cell"></td>
            <td class="data-cell"></td>
            <td class="label-cell">City/Municipality</td>
            <td class="data-cell">{{ $pds->perm_city_municipality ?? '' }}</td>
        </tr>

        <tr>
            <td class="label-cell"></td>
            <td class="data-cell"></td>
            <td class="label-cell">Province</td>
            <td class="data-cell">{{ $pds->perm_province ?? '' }}</td>
        </tr>

        <tr>
            <td class="label-cell"></td>
            <td class="data-cell"></td>
            <td class="label-cell">ZIP CODE</td>
            <td class="data-cell">{{ $pds->perm_zip_code ?? '' }}</td>
        </tr>
    </table>

    {{-- FAMILY BACKGROUND --}}
    <div class="section-title">II. FAMILY BACKGROUND</div>

    <table class="pds-table">
        <tr>
            <td class="label-cell" style="width:25%;">21. SPOUSE'S SURNAME</td>
            <td class="data-cell" colspan="3">{{ strtoupper($pds->spouse_surname ?? '') }}</td>
        </tr>
        <tr>
            <td class="label-cell">FIRST NAME</td>
            <td class="data-cell" colspan="2">{{ strtoupper($pds->spouse_first_name ?? '') }}</td>
            <td class="data-cell" style="text-align:right; font-size:7pt; background-color:#d9d9d9;">
                NAME EXTENSION (JR., SR)<br>
                <span style="font-size:8pt; background-color:#fff; display:block; margin-top:2px;">
                    {{ strtoupper($pds->spouse_name_extension ?? '') }}
                </span>
            </td>
        </tr>
        <tr>
            <td class="label-cell">MIDDLE NAME</td>
            <td class="data-cell" colspan="3">{{ strtoupper($pds->spouse_middle_name ?? '') }}</td>
        </tr>
        <tr>
            <td class="label-cell">OCCUPATION</td>
            <td class="data-cell">{{ $pds->spouse_occupation ?? '' }}</td>
            <td class="label-cell" style="width:25%;">EMPLOYER/BUSINESS NAME</td>
            <td class="data-cell">{{ $pds->spouse_employer_business_name ?? '' }}</td>
        </tr>
        <tr>
            <td class="label-cell">BUSINESS ADDRESS</td>
            <td class="data-cell" colspan="3">{{ $pds->spouse_business_address ?? '' }}</td>
        </tr>
        <tr>
            <td class="label-cell">TELEPHONE NO.</td>
            <td class="data-cell" colspan="3">{{ $pds->spouse_telephone_no ?? '' }}</td>
        </tr>

        <tr>
            <td class="label-cell">22. FATHER'S SURNAME</td>
            <td class="data-cell" colspan="3">{{ strtoupper($pds->father_surname ?? '') }}</td>
        </tr>
        <tr>
            <td class="label-cell">FIRST NAME</td>
            <td class="data-cell" colspan="2">{{ strtoupper($pds->father_first_name ?? '') }}</td>
            <td class="data-cell" style="text-align:right; font-size:7pt; background-color:#d9d9d9;">
                NAME EXTENSION (JR., SR)<br>
                <span style="font-size:8pt; background-color:#fff; display:block; margin-top:2px;">
                    {{ strtoupper($pds->father_name_extension ?? '') }}
                </span>
            </td>
        </tr>
        <tr>
            <td class="label-cell">MIDDLE NAME</td>
            <td class="data-cell" colspan="3">{{ strtoupper($pds->father_middle_name ?? '') }}</td>
        </tr>

        <tr>
            <td class="label-cell">23. MOTHER'S MAIDEN NAME</td>
            <td class="data-cell" colspan="3"></td>
        </tr>
        <tr>
            <td class="label-cell">SURNAME</td>
            <td class="data-cell" colspan="3">{{ strtoupper($pds->mother_surname ?? '') }}</td>
        </tr>
        <tr>
            <td class="label-cell">FIRST NAME</td>
            <td class="data-cell" colspan="3">{{ strtoupper($pds->mother_first_name ?? '') }}</td>
        </tr>
        <tr>
            <td class="label-cell">MIDDLE NAME</td>
            <td class="data-cell" colspan="3">{{ strtoupper($pds->mother_middle_name ?? '') }}</td>
        </tr>
    </table>

    {{-- CHILDREN --}}
    <table class="pds-table" style="margin-top:2px;">
        <tr>
            <th colspan="2" class="header-row">
                24. NAME of CHILDREN (Write full name and list all)
            </th>
            <th class="header-row">DATE OF BIRTH (mm/dd/yyyy)</th>
        </tr>
        @php
            $children = $pds->children ?? [];
            $maxChildren = 12; // Standard PDS shows 12 rows
        @endphp
        @for($i = 0; $i < $maxChildren; $i++)
            <tr>
                <td style="width:5%; text-align:center; font-size:7pt;">{{ $i + 1 }}.</td>
                <td class="data-cell" style="width:60%;">
                    {{ isset($children[$i]) ? strtoupper($children[$i]['name'] ?? '') : '' }}
                </td>
                <td class="data-cell" style="width:35%; text-align:center;">
                    @if(isset($children[$i]) && !empty($children[$i]['birthdate']))
                        {{ \Carbon\Carbon::parse($children[$i]['birthdate'])->format('m/d/Y') }}
                    @endif
                </td>
            </tr>
        @endfor
    </table>

    <div class="note-text">(Continue on separate sheet if necessary)</div>

    {{-- EDUCATIONAL BACKGROUND --}}
    <div class="section-title">III. EDUCATIONAL BACKGROUND</div>

    <table class="pds-table">
        <tr>
            <th class="header-row" rowspan="2" style="width:12%;">25. LEVEL</th>
            <th class="header-row" rowspan="2" style="width:28%;">
                NAME OF SCHOOL<br>
                <span style="font-size:6pt; font-weight:normal;">(Write in full)</span>
            </th>
            <th class="header-row" rowspan="2" style="width:20%;">
                BASIC EDUCATION/DEGREE/COURSE<br>
                <span style="font-size:6pt; font-weight:normal;">(Write in full)</span>
            </th>
            <th class="header-row" colspan="2" style="width:15%;">
                PERIOD OF ATTENDANCE
            </th>
            <th class="header-row" rowspan="2" style="width:10%;">
                HIGHEST LEVEL/<br>UNITS EARNED<br>
                <span style="font-size:6pt; font-weight:normal;">(if not graduated)</span>
            </th>
            <th class="header-row" rowspan="2" style="width:10%;">
                YEAR<br>GRADUATED
            </th>
            <th class="header-row" rowspan="2" style="width:15%;">
                SCHOLARSHIP/<br>ACADEMIC<br>HONORS<br>RECEIVED
            </th>
        </tr>
        <tr>
            <th class="header-row" style="font-size:7pt;">From</th>
            <th class="header-row" style="font-size:7pt;">To</th>
        </tr>

        @php
            $education = $pds->education ?? [];
            $eduLevels = ['ELEMENTARY', 'SECONDARY', 'VOCATIONAL / TRADE COURSE', 'COLLEGE', 'GRADUATE STUDIES'];
        @endphp

        @foreach($eduLevels as $level)
            @php
                $eduData = collect($education)->firstWhere('level', $level);
            @endphp
            <tr>
                <td class="label-cell" style="text-align:center; font-size:7pt;">{{ $level }}</td>
                <td class="data-cell">{{ $eduData['school_name'] ?? '' }}</td>
                <td class="data-cell">{{ $eduData['degree'] ?? '' }}</td>
                <td class="data-cell" style="text-align:center;">{{ $eduData['from_year'] ?? '' }}</td>
                <td class="data-cell" style="text-align:center;">{{ $eduData['to_year'] ?? '' }}</td>
                <td class="data-cell" style="text-align:center;">{{ $eduData['units_earned'] ?? '' }}</td>
                <td class="data-cell" style="text-align:center;">{{ $eduData['year_graduated'] ?? '' }}</td>
                <td class="data-cell">{{ $eduData['honors'] ?? '' }}</td>
            </tr>
        @endforeach
    </table>

    <div class="note-text">(Continue on separate sheet if necessary)</div>

    <div style="text-align:right; font-size:7pt; font-style:italic; margin-top:10px;">
        <em>(Continue on separate sheet if necessary)</em>
    </div>

    <div style="text-align:center; font-size:7pt; margin-top:15px;">
        <em>CS FORM 212 (Revised 2017), Page 1 of 4</em>
    </div>
</div>
