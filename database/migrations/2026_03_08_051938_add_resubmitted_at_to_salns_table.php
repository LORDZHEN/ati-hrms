<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('salns', function (Blueprint $table) {
            // Dedicated resubmission timestamp — null means never resubmitted (original filing only).
            // Used by SalnResource table to show "Last Resubmitted" and the resubmitted badge.
            $table->timestamp('resubmitted_at')->nullable()->after('remarks');
        });
    }

    public function down(): void
    {
        Schema::table('salns', function (Blueprint $table) {
            $table->dropColumn('resubmitted_at');
        });
    }
};
