<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the `settings` key-value table used by App\Models\Setting
     * and App\Services\FilingSeasonService.
     *
     * Seed the filing season flag to OFF by default so that no employee
     * can edit approved records until an admin explicitly enables it.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Setting identifier, e.g. filing_season_enabled');
            $table->text('value')->nullable()->comment('Stored as string; cast in PHP as needed');
            $table->timestamps();
        });

        // Seed the filing season flag to disabled (0) on first install.
        // Change to '1' here if you want it enabled by default.
        DB::table('settings')->insert([
            'key'        => 'filing_season_enabled',
            'value'      => '0',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
