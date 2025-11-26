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
        Schema::create('locator_slips', function (Blueprint $table) {
    $table->id();
    $table->string('transaction_type');
    $table->string('employee_name');
    $table->string('position');
    $table->string('destination');
    $table->text('purpose')->nullable();
    $table->date('inclusive_date');
    $table->time('out_time');
    $table->time('in_time')->nullable();
    $table->string('requested_by')->nullable();
    $table->string('approved_by')->nullable();
    $table->string('status')->default('pending');
    $table->text('rejection_reason')->nullable();
    $table->timestamp('approved_at')->nullable();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locator_slips');
    }
};
