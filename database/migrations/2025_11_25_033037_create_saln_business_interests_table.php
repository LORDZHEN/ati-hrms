<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('saln_business_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saln_id')->constrained('salns')->cascadeOnDelete();
            $table->string('name_of_entity');
            $table->text('business_address');
            $table->string('nature_of_business_interest');
            $table->date('date_of_acquisition');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saln_business_interests');
    }
};
