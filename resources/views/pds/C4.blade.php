{{-- resources/views/pds/C4.blade.php --}}
<div class="page-break">

    <h4 style="text-align:center;">C4 – OTHER INFORMATION</h4>

    <table class="table-bordered" style="width:100%; font-size:11px; border-collapse:collapse;">
        {{-- 34 --}}
        <tr>
            <td colspan="4">
                <strong>34.</strong> Are you related by consanguinity or affinity to any of the following:
            </td>
        </tr>
        <tr>
            <td>a. Within the third degree?</td>
            <td>{{ $pds->related_third_degree ? 'YES' : 'NO' }}</td>
            <td colspan="2">{{ $pds->related_third_degree_details }}</td>
        </tr>
        <tr>
            <td>b. Within the fourth degree?</td>
            <td>{{ $pds->related_fourth_degree ? 'YES' : 'NO' }}</td>
            <td colspan="2">{{ $pds->related_fourth_degree_details }}</td>
        </tr>

        {{-- 35 --}}
        <tr>
            <td>35. Have you ever been found guilty of any administrative offense?</td>
            <td>{{ $pds->has_admin_case === 'Yes' ? 'YES' : 'NO' }}</td>
            <td colspan="2">{{ $pds->admin_case_details }}</td>
        </tr>

        {{-- 36 --}}
        <tr>
            <td>36. Have you been criminally charged before any court?</td>
            <td>{{ $pds->has_criminal_case === 'Yes' ? 'YES' : 'NO' }}</td>
            <td>Status: {{ $pds->criminal_case_status }}</td>
            <td>Date Filed:
                {{ optional($pds->criminal_case_date_filed)->format('m/d/Y') }}
            </td>
        </tr>

        {{-- 37 --}}
        <tr>
            <td>37. Have you ever been convicted of any crime or violation of any law?</td>
            <td>{{ $pds->has_conviction === 'Yes' ? 'YES' : 'NO' }}</td>
            <td colspan="2">{{ $pds->conviction_details }}</td>
        </tr>

        {{-- 38 --}}
        <tr>
            <td>38. Have you ever been separated from the service?</td>
            <td>{{ $pds->has_been_separated === 'Yes' ? 'YES' : 'NO' }}</td>
            <td colspan="2">{{ $pds->separation_details }}</td>
        </tr>

        {{-- 39 --}}
        <tr>
            <td>39. Have you ever been a candidate in a national or local election?</td>
            <td>{{ $pds->has_election_candidacy === 'Yes' ? 'YES' : 'NO' }}</td>
            <td colspan="2">{{ $pds->election_candidacy_details }}</td>
        </tr>

        {{-- 40 --}}
        <tr>
            <td>40.a. Indigenous Group?</td>
            <td>{{ $pds->is_indigenous ? 'YES' : 'NO' }}</td>
            <td colspan="2">{{ $pds->indigenous_details }}</td>
        </tr>
        <tr>
            <td>40.b. Person with Disability?</td>
            <td>{{ $pds->has_disability ? 'YES' : 'NO' }}</td>
            <td colspan="2">{{ $pds->disability_details }}</td>
        </tr>
        <tr>
            <td>40.c. Solo Parent?</td>
            <td>{{ $pds->is_solo_parent ? 'YES' : 'NO' }}</td>
            <td colspan="2">{{ $pds->solo_parent_details }}</td>
        </tr>

        {{-- 41 REFERENCES --}}
        <tr>
            <td colspan="4">
                <strong>41. REFERENCES</strong>
                <span style="font-size:10px;">
                    (Person not related by consanguinity or affinity to applicant/appointee)
                </span>
            </td>
        </tr>
        <tr>
            <th style="width:30%">NAME</th>
            <th style="width:40%">ADDRESS</th>
            <th colspan="2">TEL. NO.</th>
        </tr>

        @foreach(($pds->references ?? [[], [], []]) as $ref)
        <tr>
            <td>{{ $ref['name'] ?? '' }}</td>
            <td>{{ $ref['address'] ?? '' }}</td>
            <td colspan="2">{{ $ref['tel'] ?? '' }}</td>
        </tr>
        @endforeach

        {{-- 42 DECLARATION --}}
        <tr>
            <td colspan="4">
                <strong>42.</strong>
                I declare under oath that I have personally accomplished this Personal Data Sheet
                which is a true, correct and complete statement pursuant to the provisions of
                pertinent laws, rules and regulations of the Republic of the Philippines.
                I authorize the agency head/authorized representative to verify/validate the
                contents stated herein. I agree that any misrepresentation made in this document
                and its attachments shall cause the filing of administrative/criminal case/s
                against me.
            </td>
        </tr>

        {{-- GOV ID --}}
        <tr>
            <td colspan="2">
                Government Issued ID:<br>
                <strong>{{ $pds->gov_id_type }}</strong>
            </td>
            <td colspan="2">
                ID / License / Passport No.:<br>
                <strong>{{ $pds->gov_id_no }}</strong>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                Date / Place of Issuance:<br>
                <strong>{{ $pds->gov_id_issued }}</strong>
            </td>
        </tr>
    </table>

        {{-- SIGNATURE & DATE ACCOMPLISHED (ONE BOX) --}}
    <table style="width:100%; margin-top:25px; font-size:11px; border-collapse:collapse;">
        <tr>
            <td style="width:60%; text-align:center;">
                <div style="border:1px solid #000; height:90px;">
                    <div style="height:55px; border-bottom:1px solid #000;">
                        <span style="font-size:10px;">SIGNATURE (Sign inside the box)</span>
                    </div>
                    <div style="height:35px;">
                        <span style="font-size:10px;">DATE ACCOMPLISHED</span><br>
                        {{ optional($pds->date_accomplished)->format('m/d/Y') }}
                    </div>
                </div>
            </td>

            {{-- RIGHT THUMBMARK --}}
            <td style="width:40%; text-align:center;">
                <div style="border:1px solid #000; height:90px;">
                    <span style="font-size:10px;">RIGHT THUMBMARK</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- OATH --}}
    <table style="width:100%; margin-top:25px; font-size:11px; border-collapse:collapse;">
        <tr>
            <td style="width:60%;"></td>
            <td style="width:40%; text-align:center;">
                <div style="border:1px solid #000; height:60px;">
                    <span style="font-size:10px;">
                        PERSON ADMINISTERING OATH
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <p style="text-align:right; font-size:10px; margin-top:15px;">
        CS FORM 212 (Revised 2017) – Page 4 of 4
    </p>


</div>
