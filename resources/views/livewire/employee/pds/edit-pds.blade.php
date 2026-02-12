@push('styles')
<style>
    .pds-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
        table-layout: fixed; /* fixes uneven input widths */
    }

    .pds-table td,
    .pds-table th {
        border: 1px solid #000;
        padding: 4px;
        vertical-align: top;
    }

    .pds-table input[type="text"],
    .pds-table input[type="email"],
    .pds-table input[type="date"] {
        width: 100%;
        box-sizing: border-box; /* avoids overflow */
        padding: 2px;
        font-size: 11px;
        border: 1px solid #ccc;
        border-radius: 2px;
    }

    .pds-note {
        font-size: 11px;
        text-align: justify;
        margin-bottom: 4px;
    }

    .checkbox-group {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .checkbox-group label {
        display: flex;
        align-items: center;
        gap: 3px;
    }

    .checkbox {
        width: 14px;
        height: 14px;
        border: 1px solid #0bb609;
        display: inline-block;
        text-align: center;
        line-height: 12px;
    }

    .page-break {
        page-break-after: always;
    }

    th {
        text-align: left;
        padding: 4px;
    }

    input[type="checkbox"] {
        transform: scale(0.9);
    }
</style>
@endpush

{{-- C1 – PERSONAL INFORMATION --}}
<div class="page-break">
    <form wire:submit.prevent="save">
    <table style="width:100%; font-size:11px;">
        <tr>
            <td style="width:20%;">
                CS Form No. 212<br>
                Revised 2020
            </td>
            <td style="width:60%; text-align:center;">
                <strong>REPUBLIC OF THE PHILIPPINES</strong><br>
                <strong>CIVIL SERVICE COMMISSION</strong>
            </td>
            <td style="width:20%; text-align:right;">
                Page 1 of 4
            </td>
        </tr>
    </table>

    <h2 style="text-align:center; margin-top:10px;">
        <strong>PERSONAL DATA SHEET</strong>
    </h2>

    <p class="pds-note">
        <strong>WARNING:</strong> Any misrepresentation made in the Personal Data Sheet and the Work Experience Sheet
        shall cause the filing of administrative/criminal case/s against the person concerned.
    </p>

    <p class="pds-note">
        <strong>READ THE ATTACHED GUIDE TO FILLING OUT THE PERSONAL DATA SHEET (PDS) BEFORE ACCOMPLISHING THE PDS FORM.</strong>
    </p>

    <p class="pds-note">
        Print legibly. Tick appropriate boxes ( <span class="checkbox">&#10003;</span> ) and use separate sheet if necessary.
        Indicate N/A if not applicable. <strong>DO NOT ABBREVIATE.</strong>
    </p>
<br>
    <p><strong>C1 – PERSONAL INFORMATION</strong></p>

    {{-- PERSONAL INFORMATION --}}
<table class="table-bordered pds-table">
    <tr>
    <td>1. SURNAME</td>
    <td><input type="text" wire:model="form.surname"></td>
    <td>FIRST NAME</td>
    <td><input type="text" wire:model="form.first_name"></td>
</tr>

<tr>
    <td>MIDDLE NAME</td>
    <td><input type="text" wire:model="form.middle_name"></td>
    <td>NAME EXTENSION</td>
    <td><input type="text" wire:model="form.name_extension"></td>
</tr>

    <tr>
    <td>2. DATE OF BIRTH</td>
    <td><input type="date" wire:model="form.date_of_birth"></td>
    <td>3. PLACE OF BIRTH</td>
    <td><input type="text" wire:model="form.place_of_birth"></td>
</tr>

    <tr>
        <td>SEX</td>
    <td>
        <select wire:model="form.sex">
            <option value="">Select</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>
    </td>

        <td>CIVIL STATUS</td>
    <td>
        <select wire:model="form.civil_status">
            <option value="">Select</option>
            <option value="Single">Single</option>
            <option value="Married">Married</option>
            <option value="Widowed">Widowed</option>
            <option value="Separated">Separated</option>
        </select>
    </td>
    </tr>

    <tr>
        <td>6. CITIZENSHIP</td>
        <td colspan="3">
            <label>
                <input type="checkbox" wire:model="form.filipino">
                Filipino
            </label>

            <label style="margin-left:15px;">
                <input type="checkbox" wire:model="form.dual_citizenship">
                Dual Citizenship
            </label>

            <span style="margin-left:10px;">
                <input type="text"
                       placeholder="If dual, indicate country"
                       wire:model="form.dual_citizenship_country"
                       style="width:200px;">
            </span>
        </td>
    </tr>

   {{-- PERMANENT ADDRESS --}}
<tr>
    <td>7. PERMANENT ADDRESS:</td>
    <td colspan="3">
        <div style="display:flex; flex-wrap:wrap; gap:10px;">
            <div style="flex:1 1 45%;">
                <label>House/Block/Lot</label>
                <input type="text" wire:model="form.perm_house_block_lot_no" style="width:100%;">
            </div>

            <div style="flex:1 1 45%;">
                <label>Street</label>
                <input type="text" wire:model="form.perm_street" style="width:100%;">
            </div>

            <div style="flex:1 1 45%;">
                <label>Subdivision/Village</label>
                <input type="text" wire:model="form.perm_subdivision_village" style="width:100%;">
            </div>

            <div style="flex:1 1 45%;">
                <label>Barangay</label>
                <input type="text" wire:model="form.perm_barangay" style="width:100%;">
            </div>

            <div style="flex:1 1 45%;">
                <label>City/Municipality</label>
                <input type="text" wire:model="form.perm_city_municipality" style="width:100%;">
            </div>

            <div style="flex:1 1 45%;">
                <label>Province</label>
                <input type="text" wire:model="form.perm_province" style="width:100%;">
            </div>

            <div style="flex:1 1 45%;">
                <label>ZIP code</label>
                <input type="text" wire:model="form.perm_zip_code" style="width:30%;">
            </div>
        </div>
    </td>
</tr>

{{-- RESIDENTIAL ADDRESS --}}
<tr>
    <td>8. RESIDENTIAL ADDRESS:</td>
    <td colspan="3">

        <div style="display:flex; flex-wrap:wrap; gap:10px;">
            <div style="flex:1 1 45%;">
                <label>House/Block/Lot</label>
                <input type="text" wire:model="form.res_house_block_lot_no" style="width:100%;">
            </div>

            <div style="flex:1 1 45%;">
                <label>Street</label>
                <input type="text" wire:model="form.res_street" style="width:100%;">
            </div>

            <div style="flex:1 1 45%;">
                <label>Subdivision/Village</label>
                <input type="text" wire:model="form.res_subdivision_village" style="width:100%;">
            </div>

            <div style="flex:1 1 45%;">
                <label>Barangay</label>
                <input type="text" wire:model="form.res_barangay" style="width:100%;">
            </div>

            <div style="flex:1 1 45%;">
                <label>City/Municipality</label>
                <input type="text" wire:model="form.res_city_municipality" style="width:100%;">
            </div>

            <div style="flex:1 1 45%;">
                <label>Province</label>
                <input type="text" wire:model="form.res_province" style="width:100%;">
            </div>

            <div style="flex:1 1 45%;">
                <label>ZIP code</label>
                <input type="text" wire:model="form.res_zip_code" style="width:30%;">
            </div>
        </div>
    </td>
</tr>

        <tr>
            <td>9. MOBILE NO.</td>
            <td><input type="text" wire:model="form.mobile"></td>
            <td>EMAIL</td>
            <td><input type="email" wire:model="form.email"></td>
        </tr>

        <tr>
    <td>HEIGHT (cm)</td>
    <td><input type="text" wire:model="form.height"></td>
    <td>WEIGHT (kg)</td>
    <td><input type="text" wire:model="form.weight"></td>
</tr>
<tr>
    <td>BLOOD TYPE</td>
    <td><input type="text" wire:model="form.blood_type"></td>
    <td>GSIS ID NO.</td>
    <td><input type="text" wire:model="form.gsis_id_no"></td>
</tr>
<tr>
    <td>PAG-IBIG ID NO.</td>
    <td><input type="text" wire:model="form.pag_ibig_id_no"></td>
    <td>PHILHEALTH NO.</td>
    <td><input type="text" wire:model="form.philhealth_no"></td>
</tr>
<tr>
    <td>SSS NO.</td>
    <td><input type="text" wire:model="form.sss_no"></td>
    <td>TIN NO.</td>
    <td><input type="text" wire:model="form.tin_no"></td>
</tr>
<tr>
    <td>AGENCY EMPLOYEE NO.</td>
    <td><input type="text" wire:model="form.agency_employee_no"></td>
</tr>

    </table>

    {{-- FAMILY BACKGROUND --}}
    <table class="table-bordered pds-table" style="margin-top:10px;">
        <tr>
            <th colspan="4">FAMILY BACKGROUND</th>
        </tr>

        <tr>
            <td>SPOUSE SURNAME</td>
            <td>
                <input type="text" wire:model="form.spouse_surname">
            </td>
            <td>FIRST NAME</td>
            <td>
                <input type="text" wire:model="form.spouse_first_name">
            </td>
        </tr>

        <tr>
            <td>OCCUPATION</td>
            <td>
                <input type="text" wire:model="form.spouse_occupation">
            </td>
            <td>EMPLOYER / BUSINESS NAME</td>
            <td>
                <input type="text" wire:model="form.spouse_employer_business_name">
            </td>
        </tr>

        <tr>
            <td>FATHER'S NAME</td>
            <td colspan="3">
                <input type="text" wire:model="form.father_first_name">
            </td>
        </tr>

        <tr>
            <td>MOTHER'S MAIDEN NAME</td>
            <td colspan="3">
                <input type="text" wire:model="form.mother_first_name">
            </td>
        </tr>

    </table>

    {{-- CHILDREN --}}
    <table class="table-bordered pds-table" style="margin-top:10px;">
        <tr>
            <th colspan="3">CHILDREN</th>
        </tr>
        <tr>
            <th style="width:50%">NAME</th>
            <th style="width:50%">DATE OF BIRTH</th>
            <th style="width:10%"></th>
        </tr>

       @for($i = 0; $i < 4; $i++)
        <tr>
            <td>
                <input type="text"
                    wire:model="form.children.{{ $i }}.name" style="margin:auto; width:600px;">
            </td>
            <td>
                <input type="date"
                    wire:model="form.children.{{ $i }}.birthdate" style="margin:auto; width:600px;">
            </td>
        </tr>
        @endfor

    </table>

    <p style="text-align:center;font-size:11px;">
        (Continue on separate sheet if necessary)
    </p>

    {{-- EDUCATIONAL BACKGROUND --}}
    <table class="table-bordered pds-table" style="margin-top:10px;">
        <tr>
            <th colspan="6">EDUCATIONAL BACKGROUND</th>
        </tr>

        <tr>
            <th style="width:16%">LEVEL</th>
            <th style="width:16%">NAME OF SCHOOL</th>
            <th style="width:16%">BASIC EDUCATION / DEGREE</th>
            <th style="width:16%">FROM</th>
            <th style="width:16%">TO</th>
            <th style="width:16%">HONORS</th>
        </tr>

        @for($i = 0; $i < 4; $i++)
        <tr>
            <td><input type="text" wire:model="form.education.{{ $i }}.level" style="width:200px;"></td>
            <td><input type="text" wire:model="form.education.{{ $i }}.school_name"style="width:200px;"></td>
            <td><input type="text" wire:model="form.education.{{ $i }}.degree"style="width:200px;"></td>
            <td><input type="text" wire:model="form.education.{{ $i }}.from_year"style="width:200px;"></td>
            <td><input type="text" wire:model="form.education.{{ $i }}.to_year"style="width:200px;"></td>
            <td><input type="text" wire:model="form.education.{{ $i }}.honors"style="width:200px;"></td>
        </tr>
        @endfor

    </table>
<br><br><br>
    {{-- C2 – CIVIL SERVICE ELIGIBILITY & WORK EXPERIENCE --}}
    <div class="page-break">
        <h4><strong>C2 – CIVIL SERVICE ELIGIBILITY & WORK EXPERIENCE</strong></h4>

        {{-- CIVIL SERVICE ELIGIBILITY --}}
        <table class="table-bordered pds-table" style="margin-top:10px;">
            <tr>
                <th colspan="6">CIVIL SERVICE ELIGIBILITY</th>
            </tr>
            <tr>
                <th style="width:20%">CAREER SERVICE</th>
                <th style="width:20%">RATING</th>
                <th style="width:20%">DATE OF EXAM</th>
                <th style="width:20%">PLACE</th>
                <th style="width:20%">LICENSE NO.</th>
                <th style="width:20%">VALIDITY</th>
            </tr>

            @for($i = 0; $i < 7; $i++)
            <tr>
                <td><input type="text" wire:model="form.civil_service_eligibility.{{ $i }}.career_service" style="width:200px;"></td>
                <td><input type="text" wire:model="form.civil_service_eligibility.{{ $i }}.rating" style="width:200px;"></td>
                <td><input type="date" wire:model="form.civil_service_eligibility.{{ $i }}.exam_date" ></td>
                <td><input type="text" wire:model="form.civil_service_eligibility.{{ $i }}.place" ></td>
                <td><input type="text" wire:model="form.civil_service_eligibility.{{ $i }}.license_no" style="width:200px;"></td>
                <td><input type="date" wire:model="form.civil_service_eligibility.{{ $i }}.validity" style="width:200px;"></td>
            </tr>
            @endfor

        </table>

        <br><br>

        {{-- WORK EXPERIENCE --}}
        <table class="table-bordered pds-table" style="margin-top:10px;">
            <tr>
                <th colspan="8">WORK EXPERIENCE</th>
            </tr>
            <tr>
                <th style="width:12.5%">FROM</th>
                <th style="width:12.5%">TO</th>
                <th style="width:12.5%">POSITION TITLE</th>
                <th style="width:12.5%">DEPARTMENT / AGENCY</th>
                <th style="width:12.5%">MONTHLY SALARY</th>
                <th style="width:12.5%">SALARY GRADE</th>
                <th style="width:12.5%">STATUS</th>
                <th style="width:12.5%">GOV'T SERVICE</th>
            </tr>

            @for($i = 0; $i < 28; $i++)
<tr>
    <td><input type="date" wire:model="form.work_experience.{{ $i }}.from" style="width:155px;"></td>
    <td><input type="date" wire:model="form.work_experience.{{ $i }}.to" style="width:155px;"></td>
    <td><input type="text" wire:model="form.work_experience.{{ $i }}.position" style="width:155px;"></td>
    <td><input type="text" wire:model="form.work_experience.{{ $i }}.agency" style="width:155px;"></td>
    <td><input type="text" wire:model="form.work_experience.{{ $i }}.salary" style="width:155px;"></td>
    <td><input type="text" wire:model="form.work_experience.{{ $i }}.salary_grade" style="width:155px;"></td>
    <td><input type="text" wire:model="form.work_experience.{{ $i }}.status" style="width:155px;"></td>
    <td>
        <input type="radio" value="1" wire:model="form.work_experience.{{ $i }}.is_government"> Yes
        <input type="radio" value="0" wire:model="form.work_experience.{{ $i }}.is_government"> No
    </td>
</tr>
@endfor

        </table>

    </div>
<br><br><br>
    {{-- C3 – VOLUNTARY WORK, L&D & OTHER INFORMATION --}}
    <div class="page-break">
        <h4><strong>C3 – VOLUNTARY WORK, L&D & OTHER INFORMATION</strong></h4>

        {{-- VI. VOLUNTARY WORK OR INVOLVEMENT --}}
        <table class="table-bordered pds-table">
            <tr>
                <th colspan="4">VI. VOLUNTARY WORK OR INVOLVEMENT IN CIVIC / NON-GOVERNMENT / PEOPLE / VOLUNTARY ORGANIZATION/S</th>
            </tr>
            <tr>
                <th style="width:25%">NAME & ADDRESS OF ORGANIZATION</th>
                <th style="width:25%">INCLUSIVE DATES (FROM)</th>
                <th style="width:25%">INCLUSIVE DATES (TO)</th>
                <th style="width:25%">NUMBER OF HOURS / POSITION</th>
            </tr>

            @for($i = 0; $i < 7; $i++)
            <tr>
                <td><input type="text" wire:model="form.voluntary_work.{{ $i }}.organization_name" style="width:300px;"></td>
                <td><input type="date" wire:model="form.voluntary_work.{{ $i }}.from_date" style="width:300px;"></td>
                <td><input type="date" wire:model="form.voluntary_work.{{ $i }}.to_date" style="width:300px;"></td>
                <td>
                    <input type="text" wire:model="form.voluntary_work.{{ $i }}.hours" style="width:80px;"> /
                    <input type="text" wire:model="form.voluntary_work.{{ $i }}.position" style="width:165px;">
                </td>
            </tr>
            @endfor

        </table>

        <br><br>

        {{-- VII. LEARNING AND DEVELOPMENT (L&D) --}}
        <table class="table-bordered pds-table">
            <tr>
                <th colspan="5">VII. LEARNING AND DEVELOPMENT (L&D) INTERVENTIONS / TRAINING PROGRAMS ATTENDED</th>
            </tr>
            <tr>
                <th style="width:20%">TITLE OF TRAINING</th>
                <th style="width:5%">INCLUSIVE DATES (FROM)</th>
                <th style="width:5%">INCLUSIVE DATES (TO)</th>
                <th style="width:5%">NUMBER OF HOURS</th>
                <th style="width:30%">TYPE / CONDUCTED BY</th>
            </tr>

            @for($i = 0; $i < 21; $i++)
            <tr>
                <td><input type="text" wire:model="form.learning_development.{{ $i }}.training_title" style="width:325px;"></td>
                <td><input type="date" wire:model="form.learning_development.{{ $i }}.from_date" style="width:200px;"></td>
                <td><input type="date" wire:model="form.learning_development.{{ $i }}.to_date" style="width:200px;"></td>
                <td><input type="text" wire:model="form.learning_development.{{ $i }}.hours" style="width:130px;"></td>
                <td>
                    <input type="text" wire:model="form.learning_development.{{ $i }}.type" style="width:160px;"> /
                    <input type="text" wire:model="form.learning_development.{{ $i }}.conducted_by" style="width:160px;">
                </td>
            </tr>
            @endfor

        </table>

        <br><br>

        {{-- VIII. OTHER INFORMATION --}}
    <table class="table-bordered pds-table">
        <tr>
            <th colspan="3">VIII. OTHER INFORMATION</th>
        </tr>
        <tr>
            <th style="width:33%">SPECIAL SKILLS & HOBBIES</th>
            <th style="width:33%">NON-ACADEMIC DISTINCTIONS / RECOGNITION</th>
            <th style="width:33%">MEMBERSHIP IN ASSOCIATION / ORGANIZATION</th>
        </tr>
        @php
            $maxRows = max(
                count($form['special_skills'] ?? []),
                count($form['non_academic_distinctions'] ?? []),
                count($form['membership_association'] ?? [])
            );
        @endphp

        @for($i = 0; $i < 7; $i++)
        <tr>
            <td><input type="text" wire:model="form.special_skills.{{ $i }}.skill" style="width:400px;"></td>
            <td><input type="text" wire:model="form.non_academic_distinctions.{{ $i }}.distinction" style="width:400px;"></td>
            <td><input type="text" wire:model="form.membership_association.{{ $i }}.organization" style="width:400px;"></td>
        </tr>
        @endfor
    </table>

<br><br>
    {{-- C4 – OTHER INFORMATION --}}
    <div class="page-break">
        <h4><strong>C4 – OTHER INFORMATION</strong></h4>

        <table class="table-bordered pds-table">
            {{-- 34. Relatives --}}
<tr>
    <td colspan="4"><strong>34.</strong> Are you related by consanguinity or affinity to any of the following:</td>
</tr>

<tr>
    <td>a. Within the third degree?</td>
    <td colspan="3">
        <div class="checkbox-group">
            <label>
    <input type="radio" value="1" wire:model="form.related_third_degree"> YES
</label>
<label>
    <input type="radio" value="0" wire:model="form.related_third_degree"> NO
</label>

            <input type="text" wire:model="form.related_third_degree_details" placeholder="Provide details" style="margin-left:50px; width:auto;">
        </div>
    </td>
</tr>

<tr>
    <td>b. Within the fourth degree?</td>
    <td colspan="3">
        <div class="checkbox-group">
            <label>
    <input type="radio" value="1" wire:model="form.related_fourth_degree"> YES
</label>
<label>
    <input type="radio" value="0" wire:model="form.related_fourth_degree"> NO
</label>
            <input type="text" wire:model="form.related_fourth_degree_details" placeholder="Provide details" style="margin-left:50px; width:auto;">
        </div>
    </td>
</tr>

{{-- 35. Administrative case --}}
<tr>
    <td>35. Have you ever been found guilty of any administrative offense?</td>
    <td colspan="3">
        <div class="checkbox-group">
            <label>
    <input type="radio" value="1" wire:model="form.has_admin_case"> YES
</label>
<label>
    <input type="radio" value="0" wire:model="form.has_admin_case"> NO
</label>
            <input type="text" wire:model="form.admin_case_details" placeholder="Provide details" style="margin-left:50px; width:auto;">
        </div>
    </td>
</tr>

{{-- 36. Criminal case --}}
<tr>
    <td>36. Have you been criminally charged before any court?</td>
    <td colspan="3">
        <div class="checkbox-group">
            <label>
    <input type="radio" value="1" wire:model="form.has_criminal_case"> YES
</label>
<label>
    <input type="radio" value="0" wire:model="form.has_criminal_case"> NO
</label>
            <span style="margin-left:50px;">
                Status: <input type="text" wire:model="form.criminal_case_status" style="width:180px;">
                Date Filed: <input type="date" wire:model="form.criminal_case_date_filed" style="width:150px;">
            </span>
        </div>
    </td>
</tr>

{{-- 37. Conviction --}}
<tr>
    <td>37. Have you ever been convicted of any crime or violation of any law?</td>
    <td colspan="3">
        <div class="checkbox-group">
            <label>
    <input type="radio" value="1" wire:model="form.has_conviction"> YES
</label>
<label>
    <input type="radio" value="0" wire:model="form.has_conviction"> NO
</label>
            <input type="text" wire:model="form.conviction_details" placeholder="Provide details" style="margin-left:50px; width:auto;">
        </div>
    </td>
</tr>

{{-- 38. Separation --}}
<tr>
    <td>38. Have you ever been separated from the service?</td>
    <td colspan="3">
        <div class="checkbox-group">
            <label>
    <input type="radio" value="1" wire:model="form.has_been_separated"> YES
</label>
<label>
    <input type="radio" value="0" wire:model="form.has_been_separated"> NO
</label>
            <input type="text" wire:model="form.separation_details" placeholder="Provide details" style="margin-left:50px; width:auto;">
        </div>
    </td>
</tr>

{{-- 39. Election --}}
<tr>
    <td>39. Have you ever been a candidate in a national or local election?</td>
    <td colspan="3">
        <div class="checkbox-group">
            <label>
                <input type="radio" value="1" wire:model="form.has_election_candidacy"> YES
            </label>
            <label>
                <input type="radio" value="0" wire:model="form.has_election_candidacy"> NO
            </label>
            <input type="text" wire:model="form.election_candidacy_details" placeholder="Provide details" style="margin-left:50px; width:auto;">
        </div>
    </td>
</tr>

{{-- 40. Other personal info --}}
<tr>
    <td>40.a. Indigenous Group?</td>
    <td colspan="3">
        <div class="checkbox-group">
            <label>
                <input type="radio" value="1" wire:model="form.is_indigenous"> YES
            </label>
            <label>
                <input type="radio" value="0" wire:model="form.is_indigenous"> NO
            </label>
            <input type="text" wire:model="form.indigenous_details" placeholder="Provide details" style="margin-left:50px; width:auto;">
        </div>
    </td>
</tr>

<tr>
    <td>40.b. Person with Disability?</td>
    <td colspan="3">
        <div class="checkbox-group">
            <label>
                <input type="radio" value="1" wire:model="form.has_disability"> YES
            </label>
            <label>
                <input type="radio" value="0" wire:model="form.has_disability"> NO
            </label>
            <input type="text" wire:model="form.disability_details" placeholder="Provide details" style="margin-left:50px; width:auto;">
        </div>
    </td>
</tr>

<tr>
    <td>40.c. Solo Parent?</td>
    <td colspan="3">
        <div class="checkbox-group">
            <label>
                <input type="radio" value="1" wire:model="form.is_solo_parent"> YES
            </label>
            <label>
                <input type="radio" value="0" wire:model="form.is_solo_parent"> NO
            </label>
            <input type="text" wire:model="form.solo_parent_details" placeholder="Provide details" style="margin-left:50px; width:auto;">
        </div>
    </td>
</tr>


            {{-- 41. References --}}
            <tr>
                <td colspan="4"><strong>41. REFERENCES</strong> <span style="font-size:15px;">(Not related by consanguinity or affinity)</span></td>
            </tr>
            <tr>
                <th style="width:30%">NAME</th>
                <th style="width:40%">ADDRESS</th>
                <th colspan="2">TEL. NO.</th>
            </tr>

            @for($i = 0; $i < 3; $i++)
            <tr>
                <td><input type="text" wire:model="form.references.{{ $i }}.name" style="width:400px;"></td>
                <td><input type="text" wire:model="form.references.{{ $i }}.address" style="width:430px;"></td>
                <td colspan="2"><input type="text" wire:model="form.references.{{ $i }}.tel" style="width:400px;"></td>
            </tr>
            @endfor

            {{-- 42. Declaration --}}
            <tr>
                <td colspan="4">
                    <strong>42.</strong> I declare under oath that I have personally accomplished this Personal Data Sheet,
                    which is a true, correct, and complete statement pursuant to the provisions of pertinent laws, rules, and regulations.
                </td>
            </tr>

            {{-- Government ID --}}
            <tr>
                <td colspan="2" style="margin-left:200px;">Government Issued ID:<br><input type="text" wire:model="form.gov_id_type" style="margin-left:200px;"></td>
                <td colspan="2">ID / License / Passport No.:<br><input type="text" wire:model="form.gov_id_no"></td>
            </tr>
            <tr>
                <td colspan="4" style="margin-left:200px;">Date / Place of Issuance:<br><input type="text" wire:model="form.gov_id_issued" style="margin-left:200px;"></td>
            </tr>
        </table>

        {{-- SIGNATURE & THUMB --}}
        <table style="width:100%; margin-top:10px;">
            <tr>
                <td style="width:60%; text-align:center;">
                    <div style="border:1px solid #000; height:90px;">
                        <div style="border-bottom:1px solid #000; height:55px;">
                            <span style="font-size:10px;">SIGNATURE (Sign inside the box)</span>
                        </div>
                        <div style="height:35px;">
                            <span style="font-size:10px;">DATE ACCOMPLISHED</span><br>
                            <input type="date" wire:model="form.date_accomplished">
                        </div>
                    </div>
                </td>
                <td style="width:40%; text-align:center;">
                    <div style="border:1px solid #000; height:90px;">
                        <span style="font-size:10px;">RIGHT THUMBMARK</span>
                    </div>
                </td>
            </tr>
        </table>

        {{-- OATH --}}
        <table style="width:100%; margin-top:10px;">
            <tr>
                <td style="width:60%;"></td>
                <td style="width:40%; text-align:center;">
                    <div style="border:1px solid #000; height:60px;">
                        <span style="font-size:10px;">PERSON ADMINISTERING OATH</span>
                    </div>
                </td>
            </tr>
        </table>

        <p style="text-align:right; font-size:10px; margin-top:10px;">
            CS FORM 212 (Revised 2017) – Page 4 of 4
        </p>
    </div>
    <div style="text-align: center; margin-top: 20px;">
        <button type="submit" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Save Personal Data Sheet
        </button>
    </div>
</form>
</div>
