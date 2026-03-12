<?php

// ═══════════════════════════════════════════════════════════════════════════
// FILE: app/Http/Controllers/BiometricXlsUploadController.php
// ═══════════════════════════════════════════════════════════════════════════

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Handles the AJAX upload of a ZKTeco/BioTime XLS attendance export.
 *
 * The file is stored in storage/app/biometric_imports/ under a unique name.
 * The full absolute path is returned to the frontend so BiometricImportAction
 * can find it again during the Scan and Submit steps.
 *
 * ROUTE (add to routes/web.php):
 *   Route::post('/biometric/upload-xls', [BiometricXlsUploadController::class, 'store'])
 *       ->name('biometric.upload-xls')
 *       ->middleware(['web', 'auth']);
 */
class BiometricXlsUploadController extends Controller
{
    public function store(Request $request)
    {
        // Only admins may upload
        if (!Auth::user()?->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate: accept xls and xlsx only, max 20 MB
        $request->validate([
            'biometric_xls' => ['required', 'file', 'mimes:xls,xlsx', 'max:20480'],
        ]);

        $file = $request->file('biometric_xls');

        // Store with a predictable prefix so findNewestXlsImport() can glob it
        $filename = 'bio_' . now()->format('YmdHis') . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $directory = 'biometric_imports';

        // Ensure the directory exists (Storage::disk('local') → storage/app/)
        Storage::disk('local')->makeDirectory($directory);

        $storedPath = Storage::disk('local')->putFileAs($directory, $file, $filename);
        $fullPath   = Storage::disk('local')->path($storedPath);

        Log::info('[BiometricXlsUpload] File stored', [
            'original' => $file->getClientOriginalName(),
            'stored'   => $fullPath,
            'size'     => $file->getSize(),
        ]);

        return response()->json([
            'path'     => $fullPath,
            'filename' => $filename,
        ]);
    }
}


// ═══════════════════════════════════════════════════════════════════════════
// ROUTE REGISTRATION  (add these lines to routes/web.php)
// ═══════════════════════════════════════════════════════════════════════════
//
// use App\Http\Controllers\BiometricXlsUploadController;
//
// Route::post('/biometric/upload-xls', [BiometricXlsUploadController::class, 'store'])
//     ->name('biometric.upload-xls')
//     ->middleware(['web', 'auth']);
//
// ═══════════════════════════════════════════════════════════════════════════


// ═══════════════════════════════════════════════════════════════════════════
// COMPOSER DEPENDENCY  (run this in your project root)
// ═══════════════════════════════════════════════════════════════════════════
//
//   composer require phpoffice/phpspreadsheet
//
// PhpSpreadsheet reads both .xls (via PhpSpreadsheet's Xls reader) and
// .xlsx natively — no extra extensions needed beyond what Laravel already
// requires (php-xml, php-zip).
//
// ═══════════════════════════════════════════════════════════════════════════


// ═══════════════════════════════════════════════════════════════════════════
// SERVICE PROVIDER BINDING  (AppServiceProvider.php  → register())
// ═══════════════════════════════════════════════════════════════════════════
//
// use App\Services\XlsLogParser;
//
// $this->app->singleton(XlsLogParser::class);
//
// (Optional — Laravel resolves it automatically from the container,
//  but binding as singleton avoids re-loading the class on each call.)
//
// ═══════════════════════════════════════════════════════════════════════════


// ═══════════════════════════════════════════════════════════════════════════
// SUMMARY OF CHANGES vs. THE OLD CSV-BASED APPROACH
// ═══════════════════════════════════════════════════════════════════════════
//
// OLD:  BiometricImportAction  →  BiometricLogParser  (CSV row-by-row)
//       route: biometric.upload  (accepts .csv / .txt)
//
// NEW:  BiometricImportAction  →  XlsLogParser        (XLS Logs sheet)
//       route: biometric.upload-xls  (accepts .xls / .xlsx)
//
// The DtrCalculator and the DTR CSV output format are UNCHANGED.
// The EmployeeDtr model, notifications, and PDF generation are UNCHANGED.
// The DailyTimeRecordResource table/filters/PDF actions are UNCHANGED.
//
// Files to add/replace:
//   app/Services/XlsLogParser.php                        ← NEW
//   app/Filament/…/Actions/BiometricImportAction.php     ← REPLACED
//   app/Http/Controllers/BiometricXlsUploadController.php ← NEW (replaces old CSV controller)
//   routes/web.php                                        ← add upload-xls route
//
// ═══════════════════════════════════════════════════════════════════════════
