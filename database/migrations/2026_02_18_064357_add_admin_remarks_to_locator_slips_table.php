<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('locator_slips', function (Blueprint $table) {
            $table->text('admin_remarks')->nullable()->after('approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('locator_slips', function (Blueprint $table) {
            $table->dropColumn('admin_remarks');
        });
    }
};
