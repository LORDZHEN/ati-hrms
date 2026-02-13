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
        'office_department',
        'destination',
        'purpose',
        'inclusive_date',
        'out_time',
        'in_time',
        'requested_by',
        'approved_by',
        'admin_remarks',
        'status',
        'approved_at',
        'user_id',
    ];

    protected $casts = [
        'inclusive_date' => 'date',
        'out_time' => 'datetime:H:i',  // Nullable - filled manually by HR
        'in_time' => 'datetime:H:i',   // Nullable - filled manually by HR
        'approved_at' => 'datetime',
    ];

    /* ============================================================
       RELATIONSHIPS
       ============================================================ */

    /**
     * Get the user who created this locator slip
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ============================================================
       ACCESSORS
       ============================================================ */

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'disapproved' => 'danger',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending Approval',
            'approved' => 'Approved',
            'disapproved' => 'Disapproved',
            default => 'Unknown',
        };
    }

    /* ============================================================
       MODEL EVENTS
       ============================================================ */

    protected static function booted(): void
    {
        // Automatically set user_id and populate employee data when creating
        static::creating(function ($locatorSlip) {
            $user = Auth::user();

            // Set the user_id
            $locatorSlip->user_id = $user->id;

            // Auto-populate employee information from authenticated user
            if (empty($locatorSlip->employee_name)) {
                $locatorSlip->employee_name = $user->name;
            }

            if (empty($locatorSlip->position)) {
                $locatorSlip->position = $user->position;
            }

            if (empty($locatorSlip->office_department)) {
                $locatorSlip->office_department = $user->department;
            }

            if (empty($locatorSlip->requested_by)) {
                $locatorSlip->requested_by = $user->name;
            }

            // Note: out_time and in_time are NOT auto-populated
            // These will be filled manually by HR on the printed slip
        });
    }
    
}
