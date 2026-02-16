{{-- PAGE 2: CIVIL SERVICE ELIGIBILITY & WORK EXPERIENCE --}}
<div class="pds-form-page">

    {{-- PAGE HEADER --}}
    <table style="width:100%; margin-bottom: 10px; border: none;">
        <tr>
            <td style="width:20%; font-size:8pt; border: none;">
                <em>CS Form No. 212</em><br>
                <em>Revised 2017</em>
            </td>
            <td style="width:60%; text-align:center; border: none;">
                <div style="font-size:12pt; font-weight:bold;">PERSONAL DATA SHEET</div>
            </td>
            <td style="width:20%; text-align:right; font-size:8pt; border: none;">
                <em>Page 2 of 4</em>
            </td>
        </tr>
    </table>

    {{-- SECTION IV: CIVIL SERVICE ELIGIBILITY --}}
    <div class="section-title">IV. CIVIL SERVICE ELIGIBILITY</div>

    <table class="pds-table" style="margin-top:10px;">
        <tr>
            <th class="header-row" rowspan="2" style="width:28%; font-size:7pt;">
                27. CAREER SERVICE/ RA 1080 (BOARD/ BAR) UNDER<br>
                SPECIAL LAWS/ CES/ CSEE<br>
                BARANGAY ELIGIBILITY / DRIVER'S LICENSE
            </th>
            <th class="header-row" rowspan="2" style="width:8%; font-size:6pt;">
                RATING<br>
                (If Applicable)
            </th>
            <th class="header-row" rowspan="2" style="width:12%; font-size:6pt;">
                DATE OF<br>EXAMINATION /<br>CONFERMENT
            </th>
            <th class="header-row" rowspan="2" style="width:20%; font-size:6pt;">
                PLACE OF EXAMINATION /<br>CONFERMENT
            </th>
            <th class="header-row" colspan="2" style="width:32%; font-size:6pt;">
                LICENSE (if applicable)
            </th>
        </tr>
        <tr>
            <th class="header-row" style="font-size:6pt;">NUMBER</th>
            <th class="header-row" style="font-size:6pt;">Date of<br>Validity</th>
        </tr>
    </table>

    <div style="margin: 10px 0;">
        @foreach($this->data['civil_service_eligibility'] ?? [] as $index => $cs)
        <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 10px;">
                <div>
                    <label style="font-size: 7pt; display: block;">Career Service/Eligibility</label>
                    <input type="text" wire:model="data.civil_service_eligibility.{{ $index }}.career_service" class="pds-input" />
                </div>
                <div>
                    <label style="font-size: 7pt; display: block;">Rating</label>
                    <input type="number" wire:model="data.civil_service_eligibility.{{ $index }}.rating" class="pds-input" />
                </div>
                <div>
                    <label style="font-size: 7pt; display: block;">Exam Date</label>
                    <input type="date" wire:model="data.civil_service_eligibility.{{ $index }}.exam_date" class="pds-input" />
                </div>
                <div>
                    <label style="font-size: 7pt; display: block;">Place of Examination</label>
                    <input type="text" wire:model="data.civil_service_eligibility.{{ $index }}.place" class="pds-input" />
                </div>
                <div>
                    <label style="font-size: 7pt; display: block;">License No.</label>
                    <input type="text" wire:model="data.civil_service_eligibility.{{ $index }}.license_no" class="pds-input" />
                </div>
                <div>
                    <label style="font-size: 7pt; display: block;">Validity Date</label>
                    <input type="date" wire:model="data.civil_service_eligibility.{{ $index }}.validity" class="pds-input" />
                </div>
            </div>
            <button type="button" wire:click="removeCivilService({{ $index }})" class="pds-btn-remove" style="margin-top: 5px;">Remove</button>
        </div>
        @endforeach

        <button type="button" wire:click="addCivilService" class="pds-btn-add">+ Add Civil Service Eligibility</button>
    </div>

    <div style="font-size:7pt; font-style:italic; text-align:center; margin:5px 0;">
        (Continue on separate sheet if necessary)
    </div>

    {{-- SECTION V: WORK EXPERIENCE --}}
    <div class="section-title" style="margin-top:20px;">V. WORK EXPERIENCE</div>

    <div style="font-size:6pt; font-style:italic; margin:5px 0;">
        (Include private employment. Start from your recent work) Description of duties should be indicated in the attached Work Experience sheet.
    </div>

    <table class="pds-table">
        <tr>
            <th class="header-row" colspan="2" rowspan="2" style="width:18%; font-size:6pt;">
                28. INCLUSIVE DATES<br>
                (mm/dd/yyyy)
            </th>
            <th class="header-row" rowspan="2" style="width:22%; font-size:6pt;">
                POSITION TITLE<br>
                (Write in full/Do not abbreviate)
            </th>
            <th class="header-row" rowspan="2" style="width:25%; font-size:6pt;">
                DEPARTMENT / AGENCY / OFFICE / COMPANY<br>
                (Write in full/Do not abbreviate)
            </th>
            <th class="header-row" rowspan="2" style="width:10%; font-size:6pt;">
                MONTHLY<br>SALARY
            </th>
            <th class="header-row" rowspan="2" style="width:8%; font-size:6pt;">
                SALARY/<br>JOB/<br>PAY<br>GRADE
            </th>
            <th class="header-row" rowspan="2" style="width:10%; font-size:6pt;">
                STATUS OF<br>APPOINTMENT
            </th>
            <th class="header-row" rowspan="2" style="width:7%; font-size:6pt;">
                GOV'T<br>SERVICE<br>
                (Y/ N)
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
    </table>

    <div style="margin: 10px 0;">
        @foreach($this->data['work_experience'] ?? [] as $index => $work)
        <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 10px;">
                <div>
                    <label style="font-size: 7pt; display: block;">From Date</label>
                    <input type="date" wire:model="data.work_experience.{{ $index }}.from" class="pds-input" />
                </div>
                <div>
                    <label style="font-size: 7pt; display: block;">To Date</label>
                    <input type="date" wire:model="data.work_experience.{{ $index }}.to" class="pds-input" />
                </div>
                <div>
                    <label style="font-size: 7pt; display: block;">Position Title</label>
                    <input type="text" wire:model="data.work_experience.{{ $index }}.position" class="pds-input" />
                </div>
                <div style="grid-column: span 3;">
                    <label style="font-size: 7pt; display: block;">Department/Agency/Office/Company</label>
                    <input type="text" wire:model="data.work_experience.{{ $index }}.agency" class="pds-input" />
                </div>
                <div>
                    <label style="font-size: 7pt; display: block;">Monthly Salary</label>
                    <input type="number" wire:model="data.work_experience.{{ $index }}.salary" class="pds-input" />
                </div>
                <div>
                    <label style="font-size: 7pt; display: block;">Salary Grade</label>
                    <input type="text" wire:model="data.work_experience.{{ $index }}.salary_grade" class="pds-input" />
                </div>
                <div>
                    <label style="font-size: 7pt; display: block;">Status of Appointment</label>
                    <input type="text" wire:model="data.work_experience.{{ $index }}.status" class="pds-input" />
                </div>
                <div>
                    <label style="font-size: 7pt; display: block;">Gov't Service?</label>
                    <label><input type="radio" wire:model="data.work_experience.{{ $index }}.is_government" value="1" /> Yes</label>
                    <label><input type="radio" wire:model="data.work_experience.{{ $index }}.is_government" value="0" /> No</label>
                </div>
            </div>
            <button type="button" wire:click="removeWorkExperience({{ $index }})" class="pds-btn-remove" style="margin-top: 5px;">Remove</button>
        </div>
        @endforeach

        <button type="button" wire:click="addWorkExperience" class="pds-btn-add">+ Add Work Experience</button>
    </div>

    <div style="font-size:7pt; font-style:italic; text-align:center; margin:5px 0;">
        (Continue on separate sheet if necessary)
    </div>

    <div style="text-align:center; font-size:7pt; margin-top:15px; font-style:italic;">
        CS FORM 212 (Revised 2017), Page 2 of 4
    </div>
</div>
