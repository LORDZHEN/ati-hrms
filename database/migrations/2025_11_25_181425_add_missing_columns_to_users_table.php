<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('e_signature')->nullable()->after('profile_photo_path');
            $table->string('employment_status')->nullable()->after('status');
            $table->string('department')->nullable()->after('employment_status');
            $table->string('position')->nullable()->after('department');
            $table->string('employee_id_number')->nullable()->after('employee_id');
            $table->string('user_id')->nullable()->after('employee_id_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'e_signature',
                'employment_status',
                'department',
                'position',
                'employee_id_number',
                'user_id',
            ]);
        });
    }
};
