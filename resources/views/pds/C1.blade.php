{{-- resources/views/pds/C1.blade.php --}}
<div class="page-break">
    <table style="width:100%; font-size:11px;">
        <tr>
            <td style="width:20%;">
                CS Form No. 212<br>
                Revised 2020
            </td>
            <td style="width:60%; text-align:center;">
                <strong>REPUBLIC OF THE PHILIPPINES</strong><br>
                <strong>CIVIL SERVICE COMMISSION</strong>
            </td>
            <td style="width:20%; text-align:right;">
                Page 1 of 4
            </td>
        </tr>
    </table>

    <h2 style="text-align:center; margin-top:10px;">
        <strong>PERSONAL DATA SHEET</strong>
    </h2>

    <p style="font-size:11px; text-align:justify;">
        <strong>WARNING:</strong> Any misrepresentation made in the Personal Data Sheet and the Work Experience Sheet
        shall cause the filing of administrative/criminal case/s against the person concerned.
    </p>

    <p style="font-size:11px; text-align:justify;">
        <strong>READ THE ATTACHED GUIDE TO FILLING OUT THE PERSONAL DATA SHEET (PDS) BEFORE ACCOMPLISHING THE PDS FORM.</strong>
    </p>

    <p style="font-size:11px; text-align:justify;">
        Print legibly. Tick appropriate boxes ( <span class="checkbox">&#10003;</span> ) and use separate sheet if necessary.
        Indicate N/A if not applicable. <strong>DO NOT ABBREVIATE.</strong>
    </p>

    <p><strong>C1 – PERSONAL INFORMATION, FAMILY BACKGROUND & EDUCATIONAL BACKGROUND</strong></p>

    {{-- PERSONAL INFORMATION --}}
    <table class="table-bordered">
        <tr>
            <td>1. SURNAME</td>
            <td>{{ $pds->surname }}</td>
            <td>FIRST NAME</td>
            <td>{{ $pds->first_name }}</td>
        </tr>
        <tr>
            <td>MIDDLE NAME</td>
            <td>{{ $pds->middle_name }}</td>
            <td>NAME EXTENSION</td>
            <td>{{ $pds->name_extension }}</td>
        </tr>

        <tr>
            <td>2. DATE OF BIRTH</td>
            <td>{{ optional($pds->birthdate)->format('m/d/Y') }}</td>
            <td>3. PLACE OF BIRTH</td>
            <td>{{ $pds->birthplace }}</td>
        </tr>

        <tr>
            <td>4. SEX</td>
            <td>
                <span class="checkbox">{!! $pds->sex === 'male' ? '&#10003;' : '' !!}</span> Male
                <span class="checkbox">{!! $pds->sex === 'female' ? '&#10003;' : '' !!}</span> Female
            </td>
            <td>5. CIVIL STATUS</td>
            <td>
                @foreach(['single','married','widowed','separated'] as $status)
                    <span class="checkbox">{!! $pds->civil_status === $status ? '&#10003;' : '' !!}</span>
                    {{ ucfirst($status) }}
                @endforeach
            </td>
        </tr>

        <tr>
            <td>6. CITIZENSHIP</td>
            <td colspan="3">
                <span class="checkbox">{!! $pds->citizenship === 'filipino' ? '&#10003;' : '' !!}</span> Filipino
                <span class="checkbox">{!! $pds->citizenship === 'dual' ? '&#10003;' : '' !!}</span> Dual Citizenship
                {{ $pds->citizenship_details }}
            </td>
        </tr>

        <tr>
            <td>7. RESIDENTIAL ADDRESS</td>
            <td colspan="3">{{ $pds->residential_address }}</td>
        </tr>
        <tr>
            <td>ZIP CODE</td>
            <td>{{ $pds->res_zip }}</td>
            <td>TELEPHONE NO.</td>
            <td>{{ $pds->res_tel }}</td>
        </tr>

        <tr>
            <td>8. PERMANENT ADDRESS</td>
            <td colspan="3">{{ $pds->permanent_address }}</td>
        </tr>
        <tr>
            <td>ZIP CODE</td>
            <td>{{ $pds->perm_zip }}</td>
            <td>TELEPHONE NO.</td>
            <td>{{ $pds->perm_tel }}</td>
        </tr>

        <tr>
            <td>9. MOBILE NO.</td>
            <td>{{ $pds->mobile }}</td>
            <td>EMAIL</td>
            <td>{{ $pds->email }}</td>
        </tr>
    </table>

    <br>

    {{-- FAMILY BACKGROUND --}}
    <table class="table-bordered">
        <tr>
            <th colspan="4">FAMILY BACKGROUND</th>
        </tr>
        <tr>
            <td>SPOUSE SURNAME</td>
            <td>{{ $pds->spouse_surname }}</td>
            <td>FIRST NAME</td>
            <td>{{ $pds->spouse_firstname }}</td>
        </tr>
        <tr>
            <td>OCCUPATION</td>
            <td>{{ $pds->spouse_occupation }}</td>
            <td>EMPLOYER</td>
            <td>{{ $pds->spouse_employer }}</td>
        </tr>

        <tr>
            <td>FATHER'S NAME</td>
            <td colspan="3">{{ $pds->father_name }}</td>
        </tr>
        <tr>
            <td>MOTHER'S MAIDEN NAME</td>
            <td colspan="3">{{ $pds->mother_name }}</td>
        </tr>
    </table>

    <br>

    {{-- CHILDREN --}}
    <table class="table-bordered">
        <tr>
            <th colspan="2">CHILDREN</th>
        </tr>
        <tr>
            <th>NAME</th>
            <th>DATE OF BIRTH</th>
        </tr>
        @foreach($pds->children ?? [] as $child)
        <tr>
            <td>{{ $child['name'] ?? '' }}</td>
            <td>
                {{
                    !empty($child['birthdate'])
                        ? \Carbon\Carbon::parse($child['birthdate'])->format('m/d/Y')
                        : ''
                }}
            </td>
        </tr>
        @endforeach
    </table>

    <p style="text-align:center;font-size:11px;">
        (Continue on separate sheet if necessary)
    </p>

    <br>

    {{-- EDUCATIONAL BACKGROUND --}}
    <table class="table-bordered">
        <tr>
            <th colspan="5">EDUCATIONAL BACKGROUND</th>
        </tr>
        <tr>
            <th>LEVEL</th>
            <th>NAME OF SCHOOL</th>
            <th>BASIC EDUCATION / DEGREE</th>
            <th>PERIOD</th>
            <th>HONORS</th>
        </tr>
        @foreach($pds->education ?? [] as $edu)
        <tr>
            <td>{{ strtoupper($edu['level'] ?? '') }}</td>
            <td>{{ $edu['school_name'] ?? '' }}</td>
            <td>{{ $edu['degree'] ?? '' }}</td>
            <td>{{ ($edu['from_year'] ?? '') }} - {{ ($edu['to_year'] ?? '') }}</td>
            <td>{{ $edu['honors'] ?? '' }}</td>
        </tr>
        @endforeach
    </table>
</div>
