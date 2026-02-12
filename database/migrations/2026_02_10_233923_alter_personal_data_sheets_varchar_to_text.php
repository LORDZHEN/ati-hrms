<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            // ADDRESS FIELDS
            $table->text('residential_address')->nullable()->change();
            $table->text('permanent_address')->nullable()->change();
            $table->text('res_house_block_lot_no')->nullable()->change();
            $table->text('res_street')->nullable()->change();
            $table->text('res_subdivision_village')->nullable()->change();
            $table->text('res_barangay')->nullable()->change();
            $table->text('res_city_municipality')->nullable()->change();
            $table->text('res_province')->nullable()->change();
            $table->text('res_zip_code')->nullable()->change();
            $table->text('perm_house_block_lot_no')->nullable()->change();
            $table->text('perm_street')->nullable()->change();
            $table->text('perm_subdivision_village')->nullable()->change();
            $table->text('perm_barangay')->nullable()->change();
            $table->text('perm_city_municipality')->nullable()->change();
            $table->text('perm_province')->nullable()->change();
            $table->text('perm_zip_code')->nullable()->change();

            // SPOUSE / FAMILY DETAILS
            $table->text('spouse_employer_business_name')->nullable()->change();
            $table->text('spouse_business_address')->nullable()->change();
            $table->text('spouse_occupation')->nullable()->change();
            $table->text('spouse_telephone_no')->nullable()->change();
            $table->text('spouse_first_name')->nullable()->change();
            $table->text('spouse_middle_name')->nullable()->change();
            $table->text('spouse_name_extension')->nullable()->change();
            $table->text('spouse_surname')->nullable()->change();

            // OTHER LONG TEXT FIELDS
            $table->text('criminal_case_status')->nullable()->change();
            $table->text('conviction_details')->nullable()->change();
            $table->text('admin_case_details')->nullable()->change();
            $table->text('indigenous_details')->nullable()->change();
            $table->text('disability_details')->nullable()->change();
            $table->text('solo_parent_details')->nullable()->change();
            $table->text('related_third_degree_details')->nullable()->change();
            $table->text('related_fourth_degree_details')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            // Revert TEXT columns back to VARCHAR(255)
            $table->string('residential_address', 255)->nullable()->change();
            $table->string('permanent_address', 255)->nullable()->change();
            $table->string('res_house_block_lot_no', 255)->nullable()->change();
            $table->string('res_street', 255)->nullable()->change();
            $table->string('res_subdivision_village', 255)->nullable()->change();
            $table->string('res_barangay', 255)->nullable()->change();
            $table->string('res_city_municipality', 255)->nullable()->change();
            $table->string('res_province', 255)->nullable()->change();
            $table->string('res_zip_code', 255)->nullable()->change();
            $table->string('perm_house_block_lot_no', 255)->nullable()->change();
            $table->string('perm_street', 255)->nullable()->change();
            $table->string('perm_subdivision_village', 255)->nullable()->change();
            $table->string('perm_barangay', 255)->nullable()->change();
            $table->string('perm_city_municipality', 255)->nullable()->change();
            $table->string('perm_province', 255)->nullable()->change();
            $table->string('perm_zip_code', 255)->nullable()->change();

            $table->string('spouse_employer', 255)->nullable()->change();
            $table->string('spouse_employer_business_name', 255)->nullable()->change();
            $table->string('spouse_business_address', 255)->nullable()->change();
            $table->string('spouse_occupation', 255)->nullable()->change();
            $table->string('spouse_telephone_no', 255)->nullable()->change();
            $table->string('spouse_first_name', 255)->nullable()->change();
            $table->string('spouse_middle_name', 255)->nullable()->change();
            $table->string('spouse_name_extension', 255)->nullable()->change();
            $table->string('spouse_surname', 255)->nullable()->change();
            $table->string('father_name', 255)->nullable()->change();
            $table->string('father_first_name', 255)->nullable()->change();
            $table->string('father_middle_name', 255)->nullable()->change();
            $table->string('father_surname', 255)->nullable()->change();
            $table->string('father_name_extension', 255)->nullable()->change();
            $table->string('mother_name', 255)->nullable()->change();
            $table->string('mother_first_name', 255)->nullable()->change();
            $table->string('mother_middle_name', 255)->nullable()->change();
            $table->string('mother_surname', 255)->nullable()->change();

            $table->string('criminal_case_status', 255)->nullable()->change();
            $table->string('conviction_details', 255)->nullable()->change();
            $table->string('admin_case_details', 255)->nullable()->change();
            $table->string('indigenous_details', 255)->nullable()->change();
            $table->string('disability_details', 255)->nullable()->change();
            $table->string('solo_parent_details', 255)->nullable()->change();
            $table->string('related_third_degree_details', 255)->nullable()->change();
            $table->string('related_fourth_degree_details', 255)->nullable()->change();
        });
    }
};
