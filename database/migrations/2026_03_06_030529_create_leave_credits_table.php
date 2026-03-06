<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the dedicated leave_credits table.
 *
 * This replaces storing balances directly on the users table.
 * Each employee has ONE row here; balances are updated in place.
 *
 * Run: php artisan migrate
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('leave_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // ── Current balances (what the employee can still use) ──────
            $table->decimal('vacation_leave_balance', 8, 3)->default(0);
            $table->decimal('sick_leave_balance', 8, 3)->default(0);
            $table->decimal('special_leave_balance', 8, 3)->default(3.000); // fixed yearly grant
            $table->decimal('mandatory_leave_balance', 8, 3)->default(5.000); // fixed yearly grant

            // ── Accrual tracking ────────────────────────────────────────
            // Records the last month credits were accrued so the
            // scheduler never double-accrues the same month.
            $table->date('last_accrual_date')->nullable();

            // ── Soft cap (optional, null = no cap) ──────────────────────
            $table->decimal('vacation_leave_max', 8, 3)->default(30.000);
            $table->decimal('sick_leave_max', 8, 3)->default(30.000);

            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_credits');
    }
};
