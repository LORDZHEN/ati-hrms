<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeaveApplicationPrintController;
use App\Http\Controllers\LocatorSlipPrintController;
use App\Http\Controllers\SalnPrintController;
use App\Http\Controllers\TravelOrderPrintController;
use App\Models\User;
use App\Models\LeaveApplication;
use App\Models\TravelOrder;
use App\Models\LocatorSlip;
use App\Http\Controllers\PersonalDataSheetPrintController;
use App\Models\PersonalDataSheet;
use Carbon\Carbon;
use App\Livewire\Employee\Pds\EditPds;

Route::middleware(['auth'])->group(function () {
    Route::get('/pds/edit', function () {
        return view('pds.edit');
    })->name('pds.edit');
});


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

Route::get('/pds/{pds}/print', [PersonalDataSheetPrintController::class, 'print'])
    ->name('pds.print')
    ->middleware(['auth']);

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

    $status = request('status');
    $from = request('from');
    $to = request('to');
    $period = request('period');

    $query = User::where('role', 'employee');

    if ($status) {
        $query->where('status', $status);
    }

    if ($from && $to) {
        $query->whereBetween('created_at', [
            \Carbon\Carbon::parse($from)->startOfDay(),
            \Carbon\Carbon::parse($to)->endOfDay(),
        ]);
    }

    $employees = $query
        ->orderBy('last_name')
        ->get();

    return view('reports.employee-report', compact(
        'employees',
        'status',
        'from',
        'to',
        'period'
    ));
})
    ->middleware(['auth'])
    ->name('employee.report');


Route::get('/leave-applications/report', function () {
    $user = auth()->user();
    if (!$user || $user->role !== 'admin') {
        abort(403, 'Unauthorized');
    }

    $from = request('from');
    $to = request('to');
    $period = request('period');
    $status = request('status'); // <-- add this line

    $query = LeaveApplication::with('employee');

    if ($from && $to) {
        $query->whereBetween('created_at', [
            \Carbon\Carbon::parse($from)->startOfDay(),
            \Carbon\Carbon::parse($to)->endOfDay(),
        ]);
    }

    // Filter by status if not "All"
    if ($status && $status !== 'all') {
        $query->where('status', $status);
    }

    $leaveApplications = $query->orderBy('created_at', 'desc')->get();

    // Pass $status to the view
    return view('reports.leave-application-report', compact(
        'leaveApplications',
        'from',
        'to',
        'period',
        'status'  // <-- pass it here
    ));
})
    ->middleware(['auth'])
    ->name('leave-applications.report');

Route::get('/locator-slip/report', function () {
    $user = auth()->user();
    if (!$user || $user->role !== 'admin') {
        abort(403, 'Unauthorized');
    }

    $from = request('from');
    $to = request('to');
    $period = request('period');
    $status = request('status') ?? 'all';

    $query = LocatorSlip::query();

    if ($from && $to) {
        $query->whereBetween('created_at', [
            Carbon::parse($from)->startOfDay(),
            Carbon::parse($to)->endOfDay(),
        ]);
    }

    // Filter by status
    if ($status && $status !== 'all') {
        $query->where('status', $status);
    }

    $locatorSlips = $query->orderBy('created_at', 'desc')->get();

    // Pass status to blade
    return view('reports.locator-slip-report', compact(
        'locatorSlips',
        'from',
        'to',
        'period',
        'status'
    ));
})->middleware(['auth'])->name('locator-slip.report');

Route::get('/pds/report', function () {
    $user = auth()->user();
    if (!$user || $user->role !== 'admin')
        abort(403);

    $from = request('from');
    $to = request('to');
    $period = request('period');

    $query = PersonalDataSheet::query()->with('employee');

    if ($from && $to) {
        $query->whereBetween('created_at', [
            \Carbon\Carbon::parse($from)->startOfDay(),
            \Carbon\Carbon::parse($to)->endOfDay(),
        ]);
    }

    $personalDataSheets = $query->orderBy('created_at', 'desc')->get();

    return view('reports.pds-report', compact('personalDataSheets', 'from', 'to', 'period'));
})->middleware(['auth'])->name('pds.report');


Route::get('/travel-orders/report', function () {
    $user = auth()->user();
    if (!$user || $user->role !== 'admin') {
        abort(403);
    }

    $from = request('from');
    $to = request('to');
    $period = request('period');
    $status = request('status') ?? 'all';

    $query = TravelOrder::query();

    if ($from && $to) {
        $query->whereBetween('departure_date', [
            Carbon::parse($from)->startOfDay(),
            Carbon::parse($to)->endOfDay(),
        ]);
    }

    if ($status && $status !== 'all') {
        $query->where('status', $status);
    }

    $travelOrders = $query->orderBy('departure_date', 'desc')->get();

    return view('reports.travel-order-report', compact(
        'travelOrders',
        'from',
        'to',
        'period',
        'status'
    ));
})->middleware(['auth'])->name('travel-order.report');


// SALN Comprehensive Report Route
Route::get('/saln/report', function () {
    $user = auth()->user();
    if (!$user || $user->role !== 'admin')
        abort(403);

    $from = request('from');
    $to = request('to');
    $period = request('period');

    $query = \App\Models\Saln::with('user');

    if ($from && $to) {
        $query->whereBetween('as_of_date', [
            \Carbon\Carbon::parse($from)->startOfDay(),
            \Carbon\Carbon::parse($to)->endOfDay(),
        ]);
    }

    $salns = $query->orderBy('as_of_date', 'desc')->get();

    return view('reports.saln-report', compact('salns', 'from', 'to', 'period'));
})
    ->middleware(['auth'])
    ->name('saln.report');


