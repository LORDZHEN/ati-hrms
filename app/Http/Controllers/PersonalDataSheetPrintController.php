<?php

namespace App\Http\Controllers;

use App\Models\PersonalDataSheet;

class PersonalDataSheetPrintController extends Controller
{
    public function print(PersonalDataSheet $pds)
    {
        $pds->refresh();

        return view('pds.print', compact('pds'));
    }
}
