<?php
/**
 * BIOMETRIC IMPORT DEBUGGER
 * ─────────────────────────────────────────────────────────────────
 * DROP THIS FILE into:  C:\xampp\htdocs\ati-hrms\public\biometric_debug.php
 * Then visit:           http://127.0.0.1:8000/biometric_debug.php
 *
 * DELETE THIS FILE after debugging is done — it bypasses all auth.
 * ─────────────────────────────────────────────────────────────────
 */

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->boot();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

header('Content-Type: text/html; charset=utf-8');
echo '<pre style="font-family:monospace;font-size:13px;padding:20px">';
echo "╔══════════════════════════════════════════════════════╗\n";
echo "║        BIOMETRIC IMPORT DIAGNOSTIC REPORT           ║\n";
echo "╚══════════════════════════════════════════════════════╝\n\n";

// ── 1. DB: All employees with their employee_id ─────────────────
echo "━━━ [1] DB: Users with role=employee ━━━━━━━━━━━━━━━━━━━━━━━\n";
try {
    $users = DB::table('users')
        ->where('role', 'employee')
        ->select('id', 'name', 'employee_id')
        ->orderBy('id')
        ->get();

    if ($users->isEmpty()) {
        echo "  ⚠️  NO users with role='employee' found!\n";
        echo "  Check: SELECT DISTINCT role FROM users;\n";

        // Show all distinct roles
        $roles = DB::table('users')->select('role')->distinct()->get();
        echo "  Roles in DB: " . implode(', ', $roles->pluck('role')->toArray()) . "\n";
    } else {
        foreach ($users as $u) {
            $eid = $u->employee_id;
            $eidHex = $eid ? bin2hex($eid) : 'NULL';
            $eidLen = $eid ? strlen($eid) : 0;
            echo sprintf(
                "  id=%-4s  employee_id=%-12s  len=%-3s  hex=%-20s  name=%s\n",
                $u->id,
                var_export($eid, true),
                $eidLen,
                $eidHex,
                $u->name
            );
        }
    }
} catch (\Exception $e) {
    echo "  ERROR: " . $e->getMessage() . "\n";
}

// ── 2. Biometric upload disk: what files are there? ─────────────
echo "\n━━━ [2] biometric_upload disk contents ━━━━━━━━━━━━━━━━━━━━━\n";
try {
    $diskRoot = Storage::disk('biometric_upload')->path('');
    echo "  Disk root: {$diskRoot}\n";

    $files = Storage::disk('biometric_upload')->files('/');
    if (empty($files)) {
        echo "  ⚠️  NO files on biometric_upload disk.\n";
    } else {
        foreach ($files as $file) {
            $fullPath = Storage::disk('biometric_upload')->path($file);
            $size     = Storage::disk('biometric_upload')->size($file);
            $modified = date('Y-m-d H:i:s', Storage::disk('biometric_upload')->lastModified($file));
            echo "  FILE: {$file}\n";
            echo "        full_path : {$fullPath}\n";
            echo "        size      : {$size} bytes\n";
            echo "        modified  : {$modified}\n";
        }
    }
} catch (\Exception $e) {
    echo "  ERROR: " . $e->getMessage() . "\n";
}

// ── 3. Read the latest uploaded file and check IDs ─────────────
echo "\n━━━ [3] Latest uploaded CSV — unique EmployeeIDs ━━━━━━━━━━━\n";
try {
    $files = Storage::disk('biometric_upload')->files('/');
    if (!empty($files)) {
        // Sort by modified time, newest first
        usort($files, fn($a, $b) =>
            Storage::disk('biometric_upload')->lastModified($b) -
            Storage::disk('biometric_upload')->lastModified($a)
        );
        $latestFile     = $files[0];
        $latestFullPath = Storage::disk('biometric_upload')->path($latestFile);

        echo "  Reading: {$latestFile}\n\n";

        $handle = fopen($latestFullPath, 'r');
        $header = fgetcsv($handle);
        echo "  Headers: " . implode(' | ', $header) . "\n\n";

        // Find EmployeeID column index
        $eidCol = array_search('EmployeeID', $header);
        if ($eidCol === false) {
            // Try case-insensitive
            foreach ($header as $i => $h) {
                if (strtolower(trim($h)) === 'employeeid') {
                    $eidCol = $i;
                    break;
                }
            }
        }

        if ($eidCol === false) {
            echo "  ⚠️  Cannot find EmployeeID column!\n";
        } else {
            $ids        = [];
            $totalRows  = 0;
            while (($row = fgetcsv($handle)) !== false) {
                if (empty($row) || !isset($row[$eidCol])) continue;
                $id = trim(str_replace(["\r","\n","\xEF\xBB\xBF"], '', $row[$eidCol]));
                if ($id === '') continue;
                $totalRows++;
                $ids[$id] = ($ids[$id] ?? 0) + 1;
            }
            fclose($handle);

            echo "  Total data rows : {$totalRows}\n";
            echo "  Unique IDs found: " . count($ids) . "\n";
            echo "  ID list:\n";
            ksort($ids, SORT_NATURAL);
            foreach ($ids as $id => $count) {
                echo sprintf("    %-10s  (%d rows)  hex: %s\n", $id, $count, bin2hex($id));
            }
        }
    } else {
        echo "  ⚠️  No files to read.\n";
    }
} catch (\Exception $e) {
    echo "  ERROR: " . $e->getMessage() . "\n";
}

