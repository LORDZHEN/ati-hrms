{{-- CSC Form 6 - Application for Leave --}}
{{--
    BLADE vs ALPINE $ RULE:
    Never use $wire, $event, $refs, $nextTick directly inside Blade
    attribute strings (@click="...", :class="...") — Blade will try
    to parse them as PHP variables and throw errors.

    Solution: every interaction is a named method on the Alpine component
    defined in the <script> block below. Blade attributes only call
    those method names — no $ signs appear in attributes at all.

    VIEW PAGE FIX (date_of_filing / leave_date_from / leave_date_to / number_of_working_days):
    On Filament's ViewRecord page, $this->data is NOT populated —
    only $this->record exists. On CreateRecord/EditRecord, $this->data IS populated.
    We use a $resolve() helper that checks both sources so this blade
    works correctly on all three page types without any other changes.
--}}

@php
    /**
     * $resolve(field) — reads from whichever source is available:
     *   1. $this->data[$field]  — populated on Create / Edit pages
     *   2. $this->record->$field — populated on View page
     */
    $resolve = function (string $field) {
        $fromData   = $this->data[$field] ?? null;
        $fromRecord = isset($this->record) ? ($this->record->{$field} ?? null) : null;
        return !blank($fromData) ? $fromData : (!blank($fromRecord) ? $fromRecord : null);
    };

    $raw_date_of_filing         = $resolve('date_of_filing');
    $raw_leave_date_from        = $resolve('leave_date_from');
    $raw_leave_date_to          = $resolve('leave_date_to');
    $raw_number_of_working_days = $resolve('number_of_working_days');
    $raw_type_of_leave          = $resolve('type_of_leave');
    $raw_vacation_location      = $resolve('vacation_location');
    $raw_sick_leave_location    = $resolve('sick_leave_location');
    $raw_study_leave_purpose    = $resolve('study_leave_purpose');
    $raw_other_purpose          = $resolve('other_purpose');
    $raw_commutation            = $resolve('commutation');

    // Format to YYYY-MM-DD for <input type="date">
    $fmt_date_of_filing  = $raw_date_of_filing
        ? \Carbon\Carbon::parse($raw_date_of_filing)->format('Y-m-d')
        : '';
    $fmt_leave_date_from = $raw_leave_date_from
        ? \Carbon\Carbon::parse($raw_leave_date_from)->format('Y-m-d')
        : '';
    $fmt_leave_date_to   = $raw_leave_date_to
        ? \Carbon\Carbon::parse($raw_leave_date_to)->format('Y-m-d')
        : '';
@endphp

<div
    class="leave-form-page"
    x-data="leaveForm()"
    x-init="init()"
    {{--
        data-* attributes pass PHP-resolved values to Alpine synchronously.
        Alpine reads these in init() before any $wire.get() calls.
        This is what makes dates display correctly on the View page.
    --}}
    data-date-of-filing="{{ $fmt_date_of_filing }}"
    data-leave-date-from="{{ $fmt_leave_date_from }}"
    data-leave-date-to="{{ $fmt_leave_date_to }}"
    data-working-days="{{ $raw_number_of_working_days ?? '' }}"
    data-type-of-leave="{{ $raw_type_of_leave ?? '' }}"
    data-vacation-location="{{ $raw_vacation_location ?? '' }}"
    data-sick-leave-location="{{ $raw_sick_leave_location ?? '' }}"
    data-study-leave-purpose="{{ $raw_study_leave_purpose ?? '' }}"
    data-other-purpose="{{ $raw_other_purpose ?? '' }}"
    data-commutation="{{ $raw_commutation ?? '' }}"
