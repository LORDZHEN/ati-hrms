<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// FILE: database/migrations/xxxx_xx_xx_add_annex_b_c_totals_to_salns_table.php
// Run: php artisan migrate
// This migration adds Annex B & C total columns to the salns table
// and creates the 8 new Annex B & C detail tables.

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Add Annex B & C total columns to salns ─────────────────────────
        Schema::table('salns', function (Blueprint $table) {
            $table->decimal('annex_b_total_assets', 15, 2)->default(0)->nullable()->after('net_worth');
            $table->decimal('annex_b_total_liabilities', 15, 2)->default(0)->nullable()->after('annex_b_total_assets');
            $table->decimal('annex_b_net_worth', 15, 2)->default(0)->nullable()->after('annex_b_total_liabilities');

            $table->decimal('annex_c_total_assets', 15, 2)->default(0)->nullable()->after('annex_b_net_worth');
            $table->decimal('annex_c_total_liabilities', 15, 2)->default(0)->nullable()->after('annex_c_total_assets');
            $table->decimal('annex_c_net_worth', 15, 2)->default(0)->nullable()->after('annex_c_total_liabilities');
        });

        // ── 2. ANNEX B — Declarant's Exclusive Properties ─────────────────────

        Schema::create('saln_annex_b_real_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saln_id')->constrained('salns')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->string('kind')->nullable();
            $table->string('exact_location')->nullable();
            $table->decimal('assessed_value', 15, 2)->nullable();
            $table->decimal('current_fair_market_value', 15, 2)->nullable();
            $table->unsignedSmallInteger('acquisition_year')->nullable();
            $table->string('mode_of_acquisition')->nullable();
            $table->decimal('acquisition_cost', 15, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('saln_annex_b_personal_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saln_id')->constrained('salns')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('year_acquired')->nullable();
            $table->decimal('acquisition_cost', 15, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('saln_annex_b_liabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saln_id')->constrained('salns')->cascadeOnDelete();
            $table->string('nature')->nullable();
            $table->string('name_of_creditors')->nullable();
            $table->decimal('outstanding_balance', 15, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('saln_annex_b_business_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saln_id')->constrained('salns')->cascadeOnDelete();
            $table->string('name_of_entity')->nullable();
            $table->text('business_address')->nullable();
            $table->string('nature_of_business_interest')->nullable();
            $table->date('date_of_acquisition')->nullable();
            $table->timestamps();
        });

        // ── 3. ANNEX C — Spouse & Children's Exclusive Properties ─────────────

        Schema::create('saln_annex_c_real_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saln_id')->constrained('salns')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->string('kind')->nullable();
            $table->string('exact_location')->nullable();
            $table->decimal('assessed_value', 15, 2)->nullable();
            $table->decimal('current_fair_market_value', 15, 2)->nullable();
            $table->unsignedSmallInteger('acquisition_year')->nullable();
            $table->string('mode_of_acquisition')->nullable();
            $table->decimal('acquisition_cost', 15, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('saln_annex_c_personal_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saln_id')->constrained('salns')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('year_acquired')->nullable();
            $table->decimal('acquisition_cost', 15, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('saln_annex_c_liabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saln_id')->constrained('salns')->cascadeOnDelete();
            $table->string('nature')->nullable();
            $table->string('name_of_creditors')->nullable();
            $table->decimal('outstanding_balance', 15, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('saln_annex_c_business_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saln_id')->constrained('salns')->cascadeOnDelete();
            $table->string('name_of_entity')->nullable();
            $table->text('business_address')->nullable();
            $table->string('nature_of_business_interest')->nullable();
            $table->date('date_of_acquisition')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Drop Annex C tables
        Schema::dropIfExists('saln_annex_c_business_interests');
        Schema::dropIfExists('saln_annex_c_liabilities');
        Schema::dropIfExists('saln_annex_c_personal_properties');
        Schema::dropIfExists('saln_annex_c_real_properties');

        // Drop Annex B tables
        Schema::dropIfExists('saln_annex_b_business_interests');
        Schema::dropIfExists('saln_annex_b_liabilities');
        Schema::dropIfExists('saln_annex_b_personal_properties');
        Schema::dropIfExists('saln_annex_b_real_properties');

        // Drop total columns from salns
        Schema::table('salns', function (Blueprint $table) {
            $table->dropColumn([
                'annex_b_total_assets', 'annex_b_total_liabilities', 'annex_b_net_worth',
                'annex_c_total_assets', 'annex_c_total_liabilities', 'annex_c_net_worth',
            ]);
        });
    }
};
