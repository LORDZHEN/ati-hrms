<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveApplication;

class LeaveApplicationPrintController extends Controller
{
    public function print($id)
    {
        $leaveApplication = LeaveApplication::findOrFail($id);

        return view('leave_application.print', compact('leaveApplication'));
    }
}
