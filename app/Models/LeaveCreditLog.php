<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveCreditLog extends Model
{
    protected $fillable = [
        'user_id',
        'leave_type',
        'transaction_type',
        'amount',
        'balance_after',
        'leave_application_id',
        'remarks',
    ];

    protected $casts = [
        'amount'        => 'decimal:3',
        'balance_after' => 'decimal:3',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leaveApplication(): BelongsTo
    {
        return $this->belongsTo(LeaveApplication::class);
    }
}
