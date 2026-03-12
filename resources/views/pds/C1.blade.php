{{-- resources/views/pds/C1.blade.php | Page 1 of 4 (Revised 2025) --}}
@php
  function chk(bool $checked, string $label): string {
      $mark = $checked ? '&#10003;' : '&nbsp;';
      return '<span class="ci"><span class="chkbox">'.$mark.'</span> '.$label.'</span>';
  }
@endphp

<div class="page">

{{-- ── HEADER ── --}}
<table style="margin-bottom:2px;">
  <tr>
    <td class="nb cs-label" style="width:14%; vertical-align:top;">CS Form No. 212<br>Revised 2025</td>
    <td class="nb" style="width:58%;"><div class="title">PERSONAL DATA SHEET</div></td>
    <td class="nb cs-label" style="width:14%; text-align:right; vertical-align:top;">Page 1 of 4</td>
    <td class="nb cs-label" style="width:14%; text-align:right; vertical-align:top; font-size:6pt;">
      1. CS ID No.<br><span style="font-size:5.5pt;">(Do not fill up. For CSC use only)</span>
    </td>
  </tr>
</table>

{{-- ── WARNINGS ── --}}
<div class="warn"><strong>WARNING:</strong> Any misrepresentation made in the Personal Data Sheet and the Work Experience Sheet shall cause the filing of administrative/criminal case/s against the person concerned.</div>
<div class="warn"><strong>READ THE ATTACHED GUIDE TO FILLING OUT THE PERSONAL DATA SHEET (PDS) BEFORE ACCOMPLISHING THE PDS FORM.</strong></div>
<div class="warn">Print legibly. Tick appropriate boxes ( &#x2713; ) and use separate sheet if necessary. Indicate N/A if not applicable. <strong>DO NOT ABBREVIATE.</strong></div>

<div class="sec">I. &nbsp; PERSONAL INFORMATION</div>

<table>
  <colgroup>
    <col style="width:2.5%">
    <col style="width:14%">
    <col style="width:12%">
    <col style="width:11%">
    <col style="width:16%">
    <col style="width:30%">
    <col style="width:14.5%">
  </colgroup>

  <tr style="height:16px;">
    <td class="num">2.</td>
    <td class="lc">SURNAME</td>
    <td class="dc" colspan="3" style="font-weight:bold;">{{ strtoupper($pds->surname ?? '') }}</td>
    <td class="dc">&nbsp;</td>
    <td class="dc" rowspan="6" style="text-align:center; vertical-align:top; padding:2px;">
      <div style="border:1px solid #888; width:64px; height:82px; margin:0 auto; font-size:5pt; line-height:1.4; display:flex; align-items:center; justify-content:center; text-align:center; color:#555;">
        Attach recent<br>passport-size<br>picture with<br>white background<br>and complete<br>name tag
      </div>
    </td>
  </tr>

  <tr style="height:16px;">
    <td class="num">&nbsp;</td>
    <td class="lc">FIRST NAME</td>
    <td class="dc" colspan="2" style="font-weight:bold;">{{ strtoupper($pds->first_name ?? '') }}</td>
    <td class="dc"><span class="sub">NAME EXTENSION (JR., SR)</span>{{ strtoupper($pds->name_extension ?? '') }}</td>
    <td class="dc">&nbsp;</td>
  </tr>

  <tr style="height:16px;">
    <td class="num">&nbsp;</td>
    <td class="lc">MIDDLE NAME</td>
    <td class="dc" colspan="3" style="font-weight:bold;">{{ strtoupper($pds->middle_name ?? '') }}</td>
    <td class="dc">&nbsp;</td>
  </tr>

  <tr style="height:16px;">
    <td class="num">3.</td>
    <td class="lc">DATE OF BIRTH<br><span class="sub">(dd/mm/yyyy)</span></td>
    <td class="dc" colspan="2">{{ optional($pds->date_of_birth)->format('d/m/Y') }}</td>
    <td class="lc">16. PLACE OF BIRTH</td>
    <td class="dc">{{ strtoupper($pds->place_of_birth ?? '') }}</td>
  </tr>

  <tr style="height:18px;">
    <td class="num">4.</td>
    <td class="lc">SEX AT BIRTH</td>
    <td class="dc" colspan="2">
      {!! '<span class="ci"><span class="chkbox">'.($pds->sex==='Male'?'&#10003;':'&nbsp;').'</span> Male</span>' !!}
      {!! '<span class="ci"><span class="chkbox">'.($pds->sex==='Female'?'&#10003;':'&nbsp;').'</span> Female</span>' !!}
    </td>
    <td class="lc">17. CIVIL STATUS</td>
    <td class="dc">
      @foreach(['Single','Married','Widowed','Separated','Others'] as $cs)
        {!! '<span class="ci"><span class="chkbox">'.( ($pds->civil_status??'')===$cs ? '&#10003;' : '&nbsp;' ).'</span> '.$cs.'</span>' !!}
      @endforeach
    </td>
  </tr>

  <tr style="height:18px;">
    <td class="num">5.</td>
    <td class="lc">HEIGHT (m)</td>
    <td class="dc" colspan="2">{{ $pds->height ?? '' }}</td>
    <td class="lc">18. CITIZENSHIP</td>
    <td class="dc">
      {!! '<span class="ci"><span class="chkbox">'.( ($pds->filipino??false) ? '&#10003;' : '&nbsp;' ).'</span> Filipino</span>' !!}
      {!! '<span class="ci"><span class="chkbox">'.( ($pds->dual_citizenship??false) ? '&#10003;' : '&nbsp;' ).'</span> Dual Citizenship</span>' !!}
      @if($pds->dual_citizenship ?? false)
        <span style="font-size:5.5pt; display:block;">Country: <strong>{{ $pds->dual_citizenship_country ?? '' }}</strong></span>
      @endif
    </td>
  </tr>
</table>

<table>
  <colgroup>
    <col style="width:2.5%">
    <col style="width:14%">
    <col style="width:18.5%">
    <col style="width:3%">
    <col style="width:21%">
    <col style="width:41%">
  </colgroup>

  <tr style="height:16px;">
    <td class="num">6.</td>
    <td class="lc">WEIGHT (kg)</td>
    <td class="dc">{{ $pds->weight ?? '' }}</td>
    <td class="lc" colspan="2">19. RESIDENTIAL ADDRESS</td>
    <td class="dc">&nbsp;</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">7.</td>
    <td class="lc">BLOOD TYPE</td>
    <td class="dc">{{ $pds->blood_type ?? '' }}</td>
    <td class="lc" colspan="2" style="font-size:6pt;">House/Block/Lot No.</td>
    <td class="dc">{{ $pds->res_house_block_lot_no ?? '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">8.</td>
    <td class="lc">UMID ID NO.</td>
    <td class="dc">{{ $pds->umid_id_no ?? '' }}</td>
    <td class="lc" colspan="2">Street</td>
    <td class="dc">{{ $pds->res_street ?? '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">9.</td>
    <td class="lc">ID NO.</td>
    <td class="dc">{{ $pds->id_no ?? '' }}</td>
    <td class="lc" colspan="2">Subdivision/Village</td>
    <td class="dc">{{ $pds->res_subdivision_village ?? '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">10.</td>
    <td class="lc">PHILHEALTH NO.</td>
    <td class="dc">{{ $pds->philhealth_no ?? '' }}</td>
    <td class="lc" colspan="2">Barangay</td>
    <td class="dc">{{ $pds->res_barangay ?? '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">11.</td>
    <td class="lc">SSS NO.</td>
    <td class="dc">{{ $pds->sss_no ?? '' }}</td>
    <td class="lc" colspan="2">City/Municipality</td>
    <td class="dc">{{ $pds->res_city_municipality ?? '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">12.</td>
    <td class="lc">TIN NO.</td>
    <td class="dc">{{ $pds->tin_no ?? '' }}</td>
    <td class="lc" colspan="2">Province</td>
    <td class="dc">{{ $pds->res_province ?? '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">13.</td>
    <td class="lc">PhilSys Number (PSN)</td>
    <td class="dc">{{ $pds->philsys_number ?? '' }}</td>
    <td class="lc" colspan="2"><strong>ZIP CODE</strong></td>
    <td class="dc">{{ $pds->res_zip_code ?? '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">14.</td>
    <td class="lc">AGENCY EMPLOYEE NO.</td>
    <td class="dc">{{ $pds->agency_employee_no ?? '' }}</td>
    <td class="lc" colspan="2">20. PERMANENT ADDRESS</td>
    <td class="dc">&nbsp;</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">15.</td>
    <td class="lc">TELEPHONE NO.</td>
    <td class="dc">{{ $pds->telephone_no ?? '' }}</td>
    <td class="lc" colspan="2" style="font-size:6pt;">House/Block/Lot No.</td>
    <td class="dc">{{ $pds->perm_house_block_lot_no ?? '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">16.</td>
    <td class="lc">MOBILE NO.</td>
    <td class="dc">{{ $pds->mobile ?? '' }}</td>
    <td class="lc" colspan="2">Street</td>
    <td class="dc">{{ $pds->perm_street ?? '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">17.</td>
    <td class="lc">E-MAIL ADDRESS<span class="sub">(if any)</span></td>
    <td class="dc">{{ $pds->email ?? '' }}</td>
    <td class="lc" colspan="2">Subdivision/Village</td>
    <td class="dc">{{ $pds->perm_subdivision_village ?? '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">&nbsp;</td><td class="lc">&nbsp;</td><td class="dc">&nbsp;</td>
    <td class="lc" colspan="2">Barangay</td>
    <td class="dc">{{ $pds->perm_barangay ?? '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">&nbsp;</td><td class="lc">&nbsp;</td><td class="dc">&nbsp;</td>
    <td class="lc" colspan="2">City/Municipality</td>
    <td class="dc">{{ $pds->perm_city_municipality ?? '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">&nbsp;</td><td class="lc">&nbsp;</td><td class="dc">&nbsp;</td>
    <td class="lc" colspan="2">Province</td>
    <td class="dc">{{ $pds->perm_province ?? '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">&nbsp;</td><td class="lc">&nbsp;</td><td class="dc">&nbsp;</td>
    <td class="lc" colspan="2"><strong>ZIP CODE</strong></td>
    <td class="dc">{{ $pds->perm_zip_code ?? '' }}</td>
  </tr>
</table>

<div class="sec">II. &nbsp; FAMILY BACKGROUND</div>

@php $children = $pds->children ?? []; @endphp

<table>
  <colgroup>
    <col style="width:2.5%">
    <col style="width:13.5%">
    <col style="width:17%">
    <col style="width:12%">
    <col style="width:3.5%">
    <col style="width:34%">
    <col style="width:17.5%">
  </colgroup>

  <tr style="height:16px;">
    <td class="num">22.</td>
    <td class="lc">SPOUSE'S SURNAME</td>
    <td class="dc" colspan="2" style="font-weight:bold;">{{ strtoupper($pds->spouse_surname ?? '') }}</td>
    <td class="hc" colspan="2">23. NAME OF CHILDREN (Write full name and list all)</td>
    <td class="hc">DATE OF BIRTH<br><span style="font-weight:normal; font-size:5.5pt;">(dd/mm/yyyy)</span></td>
  </tr>
  <tr style="height:16px;">
    <td class="num">&nbsp;</td>
    <td class="lc">FIRST NAME</td>
    <td class="dc" style="font-weight:bold;">{{ strtoupper($pds->spouse_first_name ?? '') }}</td>
    <td class="dc"><span class="sub">NAME EXTENSION (JR., SR)</span>{{ strtoupper($pds->spouse_name_extension ?? '') }}</td>
    <td class="dc" style="text-align:center;">1.</td>
    <td class="dc">{{ strtoupper($children[0]['name'] ?? '') }}</td>
    <td class="dc" style="text-align:center;">{{ !empty($children[0]['birthdate']??null) ? \Carbon\Carbon::parse($children[0]['birthdate'])->format('d/m/Y') : '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">&nbsp;</td>
    <td class="lc">MIDDLE NAME</td>
    <td class="dc" colspan="2" style="font-weight:bold;">{{ strtoupper($pds->spouse_middle_name ?? '') }}</td>
    <td class="dc" style="text-align:center;">2.</td>
    <td class="dc">{{ strtoupper($children[1]['name'] ?? '') }}</td>
    <td class="dc" style="text-align:center;">{{ !empty($children[1]['birthdate']??null) ? \Carbon\Carbon::parse($children[1]['birthdate'])->format('d/m/Y') : '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">&nbsp;</td>
    <td class="lc">OCCUPATION</td>
    <td class="dc" colspan="2">{{ $pds->spouse_occupation ?? '' }}</td>
    <td class="dc" style="text-align:center;">3.</td>
    <td class="dc">{{ strtoupper($children[2]['name'] ?? '') }}</td>
    <td class="dc" style="text-align:center;">{{ !empty($children[2]['birthdate']??null) ? \Carbon\Carbon::parse($children[2]['birthdate'])->format('d/m/Y') : '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">&nbsp;</td>
    <td class="lc">EMPLOYER/BUSINESS NAME</td>
    <td class="dc" colspan="2">{{ $pds->spouse_employer_business_name ?? '' }}</td>
    <td class="dc" style="text-align:center;">4.</td>
    <td class="dc">{{ strtoupper($children[3]['name'] ?? '') }}</td>
    <td class="dc" style="text-align:center;">{{ !empty($children[3]['birthdate']??null) ? \Carbon\Carbon::parse($children[3]['birthdate'])->format('d/m/Y') : '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">&nbsp;</td>
    <td class="lc">BUSINESS ADDRESS</td>
    <td class="dc" colspan="2">{{ $pds->spouse_business_address ?? '' }}</td>
    <td class="dc" style="text-align:center;">5.</td>
    <td class="dc">{{ strtoupper($children[4]['name'] ?? '') }}</td>
    <td class="dc" style="text-align:center;">{{ !empty($children[4]['birthdate']??null) ? \Carbon\Carbon::parse($children[4]['birthdate'])->format('d/m/Y') : '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">&nbsp;</td>
    <td class="lc">TELEPHONE NO.</td>
    <td class="dc" colspan="2">{{ $pds->spouse_telephone_no ?? '' }}</td>
    <td class="dc" style="text-align:center;">6.</td>
    <td class="dc">{{ strtoupper($children[5]['name'] ?? '') }}</td>
    <td class="dc" style="text-align:center;">{{ !empty($children[5]['birthdate']??null) ? \Carbon\Carbon::parse($children[5]['birthdate'])->format('d/m/Y') : '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">24.</td>
    <td class="lc">FATHER'S SURNAME</td>
    <td class="dc" colspan="2" style="font-weight:bold;">{{ strtoupper($pds->father_surname ?? '') }}</td>
    <td class="dc" style="text-align:center;">7.</td>
    <td class="dc">{{ strtoupper($children[6]['name'] ?? '') }}</td>
    <td class="dc" style="text-align:center;">{{ !empty($children[6]['birthdate']??null) ? \Carbon\Carbon::parse($children[6]['birthdate'])->format('d/m/Y') : '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">&nbsp;</td>
    <td class="lc">FIRST NAME</td>
    <td class="dc" style="font-weight:bold;">{{ strtoupper($pds->father_first_name ?? '') }}</td>
    <td class="dc"><span class="sub">NAME EXTENSION (JR., SR)</span>{{ strtoupper($pds->father_name_extension ?? '') }}</td>
    <td class="dc" style="text-align:center;">8.</td>
    <td class="dc">{{ strtoupper($children[7]['name'] ?? '') }}</td>
    <td class="dc" style="text-align:center;">{{ !empty($children[7]['birthdate']??null) ? \Carbon\Carbon::parse($children[7]['birthdate'])->format('d/m/Y') : '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">&nbsp;</td>
    <td class="lc">MIDDLE NAME</td>
    <td class="dc" colspan="2" style="font-weight:bold;">{{ strtoupper($pds->father_middle_name ?? '') }}</td>
    <td class="dc" style="text-align:center;">9.</td>
    <td class="dc">{{ strtoupper($children[8]['name'] ?? '') }}</td>
    <td class="dc" style="text-align:center;">{{ !empty($children[8]['birthdate']??null) ? \Carbon\Carbon::parse($children[8]['birthdate'])->format('d/m/Y') : '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">25.</td>
    <td class="lc">MOTHER'S MAIDEN NAME</td>
    <td class="dc" colspan="2">&nbsp;</td>
    <td class="dc" style="text-align:center;">10.</td>
    <td class="dc">{{ strtoupper($children[9]['name'] ?? '') }}</td>
    <td class="dc" style="text-align:center;">{{ !empty($children[9]['birthdate']??null) ? \Carbon\Carbon::parse($children[9]['birthdate'])->format('d/m/Y') : '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">&nbsp;</td>
    <td class="lc">SURNAME</td>
    <td class="dc" colspan="2" style="font-weight:bold;">{{ strtoupper($pds->mother_surname ?? '') }}</td>
    <td class="dc" style="text-align:center;">11.</td>
    <td class="dc">{{ strtoupper($children[10]['name'] ?? '') }}</td>
    <td class="dc" style="text-align:center;">{{ !empty($children[10]['birthdate']??null) ? \Carbon\Carbon::parse($children[10]['birthdate'])->format('d/m/Y') : '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">&nbsp;</td>
    <td class="lc">FIRST NAME</td>
    <td class="dc" colspan="2" style="font-weight:bold;">{{ strtoupper($pds->mother_first_name ?? '') }}</td>
    <td class="dc" style="text-align:center;">12.</td>
    <td class="dc">{{ strtoupper($children[11]['name'] ?? '') }}</td>
    <td class="dc" style="text-align:center;">{{ !empty($children[11]['birthdate']??null) ? \Carbon\Carbon::parse($children[11]['birthdate'])->format('d/m/Y') : '' }}</td>
  </tr>
  <tr style="height:16px;">
    <td class="num">&nbsp;</td>
    <td class="lc">MIDDLE NAME</td>
    <td class="dc" colspan="2" style="font-weight:bold;">{{ strtoupper($pds->mother_middle_name ?? '') }}</td>
    <td class="dc" colspan="3" style="font-style:italic; font-size:6pt; text-align:center;">(Continue on separate sheet if necessary)</td>
  </tr>
</table>

<div class="sec">III. &nbsp; EDUCATIONAL BACKGROUND</div>

<table>
  <tr>
    <th class="hc" rowspan="2" style="width:11%;">26. LEVEL</th>
    <th class="hc" rowspan="2" style="width:22%;">NAME OF SCHOOL<br><span style="font-weight:normal; font-size:5.5pt;">(Write in full)</span></th>
    <th class="hc" rowspan="2" style="width:22%;">BASIC EDUCATION/DEGREE/COURSE<br><span style="font-weight:normal; font-size:5.5pt;">(Write in full)</span></th>
    <th class="hc" colspan="2" style="width:14%;">PERIOD OF ATTENDANCE</th>
    <th class="hc" rowspan="2" style="width:12%;">HIGHEST LEVEL/<br>UNITS EARNED<br><span style="font-weight:normal; font-size:5.5pt;">(if not graduated)</span></th>
    <th class="hc" rowspan="2" style="width:10%;">YEAR<br>GRADUATED</th>
    <th class="hc" rowspan="2" style="width:9%;">SCHOLARSHIP/<br>ACADEMIC<br>HONORS RECEIVED</th>
  </tr>
  <tr>
    <th class="hc" style="width:7%; font-size:6pt;">From</th>
    <th class="hc" style="width:7%; font-size:6pt;">To</th>
  </tr>
  @php
    $education = $pds->education ?? [];
    $eduLevels = ['ELEMENTARY','SECONDARY','VOCATIONAL / TRADE COURSE','COLLEGE','GRADUATE STUDIES'];
  @endphp
  @foreach($eduLevels as $lvl)
    @php $e = collect($education)->firstWhere('level', $lvl); @endphp
    <tr style="height:24px;">
      <td class="lc" style="font-size:6.5pt; text-align:center; white-space:normal; line-height:1.2;">{{ $lvl }}</td>
      <td class="dc">{{ $e['school_name'] ?? '' }}</td>
      <td class="dc">{{ $e['degree'] ?? '' }}</td>
      <td class="dc" style="text-align:center;">{{ $e['from_year'] ?? '' }}</td>
      <td class="dc" style="text-align:center;">{{ $e['to_year'] ?? '' }}</td>
      <td class="dc" style="text-align:center;">{{ $e['units_earned'] ?? '' }}</td>
      <td class="dc" style="text-align:center;">{{ $e['year_graduated'] ?? '' }}</td>
      <td class="dc">{{ $e['honors'] ?? '' }}</td>
    </tr>
  @endforeach
</table>
<div class="note">(Continue on separate sheet if necessary)</div>

<table style="margin-top:4px;">
  <tr style="height:20px;">
    <td class="lc" style="width:28%; font-weight:bold; padding-left:6px;">SIGNATURE</td>
    <td class="dc" style="width:40%;">&nbsp;</td>
    <td class="lc" style="width:10%; font-weight:bold; text-align:center;">DATE</td>
    <td class="dc" style="width:22%;">&nbsp;</td>
  </tr>
</table>

<div class="footer">CS FORM 212 (Revised 2025), Page 1 of 4</div>
</div>
