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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('purok_street')->nullable();
            $table->string('city_municipality')->nullable();
            $table->string('province')->nullable();
            $table->date('birthday')->nullable();
            $table->string('status')->default('pending');
            $table->boolean('must_change_password')->default(true);
            $table->string('role')->default('employee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'phone',
                'purok_street',
                'city_municipality',
                'province',
                'birthday',
                'status',
                'must_change_password',
                'role',
            ]);
        });
    }
};
