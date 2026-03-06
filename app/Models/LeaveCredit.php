<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveCredit extends Model
{
    protected $fillable = [
        'user_id',
        'vacation_leave_balance',
        'sick_leave_balance',
        'special_leave_balance',
        'mandatory_leave_balance',
        'vacation_leave_max',
        'sick_leave_max',
        'last_accrual_date',
    ];

    protected $casts = [
        'vacation_leave_balance'  => 'decimal:3',
        'sick_leave_balance'      => 'decimal:3',
        'special_leave_balance'   => 'decimal:3',
        'mandatory_leave_balance' => 'decimal:3',
        'vacation_leave_max'      => 'decimal:3',
        'sick_leave_max'          => 'decimal:3',
        'last_accrual_date'       => 'date',
    ];

    // ── Relationships ────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(LeaveCreditLog::class, 'user_id', 'user_id');
    }

    // ── Helpers ──────────────────────────────────────────────────────

    /**
     * Return the balance column name for a given leave_application type_of_leave value.
     */
    public static function balanceColumn(string $leaveType): ?string
    {
        return match ($leaveType) {
            'vacation_leave'           => 'vacation_leave_balance',
            'mandatory_forced_leave'   => 'mandatory_leave_balance',
            'sick_leave'               => 'sick_leave_balance',
            'special_privilege_leave'  => 'special_leave_balance',
            default                    => null,   // leave types not tracked here
        };
    }

    /**
     * Return the max-cap column name, or null if there is no cap.
     */
    public static function maxColumn(string $leaveType): ?string
    {
        return match ($leaveType) {
            'vacation_leave' => 'vacation_leave_max',
            'sick_leave'     => 'sick_leave_max',
            default          => null,
        };
    }
}
