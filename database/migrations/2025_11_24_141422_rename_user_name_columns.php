<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add new 'name' column
            $table->string('name')->after('id');

            // Optional: copy data from first_name + last_name into name
            // Uncomment if you have existing users and want to preserve data
            // \DB::table('users')->update([
            //     'name' => \DB::raw("CONCAT(first_name, ' ', last_name)")
            // ]);

            // Drop first_name and last_name
            $table->dropColumn(['first_name', 'last_name']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Rollback: add first_name and last_name back
            $table->string('first_name')->after('id');
            $table->string('last_name')->after('first_name');

            // Remove 'name'
            $table->dropColumn('name');
        });
    }
};
