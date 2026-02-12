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
    Schema::table('personal_data_sheets', function (Blueprint $table) {
        $table->string('perm_zip')->nullable();
        $table->string('perm_tel')->nullable();
        $table->string('mobile')->nullable();
        $table->string('email')->nullable();
        // Add any other missing columns from your Blade form here
    });
}

public function down(): void
{
    Schema::table('personal_data_sheets', function (Blueprint $table) {
        $table->dropColumn([
            'perm_zip',
            'perm_tel',
            'mobile',
            'email',
        ]);
    });
}

};
