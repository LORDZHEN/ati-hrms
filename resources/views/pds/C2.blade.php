{{-- resources/views/pds/C2.blade.php --}}
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>CS Form No. 212 - C2</title>
    <style>
        @page { size:A4; margin:18mm; }
        body { font-family: Arial, sans-serif; font-size:12px; }
        table { width:100%; border-collapse: collapse; margin-bottom:8px; }
        td, th { padding:6px; vertical-align: top; border:1px solid #000; }
        .no-border td { border:none; padding:3px; }
        .section-title { font-weight:700; margin-top:8px; margin-bottom:4px; }
    </style>
</head>
<body>
    <h3 style="text-align:center;">CS Form No. 212 — FAMILY BACKGROUND & EDUCATION (C2)</h3>

    <div class="section-title">Spouse Information</div>
    <table>
        <tr>
            <th>SURNAME</th>
            <th>FIRST NAME</th>
            <th>NAME EXTENSION</th>
            <th>MIDDLE NAME</th>
        </tr>
        <tr>
            <td>{{ $pds->spouse_surname }}</td>
            <td>{{ $pds->spouse_first_name }}</td>
            <td>{{ $pds->spouse_name_extension }}</td>
            <td>{{ $pds->spouse_middle_name }}</td>
        </tr>
        <tr>
            <th>OCCUPATION</th>
            <th>EMPLOYER/BUSINESS NAME</th>
            <th colspan="2">BUSINESS ADDRESS / TELEPHONE NO.</th>
        </tr>
        <tr>
            <td>{{ $pds->spouse_occupation }}</td>
            <td>{{ $pds->spouse_employer_business_name }}</td>
            <td colspan="2">{{ $pds->spouse_business_address }}<br/>Tel: {{ $pds->spouse_telephone_no }}</td>
        </tr>
    </table>

    <div class="section-title">Father's Information</div>
    <table>
        <tr>
            <th>SURNAME</th>
            <th>FIRST NAME</th>
            <th>NAME EXTENSION</th>
            <th>MIDDLE NAME</th>
        </tr>
        <tr>
            <td>{{ $pds->father_surname }}</td>
            <td>{{ $pds->father_first_name }}</td>
            <td>{{ $pds->father_name_extension }}</td>
            <td>{{ $pds->father_middle_name }}</td>
        </tr>
    </table>

    <div class="section-title">Mother's Maiden Name</div>
    <table>
        <tr>
            <th>SURNAME</th>
            <th>FIRST NAME</th>
            <th>MIDDLE NAME</th>
        </tr>
        <tr>
            <td>{{ $pds->mother_surname }}</td>
            <td>{{ $pds->mother_first_name }}</td>
            <td>{{ $pds->mother_middle_name }}</td>
        </tr>
    </table>

    <div class="section-title">Children</div>
    <table>
        <tr>
            <th>No.</th>
            <th>Full Name</th>
            <th>Date of Birth</th>
        </tr>
        @if(!empty($pds->children) && is_array($pds->children))
            @foreach($pds->children as $i => $child)
                <tr>
                    <td style="text-align:center;">{{ $i + 1 }}</td>
                    <td>{{ $child['child_name'] ?? ($child['name'] ?? '') }}</td>
                    <td>{{ !empty($child['child_date_of_birth']) ? \Carbon\Carbon::parse($child['child_date_of_birth'])->format('m/d/Y') : '' }}</td>
                </tr>
            @endforeach
        @else
            <tr><td colspan="3" style="text-align:center;">No children listed</td></tr>
        @endif
    </table>

    <div class="section-title">Educational Background</div>
    <table>
        <tr>
            <th>No.</th>
            <th>LEVEL</th>
            <th>NAME OF SCHOOL</th>
            <th>BASIC EDUCATION/DEGREE/COURSE</th>
            <th>Period From</th>
            <th>Period To</th>
            <th>Year Graduated</th>
            <th>Highest Level/Units Earned</th>
            <th>Scholarship/Honors</th>
        </tr>
        @if(!empty($pds->education) && is_array($pds->education))
            @foreach($pds->education as $i => $edu)
                <tr>
                    <td style="text-align:center;">{{ $i + 1 }}</td>
                    <td>{{ strtoupper($edu['level'] ?? '') }}</td>
                    <td>{{ $edu['school_name'] ?? '' }}</td>
                    <td>{{ $edu['basic_education_degree_course'] ?? '' }}</td>
                    <td style="text-align:center;">{{ $edu['period_from'] ?? '' }}</td>
                    <td style="text-align:center;">{{ $edu['period_to'] ?? '' }}</td>
                    <td style="text-align:center;">{{ $edu['year_graduated'] ?? '' }}</td>
                    <td>{{ $edu['highest_level_units_earned'] ?? '' }}</td>
                    <td>{{ $edu['scholarship_academic_honors'] ?? '' }}</td>
                </tr>
            @endforeach
        @else
            <tr><td colspan="9" style="text-align:center;">No educational records</td></tr>
        @endif
    </table>

</body>
</html>
