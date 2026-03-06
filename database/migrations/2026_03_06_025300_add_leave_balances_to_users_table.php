<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds leave credit balance columns to the users table.
 *
 * Run:  php artisan migrate
 *
 * If you prefer a separate table (leave_credits), see the README
 * for the alternative approach.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Default CSC leave credits for Philippine government employees
            $table->decimal('vacation_leave_balance',  8, 2)->default(15.00)->after('role');
            $table->decimal('sick_leave_balance',      8, 2)->default(15.00)->after('vacation_leave_balance');
            $table->decimal('special_leave_balance',   8, 2)->default(3.00)->after('sick_leave_balance');
            $table->decimal('mandatory_leave_balance', 8, 2)->default(5.00)->after('special_leave_balance');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'vacation_leave_balance',
                'sick_leave_balance',
                'special_leave_balance',
                'mandatory_leave_balance',
            ]);
        });
    }
};
