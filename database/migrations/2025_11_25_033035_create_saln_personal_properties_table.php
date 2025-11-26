<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('saln_personal_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saln_id')->constrained('salns')->cascadeOnDelete();
            $table->text('description');
            $table->integer('year_acquired');
            $table->decimal('acquisition_cost', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saln_personal_properties');
    }
};
