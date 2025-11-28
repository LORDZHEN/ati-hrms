<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveApplication;

class LeaveApplicationPrintController extends Controller
{
    public function print($id)
    {
        $leaveApplication = LeaveApplication::findOrFail($id);

        return view('filament.pages.leave_application.print', compact('leaveApplication'));

    }
}
