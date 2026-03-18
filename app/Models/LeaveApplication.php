<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveApplication extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Leave Type Constants
    |--------------------------------------------------------------------------
    */

    public const LEAVE_VACATION = 'vacation_leave';
    public const LEAVE_SICK = 'sick_leave';
    public const LEAVE_SPECIAL_PRIVILEGE = 'special_privilege_leave';
    public const LEAVE_MATERNITY = 'maternity_leave';
    public const LEAVE_PATERNITY = 'paternity_leave';
    public const LEAVE_SOLO_PARENT = 'solo_parent_leave';
    public const LEAVE_STUDY = 'study_leave';
    public const LEAVE_REHABILITATION = 'rehabilitation_privilege';
    public const LEAVE_SPECIAL_WOMEN = 'special_leave_benefits_for_women';
    public const LEAVE_EMERGENCY = 'special_emergency_leave';
    public const LEAVE_ADOPTION = 'adoption_leave';
    public const LEAVE_WELLNESS = 'wellness_leave';
    public const LEAVE_OTHERS = 'others';

    public const LEAVE_TYPES = [
        self::LEAVE_VACATION,
        self::LEAVE_SICK,
        self::LEAVE_SPECIAL_PRIVILEGE,
        self::LEAVE_MATERNITY,
        self::LEAVE_PATERNITY,
        self::LEAVE_SOLO_PARENT,
        self::LEAVE_STUDY,
        self::LEAVE_REHABILITATION,
        self::LEAVE_SPECIAL_WOMEN,
        self::LEAVE_EMERGENCY,
        self::LEAVE_ADOPTION,
        self::LEAVE_WELLNESS,
        self::LEAVE_OTHERS,
    ];

    /*
    |--------------------------------------------------------------------------
    | Fillable Fields
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'name',
        'employee_id',
        'office_department',
        'date_of_filing',
        'position',

        'type_of_leave',
        'others_specify',
        'other_leave_type',

        'vacation_location',
        'abroad_specify',
        'sick_leave_location',
        'illness_specify',
        'hospital_illness_specify',
        'outpatient_illness_specify',
        'women_illness_specify',

        'study_leave_purpose',
        'other_purpose',

        'number_of_working_days',
        'commutation',
        'leave_date_from',
        'leave_date_to',

        'supporting_document',

        'as_of_date',

        'vacation_leave_total_earned',
        'sick_leave_total_earned',
        'vacation_leave_less_application',
        'sick_leave_less_application',
        'vacation_leave_balance',
        'sick_leave_balance',

        'recommendation',
        'authorized_officer_recommendation',
        'disapproval_reason',

        'final_action',
        'approved_days_with_pay',
        'approved_days_without_pay',
        'approved_others',

        'authorized_officer',
        'date_approved_disapproved',
        'status',

        'vacation_leave_credits',
        'sick_leave_credits',
        'emergency_leave_credits',
        'maternity_leave_credits',
        'paternity_leave_credits',
        'credits_last_updated',

        'vacation_credits_earned_ytd',
        'sick_credits_earned_ytd',
        'vacation_credits_used_ytd',
        'sick_credits_used_ytd',

        'recommended_by',
        'recommendation_status',
        'approved_by',
        'approved_at',
        'approval_status',
        'rejected_by',
        'remarks',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'date_of_filing' => 'date',
        'leave_date_from' => 'date',   // Cast ensures Carbon instance
        'leave_date_to' => 'date',   // Cast ensures Carbon instance
        'as_of_date' => 'date',
        'date_approved_disapproved' => 'date',
        'salary' => 'decimal:2',
        'vacation_leave_total_earned' => 'decimal:2',
        'sick_leave_total_earned' => 'decimal:2',
        'vacation_leave_less_application' => 'decimal:2',
        'sick_leave_less_application' => 'decimal:2',
        'vacation_leave_balance' => 'decimal:2',
        'sick_leave_balance' => 'decimal:2',
        'study_leave_purpose' => 'array',
        'other_purpose' => 'array',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function recommender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recommended_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }

    public function getLeaveTypeDisplayAttribute(): string
    {
        return str_replace('_', ' ', ucwords($this->type_of_leave, '_'));
    }

    public function getDurationInDaysAttribute(): int
    {
        if (!$this->leave_date_from || !$this->leave_date_to) {
            return 0;
        }
        return $this->leave_date_from->diffInDays($this->leave_date_to) + 1;
    }

    /**
     * getInclusiveDatesDisplayAttribute
     *
     * Returns a human-readable date range string for the print view.
     * Accessed via $leaveApplication->inclusive_dates_display.
     *
     * Examples:
     *   Single day          → "March 18, 2026"
     *   Same month & year   → "March 18 – 20, 2026"
     *   Same year           → "March 18 – April 2, 2026"
     *   Cross-year          → "December 30, 2025 – January 2, 2026"
     *   Only from date set  → "March 18, 2026 – "
     *   No dates            → ""
     */
    public function getInclusiveDatesDisplayAttribute(): string
    {
        /** @var Carbon|null $from */
        $from = $this->leave_date_from;
        /** @var Carbon|null $to */
        $to = $this->leave_date_to;

        if (!$from) {
            return '';
        }

        if (!$to) {
            return $from->format('F j, Y') . ' – ';
        }

        if ($from->isSameDay($to)) {
            // Single day
            return $from->format('F j, Y');
        }

        if ($from->isSameMonth($to) && $from->isSameYear($to)) {
            // Same month and year: "March 18 – 20, 2026"
            return $from->format('F j') . ' – ' . $to->format('j, Y');
        }

        if ($from->isSameYear($to)) {
            // Same year, different months: "March 18 – April 2, 2026"
            return $from->format('F j') . ' – ' . $to->format('F j, Y');
        }

        // Different years: "December 30, 2025 – January 2, 2026"
        return $from->format('F j, Y') . ' – ' . $to->format('F j, Y');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeDisapproved($query)
    {
        return $query->where('status', 'disapproved');
    }

    public function scopeByLeaveType($query, $type)
    {
        return $query->where('type_of_leave', $type);
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('leave_date_from', [$startDate, $endDate])
                ->orWhereBetween('leave_date_to', [$startDate, $endDate])
                ->orWhere(function ($query) use ($startDate, $endDate) {
                    $query->where('leave_date_from', '<=', $startDate)
                        ->where('leave_date_to', '>=', $endDate);
                });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDisapproved(): bool
    {
        return $this->status === 'disapproved';
    }

    public function canBeEdited(): bool
    {
        return $this->status === 'pending';
    }

    public function approve(array $details = []): void
    {
        $this->update(array_merge([
            'status' => 'approved',
            'final_action' => 'approved',
            'date_approved_disapproved' => now(),
        ], $details));
    }

    public function disapprove(string $reason, string $authorizedOfficer = null): void
    {
        $this->update([
            'status' => 'disapproved',
            'final_action' => 'disapproved',
            'disapproved_reason' => $reason,
            'authorized_officer' => $authorizedOfficer,
            'date_approved_disapproved' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Model Events
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($leaveApplication) {
            if (!$leaveApplication->date_of_filing) {
                $leaveApplication->date_of_filing = now()->toDateString();
            }

            // Auto-compute number_of_working_days if not already set
            if (
                !$leaveApplication->number_of_working_days &&
                $leaveApplication->leave_date_from &&
                $leaveApplication->leave_date_to
            ) {
                $from = Carbon::parse($leaveApplication->leave_date_from);
                $to = Carbon::parse($leaveApplication->leave_date_to);

                // Use diffInWeekdays for accuracy (excludes Sat/Sun)
                $leaveApplication->number_of_working_days =
                    $from->diffInWeekdays($to) + 1;
            }
        });
    }
}
