<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Removes the leave balance columns that were mistakenly added to the users table.
 * All leave credits are now managed exclusively in the leave_credits table.
 *
 * Run: php artisan migrate
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'vacation_leave_balance',
                'sick_leave_balance',
                'special_leave_balance',
                'mandatory_leave_balance',
            ];

            // Only drop columns that actually exist (safe for repeated runs)
            $existing = array_filter(
                $columns,
                fn($col) => Schema::hasColumn('users', $col)
            );

            if (!empty($existing)) {
                $table->dropColumn($existing);
            }
        });
    }

    public function down(): void
    {
        // Restore the columns if you need to roll back
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('vacation_leave_balance', 8, 2)->default(15.00)->after('role');
            $table->decimal('sick_leave_balance', 8, 2)->default(15.00)->after('vacation_leave_balance');
            $table->decimal('special_leave_balance', 8, 2)->default(3.00)->after('sick_leave_balance');
            $table->decimal('mandatory_leave_balance', 8, 2)->default(5.00)->after('special_leave_balance');
        });
    }
};
