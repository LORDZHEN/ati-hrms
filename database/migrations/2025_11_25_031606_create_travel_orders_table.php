<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('travel_orders', function (Blueprint $table) {
            $table->id();
            $table->string('travel_order_no')->unique()->nullable();
            $table->date('date');
            $table->string('name')->nullable();
            $table->string('position')->nullable();
            $table->decimal('salary_per_annum', 15, 2)->nullable();
            $table->string('station')->nullable();
            $table->date('departure_date');
            $table->date('return_date');
            $table->string('report_to')->nullable();
            $table->string('destination')->nullable();
            $table->text('purpose_of_trip')->nullable();
            $table->boolean('recommended_by_assistant_director')->default(false);
            $table->timestamp('recommended_at')->nullable();
            $table->foreignId('recommended_by')->nullable()->constrained('users');
            $table->boolean('approved_by_center_director')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->enum('status', ['draft','pending','recommended','approved','rejected'])->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->json('employee_ids')->nullable();
            $table->json('employee_details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_orders');
    }
};
