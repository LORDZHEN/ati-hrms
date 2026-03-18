{{-- PAGE 3: VOLUNTARY WORK, L&D, OTHER INFORMATION --}}
@php $ro = $isReadOnly ?? false; $dis = $ro ? 'disabled readonly' : ''; $disCb = $ro ? 'disabled' : ''; @endphp
<div class="pds-form-page">

    <table style="width:100%; margin-bottom: 10px; border: none;">
        <tr>
            <td style="width:20%; font-size:8pt; border: none;"><em>CS Form No. 212</em><br><em>Revised 2025</em></td>
            <td style="width:60%; text-align:center; border: none;"><div style="font-size:12pt; font-weight:bold;">PERSONAL DATA SHEET</div></td>
            <td style="width:20%; text-align:right; font-size:8pt; border: none;"><em>Page 3 of 4</em></td>
        </tr>
    </table>

    <div class="section-title">VI. VOLUNTARY WORK OR INVOLVEMENT IN CIVIC / NON-GOVERNMENT / PEOPLE / VOLUNTARY ORGANIZATION/S</div>

    <table class="pds-table" style="margin-top:10px;">
        <tr>
            <th class="header-row" rowspan="2" style="width:38%; font-size:6pt;">29. NAME & ADDRESS OF ORGANIZATION<br>(Write in full)</th>
            <th class="header-row" colspan="2" style="width:24%; font-size:6pt;">INCLUSIVE DATES<br>(dd/mm/yyyy)</th>
            <th class="header-row" rowspan="2" style="width:12%; font-size:6pt;">NUMBER OF<br>HOURS</th>
            <th class="header-row" rowspan="2" style="width:26%; font-size:6pt;">POSITION / NATURE OF WORK</th>
        </tr>
        <tr>
            <th class="header-row" style="font-size:6pt; width:12%;">From</th>
            <th class="header-row" style="font-size:6pt; width:12%;">To</th>
        </tr>
    </table>

    <div style="margin: 10px 0;">
        @foreach($this->data['voluntary_work'] ?? [] as $index => $vw)
        <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 2fr; gap: 10px;">
                <div><label style="font-size: 7pt; display: block;">Organization Name & Address</label><input type="text" wire:model="data.voluntary_work.{{ $index }}.organization_name" class="pds-input" {{ $dis }} /></div>
                <div><label style="font-size: 7pt; display: block;">From Date (dd/mm/yyyy)</label><input type="date" wire:model="data.voluntary_work.{{ $index }}.from_date" class="pds-input" {{ $dis }} /></div>
                <div><label style="font-size: 7pt; display: block;">To Date (dd/mm/yyyy)</label><input type="date" wire:model="data.voluntary_work.{{ $index }}.to_date" class="pds-input" {{ $dis }} /></div>
                <div><label style="font-size: 7pt; display: block;">Hours</label><input type="number" wire:model="data.voluntary_work.{{ $index }}.hours" class="pds-input" {{ $dis }} /></div>
                <div><label style="font-size: 7pt; display: block;">Position/Nature of Work</label><input type="text" wire:model="data.voluntary_work.{{ $index }}.position" class="pds-input" {{ $dis }} /></div>
            </div>
            @if(!$ro)
            <button type="button" wire:click="removeVoluntaryWork({{ $index }})" class="pds-btn-remove" style="margin-top: 5px;">Remove</button>
            @endif
        </div>
        @endforeach
        @if(!$ro)
        <button type="button" wire:click="addVoluntaryWork" class="pds-btn-add">+ Add Voluntary Work</button>
        @endif
    </div>

    <div style="font-size:7pt; font-style:italic; text-align:center; margin:5px 0;">(Continue on separate sheet if necessary)</div>

    <div class="section-title" style="margin-top:20px;">VII. LEARNING AND DEVELOPMENT (L&D) INTERVENTIONS/TRAINING PROGRAMS ATTENDED</div>

    <div style="font-size:6pt; font-style:italic; margin:5px 0;">(Start from the most recent L&D/training program and include only the relevant L&D/training taken for the last five (5) years for Division Chief/Executive/Managerial positions)</div>

    <table class="pds-table">
        <tr>
            <th class="header-row" rowspan="2" style="width:35%; font-size:6pt;">30. TITLE OF LEARNING AND DEVELOPMENT<br>INTERVENTIONS/TRAINING PROGRAMS<br>(Write in full)</th>
            <th class="header-row" colspan="2" style="width:22%; font-size:6pt;">INCLUSIVE DATES<br>(dd/mm/yyyy)</th>
            <th class="header-row" rowspan="2" style="width:10%; font-size:6pt;">NUMBER OF<br>HOURS</th>
            <th class="header-row" rowspan="2" style="width:12%; font-size:6pt;">Type of LD<br>(Managerial/<br>Supervisory/<br>Technical/etc)</th>
            <th class="header-row" rowspan="2" style="width:21%; font-size:6pt;">CONDUCTED/ SPONSORED BY<br>(Write in full)</th>
        </tr>
        <tr>
            <th class="header-row" style="font-size:6pt; width:11%;">From</th>
            <th class="header-row" style="font-size:6pt; width:11%;">To</th>
        </tr>
    </table>

    <div style="margin: 10px 0;">
        @foreach($this->data['learning_development'] ?? [] as $index => $ld)
        <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr 2fr; gap: 10px;">
                <div><label style="font-size: 7pt; display: block;">Training Title</label><input type="text" wire:model="data.learning_development.{{ $index }}.training_title" class="pds-input" {{ $dis }} /></div>
                <div><label style="font-size: 7pt; display: block;">From Date (dd/mm/yyyy)</label><input type="date" wire:model="data.learning_development.{{ $index }}.from_date" class="pds-input" {{ $dis }} /></div>
                <div><label style="font-size: 7pt; display: block;">To Date (dd/mm/yyyy)</label><input type="date" wire:model="data.learning_development.{{ $index }}.to_date" class="pds-input" {{ $dis }} /></div>
                <div><label style="font-size: 7pt; display: block;">Hours</label><input type="number" wire:model="data.learning_development.{{ $index }}.hours" class="pds-input" {{ $dis }} /></div>
                <div><label style="font-size: 7pt; display: block;">Type</label><input type="text" wire:model="data.learning_development.{{ $index }}.type" class="pds-input" {{ $dis }} /></div>
                <div><label style="font-size: 7pt; display: block;">Conducted By</label><input type="text" wire:model="data.learning_development.{{ $index }}.conducted_by" class="pds-input" {{ $dis }} /></div>
            </div>
            @if(!$ro)
            <button type="button" wire:click="removeLearningDevelopment({{ $index }})" class="pds-btn-remove" style="margin-top: 5px;">Remove</button>
            @endif
        </div>
        @endforeach
        @if(!$ro)
        <button type="button" wire:click="addLearningDevelopment" class="pds-btn-add">+ Add Training</button>
        @endif
    </div>

    <div style="font-size:7pt; font-style:italic; text-align:center; margin:5px 0;">(Continue on separate sheet if necessary)</div>

    <div class="section-title" style="margin-top:20px;">VIII. OTHER INFORMATION</div>

    <table class="pds-table" style="margin-top:10px;">
        <tr>
            <th style="width:33.33%; font-size:7pt; background-color:#d9d9d9;">31. SPECIAL SKILLS and HOBBIES</th>
            <th style="width:33.33%; font-size:7pt; background-color:#d9d9d9;">32. NON-ACADEMIC DISTINCTIONS / RECOGNITION<br><span style="font-size:6pt; font-weight:normal;">(Write in full)</span></th>
            <th style="width:33.33%; font-size:7pt; background-color:#d9d9d9;">33. MEMBERSHIP IN ASSOCIATION/ORGANIZATION<br><span style="font-size:6pt; font-weight:normal;">(Write in full)</span></th>
        </tr>
        <tr>
            <td style="vertical-align:top; padding:5px;">
                @foreach($this->data['special_skills'] ?? [] as $index => $skill)
                <div style="display: flex; gap: 5px; margin-bottom: 3px;">
                    <input type="text" wire:model="data.special_skills.{{ $index }}.skill" class="pds-input" placeholder="Skill or hobby" {{ $dis }} />
                    @if(!$ro)<button type="button" wire:click="removeSpecialSkill({{ $index }})" class="pds-btn-remove" style="font-size: 6pt; padding: 2px 5px;">×</button>@endif
                </div>
                @endforeach
                @if(!$ro)<button type="button" wire:click="addSpecialSkill" class="pds-btn-add" style="font-size: 7pt; padding: 3px 8px;">+ Add</button>@endif
            </td>
            <td style="vertical-align:top; padding:5px;">
                @foreach($this->data['non_academic_distinctions'] ?? [] as $index => $distinction)
                <div style="display: flex; gap: 5px; margin-bottom: 3px;">
                    <input type="text" wire:model="data.non_academic_distinctions.{{ $index }}.distinction" class="pds-input" placeholder="Distinction" {{ $dis }} />
                    @if(!$ro)<button type="button" wire:click="removeDistinction({{ $index }})" class="pds-btn-remove" style="font-size: 6pt; padding: 2px 5px;">×</button>@endif
                </div>
                @endforeach
                @if(!$ro)<button type="button" wire:click="addDistinction" class="pds-btn-add" style="font-size: 7pt; padding: 3px 8px;">+ Add</button>@endif
            </td>
            <td style="vertical-align:top; padding:5px;">
                @foreach($this->data['membership_association'] ?? [] as $index => $membership)
                <div style="display: flex; gap: 5px; margin-bottom: 3px;">
                    <input type="text" wire:model="data.membership_association.{{ $index }}.organization" class="pds-input" placeholder="Organization" {{ $dis }} />
                    @if(!$ro)<button type="button" wire:click="removeMembership({{ $index }})" class="pds-btn-remove" style="font-size: 6pt; padding: 2px 5px;">×</button>@endif
                </div>
                @endforeach
                @if(!$ro)<button type="button" wire:click="addMembership" class="pds-btn-add" style="font-size: 7pt; padding: 3px 8px;">+ Add</button>@endif
            </td>
        </tr>
    </table>

    <div style="font-size:7pt; font-style:italic; text-align:center; margin:5px 0;">(Continue on separate sheet if necessary)</div>
    <div style="text-align:center; font-size:7pt; margin-top:15px; font-style:italic;">CS FORM 212 (Revised 2025), Page 3 of 4</div>
</div>
