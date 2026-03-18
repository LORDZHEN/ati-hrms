<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Add 'wellness_leave' to the leave_applications.type_of_leave column.
 *
 * SAFE TO RUN — existing records are untouched.
 *
 * If your column is VARCHAR / TEXT (the most common Filament setup),
 * this migration is a no-op and you can skip it — the new value will
 * save automatically.
 *
 * If your column is an ENUM, this migration extends it to include
 * 'wellness_leave' without touching any existing rows.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Check current column type ─────────────────────────────────────
        $columnType = DB::selectOne("
            SELECT COLUMN_TYPE
            FROM   INFORMATION_SCHEMA.COLUMNS
            WHERE  TABLE_SCHEMA = DATABASE()
              AND  TABLE_NAME   = 'leave_applications'
              AND  COLUMN_NAME  = 'type_of_leave'
        ");

        if (!$columnType) {
            return; // Column doesn't exist — nothing to do
        }

        $type = strtolower($columnType->COLUMN_TYPE ?? '');

        // Only modify if it is actually an ENUM
        if (!str_starts_with($type, 'enum')) {
            // VARCHAR / TEXT — no change needed, value saves as-is
            return;
        }

        // ── Parse existing enum values ────────────────────────────────────
        // COLUMN_TYPE looks like: enum('vacation_leave','sick_leave',...)
        preg_match_all("/'([^']+)'/", $type, $matches);
        $existing = $matches[1] ?? [];

        if (in_array('wellness_leave', $existing, true)) {
            return; // Already present — idempotent
        }

        // Insert 'wellness_leave' before 'others' (or at the end if 'others' absent)
        $othersIdx = array_search('others', $existing, true);
        if ($othersIdx !== false) {
            array_splice($existing, $othersIdx, 0, ['wellness_leave']);
        } else {
            $existing[] = 'wellness_leave';
        }

        $enumDef = implode(', ', array_map(fn($v) => "'{$v}'", $existing));

        DB::statement("
            ALTER TABLE leave_applications
            MODIFY COLUMN type_of_leave ENUM({$enumDef}) NOT NULL
        ");
    }

    public function down(): void
    {
        // Re-read current enum
        $columnType = DB::selectOne("
            SELECT COLUMN_TYPE
            FROM   INFORMATION_SCHEMA.COLUMNS
            WHERE  TABLE_SCHEMA = DATABASE()
              AND  TABLE_NAME   = 'leave_applications'
              AND  COLUMN_NAME  = 'type_of_leave'
        ");

        if (!$columnType) return;

        $type = strtolower($columnType->COLUMN_TYPE ?? '');
        if (!str_starts_with($type, 'enum')) return;

        preg_match_all("/'([^']+)'/", $type, $matches);
        $existing = array_filter($matches[1] ?? [], fn($v) => $v !== 'wellness_leave');

        if (empty($existing)) return;

        $enumDef = implode(', ', array_map(fn($v) => "'{$v}'", $existing));

        DB::statement("
            ALTER TABLE leave_applications
            MODIFY COLUMN type_of_leave ENUM({$enumDef}) NOT NULL
        ");
    }
};
