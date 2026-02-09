@push('styles')
<style>
    .pds-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }

    .pds-table td,
    .pds-table th {
        border: 1px solid #000;
        padding: 4px;
        vertical-align: top;
    }

    .pds-table input {
        width: 100%;
        border: none;
        outline: none;
        background: transparent;
        font-size: 11px;
    }

    .pds-note {
        font-size: 11px;
        text-align: justify;
    }

    .checkbox {
        display: inline-block;
        width: 12px;
        height: 12px;
        border: 1px solid #000;
        text-align: center;
        line-height: 10px;
    }
</style>
@endpush


{{-- C1 – PERSONAL INFORMATION --}}
<div class="page-break">
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
            <td><input type="text" wire:model.defer="pds.surname"></td>
            <td>FIRST NAME</td>
            <td><input type="text" wire:model.defer="pds.first_name"></td>
        </tr>

        <tr>
            <td>MIDDLE NAME</td>
            <td><input type="text" wire:model.defer="pds.middle_name"></td>
            <td>NAME EXTENSION</td>
            <td><input type="text" wire:model.defer="pds.name_extension"></td>
        </tr>

        <tr>
            <td>2. DATE OF BIRTH</td>
            <td>
                <input type="date" wire:model.defer="pds.date_of_birth">
            </td>
            <td>3. PLACE OF BIRTH</td>
            <td>
                <input type="text" wire:model.defer="pds.place_of_birth">
            </td>
        </tr>

        <tr>
            <td>4. SEX</td>
            <td>
                <label>
                    <input type="radio" wire:model="pds.sex" value="Male"> Male
                </label>
                <label style="margin-left:10px;">
                    <input type="radio" wire:model="pds.sex" value="Female"> Female
                </label>
            </td>

            <td>5. CIVIL STATUS</td>
            <td>
                @foreach(['Single','Married','Widowed','Separated'] as $status)
                    <label style="margin-right:8px;">
                        <input type="radio" wire:model="pds.civil_status" value="{{ $status }}">
                        {{ $status }}
                    </label>
                @endforeach
            </td>
        </tr>

        <tr>
            <td>6. CITIZENSHIP</td>
            <td colspan="3">
                <label>
                    <input type="checkbox" wire:model="pds.filipino">
                    Filipino
                </label>

                <label style="margin-left:15px;">
                    <input type="checkbox" wire:model="pds.dual_citizenship">
                    Dual Citizenship
                </label>

                <span style="margin-left:10px;">
                    <input type="text"
                           placeholder="If dual, indicate country"
                           wire:model.defer="pds.dual_citizenship_country"
                           style="width:200px;">
                </span>
            </td>
        </tr>

        <tr>
            <td>7. RESIDENTIAL ADDRESS</td>
            <td colspan="3">
                <input type="text" wire:model.defer="pds.residential_address">
            </td>
        </tr>

        <tr>
            <td>ZIP CODE</td>
            <td><input type="text" wire:model.defer="pds.res_zip"></td>
            <td>TELEPHONE NO.</td>
            <td><input type="text" wire:model.defer="pds.res_tel"></td>
        </tr>

        <tr>
            <td>8. PERMANENT ADDRESS</td>
            <td colspan="3">
                <input type="text" wire:model.defer="pds.permanent_address">
            </td>
        </tr>

        <tr>
            <td>ZIP CODE</td>
            <td><input type="text" wire:model.defer="pds.perm_zip"></td>
            <td>TELEPHONE NO.</td>
            <td><input type="text" wire:model.defer="pds.perm_tel"></td>
        </tr>

        <tr>
            <td>9. MOBILE NO.</td>
            <td><input type="text" wire:model.defer="pds.mobile"></td>
            <td>EMAIL</td>
            <td><input type="email" wire:model.defer="pds.email"></td>
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
                <input type="text" wire:model.defer="pds.spouse_surname">
            </td>
            <td>FIRST NAME</td>
            <td>
                <input type="text" wire:model.defer="pds.spouse_firstname">
            </td>
        </tr>

        <tr>
            <td>OCCUPATION</td>
            <td>
                <input type="text" wire:model.defer="pds.spouse_occupation">
            </td>
            <td>EMPLOYER / BUSINESS NAME</td>
            <td>
                <input type="text" wire:model.defer="pds.spouse_employer">
            </td>
        </tr>

        <tr>
            <td>FATHER'S NAME</td>
            <td colspan="3">
                <input type="text" wire:model.defer="pds.father_name">
            </td>
        </tr>

        <tr>
            <td>MOTHER'S MAIDEN NAME</td>
            <td colspan="3">
                <input type="text" wire:model.defer="pds.mother_name">
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

        @foreach($pds->children ?? [] as $index => $child)
            <tr>
                <td>
                    <input type="text"
                        wire:model.defer="pds.children.{{ $index }}.name">
                </td>

                <td>
                    <input type="date"
                        wire:model.defer="pds.children.{{ $index }}.birthdate">
                </td>

                <td style="text-align:center;">
                    <button type="button"
                            wire:click="removeChild({{ $index }})">
                        ❌
                    </button>
                </td>
            </tr>
        @endforeach
    </table>

    <button type="button"
            wire:click="addChild"
            style="margin-top:5px; font-size:11px;">
        ➕ Add Child
    </button>

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

        @foreach($pds->education ?? [] as $index => $edu)
            <tr>
                <td>
                    <input type="text"
                        wire:model.defer="pds.education.{{ $index }}.level">
                </td>

                <td>
                    <input type="text"
                        wire:model.defer="pds.education.{{ $index }}.school_name">
                </td>

                <td>
                    <input type="text"
                        wire:model.defer="pds.education.{{ $index }}.degree">
                </td>

                <td>
                    <input type="text"
                        wire:model.defer="pds.education.{{ $index }}.from_year">
                </td>

                <td>
                    <input type="text"
                        wire:model.defer="pds.education.{{ $index }}.to_year">
                </td>

                <td>
                    <input type="text"
                        wire:model.defer="pds.education.{{ $index }}.honors">
                </td>
            </tr>
        @endforeach
    </table>

    <button type="button"
            wire:click="addEducation"
            style="margin-top:5px; font-size:11px;">
        ➕ Add Education Row
    </button>
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

            @foreach($pds->civil_service_eligibility ?? [] as $index => $el)
            <tr>
                <td><input type="text" wire:model.defer="pds.civil_service_eligibility.{{ $index }}.career_service"></td>
                <td><input type="text" wire:model.defer="pds.civil_service_eligibility.{{ $index }}.rating"></td>
                <td><input type="date" wire:model.defer="pds.civil_service_eligibility.{{ $index }}.exam_date"></td>
                <td><input type="text" wire:model.defer="pds.civil_service_eligibility.{{ $index }}.place"></td>
                <td><input type="text" wire:model.defer="pds.civil_service_eligibility.{{ $index }}.license_no"></td>
                <td><input type="date" wire:model.defer="pds.civil_service_eligibility.{{ $index }}.validity"></td>
                <td style="text-align:center;">
                    <button type="button" wire:click="removeEligibility({{ $index }})">❌</button>
                </td>
            </tr>
            @endforeach
        </table>

        <button type="button" wire:click="addEligibility" style="margin-top:5px; font-size:11px;">
            ➕ Add Eligibility
        </button>

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

            @foreach($pds->work_experience ?? [] as $index => $work)
            <tr>
                <td><input type="date" wire:model.defer="pds.work_experience.{{ $index }}.from"></td>
                <td><input type="date" wire:model.defer="pds.work_experience.{{ $index }}.to"></td>
                <td><input type="text" wire:model.defer="pds.work_experience.{{ $index }}.position"></td>
                <td><input type="text" wire:model.defer="pds.work_experience.{{ $index }}.agency"></td>
                <td><input type="text" wire:model.defer="pds.work_experience.{{ $index }}.salary"></td>
                <td><input type="text" wire:model.defer="pds.work_experience.{{ $index }}.salary_grade"></td>
                <td><input type="text" wire:model.defer="pds.work_experience.{{ $index }}.status"></td>
                <td>
                    <label>
                        <input type="checkbox" wire:model="pds.work_experience.{{ $index }}.is_government">
                        YES
                    </label>
                </td>
                <td style="text-align:center;">
                    <button type="button" wire:click="removeWork({{ $index }})">❌</button>
                </td>
            </tr>
            @endforeach
        </table>

        <button type="button" wire:click="addWork" style="margin-top:5px; font-size:11px;">
            ➕ Add Work Experience
        </button>
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
            @foreach($pds->voluntary_work ?? [] as $index => $work)
            <tr>
                <td><input type="text" wire:model.defer="pds.voluntary_work.{{ $index }}.organization_name"></td>
                <td><input type="date" wire:model.defer="pds.voluntary_work.{{ $index }}.from_date"></td>
                <td><input type="date" wire:model.defer="pds.voluntary_work.{{ $index }}.to_date"></td>
                <td>
                    <input type="text" wire:model.defer="pds.voluntary_work.{{ $index }}.hours"> /
                    <input type="text" wire:model.defer="pds.voluntary_work.{{ $index }}.position">
                </td>
                <td style="text-align:center;">
                    <button type="button" wire:click="removeVoluntary({{ $index }})">❌</button>
                </td>
            </tr>
            @endforeach
        </table>
        <button type="button" wire:click="addVoluntary" style="margin-top:5px; font-size:11px;">➕ Add Voluntary Work</button>

        <br><br>

        {{-- VII. LEARNING AND DEVELOPMENT (L&D) --}}
        <table class="table-bordered pds-table">
            <tr>
                <th colspan="5">VII. LEARNING AND DEVELOPMENT (L&D) INTERVENTIONS / TRAINING PROGRAMS ATTENDED</th>
            </tr>
            <tr>
                <th style="width:20%">TITLE OF TRAINING</th>
                <th style="width:20%">INCLUSIVE DATES (FROM)</th>
                <th style="width:20%">INCLUSIVE DATES (TO)</th>
                <th style="width:20%">NUMBER OF HOURS</th>
                <th style="width:20%">TYPE / CONDUCTED BY</th>
            </tr>
            @foreach($pds->learning_development ?? [] as $index => $ld)
            <tr>
                <td><input type="text" wire:model.defer="pds.learning_development.{{ $index }}.training_title"></td>
                <td><input type="date" wire:model.defer="pds.learning_development.{{ $index }}.from_date"></td>
                <td><input type="date" wire:model.defer="pds.learning_development.{{ $index }}.to_date"></td>
                <td><input type="text" wire:model.defer="pds.learning_development.{{ $index }}.hours"></td>
                <td>
                    <input type="text" wire:model.defer="pds.learning_development.{{ $index }}.type"> /
                    <input type="text" wire:model.defer="pds.learning_development.{{ $index }}.conducted_by">
                </td>
                <td style="text-align:center;">
                    <button type="button" wire:click="removeLD({{ $index }})">❌</button>
                </td>
            </tr>
            @endforeach
        </table>
        <button type="button" wire:click="addLD" style="margin-top:5px; font-size:11px;">➕ Add L&D Entry</button>

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
                    count($pds['special_skills'] ?? []),
                    count($pds['non_academic_distinctions'] ?? []),
                    count($pds['membership_association'] ?? [])
                );
            @endphp
            @for($i = 0; $i < $maxRows; $i++)
            <tr>
                <td>
                    <input type="text" wire:model.defer="pds.special_skills.{{ $i }}.skill">
                </td>
                <td>
                    <input type="text" wire:model.defer="pds.non_academic_distinctions.{{ $i }}.distinction">
                </td>
                <td>
                    <input type="text" wire:model.defer="pds.membership_association.{{ $i }}.organization">
                </td>
                <td style="text-align:center;">
                    <button type="button" wire:click="removeOther({{ $i }})">❌</button>
                </td>
            </tr>
            @endfor
        </table>
        <button type="button" wire:click="addOther" style="margin-top:5px; font-size:11px;">➕ Add Other Info</button>
    </div>
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
                <td>
                    <input type="checkbox" wire:model="pds.related_third_degree"> YES
                </td>
                <td colspan="2">
                    <input type="text" wire:model.defer="pds.related_third_degree_details" placeholder="Provide details">
                </td>
            </tr>
            <tr>
                <td>b. Within the fourth degree?</td>
                <td>
                    <input type="checkbox" wire:model="pds.related_fourth_degree"> YES
                </td>
                <td colspan="2">
                    <input type="text" wire:model.defer="pds.related_fourth_degree_details" placeholder="Provide details">
                </td>
            </tr>

            {{-- 35. Administrative case --}}
            <tr>
                <td>35. Have you ever been found guilty of any administrative offense?</td>
                <td>
                    <input type="checkbox" wire:model="pds.has_admin_case"> YES
                </td>
                <td colspan="2">
                    <input type="text" wire:model.defer="pds.admin_case_details" placeholder="Provide details">
                </td>
            </tr>

            {{-- 36. Criminal case --}}
            <tr>
                <td>36. Have you been criminally charged before any court?</td>
                <td><input type="checkbox" wire:model="pds.has_criminal_case"> YES</td>
                <td>
                    Status: <input type="text" wire:model.defer="pds.criminal_case_status">
                </td>
                <td>
                    Date Filed: <input type="date" wire:model.defer="pds.criminal_case_date_filed">
                </td>
            </tr>

            {{-- 37. Conviction --}}
            <tr>
                <td>37. Have you ever been convicted of any crime or violation of any law?</td>
                <td><input type="checkbox" wire:model="pds.has_conviction"> YES</td>
                <td colspan="2">
                    <input type="text" wire:model.defer="pds.conviction_details" placeholder="Provide details">
                </td>
            </tr>

            {{-- 38. Separation --}}
            <tr>
                <td>38. Have you ever been separated from the service?</td>
                <td><input type="checkbox" wire:model="pds.has_been_separated"> YES</td>
                <td colspan="2">
                    <input type="text" wire:model.defer="pds.separation_details" placeholder="Provide details">
                </td>
            </tr>

            {{-- 39. Election --}}
            <tr>
                <td>39. Have you ever been a candidate in a national or local election?</td>
                <td><input type="checkbox" wire:model="pds.has_election_candidacy"> YES</td>
                <td colspan="2">
                    <input type="text" wire:model.defer="pds.election_candidacy_details" placeholder="Provide details">
                </td>
            </tr>

            {{-- 40. Other personal info --}}
            <tr>
                <td>40.a. Indigenous Group?</td>
                <td><input type="checkbox" wire:model="pds.is_indigenous"> YES</td>
                <td colspan="2"><input type="text" wire:model.defer="pds.indigenous_details"></td>
            </tr>
            <tr>
                <td>40.b. Person with Disability?</td>
                <td><input type="checkbox" wire:model="pds.has_disability"> YES</td>
                <td colspan="2"><input type="text" wire:model.defer="pds.disability_details"></td>
            </tr>
            <tr>
                <td>40.c. Solo Parent?</td>
                <td><input type="checkbox" wire:model="pds.is_solo_parent"> YES</td>
                <td colspan="2"><input type="text" wire:model.defer="pds.solo_parent_details"></td>
            </tr>

            {{-- 41. References --}}
            <tr>
                <td colspan="4"><strong>41. REFERENCES</strong> <span style="font-size:10px;">(Not related by consanguinity or affinity)</span></td>
            </tr>
            <tr>
                <th style="width:30%">NAME</th>
                <th style="width:40%">ADDRESS</th>
                <th colspan="2">TEL. NO.</th>
            </tr>

            @foreach($pds->references ?? [[], [], []] as $index => $ref)
            <tr>
                <td><input type="text" wire:model.defer="pds.references.{{ $index }}.name"></td>
                <td><input type="text" wire:model.defer="pds.references.{{ $index }}.address"></td>
                <td colspan="2"><input type="text" wire:model.defer="pds.references.{{ $index }}.tel"></td>
            </tr>
            @endforeach

            {{-- 42. Declaration --}}
            <tr>
                <td colspan="4">
                    <strong>42.</strong> I declare under oath that I have personally accomplished this Personal Data Sheet,
                    which is a true, correct, and complete statement pursuant to the provisions of pertinent laws, rules, and regulations.
                </td>
            </tr>

            {{-- Government ID --}}
            <tr>
                <td colspan="2">Government Issued ID:<br><input type="text" wire:model.defer="pds.gov_id_type"></td>
                <td colspan="2">ID / License / Passport No.:<br><input type="text" wire:model.defer="pds.gov_id_no"></td>
            </tr>
            <tr>
                <td colspan="4">Date / Place of Issuance:<br><input type="text" wire:model.defer="pds.gov_id_issued"></td>
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
                            <input type="date" wire:model.defer="pds.date_accomplished">
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

</div>
