<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeaveApplicationPrintController;
use App\Http\Controllers\LocatorSlipPrintController;
use App\Http\Controllers\SalnPrintController;
use App\Http\Controllers\TravelOrderPrintController;
use App\Models\User;
use App\Models\LeaveApplication;
use App\Models\TravelOrder;

/*
|--------------------------------------------------------------------------
| Print Routes
|--------------------------------------------------------------------------
*/
Route::get('/travel-order/{travelOrder}/print', [TravelOrderPrintController::class, 'print'])
    ->name('travel-order.print');

Route::get('/locator-slip/print/{id}', [LocatorSlipPrintController::class, 'print'])
    ->name('locator_slip.print');

Route::get('/leave-application/print/{id}', [LeaveApplicationPrintController::class, 'print'])
    ->name('leave_application.print');

Route::get('/saln/{saln}/print', [SalnPrintController::class, 'print'])
    ->name('saln.print');

/*
|--------------------------------------------------------------------------
| Default Welcome Page
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('filament.hrms.auth.login');
});

/*
|--------------------------------------------------------------------------
| Employee Report Route
|--------------------------------------------------------------------------
*/
Route::get('/admin/employee-report', function () {
    $user = auth()->user();
    if (!$user || $user->role !== 'admin') {
        abort(403, 'Unauthorized');
    }

    $status = request('status'); // optional filter
    $query = User::where('role', 'employee');

    if ($status) {
        $query->where('status', $status);
    }

    $employees = $query->get();

    // Use dot notation for subfolder 'reports'
    return view('reports.employee-report', compact('employees', 'status'));
})->middleware(['auth'])->name('employee.report');

Route::get('/leave-applications/report', function () {
    $leaveApplications = LeaveApplication::all();
    return view('reports.leave-application-report', compact('leaveApplications'));
})->name('leave-applications.report');

Route::get('/locator-slip/report', function () {
    $locatorSlips = \App\Models\LocatorSlip::all();
    return view('reports.locator-slip-report', compact('locatorSlips'));
})->name('locator-slip.report');

Route::get('/travel-orders/report', function () {
    $travelOrders = TravelOrder::all(); // Fetch all travel orders
    return view('reports.travel-order-report', compact('travelOrders'));
})->middleware(['auth'])->name('travel-order.report');

Route::get('/saln/report', function () {
    $salns = \App\Models\Saln::with('user')->get();
    return view('reports.saln-report', compact('salns'));
})->middleware(['auth'])->name('saln.report');
