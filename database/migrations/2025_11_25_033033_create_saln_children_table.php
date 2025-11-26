<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('saln_children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saln_id')->constrained('salns')->cascadeOnDelete();
            $table->string('name');
            $table->date('date_of_birth');
            $table->integer('age')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saln_children');
    }
};
