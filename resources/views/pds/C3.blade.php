{{-- resources/views/pds/C3.blade.php --}}
<div class="page-break">
    <h4>C3 – VOLUNTARY WORK, L&D & OTHER INFORMATION</h4>

    {{-- VI. VOLUNTARY WORK OR INVOLVEMENT --}}
    <table class="table-bordered">
        <tr>
            <th colspan="4">VI. VOLUNTARY WORK OR INVOLVEMENT IN CIVIC / NON-GOVERNMENT / PEOPLE / VOLUNTARY ORGANIZATION/S</th>
        </tr>
        <tr>
            <th>NAME & ADDRESS OF ORGANIZATION</th>
            <th>INCLUSIVE DATES (FROM)</th>
            <th>INCLUSIVE DATES (TO)</th>
            <th>NUMBER OF HOURS / POSITION</th>
        </tr>
        @foreach($pds->voluntary_work ?? [] as $work)
        <tr>
            <td>{{ $work['organization_name'] ?? '' }}</td>
            <td>{{ !empty($work['from_date']) ? \Carbon\Carbon::parse($work['from_date'])->format('m/d/Y') : '' }}</td>
            <td>{{ !empty($work['to_date']) ? \Carbon\Carbon::parse($work['to_date'])->format('m/d/Y') : '' }}</td>
            <td>{{ $work['hours'] ?? '' }} / {{ $work['position'] ?? '' }}</td>
        </tr>
        @endforeach
    </table>

    <br>

    {{-- VII. LEARNING AND DEVELOPMENT (L&D) --}}
    <table class="table-bordered">
        <tr>
            <th colspan="5">VII. LEARNING AND DEVELOPMENT (L&D) INTERVENTIONS / TRAINING PROGRAMS ATTENDED</th>
        </tr>
        <tr>
            <th>TITLE OF TRAINING</th>
            <th>INCLUSIVE DATES (FROM)</th>
            <th>INCLUSIVE DATES (TO)</th>
            <th>NUMBER OF HOURS</th>
            <th>TYPE / CONDUCTED BY</th>
        </tr>
        @foreach($pds->learning_development ?? [] as $ld)
        <tr>
            <td>{{ $ld['training_title'] ?? '' }}</td>
            <td>{{ !empty($ld['from_date']) ? \Carbon\Carbon::parse($ld['from_date'])->format('m/d/Y') : '' }}</td>
            <td>{{ !empty($ld['to_date']) ? \Carbon\Carbon::parse($ld['to_date'])->format('m/d/Y') : '' }}</td>
            <td>{{ $ld['hours'] ?? '' }}</td>
            <td>{{ $ld['type'] ?? '' }} / {{ $ld['conducted_by'] ?? '' }}</td>
        </tr>
        @endforeach
    </table>

    <br>

    {{-- VIII. OTHER INFORMATION --}}
    <table class="table-bordered">
        <tr>
            <th colspan="3">VIII. OTHER INFORMATION</th>
        </tr>
        <tr>
            <th>SPECIAL SKILLS & HOBBIES</th>
            <th>NON-ACADEMIC DISTINCTIONS / RECOGNITION</th>
            <th>MEMBERSHIP IN ASSOCIATION / ORGANIZATION</th>
        </tr>
        @php
            $maxRows = max(
                count($pds->special_skills ?? []),
                count($pds->non_academic_distinctions ?? []),
                count($pds->membership_association ?? [])
            );
        @endphp
        @for($i = 0; $i < $maxRows; $i++)
        <tr>
            <td>{{ $pds->special_skills[$i]['skill'] ?? '' }}</td>
            <td>{{ $pds->non_academic_distinctions[$i]['distinction'] ?? '' }}</td>
            <td>{{ $pds->membership_association[$i]['organization'] ?? '' }}</td>
        </tr>
        @endfor
    </table>
</div>
