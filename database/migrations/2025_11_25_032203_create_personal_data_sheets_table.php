<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_data_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Personal Info
            $table->string('surname')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('name_extension')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('sex')->nullable();
            $table->string('civil_status')->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 1)->nullable();
            $table->string('blood_type')->nullable();
            $table->string('gsis_id_no')->nullable();
            $table->string('pag_ibig_id_no')->nullable();
            $table->string('philhealth_no')->nullable();
            $table->string('sss_no')->nullable();
            $table->string('tin_no')->nullable();
            $table->string('agency_employee_no')->nullable();
            $table->string('email_address')->nullable();
            $table->string('telephone_no')->nullable();
            $table->string('mobile_no')->nullable();
            $table->text('remarks')->nullable();
            $table->year('year')->nullable();

            // Citizenship
            $table->boolean('filipino')->default(true);
            $table->boolean('dual_citizenship')->default(false);
            $table->boolean('by_birth')->default(false);
            $table->boolean('by_naturalization')->default(false);
            $table->string('country')->nullable();

            // Residential Address
            $table->string('res_house_block_lot_no')->nullable();
            $table->string('res_street')->nullable();
            $table->string('res_subdivision_village')->nullable();
            $table->string('res_barangay')->nullable();
            $table->string('res_city_municipality')->nullable();
            $table->string('res_province')->nullable();
            $table->string('res_zip_code', 10)->nullable();

            // Permanent Address
            $table->string('perm_house_block_lot_no')->nullable();
            $table->string('perm_street')->nullable();
            $table->string('perm_subdivision_village')->nullable();
            $table->string('perm_barangay')->nullable();
            $table->string('perm_city_municipality')->nullable();
            $table->string('perm_province')->nullable();
            $table->string('perm_zip_code', 10)->nullable();

            // Family Background
            $table->string('spouse_surname')->nullable();
            $table->string('spouse_first_name')->nullable();
            $table->string('spouse_middle_name')->nullable();
            $table->string('spouse_name_extension')->nullable();
            $table->string('spouse_occupation')->nullable();
            $table->string('spouse_employer_business_name')->nullable();
            $table->string('spouse_business_address')->nullable();
            $table->string('spouse_telephone_no')->nullable();

            $table->string('father_surname')->nullable();
            $table->string('father_first_name')->nullable();
            $table->string('father_middle_name')->nullable();
            $table->string('father_name_extension')->nullable();

            $table->string('mother_surname')->nullable();
            $table->string('mother_first_name')->nullable();
            $table->string('mother_middle_name')->nullable();

            // JSON fields
            $table->json('children')->nullable();
            $table->json('education')->nullable();
            $table->json('eligibilities')->nullable();
            $table->json('work_experience')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_data_sheets');
    }
};

