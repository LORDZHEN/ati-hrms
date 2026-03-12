<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Saln extends Model
{
    protected $fillable = [
        'user_id',

        // ── 2025 Compliance type ──────────────────────────────────────────────
        'compliance_assumption',
        'compliance_annual',
        'compliance_exit',
        'as_of_date',

        // ── Filing type ───────────────────────────────────────────────────────
        'joint_filing',
        'separate_filing',
        'not_applicable',

        // ── Declarant ─────────────────────────────────────────────────────────
        'declarant_family_name',
        'declarant_first_name',
        'declarant_middle_initial',
        'declarant_position',
        'declarant_agency_office',
        'declarant_office_address',

        // ── Spouse ────────────────────────────────────────────────────────────
        'spouse_family_name',
        'spouse_first_name',
        'spouse_middle_initial',
        'spouse_position',
        'spouse_agency_office',
        'spouse_office_address',

        // ── Multiple marriages (2025 new) ─────────────────────────────────────
        'multiple_marriages_names',
        'multiple_marriages_not_applicable',

        // ── Flags ─────────────────────────────────────────────────────────────
        'no_business_interests',
        'no_relatives_in_government',

        // ── Totals (Annex A — main form) ──────────────────────────────────────
        'total_assets',
        'total_liabilities',
        'net_worth',

        // ── Annex B totals (declarant exclusive properties) ───────────────────
        'annex_b_total_assets',
        'annex_b_total_liabilities',
        'annex_b_net_worth',

        // ── Annex C totals (spouse & children exclusive properties) ───────────
        'annex_c_total_assets',
        'annex_c_total_liabilities',
        'annex_c_net_worth',

        // ── Dates / oath ──────────────────────────────────────────────────────
        'date_signed',
        'declarant_id_presented',
        'subscribed_sworn_date',
        'person_administering_oath',

        // ── Admin ─────────────────────────────────────────────────────────────
        'remarks',
        'status',
        'resubmitted_at',
    ];

    protected $casts = [
        'as_of_date'            => 'date',
        'date_signed'           => 'date',
        'subscribed_sworn_date' => 'date',
        'resubmitted_at'        => 'datetime',

        // Compliance type booleans
        'compliance_assumption' => 'boolean',
        'compliance_annual'     => 'boolean',
        'compliance_exit'       => 'boolean',

        // Filing type booleans
        'joint_filing'          => 'boolean',
        'separate_filing'       => 'boolean',
        'not_applicable'        => 'boolean',

        // Multiple marriages
        'multiple_marriages_not_applicable' => 'boolean',

        // Business / relatives
        'no_business_interests'      => 'boolean',
        'no_relatives_in_government' => 'boolean',

        // Annex A totals
        'total_assets'      => 'decimal:2',
        'total_liabilities' => 'decimal:2',
        'net_worth'         => 'decimal:2',

        // Annex B totals
        'annex_b_total_assets'      => 'decimal:2',
        'annex_b_total_liabilities' => 'decimal:2',
        'annex_b_net_worth'         => 'decimal:2',

        // Annex C totals
        'annex_c_total_assets'      => 'decimal:2',
        'annex_c_total_liabilities' => 'decimal:2',
        'annex_c_net_worth'         => 'decimal:2',
    ];

    // =========================================================================
    //  RELATIONSHIPS
    // =========================================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Annex A ───────────────────────────────────────────────────────────────

    public function children(): HasMany
    {
        return $this->hasMany(SalnChild::class);
    }

    public function realProperties(): HasMany
    {
        return $this->hasMany(SalnRealProperty::class);
    }

    public function personalProperties(): HasMany
    {
        return $this->hasMany(SalnPersonalProperty::class);
    }

    public function liabilities(): HasMany
    {
        return $this->hasMany(SalnLiability::class);
    }

    public function businessInterests(): HasMany
    {
        return $this->hasMany(SalnBusinessInterest::class);
    }

    public function relativesInGovernment(): HasMany
    {
        return $this->hasMany(SalnRelativeGovernment::class);
    }

    // ── Annex B (declarant's exclusive properties) ────────────────────────────

    public function annexBRealProperties(): HasMany
    {
        return $this->hasMany(SalnAnnexBRealProperty::class);
    }

    public function annexBPersonalProperties(): HasMany
    {
        return $this->hasMany(SalnAnnexBPersonalProperty::class);
    }

    public function annexBLiabilities(): HasMany
    {
        return $this->hasMany(SalnAnnexBLiability::class);
    }

    public function annexBBusinessInterests(): HasMany
    {
        return $this->hasMany(SalnAnnexBBusinessInterest::class);
    }

    // ── Annex C (spouse & children's exclusive properties) ────────────────────

    public function annexCRealProperties(): HasMany
    {
        return $this->hasMany(SalnAnnexCRealProperty::class);
    }

    public function annexCPersonalProperties(): HasMany
    {
        return $this->hasMany(SalnAnnexCPersonalProperty::class);
    }

    public function annexCLiabilities(): HasMany
    {
        return $this->hasMany(SalnAnnexCLiability::class);
    }

    public function annexCBusinessInterests(): HasMany
    {
        return $this->hasMany(SalnAnnexCBusinessInterest::class);
    }

    // =========================================================================
    //  TOTALS CALCULATION
    // =========================================================================

    /**
     * Recalculate and persist ALL totals (Annex A, B, and C).
     * Call this after all relationships have been saved.
     */
    public function calculateTotals(): void
    {
        $this->load([
            'realProperties', 'personalProperties', 'liabilities',
            'annexBRealProperties', 'annexBPersonalProperties', 'annexBLiabilities',
            'annexCRealProperties', 'annexCPersonalProperties', 'annexCLiabilities',
        ]);

        // ── Annex A ───────────────────────────────────────────────────────────
        $this->total_assets = $this->realProperties->sum('current_fair_market_value')
            + $this->personalProperties->sum('acquisition_cost');
        $this->total_liabilities = $this->liabilities->sum('outstanding_balance');
        $this->net_worth = $this->total_assets - $this->total_liabilities;

        // ── Annex B ───────────────────────────────────────────────────────────
        $this->annex_b_total_assets = $this->annexBRealProperties->sum('current_fair_market_value')
            + $this->annexBPersonalProperties->sum('acquisition_cost');
        $this->annex_b_total_liabilities = $this->annexBLiabilities->sum('outstanding_balance');
        $this->annex_b_net_worth = $this->annex_b_total_assets - $this->annex_b_total_liabilities;

        // ── Annex C ───────────────────────────────────────────────────────────
        $this->annex_c_total_assets = $this->annexCRealProperties->sum('current_fair_market_value')
            + $this->annexCPersonalProperties->sum('acquisition_cost');
        $this->annex_c_total_liabilities = $this->annexCLiabilities->sum('outstanding_balance');
        $this->annex_c_net_worth = $this->annex_c_total_assets - $this->annex_c_total_liabilities;

        $this->saveQuietly();
    }

    // =========================================================================
    //  ACCESSORS
    // =========================================================================

    /**
     * Human-readable compliance type label.
     */
    public function getComplianceTypeLabelAttribute(): string
    {
        if ($this->compliance_assumption) return 'Assumption of Office';
        if ($this->compliance_annual)     return 'Annual Filing';
        if ($this->compliance_exit)       return 'Exit';
        return '—';
    }
}
