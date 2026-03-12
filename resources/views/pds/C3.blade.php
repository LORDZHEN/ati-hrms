{{-- resources/views/pds/C3.blade.php | Page 3 of 4 (Revised 2025) --}}
<div class="page">

<table style="margin-bottom:2px;">
  <tr>
    <td class="nb cs-label" style="width:14%; vertical-align:top;">CS Form No. 212<br>Revised 2025</td>
    <td class="nb" style="width:72%;"><div class="title">PERSONAL DATA SHEET</div></td>
    <td class="nb cs-label" style="width:14%; text-align:right; vertical-align:top;">Page 3 of 4</td>
  </tr>
</table>

<div class="sec" style="margin-top:0;">VI. &nbsp; VOLUNTARY WORK OR INVOLVEMENT IN CIVIC / NON-GOVERNMENT / PEOPLE / VOLUNTARY ORGANIZATION/S</div>

<table style="table-layout:fixed; width:100%;">
  <colgroup>
    <col style="width:38%">
    <col style="width:12%">
    <col style="width:12%">
    <col style="width:11%">
    <col style="width:27%">
  </colgroup>
  <tr>
    <th class="hc" rowspan="2" style="text-align:left; padding:2px 4px;">
      29. NAME &amp; ADDRESS OF ORGANIZATION <span style="font-weight:normal;">(Write in full)</span>
    </th>
    <th class="hc" colspan="2">INCLUSIVE DATES<br><span style="font-weight:normal; font-size:6pt;">(dd/mm/yyyy)</span></th>
    <th class="hc" rowspan="2">NUMBER<br>OF HOURS</th>
    <th class="hc" rowspan="2">POSITION / NATURE OF WORK</th>
  </tr>
  <tr>
    <th class="hc">From</th>
    <th class="hc">To</th>
  </tr>
  @php $vw_list = $pds->voluntary_work ?? []; @endphp
  @for($i = 0; $i < 7; $i++)
    @php $vw = $vw_list[$i] ?? null; @endphp
    <tr style="height:24px;">
      <td class="dc">{{ $vw['organization_name'] ?? '' }}</td>
      <td class="dc" style="text-align:center;">{{ !empty($vw['from_date']??null) ? \Carbon\Carbon::parse($vw['from_date'])->format('d/m/Y') : '' }}</td>
      <td class="dc" style="text-align:center;">{{ !empty($vw['to_date']??null) ? \Carbon\Carbon::parse($vw['to_date'])->format('d/m/Y') : '' }}</td>
      <td class="dc" style="text-align:center;">{{ $vw['hours'] ?? '' }}</td>
      <td class="dc">{{ $vw['position'] ?? '' }}</td>
    </tr>
  @endfor
</table>
<div class="note">(Continue on separate sheet if necessary)</div>

<div class="sec" style="margin-top:3px;">VII. &nbsp; LEARNING AND DEVELOPMENT (L&amp;D) INTERVENTIONS/TRAINING PROGRAMS ATTENDED</div>
<div style="font-size:6.5pt; font-style:italic; margin:2px 0;">(Start from the most recent L&amp;D/training program and include only the relevant L&amp;D/training taken for the last five (5) years for Division Chief/Executive/Managerial positions)</div>

<table style="table-layout:fixed; width:100%;">
  <colgroup>
    <col style="width:35%">
    <col style="width:11%">
    <col style="width:11%">
    <col style="width:10%">
    <col style="width:13%">
    <col style="width:20%">
  </colgroup>
  <tr>
    <th class="hc" rowspan="2" style="text-align:left; padding:2px 4px;">
      30. TITLE OF LEARNING AND DEVELOPMENT INTERVENTIONS/TRAINING PROGRAMS<br>
      <span style="font-weight:normal; font-size:6pt;">(Write in full)</span>
    </th>
    <th class="hc" colspan="2">INCLUSIVE DATES<br><span style="font-weight:normal; font-size:6pt;">(dd/mm/yyyy)</span></th>
    <th class="hc" rowspan="2">NUMBER<br>OF HOURS</th>
    <th class="hc" rowspan="2">Type of LD<br><span style="font-weight:normal; font-size:6pt;">(Managerial/<br>Supervisory/<br>Technical/etc)</span></th>
    <th class="hc" rowspan="2">CONDUCTED/<br>SPONSORED BY<br><span style="font-weight:normal; font-size:6pt;">(Write in full)</span></th>
  </tr>
  <tr>
    <th class="hc">From</th>
    <th class="hc">To</th>
  </tr>
  @php $ld_list = $pds->learning_development ?? []; @endphp
  @for($i = 0; $i < 21; $i++)
    @php $ld = $ld_list[$i] ?? null; @endphp
    <tr style="height:23px;">
      <td class="dc">{{ $ld['training_title'] ?? '' }}</td>
      <td class="dc" style="text-align:center;">{{ !empty($ld['from_date']??null) ? \Carbon\Carbon::parse($ld['from_date'])->format('d/m/Y') : '' }}</td>
      <td class="dc" style="text-align:center;">{{ !empty($ld['to_date']??null) ? \Carbon\Carbon::parse($ld['to_date'])->format('d/m/Y') : '' }}</td>
      <td class="dc" style="text-align:center;">{{ $ld['hours'] ?? '' }}</td>
      <td class="dc">{{ $ld['type'] ?? '' }}</td>
      <td class="dc">{{ $ld['conducted_by'] ?? '' }}</td>
    </tr>
  @endfor
</table>
<div class="note">(Continue on separate sheet if necessary)</div>

<div class="sec" style="margin-top:3px;">VIII. &nbsp; OTHER INFORMATION</div>

<table style="table-layout:fixed; width:100%;">
  <tr>
    <th class="hc" style="width:33.33%;">31. SPECIAL SKILLS and HOBBIES</th>
    <th class="hc" style="width:33.33%;">32. NON-ACADEMIC DISTINCTIONS / RECOGNITION<br><span style="font-weight:normal; font-size:6pt;">(Write in full)</span></th>
    <th class="hc" style="width:33.33%;">33. MEMBERSHIP IN ASSOCIATION/ORGANIZATION<br><span style="font-weight:normal; font-size:6pt;">(Write in full)</span></th>
  </tr>
  @php
    $skills       = $pds->special_skills ?? [];
    $distinctions = $pds->non_academic_distinctions ?? [];
    $memberships  = $pds->membership_association ?? [];
    $maxRows      = max(count($skills), count($distinctions), count($memberships), 7);
  @endphp
  @for($i = 0; $i < $maxRows; $i++)
    <tr style="height:24px;">
      <td class="dc">{{ $skills[$i]['skill'] ?? '' }}</td>
      <td class="dc">{{ $distinctions[$i]['distinction'] ?? '' }}</td>
      <td class="dc">{{ $memberships[$i]['organization'] ?? '' }}</td>
    </tr>
  @endfor
</table>
<div class="note">(Continue on separate sheet if necessary)</div>

<div class="footer">CS FORM 212 (Revised 2025), Page 3 of 4</div>
</div>
