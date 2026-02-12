<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RevertLocationIdsToStringsOnUsersTable extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Drop foreign keys if they exist
            try {
                $table->dropForeign(['region_id']);
            } catch (\Throwable $e) {}

            try {
                $table->dropForeign(['province_id']);
            } catch (\Throwable $e) {}

            try {
                $table->dropForeign(['city_id']);
            } catch (\Throwable $e) {}

            try {
                $table->dropForeign(['barangay_id']);
            } catch (\Throwable $e) {}

            // Change columns to string
            $table->string('region_id')->nullable()->change();
            $table->string('province_id')->nullable()->change();
            $table->string('city_id')->nullable()->change();
            $table->string('barangay_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->unsignedBigInteger('region_id')->nullable()->change();
            $table->unsignedBigInteger('province_id')->nullable()->change();
            $table->unsignedBigInteger('city_id')->nullable()->change();
            $table->unsignedBigInteger('barangay_id')->nullable()->change();
        });
    }
}
