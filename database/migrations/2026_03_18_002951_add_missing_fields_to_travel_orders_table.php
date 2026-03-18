<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('travel_orders', function (Blueprint $table) {
            // These fields are shown in the print blade but were missing/commented out
            if (! Schema::hasColumn('travel_orders', 'assistant_laborer_allowed')) {
                $table->string('assistant_laborer_allowed')->nullable()->after('purpose_of_trip');
            }

            if (! Schema::hasColumn('travel_orders', 'per_diems_expenses_allowed')) {
                $table->string('per_diems_expenses_allowed')->nullable()->after('assistant_laborer_allowed');
            }

            if (! Schema::hasColumn('travel_orders', 'appropriation_funds')) {
                $table->string('appropriation_funds')->nullable()->after('per_diems_expenses_allowed');
            }

            // Was commented out in the model — now restored
            if (! Schema::hasColumn('travel_orders', 'remarks_special_instructions')) {
                $table->text('remarks_special_instructions')->nullable()->after('appropriation_funds');
            }
        });
    }

    public function down(): void
    {
        Schema::table('travel_orders', function (Blueprint $table) {
            $table->dropColumnIfExists('assistant_laborer_allowed');
            $table->dropColumnIfExists('per_diems_expenses_allowed');
            $table->dropColumnIfExists('appropriation_funds');
            $table->dropColumnIfExists('remarks_special_instructions');
        });
    }
};
