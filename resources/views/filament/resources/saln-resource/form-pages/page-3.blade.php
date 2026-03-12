{{-- PAGE 3: ANNEX B — Additional Sheet for Declarant (SALN Form AS-1) --}}
{{-- 2025 CSC SALN Format — CSC Resolution No. 2500632, Promulgated June 25, 2025 --}}
{{-- Purpose: Additional sheet/s for the declarant's OWN exclusive properties --}}
@php $ro = $isReadOnly ?? false; $dis = $ro ? 'disabled readonly' : ''; $disCb = $ro ? 'disabled' : ''; @endphp

<div class="saln-form-page" style="margin-top: 30px;">

    {{-- ── HEADER ── --}}
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:4px;">
        <div class="saln-annex-label">ANNEX B</div>
        <div class="saln-header-ref" style="text-align:right;">
            SALN Form AS-1 (Declarant)<br>
            Per CSC Resolution No. ____________<br>
            Promulgated on ____________
        </div>
    </div>

    <div class="saln-form-title">SWORN STATEMENT OF ASSETS, LIABILITIES AND NET WORTH</div>
    <div style="text-align:center; font-size:8pt; margin-bottom:2px;">
        As of
        <span style="border-bottom:1px solid #000; display:inline-block; min-width:160px; padding:0 4px; vertical-align:bottom;">
            @if(!empty($this->data['as_of_date']))
                {{ \Carbon\Carbon::parse($this->data['as_of_date'])->format('F d, Y') }}
            @endif
        </span>
    </div>
    <div style="text-align:center; font-size:8pt; font-style:italic; margin-bottom:8px;">(Additional sheet/s for the declarant)</div>

    {{-- ── NAME / POSITION ── --}}
    <table style="width:100%; border-collapse:collapse; margin:5px 0;">
        <tr>
            <td class="saln-pinfo-label" style="width:50px;">NAME:</td>
            <td class="saln-pinfo-val" style="width:22%;">
                <input type="text" wire:model="data.declarant_family_name" class="saln-input" placeholder="Family Name" {{ $dis }} />
                <div class="saln-name-hint">(Family Name)</div>
            </td>
            <td style="width:4px;"></td>
            <td class="saln-pinfo-val" style="width:22%;">
                <input type="text" wire:model="data.declarant_first_name" class="saln-input" placeholder="First Name" {{ $dis }} />
                <div class="saln-name-hint">(First Name)</div>
            </td>
            <td style="width:4px;"></td>
            <td class="saln-pinfo-val" style="width:7%;">
                <input type="text" wire:model="data.declarant_middle_initial" class="saln-input" placeholder="M.I." maxlength="5" {{ $dis }} />
                <div class="saln-name-hint">(M.I.)</div>
            </td>
            <td style="width:8px;"></td>
            <td class="saln-pinfo-label" style="width:70px;">POSITION:</td>
            <td class="saln-pinfo-val">
                <input type="text" wire:model="data.declarant_position" class="saln-input" {{ $dis }} />
            </td>
        </tr>
        <tr style="height:6px;"></tr>
        <tr>
            <td></td><td colspan="5"></td><td></td>
            <td class="saln-pinfo-label">AGENCY/OFFICE:</td>
            <td class="saln-pinfo-val">
                <input type="text" wire:model="data.declarant_agency_office" class="saln-input" {{ $dis }} />
            </td>
        </tr>
    </table>

    <div style="border-top:2px solid #000; margin:6px 0;"></div>

    {{-- ── ASSETS, LIABILITIES AND NET WORTH ── --}}
    <div class="saln-section-header">ASSETS, LIABILITIES AND NET WORTH</div>

    <div style="font-weight:bold; font-size:9pt; margin:5px 0 4px;">1. &nbsp;ASSETS</div>

    {{-- ── REAL PROPERTIES (Annex B) ── --}}
    <div class="saln-subsection-title">a. &nbsp;Real Properties <span class="saln-subsection-desc">(e.g. lot, house and lot, condominium, and improvements)</span></div>

    <table class="saln-table">
        <thead>
            <tr>
                <th rowspan="2" style="width:14%;">DESCRIPTION<br><span style="font-weight:normal; font-size:6pt; font-style:italic;">(e.g. lot, house and lot, condominium, and improvements)</span></th>
                <th rowspan="2" style="width:10%;">KIND<br><span style="font-weight:normal; font-size:6pt; font-style:italic;">(e.g. residential, commercial, industrial, agricultural and mixed use)</span></th>
                <th rowspan="2" style="width:15%;">EXACT LOCATION</th>
                <th rowspan="2" style="width:12%;">ASSESSED VALUE<br><span style="font-weight:normal; font-size:6pt; font-style:italic;">(As found in the Tax Declaration of Real Property)</span></th>
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
            @foreach($this->data['annexBRealProperties'] ?? [] as $index => $prop)
            <tr>
                <td class="la" style="padding:3px 4px;">
                    <textarea wire:model="data.annexBRealProperties.{{ $index }}.description" class="saln-input" rows="2" {{ $dis }}></textarea>
                </td>
                <td style="padding:3px 4px;">
                    <input type="text" wire:model="data.annexBRealProperties.{{ $index }}.kind" class="saln-input" {{ $dis }} />
                </td>
                <td class="la" style="padding:3px 4px;">
                    <input type="text" wire:model="data.annexBRealProperties.{{ $index }}.exact_location" class="saln-input" {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="number" wire:model="data.annexBRealProperties.{{ $index }}.assessed_value" class="saln-input" step="0.01" {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="number"
                        wire:model="data.annexBRealProperties.{{ $index }}.current_fair_market_value"
                        data-saln="annexb-real-fmv"
                        class="saln-input" step="0.01"
                        x-on:input="$store.saln.recalculate()"
                        x-on:change="$store.saln.recalculate()"
                        {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="number" wire:model="data.annexBRealProperties.{{ $index }}.acquisition_year" class="saln-input" placeholder="YYYY" min="1900" max="{{ date('Y') }}" {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="text" wire:model="data.annexBRealProperties.{{ $index }}.mode_of_acquisition" class="saln-input" {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="number" wire:model="data.annexBRealProperties.{{ $index }}.acquisition_cost" class="saln-input" step="0.01" {{ $dis }} />
                </td>
            </tr>
            @if(!$ro)
            <tr style="background:#fff8f8;">
                <td colspan="8" style="padding:2px 5px; text-align:right; border-top:none;">
                    <button type="button" wire:click="removeAnnexBRealProperty({{ $index }})" class="saln-btn-remove">Remove</button>
                </td>
            </tr>
            @endif
            @endforeach
            @for($i = count($this->data['annexBRealProperties'] ?? []); $i < 5; $i++)
            <tr class="dr"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
            @endfor
            <tr class="sub-r">
                <td colspan="4" style="text-align:right; padding:3px 6px;">Subtotal:</td>
                <td x-data style="padding:3px 5px;"
                    x-init="$store.saln.recalculate()"
                    x-text="(() => { let t=0; document.querySelectorAll('[data-saln=annexb-real-fmv]').forEach(e=>t+=parseFloat(e.value)||0); return '₱'+t.toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2}); })()">₱0.00</td>
                <td colspan="3"></td>
            </tr>
        </tbody>
    </table>
    @if(!$ro)
    <div style="margin:3px 0 6px;">
        <button type="button" wire:click="addAnnexBRealProperty" class="saln-btn-add">+ Add Real Property</button>
    </div>
    @endif

    {{-- ── PERSONAL PROPERTIES (Annex B) ── --}}
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
            @foreach($this->data['annexBPersonalProperties'] ?? [] as $index => $prop)
            <tr>
                <td class="la" style="padding:3px 4px;">
                    <div style="display:flex; align-items:center; gap:4px;">
                        <textarea wire:model="data.annexBPersonalProperties.{{ $index }}.description" class="saln-input" rows="1" {{ $dis }}></textarea>
                        @if(!$ro)
                        <button type="button" wire:click="removeAnnexBPersonalProperty({{ $index }})" class="saln-btn-remove" style="flex-shrink:0;">×</button>
                        @endif
                    </div>
                </td>
                <td style="padding:3px 4px;">
                    <input type="number" wire:model="data.annexBPersonalProperties.{{ $index }}.year_acquired" class="saln-input" placeholder="YYYY" min="1900" max="{{ date('Y') }}" {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="number"
                        wire:model="data.annexBPersonalProperties.{{ $index }}.acquisition_cost"
                        data-saln="annexb-personal-cost"
                        class="saln-input" step="0.01"
                        x-on:input="$store.saln.recalculate()"
                        x-on:change="$store.saln.recalculate()"
                        {{ $dis }} />
                </td>
            </tr>
            @endforeach
            @for($i = count($this->data['annexBPersonalProperties'] ?? []); $i < 3; $i++)
            <tr class="dr"><td></td><td></td><td></td></tr>
            @endfor
            <tr class="sub-r">
                <td colspan="2" style="text-align:right; padding:3px 6px;">Subtotal:</td>
                <td x-data style="padding:3px 5px;"
                    x-init="$store.saln.recalculate()"
                    x-text="(() => { let t=0; document.querySelectorAll('[data-saln=annexb-personal-cost]').forEach(e=>t+=parseFloat(e.value)||0); return '₱'+t.toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2}); })()">₱0.00</td>
            </tr>
            <tr class="sub-r" style="font-size:9pt;">
                <td colspan="2" style="text-align:right; padding:4px 6px; font-weight:bold;">TOTAL ASSETS:</td>
                <td x-data style="padding:4px 5px; font-weight:bold; color:#15803d;"
                    x-init="$store.saln.recalculate()"
                    x-text="(() => { let t=0; document.querySelectorAll('[data-saln=annexb-real-fmv],[data-saln=annexb-personal-cost]').forEach(e=>t+=parseFloat(e.value)||0); return '₱'+t.toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2}); })()">₱0.00</td>
            </tr>
        </tbody>
    </table>
    @if(!$ro)
    <div style="margin:3px 0 5px;">
        <button type="button" wire:click="addAnnexBPersonalProperty" class="saln-btn-add">+ Add Personal Property</button>
    </div>
    @endif

    <div style="border-top:2px solid #000; margin:6px 0;"></div>

    {{-- ── LIABILITIES (Annex B) ── --}}
    <div style="font-weight:bold; font-size:9pt; margin:0 0 4px;">2. &nbsp;LIABILITIES</div>

    <table class="saln-table">
        <thead>
            <tr>
                <th style="width:35%;">NATURE</th>
                <th style="width:40%;">NAME OF CREDITORS</th>
                <th style="width:25%;">OUTSTANDING BALANCE</th>
            </tr>
        </thead>
        <tbody>
            @foreach($this->data['annexBLiabilities'] ?? [] as $index => $liability)
            <tr>
                <td class="la" style="padding:3px 4px;">
                    <input type="text" wire:model="data.annexBLiabilities.{{ $index }}.nature" class="saln-input" placeholder="e.g., Housing Loan" {{ $dis }} />
                </td>
                <td class="la" style="padding:3px 4px;">
                    <input type="text" wire:model="data.annexBLiabilities.{{ $index }}.name_of_creditors" class="saln-input" {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="number"
                        wire:model="data.annexBLiabilities.{{ $index }}.outstanding_balance"
                        data-saln="annexb-liability"
                        class="saln-input" step="0.01"
                        x-on:input="$store.saln.recalculate()"
                        x-on:change="$store.saln.recalculate()"
                        {{ $dis }} />
                </td>
            </tr>
            @if(!$ro)
            <tr style="background:#fff8f8;">
                <td colspan="3" style="padding:2px 5px; text-align:right; border-top:none;">
                    <button type="button" wire:click="removeAnnexBLiability({{ $index }})" class="saln-btn-remove">Remove</button>
                </td>
            </tr>
            @endif
            @endforeach
            @for($i = count($this->data['annexBLiabilities'] ?? []); $i < 3; $i++)
            <tr class="dr"><td></td><td></td><td></td></tr>
            @endfor
            <tr class="sub-r">
                <td colspan="2" style="text-align:right; padding:3px 8px; font-weight:bold;">TOTAL LIABILITIES:</td>
                <td x-data style="padding:3px 5px;"
                    x-init="$store.saln.recalculate()"
                    x-text="(() => { let t=0; document.querySelectorAll('[data-saln=annexb-liability]').forEach(e=>t+=parseFloat(e.value)||0); return '₱'+t.toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2}); })()">₱0.00</td>
            </tr>
        </tbody>
    </table>
    @if(!$ro)
    <div style="margin:3px 0 5px;">
        <button type="button" wire:click="addAnnexBLiability" class="saln-btn-add">+ Add Liability</button>
    </div>
    @endif

    <div style="border-top:2px solid #000; margin:6px 0;"></div>

    {{-- ── BUSINESS INTERESTS (Annex B) ── --}}
    <div class="saln-section-header">BUSINESS INTERESTS AND FINANCIAL CONNECTIONS</div>

    <table class="saln-table">
        <thead>
            <tr>
                <th style="width:25%;">NAME OF ENTITY/<br>BUSINESS ENTERPRISE</th>
                <th style="width:25%;">BUSINESS ADDRESS</th>
                <th style="width:25%;">NATURE OF BUSINESS INTEREST &amp;/OR FINANCIAL CONNECTION</th>
                <th style="width:25%;">DATE OF ACQUISITION OF INTEREST OR CONNECTION</th>
            </tr>
        </thead>
        <tbody>
            @foreach($this->data['annexBBusinessInterests'] ?? [] as $index => $business)
            <tr>
                <td class="la" style="padding:3px 4px;">
                    <input type="text" wire:model="data.annexBBusinessInterests.{{ $index }}.name_of_entity" class="saln-input" {{ $dis }} />
                </td>
                <td class="la" style="padding:3px 4px;">
                    <textarea wire:model="data.annexBBusinessInterests.{{ $index }}.business_address" class="saln-input" rows="2" {{ $dis }}></textarea>
                </td>
                <td class="la" style="padding:3px 4px;">
                    <input type="text" wire:model="data.annexBBusinessInterests.{{ $index }}.nature_of_business_interest" class="saln-input" {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="date" wire:model="data.annexBBusinessInterests.{{ $index }}.date_of_acquisition" class="saln-input" {{ $dis }} />
                </td>
            </tr>
            @if(!$ro)
            <tr style="background:#fff8f8;">
                <td colspan="4" style="padding:2px 5px; text-align:right; border-top:none;">
                    <button type="button" wire:click="removeAnnexBBusinessInterest({{ $index }})" class="saln-btn-remove">Remove</button>
                </td>
            </tr>
            @endif
            @endforeach
            @for($i = count($this->data['annexBBusinessInterests'] ?? []); $i < 3; $i++)
            <tr class="dr"><td></td><td></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>
    @if(!$ro)
    <div style="margin:3px 0 5px;">
        <button type="button" wire:click="addAnnexBBusinessInterest" class="saln-btn-add">+ Add Business Interest</button>
    </div>
    @endif

    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px; padding-top:4px; border-top:1px solid #ccc; font-size:7.5pt;">
        <div>Page ___ of ___</div>
        <div style="font-style:italic;">Signature/Initial of Declarant: ___________________________</div>
    </div>

</div>
