<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('locator_slips', function (Blueprint $table) {
            // Make time fields nullable since they'll be filled manually by HR
            $table->time('out_time')->nullable()->change();
            $table->time('in_time')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locator_slips', function (Blueprint $table) {
            $table->time('out_time')->nullable(false)->change();
            $table->time('in_time')->nullable(false)->change();
        });
    }
};
