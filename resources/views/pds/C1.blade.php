{{-- resources/views/pds/C1.blade.php --}}
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>CS Form No. 212 - C1</title>
    <style>
        @page { size: A4; margin: 18mm; }
        body { font-family: Arial, sans-serif; font-size: 12px; color:#000; }
        .container { width:100%; }
        .header { text-align:center; font-weight:700; margin-bottom:8px; }
        table { width:100%; border-collapse: collapse; margin-bottom:8px; }
        td, th { padding:6px; vertical-align: top; }
        .field-label { font-weight:700; font-size:11px; width:30%; }
        .outline { border:1px solid #000; padding:6px; }
        .small { font-size:11px; }
        .grid-2 td { width:50%; }
        .grid-3 td { width:33.33%; }
        .checkbox { display:inline-block; width:12px; height:12px; border:1px solid #000; text-align:center; line-height:12px; margin-right:6px; font-size:10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            CS FORM NO. 212 (Revised 2025) — PERSONAL DATA SHEET (C1)
        </div>

        <table>
            <tr>
                <td class="field-label">1. SURNAME</td>
                <td class="outline">{{ $pds->surname }}</td>
                <td class="field-label">2. FIRST NAME</td>
                <td class="outline">{{ $pds->first_name }}</td>
            </tr>
            <tr>
                <td class="field-label">3. NAME EXTENSION</td>
                <td class="outline">{{ $pds->name_extension }}</td>
                <td class="field-label">4. MIDDLE NAME</td>
                <td class="outline">{{ $pds->middle_name }}</td>
            </tr>
            <tr>
                <td class="field-label">5. DATE OF BIRTH</td>
                <td class="outline">{{ optional($pds->date_of_birth)->format('m/d/Y') }}</td>
                <td class="field-label">6. PLACE OF BIRTH</td>
                <td class="outline">{{ $pds->place_of_birth }}</td>
            </tr>
            <tr>
                <td class="field-label">7. SEX</td>
                <td class="outline">{{ ucfirst($pds->sex ?? '') }}</td>
                <td class="field-label">8. CIVIL STATUS</td>
                <td class="outline">{{ ucfirst($pds->civil_status ?? '') }}</td>
            </tr>
            <tr>
                <td class="field-label">9. HEIGHT (cm)</td>
                <td class="outline">{{ $pds->height }}</td>
                <td class="field-label">10. WEIGHT (kg)</td>
                <td class="outline">{{ $pds->weight }}</td>
            </tr>
            <tr>
                <td class="field-label">11. BLOOD TYPE</td>
                <td class="outline">{{ $pds->blood_type }}</td>
                <td class="field-label">12. GSIS ID NO.</td>
                <td class="outline">{{ $pds->gsis_id_no }}</td>
            </tr>
            <tr>
                <td class="field-label">13. PAG-IBIG ID NO.</td>
                <td class="outline">{{ $pds->pag_ibig_id_no }}</td>
                <td class="field-label">14. PHILHEALTH NO.</td>
                <td class="outline">{{ $pds->philhealth_no }}</td>
            </tr>
            <tr>
                <td class="field-label">15. SSS NO.</td>
                <td class="outline">{{ $pds->sss_no }}</td>
                <td class="field-label">16. TIN NO.</td>
                <td class="outline">{{ $pds->tin_no }}</td>
            </tr>
            <tr>
                <td class="field-label">17. AGENCY EMPLOYEE NO.</td>
                <td class="outline">{{ $pds->agency_employee_no }}</td>
                <td class="field-label">Year</td>
                <td class="outline">{{ $pds->year }}</td>
            </tr>
        </table>

        <h4 class="small">Citizenship</h4>
        <table>
            <tr>
                <td>
                    <span class="checkbox">{!! $pds->filipino ? '&#10003;' : '' !!}</span> Filipino
                    &nbsp;&nbsp;
                    <span class="checkbox">{!! $pds->dual_citizenship ? '&#10003;' : '' !!}</span> Dual Citizenship
                </td>
                <td>
                    @if($pds->dual_citizenship)
                        <div class="small">
                            <span class="checkbox">{!! $pds->by_birth ? '&#10003;' : '' !!}</span> by birth
                            <span style="margin-left:12px" class="checkbox">{!! $pds->by_naturalization ? '&#10003;' : '' !!}</span> by naturalization
                            <div style="margin-top:6px"><strong>If dual citizen, country:</strong> {{ $pds->country }}</div>
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        <h4 class="small">Residential Address</h4>
        <table class="grid-3">
            <tr>
                <td class="field-label">House/Block/Lot No.</td>
                <td class="outline" colspan="2">{{ $pds->res_house_block_lot_no }}</td>
            </tr>
            <tr>
                <td class="field-label">Street</td>
                <td class="outline" colspan="2">{{ $pds->res_street }}</td>
            </tr>
            <tr>
                <td class="field-label">Subdivision/Village</td>
                <td class="outline">{{ $pds->res_subdivision_village }}</td>
                <td class="outline">{{ $pds->res_barangay }}, {{ $pds->res_city_municipality }}</td>
            </tr>
            <tr>
                <td class="field-label">Province</td>
                <td class="outline">{{ $pds->res_province }}</td>
                <td class="outline">ZIP: {{ $pds->res_zip_code }}</td>
            </tr>
        </table>

        <h4 class="small">Permanent Address</h4>
        <table class="grid-3">
            <tr>
                <td class="field-label">House/Block/Lot No.</td>
                <td class="outline" colspan="2">{{ $pds->perm_house_block_lot_no }}</td>
            </tr>
            <tr>
                <td class="field-label">Street</td>
                <td class="outline" colspan="2">{{ $pds->perm_street }}</td>
            </tr>
            <tr>
                <td class="field-label">Subdivision/Village</td>
                <td class="outline">{{ $pds->perm_subdivision_village }}</td>
                <td class="outline">{{ $pds->perm_barangay }}, {{ $pds->perm_city_municipality }}</td>
            </tr>
            <tr>
                <td class="field-label">Province</td>
                <td class="outline">{{ $pds->perm_province }}</td>
                <td class="outline">ZIP: {{ $pds->perm_zip_code }}</td>
            </tr>
        </table>

        <h4 class="small">Contact Information</h4>
        <table>
            <tr>
                <td class="field-label">Telephone No.</td>
                <td class="outline">{{ $pds->telephone_no }}</td>
                <td class="field-label">Mobile No.</td>
                <td class="outline">{{ $pds->mobile_no }}</td>
            </tr>
            <tr>
                <td class="field-label">E-mail Address</td>
                <td class="outline" colspan="3">{{ $pds->email_address }}</td>
            </tr>
        </table>

    </div>
</body>
</html>
