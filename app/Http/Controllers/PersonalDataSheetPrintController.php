<?php

namespace App\Http\Controllers;

use App\Models\PersonalDataSheet;
use Barryvdh\DomPDF\Facade\Pdf;

class PersonalDataSheetPrintController extends Controller
{
    public function print($id)
    {
        $pds = PersonalDataSheet::findOrFail($id);

        // Combine all C1–C4 views into one print layout
        $pdf = Pdf::loadView('pds.print', [
            'pds' => $pds
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("PDS-{$pds->surname}.pdf");
    }
}
