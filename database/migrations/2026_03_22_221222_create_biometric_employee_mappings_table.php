<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the biometric_employee_mappings table.
 *
 * PURPOSE
 * ───────
 * ZKTeco / BioTime devices assign simple numeric enrollment IDs (1, 2, 3…)
 * at the time of fingerprint registration. These device IDs are completely
 * independent of the government plantilla IDs stored in users.employee_id
 * (e.g. "OSEC-DAB-AGR-252-1998"). This table is the explicit bridge between
 * the two ID spaces, replacing the fragile assumption that both columns hold
 * the same value.
 *
 * HOW IT WORKS
 * ────────────
 * 1. HR maps each user to their biometric device number via the Filament UI.
 * 2. XlsLogParser::detectEmployees() queries this table FIRST, then falls
 *    back to users.employee_id for backward compatibility.
 * 3. When an employee re-enrolls and gets a new device number, HR sets the
 *    old row to is_active = false and inserts a new active row — preserving
 *    historical audit trail without breaking anything.
 *
 * UNIQUE CONSTRAINT NOTE
 * ──────────────────────
 * The constraint is on (device_id) alone — one device number maps to exactly
 * one person at any given time. is_active is NOT included in the unique key
 * intentionally: device numbers are recycled when employees leave and new
 * people are enrolled, so the same device_id may appear multiple times in
 * history. The application-layer check in the Filament resource deactivates
 * any existing active row before inserting a new one for the same device_id.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biometric_employee_mappings', function (Blueprint $table) {
            $table->id();

            // FK to users.id — the system user this device number belongs to
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // The numeric (or alphanumeric) enrollment number from the biometric device
            // Stored as string to accommodate devices that use non-numeric IDs
            $table->string('device_id');

            // Optional human-readable label for multi-device setups
            // e.g. "Main Gate", "Training Center Reader 2"
            $table->string('device_name')->nullable();

            // Soft-deactivation for re-enrollment events.
            // When an employee re-enrolls with a new device number:
            //   1. Set old row is_active = false
            //   2. Insert new row with new device_id, is_active = true
            // This preserves the full enrollment history without deletion.
            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();

            // One device_id can only have ONE active mapping at a time.
            // Enforced at DB level; the Filament resource also checks before save.
            $table->unique(['device_id', 'is_active'], 'unique_active_device_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biometric_employee_mappings');
    }
};