>

    {{-- HEADER --}}
    <div class="leave-header">
        <div class="cs-note">
            Civil Service Form No. 6<br>
            Revised 2020
        </div>
        <div class="leave-header-content">
            <img src="{{ asset('images/ati_logo.png') }}" alt="Logo" class="leave-logo">
            <div class="leave-agency-info">
                <div class="leave-agency-text">
                    Republic of the Philippines<br>
                    <strong>AGRICULTURAL TRAINING INSTITUTE</strong><br>
                    Datu Abdul Dadia, Panabo City, Davao Del Norte, Philippines 8105<br>
                    Email Address: atixI.davao@gmail.com | Tel No. (084) 823-0557 | www.ati.da.gov.ph
                </div>
                <div class="leave-form-title">APPLICATION FOR LEAVE</div>
            </div>
        </div>
    </div>

    {{-- SECTION 1 & 2 --}}
    <table class="leave-table">
        <tr style="min-height: 16mm;">
            <td width="25%">
                <span class="leave-label">1. OFFICE/DEPARTMENT</span><br>
                <input type="text" wire:model="data.office_department" class="leave-input" readonly style="margin-top:2px;" />
            </td>
            <td width="8%" style="text-align:center; vertical-align:middle;">
                <span class="leave-label">2.<br>NAME:</span>
            </td>
            <td width="22%">
                <span class="leave-label">(Last)</span><br>
                <input type="text" wire:model="data.last_name" class="leave-input" readonly style="margin-top:2px;" />
            </td>
            <td width="22%">
                <span class="leave-label">(First)</span><br>
                <input type="text" wire:model="data.first_name" class="leave-input" readonly style="margin-top:2px;" />
            </td>
            <td width="23%">
                <span class="leave-label">(Middle)</span><br>
                <input type="text" wire:model="data.middle_name" class="leave-input" readonly style="margin-top:2px;" />
            </td>
        </tr>
    </table>

    {{-- SECTION 3, 4, 5 --}}
    <table class="leave-table">
        <tr style="min-height: 12mm;">
            <td width="25%">
                <span class="leave-label">3. DATE OF FILING</span><br>
                {{--
                    FIX: value="{{ $fmt_date_of_filing }}" renders the date from PHP.
                    This shows immediately even on View page where wire:model has no data.
                    wire:model keeps it reactive on Create/Edit.
                --}}
                <input
                    type="date"
                    wire:model="data.date_of_filing"
                    class="leave-input"
                    readonly
                    style="margin-top:2px;"
                    value="{{ $fmt_date_of_filing }}"
                />
            </td>
            <td width="37.5%">
                <span class="leave-label">4. POSITION</span><br>
                <input type="text" wire:model="data.position" class="leave-input" readonly style="margin-top:2px;" />
            </td>
            <td width="37.5%">
                <span class="leave-label">5. SALARY</span><br>
                <span class="leave-value" style="display:block; margin-top:2px;">(Optional)</span>
            </td>
        </tr>
    </table>

    {{-- SECTION 6 HEADER --}}
    <table class="leave-table">
        <tr>
            <td class="leave-section-band">6. DETAILS OF APPLICATION</td>
        </tr>
    </table>

    {{-- SECTION 6.A & 6.B --}}
    <table class="leave-table">
        <tr style="min-height: 88mm; vertical-align: top;">

            {{-- 6.A TYPE OF LEAVE --}}
            <td width="50%">
                <div class="leave-label" style="margin-bottom: 4px;">6.A TYPE OF LEAVE TO BE AVAILED OF</div>

                @php
                $leaveTypes = [
                    'vacation_leave'                   => 'Vacation Leave (Sec 51, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                    'mandatory_forced_leave'           => 'Mandatory/Forced Leave (Sec. 25, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                    'sick_leave'                       => 'Sick Leave (Sec. 43, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                    'maternity_leave'                  => 'Maternity Leave (R.A. No. 11210 / IRR issued by CSC, DOLE and SSS)',
                    'paternity_leave'                  => 'Paternity Leave (R.A. No. 8187 / CSC MC No. 71, s. 1998, as amended)',
                    'special_privilege_leave'          => 'Special Privilege Leave (Sec. 21, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                    'solo_parent_leave'                => 'Solo Parent Leave (R.A. No. 8972 / CSC MC No. 8, s. 2004)',
                    'study_leave'                      => 'Study Leave (Sec. 68, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                    '10_day_vawc_leave'                => '10-Day VAWC Leave (R.A. No. 9262 / CSC MC No. 15, s. 2005)',
                    'rehabilitation_privilege'         => 'Rehabilitation Privilege (Sec. 55, Rule XVI, Omnibus Rules Implementing E.O. No. 292)',
                    'special_leave_benefits_for_women' => 'Special Leave Benefits for Women (R.A. No. 9710 / CSC MC No. 25, s. 2010)',
                    'special_emergency_leave'          => 'Special Emergency (Calamity) Leave (CSC MC No. 2, s. 2012, as amended)',
                    'adoption_leave'                   => 'Adoption Leave (R.A. No. 8552)',
                    'others'                           => 'Others:',
                ];
                @endphp

                @foreach($leaveTypes as $key => $text)
                <div class="leave-type-item">
                    <span
                        class="lf-checkbox"
                        :class="{ 'lf-checkbox--checked': leaveType === '{{ $key }}' }"
                        @click="selectLeaveType('{{ $key }}')"
                    ></span>
                    <span style="cursor:default;">{{ $text }}</span>
                </div>
                @endforeach

                @if($raw_type_of_leave === 'others')
                <div style="margin-top: 3px;">
                    <input type="text" wire:model="data.other_leave_type" class="leave-input" placeholder="Specify other leave type" style="border-bottom:1px solid #000; min-width:150px;" />
                </div>
                @endif
            </td>

            {{-- 6.B DETAILS OF LEAVE --}}
            <td width="50%">
                <div class="leave-label" style="margin-bottom: 4px;">6.B DETAILS OF LEAVE</div>

                {{-- Vacation / Special Privilege Leave --}}
                <div class="leave-detail-section">
                    <em>In case of Vacation/Special Privilege Leave:</em><br>

                    <div style="display:flex; align-items:center; gap:3px; margin:3px 0;">
                        <span
                            class="lf-checkbox"
                            :class="{ 'lf-checkbox--checked': vacationLocation === 'within_philippines' }"
                            @click="toggleField('vacationLocation', 'within_philippines', 'data.vacation_location')"
                        ></span>
                        Within the Philippines
                    </div>

                    <div style="display:flex; align-items:center; gap:3px; margin:3px 0; flex-wrap:wrap;">
                        <span
                            class="lf-checkbox"
                            :class="{ 'lf-checkbox--checked': vacationLocation === 'abroad' }"
                            @click="toggleField('vacationLocation', 'abroad', 'data.vacation_location')"
                        ></span>
                        Abroad (Specify)
                        @if($raw_vacation_location === 'abroad')
                        <input type="text" wire:model="data.abroad_specify" class="leave-input" placeholder="Country" style="border-bottom:1px solid #000; min-width:75px; margin-left:5px;" />
                        @endif
                    </div>
                </div>

                {{-- Sick Leave --}}
                <div class="leave-detail-section">
                    <em>In case of Sick Leave:</em><br>

                    <div style="display:flex; align-items:center; gap:3px; margin:3px 0; flex-wrap:wrap;">
                        <span
                            class="lf-checkbox"
                            :class="{ 'lf-checkbox--checked': sickLeaveLocation === 'in_hospital' }"
                            @click="toggleField('sickLeaveLocation', 'in_hospital', 'data.sick_leave_location')"
                        ></span>
                        In Hospital (Specify Illness)
                        @if($raw_sick_leave_location === 'in_hospital')
                        <input type="text" wire:model="data.hospital_illness_specify" class="leave-input" placeholder="Illness" style="border-bottom:1px solid #000; min-width:50px; margin-left:5px;" />
                        @endif
                    </div>

                    <div style="display:flex; align-items:center; gap:3px; margin:3px 0; flex-wrap:wrap;">
                        <span
                            class="lf-checkbox"
                            :class="{ 'lf-checkbox--checked': sickLeaveLocation === 'out_patient' }"
                            @click="toggleField('sickLeaveLocation', 'out_patient', 'data.sick_leave_location')"
                        ></span>
                        Out Patient (Specify Illness)
                        @if($raw_sick_leave_location === 'out_patient')
                        <input type="text" wire:model="data.outpatient_illness_specify" class="leave-input" placeholder="Illness" style="border-bottom:1px solid #000; min-width:50px; margin-left:5px;" />
                        @endif
                    </div>
                </div>

                {{-- Special Leave Benefits for Women --}}
                <div class="leave-detail-section">
                    <em>In case of Special Leave Benefits for Women:</em><br>
                    (Specify Illness)
                    <input type="text" wire:model="data.women_illness_specify" class="leave-input" placeholder="Illness" style="border-bottom:1px solid #000; min-width:100px; margin-top:2px;" />
                </div>

                {{-- Study Leave --}}
                <div class="leave-detail-section">
                    <em>In case of Study Leave:</em><br>

                    <div style="display:flex; align-items:center; gap:3px; margin:3px 0;">
                        <span
                            class="lf-checkbox"
                            :class="{ 'lf-checkbox--checked': studyLeavePurpose === 'masters_degree' }"
                            @click="toggleField('studyLeavePurpose', 'masters_degree', 'data.study_leave_purpose')"
                        ></span>
                        Completion of Master's Degree
                    </div>

                    <div style="display:flex; align-items:center; gap:3px; margin:3px 0;">
                        <span
                            class="lf-checkbox"
                            :class="{ 'lf-checkbox--checked': studyLeavePurpose === 'bar_board_review' }"
                            @click="toggleField('studyLeavePurpose', 'bar_board_review', 'data.study_leave_purpose')"
                        ></span>
                        BAR/Board Examination Review
                    </div>
                </div>

                {{-- Other Purpose --}}
                <div class="leave-detail-section" style="margin-bottom: 0;">
                    <em>Other purpose:</em><br>

                    <div style="display:flex; align-items:center; gap:3px; margin:3px 0;">
                        <span
                            class="lf-checkbox"
                            :class="{ 'lf-checkbox--checked': otherPurpose === 'monetization' }"
                            @click="toggleField('otherPurpose', 'monetization', 'data.other_purpose')"
                        ></span>
                        Monetization of Leave Credits
                    </div>

                    <div style="display:flex; align-items:center; gap:3px; margin:3px 0;">
                        <span
                            class="lf-checkbox"
                            :class="{ 'lf-checkbox--checked': otherPurpose === 'terminal_leave' }"
                            @click="toggleField('otherPurpose', 'terminal_leave', 'data.other_purpose')"
                        ></span>
                        Terminal Leave
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- SECTION 6.C & 6.D --}}
    <table class="leave-table">
        <tr style="min-height: 22mm;">
            <td width="50%">
                <div class="leave-label">6.C NUMBER OF WORKING DAYS APPLIED FOR</div>

                {{--
                    FIX: value="{{ $raw_number_of_working_days }}" shows the PHP value
                    immediately. Alpine :value overrides it reactively on Create/Edit.
                --}}
                <div style="margin-top: 3px;">
                    <input
                        type="number"
                        class="leave-input"
                        readonly
                        step="0.5"
                        style="border-bottom:1px solid #000; width:65px;"
                        :value="workingDays !== null ? workingDays : ''"
                        value="{{ $raw_number_of_working_days ?? '' }}"
                    />
                </div>

                <div style="margin-top: 6px; font-weight: bold; font-size: 8pt;">
                    INCLUSIVE DATES
                </div>

                <div class="leave-grid-2" style="margin-top: 2px;">
                    <div>
                        <label class="leave-field-label">
                            From:
                            <span x-show="leaveType === 'vacation_leave'" style="color:#d97706; font-style:italic;">
                                (earliest: <span x-text="vacationMinDisplay"></span>)
                            </span>
                            <span x-show="leaveType === 'sick_leave'" style="color:#ef4444; font-style:italic;">
                                (past dates only)
                            </span>
                        </label>
                        {{--
                            FIX: value="{{ $fmt_leave_date_from }}" shows the PHP value immediately.
                            :value="dateFrom || '...'" keeps Alpine reactive on Create/Edit.
                        --}}
                        <input
                            type="date"
                            class="leave-input"
                            style="border-bottom:1px solid #000;"
                            :min="fromMin"
                            :max="fromMax"
                            :value="dateFrom || '{{ $fmt_leave_date_from }}'"
                            value="{{ $fmt_leave_date_from }}"
                            x-ref="dateFrom"
                            @change="onFromChange($event.target.value)"
                        />
                    </div>
                    <div>
                        <label class="leave-field-label">
                            To:
                            <span x-show="leaveType === 'sick_leave'" style="color:#ef4444; font-style:italic;">
                                (past dates only)
                            </span>
                        </label>
                        <input
                            type="date"
                            class="leave-input"
                            style="border-bottom:1px solid #000;"
                            :min="toMin"
                            :max="toMax"
                            :value="dateTo || '{{ $fmt_leave_date_to }}'"
                            value="{{ $fmt_leave_date_to }}"
                            x-ref="dateTo"
                            @change="onToChange($event.target.value)"
                        />
                    </div>
                </div>
            </td>

            {{-- 6.D COMMUTATION --}}
            <td width="50%">
                <div class="leave-label">6.D COMMUTATION</div>
                <div style="margin-top: 5px;">

                    <div style="display:flex; align-items:center; gap:5px; margin:4px 0;">
                        <span
                            class="lf-checkbox"
                            :class="{ 'lf-checkbox--checked': commutation === 'not_requested' }"
                            @click="toggleField('commutation', 'not_requested', 'data.commutation')"
                        ></span>
                        Not Requested
                    </div>

                    <div style="display:flex; align-items:center; gap:5px; margin:4px 0;">
                        <span
                            class="lf-checkbox"
                            :class="{ 'lf-checkbox--checked': commutation === 'requested' }"
                            @click="toggleField('commutation', 'requested', 'data.commutation')"
                        ></span>
                        Requested
                    </div>

                </div>
                <div style="text-align: right; padding-right: 12px; margin-top: 8px;">
                    <span class="leave-signature-line" style="width: 135px;"></span><br>
                    <span class="leave-signature-label">(Signature of Applicant)</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- Supporting Document Upload Note --}}
    @if($raw_type_of_leave === 'sick_leave' && ($raw_number_of_working_days ?? 0) >= 3)
    <div style="margin:5px 0; padding:8px; border:1px dashed #f59e0b; background:#fffbeb; border-radius:4px; font-size:8pt;">
        <strong>⚠️ Medical Certificate Required:</strong> Sick leave of 3 days or more requires supporting medical documentation.
        Please upload via the collapsed "Form Fields" section below.
    </div>
    @endif

    {{-- SECTION 7 HEADER --}}
    <table class="leave-table">
        <tr>
            <td class="leave-section-band">7. DETAILS OF ACTION ON APPLICATION</td>
        </tr>
    </table>

    {{-- SECTION 7.A & 7.B --}}
    <table class="leave-table">
        <tr style="min-height: 62mm; vertical-align: top;">
            <td width="50%">
                <div class="leave-label">7.A CERTIFICATION OF LEAVE CREDITS</div>
                <div style="margin-top: 3px; font-size: 8pt;">
                    As of <span class="leave-underline" style="min-width: 75px;"></span>
                </div>

                <table class="leave-credits-table">
                    <tr>
                        <td class="header-cell" style="width: 45%;"></td>
                        <td class="header-cell">Vacation Leave</td>
                        <td class="header-cell">Sick Leave</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Total Earned</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Less this application</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Balance</td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>

                <div class="leave-signature-area">
                    <span class="leave-signature-line"></span><br>
                    <span class="leave-signature-label">(Authorized Officer)</span>
                </div>
            </td>

            <td width="50%">
                <div class="leave-label">7.B RECOMMENDATION</div>
                <div style="margin-top: 6px;">
                    <span class="leave-checkbox"></span>
                    For approval
                </div>
                <div style="margin-top: 4px;">
                    <span class="leave-checkbox"></span>
                    For disapproval due to
                </div>
                <span class="leave-underline-full"></span>
                <span class="leave-underline-full"></span>
                <span class="leave-underline-full"></span>

                <div class="leave-signature-area">
                    <span class="leave-signature-line"></span><br>
                    <span class="leave-signature-label">(Authorized Officer)</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- SECTION 7.C & 7.D --}}
    <table class="leave-table">
        <tr style="min-height: 24mm;">
            <td width="50%">
                <div class="leave-label">7.C APPROVED FOR:</div>
                <div style="margin-top: 3px;">
                    <span class="leave-underline" style="min-width: 45px;"></span> days with pay
                </div>
                <div style="margin-top: 2px;">
                    <span class="leave-underline" style="min-width: 45px;"></span> days without pay
                </div>
                <div style="margin-top: 2px;">
                    <span class="leave-underline" style="min-width: 45px;"></span> others (Specify)
                </div>
            </td>
            <td width="50%">
                <div class="leave-label">7.D DISAPPROVED DUE TO:</div>
                <span class="leave-underline-full"></span>
                <span class="leave-underline-full"></span>
                <span class="leave-underline-full"></span>
            </td>
        </tr>
    </table>

    {{-- FINAL SIGNATURE --}}
    <table class="leave-table">
        <tr style="min-height: 14mm; text-align: center; vertical-align: middle;">
            <td>
                <span class="leave-signature-line" style="width: 200px;"></span><br>
                <span class="leave-signature-label">(Head of Agency / Authorized Official)</span>
            </td>
        </tr>
    </table>

    {{-- FOOTER --}}
    <div style="margin-top:3mm; text-align:center; font-size:6.5pt;">
        ATI-QF/AHRMO-09 &nbsp;&nbsp; Rev.03 &nbsp;&nbsp; Effectivity Date: July 09, 2021 &nbsp;&nbsp; Director
    </div>

