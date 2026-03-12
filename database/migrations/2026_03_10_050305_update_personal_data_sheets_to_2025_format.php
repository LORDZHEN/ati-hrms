<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    // In migration file:
    public function up()
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            // Add new field
            $table->string('philsys_number')->nullable()->after('sss_no');

            // Rename existing fields
            $table->renameColumn('gsis_id_no', 'umid_id_no');
            $table->renameColumn('pag_ibig_id_no', 'id_no');
        });
    }

    public function down()
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            $table->dropColumn('philsys_number');
            $table->renameColumn('umid_id_no', 'gsis_id_no');
            $table->renameColumn('id_no', 'pag_ibig_id_no');
        });
    }
};
