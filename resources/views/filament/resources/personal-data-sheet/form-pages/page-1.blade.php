{{-- PAGE 1: PERSONAL INFORMATION, FAMILY BACKGROUND, EDUCATION (CS Form 212 Revised 2025) --}}
@php $ro = $isReadOnly ?? false; $dis = $ro ? 'disabled readonly' : ''; $disCb = $ro ? 'disabled' : ''; @endphp
<div class="pds-form-page">

    {{-- HEADER --}}
    <table style="width:100%; margin-bottom: 10px; border: none;">
        <tr>
            <td style="width:20%; font-size:8pt; border: none;"><em>CS Form No. 212</em><br><em>Revised 2025</em></td>
            <td style="width:60%; text-align:center; border: none;"><div style="font-size:12pt; font-weight:bold;">PERSONAL DATA SHEET</div></td>
            <td style="width:20%; text-align:right; font-size:8pt; border: none;"><em>Page 1 of 4</em></td>
        </tr>
    </table>

    <div class="warning-text"><strong>WARNING:</strong> Any misrepresentation made in the Personal Data Sheet and the Work Experience Sheet shall cause the filing of administrative/criminal case/s against the person concerned.</div>
    <div class="warning-text"><strong>READ THE ATTACHED GUIDE TO FILLING OUT THE PERSONAL DATA SHEET (PDS) BEFORE ACCOMPLISHING THE PDS FORM.</strong></div>
    <div class="warning-text">Print legibly. Tick appropriate boxes (☑) and use separate sheet if necessary. Indicate N/A if not applicable. <strong>DO NOT ABBREVIATE.</strong></div>

    <div class="section-title">I. PERSONAL INFORMATION</div>

    <table class="pds-table">
        <tr>
            <td class="label-cell">2. SURNAME</td>
            <td class="input-cell" colspan="3"><input type="text" wire:model="data.surname" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell">FIRST NAME</td>
            <td class="input-cell" colspan="2"><input type="text" wire:model="data.first_name" class="pds-input" {{ $dis }} /></td>
            <td class="input-cell" style="width:20%;">
                <div style="font-size:7pt; color:#666;">NAME EXTENSION (JR., SR)</div>
                <input type="text" wire:model="data.name_extension" class="pds-input" {{ $dis }} />
            </td>
        </tr>
        <tr>
            <td class="label-cell">MIDDLE NAME</td>
            <td class="input-cell" colspan="3"><input type="text" wire:model="data.middle_name" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell">3. DATE OF BIRTH<br><span style="font-size:6pt;">(dd/mm/yyyy)</span></td>
            <td class="input-cell"><input type="date" wire:model="data.date_of_birth" class="pds-input" {{ $dis }} placeholder="dd/mm/yyyy" /></td>
            <td class="label-cell">16. PLACE OF BIRTH</td>
            <td class="input-cell"><input type="text" wire:model="data.place_of_birth" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell">4. SEX AT BIRTH</td>
            <td class="input-cell">
                <select wire:model="data.sex" class="pds-input" {{ $dis }}>
                    <option value="">Select</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </td>
            <td class="label-cell">17. CIVIL STATUS</td>
            <td class="input-cell">
                <select wire:model="data.civil_status" class="pds-input" {{ $dis }}>
                    <option value="">Select</option>
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                    <option value="Widowed">Widowed</option>
                    <option value="Separated">Separated</option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="label-cell">5. HEIGHT (cm)</td>
            <td class="input-cell"><input type="number" step="0.01" wire:model="data.height" class="pds-input" {{ $dis }} /></td>
            <td class="label-cell">18. CITIZENSHIP</td>
            <td class="input-cell" rowspan="2">
                <label><input type="checkbox" wire:model="data.filipino" {{ $disCb }} /> Filipino</label><br>
                <label><input type="checkbox" wire:model="data.dual_citizenship" {{ $disCb }} /> Dual Citizenship</label>
                @if($this->data['dual_citizenship'] ?? false)
                    <input type="text" wire:model="data.dual_citizenship_country" placeholder="Country" class="pds-input" style="margin-top:5px;" {{ $dis }} />
                @endif
            </td>
        </tr>
        <tr>
            <td class="label-cell">6. WEIGHT (kg)</td>
            <td class="input-cell"><input type="number" step="0.1" wire:model="data.weight" class="pds-input" {{ $dis }} /></td>
            <td class="label-cell"></td>
        </tr>
        <tr>
            <td class="label-cell">7. BLOOD TYPE</td>
            <td class="input-cell"><input type="text" wire:model="data.blood_type" class="pds-input" {{ $dis }} /></td>
            <td class="label-cell">19. RESIDENTIAL ADDRESS</td>
            <td class="input-cell"></td>
        </tr>
        <tr>
            <td class="label-cell">8. UMID ID NO.</td>
            <td class="input-cell"><input type="text" wire:model="data.umid_id_no" class="pds-input" {{ $dis }} /></td>
            <td class="label-cell" style="font-size:7pt;">House/Block/Lot No.</td>
            <td class="input-cell"><input type="text" wire:model="data.res_house_block_lot_no" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell">9. ID NO.</td>
            <td class="input-cell"><input type="text" wire:model="data.id_no" class="pds-input" {{ $dis }} /></td>
            <td class="label-cell">Street</td>
            <td class="input-cell"><input type="text" wire:model="data.res_street" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell">10. PHILHEALTH NO.</td>
            <td class="input-cell"><input type="text" wire:model="data.philhealth_no" class="pds-input" {{ $dis }} /></td>
            <td class="label-cell">Subdivision/Village</td>
            <td class="input-cell"><input type="text" wire:model="data.res_subdivision_village" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell">11. SSS NO.</td>
            <td class="input-cell"><input type="text" wire:model="data.sss_no" class="pds-input" {{ $dis }} /></td>
            <td class="label-cell">Barangay</td>
            <td class="input-cell"><input type="text" wire:model="data.res_barangay" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell">12. TIN NO.</td>
            <td class="input-cell"><input type="text" wire:model="data.tin_no" class="pds-input" {{ $dis }} /></td>
            <td class="label-cell">City/Municipality</td>
            <td class="input-cell"><input type="text" wire:model="data.res_city_municipality" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell">13. PhilSys Number (PSN)</td>
            <td class="input-cell"><input type="text" wire:model="data.philsys_number" class="pds-input" placeholder="N/A if not registered" {{ $dis }} /></td>
            <td class="label-cell">Province</td>
            <td class="input-cell"><input type="text" wire:model="data.res_province" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell">14. AGENCY EMPLOYEE NO.</td>
            <td class="input-cell"><input type="text" wire:model="data.agency_employee_no" class="pds-input" {{ $dis }} /></td>
            <td class="label-cell">ZIP CODE</td>
            <td class="input-cell"><input type="text" wire:model="data.res_zip_code" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell">15. TELEPHONE NO.</td>
            <td class="input-cell"><input type="text" wire:model="data.telephone_no" class="pds-input" {{ $dis }} /></td>
            <td class="label-cell">20. PERMANENT ADDRESS</td>
            <td class="input-cell"><label><input type="checkbox" wire:model.live="data.same_as_residential" {{ $disCb }} /> Same as Residential</label></td>
        </tr>
        <tr>
            <td class="label-cell">16. MOBILE NO.</td>
            <td class="input-cell"><input type="text" wire:model="data.mobile" class="pds-input" {{ $dis }} /></td>
            <td class="label-cell" style="font-size:7pt;">House/Block/Lot No.</td>
            <td class="input-cell"><input type="text" wire:model="data.perm_house_block_lot_no" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell">17. E-MAIL ADDRESS (if any)</td>
            <td class="input-cell"><input type="email" wire:model="data.email" class="pds-input" {{ $dis }} /></td>
            <td class="label-cell">Street</td>
            <td class="input-cell"><input type="text" wire:model="data.perm_street" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell"></td><td class="input-cell"></td>
            <td class="label-cell">Subdivision/Village</td>
            <td class="input-cell"><input type="text" wire:model="data.perm_subdivision_village" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell"></td><td class="input-cell"></td>
            <td class="label-cell">Barangay</td>
            <td class="input-cell"><input type="text" wire:model="data.perm_barangay" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell"></td><td class="input-cell"></td>
            <td class="label-cell">City/Municipality</td>
            <td class="input-cell"><input type="text" wire:model="data.perm_city_municipality" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell"></td><td class="input-cell"></td>
            <td class="label-cell">Province</td>
            <td class="input-cell"><input type="text" wire:model="data.perm_province" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell"></td><td class="input-cell"></td>
            <td class="label-cell">ZIP CODE</td>
            <td class="input-cell"><input type="text" wire:model="data.perm_zip_code" class="pds-input" {{ $dis }} /></td>
        </tr>
    </table>

    <div class="section-title">II. FAMILY BACKGROUND</div>

    <table class="pds-table">
        <tr>
            <td class="label-cell">21. SPOUSE'S SURNAME</td>
            <td class="input-cell" colspan="3"><input type="text" wire:model="data.spouse_surname" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell">FIRST NAME</td>
            <td class="input-cell" colspan="2"><input type="text" wire:model="data.spouse_first_name" class="pds-input" {{ $dis }} /></td>
            <td class="input-cell" style="width:20%;">
                <div style="font-size:7pt; color:#666;">NAME EXTENSION (JR., SR)</div>
                <input type="text" wire:model="data.spouse_name_extension" class="pds-input" {{ $dis }} />
            </td>
        </tr>
        <tr>
            <td class="label-cell">MIDDLE NAME</td>
            <td class="input-cell" colspan="3"><input type="text" wire:model="data.spouse_middle_name" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell">OCCUPATION</td>
            <td class="input-cell"><input type="text" wire:model="data.spouse_occupation" class="pds-input" {{ $dis }} /></td>
            <td class="label-cell">EMPLOYER/BUSINESS NAME</td>
            <td class="input-cell"><input type="text" wire:model="data.spouse_employer_business_name" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell">BUSINESS ADDRESS</td>
            <td class="input-cell" colspan="3"><input type="text" wire:model="data.spouse_business_address" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell">TELEPHONE NO.</td>
            <td class="input-cell" colspan="3"><input type="text" wire:model="data.spouse_telephone_no" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell">22. FATHER'S SURNAME</td>
            <td class="input-cell" colspan="3"><input type="text" wire:model="data.father_surname" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell">FIRST NAME</td>
            <td class="input-cell" colspan="2"><input type="text" wire:model="data.father_first_name" class="pds-input" {{ $dis }} /></td>
            <td class="input-cell" style="width:20%;">
                <div style="font-size:7pt; color:#666;">NAME EXTENSION (JR., SR)</div>
                <input type="text" wire:model="data.father_name_extension" class="pds-input" {{ $dis }} />
            </td>
        </tr>
        <tr>
            <td class="label-cell">MIDDLE NAME</td>
            <td class="input-cell" colspan="3"><input type="text" wire:model="data.father_middle_name" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell">23. MOTHER'S MAIDEN NAME</td>
            <td class="input-cell" colspan="3"></td>
        </tr>
        <tr>
            <td class="label-cell">SURNAME</td>
            <td class="input-cell" colspan="3"><input type="text" wire:model="data.mother_surname" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell">FIRST NAME</td>
            <td class="input-cell" colspan="3"><input type="text" wire:model="data.mother_first_name" class="pds-input" {{ $dis }} /></td>
        </tr>
        <tr>
            <td class="label-cell">MIDDLE NAME</td>
            <td class="input-cell" colspan="3"><input type="text" wire:model="data.mother_middle_name" class="pds-input" {{ $dis }} /></td>
        </tr>
    </table>

    <div style="margin: 15px 0;">
        <div style="font-weight:bold; font-size:8pt; margin-bottom:5px;">24. NAME OF CHILDREN (Write full name and list all)</div>
        @foreach($this->data['children'] ?? [] as $index => $child)
        <div style="display: flex; gap: 10px; margin-bottom: 5px; align-items: center;">
            <input type="text" wire:model="data.children.{{ $index }}.name" placeholder="Full Name" class="pds-input" style="flex: 2;" {{ $dis }} />
            <input type="date" wire:model="data.children.{{ $index }}.birthdate" placeholder="dd/mm/yyyy" class="pds-input" style="flex: 1;" {{ $dis }} />
            @if(!$ro)
            <button type="button" wire:click="removeChild({{ $index }})" class="pds-btn-remove">Remove</button>
            @endif
        </div>
        @endforeach
        @if(!$ro)
        <button type="button" wire:click="addChild" class="pds-btn-add">+ Add Child</button>
        @endif
    </div>

    <div class="section-title">III. EDUCATIONAL BACKGROUND</div>

    <div style="margin: 15px 0;">
        @foreach($this->data['education'] ?? [] as $index => $edu)
        <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div>
                    <label style="font-size: 7pt; display: block;">Level</label>
                    <select wire:model="data.education.{{ $index }}.level" class="pds-input" {{ $dis }}>
                        <option value="">Select Level</option>
                        <option value="ELEMENTARY">Elementary</option>
                        <option value="SECONDARY">Secondary</option>
                        <option value="VOCATIONAL/TRADE COURSE">Vocational/Trade Course</option>
                        <option value="COLLEGE">College</option>
                        <option value="GRADUATE STUDIES">Graduate Studies</option>
                    </select>
                </div>
                <div><label style="font-size: 7pt; display: block;">School Name</label><input type="text" wire:model="data.education.{{ $index }}.school_name" class="pds-input" {{ $dis }} /></div>
                <div><label style="font-size: 7pt; display: block;">Degree/Course</label><input type="text" wire:model="data.education.{{ $index }}.degree" class="pds-input" {{ $dis }} /></div>
                <div><label style="font-size: 7pt; display: block;">From Year</label><input type="text" wire:model="data.education.{{ $index }}.from_year" maxlength="4" class="pds-input" {{ $dis }} /></div>
                <div><label style="font-size: 7pt; display: block;">To Year</label><input type="text" wire:model="data.education.{{ $index }}.to_year" maxlength="4" class="pds-input" {{ $dis }} /></div>
                <div><label style="font-size: 7pt; display: block;">Honors Received</label><input type="text" wire:model="data.education.{{ $index }}.honors" class="pds-input" {{ $dis }} /></div>
            </div>
            @if(!$ro)
            <button type="button" wire:click="removeEducation({{ $index }})" class="pds-btn-remove" style="margin-top: 5px;">Remove</button>
            @endif
        </div>
        @endforeach
        @if(!$ro)
        <button type="button" wire:click="addEducation" class="pds-btn-add">+ Add Education</button>
        @endif
    </div>

    <div style="text-align:center; font-size:7pt; margin-top:15px; font-style:italic;">CS FORM 212 (Revised 2025), Page 1 of 4</div>
</div>
