<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_transaction_histories_table
 *
 * Creates a centralized, database-backed activity log for all HRMS transactions.
 * This replaces the ad-hoc buildRecentActivities() function on the dashboard
 * with a proper, queryable, indexed audit trail.
 *
 * Run with: php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_histories', function (Blueprint $table) {
            $table->id();

            // ── Actor ──────────────────────────────────────────────────────────
            // Nullable so system-generated entries (e.g. scheduled imports) still work.
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Denormalized display name — avoids a join on every listing query.
            $table->string('employee_name', 255)->index();

            // ── Classification ─────────────────────────────────────────────────
            // Human-readable label:  "Leave Application", "Travel Order", etc.
            $table->string('transaction_type', 100)->index();

            // Module slug for filtering:  Leave | Travel | Locator | SALN | PDS | Employee | DTR
            $table->string('module', 60)->index();

            // ── Content ────────────────────────────────────────────────────────
            // Full description of what happened, e.g. "Filed a sick leave for 3 days".
            $table->text('description');

            // Workflow status:  pending | approved | rejected | filed | uploaded | registered
            $table->string('status', 60)->default('pending')->index();

            // ── UI Metadata ────────────────────────────────────────────────────
            // Heroicon name used by the timeline UI, e.g. "heroicon-o-calendar".
            $table->string('icon', 100)->nullable();

            // Tailwind color key for badge / icon background, e.g. "blue", "amber".
            $table->string('color', 40)->nullable();

            // ── Back-reference to the originating record ───────────────────────
            $table->unsignedBigInteger('record_id')->nullable();

            // Direct Filament resource URL for the "View Record" link.
            $table->string('record_url', 500)->nullable();

            $table->timestamps();

            // ── Performance indexes ─────────────────────────────────────────────
            $table->index(['module', 'status']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_histories');
    }
};
