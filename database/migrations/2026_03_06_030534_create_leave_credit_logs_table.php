<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Audit trail for every credit change (accrual, deduction, reversal, manual adjustment).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_credit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Which leave type was affected
            $table->enum('leave_type', [
                'vacation_leave',
                'sick_leave',
                'special_privilege_leave',
                'mandatory_forced_leave',
            ]);

            // What kind of transaction
            $table->enum('transaction_type', [
                'accrual',       // monthly scheduler added credits
                'deduction',     // leave application approved
                'reversal',      // approved leave was cancelled / disapproved
                'adjustment',    // manual HR override
                'year_reset',    // annual reset run by scheduler
            ]);

            $table->decimal('amount', 8, 3);          // positive = credit, negative = debit
            $table->decimal('balance_after', 8, 3);   // snapshot of balance after this entry

            // Optional FK to the leave application that caused the change
            $table->foreignId('leave_application_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'leave_type']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_credit_logs');
    }
};
