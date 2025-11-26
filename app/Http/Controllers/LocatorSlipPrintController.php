<?php

namespace App\Http\Controllers;

use App\Models\LocatorSlip;
use Illuminate\Http\Request;

class LocatorSlipPrintController extends Controller
{
    public function print($id)
    {
        $record = LocatorSlip::findOrFail($id);

        return view('filament.employee.locator-slip.print', compact('record'));
    }
}
