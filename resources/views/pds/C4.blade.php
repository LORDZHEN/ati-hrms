{{-- resources/views/pds/C4.blade.php | Page 4 of 4 (Revised 2025) --}}
@php
  function yesno(bool $val): string {
      $yMark = $val  ? '&#10003;' : '&nbsp;';
      $nMark = !$val ? '&#10003;' : '&nbsp;';
      return '<span class="ci"><span class="chkbox">'.$yMark.'</span> Yes</span>'
           . '<span class="ci"><span class="chkbox">'.$nMark.'</span> No</span>';
  }
@endphp

<div class="page">

<table style="margin-bottom:2px;">
  <tr>
    <td class="nb cs-label" style="width:14%; vertical-align:top;">CS Form No. 212<br>Revised 2025</td>
    <td class="nb" style="width:72%;"><div class="title">PERSONAL DATA SHEET</div></td>
    <td class="nb cs-label" style="width:14%; text-align:right; vertical-align:top;">Page 4 of 4</td>
  </tr>
</table>

<div class="sec">ANSWER THE FOLLOWING QUESTIONS</div>

<table>
  <colgroup>
    <col style="width:72%">
    <col style="width:28%">
  </colgroup>

  <tr>
    <td class="lc" colspan="2" style="font-weight:normal; padding:5px 7px;">
      <strong>34.</strong> Are you related by consanguinity or affinity to the appointing or recommending authority, or to the chief of bureau or office or to the person who has immediate supervision over you in the Office, Bureau or Department where you will be appointed,
    </td>
  </tr>
  <tr style="height:26px;">
    <td class="dc" style="padding-left:20px;">a. within the third degree?</td>
    <td class="dc" style="padding:3px 8px;">
      {!! yesno((bool)($pds->related_third_degree ?? false)) !!}
      @if($pds->related_third_degree ?? false)
        <div style="font-size:6.5pt; margin-top:2px;">{{ $pds->related_third_degree_details ?? '' }}</div>
      @endif
    </td>
  </tr>
  <tr style="height:26px;">
    <td class="dc" style="padding-left:20px;">b. within the fourth degree (for Local Government Unit - Career Employees)?</td>
    <td class="dc" style="padding:3px 8px;">
      {!! yesno((bool)($pds->related_fourth_degree ?? false)) !!}
      @if($pds->related_fourth_degree ?? false)
        <div style="font-size:6.5pt; margin-top:2px;">{{ $pds->related_fourth_degree_details ?? '' }}</div>
      @endif
    </td>
  </tr>

  <tr style="height:26px;">
    <td class="dc" style="padding:4px 7px;"><strong>35.</strong> a. Have you ever been found guilty of any administrative offense?</td>
    <td class="dc" style="padding:3px 8px;">
      {!! yesno((bool)($pds->has_admin_case ?? false)) !!}
      @if($pds->has_admin_case ?? false)
        <div style="font-size:6.5pt;">{{ $pds->admin_case_details ?? '' }}</div>
      @endif
    </td>
  </tr>
  <tr style="height:26px;">
    <td class="dc" style="padding-left:32px;">b. Have you been criminally charged before any court?</td>
    <td class="dc" style="padding:3px 8px;">
      {!! yesno((bool)($pds->has_criminal_case ?? false)) !!}
      @if($pds->has_criminal_case ?? false)
        <div style="font-size:6.5pt;">Date Filed: {{ optional($pds->criminal_case_date_filed)->format('d/m/Y') }} &nbsp; Status: {{ $pds->criminal_case_status ?? '' }}</div>
      @endif
    </td>
  </tr>

  <tr style="height:32px;">
    <td class="dc" style="padding:4px 7px;"><strong>36.</strong> Have you ever been convicted of any crime or violation of any law, decree, ordinance or regulation by any court or tribunal?</td>
    <td class="dc" style="padding:3px 8px;">
      {!! yesno((bool)($pds->has_conviction ?? false)) !!}
      @if($pds->has_conviction ?? false)<div style="font-size:6.5pt;">{{ $pds->conviction_details ?? '' }}</div>@endif
    </td>
  </tr>

  <tr style="height:40px;">
    <td class="dc" style="padding:4px 7px;"><strong>37.</strong> Have you ever been separated from the service in any of the following modes: resignation, retirement, dropped from the rolls, dismissal, termination, end of term, finished contract or phased out (abolition) in the public or private sector?</td>
    <td class="dc" style="padding:3px 8px;">
      {!! yesno((bool)($pds->has_been_separated ?? false)) !!}
      @if($pds->has_been_separated ?? false)<div style="font-size:6.5pt;">{{ $pds->separation_details ?? '' }}</div>@endif
    </td>
  </tr>

  <tr style="height:32px;">
    <td class="dc" style="padding:4px 7px;"><strong>38.</strong> Have you ever been a candidate in a national or local election held within the last year (except Barangay election)?</td>
    <td class="dc" style="padding:3px 8px;">
      {!! yesno((bool)($pds->has_election_candidacy ?? false)) !!}
      @if($pds->has_election_candidacy ?? false)<div style="font-size:6.5pt;">{{ $pds->election_candidacy_details ?? '' }}</div>@endif
    </td>
  </tr>

  <tr style="height:32px;">
    <td class="dc" style="padding:4px 7px;"><strong>39.</strong> Have you resigned from the government service during the three (3)-year period from June 10, 1987 to November 2, 1990?</td>
    <td class="dc" style="padding:3px 8px;">
      {!! yesno((bool)($pds->resigned_gov_service ?? false)) !!}
      @if($pds->resigned_gov_service ?? false)<div style="font-size:6.5pt;">{{ $pds->resigned_gov_service_details ?? '' }}</div>@endif
    </td>
  </tr>

  <tr>
    <td class="lc" colspan="2" style="font-weight:normal; padding:5px 7px;">
      <strong>40.</strong> Pursuant to: (a) Indigenous People's Act (RA 8371); (b) Magna Carta for Disabled Persons (RA 7277); and (c) Solo Parents Welfare Act of 2000 (RA 8972), please answer the following items:
    </td>
  </tr>
  <tr style="height:26px;">
    <td class="dc" style="padding-left:20px;">a. Are you a member of any indigenous group?</td>
    <td class="dc" style="padding:3px 8px;">
      {!! yesno((bool)($pds->is_indigenous ?? false)) !!}
      @if($pds->is_indigenous ?? false)<div style="font-size:6.5pt;">{{ $pds->indigenous_details ?? '' }}</div>@endif
    </td>
  </tr>
  <tr style="height:26px;">
    <td class="dc" style="padding-left:20px;">b. Are you a person with disability?</td>
    <td class="dc" style="padding:3px 8px;">
      {!! yesno((bool)($pds->has_disability ?? false)) !!}
      @if($pds->has_disability ?? false)<div style="font-size:6.5pt;">{{ $pds->disability_details ?? '' }}</div>@endif
    </td>
  </tr>
  <tr style="height:26px;">
    <td class="dc" style="padding-left:20px;">c. Are you a solo parent?</td>
    <td class="dc" style="padding:3px 8px;">
      {!! yesno((bool)($pds->is_solo_parent ?? false)) !!}
      @if($pds->is_solo_parent ?? false)<div style="font-size:6.5pt;">{{ $pds->solo_parent_details ?? '' }}</div>@endif
    </td>
  </tr>
