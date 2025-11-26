<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('saln_real_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saln_id')->constrained('salns')->cascadeOnDelete();
            $table->text('description');
            $table->string('kind');
            $table->string('exact_location');
            $table->decimal('assessed_value', 15, 2);
            $table->decimal('current_fair_market_value', 15, 2);
            $table->integer('acquisition_year');
            $table->string('mode_of_acquisition');
            $table->decimal('acquisition_cost', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saln_real_properties');
    }
};
