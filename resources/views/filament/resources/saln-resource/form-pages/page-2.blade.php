{{-- PAGE 2: ANNEX A — Liabilities, Business Interests, Relatives, Certification, Signatures --}}
{{-- 2025 CSC SALN Format — CSC Resolution No. 2500632, Promulgated June 25, 2025 --}}
@php $ro = $isReadOnly ?? false; $dis = $ro ? 'disabled readonly' : ''; $disCb = $ro ? 'disabled' : ''; @endphp

<div class="saln-form-page">

    {{-- ── LIABILITIES ── --}}
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
            @foreach($this->data['liabilities'] ?? [] as $index => $liability)
            <tr>
                <td class="la" style="padding:3px 4px;">
                    <input type="text" wire:model="data.liabilities.{{ $index }}.nature"
                        class="saln-input" placeholder="e.g., Housing Loan" {{ $dis }} />
                </td>
                <td class="la" style="padding:3px 4px;">
                    <input type="text" wire:model="data.liabilities.{{ $index }}.name_of_creditors"
                        class="saln-input" {{ $dis }} />
                </td>
                <td style="padding:3px 4px;">
                    <input type="number"
                        wire:model="data.liabilities.{{ $index }}.outstanding_balance"
                        data-saln="liability-balance"
                        class="saln-input" step="0.01"
                        x-on:input="$store.saln.recalculate()"
                        x-on:change="$store.saln.recalculate()"
                        {{ $dis }} />
                </td>
            </tr>
            @if(!$ro)
            <tr style="background:#fff8f8;">
                <td colspan="3" style="padding:2px 5px; text-align:right; border-top:none;">
                    <button type="button" wire:click="removeLiability({{ $index }})" class="saln-btn-remove">Remove</button>
                </td>
            </tr>
            @endif
            @endforeach
            @for($i = count($this->data['liabilities'] ?? []); $i < 4; $i++)
            <tr class="dr"><td></td><td></td><td></td></tr>
            @endfor
            <tr class="sub-r">
                <td colspan="2" style="text-align:right; padding:3px 8px; font-weight:bold;">TOTAL LIABILITIES:</td>
                <td x-data style="padding:3px 5px;"
                    x-init="$store.saln.recalculate()"
                    x-text="$store.saln.fmt($store.saln.totalLiabilities)">₱0.00</td>
            </tr>
        </tbody>
    </table>
    @if(!$ro)
    <div style="margin:3px 0 6px;">
        <button type="button" wire:click="addLiability" class="saln-btn-add">+ Add Liability</button>
    </div>
    @endif

    {{-- NET WORTH --}}
    <div x-data class="saln-net-worth-live">
        <span style="font-weight:bold; font-size:9pt;">NET WORTH: Total Assets less Total Liabilities =</span>
        <span class="nw-value"
            :class="{ 'negative': $store.saln.netWorth < 0 }"
            x-text="$store.saln.fmt($store.saln.netWorth)"
            x-init="$store.saln.recalculate()">₱0.00</span>
    </div>

    <div style="border-top:2px solid #000; margin:6px 0;"></div>

    {{-- ── BUSINESS INTERESTS ── --}}
    <div class="saln-section-header">BUSINESS INTERESTS AND FINANCIAL CONNECTIONS</div>
    <div class="saln-section-subtitle">(of Declarant /Declarant's spouse/ Unmarried Children Below Eighteen (18) years of Age Living in Declarant's Household)</div>

    <div style="margin:4px 0 5px;">
        <label class="saln-checkbox-label">
            <input type="checkbox" wire:model.live="data.no_business_interests"
                class="saln-checkbox-input" {{ $disCb }} />
            I/ We do not have any business interest or financial connection.
        </label>
    </div>

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
            @if(!($this->data['no_business_interests'] ?? false))
                @foreach($this->data['businessInterests'] ?? [] as $index => $business)
                <tr>
                    <td class="la" style="padding:3px 4px;">
                        <input type="text" wire:model="data.businessInterests.{{ $index }}.name_of_entity" class="saln-input" {{ $dis }} />
                    </td>
                    <td class="la" style="padding:3px 4px;">
                        <textarea wire:model="data.businessInterests.{{ $index }}.business_address" class="saln-input" rows="2" {{ $dis }}></textarea>
                    </td>
                    <td class="la" style="padding:3px 4px;">
                        <input type="text" wire:model="data.businessInterests.{{ $index }}.nature_of_business_interest" class="saln-input" {{ $dis }} />
                    </td>
                    <td style="padding:3px 4px;">
                        <input type="date" wire:model="data.businessInterests.{{ $index }}.date_of_acquisition" class="saln-input" {{ $dis }} />
                    </td>
                </tr>
                @if(!$ro)
                <tr style="background:#fff8f8;">
                    <td colspan="4" style="padding:2px 5px; text-align:right; border-top:none;">
                        <button type="button" wire:click="removeBusinessInterest({{ $index }})" class="saln-btn-remove">Remove</button>
                    </td>
                </tr>
                @endif
                @endforeach
            @endif
            @for($i = count(!($this->data['no_business_interests'] ?? false) ? ($this->data['businessInterests'] ?? []) : []); $i < 2; $i++)
            <tr class="dr"><td></td><td></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>
    @if(!$ro && !($this->data['no_business_interests'] ?? false))
    <div style="margin:3px 0 5px;">
        <button type="button" wire:click="addBusinessInterest" class="saln-btn-add">+ Add Business Interest</button>
    </div>
    @endif

    <div style="border-top:2px solid #000; margin:6px 0;"></div>

    {{-- ── RELATIVES IN GOVERNMENT ── --}}
    <div class="saln-section-header">RELATIVES IN THE GOVERNMENT SERVICE</div>
    <div class="saln-section-subtitle">(Within the Fourth Degree of Consanguinity or Affinity. Include also Bilas, Balae and Inso<sup>iv</sup>)</div>

    <div style="margin:4px 0 5px;">
        <label class="saln-checkbox-label">
            <input type="checkbox" wire:model.live="data.no_relatives_in_government"
                class="saln-checkbox-input" {{ $disCb }} />
            I/ We do not know of any relative/s in the government service.
        </label>
    </div>

    <table class="saln-table">
        <thead>
            <tr>
                <th style="width:25%;">NAME OF RELATIVE</th>
                <th style="width:20%;">RELATIONSHIP</th>
                <th style="width:25%;">POSITION</th>
                <th style="width:30%;">NAME OF AGENCY/OFFICE AND ADDRESS</th>
            </tr>
        </thead>
        <tbody>
            @if(!($this->data['no_relatives_in_government'] ?? false))
                @foreach($this->data['relativesInGovernment'] ?? [] as $index => $relative)
                <tr>
                    <td class="la" style="padding:3px 4px;">
                        <input type="text" wire:model="data.relativesInGovernment.{{ $index }}.name_of_relative" class="saln-input" {{ $dis }} />
                    </td>
                    <td style="padding:3px 4px;">
                        <input type="text" wire:model="data.relativesInGovernment.{{ $index }}.relationship" class="saln-input" {{ $dis }} />
                    </td>
                    <td class="la" style="padding:3px 4px;">
                        <input type="text" wire:model="data.relativesInGovernment.{{ $index }}.position" class="saln-input" {{ $dis }} />
                    </td>
                    <td class="la" style="padding:3px 4px;">
                        <textarea wire:model="data.relativesInGovernment.{{ $index }}.name_of_agency_office_address" class="saln-input" rows="2" {{ $dis }}></textarea>
                    </td>
                </tr>
                @if(!$ro)
                <tr style="background:#fff8f8;">
                    <td colspan="4" style="padding:2px 5px; text-align:right; border-top:none;">
                        <button type="button" wire:click="removeRelativeInGovernment({{ $index }})" class="saln-btn-remove">Remove</button>
                    </td>
                </tr>
                @endif
                @endforeach
            @endif
            @for($i = count(!($this->data['no_relatives_in_government'] ?? false) ? ($this->data['relativesInGovernment'] ?? []) : []); $i < 2; $i++)
            <tr class="dr"><td></td><td></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>
    @if(!$ro && !($this->data['no_relatives_in_government'] ?? false))
    <div style="margin:3px 0 5px;">
        <button type="button" wire:click="addRelativeInGovernment" class="saln-btn-add">+ Add Relative</button>
    </div>
    @endif

    <div style="border-top:2px solid #000; margin:6px 0;"></div>

    {{-- ── CERTIFICATION ── --}}
    <div class="saln-certification">
        I hereby certify that these are true and correct statements of my assets, liabilities, net worth, business interests and financial connections, including those of my spouse and unmarried children below eighteen (18) years of age living in my household, and that to the best of my knowledge, the above-enumerated are names of my relatives in the government within the fourth civil degree of consanguinity or affinity.
    </div>
    <div class="saln-certification" style="margin-top:5px;">
        I hereby authorize the Ombudsman or his/her duly authorized representative to obtain and secure from all appropriate government agencies, including the Bureau of Internal Revenue such documents that may show my assets, liabilities, net worth, business interests and financial connections, to include those of my spouse and unmarried children below 18 years of age living with me in my household covering previous years to include the year I first assumed office in government.
    </div>

    {{-- DATE --}}
    <div style="margin:8px 0; font-size:8.5pt; display:flex; align-items:center; gap:8px;">
        <span style="font-weight:bold;">Date:</span>
        <div style="border-bottom:1px solid #000; width:220px; padding:1px 4px;">
            <input type="date" wire:model="data.date_signed" class="saln-input"
                style="width:220px;" {{ $dis }} />
        </div>
    </div>

    {{-- SIGNATURES --}}
    <div class="saln-signature-row">
        <div class="saln-signature-box">
            <div class="saln-signature-line"></div>
            <div class="saln-signature-label">(Signature of Declarant)</div>
            <div style="margin-top:6px; font-size:7.5pt;">
                <div style="font-weight:bold;">Government Issued ID:</div>
                <div style="border-bottom:1px solid #000; min-height:14px; margin:2px 0;"></div>
                <div style="font-weight:bold;">ID No.:</div>
                <div style="border-bottom:1px solid #000; min-height:14px; margin:2px 0;"></div>
                <div style="font-weight:bold;">Date Issued:</div>
                <div style="border-bottom:1px solid #000; min-height:14px; margin:2px 0;"></div>
            </div>
        </div>
        <div class="saln-signature-box">
            <div class="saln-signature-line"></div>
            <div class="saln-signature-label">(Signature of Declarant)</div>
            <div style="margin-top:6px; font-size:7.5pt;">
                <div style="font-weight:bold;">Government Issued ID:</div>
                <div style="border-bottom:1px solid #000; min-height:14px; margin:2px 0;"></div>
                <div style="font-weight:bold;">ID No.:</div>
                <div style="border-bottom:1px solid #000; min-height:14px; margin:2px 0;"></div>
                <div style="font-weight:bold;">Date Issued:</div>
                <div style="border-bottom:1px solid #000; min-height:14px; margin:2px 0;"></div>
            </div>
        </div>
    </div>

    {{-- OATH --}}
    <div style="margin-top:12px; text-align:justify; font-size:8pt; font-weight:bold; text-decoration:underline; line-height:1.7;">
        SUBSCRIBED AND SWORN to before me this
        <input type="text" wire:model="data.subscribed_sworn_date" class="saln-input"
            style="width:180px; border-bottom:1px solid #000; display:inline-block; text-align:center; text-decoration:none;"
            {{ $dis }} placeholder="__ day of _____, ____" />
        affiant exhibiting to me the above-stated government-issued identification card.
    </div>

    <div style="text-align:center; margin-top:20px;">
        <div style="border-top:1.5px solid #000; width:260px; margin:0 auto 4px;"></div>
        <div style="font-size:7.5pt; font-weight:bold;">
            <input type="text" wire:model="data.person_administering_oath" class="saln-input"
                style="text-align:center; width:260px; display:inline-block;" {{ $dis }}
                placeholder="(Person Administering Oath)" />
        </div>
    </div>

    {{-- FOOTNOTES --}}
    <div class="saln-footnotes">
        <p><sup>i</sup> Position, Agency, and Address shall only be declared if the spouse is a public official or employee.</p>
        <p><sup>ii</sup> Additional sheets may be used by the declarant, if necessary.</p>
        <p><sup>iii</sup> Capital or paraphernal assets, and liabilities of the declarant's spouse, and properties of children below 18 years of age and living in the declarant's household shall be disclosed using the additional sheets provided.</p>
        <p><sup>iv</sup> Balae refers to the parent of one's son or daughter-in-law; Bilas refers to a brother-in-law's wife or sister-in-law's husband; Inso refers to the appellation for the wife of an elder brother or male cousin.</p>
    </div>

    {{-- NOTES --}}
    <div class="saln-note-section">
        <p><strong>NOTE:</strong> Violation of this law is punishable by a fine not exceeding five thousand pesos (₱5,000) or imprisonment not exceeding one (1) year, or both, at the discretion of the court (Section 11, R.A. 6713).</p>
        <p style="margin-top:3px;"><strong>REMINDER:</strong> Any misrepresentation or non-disclosure of any material fact required to be stated herein shall constitute perjury under Article 183 of the Revised Penal Code and shall be punished accordingly.</p>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px; padding-top:4px; border-top:1px solid #ccc; font-size:7.5pt;">
        <div>Page 2 of ___</div>
        <div style="font-style:italic;">Signature/Initial of Declarant: ___________________________</div>
    </div>

</div>