</table>

<div class="sec" style="margin-top:5px;">
  41. &nbsp; REFERENCES &nbsp;
  <span style="font-weight:normal; font-size:7pt;">(Person not related by consanguinity or affinity to applicant/appointee)</span>
</div>

<table>
  <colgroup>
    <col style="width:34%">
    <col style="width:46%">
    <col style="width:20%">
  </colgroup>
  <tr style="height:20px;">
    <th class="hc">NAME</th>
    <th class="hc">ADDRESS</th>
    <th class="hc">TEL. NO.</th>
  </tr>
  @php $refs = $pds->references ?? []; @endphp
  @for($i = 0; $i < 3; $i++)
    @php $ref = $refs[$i] ?? null; @endphp
    <tr style="height:26px;">
      <td class="dc" style="font-weight:bold;">{{ strtoupper($ref['name'] ?? '') }}</td>
      <td class="dc">{{ $ref['address'] ?? '' }}</td>
      <td class="dc">{{ $ref['tel'] ?? '' }}</td>
    </tr>
  @endfor
</table>

<div class="sec" style="margin-top:5px;">
  42. &nbsp; GOVERNMENT ISSUED ID
  <span style="font-weight:normal; font-size:7pt;">(i.e. Passport, GSIS, SSS, PRC, Driver's License, etc.) Please indicate ID Number and Date of Issuance</span>
</div>

<table>
  <colgroup>
    <col style="width:30%">
    <col style="width:70%">
  </colgroup>
  <tr style="height:26px;"><td class="lc">Government Issued ID</td><td class="dc">{{ $pds->gov_id_type ?? '' }}</td></tr>
  <tr style="height:26px;"><td class="lc">ID/License/Passport No.</td><td class="dc">{{ $pds->gov_id_no ?? '' }}</td></tr>
  <tr style="height:26px;"><td class="lc">Date/Place of Issuance</td><td class="dc">{{ $pds->gov_id_issued ?? '' }}</td></tr>
</table>

<table style="margin-top:5px;">
  <tr>
    <td style="border:1px solid #000; padding:8px 10px; line-height:1.6; text-align:justify;">
      I declare under oath that I have personally accomplished this Personal Data Sheet which is a true, correct and complete statement pursuant to the provisions of pertinent laws, rules and regulations of the Republic of the Philippines. I authorize the agency head/authorized representative to verify/validate the contents stated herein. I agree that any misrepresentation made in this document and its attachments shall cause the filing of administrative/criminal case/s against me.
    </td>
  </tr>
</table>

<table style="margin-top:5px; border-collapse:collapse;">
  <colgroup>
    <col style="width:55%">
    <col style="width:45%">
  </colgroup>
  <tr>
    <td style="border:1px solid #000; padding:8px 10px; vertical-align:top; height:90px;">
      <div style="margin-bottom:46px;">Signature (Sign inside the box)</div>
      <div style="border-top:1px solid #000; padding-top:3px;">
        Date Accomplished: {{ optional($pds->date_accomplished)->format('d/m/Y') }}
      </div>
    </td>
    <td style="border:1px solid #000; padding:8px 10px; text-align:center; vertical-align:middle; height:90px;">
      Right Thumbmark
    </td>
  </tr>
</table>

<table style="margin-top:5px; border-collapse:collapse;">
  <colgroup>
    <col style="width:57%">
    <col style="width:43%">
  </colgroup>
  <tr>
    <td style="border:none; line-height:2.2; vertical-align:top; padding-right:8px;">
      SUBSCRIBED AND SWORN to before me this
      <span style="display:inline-block; min-width:30px; border-bottom:1px solid #000; text-align:center;">{{ optional($pds->oath_date)->format('d') }}</span>
      day of
      <span style="display:inline-block; min-width:65px; border-bottom:1px solid #000; text-align:center;">{{ optional($pds->oath_date)->format('F') }}</span>,
      <span style="display:inline-block; min-width:38px; border-bottom:1px solid #000; text-align:center;">{{ optional($pds->oath_date)->format('Y') }}</span>
      at <span style="display:inline-block; min-width:82px; border-bottom:1px solid #000;"></span>, Philippines.
      <br>
      Administering Officer <span style="display:inline-block; min-width:138px; border-bottom:1px solid #000;"></span>
      <br>
      Position/Title/Designation <span style="display:inline-block; min-width:120px; border-bottom:1px solid #000;"></span>
      <br>
      Dept./Agency/Office/LGU <span style="display:inline-block; min-width:124px; border-bottom:1px solid #000;"></span>
    </td>
    <td style="border:1px solid #000; text-align:center; vertical-align:middle; height:90px; padding:8px;">
      Person Administering Oath
    </td>
  </tr>
</table>

<div class="footer" style="margin-top:5px;">CS FORM 212 (Revised 2025), Page 4 of 4</div>
</div>
