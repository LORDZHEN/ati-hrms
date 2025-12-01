<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class TravelOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_order_no',
        'date',
        'name',
        'position',
        'salary_per_annum',
        'station',
        'departure_date',
        'return_date',
        'report_to',
        'destination',
        'purpose_of_trip',
        'assistant_laborer_allowed',
        'per_diems_expenses_allowed',
        'appropriation_funds',
        'remarks_special_instructions',
        'recommended_by_assistant_director',
        'recommended_at',
        'recommended_by',
        'approved_by_center_director',
        'approved_at',
        'approved_by',
        'status',
        'created_by',
        'employee_ids',
        'employee_details',
    ];

    protected $casts = [
        'date' => 'date',
        'departure_date' => 'date',
        'return_date' => 'date',
        'salary_per_annum' => 'decimal:2',
        'recommended_by_assistant_director' => 'boolean',
        'approved_by_center_director' => 'boolean',
        'recommended_at' => 'datetime',
        'approved_at' => 'datetime',
        'employee_ids' => 'array',
        'employee_details' => 'array',
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recommender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recommended_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRecommended($query)
    {
        return $query->where('status', 'recommended');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Accessors
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'secondary',
            'pending' => 'warning',
            'recommended' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'pending' => 'Pending Review',
            'recommended' => 'Recommended',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'Unknown',
        };
    }

    // Methods
    public function canBeRecommended(): bool
    {
        return $this->status === 'pending' && !$this->recommended_by_assistant_director;
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'recommended' && !$this->approved_by_center_director;
    }

    public function canBeRejected(): bool
    {
        return in_array($this->status, ['pending', 'recommended']);
    }

    public function recommend(User $user): void
    {
        $this->update([
            'recommended_by_assistant_director' => true,
            'recommended_at' => now(),
            'recommended_by' => $user->id,
            'status' => 'recommended',
        ]);
    }

    public function approve(User $user): void
    {
        $this->update([
            'approved_by_center_director' => true,
            'approved_at' => now(),
            'approved_by' => $user->id,
            'status' => 'approved',
        ]);
    }

    public function reject(): void
    {
        $this->update([
            'status' => 'rejected',
        ]);
    }

    /**
     * Auto-generate travel_order_no in format mm-yyyy-0001
     */
    protected static function booted()
    {
        static::creating(function ($travelOrder) {
            // Set creator
            $travelOrder->created_by = Auth::id();

            // Auto-generate travel order number if empty
            if (empty($travelOrder->travel_order_no)) {
                $date = $travelOrder->date ?? now();
                $month = $date->format('m');
                $year = $date->format('Y');

                // Count existing records in the same month/year
                $count = static::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->count() + 1;

                $sequence = str_pad($count, 4, '0', STR_PAD_LEFT);

                $travelOrder->travel_order_no = "{$month}-{$year}-{$sequence}";
            }
        });
    }
}
