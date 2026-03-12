{{-- resources/views/pds/C2.blade.php | Page 2 of 4 (Revised 2025) --}}
<div class="page">

<table style="margin-bottom:2px;">
  <tr>
    <td class="nb cs-label" style="width:14%; vertical-align:top;">CS Form No. 212<br>Revised 2025</td>
    <td class="nb" style="width:72%;"><div class="title">PERSONAL DATA SHEET</div></td>
    <td class="nb cs-label" style="width:14%; text-align:right; vertical-align:top;">Page 2 of 4</td>
  </tr>
</table>

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
    <th class="hc" rowspan="2" style="text-align:left; padding:2px 5px;">
      27. CAREER SERVICE/ RA 1080 (BOARD/ BAR) UNDER SPECIAL LAWS/ CES/ CSEE<br>
      BARANGAY ELIGIBILITY / DRIVER'S LICENSE
    </th>
    <th class="hc" rowspan="2">RATING<br><span style="font-weight:normal; font-size:6pt;">(If Applicable)</span></th>
    <th class="hc" rowspan="2">DATE OF<br>EXAMINATION /<br>CONFERMENT<br><span style="font-weight:normal; font-size:6pt;">(dd/mm/yyyy)</span></th>
    <th class="hc" rowspan="2">PLACE OF EXAMINATION /<br>CONFERMENT</th>
    <th class="hc" colspan="2">LICENSE (if applicable)</th>
  </tr>
  <tr>
    <th class="hc">NUMBER</th>
    <th class="hc">Date of Validity</th>
  </tr>
  @php $elig = $pds->civil_service_eligibility ?? []; @endphp
  @for($i = 0; $i < 7; $i++)
    @php $el = $elig[$i] ?? null; @endphp
    <tr style="height:20px;">
      <td class="dc">{{ $el['career_service'] ?? '' }}</td>
      <td class="dc" style="text-align:center;">{{ $el['rating'] ?? '' }}</td>
      <td class="dc" style="text-align:center;">{{ !empty($el['exam_date']??null) ? \Carbon\Carbon::parse($el['exam_date'])->format('d/m/Y') : '' }}</td>
      <td class="dc">{{ $el['place'] ?? '' }}</td>
      <td class="dc" style="text-align:center;">{{ $el['license_no'] ?? '' }}</td>
      <td class="dc" style="text-align:center;">{{ !empty($el['validity']??null) ? \Carbon\Carbon::parse($el['validity'])->format('d/m/Y') : '' }}</td>
    </tr>
  @endfor
</table>
<div class="note">(Continue on separate sheet if necessary)</div>

<div class="sec" style="margin-top:3px;">V. &nbsp; WORK EXPERIENCE</div>
<div style="font-size:6.5pt; font-style:italic; margin:2px 0;">(Include private employment. Start from your recent work) Description of duties should be indicated in the attached Work Experience sheet.</div>

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
    <th class="hc" colspan="2">
      28. INCLUSIVE DATES<br><span style="font-weight:normal; font-size:6pt;">(dd/mm/yyyy)</span>
    </th>
    <th class="hc" rowspan="2">POSITION TITLE<br><span style="font-weight:normal; font-size:6pt;">(Write in full/<br>Do not abbreviate)</span></th>
    <th class="hc" rowspan="2">DEPARTMENT / AGENCY /<br>OFFICE / COMPANY<br><span style="font-weight:normal; font-size:6pt;">(Write in full/<br>Do not abbreviate)</span></th>
    <th class="hc" rowspan="2">MONTHLY<br>SALARY</th>
    <th class="hc" rowspan="2">SALARY/<br>JOB/<br>PAY GRADE<br><span style="font-weight:normal; font-size:6pt;">(if applicable)<br>&amp; STEP</span></th>
    <th class="hc" rowspan="2">STATUS OF<br>APPOINTMENT</th>
    <th class="hc" rowspan="2">GOV'T<br>SERVICE<br>(Y / N)</th>
  </tr>
  <tr>
    <th class="hc">From</th>
    <th class="hc">To</th>
  </tr>
  @php $work = $pds->work_experience ?? []; @endphp
  @for($i = 0; $i < 28; $i++)
    @php $w = $work[$i] ?? null; @endphp
    <tr style="height:21px;">
      <td class="dc" style="text-align:center;">{{ !empty($w['from']??null) ? \Carbon\Carbon::parse($w['from'])->format('d/m/Y') : '' }}</td>
      <td class="dc" style="text-align:center;">
        @if(!empty($w['from']??null))
          {{ !empty($w['to']??null) ? \Carbon\Carbon::parse($w['to'])->format('d/m/Y') : 'Present' }}
        @endif
      </td>
      <td class="dc">{{ $w['position'] ?? '' }}</td>
      <td class="dc">{{ $w['agency'] ?? '' }}</td>
      <td class="dc" style="text-align:right;">{{ $w['salary'] ?? '' }}</td>
      <td class="dc" style="text-align:center;">{{ $w['salary_grade'] ?? '' }}</td>
      <td class="dc">{{ $w['status'] ?? '' }}</td>
      <td class="dc" style="text-align:center;">@isset($w['is_government']){{ $w['is_government'] ? 'Y' : 'N' }}@endisset</td>
    </tr>
  @endfor
</table>
<div class="note">(Continue on separate sheet if necessary)</div>

<div class="footer">CS FORM 212 (Revised 2025), Page 2 of 4</div>
</div>
