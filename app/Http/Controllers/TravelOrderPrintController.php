<?php

namespace App\Http\Controllers;

use App\Models\TravelOrder;
use Illuminate\Http\Request;

class TravelOrderPrintController extends Controller
{
    /**
     * Show the print view for a Travel Order.
     */
    public function print(TravelOrder $travelOrder)
    {
        return view('filament.employee.travel-order.print', [
            'record' => $travelOrder,
        ]);
    }
}