// ── 4. Match simulation ─────────────────────────────────────────
echo "\n━━━ [4] Match simulation (CSV IDs vs DB employee_ids) ━━━━━━━\n";
try {
    $files = Storage::disk('biometric_upload')->files('/');
    if (!empty($files)) {
        usort($files, fn($a, $b) =>
            Storage::disk('biometric_upload')->lastModified($b) -
            Storage::disk('biometric_upload')->lastModified($a)
        );
        $latestFullPath = Storage::disk('biometric_upload')->path($files[0]);

        // Get CSV IDs
        $handle  = fopen($latestFullPath, 'r');
        $header  = fgetcsv($handle);
        $eidCol  = 0;
        foreach ($header as $i => $h) {
            if (strtolower(trim($h)) === 'employeeid') { $eidCol = $i; break; }
        }
        $csvIds = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (!isset($row[$eidCol])) continue;
            $id = trim(str_replace(["\r","\n","\xEF\xBB\xBF"], '', $row[$eidCol]));
            if ($id !== '') $csvIds[$id] = true;
        }
        fclose($handle);

        // Get DB IDs
        $dbUsers = DB::table('users')
            ->where('role', 'employee')
            ->whereNotNull('employee_id')
            ->where('employee_id', '!=', '')
            ->select('id', 'name', 'employee_id')
            ->get();

        $matched = 0;
        echo "  Checking " . count($csvIds) . " CSV IDs against " . $dbUsers->count() . " DB employees...\n\n";

        foreach ($dbUsers as $u) {
            $dbId    = trim((string) $u->employee_id);
            $inCsv   = isset($csvIds[$dbId]);
            $status  = $inCsv ? '✅ MATCH' : '❌ no match';
            echo "  DB employee_id={$dbId}  name={$u->name}  → {$status}\n";
            if ($inCsv) $matched++;
        }

        echo "\n  RESULT: {$matched} / {$dbUsers->count()} employees would be detected.\n";

        if ($matched === 0) {
            echo "\n  ⚠️  ZERO MATCHES. Possible causes:\n";
            echo "     a) DB employee_id values don't match biometric device IDs (e.g. 'EMP001' vs '1')\n";
            echo "     b) File on disk is a stale old upload (deploy the new BiometricImportAction fix)\n";
            echo "     c) The user role in DB is not exactly 'employee'\n";
        }
    }
} catch (\Exception $e) {
    echo "  ERROR: " . $e->getMessage() . "\n";
}

// ── 5. Check which BiometricImportAction is loaded ──────────────
echo "\n━━━ [5] BiometricImportAction file on disk ━━━━━━━━━━━━━━━━━\n";
$actionPath = base_path('app/Filament/Resources/DailyTimeRecordResource/Actions/BiometricImportAction.php');
if (file_exists($actionPath)) {
    $mtime = date('Y-m-d H:i:s', filemtime($actionPath));
    echo "  Path     : {$actionPath}\n";
    echo "  Modified : {$mtime}\n";
    // Check if it has the unique filename fix
    $src = file_get_contents($actionPath);
    $hasUniqueName = str_contains($src, 'getUploadedFileNameForStorageUsing');
    $hasFullPath   = str_contains($src, 'resolveFullBiometricPath');
    $hasOldBasename = str_contains($src, "basename(\$filename)") || str_contains($src, 'basename($filename)');
    echo "  Has unique filename fix    : " . ($hasUniqueName ? '✅ YES' : '❌ NO — old version') . "\n";
    echo "  Has resolveFullBiometricPath: " . ($hasFullPath   ? '✅ YES' : '❌ NO — old version') . "\n";
    echo "  Has OLD basename() bug     : " . ($hasOldBasename ? '⚠️  YES — still old code!' : '✅ NO') . "\n";
} else {
    echo "  ⚠️  File not found at expected path!\n";
}

// ── 6. BiometricLogParser file check ───────────────────────────
echo "\n━━━ [6] BiometricLogParser file on disk ━━━━━━━━━━━━━━━━━━━━\n";
$parserPath = base_path('app/Services/BiometricLogParser.php');
if (file_exists($parserPath)) {
    $mtime = date('Y-m-d H:i:s', filemtime($parserPath));
    echo "  Path     : {$parserPath}\n";
    echo "  Modified : {$mtime}\n";
    $src = file_get_contents($parserPath);
    $hasPhpMatch = str_contains($src, 'dbLookup') && str_contains($src, 'keyBy');
    echo "  Has PHP-side match fix : " . ($hasPhpMatch ? '✅ YES' : '❌ NO — old SQL version') . "\n";
} else {
    echo "  ⚠️  File not found!\n";
}

echo "\n";
echo "━━━ END OF REPORT ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo '</pre>';