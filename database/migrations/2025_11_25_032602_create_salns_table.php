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
        Schema::create('salns', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->date('as_of_date');
    $table->boolean('joint_filing')->default(false);
    $table->boolean('separate_filing')->default(false);
    $table->boolean('not_applicable')->default(false);

    $table->string('declarant_family_name')->nullable();
    $table->string('declarant_first_name')->nullable();
    $table->string('declarant_middle_initial', 5)->nullable();
    $table->string('declarant_position')->nullable();
    $table->string('declarant_agency_office')->nullable();
    $table->text('declarant_office_address')->nullable();

    $table->string('spouse_family_name')->nullable();
    $table->string('spouse_first_name')->nullable();
    $table->string('spouse_middle_initial', 5)->nullable();
    $table->string('spouse_position')->nullable();
    $table->string('spouse_agency_office')->nullable();
    $table->text('spouse_office_address')->nullable();

    $table->decimal('total_assets', 15, 2)->default(0);
    $table->decimal('total_liabilities', 15, 2)->default(0);
    $table->decimal('net_worth', 15, 2)->default(0);

    $table->boolean('has_business_interests')->default(false);
    $table->boolean('no_business_interests')->default(false);
    $table->boolean('has_relatives_in_government')->default(false);
    $table->boolean('no_relatives_in_government')->default(false);

    $table->date('date_signed')->nullable();
    $table->string('declarant_signature')->nullable();
    $table->string('spouse_signature')->nullable();
    $table->string('person_administering_oath')->nullable();
    $table->date('subscribed_sworn_date')->nullable();

    $table->string('declarant_id_presented')->nullable();
    $table->string('spouse_id_presented')->nullable();
    $table->text('remarks')->nullable();

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salns');
    }
};
