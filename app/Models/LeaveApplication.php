<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'office_department',
        'date_of_filing',
        'last_name',
        'first_name',
        'middle_name',
        'position',
        // 'salary', ← optional, if you're hiding it
        'type_of_leave',
        'others_specify',
        'other_leave_type', // NEW
        'vacation_location',
        'abroad_specify',
        'sick_leave_location',
        'illness_specify',
        'hospital_illness_specify', // NEW
        'outpatient_illness_specify', // NEW
        'women_illness_specify',
        'study_leave_purpose',
        'other_purpose',
        'number_of_working_days',
        'commutation',
        'leave_date_from',
        'leave_date_to',
        'supporting_document', // NEW
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
        'disapproved_reason',
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

        'recommendation',
        'authorized_officer_recommendation',
        'disapproval_reason',
        'approved_days_with_pay',
        'approved_days_without_pay',
        'approved_others',
        'final_action',
        'authorized_officer',
        'date_approved_disapproved',
        'status',

        'recommended_by',
        'recommendation_status',
        'approved_by',
        'approved_at',
        'approval_status',
        'rejected_by',
        'remarks',
    ];

    protected $casts = [
        'date_of_filing' => 'date',
        'leave_date_from' => 'date',
        'leave_date_to' => 'date',
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
        'working_days_from' => 'date',
        'working_days_to' => 'date',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
    public function recommender()
    {
        return $this->belongsTo(User::class, 'recommended_by');
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Accessors
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
        return $this->leave_date_from->diffInDays($this->leave_date_to) + 1;
    }

    // Scopes
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

    // Helper methods
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

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($leaveApplication) {
            if (!$leaveApplication->date_of_filing) {
                $leaveApplication->date_of_filing = now()->toDateString();
            }

            // Auto-calculate working days if not provided
            if (!$leaveApplication->number_of_working_days && $leaveApplication->leave_date_from && $leaveApplication->leave_date_to) {
                $leaveApplication->number_of_working_days = $leaveApplication->leave_date_from->diffInDays($leaveApplication->leave_date_to) + 1;
            }
        });
    }
}
