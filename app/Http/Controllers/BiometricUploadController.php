<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BiometricUploadController extends Controller
{
    public function store(Request $request)
    {
        if (!$request->hasFile('biometric_csv')) {
            return response()->json(['error' => 'No file received'], 422);
        }

        $file = $request->file('biometric_csv');

        if (!$file->isValid()) {
            return response()->json(['error' => 'File upload failed: ' . $file->getErrorMessage()], 422);
        }

        $contents  = file_get_contents($file->getRealPath());
        $firstLine = strtok($contents, "\n");

        if (empty($firstLine) || (strpos($firstLine, ',') === false && strpos($firstLine, ';') === false)) {
            return response()->json(['error' => 'File does not appear to be a valid CSV'], 422);
        }

        // Save to storage/app/biometric_imports/ (disk local root = storage/app/)
        $stableRelative = 'biometric_imports/bio_' . now()->format('YmdHis') . '_' . uniqid() . '.csv';
        Storage::disk('local')->put($stableRelative, $contents);
        $stableAbsolute = Storage::disk('local')->path($stableRelative);

        Log::info('[BiometricUpload] File saved via direct upload', [
            'to'     => $stableAbsolute,
            'exists' => file_exists($stableAbsolute),
            'size'   => file_exists($stableAbsolute) ? filesize($stableAbsolute) : 0,
        ]);

        return response()->json([
            'path'     => $stableAbsolute,
            'filename' => $file->getClientOriginalName(),
            'size'     => $file->getSize(),
        ]);
    }
}