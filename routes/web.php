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
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Saln;

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

    // ── Auth & authorization ──────────────────────────────────────
    $user = auth()->user();
    if (!$user || $user->role !== 'admin') {
        abort(403, 'Unauthorized');
    }

    // ── Query parameters ─────────────────────────────────────────
    $status = request('status', 'all');   // all | active | pending | inactive
    $from = request('from');
    $to = request('to');
    $period = request('period', 'custom');

    // ── Resolve from/to when not explicitly provided ─────────────
    if (!$from || !$to) {
        $now = Carbon::now();

        [$from, $to] = match ($period) {
            'weekly' => [$now->copy()->startOfWeek()->toDateString(), $now->copy()->endOfWeek()->toDateString()],
            'quarterly' => [$now->copy()->startOfQuarter()->toDateString(), $now->copy()->endOfQuarter()->toDateString()],
            'yearly' => [$now->copy()->startOfYear()->toDateString(), $now->copy()->endOfYear()->toDateString()],
            default => [$now->copy()->startOfMonth()->toDateString(), $now->copy()->endOfMonth()->toDateString()], // monthly + custom fallback
        };
    }

    // ── Build employee query ──────────────────────────────────────
    $query = User::whereIn('role', ['employee', 'admin']);

    // Status filter — skip when 'all'
    if ($status && $status !== 'all') {
        $query->where('status', $status);
    }

    // Date range filter on created_at
    if ($from && $to) {
        $query->whereBetween('created_at', [
            Carbon::parse($from)->startOfDay(),
            Carbon::parse($to)->endOfDay(),
        ]);
    }

    $employees = $query->orderBy('last_name')->get();

    // ── Generate PDF ─────────────────────────────────────────────
    $pdf = Pdf::loadView('reports.employee-report', compact(
        'employees',
        'status',
        'from',
        'to',
        'period'
    ))
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,   // set true only if you embed external images
            'dpi' => 150,
        ]);

    $filename = sprintf(
        'employee-report-%s-%s.pdf',
        $status,
        Carbon::now()->format('Ymd-His')
    );

    // stream() opens inline in browser; download() forces a file save
    return $pdf->stream($filename);

})->middleware(['auth'])->name('employee.report');


Route::get('/leave-applications/report', function () {

    // ── Auth & authorization ──────────────────────────────────────────────
    $user = auth()->user();
    if (!$user || $user->role !== 'admin') {
        abort(403, 'Unauthorized');
    }

    // ── Query parameters ──────────────────────────────────────────────────
    $status = request('status', 'all');
    $from = request('from');
    $to = request('to');
    $period = request('period', 'monthly');

    // ── Resolve from/to when not provided ─────────────────────────────────
    if (!$from || !$to) {
        $now = Carbon::now();

        [$from, $to] = match ($period) {
            'weekly' => [$now->copy()->startOfWeek()->toDateString(), $now->copy()->endOfWeek()->toDateString()],
            'quarterly' => [$now->copy()->startOfQuarter()->toDateString(), $now->copy()->endOfQuarter()->toDateString()],
            'yearly' => [$now->copy()->startOfYear()->toDateString(), $now->copy()->endOfYear()->toDateString()],
            default => [$now->copy()->startOfMonth()->toDateString(), $now->copy()->endOfMonth()->toDateString()],
        };
    }

    // ── Build query ────────────────────────────────────────────────────────
    $query = LeaveApplication::with('employee')
        ->whereBetween('created_at', [
            Carbon::parse($from)->startOfDay(),
            Carbon::parse($to)->endOfDay(),
        ]);

    if ($status && $status !== 'all') {
        $query->where('status', $status);
    }

    $leaveApplications = $query->orderBy('created_at', 'desc')->get();

    // ── Generate PDF ───────────────────────────────────────────────────────
    $pdf = Pdf::loadView('reports.leave-application-report', compact(
        'leaveApplications',
        'status',
        'from',
        'to',
        'period'
    ))
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'dpi' => 150,
        ]);

    $filename = sprintf(
        'leave-report-%s-%s.pdf',
        $status,
        Carbon::now()->format('Ymd-His')
    );

    return $pdf->stream($filename);

})->middleware(['auth'])->name('leave-applications.report');


