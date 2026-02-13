{{-- resources/views/pds/C4.blade.php --}}
<style>
    .pds-container {
        font-family: Arial, sans-serif;
        font-size: 9pt;
        line-height: 1.2;
        color: #000;
        max-width: 8.5in;
        margin: 0 auto;
        padding: 0.5in;
    }

    .pds-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }

    .pds-table td,
    .pds-table th {
        border: 1px solid #000;
        padding: 2px 4px;
        vertical-align: top;
        font-size: 8pt;
    }

    .pds-table th {
        background-color: #d9d9d9;
        font-weight: bold;
        text-align: center;
    }

    .label-cell {
        background-color: #d9d9d9;
        font-weight: normal;
        font-size: 7pt;
    }

    .data-cell {
        font-size: 7pt;
        min-height: 18px;
    }

    .header-row {
        background-color: #d9d9d9;
        font-weight: bold;
        text-align: center;
        font-size: 7pt;
        padding: 4px;
    }

    .checkbox-group {
        display: inline-block;
        margin-right: 10px;
    }

    .checkbox {
        display: inline-block;
        width: 12px;
        height: 12px;
        border: 1px solid #000;
        text-align: center;
        line-height: 12px;
        margin-right: 3px;
        vertical-align: middle;
    }

    .section-title {
        background-color: #969696;
        color: #fff;
        font-weight: bold;
        font-size: 9pt;
        padding: 3px 5px;
        margin: 8px 0 2px 0;
    }

    .note-text {
        font-size: 7pt;
        font-style: italic;
        text-align: center;
        margin: 3px 0;
    }

    .page-break {
        page-break-after: always;
    }

    .signature-box {
        border: 1px solid #000;
        min-height: 60px;
        padding: 5px;
        text-align: center;
        font-size: 7pt;
    }
</style>

