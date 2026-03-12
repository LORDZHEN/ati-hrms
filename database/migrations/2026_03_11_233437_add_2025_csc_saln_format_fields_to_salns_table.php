<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add 2025 CSC SALN format fields
 *
 * Changes from 2015 → 2025 format:
 *  - Add compliance type fields (assumption / annual / exit)
 *  - Add multiple marriages fields
 *  - Remove has_business_interests (only "no" checkbox kept)
 *  - Remove has_relatives_in_government (only "no" checkbox kept)
 *  - saln_children: date_of_birth no longer required (kept for backward compat)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('salns', function (Blueprint $table) {
            // ── Compliance type (2025 new) ────────────────────────────────────
            $table->boolean('compliance_assumption')->default(false)->after('user_id');
            $table->boolean('compliance_annual')->default(true)->after('compliance_assumption');
            $table->boolean('compliance_exit')->default(false)->after('compliance_annual');

            // ── Multiple marriages (2025 new) ─────────────────────────────────
            $table->text('multiple_marriages_names')->nullable()->after('not_applicable');
            $table->boolean('multiple_marriages_not_applicable')->default(true)->after('multiple_marriages_names');

            // ── Remove old "has" flags (now only "no" checkbox exists in 2025) ─
            // We keep has_business_interests / has_relatives_in_government columns
            // for backward compatibility but stop writing to them.
            // Uncomment the lines below only after confirming no existing data depends on them:
            // $table->dropColumn('has_business_interests');
            // $table->dropColumn('has_relatives_in_government');
        });
    }

    public function down(): void
    {
        Schema::table('salns', function (Blueprint $table) {
            $table->dropColumn([
                'compliance_assumption',
                'compliance_annual',
                'compliance_exit',
                'multiple_marriages_names',
                'multiple_marriages_not_applicable',
            ]);
        });
    }
};
