<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {

            /* =====================================================
             | C3 – VOLUNTARY WORK, L&D, OTHER INFORMATION
             |=====================================================*/
            $table->json('voluntary_work')->nullable();
            $table->json('learning_development')->nullable();

            $table->json('special_skills')->nullable();
            $table->json('non_academic_distinctions')->nullable();
            $table->json('membership_association')->nullable();

            /* =====================================================
             | C4 – OTHER INFORMATION (Items 34–40)
             |=====================================================*/

            // 34
            $table->boolean('related_third_degree')->default(false);
            $table->text('related_third_degree_details')->nullable();

            $table->boolean('related_fourth_degree')->default(false);
            $table->text('related_fourth_degree_details')->nullable();

            // 35
            $table->string('has_admin_case')->nullable();
            $table->text('admin_case_details')->nullable();

            // 36
            $table->string('has_criminal_case')->nullable();
            $table->string('criminal_case_status')->nullable();
            $table->date('criminal_case_date_filed')->nullable();

            // 37
            $table->string('has_conviction')->nullable();
            $table->text('conviction_details')->nullable();

            // 38
            $table->string('has_been_separated')->nullable();
            $table->text('separation_details')->nullable();

            // 39
            $table->string('has_election_candidacy')->nullable();
            $table->text('election_candidacy_details')->nullable();

            // 40
            $table->boolean('is_indigenous')->default(false);
            $table->string('indigenous_details')->nullable();

            $table->boolean('has_disability')->default(false);
            $table->string('disability_details')->nullable();

            $table->boolean('is_solo_parent')->default(false);
            $table->string('solo_parent_details')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            $table->dropColumn([
                // C2
                'civil_service_eligibility',
                'work_experience',

                // C3
                'voluntary_work',
                'learning_development',
                'special_skills',
                'non_academic_distinctions',
                'membership_association',

                // C4
                'related_third_degree',
                'related_third_degree_details',
                'related_fourth_degree',
                'related_fourth_degree_details',

                'has_admin_case',
                'admin_case_details',

                'has_criminal_case',
                'criminal_case_status',
                'criminal_case_date_filed',

                'has_conviction',
                'conviction_details',

                'has_been_separated',
                'separation_details',

                'has_election_candidacy',
                'election_candidacy_details',

                'is_indigenous',
                'indigenous_details',

                'has_disability',
                'disability_details',

                'is_solo_parent',
                'solo_parent_details',
            ]);
        });
    }
};