<div class="pds-container page-break">
    {{-- PAGE HEADER --}}
    <table style="width:100%; margin-bottom: 5px; border: none;">
        <tr>
            <td style="width:20%; font-size:8pt; border: none;">
                <em>CS Form No. 212</em><br>
                <em>Revised 2017</em>
            </td>
            <td style="width:60%; text-align:center; border: none;">
                <div style="font-size:11pt; font-weight:bold;">PERSONAL DATA SHEET</div>
            </td>
            <td style="width:20%; text-align:right; font-size:8pt; border: none;">
                <em>Page 4 of 4</em>
            </td>
        </tr>
    </table>

    {{-- QUESTIONS 34-40 --}}
    <table class="pds-table">
        <tr>
            <td colspan="5" class="label-cell" style="font-size:7pt; padding:3px;">
                <strong>34.</strong> Are you related by consanguinity or affinity to the appointing or recommending authority, or to the
                chief of bureau or office or to the person who has immediate supervision over you in the Office,
                Bureau or Department where you will be appointed,
            </td>
        </tr>
        <tr>
            <td style="width:50%; font-size:7pt; padding:3px;">
                a. within the third degree?
            </td>
            <td style="width:10%; text-align:center;">
                <div class="checkbox-group">
                    <span class="checkbox">{{ ($pds->related_third_degree ?? false) ? '✓' : '' }}</span> YES
                </div>
                <div class="checkbox-group">
                    <span class="checkbox">{{ !($pds->related_third_degree ?? false) ? '✓' : '' }}</span> NO
                </div>
            </td>
            <td colspan="3" style="width:40%; font-size:7pt;">
                {{ $pds->related_third_degree_details ?? '' }}
            </td>
        </tr>
        <tr>
            <td style="font-size:7pt; padding:3px;">
                b. within the fourth degree (for Local Government Unit - Career Employees)?
            </td>
            <td style="text-align:center;">
                <div class="checkbox-group">
                    <span class="checkbox">{{ ($pds->related_fourth_degree ?? false) ? '✓' : '' }}</span> YES
                </div>
                <div class="checkbox-group">
                    <span class="checkbox">{{ !($pds->related_fourth_degree ?? false) ? '✓' : '' }}</span> NO
                </div>
            </td>
            <td colspan="3" style="font-size:7pt;">
                {{ $pds->related_fourth_degree_details ?? '' }}
            </td>
        </tr>

        <tr>
            <td style="font-size:7pt; padding:3px;">
                <strong>35.</strong> a. Have you ever been found guilty of any administrative offense?
            </td>
            <td style="text-align:center;">
                <div class="checkbox-group">
                    <span class="checkbox">{{ ($pds->has_admin_case ?? false) ? '✓' : '' }}</span> YES
                </div>
                <div class="checkbox-group">
                    <span class="checkbox">{{ !($pds->has_admin_case ?? false) ? '✓' : '' }}</span> NO
                </div>
            </td>
            <td colspan="3" style="font-size:7pt;">
                If YES, give details: {{ $pds->admin_case_details ?? '' }}
            </td>
        </tr>

        <tr>
            <td style="font-size:7pt; padding:3px;">
                b. Have you been criminally charged before any court?
            </td>
            <td style="text-align:center;">
                <div class="checkbox-group">
                    <span class="checkbox">{{ ($pds->has_criminal_case ?? false) ? '✓' : '' }}</span> YES
                </div>
                <div class="checkbox-group">
                    <span class="checkbox">{{ !($pds->has_criminal_case ?? false) ? '✓' : '' }}</span> NO
                </div>
            </td>
            <td colspan="3" style="font-size:7pt;">
                If YES, give details:<br>
                Date Filed: {{ optional($pds->criminal_case_date_filed)->format('m/d/Y') }}<br>
                Status: {{ $pds->criminal_case_status ?? '' }}
            </td>
        </tr>

        <tr>
            <td style="font-size:7pt; padding:3px;">
                <strong>36.</strong> Have you ever been convicted of any crime or violation of any law, decree, ordinance or regulation by any court or tribunal?
            </td>
            <td style="text-align:center;">
                <div class="checkbox-group">
                    <span class="checkbox">{{ ($pds->has_conviction ?? false) ? '✓' : '' }}</span> YES
                </div>
                <div class="checkbox-group">
                    <span class="checkbox">{{ !($pds->has_conviction ?? false) ? '✓' : '' }}</span> NO
                </div>
            </td>
            <td colspan="3" style="font-size:7pt;">
                If YES, give details: {{ $pds->conviction_details ?? '' }}
            </td>
        </tr>

        <tr>
            <td style="font-size:7pt; padding:3px;">
                <strong>37.</strong> Have you ever been separated from the service in any of the following modes: resignation,
                retirement, dropped from the rolls, dismissal, termination, end of term, finished contract or phased out
                (abolition) in the public or private sector?
            </td>
            <td style="text-align:center;">
                <div class="checkbox-group">
                    <span class="checkbox">{{ ($pds->has_been_separated ?? false) ? '✓' : '' }}</span> YES
                </div>
                <div class="checkbox-group">
                    <span class="checkbox">{{ !($pds->has_been_separated ?? false) ? '✓' : '' }}</span> NO
                </div>
            </td>
            <td colspan="3" style="font-size:7pt;">
                If YES, give details: {{ $pds->separation_details ?? '' }}
            </td>
        </tr>

        <tr>
            <td style="font-size:7pt; padding:3px;">
                <strong>38.</strong> a. Have you ever been a candidate in a national or local election held within the last year
                (except Barangay election)?
            </td>
            <td style="text-align:center;">
                <div class="checkbox-group">
                    <span class="checkbox">{{ ($pds->has_election_candidacy ?? false) ? '✓' : '' }}</span> YES
                </div>
                <div class="checkbox-group">
                    <span class="checkbox">{{ !($pds->has_election_candidacy ?? false) ? '✓' : '' }}</span> NO
                </div>
            </td>
            <td colspan="3" style="font-size:7pt;">
                If YES, give details: {{ $pds->election_candidacy_details ?? '' }}
            </td>
        </tr>

        <tr>
            <td style="font-size:7pt; padding:3px;">
                b. Have you resigned from the government service during the three (3)-month period before the last
                election to promote/actively campaign for a national or local candidate?
            </td>
            <td style="text-align:center;">
                <div class="checkbox-group">
                    <span class="checkbox">{{ ($pds->resigned_for_campaign ?? false) ? '✓' : '' }}</span> YES
                </div>
                <div class="checkbox-group">
                    <span class="checkbox">{{ !($pds->resigned_for_campaign ?? false) ? '✓' : '' }}</span> NO
                </div>
            </td>
            <td colspan="3" style="font-size:7pt;">
                If YES, give details: {{ $pds->resigned_campaign_details ?? '' }}
            </td>
        </tr>

        <tr>
            <td style="font-size:7pt; padding:3px;">
                <strong>39.</strong> Have you acquired the status of an immigrant or permanent resident of another country?
            </td>
            <td style="text-align:center;">
                <div class="checkbox-group">
                    <span class="checkbox">{{ ($pds->is_immigrant ?? false) ? '✓' : '' }}</span> YES
                </div>
                <div class="checkbox-group">
                    <span class="checkbox">{{ !($pds->is_immigrant ?? false) ? '✓' : '' }}</span> NO
                </div>
            </td>
            <td colspan="3" style="font-size:7pt;">
                If YES, give details (country): {{ $pds->immigrant_details ?? '' }}
            </td>
        </tr>

        <tr>
            <td style="font-size:7pt; padding:3px;">
                <strong>40.</strong> Pursuant to: (a) Indigenous People's Act (RA 8371); (b) Magna Carta for Disabled Persons (RA 7277);
                and (c) Solo Parents Welfare Act of 2000 (RA 8972), please answer the following items:
            </td>
            <td colspan="4"></td>
        </tr>

        <tr>
            <td style="font-size:7pt; padding:3px; padding-left:20px;">
                a. Are you a member of any indigenous group?
            </td>
            <td style="text-align:center;">
                <div class="checkbox-group">
                    <span class="checkbox">{{ ($pds->is_indigenous ?? false) ? '✓' : '' }}</span> YES
                </div>
                <div class="checkbox-group">
                    <span class="checkbox">{{ !($pds->is_indigenous ?? false) ? '✓' : '' }}</span> NO
                </div>
            </td>
            <td colspan="3" style="font-size:7pt;">
                If YES, please specify: {{ $pds->indigenous_details ?? '' }}
            </td>
        </tr>

        <tr>
            <td style="font-size:7pt; padding:3px; padding-left:20px;">
                b. Are you a person with disability?
            </td>
            <td style="text-align:center;">
                <div class="checkbox-group">
                    <span class="checkbox">{{ ($pds->has_disability ?? false) ? '✓' : '' }}</span> YES
                </div>
                <div class="checkbox-group">
                    <span class="checkbox">{{ !($pds->has_disability ?? false) ? '✓' : '' }}</span> NO
                </div>
            </td>
            <td colspan="3" style="font-size:7pt;">
                If YES, please specify ID No: {{ $pds->disability_details ?? '' }}
            </td>
        </tr>

        <tr>
            <td style="font-size:7pt; padding:3px; padding-left:20px;">
                c. Are you a solo parent?
            </td>
            <td style="text-align:center;">
                <div class="checkbox-group">
                    <span class="checkbox">{{ ($pds->is_solo_parent ?? false) ? '✓' : '' }}</span> YES
                </div>
                <div class="checkbox-group">
                    <span class="checkbox">{{ !($pds->is_solo_parent ?? false) ? '✓' : '' }}</span> NO
                </div>
            </td>
            <td colspan="3" style="font-size:7pt;">
                If YES, please specify ID No: {{ $pds->solo_parent_details ?? '' }}
            </td>
        </tr>
    </table>

    {{-- REFERENCES --}}
    <div class="section-title" style="margin-top:8px;">41. REFERENCES</div>
    <div style="font-size:6pt; font-style:italic; margin:2px 0;">
        (Person not related by consanguinity or affinity to applicant /appointee)
    </div>

    <table class="pds-table">
        <tr>
            <th class="header-row" style="width:30%;">NAME</th>
            <th class="header-row" style="width:50%;">ADDRESS</th>
            <th class="header-row" style="width:20%;">TEL. NO.</th>
        </tr>

        @php
            $references = $pds->references ?? [];
            $maxReferences = 3; // Standard PDS shows 3 references
        @endphp

        @for($i = 0; $i < $maxReferences; $i++)
            @php
                $ref = $references[$i] ?? null;
            @endphp
            <tr>
                <td class="data-cell">{{ $ref['name'] ?? '' }}</td>
                <td class="data-cell">{{ $ref['address'] ?? '' }}</td>
                <td class="data-cell">{{ $ref['tel'] ?? '' }}</td>
            </tr>
        @endfor
    </table>

    {{-- DECLARATION --}}
    <div class="section-title" style="margin-top:8px;">42. I declare under oath that I have personally accomplished this Personal Data Sheet which is a true, correct and complete statement pursuant to the provisions of pertinent laws, rules and regulations of the Republic of the Philippines. I authorize the agency head/authorized representative to verify/validate the contents stated herein. I agree that any misrepresentation made in this document and its attachments shall cause the filing of administrative/criminal case/s against me.</div>

    <table class="pds-table" style="margin-top:5px;">
        <tr>
            <td colspan="2" class="label-cell">
                Government Issued ID (i.e.Passport, GSIS, SSS, PRC, Driver's License, etc.)<br>
                PLEASE INDICATE ID Number and Date of Issuance
            </td>
            <td colspan="2" class="label-cell">ID/License/Passport No.:</td>
        </tr>
        <tr>
            <td colspan="2" class="data-cell" style="min-height:25px;">
                {{ $pds->gov_id_type ?? '' }}
            </td>
            <td colspan="2" class="data-cell" style="min-height:25px;">
                {{ $pds->gov_id_no ?? '' }}
            </td>
        </tr>
        <tr>
            <td colspan="4" class="label-cell">Date/Place of Issuance</td>
        </tr>
        <tr>
            <td colspan="4" class="data-cell" style="min-height:25px;">
                {{ $pds->gov_id_issued ?? '' }}
            </td>
        </tr>
    </table>

    {{-- SIGNATURE AND OATH SECTIONS --}}
    <table style="width:100%; margin-top:10px; border-collapse:collapse;">
        <tr>
            <td style="width:50%; vertical-align:top; padding-right:5px;">
                <div class="signature-box" style="min-height:80px;">
                    <div style="font-size:7pt; margin-bottom:30px;">Signature (Sign inside the box)</div>
                    <div style="border-top:1px solid #000; padding-top:5px; font-size:7pt;">
                        Date Accomplished: {{ optional($pds->date_accomplished)->format('m/d/Y') }}
                    </div>
                </div>
            </td>
            <td style="width:50%; vertical-align:top; padding-left:5px;">
                <div class="signature-box" style="min-height:80px;">
                    <div style="font-size:7pt;">Right Thumbmark</div>
                </div>
            </td>
        </tr>
    </table>

    <table style="width:100%; margin-top:10px; border-collapse:collapse;">
        <tr>
            <td style="width:50%; vertical-align:top;">
                <div style="font-size:7pt; padding:10px 0;">
                    SUBSCRIBED AND SWORN to before me this
                    <span style="display:inline-block; width:80px; border-bottom:1px solid #000; text-align:center;">{{ optional($pds->oath_date)->format('d') }}</span>
                    day of
                    <span style="display:inline-block; width:100px; border-bottom:1px solid #000; text-align:center;">{{ optional($pds->oath_date)->format('F') }}</span>,
                    <span style="display:inline-block; width:60px; border-bottom:1px solid #000; text-align:center;">{{ optional($pds->oath_date)->format('Y') }}</span>
                </div>
            </td>
            <td style="width:50%; vertical-align:top; padding-left:5px;">
                <div class="signature-box" style="min-height:60px;">
                    <div style="font-size:7pt;">Person Administering Oath</div>
                </div>
            </td>
        </tr>
    </table>

    <div style="text-align:center; font-size:7pt; margin-top:15px;">
        <em>CS FORM 212 (Revised 2017), Page 4 of 4</em>
    </div>
</div>
