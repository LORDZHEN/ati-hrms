{{-- resources/views/pds/C2.blade.php | Page 2 of 4 --}}
<div class="page">

{{-- ── HEADER ── --}}
<table style="margin-bottom:3px;">
  <tr>
    <td class="nb cs-label" style="width:14%; vertical-align:top;">CS Form No. 212<br>Revised 2017</td>
    <td class="nb" style="width:72%;"><div class="title">PERSONAL DATA SHEET</div></td>
    <td class="nb cs-label" style="width:14%; text-align:right; vertical-align:top;">Page 2 of 4</td>
  </tr>
</table>

{{-- ════════════════════════════════════════════
     SECTION IV — CIVIL SERVICE ELIGIBILITY
     ════════════════════════════════════════════ --}}
<div class="sec">IV. &nbsp; CIVIL SERVICE ELIGIBILITY</div>

<table>
  <colgroup>
    <col style="width:29%">
    <col style="width:9%">
    <col style="width:13%">
    <col style="width:21%">
    <col style="width:15%">
    <col style="width:13%">
  </colgroup>
  <tr>
    <th class="hc" rowspan="2" style="text-align:left; padding:3px 5px; font-size:8pt;">
      27. CAREER SERVICE/ RA 1080 (BOARD/ BAR) UNDER SPECIAL LAWS/ CES/ CSEE<br>
      BARANGAY ELIGIBILITY / DRIVER'S LICENSE
    </th>
    <th class="hc" rowspan="2" style="font-size:7.5pt;">RATING<br><span style="font-weight:normal; font-size:7pt;">(If Applicable)</span></th>
    <th class="hc" rowspan="2" style="font-size:7.5pt;">DATE OF<br>EXAMINATION /<br>CONFERMENT<br><span style="font-weight:normal; font-size:7pt;">(mm/dd/yyyy)</span></th>
    <th class="hc" rowspan="2" style="font-size:7.5pt;">PLACE OF EXAMINATION /<br>CONFERMENT</th>
    <th class="hc" colspan="2" style="font-size:7.5pt;">LICENSE (if applicable)</th>
  </tr>
  <tr>
    <th class="hc" style="font-size:7.5pt;">NUMBER</th>
    <th class="hc" style="font-size:7.5pt;">Date of Validity</th>
  </tr>
  @php $elig = $pds->civil_service_eligibility ?? []; @endphp
  @for($i = 0; $i < 7; $i++)
    @php $el = $elig[$i] ?? null; @endphp
    <tr style="height:24px;">
      <td class="dc" style="font-size:8pt;">{{ $el['career_service'] ?? '' }}</td>
      <td class="dc" style="text-align:center; font-size:8pt;">{{ $el['rating'] ?? '' }}</td>
      <td class="dc" style="text-align:center; font-size:8pt;">{{ !empty($el['exam_date']??null) ? \Carbon\Carbon::parse($el['exam_date'])->format('m/d/Y') : '' }}</td>
      <td class="dc" style="font-size:8pt;">{{ $el['place'] ?? '' }}</td>
      <td class="dc" style="text-align:center; font-size:8pt;">{{ $el['license_no'] ?? '' }}</td>
      <td class="dc" style="text-align:center; font-size:8pt;">{{ !empty($el['validity']??null) ? \Carbon\Carbon::parse($el['validity'])->format('m/d/Y') : '' }}</td>
    </tr>
  @endfor
</table>
<div class="note" style="font-size:7.5pt;">(Continue on separate sheet if necessary)</div>

{{-- ════════════════════════════════════════════
     SECTION V — WORK EXPERIENCE
     ════════════════════════════════════════════ --}}
<div class="sec" style="margin-top:5px;">V. &nbsp; WORK EXPERIENCE</div>
<div style="font-size:7.5pt; font-style:italic; margin:3px 0;">(Include private employment. Start from your recent work) Description of duties should be indicated in the attached Work Experience sheet.</div>

<table>
  <colgroup>
    <col style="width:10%">
    <col style="width:10%">
    <col style="width:20%">
    <col style="width:22%">
    <col style="width:9%">
    <col style="width:11%">
    <col style="width:11%">
    <col style="width:7%">
  </colgroup>
  <tr>
    <th class="hc" colspan="2" style="font-size:8pt;">
      28. INCLUSIVE DATES<br><span style="font-weight:normal; font-size:7.5pt;">(mm/dd/yyyy)</span>
    </th>
    <th class="hc" rowspan="2" style="font-size:8pt;">POSITION TITLE<br><span style="font-weight:normal; font-size:7pt;">(Write in full/<br>Do not abbreviate)</span></th>
    <th class="hc" rowspan="2" style="font-size:8pt;">DEPARTMENT / AGENCY /<br>OFFICE / COMPANY<br><span style="font-weight:normal; font-size:7pt;">(Write in full/<br>Do not abbreviate)</span></th>
    <th class="hc" rowspan="2" style="font-size:8pt;">MONTHLY<br>SALARY</th>
    <th class="hc" rowspan="2" style="font-size:8pt;">SALARY/<br>JOB/<br>PAY GRADE<br><span style="font-weight:normal; font-size:7pt;">(if applicable)<br>&amp; STEP</span></th>
    <th class="hc" rowspan="2" style="font-size:8pt;">STATUS OF<br>APPOINTMENT</th>
    <th class="hc" rowspan="2" style="font-size:8pt;">GOV'T<br>SERVICE<br>(Y / N)</th>
  </tr>
  <tr>
    <th class="hc" style="font-size:8pt;">From</th>
    <th class="hc" style="font-size:8pt;">To</th>
  </tr>
  @php $work = $pds->work_experience ?? []; @endphp
  @for($i = 0; $i < 28; $i++)
    @php $w = $work[$i] ?? null; @endphp
    <tr style="height:20px;">
      <td class="dc" style="text-align:center; font-size:8pt;">{{ !empty($w['from']??null) ? \Carbon\Carbon::parse($w['from'])->format('m/d/Y') : '' }}</td>
      <td class="dc" style="text-align:center; font-size:8pt;">
        @if(!empty($w['from']??null))
          {{ !empty($w['to']??null) ? \Carbon\Carbon::parse($w['to'])->format('m/d/Y') : 'Present' }}
        @endif
      </td>
      <td class="dc" style="font-size:8pt;">{{ $w['position'] ?? '' }}</td>
      <td class="dc" style="font-size:8pt;">{{ $w['agency'] ?? '' }}</td>
      <td class="dc" style="text-align:right; font-size:8pt;">{{ $w['salary'] ?? '' }}</td>
      <td class="dc" style="text-align:center; font-size:8pt;">{{ $w['salary_grade'] ?? '' }}</td>
      <td class="dc" style="font-size:8pt;">{{ $w['status'] ?? '' }}</td>
      <td class="dc" style="text-align:center; font-size:8pt;">@isset($w['is_government']){{ $w['is_government'] ? 'Y' : 'N' }}@endisset</td>
    </tr>
  @endfor
</table>
<div class="note" style="font-size:7.5pt;">(Continue on separate sheet if necessary)</div>

<div class="footer">CS FORM 212 (Revised 2017), Page 2 of 4</div>
</div>