</div>

{{-- ================================================================
     ALPINE COMPONENT — leaveForm()
     All $wire calls live here in a <script> tag — Blade never
     parses $ inside <script>, so no escaping is needed.

     VIEW PAGE FIX:
     init() reads data-* attributes on the root element FIRST (synchronous,
     always present, set by PHP). $wire.get() is used SECOND as a live
     override for Create/Edit reactivity. This two-step approach means
     all dates and working days display correctly on every page type.
================================================================ --}}
<script>
function leaveForm() {
    return {

        // ── Alpine-local state ────────────────────────────────────────
        leaveType:          null,
        vacationLocation:   null,
        sickLeaveLocation:  null,
        studyLeavePurpose:  null,
        otherPurpose:       null,
        commutation:        null,

        dateFrom:    '',
        dateTo:      '',
        workingDays: null,

        today:              '',
        vacationMin:        '',
        vacationMinDisplay: '',
        fromMin: '',
        fromMax: '',
        toMin:   '',
        toMax:   '',

        // ── Helpers ──────────────────────────────────────────────────

        fmt(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        },

        addWorkingDays(date, n) {
            const result = new Date(date);
            let added = 0;
            while (added < n) {
                result.setDate(result.getDate() + 1);
                const day = result.getDay();
                if (day !== 0 && day !== 6) added++;
            }
            return result;
        },

        fmtDisplay(ymd) {
            if (!ymd) return '';
            const d = new Date(ymd + 'T00:00:00');
            return d.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
        },

        countWorkingDays(from, to) {
            if (!from || !to) return null;
            const f = new Date(from + 'T00:00:00');
            const t = new Date(to   + 'T00:00:00');
            if (t < f) return null;
            let count = 0;
            const cur = new Date(f);
            while (cur <= t) {
                const day = cur.getDay();
                if (day !== 0 && day !== 6) count++;
                cur.setDate(cur.getDate() + 1);
            }
            return count > 0 ? count : null;
        },

        // Read a data-* attribute. Alpine converts kebab-case data attrs
        // to camelCase in $el.dataset automatically.
        // e.g. data-leave-date-from → this.$el.dataset.leaveDateFrom
        attr(name) {
            const val = this.$el.dataset[name];
            return (val !== undefined && val !== '') ? val : null;
        },

        // ── Date limit logic ─────────────────────────────────────────

        updateDateLimits() {
            const type = this.leaveType;
            const from = this.dateFrom;

            if (type === 'vacation_leave') {
                this.fromMin = this.vacationMin;
                this.fromMax = '';
                this.toMin   = from || this.vacationMin;
                this.toMax   = '';
            } else if (type === 'sick_leave') {
                this.fromMin = '';
                this.fromMax = this.today;
                this.toMin   = from || '';
                this.toMax   = this.today;
            } else {
                this.fromMin = this.today;
                this.fromMax = '';
                this.toMin   = from || this.today;
                this.toMax   = '';
            }
        },

        // ── Checkbox toggle methods ───────────────────────────────────

        toggleField(localProp, value, wirePath) {
            const next = this[localProp] === value ? null : value;
            this[localProp] = next;
            this.$wire.set(wirePath, next);
        },

        async selectLeaveType(key) {
            const next = this.leaveType === key ? null : key;
            this.leaveType = next;
            this.$wire.set('data.type_of_leave', next);

            this.dateFrom    = '';
            this.dateTo      = '';
            this.workingDays = null;
            this.$wire.set('data.leave_date_from', null);
            this.$wire.set('data.leave_date_to', null);
            this.$wire.set('data.number_of_working_days', null);

            this.updateDateLimits();
        },

        // ── Date change handlers ──────────────────────────────────────

        onFromChange(value) {
            this.dateFrom = value;
            this.updateDateLimits();

            if (this.dateTo && this.dateTo < value) {
                this.dateTo      = '';
                this.workingDays = null;
                this.$wire.set('data.leave_date_to', null);
                this.$wire.set('data.number_of_working_days', null);
            }

            this.$wire.set('data.leave_date_from', value || null);

            const days = this.countWorkingDays(this.dateFrom, this.dateTo);
            this.workingDays = days;
            this.$wire.set('data.number_of_working_days', days);
        },

        onToChange(value) {
            this.dateTo = value;
            this.updateDateLimits();
            this.$wire.set('data.leave_date_to', value || null);

            const days = this.countWorkingDays(this.dateFrom, this.dateTo);
            this.workingDays = days;
            this.$wire.set('data.number_of_working_days', days);
        },

        // ── Initialisation ───────────────────────────────────────────

        async init() {
            const now           = new Date();
            this.today          = this.fmt(now);
            const vacMin        = this.addWorkingDays(now, 5);
            this.vacationMin    = this.fmt(vacMin);
            this.vacationMinDisplay = this.fmtDisplay(this.vacationMin);

            // ── Step 1: PHP data-* attributes (synchronous, always present)
            // This is the fix — these values come from $this->record on
            // View page, so they are always populated regardless of page type.
            this.leaveType         = this.attr('typeOfLeave');
            this.vacationLocation  = this.attr('vacationLocation');
            this.sickLeaveLocation = this.attr('sickLeaveLocation');
            this.studyLeavePurpose = this.attr('studyLeavePurpose');
            this.otherPurpose      = this.attr('otherPurpose');
            this.commutation       = this.attr('commutation');
            this.dateFrom          = this.attr('leaveDateFrom') || '';
            this.dateTo            = this.attr('leaveDateTo')   || '';
            const rawDays          = this.attr('workingDays');
            this.workingDays       = rawDays ? parseFloat(rawDays) : null;

            // ── Step 2: $wire.get() overrides for live Create/Edit state
            // Silently ignored on View page where data.* paths are not exposed.
            try {
                const lt = await this.$wire.get('data.type_of_leave');
                if (lt) this.leaveType = lt;

                const vl = await this.$wire.get('data.vacation_location');
                if (vl) this.vacationLocation = vl;

                const sl = await this.$wire.get('data.sick_leave_location');
                if (sl) this.sickLeaveLocation = sl;

                const sp = await this.$wire.get('data.study_leave_purpose');
                if (sp) this.studyLeavePurpose = sp;

                const op = await this.$wire.get('data.other_purpose');
                if (op) this.otherPurpose = op;

                const cm = await this.$wire.get('data.commutation');
                if (cm) this.commutation = cm;

                const df = await this.$wire.get('data.leave_date_from');
                if (df) this.dateFrom = df;

                const dt = await this.$wire.get('data.leave_date_to');
                if (dt) this.dateTo = dt;

                const wd = await this.$wire.get('data.number_of_working_days');
                if (wd !== null && wd !== undefined) this.workingDays = wd;
            } catch (e) {
                // View page — silently ignore
            }

            this.updateDateLimits();

            // Keep in sync when hidden Filament fields change (Create/Edit)
            this.$wire.on('refresh', async () => {
                try {
                    const lt = await this.$wire.get('data.type_of_leave');          if (lt) this.leaveType = lt;
                    const vl = await this.$wire.get('data.vacation_location');       if (vl) this.vacationLocation = vl;
                    const sl = await this.$wire.get('data.sick_leave_location');     if (sl) this.sickLeaveLocation = sl;
                    const sp = await this.$wire.get('data.study_leave_purpose');     if (sp) this.studyLeavePurpose = sp;
                    const op = await this.$wire.get('data.other_purpose');           if (op) this.otherPurpose = op;
                    const cm = await this.$wire.get('data.commutation');             if (cm) this.commutation = cm;
                    const df = await this.$wire.get('data.leave_date_from');         if (df) this.dateFrom = df;
                    const dt = await this.$wire.get('data.leave_date_to');           if (dt) this.dateTo = dt;
                    const wd = await this.$wire.get('data.number_of_working_days');  if (wd !== null && wd !== undefined) this.workingDays = wd;
                } catch (e) {}
                this.updateDateLimits();
            });
        },
    };
}
</script>
