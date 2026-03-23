<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biometric_employee_mappings', function (Blueprint $table) {
            // Drop the broken unique constraint — it prevents device recycling.
            // The one-active-per-device rule is enforced at the application layer.
            $table->dropUnique('unique_active_device_id');

            // Add indexes that actually help query performance:
            // XlsLogParser queries: active()->where('device_id', ?)
            // History drawer queries: where('user_id', ?)
            $table->index(['device_id', 'is_active'], 'idx_device_id_active');
            $table->index('user_id', 'idx_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('biometric_employee_mappings', function (Blueprint $table) {
            $table->dropIndex('idx_device_id_active');
            $table->dropIndex('idx_user_id');
            $table->unique(['device_id', 'is_active'], 'unique_active_device_id');
        });
    }
};