Route::get('/locator-slip/report', function () {

    $user = auth()->user();
    if (!$user || $user->role !== 'admin') {
        abort(403, 'Unauthorized');
    }

    $status = request('status', 'all');
    $from = request('from');
    $to = request('to');
    $period = request('period', 'monthly');

    if (!$from || !$to) {
        $now = Carbon::now();
        [$from, $to] = match ($period) {
            'weekly' => [$now->copy()->startOfWeek()->toDateString(), $now->copy()->endOfWeek()->toDateString()],
            'quarterly' => [$now->copy()->startOfQuarter()->toDateString(), $now->copy()->endOfQuarter()->toDateString()],
            'yearly' => [$now->copy()->startOfYear()->toDateString(), $now->copy()->endOfYear()->toDateString()],
            default => [$now->copy()->startOfMonth()->toDateString(), $now->copy()->endOfMonth()->toDateString()],
        };
    }

    $query = LocatorSlip::query();

    if ($from && $to) {
        $query->where(function ($q) use ($from, $to) {
            $q->whereBetween('inclusive_date', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ])->orWhere(function ($q2) use ($from, $to) {
                $q2->whereNull('inclusive_date')
                    ->whereBetween('created_at', [
                        Carbon::parse($from)->startOfDay(),
                        Carbon::parse($to)->endOfDay(),
                    ]);
            });
        });
    }

    if ($status && $status !== 'all') {
        $query->where('status', $status);
    }

    $locatorSlips = $query->orderBy('inclusive_date', 'desc')->get();

    $pdf = Pdf::loadView('reports.locator-slip-report', compact(
        'locatorSlips',
        'status',
        'from',
        'to',
        'period'
    ))
        ->setPaper('a4', 'portrait')   // ← portrait, same as employee report
        ->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'dpi' => 150,
        ]);

    $filename = sprintf('locator-slip-report-%s-%s.pdf', $status, Carbon::now()->format('Ymd-His'));

    return $pdf->stream($filename);

})->middleware(['auth'])->name('locator-slip.report');


Route::get('/pds/report', function () {

    // ── Auth & authorization ──────────────────────────────────────────────
    $user = auth()->user();
    if (!$user || $user->role !== 'admin') {
        abort(403, 'Unauthorized');
    }

    // ── Query parameters ──────────────────────────────────────────────────
    $status = request('status', 'all');
    $from = request('from');
    $to = request('to');
    $period = request('period', 'monthly');

    // ── Resolve from/to when not explicitly provided ──────────────────────
    if (!$from || !$to) {
        $now = Carbon::now();

        [$from, $to] = match ($period) {
            'weekly' => [$now->copy()->startOfWeek()->toDateString(), $now->copy()->endOfWeek()->toDateString()],
            'quarterly' => [$now->copy()->startOfQuarter()->toDateString(), $now->copy()->endOfQuarter()->toDateString()],
            'yearly' => [$now->copy()->startOfYear()->toDateString(), $now->copy()->endOfYear()->toDateString()],
            default => [$now->copy()->startOfMonth()->toDateString(), $now->copy()->endOfMonth()->toDateString()],
        };
    }

    // ── Build query ───────────────────────────────────────────────────────
    // Filter on created_at (submission date), eager-load user for fallback name
    $query = PersonalDataSheet::query()->with('user');

    if ($from && $to) {
        $query->whereBetween('created_at', [
            Carbon::parse($from)->startOfDay(),
            Carbon::parse($to)->endOfDay(),
        ]);
    }

    if ($status && $status !== 'all') {
        $query->where('status', $status);
    }

    $personalDataSheets = $query->orderBy('created_at', 'desc')->get();

    // ── Generate PDF ──────────────────────────────────────────────────────
    $pdf = Pdf::loadView('reports.pds-report', compact(
        'personalDataSheets',
        'status',
        'from',
        'to',
        'period'
    ))
        ->setPaper('a4', 'landscape')   // landscape fits the wider table comfortably
        ->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'dpi' => 150,
        ]);

    $filename = sprintf(
        'pds-report-%s-%s.pdf',
        $status,
        Carbon::now()->format('Ymd-His')
    );

    return $pdf->stream($filename);

})->middleware(['auth'])->name('pds.report');


