<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            $table->json('references')->nullable()->after('is_solo_parent');
            $table->string('gov_id_type')->nullable()->after('references');
            $table->string('gov_id_no')->nullable()->after('gov_id_type');
            $table->string('gov_id_issued')->nullable()->after('gov_id_no');
            $table->date('date_accomplished')->nullable()->after('gov_id_issued');
        });
    }

    public function down(): void
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            $table->dropColumn([
                'references',
                'gov_id_type',
                'gov_id_no',
                'gov_id_issued',
                'date_accomplished',
            ]);
        });
    }
};
