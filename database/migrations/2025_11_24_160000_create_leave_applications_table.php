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
        Schema::create('leave_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('office_department')->nullable();
            $table->date('date_of_filing')->nullable();
            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('position')->nullable();
            $table->decimal('salary', 15, 2)->nullable();
            $table->string('type_of_leave')->nullable();
            $table->string('others_specify')->nullable();
            $table->string('other_leave_type')->nullable();
            $table->string('vacation_location')->nullable();
            $table->string('abroad_specify')->nullable();
            $table->string('sick_leave_location')->nullable();
            $table->string('illness_specify')->nullable();
            $table->string('hospital_illness_specify')->nullable();
            $table->string('outpatient_illness_specify')->nullable();
            $table->string('women_illness_specify')->nullable();
            $table->json('study_leave_purpose')->nullable();
            $table->json('other_purpose')->nullable();
            $table->integer('number_of_working_days')->nullable();
            $table->boolean('commutation')->default(false);
            $table->date('leave_date_from')->nullable();
            $table->date('leave_date_to')->nullable();
            $table->string('supporting_document')->nullable();
            $table->date('as_of_date')->nullable();
            $table->decimal('vacation_leave_total_earned', 15, 2)->nullable();
            $table->decimal('sick_leave_total_earned', 15, 2)->nullable();
            $table->decimal('vacation_leave_less_application', 15, 2)->nullable();
            $table->decimal('sick_leave_less_application', 15, 2)->nullable();
            $table->decimal('vacation_leave_balance', 15, 2)->nullable();
            $table->decimal('sick_leave_balance', 15, 2)->nullable();
            $table->string('recommendation')->nullable();
            $table->string('authorized_officer_recommendation')->nullable();
            $table->string('disapproval_reason')->nullable();
            $table->string('final_action')->nullable();
            $table->integer('approved_days_with_pay')->nullable();
            $table->integer('approved_days_without_pay')->nullable();
            $table->string('approved_others')->nullable();
            $table->string('disapproved_reason')->nullable();
            $table->string('authorized_officer')->nullable();
            $table->date('date_approved_disapproved')->nullable();
            $table->string('status')->nullable();
            $table->decimal('vacation_leave_credits', 15, 2)->nullable();
            $table->decimal('sick_leave_credits', 15, 2)->nullable();
            $table->decimal('emergency_leave_credits', 15, 2)->nullable();
            $table->decimal('maternity_leave_credits', 15, 2)->nullable();
            $table->decimal('paternity_leave_credits', 15, 2)->nullable();
            $table->date('credits_last_updated')->nullable();
            $table->decimal('vacation_credits_earned_ytd', 15, 2)->nullable();
            $table->decimal('sick_credits_earned_ytd', 15, 2)->nullable();
            $table->decimal('vacation_credits_used_ytd', 15, 2)->nullable();
            $table->decimal('sick_credits_used_ytd', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_applications');
    }
};
