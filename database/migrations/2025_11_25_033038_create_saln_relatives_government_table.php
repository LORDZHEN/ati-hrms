<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('saln_relatives_government', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saln_id')->constrained('salns')->cascadeOnDelete();
            $table->string('name_of_relative');
            $table->string('relationship');
            $table->string('position');
            $table->text('name_of_agency_office_address');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saln_relatives_government');
    }
};
