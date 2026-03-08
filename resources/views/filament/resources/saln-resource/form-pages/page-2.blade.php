{{-- PAGE 2: LIABILITIES, BUSINESS INTERESTS, RELATIVES, DECLARATION --}}
@php $ro = $isReadOnly ?? false; $dis = $ro ? 'disabled readonly' : ''; $disCb = $ro ? 'disabled' : ''; @endphp
<div class="saln-form-page">

    <div class="saln-form-title" style="font-size:11pt;">SWORN STATEMENT OF ASSETS, LIABILITIES AND NET WORTH</div>
    <div style="text-align:center; font-size:8pt; font-style:italic; margin-bottom:10px;">(Continuation - Page 2)</div>

    {{-- LIABILITIES --}}
    <div style="font-weight:bold; font-size:9pt; margin:8px 0 4px 0;">
        2. LIABILITIES
        <span style="font-size:7.5pt; font-weight:normal; font-style:italic;">(Loans, Mortgages, and other Obligations)</span>
    </div>

    <table class="saln-table">
        <thead>
            <tr>
                <th style="width:35%;">NATURE</th>
                <th style="width:40%;">NAME OF CREDITORS</th>
                <th style="width:25%;">OUTSTANDING BALANCE</th>
            </tr>
        </thead>
    </table>

    <div style="margin:5px 0;">
        @foreach($this->data['liabilities'] ?? [] as $index => $liability)
        <div class="saln-repeater-item">
            <div class="saln-grid-3">
                <div><label class="saln-field-label">Nature of Liability</label><input type="text" wire:model="data.liabilities.{{ $index }}.nature" class="saln-input" placeholder="e.g., Housing Loan" {{ $dis }} /></div>
                <div><label class="saln-field-label">Name of Creditor</label><input type="text" wire:model="data.liabilities.{{ $index }}.name_of_creditors" class="saln-input" {{ $dis }} /></div>
                <div><label class="saln-field-label">Outstanding Balance (₱)</label><input type="number" wire:model="data.liabilities.{{ $index }}.outstanding_balance" class="saln-input" step="0.01" {{ $dis }} /></div>
            </div>
            @if(!$ro)
            <button type="button" wire:click="removeLiability({{ $index }})" class="saln-btn-remove">Remove</button>
            @endif
        </div>
        @endforeach
        @if(!$ro)
        <button type="button" wire:click="addLiability" class="saln-btn-add">+ Add Liability</button>
        @endif
    </div>

    <div class="saln-net-worth-box">
        NET WORTH = Total Assets less Total Liabilities =
        <span style="font-size:9pt; font-weight:normal;">(Calculated automatically on save)</span>
    </div>

    {{-- BUSINESS INTERESTS --}}
    <div class="saln-section-header">BUSINESS INTERESTS AND FINANCIAL CONNECTIONS</div>
    <div class="saln-section-subtitle">(of Declarant/Declarant's spouse; Ownership/Owning Shareholder (10 percent of total))</div>

    <div style="margin:6px 0;">
        <label class="saln-checkbox-label" style="display:inline-flex; margin-bottom:6px;">
            <input type="checkbox" wire:model.live="data.has_business_interests" class="saln-checkbox-input" {{ $disCb }}
                @if(!$ro) x-on:change="if($event.target.checked) $wire.set('data.no_business_interests', false)" @endif />
            I/We have business interest or financial connection.
        </label>
        <br>
        <label class="saln-checkbox-label" style="display:inline-flex;">
            <input type="checkbox" wire:model.live="data.no_business_interests" class="saln-checkbox-input" {{ $disCb }}
                @if(!$ro) x-on:change="if($event.target.checked) $wire.set('data.has_business_interests', false)" @endif />
            I/We do not have any business interest or financial connection.
        </label>
    </div>

    @if($this->data['has_business_interests'] ?? false)
    <table class="saln-table">
        <thead>
            <tr>
                <th style="width:25%;">NAME OF ENTITY/BUSINESS ENTERPRISE</th>
                <th style="width:25%;">BUSINESS ADDRESS</th>
                <th style="width:25%;">NATURE OF BUSINESS INTEREST</th>
                <th style="width:25%;">DATE OF ACQUISITION</th>
            </tr>
        </thead>
    </table>
    <div style="margin:5px 0;">
        @foreach($this->data['businessInterests'] ?? [] as $index => $business)
        <div class="saln-repeater-item">
            <div class="saln-grid-2">
                <div><label class="saln-field-label">Business Name</label><input type="text" wire:model="data.businessInterests.{{ $index }}.name_of_entity" class="saln-input" {{ $dis }} /></div>
                <div><label class="saln-field-label">Business Address</label><textarea wire:model="data.businessInterests.{{ $index }}.business_address" class="saln-input" rows="2" {{ $dis }}></textarea></div>
                <div><label class="saln-field-label">Nature of Interest</label><input type="text" wire:model="data.businessInterests.{{ $index }}.nature_of_business_interest" class="saln-input" {{ $dis }} /></div>
                <div><label class="saln-field-label">Date of Acquisition</label><input type="date" wire:model="data.businessInterests.{{ $index }}.date_of_acquisition" class="saln-input" {{ $dis }} /></div>
            </div>
            @if(!$ro)
            <button type="button" wire:click="removeBusinessInterest({{ $index }})" class="saln-btn-remove">Remove</button>
            @endif
        </div>
        @endforeach
        @if(!$ro)
        <button type="button" wire:click="addBusinessInterest" class="saln-btn-add">+ Add Business Interest</button>
        @endif
    </div>
    @endif

    {{-- RELATIVES IN GOVERNMENT --}}
    <div class="saln-section-header">RELATIVES IN THE GOVERNMENT SERVICE</div>
    <div class="saln-section-subtitle">(Within the Fourth Degree of Consanguinity or Affinity)</div>

    <div style="margin:6px 0;">
        <label class="saln-checkbox-label" style="display:inline-flex; margin-bottom:6px;">
            <input type="checkbox" wire:model.live="data.has_relatives_in_government" class="saln-checkbox-input" {{ $disCb }}
                @if(!$ro) x-on:change="if($event.target.checked) $wire.set('data.no_relatives_in_government', false)" @endif />
            I have relatives in the government service within the fourth degree.
        </label>
        <br>
        <label class="saln-checkbox-label" style="display:inline-flex;">
            <input type="checkbox" wire:model.live="data.no_relatives_in_government" class="saln-checkbox-input" {{ $disCb }}
                @if(!$ro) x-on:change="if($event.target.checked) $wire.set('data.has_relatives_in_government', false)" @endif />
            I/We do not know of any relative in the government service.
        </label>
    </div>

    @if($this->data['has_relatives_in_government'] ?? false)
    <table class="saln-table">
        <thead>
            <tr>
                <th style="width:25%;">NAME OF RELATIVE</th>
                <th style="width:20%;">RELATIONSHIP</th>
                <th style="width:25%;">POSITION</th>
                <th style="width:30%;">AGENCY/OFFICE AND ADDRESS</th>
            </tr>
        </thead>
    </table>
    <div style="margin:5px 0;">
        @foreach($this->data['relativesInGovernment'] ?? [] as $index => $relative)
        <div class="saln-repeater-item">
            <div class="saln-grid-2">
                <div><label class="saln-field-label">Name of Relative</label><input type="text" wire:model="data.relativesInGovernment.{{ $index }}.name_of_relative" class="saln-input" {{ $dis }} /></div>
                <div><label class="saln-field-label">Relationship</label><input type="text" wire:model="data.relativesInGovernment.{{ $index }}.relationship" class="saln-input" placeholder="e.g., Father, Sibling" {{ $dis }} /></div>
                <div><label class="saln-field-label">Position</label><input type="text" wire:model="data.relativesInGovernment.{{ $index }}.position" class="saln-input" {{ $dis }} /></div>
                <div><label class="saln-field-label">Agency/Office and Address</label><textarea wire:model="data.relativesInGovernment.{{ $index }}.name_of_agency_office_address" class="saln-input" rows="2" {{ $dis }}></textarea></div>
            </div>
            @if(!$ro)
            <button type="button" wire:click="removeRelativeInGovernment({{ $index }})" class="saln-btn-remove">Remove</button>
            @endif
        </div>
        @endforeach
        @if(!$ro)
        <button type="button" wire:click="addRelativeInGovernment" class="saln-btn-add">+ Add Relative</button>
        @endif
    </div>
    @endif

    {{-- CERTIFICATION --}}
    <div class="saln-certification" style="margin-top:14px;">
        I hereby certify that these are true and correct statements of my assets, liabilities, net worth, business interests and financial connections, including those of my spouse and unmarried children below eighteen (18) years of age living in my household, and that to the best of my knowledge, the above-enumerated are names of my relatives in the government within the fourth civil degree of consanguinity or affinity.
    </div>
    <div class="saln-certification">
        I hereby authorize the Ombudsman or his/her duly authorized representative to obtain and secure from all appropriate government agencies, including the Bureau of Internal Revenue such documents that may show my assets, liabilities, net worth, business interests and financial connections, to include those of my spouse and unmarried children below 18 years of age living with me in my household covering previous years to include the year I first assumed office in government.
    </div>

    {{-- DATE SIGNED --}}
    <div style="margin:10px 0; font-size:8.5pt; display:flex; align-items:center; gap:8px;">
        <span style="font-weight:bold;">Date Signed:</span>
        <input type="date" wire:model="data.date_signed" class="saln-input" style="width:200px; border-bottom:1px solid #000;" {{ $dis }} />
    </div>

    {{-- SIGNATURES --}}
    <div class="saln-signature-row">
        <div class="saln-signature-box">
            <div class="saln-signature-line"></div>
            <div class="saln-signature-label">(Signature of Declarant)</div>
        </div>
        <div class="saln-signature-box">
            <div class="saln-signature-line"></div>
            <div class="saln-signature-label">(Signature of Co-Declarant/Spouse)</div>
        </div>
    </div>

    {{-- OATH SECTION --}}
    <div style="margin-top:14px; border:1px dashed #aaa; padding:10px; background:#fafafa; border-radius:4px;">
        <div style="font-size:7.5pt; font-weight:bold; margin-bottom:8px; color:#555;">SUBSCRIBED AND SWORN (Admin Section)</div>
        <div style="display:flex; gap:15px; flex-wrap:wrap; align-items:flex-end;">
            <div>
                <label class="saln-field-label">Date Subscribed and Sworn</label>
                <input type="date" wire:model="data.subscribed_sworn_date" class="saln-input" style="width:200px; border-bottom:1px solid #000;" {{ $dis }} />
            </div>
            <div style="flex:1;">
                <label class="saln-field-label">Person Administering Oath</label>
                <input type="text" wire:model="data.person_administering_oath" class="saln-input" style="border-bottom:1px solid #000;" {{ $dis }} />
            </div>
        </div>
    </div>

    {{-- ID UPLOADS NOTE --}}
    <div style="margin-top:10px; border:1px dashed #999; padding:8px; background:#f9f9f9; border-radius:4px; font-size:7.5pt; color:#666;">
        📎 <strong>Government ID uploads</strong> — use the collapsed "Form Fields" section below to upload government IDs if needed.
    </div>

    {{-- NOTES --}}
    <div class="saln-note-section">
        <p style="margin:3px 0;"><strong>NOTE:</strong> Violation of this law is punishable by a fine not exceeding five thousand pesos (₱5,000) or imprisonment not exceeding one (1) year, or both, at the discretion of the court (Section 11, R.A. 6713).</p>
        <p style="margin:3px 0;"><strong>REMINDER:</strong> Any misrepresentation or non-disclosure of any material fact required to be stated herein shall constitute perjury under Article 183 of the Revised Penal Code and shall be punished accordingly.</p>
    </div>

    <div style="text-align:right; font-size:8pt; font-weight:bold; margin-top:8px;">Page 2 of 2</div>
</div>
