<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class LocatorSlip extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_type',
        'employee_name',
        'position',
        // 'department',
        'office_department',
        'destination',
        'purpose',
        'inclusive_date',
        'out_time',
        'in_time',
        'requested_by',
        'approved_by',
        'status',
        'rejection_reason',
        'approved_at',
        'user_id',
    ];

    protected $casts = [
        'inclusive_date' => 'date',
        'out_time' => 'datetime:H:i',
        'in_time' => 'datetime:H:i',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'Unknown',
        };
    }
    protected static function booted()
    {
        static::creating(function ($locatorSlip) {
            $locatorSlip->user_id = Auth::id();
        });
    }


}
