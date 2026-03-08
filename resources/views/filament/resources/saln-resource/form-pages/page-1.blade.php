{{-- PAGE 1: DECLARANT INFO, SPOUSE, CHILDREN, ASSETS --}}
@php $ro = $isReadOnly ?? false; $dis = $ro ? 'disabled readonly' : ''; $disCb = $ro ? 'disabled' : ''; @endphp
<div class="saln-form-page">

    <div class="saln-header-date">
        Revised as of January 2015<br>
        Per CSC Resolution No. 1500958<br>
        Promulgated on January 21, 2015
    </div>

    <div class="saln-form-title">SWORN STATEMENT OF ASSETS, LIABILITIES AND NET WORTH</div>

    @php
        $asOfFormatted = '';
        try {
            $raw = $this->data['as_of_date'] ?? null;
            if ($raw) $asOfFormatted = \Carbon\Carbon::parse($raw)->format('Y-m-d');
        } catch (\Exception $e) {}
    @endphp
    <div style="text-align:center; margin:6px 0;">
        <span style="font-weight:bold; font-size:9pt;">As of: </span>
        {{--
            We intentionally do NOT use wire:model here.
            wire:model on a native <input type="date"> causes Livewire to re-set
            the value after each render using the raw stored string
            (e.g. "2026-03-08T00:00:00.000000Z"), overwriting our clean value.
            Instead we use x-model (Alpine) which reads/writes only the
            Y-m-d portion the browser expects, and we push the clean value
            back to Livewire via x-on:change.
        --}}
        <input type="date"
               x-data
               x-model="$el.value"
               :value="'{{ $asOfFormatted }}'"
               @if(!$ro)
               x-on:change="$wire.set('data.as_of_date', $event.target.value)"
               @endif
               class="saln-input"
               style="width:200px; border-bottom:1.5px solid #000; font-size:10pt; font-weight:bold;"
               {{ $dis }} />
    </div>

    <div class="saln-required-by">(Required by R.A. 6713)</div>

    {{-- FILING TYPE --}}
    <div style="margin:8px 0; font-size:8pt;">
        <div style="font-weight:bold; margin-bottom:5px;">
            Note: Husband and wife who are both public officials and employees may file the required statements jointly or separately.
        </div>
        <div style="display:flex; gap:15px; flex-wrap:wrap; align-items:center;">
            <label class="saln-checkbox-label">
                <input type="checkbox" wire:model.live="data.joint_filing" class="saln-checkbox-input" {{ $disCb }}
                    @if(!$ro) x-on:change="if($event.target.checked){ $wire.set('data.separate_filing', false); $wire.set('data.not_applicable', false); }" @endif />
                Joint Filing
            </label>
            <label class="saln-checkbox-label">
                <input type="checkbox" wire:model.live="data.separate_filing" class="saln-checkbox-input" {{ $disCb }}
                    @if(!$ro) x-on:change="if($event.target.checked){ $wire.set('data.joint_filing', false); $wire.set('data.not_applicable', false); }" @endif />
                Separate Filing
            </label>
            <label class="saln-checkbox-label">
                <input type="checkbox" wire:model.live="data.not_applicable" class="saln-checkbox-input" {{ $disCb }}
                    @if(!$ro) x-on:change="if($event.target.checked){ $wire.set('data.joint_filing', false); $wire.set('data.separate_filing', false); }" @endif />
                Not Applicable
            </label>
        </div>
    </div>

    {{-- DECLARANT INFORMATION --}}
    <table style="width:100%; border-collapse:collapse; margin:10px 0;">
        <tr>
            <td style="font-weight:bold; font-size:8pt; white-space:nowrap; padding:3px 5px; width:90px;">DECLARANT:</td>
            <td style="border-bottom:1px solid #000; padding:1px 2px; width:22%;">
                <input type="text" wire:model="data.declarant_family_name" class="saln-input" placeholder="Family Name" {{ $dis }} />
                <div style="text-align:center; font-size:6.5pt; font-style:italic;">(Family Name)</div>
            </td>
            <td style="width:5px;"></td>
            <td style="border-bottom:1px solid #000; padding:1px 2px; width:22%;">
                <input type="text" wire:model="data.declarant_first_name" class="saln-input" placeholder="First Name" {{ $dis }} />
                <div style="text-align:center; font-size:6.5pt; font-style:italic;">(First Name)</div>
            </td>
            <td style="width:5px;"></td>
            <td style="border-bottom:1px solid #000; padding:1px 2px; width:8%;">
                <input type="text" wire:model="data.declarant_middle_initial" class="saln-input" placeholder="M.I." maxlength="5" {{ $dis }} />
                <div style="text-align:center; font-size:6.5pt; font-style:italic;">(M.I.)</div>
            </td>
            <td style="font-weight:bold; font-size:8pt; white-space:nowrap; padding:3px 8px;">POSITION:</td>
            <td style="border-bottom:1px solid #000; padding:1px 2px;">
                <input type="text" wire:model="data.declarant_position" class="saln-input" {{ $dis }} />
            </td>
        </tr>
        <tr style="height:8px;"></tr>
        <tr>
            <td style="font-weight:bold; font-size:8pt; padding:3px 5px;">ADDRESS:</td>
            <td colspan="5" style="border-bottom:1px solid #000; padding:1px 2px;">
                <textarea wire:model="data.declarant_office_address" class="saln-input" rows="2" {{ $dis }}></textarea>
            </td>
            <td style="font-weight:bold; font-size:8pt; padding:3px 8px;">AGENCY/OFFICE:</td>
            <td style="border-bottom:1px solid #000; padding:1px 2px;">
                <input type="text" wire:model="data.declarant_agency_office" class="saln-input" {{ $dis }} />
            </td>
        </tr>
    </table>

    {{-- SPOUSE INFORMATION --}}
    <table style="width:100%; border-collapse:collapse; margin:8px 0;">
        <tr>
            <td style="font-weight:bold; font-size:8pt; white-space:nowrap; padding:3px 5px; width:90px;">SPOUSE:</td>
            <td style="border-bottom:1px solid #000; padding:1px 2px; width:22%;">
                <input type="text" wire:model="data.spouse_family_name" class="saln-input" placeholder="Family Name" {{ $dis }} />
                <div style="text-align:center; font-size:6.5pt; font-style:italic;">(Family Name)</div>
            </td>
            <td style="width:5px;"></td>
            <td style="border-bottom:1px solid #000; padding:1px 2px; width:22%;">
                <input type="text" wire:model="data.spouse_first_name" class="saln-input" placeholder="First Name" {{ $dis }} />
                <div style="text-align:center; font-size:6.5pt; font-style:italic;">(First Name)</div>
            </td>
            <td style="width:5px;"></td>
            <td style="border-bottom:1px solid #000; padding:1px 2px; width:8%;">
                <input type="text" wire:model="data.spouse_middle_initial" class="saln-input" placeholder="M.I." maxlength="5" {{ $dis }} />
                <div style="text-align:center; font-size:6.5pt; font-style:italic;">(M.I.)</div>
            </td>
            <td style="font-weight:bold; font-size:8pt; white-space:nowrap; padding:3px 8px;">POSITION:</td>
            <td style="border-bottom:1px solid #000; padding:1px 2px;">
                <input type="text" wire:model="data.spouse_position" class="saln-input" {{ $dis }} />
            </td>
        </tr>
        <tr style="height:8px;"></tr>
        <tr>
            <td></td><td colspan="5"></td>
            <td style="font-weight:bold; font-size:8pt; padding:3px 8px;">AGENCY/OFFICE:</td>
            <td style="border-bottom:1px solid #000; padding:1px 2px;">
                <input type="text" wire:model="data.spouse_agency_office" class="saln-input" {{ $dis }} />
            </td>
        </tr>
        <tr>
            <td></td><td colspan="5"></td>
            <td style="font-weight:bold; font-size:8pt; padding:3px 8px;">OFFICE ADDRESS:</td>
            <td style="border-bottom:1px solid #000; padding:1px 2px;">
                <textarea wire:model="data.spouse_office_address" class="saln-input" rows="2" {{ $dis }}></textarea>
            </td>
        </tr>
    </table>

    {{-- CHILDREN --}}
    <div class="saln-section-header">UNMARRIED CHILDREN BELOW EIGHTEEN (18) YEARS OF AGE LIVING IN DECLARANT'S HOUSEHOLD</div>

    <table class="saln-table">
        <thead>
            <tr>
                <th style="width:55%;">NAME</th>
                <th style="width:30%;">DATE OF BIRTH</th>
                <th style="width:15%;">AGE</th>
            </tr>
        </thead>
    </table>

    <div style="margin:5px 0;">
        @foreach($this->data['children'] ?? [] as $index => $child)
        <div style="display:flex; gap:8px; margin-bottom:5px; align-items:center;">
            <input type="text" wire:model="data.children.{{ $index }}.name" placeholder="Full Name" class="saln-input" style="flex:2; border-bottom:1px solid #ccc;" {{ $dis }} />
            @php
                $dobRaw = $child['date_of_birth'] ?? null;
                $dobFormatted = '';
                $dobDisplay   = '';
                try {
                    if ($dobRaw) {
                        $dobCarbon    = \Carbon\Carbon::parse($dobRaw);
                        $dobFormatted = $dobCarbon->format('Y-m-d');   // for the input value
                        $dobDisplay   = $dobCarbon->format('M d, Y'); // human-readable label
                    }
                } catch (\Exception $e) {}
            @endphp
            {{--
                date_of_birth: parse the raw ISO string (may be "2015-06-16T00:00:00.000000Z")
                into a clean Y-m-d for the browser.  Use Alpine x-on:change (not wire:model)
                so Livewire never overwrites the value with the raw ISO string mid-render.
                Also shows a human-readable date label next to the input.
            --}}
            <div style="flex:1.5; display:flex; flex-direction:column; gap:2px;">
                <input type="date"
                       x-data
                       :value="'{{ $dobFormatted }}'"
                       class="saln-input"
                       style="border-bottom:1px solid #ccc; width:100%;"
                       {{ $dis }}
                       @if(!$ro)
                       x-on:change="
                           const v = $event.target.value;
                           $wire.set('data.children.{{ $index }}.date_of_birth', v);
                           const dob = new Date(v);
                           if (!isNaN(dob)) {
                               const today = new Date();
                               let age = today.getFullYear() - dob.getFullYear();
                               const m = today.getMonth() - dob.getMonth();
                               if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
                               $wire.set('data.children.{{ $index }}.age', age);
                           }
                       "
                       @endif
                />
                @if($dobDisplay)
                <span style="font-size:6.5pt; color:#555; padding-left:2px;">{{ $dobDisplay }}</span>
                @endif
            </div>
            <input type="number"
                   x-data
                   :value="'{{ $child['age'] ?? '' }}'"
                   @if(!$ro)
                   x-on:change="$wire.set('data.children.{{ $index }}.age', $event.target.value)"
                   @endif
                   placeholder="Age"
                   class="saln-input"
                   style="flex:0.5; border-bottom:1px solid #ccc;"
                   {{ $dis }} />
            @if(!$ro)
            <button type="button" wire:click="removeChild({{ $index }})" class="saln-btn-remove">×</button>
            @endif
        </div>
        @endforeach
        @if(!$ro)
        <button type="button" wire:click="addChild" class="saln-btn-add">+ Add Child</button>
        @endif
    </div>

    <div style="font-size:7pt; font-style:italic; text-align:center; margin:4px 0;">(Continue on separate sheet if necessary)</div>

    {{-- ASSETS --}}
    <div class="saln-section-header">ASSETS, LIABILITIES AND NETWORTH</div>
    <div class="saln-section-subtitle">(Including those of the spouse and unmarried children below eighteen (18) years of age living in declarant's household)</div>

    <div style="font-weight:bold; font-size:9pt; margin:8px 0 4px 0;">1. ASSETS</div>

    {{-- REAL PROPERTIES --}}
    <div class="saln-subsection-title">a. Real Properties <span class="saln-subsection-desc">(Land, Buildings, and other Real Estate)</span></div>

    <table class="saln-table">
        <thead>
            <tr>
                <th rowspan="2" style="width:14%;">DESCRIPTION</th>
                <th rowspan="2" style="width:9%;">KIND</th>
                <th rowspan="2" style="width:16%;">EXACT LOCATION</th>
                <th rowspan="2" style="width:11%;">ASSESSED VALUE</th>
                <th rowspan="2" style="width:13%;">CURRENT FAIR MARKET VALUE</th>
                <th colspan="3">ACQUISITION</th>
            </tr>
            <tr>
                <th style="width:9%;">YEAR</th>
                <th style="width:14%;">MODE</th>
                <th style="width:14%;">COST</th>
            </tr>
        </thead>
    </table>

    <div style="margin:5px 0;">
        @foreach($this->data['realProperties'] ?? [] as $index => $prop)
        <div class="saln-repeater-item">
            <div class="saln-grid-4-real">
                <div><label class="saln-field-label">Description</label><textarea wire:model="data.realProperties.{{ $index }}.description" class="saln-input" rows="2" {{ $dis }}></textarea></div>
                <div><label class="saln-field-label">Kind</label><input type="text" wire:model="data.realProperties.{{ $index }}.kind" class="saln-input" {{ $dis }} /></div>
                <div><label class="saln-field-label">Exact Location</label><input type="text" wire:model="data.realProperties.{{ $index }}.exact_location" class="saln-input" {{ $dis }} /></div>
                <div><label class="saln-field-label">Assessed Value (₱)</label><input type="number" wire:model="data.realProperties.{{ $index }}.assessed_value" class="saln-input" step="0.01" {{ $dis }} /></div>
            </div>
            <div class="saln-grid-4">
                <div><label class="saln-field-label">Fair Market Value (₱)</label><input type="number" wire:model="data.realProperties.{{ $index }}.current_fair_market_value" class="saln-input" step="0.01" {{ $dis }} /></div>
                <div><label class="saln-field-label">Year Acquired</label><input type="number" wire:model="data.realProperties.{{ $index }}.acquisition_year" class="saln-input" placeholder="YYYY" min="1900" max="{{ date('Y') }}" {{ $dis }} /></div>
                <div><label class="saln-field-label">Mode of Acquisition</label><input type="text" wire:model="data.realProperties.{{ $index }}.mode_of_acquisition" class="saln-input" {{ $dis }} /></div>
                <div><label class="saln-field-label">Acquisition Cost (₱)</label><input type="number" wire:model="data.realProperties.{{ $index }}.acquisition_cost" class="saln-input" step="0.01" {{ $dis }} /></div>
            </div>
            @if(!$ro)
            <button type="button" wire:click="removeRealProperty({{ $index }})" class="saln-btn-remove">Remove</button>
            @endif
        </div>
        @endforeach
        @if(!$ro)
        <button type="button" wire:click="addRealProperty" class="saln-btn-add">+ Add Real Property</button>
        @endif
    </div>

    {{-- PERSONAL PROPERTIES --}}
    <div class="saln-subsection-title" style="margin-top:10px;">b. Personal Properties <span class="saln-subsection-desc">(Vehicles, Jewelry, Cash, Bank Deposits, etc.)</span></div>

    <table class="saln-table">
        <thead>
            <tr>
                <th style="width:60%;">DESCRIPTION</th>
                <th style="width:20%;">YEAR ACQUIRED</th>
                <th style="width:20%;">ACQUISITION COST/AMOUNT</th>
            </tr>
        </thead>
    </table>

    <div style="margin:5px 0;">
        @foreach($this->data['personalProperties'] ?? [] as $index => $prop)
        <div class="saln-repeater-item">
            <div class="saln-grid-2-1">
                <div><label class="saln-field-label">Description</label><textarea wire:model="data.personalProperties.{{ $index }}.description" class="saln-input" rows="2" {{ $dis }}></textarea></div>
                <div><label class="saln-field-label">Year Acquired</label><input type="number" wire:model="data.personalProperties.{{ $index }}.year_acquired" class="saln-input" placeholder="YYYY" min="1900" max="{{ date('Y') }}" {{ $dis }} /></div>
            </div>
            <div>
                <label class="saln-field-label">Acquisition Cost/Amount (₱)</label>
                <input type="number" wire:model="data.personalProperties.{{ $index }}.acquisition_cost" class="saln-input" step="0.01" style="max-width:200px;" {{ $dis }} />
            </div>
            @if(!$ro)
            <button type="button" wire:click="removePersonalProperty({{ $index }})" class="saln-btn-remove" style="margin-top:6px;">Remove</button>
            @endif
        </div>
        @endforeach
        @if(!$ro)
        <button type="button" wire:click="addPersonalProperty" class="saln-btn-add">+ Add Personal Property</button>
        @endif
    </div>

    <div style="text-align:right; font-weight:bold; font-size:9pt; border-top:2px solid #000; border-bottom:1px solid #000; padding:4px 5px; margin-top:8px;">
        TOTAL ASSETS (a+b): &nbsp;
        <span style="min-width:150px; display:inline-block; border-bottom:1px solid #000; text-align:right; font-size:8pt; color:#555;">(Calculated on save)</span>
    </div>

    <div style="text-align:center; font-size:7pt; margin-top:15px; font-style:italic;">SALN Form (Revised 2015), Page 1 of 2</div>
</div>
