<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'employee_id_number')) {
                $table->dropColumn('employee_id_number');
            }

            if (Schema::hasColumn('users', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'employee_id_number')) {
                $table->string('employee_id_number')->nullable()->after('employee_id');
            }

            if (!Schema::hasColumn('users', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('employee_id_number');
            }
        });
    }
};
