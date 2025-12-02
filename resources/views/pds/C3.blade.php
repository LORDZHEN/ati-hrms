{{-- resources/views/pds/C3.blade.php --}}
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>CS Form No. 212 - C3</title>
    <style>
        @page { size:A4; margin:18mm; }
        body { font-family: Arial, sans-serif; font-size:12px; }
        table { width:100%; border-collapse: collapse; margin-bottom:8px; }
        td, th { padding:6px; vertical-align: top; border:1px solid #000; font-size:12px; }
        th { background:#f2f2f2; }
    </style>
</head>
<body>
    <h3 style="text-align:center;">CS Form No. 212 — CIVIL SERVICE ELIGIBILITY & WORK EXPERIENCE (C3)</h3>

    <div style="margin-top:8px; font-weight:700;">Civil Service Eligibility</div>
    <table>
        <tr>
            <th>No.</th>
            <th>Eligibility</th>
            <th>Rating</th>
            <th>Date of Examination / Conferment</th>
            <th>Place of Examination / Conferment</th>
            <th>License Number (if applicable)</th>
            <th>Date of Validity</th>
        </tr>
        @if(!empty($pds->eligibilities) && is_array($pds->eligibilities))
            @foreach($pds->eligibilities as $i => $el)
                <tr>
                    <td style="text-align:center;">{{ $i + 1 }}</td>
                    <td>{{ $el['eligibility'] ?? '' }}</td>
                    <td style="text-align:center;">{{ $el['rating'] ?? '' }}</td>
                    <td style="text-align:center;">{{ !empty($el['date_of_examination']) ? \Carbon\Carbon::parse($el['date_of_examination'])->format('m/d/Y') : '' }}</td>
                    <td>{{ $el['place_of_examination'] ?? '' }}</td>
                    <td style="text-align:center;">{{ $el['license_number'] ?? '' }}</td>
                    <td style="text-align:center;">{{ !empty($el['license_validity']) ? \Carbon\Carbon::parse($el['license_validity'])->format('m/d/Y') : '' }}</td>
                </tr>
            @endforeach
        @else
            <tr><td colspan="7" style="text-align:center;">No eligibility records</td></tr>
        @endif
    </table>

    <div style="margin-top:8px; font-weight:700;">Work Experience (Public and Private)</div>
    <table>
        <tr>
            <th>No.</th>
            <th>Position Title</th>
            <th>Department / Agency / Office / Company</th>
            <th>From</th>
            <th>To</th>
            <th>Monthly Salary</th>
            <th>Salary Grade & Step</th>
            <th>Status of Appointment</th>
            <th>Gov't Service (Y/N)</th>
        </tr>
        @if(!empty($pds->work_experience) && is_array($pds->work_experience))
            @foreach($pds->work_experience as $i => $w)
                <tr>
                    <td style="text-align:center;">{{ $i + 1 }}</td>
                    <td>{{ $w['position_title'] ?? '' }}</td>
                    <td>{{ $w['department_agency'] ?? '' }}</td>
                    <td style="text-align:center;">{{ !empty($w['from']) ? \Carbon\Carbon::parse($w['from'])->format('m/d/Y') : '' }}</td>
                    <td style="text-align:center;">{{ !empty($w['to']) ? \Carbon\Carbon::parse($w['to'])->format('m/d/Y') : '' }}</td>
                    <td style="text-align:right;">{{ $w['monthly_salary'] ?? '' }}</td>
                    <td style="text-align:center;">{{ $w['salary_grade'] ?? '' }}</td>
                    <td style="text-align:center;">{{ $w['status_of_appointment'] ?? '' }}</td>
                    <td style="text-align:center;">{{ (isset($w['is_gov_service']) && $w['is_gov_service']) ? 'Y' : 'N' }}</td>
                </tr>
            @endforeach
        @else
            <tr><td colspan="9" style="text-align:center;">No work experience records</td></tr>
        @endif
    </table>

</body>
</html>
