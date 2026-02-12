<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | 1. Drop old columns (ONLY if you are sure you don't need them)
            |--------------------------------------------------------------------------
            */

            $table->dropColumn([
                'region_name',
                'province',
                'city_municipality',
                'barangay_name',
            ]);

            /*
            |--------------------------------------------------------------------------
            | 2. Add new foreign key columns
            |--------------------------------------------------------------------------
            */

            $table->foreignId('region_id')
                ->nullable()
                ->after('purok_street')
                ->constrained('regions')
                ->nullOnDelete();

            $table->foreignId('province_id')
                ->nullable()
                ->after('region_id')
                ->constrained('provinces')
                ->nullOnDelete();

            $table->foreignId('city_id')
                ->nullable()
                ->after('province_id')
                ->constrained('cities')
                ->nullOnDelete();

            $table->foreignId('barangay_id')
                ->nullable()
                ->after('city_id')
                ->constrained('barangays')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Drop foreign keys first
            |--------------------------------------------------------------------------
            */

            $table->dropForeign(['region_id']);
            $table->dropForeign(['province_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['barangay_id']);

            $table->dropColumn([
                'region_id',
                'province_id',
                'city_id',
                'barangay_id',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Restore old columns (rollback safety)
            |--------------------------------------------------------------------------
            */

            $table->string('region_name')->nullable();
            $table->string('province')->nullable();
            $table->string('city_municipality')->nullable();
            $table->string('barangay_name')->nullable();
        });
    }
};