Route::get('/travel-orders/report', function () {

    // ── Auth & authorization ──────────────────────────────────────
    $user = auth()->user();
    if (!$user || $user->role !== 'admin') {
        abort(403, 'Unauthorized');
    }

    // ── Query parameters ─────────────────────────────────────────
    $status = request('status', 'all');
    $from = request('from');
    $to = request('to');
    $period = request('period', 'monthly');

    // ── Resolve from/to when not explicitly provided ─────────────
    if (!$from || !$to) {
        $now = Carbon::now();

        [$from, $to] = match ($period) {
            'weekly' => [$now->copy()->startOfWeek()->toDateString(), $now->copy()->endOfWeek()->toDateString()],
            'quarterly' => [$now->copy()->startOfQuarter()->toDateString(), $now->copy()->endOfQuarter()->toDateString()],
            'yearly' => [$now->copy()->startOfYear()->toDateString(), $now->copy()->endOfYear()->toDateString()],
            default => [$now->copy()->startOfMonth()->toDateString(), $now->copy()->endOfMonth()->toDateString()],
        };
    }

    // ── Build query ───────────────────────────────────────────────
    // Filter on departure_date (the actual travel date, not created_at)
    $query = TravelOrder::with(['recommender', 'approver', 'creator']);

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

    // ── Generate PDF ─────────────────────────────────────────────
    $pdf = Pdf::loadView('reports.travel-order-report', compact(
        'travelOrders',
        'status',
        'from',
        'to',
        'period'
    ))
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'dpi' => 150,
        ]);

    $filename = sprintf(
        'travel-order-report-%s-%s.pdf',
        $status,
        Carbon::now()->format('Ymd-His')
    );

    return $pdf->stream($filename);

})->middleware(['auth'])->name('travel-order.report');


// SALN Comprehensive Report Route
Route::get('/saln/report', function () {

    // ── Auth & authorization ──────────────────────────────────────────────
    $user = auth()->user();
    if (! $user || $user->role !== 'admin') {
        abort(403, 'Unauthorized');
    }

    // ── Query parameters ──────────────────────────────────────────────────
    $remarks_filter = request('remarks_filter', 'all'); // all | with_remarks | no_remarks
    $from           = request('from');
    $to             = request('to');
    $period         = request('period', 'monthly');

    // ── Resolve from/to when not explicitly provided ──────────────────────
    if (! $from || ! $to) {
        $now = Carbon::now();

        [$from, $to] = match ($period) {
            'weekly'    => [$now->copy()->startOfWeek()->toDateString(),    $now->copy()->endOfWeek()->toDateString()],
            'quarterly' => [$now->copy()->startOfQuarter()->toDateString(), $now->copy()->endOfQuarter()->toDateString()],
            'yearly'    => [$now->copy()->startOfYear()->toDateString(),     $now->copy()->endOfYear()->toDateString()],
            default     => [$now->copy()->startOfMonth()->toDateString(),    $now->copy()->endOfMonth()->toDateString()],
        };
    }

    // ── Build query ───────────────────────────────────────────────────────
    // Filter on as_of_date (the SALN declaration date, not created_at)
    $query = Saln::with(['user'])
        ->with([
            'realProperties',
            'personalProperties',
            'liabilities',
            'businessInterests',
            'relativesInGovernment',
        ]);

    if ($from && $to) {
        $query->whereBetween('as_of_date', [
            Carbon::parse($from)->startOfDay(),
            Carbon::parse($to)->endOfDay(),
        ]);
    }

    // Remarks filter
    if ($remarks_filter === 'with_remarks') {
        $query->whereNotNull('remarks')->where('remarks', '!=', '');
    } elseif ($remarks_filter === 'no_remarks') {
        $query->where(fn($q) => $q->whereNull('remarks')->orWhere('remarks', ''));
    }

    $salns = $query->orderBy('as_of_date', 'desc')->get();

    // ── Generate PDF ──────────────────────────────────────────────────────
    $pdf = Pdf::loadView('reports.saln-report', compact(
        'salns',
        'remarks_filter',
        'from',
        'to',
        'period'
    ))
    ->setPaper('a4', 'landscape')   // landscape fits the financial columns comfortably
    ->setOptions([
        'defaultFont'          => 'sans-serif',
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled'      => false,
        'dpi'                  => 150,
    ]);

    $filename = sprintf(
        'saln-report-%s-%s.pdf',
        $remarks_filter,
        Carbon::now()->format('Ymd-His')
    );

    return $pdf->stream($filename);

})->middleware(['auth'])->name('saln.report');

// Biometric CSV upload endpoint — bypasses Livewire file upload
Route::post('/biometric/upload-csv', [\App\Http\Controllers\BiometricUploadController::class, 'store'])
    ->middleware(['web', 'auth'])
    ->name('biometric.upload');

