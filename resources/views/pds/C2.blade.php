{{-- resources/views/pds/C2.blade.php --}}
<div class="page-break">
    <h4>C2 – CIVIL SERVICE ELIGIBILITY & WORK EXPERIENCE</h4>

    {{-- CIVIL SERVICE ELIGIBILITY --}}
    <table class="table-bordered">
        <tr>
            <th colspan="6">CIVIL SERVICE ELIGIBILITY</th>
        </tr>
        <tr>
            <th>CAREER SERVICE</th>
            <th>RATING</th>
            <th>DATE OF EXAM</th>
            <th>PLACE</th>
            <th>LICENSE NO.</th>
            <th>VALIDITY</th>
        </tr>
        @foreach($pds->civil_service_eligibility ?? [] as $el)
        <tr>
            <td>{{ $el['career_service'] ?? '' }}</td>
            <td>{{ $el['rating'] ?? '' }}</td>
            <td>
                {{ !empty($el['exam_date']) ? \Carbon\Carbon::parse($el['exam_date'])->format('m/d/Y') : '' }}
            </td>
            <td>{{ $el['place'] ?? '' }}</td>
            <td>{{ $el['license_no'] ?? '' }}</td>
            <td>
                {{ !empty($el['validity']) ? \Carbon\Carbon::parse($el['validity'])->format('m/d/Y') : '' }}
            </td>
        </tr>
        @endforeach
    </table>

    <br>

    {{-- WORK EXPERIENCE --}}
    <table class="table-bordered">
        <tr>
            <th colspan="8">WORK EXPERIENCE</th>
        </tr>
        <tr>
            <th>FROM</th>
            <th>TO</th>
            <th>POSITION TITLE</th>
            <th>DEPARTMENT / AGENCY</th>
            <th>MONTHLY SALARY</th>
            <th>SALARY GRADE</th>
            <th>STATUS</th>
            <th>GOV'T SERVICE</th>
        </tr>
        @foreach($pds->work_experience ?? [] as $work)
        <tr>
            <td>{{ !empty($work['from']) ? \Carbon\Carbon::parse($work['from'])->format('m/d/Y') : '' }}</td>
            <td>{{ !empty($work['to']) ? \Carbon\Carbon::parse($work['to'])->format('m/d/Y') : '' }}</td>
            <td>{{ $work['position'] ?? '' }}</td>
            <td>{{ $work['agency'] ?? '' }}</td>
            <td>{{ $work['salary'] ?? '' }}</td>
            <td>{{ $work['salary_grade'] ?? '' }}</td>
            <td>{{ $work['status'] ?? '' }}</td>
            <td>{{ !empty($work['is_government']) && $work['is_government'] ? 'YES' : 'NO' }}</td>
        </tr>
        @endforeach
    </table>
</div>
