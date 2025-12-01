<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('locator_slips', function (Blueprint $table) {
        $table->string('office_department')->nullable()->after('position');
    });
}

public function down()
{
    Schema::table('locator_slips', function (Blueprint $table) {
        $table->dropColumn('office_department');
    });
}

};
