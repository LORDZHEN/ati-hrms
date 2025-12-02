{{-- resources/views/pds/C4.blade.php --}}
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>CS Form No. 212 - C4</title>
    <style>
        @page { size:A4; margin:18mm; }
        body { font-family: Arial, sans-serif; font-size:12px; }
        h4 { margin-bottom:6px; }
        table { width:100%; border-collapse: collapse; margin-bottom:8px; }
        td, th { padding:6px; vertical-align: top; border:1px solid #000; }
        .note { font-size:11px; }
    </style>
</head>
<body>
    <h3 style="text-align:center;">CS Form No. 212 — OTHER INFORMATION & DECLARATION (C4)</h3>

    <h4>Voluntary Activities / Memberships in Civic / Socio-Civic Organizations</h4>
    <table>
        <tr>
            <th>No.</th>
            <th>Name & Address of Organization</th>
            <th>Inclusive Dates</th>
            <th>Position / Nature of Involvement</th>
        </tr>
        {{-- If you have fields for voluntary activities, map them as JSON like $pds->voluntary_activities --}}
        <tr><td colspan="4" style="text-align:center;">(If you maintain voluntary activities in model, map them here)</td></tr>
    </table>

    <h4>Learning & Development Interventions / Trainings</h4>
    <table>
        <tr>
            <th>No.</th>
            <th>Title</th>
            <th>Type</th>
            <th>Inclusive Dates</th>
            <th>Number of Hours</th>
            <th>Conducted/Provided By</th>
        </tr>
        <tr><td colspan="6" style="text-align:center;">(If you maintain trainings in model, map them here)</td></tr>
    </table>

    <h4>Other Information</h4>
    <table>
        <tr>
            <th>Have you ever been convicted of any crime?</th>
            <td></td>
            <th>Have you ever been separated from the service?</th>
            <td></td>
        </tr>
        <tr>
            <th>Have you ever been a candidate in a national or local election?</th>
            <td></td>
            <th>Other relevant information</th>
            <td></td>
        </tr>
    </table>

    <h4>Declaration</h4>
    <p class="note">
        I declare under oath that this Personal Data Sheet has been accomplished by me and that all entries are true and correct.
    </p>

    <table>
        <tr>
            <td style="width:50%; border:none; padding-top:40px;">
                _______________________________<br>
                Signature over Printed Name
            </td>
            <td style="width:25%; border:none; text-align:center;">
                Date: {{ now()->format('m/d/Y') }}
            </td>
            <td style="width:25%; border:none; text-align:center;">
                Place:
            </td>
        </tr>
    </table>

    @if(!empty($pds->remarks))
        <h4>Remarks (Admin)</h4>
        <div style="border:1px solid #000; padding:8px;">{{ $pds->remarks }}</div>
    @endif

</body>
</html>
