{{-- PAGE 4: ANNEX C — Additional Sheet for Spouse & Unmarried Children (SALN Form AS-2) --}}
{{-- 2025 CSC SALN Format — CSC Resolution No. 2500632, Promulgated June 25, 2025 --}}
{{-- Purpose: Exclusive properties of the declarant's spouse and unmarried children below 18 --}}
@php $ro = $isReadOnly ?? false; $dis = $ro ? 'disabled readonly' : ''; $disCb = $ro ? 'disabled' : ''; @endphp

<div class="saln-form-page" style="margin-top: 30px;">

    {{-- ── HEADER ── --}}
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:4px;">
        <div class="saln-annex-label">ANNEX C</div>
        <div class="saln-header-ref" style="text-align:right;">
            2025 SALN Form AS-2 (Spouse &amp; Children)<br>
            Per CSC Resolution No. ____________<br>
            Promulgated on ____________
        </div>
    </div>

    <div class="saln-form-title">STATEMENT OF ASSETS, LIABILITIES AND NET WORTH</div>
    <div style="text-align:center; font-size:8pt; margin-bottom:2px;">
        As of
        <span style="border-bottom:1px solid #000; display:inline-block; min-width:160px; padding:0 4px; vertical-align:bottom;">
            @if(!empty($this->data['as_of_date']))
                {{ \Carbon\Carbon::parse($this->data['as_of_date'])->format('F d, Y') }}
            @endif
        </span>
    </div>
    <div style="text-align:center; font-size:8pt; font-style:italic; margin-bottom:8px; line-height:1.4;">
        (Additional sheet/s for the exclusive properties of the declarant's spouse and unmarried children<br>
        below eighteen (18) years of age living in declarant's household)
    </div>

    {{-- ── NAME / POSITION (Spouse) ── --}}
    <table style="width:100%; border-collapse:collapse; margin:5px 0;">
        <tr>
            <td class="saln-pinfo-label" style="width:50px;">NAME:</td>
            <td class="saln-pinfo-val" style="width:22%;">
                <input type="text" wire:model="data.spouse_family_name" class="saln-input" placeholder="Family Name" {{ $dis }} />
                <div class="saln-name-hint">(Family Name)</div>
            </td>
            <td style="width:4px;"></td>
            <td class="saln-pinfo-val" style="width:22%;">
                <input type="text" wire:model="data.spouse_first_name" class="saln-input" placeholder="First Name" {{ $dis }} />
                <div class="saln-name-hint">(First Name)</div>
            </td>
            <td style="width:4px;"></td>
            <td class="saln-pinfo-val" style="width:7%;">
                <input type="text" wire:model="data.spouse_middle_initial" class="saln-input" placeholder="M.I." maxlength="5" {{ $dis }} />
                <div class="saln-name-hint">(M.I.)</div>
            </td>
            <td style="width:8px;"></td>
            <td class="saln-pinfo-label" style="width:70px;">POSITION:</td>
            <td class="saln-pinfo-val">
                <input type="text" wire:model="data.spouse_position" class="saln-input" {{ $dis }} />
            </td>
        </tr>
        <tr style="height:6px;"></tr>
        <tr>
            <td></td><td colspan="5"></td><td></td>
            <td class="saln-pinfo-label">AGENCY/OFFICE:</td>
            <td class="saln-pinfo-val">
                <input type="text" wire:model="data.spouse_agency_office" class="saln-input" {{ $dis }} />
            </td>
        </tr>
    </table>

    <div style="border-top:2px solid #000; margin:6px 0;"></div>

    {{-- ── ASSETS, LIABILITIES AND NET WORTH ── --}}
    <div class="saln-section-header">ASSETS, LIABILITIES AND NET WORTH</div>

    <div style="font-weight:bold; font-size:9pt; margin:5px 0 4px;">1. &nbsp;ASSETS</div>

    {{-- ── REAL PROPERTIES (Annex C) ── --}}
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
            @foreach($this->data['annexCRealProperties'] ?? [] as $index => $prop)
            <tr>
                <td class="la" style="padding:3px 4px;">
                    <textarea wire:model="data.annexCRealProperties.{{ $index }}.description" class="saln-input" rows="2" {{ $dis }}></textarea>
                </td>
                <td style="padding:3px 4px;">
                    <input type="text" wire:model="data.annexCRealProperties.{{ $index }}.kind" class="saln-input" {{ $dis }} />
                </td>
                <td class="la" style="padding:3px 4px;">
                    <input type="text" wire:model="data.annexCRealProperties.{{ $index }}.exact_location" class="saln-input" {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="number" wire:model="data.annexCRealProperties.{{ $index }}.assessed_value" class="saln-input" step="0.01" {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="number"
                        wire:model="data.annexCRealProperties.{{ $index }}.current_fair_market_value"
                        data-saln="annexc-real-fmv"
                        class="saln-input" step="0.01"
                        x-on:input="$store.saln.recalculate()"
                        x-on:change="$store.saln.recalculate()"
                        {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="number" wire:model="data.annexCRealProperties.{{ $index }}.acquisition_year" class="saln-input" placeholder="YYYY" min="1900" max="{{ date('Y') }}" {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="text" wire:model="data.annexCRealProperties.{{ $index }}.mode_of_acquisition" class="saln-input" {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="number" wire:model="data.annexCRealProperties.{{ $index }}.acquisition_cost" class="saln-input" step="0.01" {{ $dis }} />
                </td>
            </tr>
            @if(!$ro)
            <tr style="background:#fff8f8;">
                <td colspan="8" style="padding:2px 5px; text-align:right; border-top:none;">
                    <button type="button" wire:click="removeAnnexCRealProperty({{ $index }})" class="saln-btn-remove">Remove</button>
                </td>
            </tr>
            @endif
            @endforeach
            @for($i = count($this->data['annexCRealProperties'] ?? []); $i < 5; $i++)
            <tr class="dr"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>
    @if(!$ro)
    <div style="margin:3px 0 6px;">
        <button type="button" wire:click="addAnnexCRealProperty" class="saln-btn-add">+ Add Real Property</button>
    </div>
    @endif

    {{-- ── PERSONAL PROPERTIES (Annex C) ── --}}
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
            @foreach($this->data['annexCPersonalProperties'] ?? [] as $index => $prop)
            <tr>
                <td class="la" style="padding:3px 4px;">
                    <div style="display:flex; align-items:center; gap:4px;">
                        <textarea wire:model="data.annexCPersonalProperties.{{ $index }}.description" class="saln-input" rows="1" {{ $dis }}></textarea>
                        @if(!$ro)
                        <button type="button" wire:click="removeAnnexCPersonalProperty({{ $index }})" class="saln-btn-remove" style="flex-shrink:0;">×</button>
                        @endif
                    </div>
                </td>
                <td style="padding:3px 4px;">
                    <input type="number" wire:model="data.annexCPersonalProperties.{{ $index }}.year_acquired" class="saln-input" placeholder="YYYY" min="1900" max="{{ date('Y') }}" {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="number"
                        wire:model="data.annexCPersonalProperties.{{ $index }}.acquisition_cost"
                        data-saln="annexc-personal-cost"
                        class="saln-input" step="0.01"
                        x-on:input="$store.saln.recalculate()"
                        x-on:change="$store.saln.recalculate()"
                        {{ $dis }} />
                </td>
            </tr>
            @endforeach
            @for($i = count($this->data['annexCPersonalProperties'] ?? []); $i < 3; $i++)
            <tr class="dr"><td></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>
    @if(!$ro)
    <div style="margin:3px 0 5px;">
        <button type="button" wire:click="addAnnexCPersonalProperty" class="saln-btn-add">+ Add Personal Property</button>
    </div>
    @endif

    <div style="border-top:2px solid #000; margin:6px 0;"></div>

    {{-- ── LIABILITIES (Annex C) ── --}}
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
            @foreach($this->data['annexCLiabilities'] ?? [] as $index => $liability)
            <tr>
                <td class="la" style="padding:3px 4px;">
                    <input type="text" wire:model="data.annexCLiabilities.{{ $index }}.nature" class="saln-input" placeholder="e.g., Housing Loan" {{ $dis }} />
                </td>
                <td class="la" style="padding:3px 4px;">
                    <input type="text" wire:model="data.annexCLiabilities.{{ $index }}.name_of_creditors" class="saln-input" {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="number"
                        wire:model="data.annexCLiabilities.{{ $index }}.outstanding_balance"
                        data-saln="annexc-liability"
                        class="saln-input" step="0.01"
                        x-on:input="$store.saln.recalculate()"
                        x-on:change="$store.saln.recalculate()"
                        {{ $dis }} />
                </td>
            </tr>
            @if(!$ro)
            <tr style="background:#fff8f8;">
                <td colspan="3" style="padding:2px 5px; text-align:right; border-top:none;">
                    <button type="button" wire:click="removeAnnexCLiability({{ $index }})" class="saln-btn-remove">Remove</button>
                </td>
            </tr>
            @endif
            @endforeach
            @for($i = count($this->data['annexCLiabilities'] ?? []); $i < 3; $i++)
            <tr class="dr"><td></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>
    @if(!$ro)
    <div style="margin:3px 0 5px;">
        <button type="button" wire:click="addAnnexCLiability" class="saln-btn-add">+ Add Liability</button>
    </div>
    @endif

    <div style="border-top:2px solid #000; margin:6px 0;"></div>

    {{-- ── BUSINESS INTERESTS (Annex C) ── --}}
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
            @foreach($this->data['annexCBusinessInterests'] ?? [] as $index => $business)
            <tr>
                <td class="la" style="padding:3px 4px;">
                    <input type="text" wire:model="data.annexCBusinessInterests.{{ $index }}.name_of_entity" class="saln-input" {{ $dis }} />
                </td>
                <td class="la" style="padding:3px 4px;">
                    <textarea wire:model="data.annexCBusinessInterests.{{ $index }}.business_address" class="saln-input" rows="2" {{ $dis }}></textarea>
                </td>
                <td class="la" style="padding:3px 4px;">
                    <input type="text" wire:model="data.annexCBusinessInterests.{{ $index }}.nature_of_business_interest" class="saln-input" {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="date" wire:model="data.annexCBusinessInterests.{{ $index }}.date_of_acquisition" class="saln-input" {{ $dis }} />
                </td>
            </tr>
            @if(!$ro)
            <tr style="background:#fff8f8;">
                <td colspan="4" style="padding:2px 5px; text-align:right; border-top:none;">
                    <button type="button" wire:click="removeAnnexCBusinessInterest({{ $index }})" class="saln-btn-remove">Remove</button>
                </td>
            </tr>
            @endif
            @endforeach
            @for($i = count($this->data['annexCBusinessInterests'] ?? []); $i < 3; $i++)
            <tr class="dr"><td></td><td></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>
    @if(!$ro)
    <div style="margin:3px 0 5px;">
        <button type="button" wire:click="addAnnexCBusinessInterest" class="saln-btn-add">+ Add Business Interest</button>
    </div>
    @endif

    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px; padding-top:4px; border-top:1px solid #ccc; font-size:7.5pt;">
        <div>Page ___ of ___</div>
        <div style="font-style:italic;">Signature/Initial of Declarant: ___________________________</div>
    </div>

</div>
