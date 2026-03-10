<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('salns', function (Blueprint $table) {
            // Only add status — resubmitted_at already exists in the DB
            if (!Schema::hasColumn('salns', 'status')) {
                $table->string('status')->default('submitted')->after('remarks');
            }

            if (!Schema::hasColumn('salns', 'resubmitted_at')) {
                $table->timestamp('resubmitted_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('salns', function (Blueprint $table) {
            if (Schema::hasColumn('salns', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('salns', 'resubmitted_at')) {
                $table->dropColumn('resubmitted_at');
            }
        });
    }
};
