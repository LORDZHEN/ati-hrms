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
        // 'remarks_special_instructions',
        'rejection_remark',                  // ← dedicated rejection remark column
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
        'travel_type',
        'batch_id',
    ];

    protected $casts = [
        'date'                              => 'date',
        'departure_date'                    => 'date',
        'return_date'                       => 'date',
        'salary_per_annum'                  => 'decimal:2',
        'recommended_by_assistant_director' => 'boolean',
        'approved_by_center_director'       => 'boolean',
        'recommended_at'                    => 'datetime',
        'approved_at'                       => 'datetime',
        'employee_ids'                      => 'array',
        'employee_details'                  => 'array',
    ];

    /* ============================================================
       RELATIONSHIPS
       ============================================================ */

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

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /* ============================================================
       SCOPES
       ============================================================ */

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

    /* ============================================================
       ACCESSORS
       ============================================================ */

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'warning',
            'recommended' => 'info',
            'approved'    => 'success',
            'rejected'    => 'danger',
            default       => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'Pending Review',
            'recommended' => 'Recommended',
            'approved'    => 'Approved',
            'rejected'    => 'Rejected',
            default       => 'Unknown',
        };
    }

    /* ============================================================
       BUSINESS LOGIC METHODS
       ============================================================ */

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
            'recommended_at'                    => now(),
            'recommended_by'                    => $user->id,
            'status'                            => 'recommended',
        ]);
    }

    public function approve(User $user): void
    {
        $updateData = [
            'approved_by_center_director' => true,
            'approved_at'                 => now(),
            'approved_by'                 => $user->id,
            'status'                      => 'approved',
            'rejection_remark'            => null,
        ];

        $this->update($updateData);

        if ($this->travel_type === 'batch' && $this->batch_id) {
            static::where('batch_id', $this->batch_id)
                ->where('id', '!=', $this->id)
                ->update($updateData);
        }
    }

    public function reject(string $remark): void
    {
        $updateData = [
            'status'           => 'rejected',
            'rejection_remark' => $remark,
        ];

        $this->update($updateData);

        if ($this->travel_type === 'batch' && $this->batch_id) {
            static::where('batch_id', $this->batch_id)
                ->where('id', '!=', $this->id)
                ->update($updateData);
        }
    }

    /* ============================================================
       MODEL EVENTS
       ============================================================ */

    protected static function booted(): void
    {
        static::creating(function ($travelOrder) {
            $travelOrder->created_by = $travelOrder->created_by ?? Auth::id();
            self::populateTravelerNames($travelOrder);
        });

        static::updating(function ($travelOrder) {
            if ($travelOrder->isDirty(['travel_type', 'employee_ids'])) {
                self::populateTravelerNames($travelOrder);
            }
        });
    }

    private static function populateTravelerNames($travelOrder): void
    {
        if ($travelOrder->travel_type === 'solo') {
            $user              = Auth::user();
            $travelOrder->name = $user->full_name ?? $user->name;
        } elseif ($travelOrder->travel_type === 'batch') {
            if (!empty($travelOrder->employee_ids) && is_array($travelOrder->employee_ids)) {
                $names             = User::whereIn('id', $travelOrder->employee_ids)
                    ->get()
                    ->map(fn($u) => $u->full_name ?? $u->name)
                    ->toArray();
                $travelOrder->name = implode(', ', $names);
            }
        }
    }
}
