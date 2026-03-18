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

        // ── Administrative fields shown in print blade ──────────────────────
        'assistant_laborer_allowed',
        'per_diems_expenses_allowed',
        'appropriation_funds',
        'remarks_special_instructions',  // restored — was commented out

        // ── Workflow / approval ──────────────────────────────────────────────
        'rejection_remark',
        'recommended_by_assistant_director',
        'recommended_at',
        'recommended_by',
        'approved_by_center_director',
        'approved_at',
        'approved_by',
        'status',

        // ── Travel type & batch support ──────────────────────────────────────
        'travel_type',       // 'solo' | 'batch'
        'employee_ids',      // JSON: [1, 2, 3]  — used for tab query & tagging
        'employee_details',  // JSON: [['name'=>..,'position'=>..], ...]  — used for print
        'batch_id',          // groups related batch records together

        'created_by',
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
            'pending' => 'Pending Review',
            'recommended' => 'Recommended',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'Unknown',
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
            'recommended_at' => now(),
            'recommended_by' => $user->id,
            'status' => 'recommended',
        ]);
    }

    /**
     * Approve this travel order.
     * For batch travel, also approves all sibling records sharing the same batch_id.
     */
    public function approve(User $user): void
    {
        $updateData = [
            'approved_by_center_director' => true,
            'approved_at' => now(),
            'approved_by' => $user->id,
            'status' => 'approved',
            'rejection_remark' => null,
        ];

        $this->update($updateData);

        if ($this->travel_type === 'batch' && $this->batch_id) {
            static::where('batch_id', $this->batch_id)
                ->where('id', '!=', $this->id)
                ->update($updateData);
        }
    }

    /**
     * Reject this travel order.
     * For batch travel, also rejects all sibling records sharing the same batch_id.
     */
    public function reject(string $remark): void
    {
        $updateData = [
            'status' => 'rejected',
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
            self::syncEmployeeDetails($travelOrder);
        });

        static::updating(function ($travelOrder) {
            if ($travelOrder->isDirty(['travel_type', 'employee_ids', 'position', 'name'])) {
                self::syncEmployeeDetails($travelOrder);
            }
        });
    }

    /**
     * Keeps the `name` display field and `employee_details` JSON in sync
     * with whichever travel type is active.
     *
     * Solo  → name = auth user's name, employee_details = null
     * Batch → name = comma-joined employee names, employee_details = [{name, position}, ...]
     */
    private static function syncEmployeeDetails($travelOrder): void
    {
        if ($travelOrder->travel_type === 'solo') {
            $user = Auth::user();
            $travelOrder->name = filled($user->full_name) ? $user->full_name : $user->name;
            $travelOrder->position = $user->position ?? $travelOrder->position;
            $travelOrder->employee_details = null;

        } elseif ($travelOrder->travel_type === 'batch') {
            $ids = $travelOrder->employee_ids ?? [];
            $creatorId = $travelOrder->created_by ?? Auth::id();

            // Always ensure the creator appears first in the list
            if ($creatorId && !in_array($creatorId, $ids)) {
                array_unshift($ids, $creatorId);
                $travelOrder->employee_ids = $ids;
            }

            if (!empty($ids) && is_array($ids)) {
                $employees = User::whereIn('id', $ids)
                    ->get(['id', 'name', 'first_name', 'middle_name', 'last_name', 'suffix', 'position', 'role'])
                    ->sortBy(fn($u) => array_search($u->id, $ids))
                    ->map(fn($u) => [
                        'id' => $u->id,
                        'name' => filled($u->full_name) ? $u->full_name : $u->name,
                        'position' => $u->position ?? '',
                        'role' => $u->role ?? '',
                        'role_label' => User::getRoles()[$u->role] ?? ucwords(str_replace('_', ' ', $u->role ?? '')),
                    ])
                    ->values()
                    ->toArray();

                $travelOrder->employee_details = $employees;
                $travelOrder->name = collect($employees)->pluck('name')->implode(', ');
            }
        }
    }
}
