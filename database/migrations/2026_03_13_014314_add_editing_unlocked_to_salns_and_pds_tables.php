<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salns', function (Blueprint $table) {
            $table->boolean('editing_unlocked')->default(false)->after('status');
        });

        Schema::table('personal_data_sheets', function (Blueprint $table) {
            $table->boolean('editing_unlocked')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('salns', function (Blueprint $table) {
            $table->dropColumn('editing_unlocked');
        });

        Schema::table('personal_data_sheets', function (Blueprint $table) {
            $table->dropColumn('editing_unlocked');
        });
    }
};
