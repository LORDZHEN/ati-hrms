{{-- PAGE 1: ANNEX A — Compliance, Declarant/Spouse, Multiple Marriages, Children, Assets --}}
{{-- 2025 CSC SALN Format — CSC Resolution No. 2500632, Promulgated June 25, 2025 --}}
@php $ro = $isReadOnly ?? false; $dis = $ro ? 'disabled readonly' : ''; $disCb = $ro ? 'disabled' : ''; @endphp

<div class="saln-form-page">

    {{-- ── HEADER ── --}}
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:4px;">
        <div class="saln-annex-label">ANNEX A</div>
        <div class="saln-header-ref" style="text-align:right;">
            2025 SALN Form<br>
            Per CSC Resolution No. ____________<br>
            Promulgated on ____________
        </div>
    </div>

    <div class="saln-form-title">SWORN STATEMENT OF ASSETS, LIABILITIES, AND NET WORTH</div>
    <div class="saln-form-subtitle">(As required by R.A. No. 6713)</div>

    {{-- ── COMPLIANCE TYPE ── --}}
    <div class="saln-compliance-block">
        <div class="comp-title">COMPLIANCE FOR:</div>
        <div class="saln-compliance-options">
            <label>
                <input type="checkbox" wire:model.live="data.compliance_assumption"
                    class="saln-checkbox-input" {{ $disCb }}
                    @if(!$ro) x-on:change="if($event.target.checked){ $wire.set('data.compliance_annual',false); $wire.set('data.compliance_exit',false); }" @endif />
                Assumption of office as of
                <input type="date" wire:model="data.as_of_date" class="saln-input"
                    style="width:130px; border-bottom:1px solid #000; display:inline-block;"
                    @if($ro || !($this->data['compliance_assumption'] ?? false)) disabled @endif />
            </label>
            <label>
                <input type="checkbox" wire:model.live="data.compliance_annual"
                    class="saln-checkbox-input" {{ $disCb }}
                    @if(!$ro) x-on:change="if($event.target.checked){ $wire.set('data.compliance_assumption',false); $wire.set('data.compliance_exit',false); }" @endif />
                Annual filing as of December 31,
                <input type="text" wire:model="data.as_of_date" class="saln-input"
                    style="width:55px; border-bottom:1px solid #000; display:inline-block;"
                    placeholder="YYYY" {{ $ro ? 'disabled' : '' }} />
            </label>
            <label>
                <input type="checkbox" wire:model.live="data.compliance_exit"
                    class="saln-checkbox-input" {{ $disCb }}
                    @if(!$ro) x-on:change="if($event.target.checked){ $wire.set('data.compliance_assumption',false); $wire.set('data.compliance_annual',false); }" @endif />
                Exit as of
                <input type="date" wire:model="data.as_of_date" class="saln-input"
                    style="width:130px; border-bottom:1px solid #000; display:inline-block;"
                    @if($ro || !($this->data['compliance_exit'] ?? false)) disabled @endif />
            </label>
        </div>
    </div>

    {{-- ── DECLARANT ── --}}
    <table style="width:100%; border-collapse:collapse; margin:5px 0;">
        <tr>
            <td class="saln-pinfo-label" style="width:80px;">DECLARANT:</td>
            <td class="saln-pinfo-val" style="width:20%;">
                <input type="text" wire:model="data.declarant_family_name" class="saln-input" placeholder="Family Name" {{ $dis }} />
                <div class="saln-name-hint">(Family Name)</div>
            </td>
            <td style="width:4px;"></td>
            <td class="saln-pinfo-val" style="width:20%;">
                <input type="text" wire:model="data.declarant_first_name" class="saln-input" placeholder="First Name" {{ $dis }} />
                <div class="saln-name-hint">(First Name)</div>
            </td>
            <td style="width:4px;"></td>
            <td class="saln-pinfo-val" style="width:6%;">
                <input type="text" wire:model="data.declarant_middle_initial" class="saln-input" placeholder="M.I." maxlength="5" {{ $dis }} />
                <div class="saln-name-hint">(M.I.)</div>
            </td>
            <td style="width:8px;"></td>
            <td class="saln-pinfo-label" style="width:80px;">POSITION:</td>
            <td class="saln-pinfo-val">
                <input type="text" wire:model="data.declarant_position" class="saln-input" {{ $dis }} />
            </td>
        </tr>
        <tr style="height:6px;"></tr>
        <tr>
            <td class="saln-pinfo-label">AGENCY/ OFFICE:</td>
            <td class="saln-pinfo-val" colspan="5">
                <input type="text" wire:model="data.declarant_agency_office" class="saln-input" {{ $dis }} />
            </td>
            <td></td>
            <td class="saln-pinfo-label">OFFICE ADDRESS:</td>
            <td class="saln-pinfo-val">
                <textarea wire:model="data.declarant_office_address" class="saln-input" rows="2" {{ $dis }}></textarea>
            </td>
        </tr>
    </table>

    <div style="border-top:1px solid #000; margin:4px 0;"></div>

    {{-- ── SPOUSE ── --}}
    <table style="width:100%; border-collapse:collapse; margin:5px 0;">
        <tr>
            <td class="saln-pinfo-label" style="width:80px;">SPOUSE:</td>
            <td class="saln-pinfo-val" style="width:20%;">
                <input type="text" wire:model="data.spouse_family_name" class="saln-input" placeholder="Family Name" {{ $dis }} />
                <div class="saln-name-hint">(Family Name)</div>
            </td>
            <td style="width:4px;"></td>
            <td class="saln-pinfo-val" style="width:20%;">
                <input type="text" wire:model="data.spouse_first_name" class="saln-input" placeholder="First Name" {{ $dis }} />
                <div class="saln-name-hint">(First Name)</div>
            </td>
            <td style="width:4px;"></td>
            <td class="saln-pinfo-val" style="width:6%;">
                <input type="text" wire:model="data.spouse_middle_initial" class="saln-input" placeholder="M.I." maxlength="5" {{ $dis }} />
                <div class="saln-name-hint">(M.I.)</div>
            </td>
            <td style="width:8px;"></td>
            <td class="saln-pinfo-label" style="width:80px;">POSITION:</td>
            <td class="saln-pinfo-val">
                <input type="text" wire:model="data.spouse_position" class="saln-input" placeholder="(if public official/employee)" {{ $dis }} />
            </td>
        </tr>
        <tr style="height:6px;"></tr>
        <tr>
            <td></td><td colspan="5"></td><td></td>
            <td class="saln-pinfo-label">AGENCY/ OFFICE:</td>
            <td class="saln-pinfo-val">
                <input type="text" wire:model="data.spouse_agency_office" class="saln-input" {{ $dis }} />
            </td>
        </tr>
        <tr>
            <td></td><td colspan="5"></td><td></td>
            <td class="saln-pinfo-label">OFFICE ADDRESS:</td>
            <td class="saln-pinfo-val">
                <textarea wire:model="data.spouse_office_address" class="saln-input" rows="2" {{ $dis }}></textarea>
            </td>
        </tr>
    </table>

    <div style="border-top:2px solid #000; margin:5px 0;"></div>

    {{-- ── FILING TYPE ── --}}
    <div style="margin:4px 0; font-size:8pt;">
        <div style="font-weight:bold; font-size:7.5pt; margin-bottom:4px; text-transform:uppercase;">
            Spouses, who are both public officials or employees, may file the SALN jointly or separately.<br>
            The declarant shall check the appropriate box
        </div>
        <div style="display:flex; gap:20px; flex-wrap:wrap; align-items:center;">
            <label class="saln-checkbox-label">
                <input type="checkbox" wire:model.live="data.joint_filing" class="saln-checkbox-input" {{ $disCb }}
                    @if(!$ro) x-on:change="if($event.target.checked){ $wire.set('data.separate_filing',false); $wire.set('data.not_applicable',false); }" @endif />
                Joint Filing
            </label>
            <label class="saln-checkbox-label">
                <input type="checkbox" wire:model.live="data.separate_filing" class="saln-checkbox-input" {{ $disCb }}
                    @if(!$ro) x-on:change="if($event.target.checked){ $wire.set('data.joint_filing',false); $wire.set('data.not_applicable',false); }" @endif />
                Separate Filing
            </label>
            <label class="saln-checkbox-label">
                <input type="checkbox" wire:model.live="data.not_applicable" class="saln-checkbox-input" {{ $disCb }}
                    @if(!$ro) x-on:change="if($event.target.checked){ $wire.set('data.joint_filing',false); $wire.set('data.separate_filing',false); }" @endif />
                Not Applicable
            </label>
        </div>
    </div>

    {{-- ── MULTIPLE MARRIAGES ── --}}
    <div class="saln-mm-block">
        <div class="mm-title">IF WITH MULTIPLE MARRIAGES, INDICATE NAME(S) OF SPOUSES, OTHERWISE CHECK THE "NOT APPLICABLE" BOX.</div>
        <div style="display:flex; gap:10px; align-items:center; margin:3px 0;">
            <textarea wire:model="data.multiple_marriages_names" class="saln-input"
                style="border:1px solid #ccc; border-radius:2px; padding:3px 5px; flex:1;"
                rows="2" placeholder="Name(s) of spouse(s) from multiple marriages…"
                @if($ro || ($this->data['multiple_marriages_not_applicable'] ?? false)) disabled @endif></textarea>
            <label class="saln-checkbox-label" style="white-space:nowrap; flex-shrink:0;">
                <input type="checkbox" wire:model.live="data.multiple_marriages_not_applicable"
                    class="saln-checkbox-input" {{ $disCb }}
                    @if(!$ro) x-on:change="if($event.target.checked) $wire.set('data.multiple_marriages_names','')" @endif />
                Not Applicable
            </label>
        </div>
    </div>

    <div style="border-top:2px solid #000; margin:5px 0;"></div>

    {{-- ── CHILDREN ── --}}
    <div class="saln-section-header">UNMARRIED CHILDREN BELOW EIGHTEEN (18) YEARS OF AGE LIVING IN DECLARANT'S HOUSEHOLD</div>

    <table class="saln-table">
        <thead>
            <tr>
                <th style="width:75%;">NAME OF CHILD</th>
                <th style="width:25%;">AGE</th>
            </tr>
        </thead>
        <tbody>
            @foreach($this->data['children'] ?? [] as $index => $child)
            <tr class="dr">
                <td class="la" style="padding:2px 4px;">
                    <div style="display:flex; align-items:center; gap:4px;">
                        <input type="text" wire:model="data.children.{{ $index }}.name"
                            placeholder="Full Name of Child" class="saln-input" {{ $dis }} />
                        @if(!$ro)
                        <button type="button" wire:click="removeChild({{ $index }})"
                            class="saln-btn-remove" style="padding:1px 6px; font-size:9pt; flex-shrink:0;">×</button>
                        @endif
                    </div>
                </td>
                <td style="padding:2px 4px;">
                    <input type="number" wire:model="data.children.{{ $index }}.age"
                        placeholder="Age" class="saln-input" min="0" max="17" {{ $dis }} />
                </td>
            </tr>
            @endforeach
            @for($i = count($this->data['children'] ?? []); $i < 3; $i++)
            <tr class="dr"><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>
    @if(!$ro)
    <div style="margin:3px 0 5px;">
        <button type="button" wire:click="addChild" class="saln-btn-add">+ Add Child</button>
    </div>
    @endif
    <div style="font-size:7pt; font-style:italic; text-align:center; margin-bottom:2px;">(Continue on separate sheet if necessary)</div>

    <div style="border-top:2px solid #000; margin:5px 0;"></div>

    {{-- ── ASSETS ── --}}
    <div class="saln-section-header">ASSETS, LIABILITIES AND NETWORTH<sup>ii</sup></div>
    <div class="saln-section-subtitle">(Including those of the spouse and unmarried children below eighteen (18) years of age living in declarant's household)<sup>ii</sup></div>

    <div style="font-weight:bold; font-size:9pt; margin:5px 0 4px;">1. &nbsp;ASSETS</div>

    {{-- REAL PROPERTIES --}}
    <div class="saln-subsection-title">a. &nbsp;Real Properties <span class="saln-subsection-desc">(e.g. lot, house and lot, condominium, and improvements)</span></div>

    <table class="saln-table">
        <thead>
            <tr>
                <th rowspan="2" style="width:14%;">DESCRIPTION<br><span style="font-weight:normal; font-size:6pt; font-style:italic;">(e.g. lot, house and lot, condominium, and improvements)</span></th>
                <th rowspan="2" style="width:10%;">KIND<br><span style="font-weight:normal; font-size:6pt; font-style:italic;">(e.g. residential, commercial, industrial, agricultural and mixed use)</span></th>
                <th rowspan="2" style="width:15%;">EXACT LOCATION</th>
                <th rowspan="2" style="width:12%;">ASSESSED VALUE<br><span style="font-weight:normal; font-size:6pt; font-style:italic;">(As found in the Tax Declaration of Real Property, if available)</span></th>
                <th rowspan="2" style="width:13%;">CURRENT FAIR MARKET VALUE</th>
                <th colspan="3">ACQUISITION</th>
            </tr>
            <tr>
                <th style="width:8%;">YEAR</th>
                <th style="width:14%;">MODE</th>
                <th style="width:14%;">COST</th>
            </tr>
        </thead>
        <tbody>
            @foreach($this->data['realProperties'] ?? [] as $index => $prop)
            <tr>
                <td class="la" style="padding:3px 4px;">
                    <textarea wire:model="data.realProperties.{{ $index }}.description" class="saln-input" rows="2" {{ $dis }}></textarea>
                </td>
                <td style="padding:3px 4px;">
                    <input type="text" wire:model="data.realProperties.{{ $index }}.kind" class="saln-input" {{ $dis }} />
                </td>
                <td class="la" style="padding:3px 4px;">
                    <input type="text" wire:model="data.realProperties.{{ $index }}.exact_location" class="saln-input" {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="number" wire:model="data.realProperties.{{ $index }}.assessed_value" class="saln-input" step="0.01" {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="number"
                        wire:model="data.realProperties.{{ $index }}.current_fair_market_value"
                        data-saln="real-fmv"
                        class="saln-input" step="0.01"
                        x-on:input="$store.saln.recalculate()"
                        x-on:change="$store.saln.recalculate()"
                        {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="number" wire:model="data.realProperties.{{ $index }}.acquisition_year" class="saln-input" placeholder="YYYY" min="1900" max="{{ date('Y') }}" {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="text" wire:model="data.realProperties.{{ $index }}.mode_of_acquisition" class="saln-input" {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="number" wire:model="data.realProperties.{{ $index }}.acquisition_cost" class="saln-input" step="0.01" {{ $dis }} />
                </td>
            </tr>
            @if(!$ro)
            <tr style="background:#fff8f8;">
                <td colspan="8" style="padding:2px 5px; text-align:right; border-top:none;">
                    <button type="button" wire:click="removeRealProperty({{ $index }})" class="saln-btn-remove">Remove</button>
                </td>
            </tr>
            @endif
            @endforeach
            @for($i = count($this->data['realProperties'] ?? []); $i < 2; $i++)
            <tr class="dr"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
            @endfor
            <tr class="sub-r">
                <td colspan="4" style="text-align:right; padding:3px 6px;">Subtotal:</td>
                <td x-data style="padding:3px 5px;"
                    x-init="$store.saln.recalculate()"
                    x-text="(() => { let t=0; document.querySelectorAll('[data-saln=real-fmv]').forEach(e=>t+=parseFloat(e.value)||0); return '₱'+t.toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2}); })()">₱0.00</td>
                <td colspan="3"></td>
            </tr>
        </tbody>
    </table>
    @if(!$ro)
    <div style="margin:3px 0 6px;">
        <button type="button" wire:click="addRealProperty" class="saln-btn-add">+ Add Real Property</button>
    </div>
    @endif

    {{-- PERSONAL PROPERTIES --}}
    <div class="saln-subsection-title" style="margin-top:8px;">b. &nbsp;Personal Properties <span class="saln-subsection-desc">(Vehicles, Jewelry, Cash, Bank Deposits, etc.)</span></div>

    <table class="saln-table">
        <thead>
            <tr>
                <th style="width:60%;">DESCRIPTION</th>
                <th style="width:20%;">ACQUISITION YEAR</th>
                <th style="width:20%;">ACQUISITION COST/ AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($this->data['personalProperties'] ?? [] as $index => $prop)
            <tr>
                <td class="la" style="padding:3px 4px;">
                    <div style="display:flex; align-items:center; gap:4px;">
                        <textarea wire:model="data.personalProperties.{{ $index }}.description" class="saln-input" rows="1" {{ $dis }}></textarea>
                        @if(!$ro)
                        <button type="button" wire:click="removePersonalProperty({{ $index }})" class="saln-btn-remove" style="flex-shrink:0;">×</button>
                        @endif
                    </div>
                </td>
                <td style="padding:3px 4px;">
                    <input type="number" wire:model="data.personalProperties.{{ $index }}.year_acquired" class="saln-input" placeholder="YYYY" min="1900" max="{{ date('Y') }}" {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="number"
                        wire:model="data.personalProperties.{{ $index }}.acquisition_cost"
                        data-saln="personal-cost"
                        class="saln-input" step="0.01"
                        x-on:input="$store.saln.recalculate()"
                        x-on:change="$store.saln.recalculate()"
                        {{ $dis }} />
                </td>
            </tr>
            @endforeach
            @for($i = count($this->data['personalProperties'] ?? []); $i < 3; $i++)
            <tr class="dr"><td></td><td></td><td></td></tr>
            @endfor
            <tr class="sub-r">
                <td colspan="2" style="text-align:right; padding:3px 6px;">Subtotal:</td>
                <td x-data style="padding:3px 5px;"
                    x-init="$store.saln.recalculate()"
                    x-text="(() => { let t=0; document.querySelectorAll('[data-saln=personal-cost]').forEach(e=>t+=parseFloat(e.value)||0); return '₱'+t.toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2}); })()">₱0.00</td>
            </tr>
        </tbody>
    </table>
    @if(!$ro)
    <div style="margin:3px 0 5px;">
        <button type="button" wire:click="addPersonalProperty" class="saln-btn-add">+ Add Personal Property</button>
    </div>
    @endif

    {{-- TOTAL ASSETS --}}
    <div x-data class="saln-live-total-bar">
        <span style="font-size:9pt; font-weight:bold; letter-spacing:0.3px;">TOTAL ASSETS:</span>
        <span class="total-value"
            x-text="$store.saln.fmt($store.saln.totalAssets)"
            x-init="$store.saln.recalculate()">₱0.00</span>
    </div>

    <div style="display:flex; justify-content:flex-end; margin-top:8px; padding-top:4px; border-top:1px solid #000; font-size:7.5pt; font-style:italic;">
        Signature/Initial of Declarant: ___________________________
    </div>

    <div style="text-align:center; font-size:7.5pt; margin-top:10px; padding-top:4px; border-top:1px solid #ccc;">
        Page 1 of ___
    </div>

</div>
