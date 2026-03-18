{{-- PAGE 4: QUESTIONS, REFERENCES, DECLARATION --}}
@php $ro = $isReadOnly ?? false; $dis = $ro ? 'disabled readonly' : ''; $disCb = $ro ? 'disabled' : ''; @endphp
<div class="pds-form-page">

    <table style="width:100%; margin-bottom: 10px; border: none;">
        <tr>
            <td style="width:20%; font-size:8pt; border: none;"><em>CS Form No. 212</em><br><em>Revised 2025</em></td>
            <td style="width:60%; text-align:center; border: none;"><div style="font-size:12pt; font-weight:bold;">PERSONAL DATA SHEET</div></td>
            <td style="width:20%; text-align:right; font-size:8pt; border: none;"><em>Page 4 of 4</em></td>
        </tr>
    </table>

    <div class="section-title">ANSWER THE FOLLOWING QUESTIONS</div>

    <table class="pds-table" style="margin-top:10px;">
        <tr>
            <td colspan="2" style="font-size:7pt; padding:8px; background-color:#d9d9d9;">
                <strong>34.</strong> Are you related by consanguinity or affinity to the appointing or recommending authority, or to the chief of bureau or office or to the person who has immediate supervision over you in the Office, Bureau or Department where you will be appointed,
            </td>
        </tr>
        <tr>
            <td style="width:70%; font-size:7pt; padding:8px;">a. within the third degree?</td>
            <td style="width:30%; padding:5px;">
                <div style="display: flex; gap: 15px;">
                    <label class="radio-label"><input type="radio" wire:model.live="data.related_third_degree" value="1" class="radio-input" {{ $disCb }} /><span>Yes</span></label>
                    <label class="radio-label"><input type="radio" wire:model.live="data.related_third_degree" value="0" class="radio-input" {{ $disCb }} /><span>No</span></label>
                </div>
            </td>
        </tr>
        @if(($this->data['related_third_degree'] ?? null) == '1')
        <tr><td colspan="2" style="padding:5px;"><textarea wire:model="data.related_third_degree_details" class="pds-input" rows="2" placeholder="Please provide details" {{ $dis }}></textarea></td></tr>
        @endif

        <tr>
            <td style="font-size:7pt; padding:8px;">b. within the fourth degree (for Local Government Unit - Career Employees)?</td>
            <td style="padding:5px;">
                <div style="display: flex; gap: 15px;">
                    <label class="radio-label"><input type="radio" wire:model.live="data.related_fourth_degree" value="1" class="radio-input" {{ $disCb }} /><span>Yes</span></label>
                    <label class="radio-label"><input type="radio" wire:model.live="data.related_fourth_degree" value="0" class="radio-input" {{ $disCb }} /><span>No</span></label>
                </div>
            </td>
        </tr>
        @if(($this->data['related_fourth_degree'] ?? null) == '1')
        <tr><td colspan="2" style="padding:5px;"><textarea wire:model="data.related_fourth_degree_details" class="pds-input" rows="2" placeholder="Please provide details" {{ $dis }}></textarea></td></tr>
        @endif

        <tr>
            <td style="font-size:7pt; padding:8px;"><strong>35.</strong> a. Have you ever been found guilty of any administrative offense?</td>
            <td style="padding:5px;">
                <div style="display: flex; gap: 15px;">
                    <label class="radio-label"><input type="radio" wire:model.live="data.has_admin_case" value="1" class="radio-input" {{ $disCb }} /><span>Yes</span></label>
                    <label class="radio-label"><input type="radio" wire:model.live="data.has_admin_case" value="0" class="radio-input" {{ $disCb }} /><span>No</span></label>
                </div>
            </td>
        </tr>
        @if(($this->data['has_admin_case'] ?? null) == '1')
        <tr><td colspan="2" style="padding:5px;"><textarea wire:model="data.admin_case_details" class="pds-input" rows="2" placeholder="Please provide details" {{ $dis }}></textarea></td></tr>
        @endif

        <tr>
            <td style="font-size:7pt; padding:8px;">b. Have you been criminally charged before any court?</td>
            <td style="padding:5px;">
                <div style="display: flex; gap: 15px;">
                    <label class="radio-label"><input type="radio" wire:model.live="data.has_criminal_case" value="1" class="radio-input" {{ $disCb }} /><span>Yes</span></label>
                    <label class="radio-label"><input type="radio" wire:model.live="data.has_criminal_case" value="0" class="radio-input" {{ $disCb }} /><span>No</span></label>
                </div>
            </td>
        </tr>
        @if(($this->data['has_criminal_case'] ?? null) == '1')
        <tr>
            <td colspan="2" style="padding:5px;">
                <div style="display: flex; gap: 10px;">
                    <div style="flex: 1;"><label style="font-size: 7pt; display: block;">Date Filed</label><input type="date" wire:model="data.criminal_case_date_filed" class="pds-input" {{ $dis }} /></div>
                    <div style="flex: 2;"><label style="font-size: 7pt; display: block;">Status</label><input type="text" wire:model="data.criminal_case_status" class="pds-input" {{ $dis }} /></div>
                </div>
            </td>
        </tr>
        @endif

        <tr>
            <td style="font-size:7pt; padding:8px;"><strong>36.</strong> Have you ever been convicted of any crime or violation of any law, decree, ordinance or regulation by any court or tribunal?</td>
            <td style="padding:5px;">
                <div style="display: flex; gap: 15px;">
                    <label class="radio-label"><input type="radio" wire:model.live="data.has_conviction" value="1" class="radio-input" {{ $disCb }} /><span>Yes</span></label>
                    <label class="radio-label"><input type="radio" wire:model.live="data.has_conviction" value="0" class="radio-input" {{ $disCb }} /><span>No</span></label>
                </div>
            </td>
        </tr>
        @if(($this->data['has_conviction'] ?? null) == '1')
        <tr><td colspan="2" style="padding:5px;"><textarea wire:model="data.conviction_details" class="pds-input" rows="2" placeholder="Please provide details" {{ $dis }}></textarea></td></tr>
        @endif

        <tr>
            <td style="font-size:7pt; padding:8px;"><strong>37.</strong> Have you ever been separated from the service in any of the following modes: resignation, retirement, dropped from the rolls, dismissal, termination, end of term, finished contract or phased out (abolition) in the public or private sector?</td>
            <td style="padding:5px;">
                <div style="display: flex; gap: 15px;">
                    <label class="radio-label"><input type="radio" wire:model.live="data.has_been_separated" value="1" class="radio-input" {{ $disCb }} /><span>Yes</span></label>
                    <label class="radio-label"><input type="radio" wire:model.live="data.has_been_separated" value="0" class="radio-input" {{ $disCb }} /><span>No</span></label>
                </div>
            </td>
        </tr>
        @if(($this->data['has_been_separated'] ?? null) == '1')
        <tr><td colspan="2" style="padding:5px;"><textarea wire:model="data.separation_details" class="pds-input" rows="2" placeholder="Please provide details" {{ $dis }}></textarea></td></tr>
        @endif

        <tr>
            <td style="font-size:7pt; padding:8px;"><strong>38.</strong> Have you ever been a candidate in a national or local election held within the last year (except Barangay election)?</td>
            <td style="padding:5px;">
                <div style="display: flex; gap: 15px;">
                    <label class="radio-label"><input type="radio" wire:model.live="data.has_election_candidacy" value="1" class="radio-input" {{ $disCb }} /><span>Yes</span></label>
                    <label class="radio-label"><input type="radio" wire:model.live="data.has_election_candidacy" value="0" class="radio-input" {{ $disCb }} /><span>No</span></label>
                </div>
            </td>
        </tr>
        @if(($this->data['has_election_candidacy'] ?? null) == '1')
        <tr><td colspan="2" style="padding:5px;"><textarea wire:model="data.election_candidacy_details" class="pds-input" rows="2" placeholder="Please provide details" {{ $dis }}></textarea></td></tr>
        @endif

        <tr>
            <td style="font-size:7pt; padding:8px;"><strong>39.</strong> Have you resigned from the government service during the three (3)-year period from June 10, 1987 to November 2, 1990?</td>
            <td style="padding:5px;">
                <div style="display: flex; gap: 15px;">
                    <label class="radio-label"><input type="radio" wire:model.live="data.resigned_gov_service" value="1" class="radio-input" {{ $disCb }} /><span>Yes</span></label>
                    <label class="radio-label"><input type="radio" wire:model.live="data.resigned_gov_service" value="0" class="radio-input" {{ $disCb }} /><span>No</span></label>
                </div>
            </td>
        </tr>
        @if(($this->data['resigned_gov_service'] ?? null) == '1')
        <tr><td colspan="2" style="padding:5px;"><textarea wire:model="data.resigned_gov_service_details" class="pds-input" rows="2" placeholder="Please provide details" {{ $dis }}></textarea></td></tr>
        @endif

        <tr>
            <td colspan="2" style="font-size:7pt; padding:8px; background-color:#d9d9d9;">
                <strong>40.</strong> Pursuant to: (a) Indigenous People's Act (RA 8371); (b) Magna Carta for Disabled Persons (RA 7277); and (c) Solo Parents Welfare Act of 2000 (RA 8972), please answer the following items:
            </td>
        </tr>

        <tr>
            <td style="font-size:7pt; padding:8px; padding-left:30px;">a. Are you a member of any indigenous group?</td>
            <td style="padding:5px;"><label class="checkbox-label"><input type="checkbox" wire:model.live="data.is_indigenous" class="checkbox-input" {{ $disCb }} /><span>Yes</span></label></td>
        </tr>
        @if($this->data['is_indigenous'] ?? false)
        <tr><td colspan="2" style="padding:5px;"><input type="text" wire:model="data.indigenous_details" class="pds-input" placeholder="Please specify indigenous group" {{ $dis }} /></td></tr>
        @endif

        <tr>
            <td style="font-size:7pt; padding:8px; padding-left:30px;">b. Are you a person with disability?</td>
            <td style="padding:5px;"><label class="checkbox-label"><input type="checkbox" wire:model.live="data.has_disability" class="checkbox-input" {{ $disCb }} /><span>Yes</span></label></td>
        </tr>
        @if($this->data['has_disability'] ?? false)
        <tr><td colspan="2" style="padding:5px;"><input type="text" wire:model="data.disability_details" class="pds-input" placeholder="Please specify disability" {{ $dis }} /></td></tr>
        @endif

        <tr>
            <td style="font-size:7pt; padding:8px; padding-left:30px;">c. Are you a solo parent?</td>
            <td style="padding:5px;"><label class="checkbox-label"><input type="checkbox" wire:model.live="data.is_solo_parent" class="checkbox-input" {{ $disCb }} /><span>Yes</span></label></td>
        </tr>
        @if($this->data['is_solo_parent'] ?? false)
        <tr><td colspan="2" style="padding:5px;"><input type="text" wire:model="data.solo_parent_details" class="pds-input" placeholder="Solo Parent ID No." {{ $dis }} /></td></tr>
        @endif
    </table>

    <div class="section-title" style="margin-top:20px;">41. REFERENCES</div>
    <div style="font-size:7pt; font-style:italic; margin:5px 0;">(Person not related by consanguinity or affinity to applicant/appointee)</div>

    <div style="margin: 15px 0;">
        @foreach($this->data['references'] ?? [] as $index => $ref)
        <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: 2fr 2fr 1fr; gap: 10px;">
                <div><label style="font-size: 7pt; display: block;">Name</label><input type="text" wire:model="data.references.{{ $index }}.name" class="pds-input" {{ $dis }} /></div>
                <div><label style="font-size: 7pt; display: block;">Address</label><input type="text" wire:model="data.references.{{ $index }}.address" class="pds-input" {{ $dis }} /></div>
                <div><label style="font-size: 7pt; display: block;">Tel. No.</label><input type="text" wire:model="data.references.{{ $index }}.tel" class="pds-input" {{ $dis }} /></div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="section-title" style="margin-top:20px;">42. GOVERNMENT ISSUED ID AND DECLARATION</div>

    <div style="font-size:7pt; margin:10px 0; line-height:1.4;">
        I declare under oath that I have personally accomplished this Personal Data Sheet which is a true, correct and complete statement pursuant to the provisions of pertinent laws, rules and regulations of the Republic of the Philippines. I authorize the agency head/authorized representative to verify/validate the contents stated herein. I agree that any misrepresentation made in this document and its attachments shall cause the filing of administrative/criminal case/s against me.
    </div>

    <table class="pds-table" style="margin-top:10px;">
        <tr><td class="label-cell" style="width:33.33%;">Government Issued ID</td><td class="input-cell"><input type="text" wire:model="data.gov_id_type" class="pds-input" placeholder="e.g., Driver's License, Passport" {{ $dis }} /></td></tr>
        <tr><td class="label-cell">ID/License/Passport No.</td><td class="input-cell"><input type="text" wire:model="data.gov_id_no" class="pds-input" {{ $dis }} /></td></tr>
        <tr><td class="label-cell">Date/Place of Issuance</td><td class="input-cell"><input type="text" wire:model="data.gov_id_issued" class="pds-input" {{ $dis }} /></td></tr>
        <tr><td class="label-cell">Date Accomplished (dd/mm/yyyy)</td><td class="input-cell"><input type="date" wire:model="data.date_accomplished" class="pds-input" {{ $dis }} /></td></tr>
    </table>

    <div style="text-align:center; font-size:7pt; margin-top:15px; font-style:italic;">CS FORM 212 (Revised 2025), Page 4 of 4</div>
</div>
