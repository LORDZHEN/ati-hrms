<?php
// database/migrations/xxxx_xx_xx_update_users_role_employee_to_regular.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rename existing 'employee' records to 'regular'
        DB::table('users')
            ->where('role', 'employee')
            ->update(['role' => 'regular']);

        // Update the column enum if your DB uses enum type
        // If your column is just VARCHAR, the above is sufficient.
        // Uncomment below only if using MySQL ENUM:
        /*
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','regular','job_order') NOT NULL DEFAULT 'regular'");
        */
    }

    public function down(): void
    {
        DB::table('users')
            ->where('role', 'regular')
            ->update(['role' => 'employee']);
    }
};
