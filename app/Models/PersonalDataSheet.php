<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalDataSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        // ── Identity ──────────────────────────────────────────────────────────
        'user_id',
        'surname',
        'first_name',
        'middle_name',
        'name_extension',
        'date_of_birth',
        'place_of_birth',
        'sex',
        'civil_status',
        'filipino',
        'dual_citizenship',
        'dual_citizenship_country',
        'mobile',
        'email',

        // ── Government IDs ────────────────────────────────────────────────────
        'height',
        'weight',
        'blood_type',
        'gsis_id_no',
        'pag_ibig_id_no',
        'philhealth_no',
        'sss_no',
        'tin_no',
        'agency_employee_no',

        // ── Residential Address ───────────────────────────────────────────────
        'res_house_block_lot_no',
        'res_street',
        'res_subdivision_village',
        'res_barangay',
        'res_city_municipality',
        'res_province',
        'res_zip_code',

        // ── Permanent Address ─────────────────────────────────────────────────
        'perm_house_block_lot_no',
        'perm_street',
        'perm_subdivision_village',
        'perm_barangay',
        'perm_city_municipality',
        'perm_province',
        'perm_zip_code',
        'telephone_no',

        // ── Spouse ────────────────────────────────────────────────────────────
        'spouse_surname',
        'spouse_first_name',
        'spouse_middle_name',
        'spouse_name_extension',
        'spouse_occupation',
        'spouse_employer_business_name',
        'spouse_business_address',
        'spouse_telephone_no',

        // ── Father ────────────────────────────────────────────────────────────
        'father_surname',
        'father_first_name',
        'father_middle_name',
        'father_name_extension',

        // ── Mother ────────────────────────────────────────────────────────────
        'mother_surname',
        'mother_first_name',
        'mother_middle_name',

        // ── JSON / Repeater sections ──────────────────────────────────────────
        'children',
        'education',
        'civil_service_eligibility',
        'work_experience',
        'voluntary_work',
        'learning_development',
        'special_skills',
        'non_academic_distinctions',
        'membership_association',

        // ── Questionnaire ─────────────────────────────────────────────────────
        'related_third_degree',
        'related_third_degree_details',
        'related_fourth_degree',
        'related_fourth_degree_details',
        'has_admin_case',
        'admin_case_details',
        'has_criminal_case',
        'criminal_case_status',
        'criminal_case_date_filed',
        'has_conviction',
        'conviction_details',
        'has_been_separated',
        'separation_details',
        'has_election_candidacy',
        'election_candidacy_details',
        'is_indigenous',
        'indigenous_details',
        'has_disability',
        'disability_details',
        'is_solo_parent',
        'solo_parent_details',

        // ── References & ID ───────────────────────────────────────────────────
        'references',
        'gov_id_type',
        'gov_id_no',
        'gov_id_issued',
        'date_accomplished',

        // ── Admin / Workflow ──────────────────────────────────────────────────
        'remarks',
        'status',
        'year',

        // ── Workflow Lock (added for approval lock feature) ───────────────────
        //    false  = editing locked after approval  (default)
        //    true   = admin has unlocked for employee re-editing
        'editing_unlocked',
    ];

    protected $casts = [
        // ── Dates ─────────────────────────────────────────────────────────────
        'date_of_birth'          => 'date',
        'criminal_case_date_filed' => 'date',

        // ── Booleans ──────────────────────────────────────────────────────────
        'filipino'               => 'boolean',
        'dual_citizenship'       => 'boolean',
        'by_birth'               => 'boolean',
        'by_naturalization'      => 'boolean',
        'related_third_degree'   => 'boolean',
        'related_fourth_degree'  => 'boolean',
        'is_indigenous'          => 'boolean',
        'has_disability'         => 'boolean',
        'is_solo_parent'         => 'boolean',
        'has_admin_case'         => 'boolean',
        'has_criminal_case'      => 'boolean',
        'has_conviction'         => 'boolean',
        'has_been_separated'     => 'boolean',
        'has_election_candidacy' => 'boolean',

        // ── Workflow lock ─────────────────────────────────────────────────────
        'editing_unlocked'       => 'boolean',

        // ── Decimals ──────────────────────────────────────────────────────────
        'height'                 => 'decimal:2',
        'weight'                 => 'decimal:1',

        // ── JSON arrays ───────────────────────────────────────────────────────
        'children'                 => 'array',
        'education'                => 'array',
        'civil_service_eligibility' => 'array',
        'work_experience'          => 'array',
        'voluntary_work'           => 'array',
        'learning_development'     => 'array',
        'special_skills'           => 'array',
        'non_academic_distinctions' => 'array',
        'membership_association'   => 'array',
        'references'               => 'array',
    ];

    /* ============================================================
       RELATIONSHIPS
    ============================================================ */

    /**
     * Each PDS belongs to a User (employee).
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /* ============================================================
       ACCESSORS
    ============================================================ */

    public function getFullNameAttribute(): string
    {
        $name = $this->first_name;

        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }

        $name .= ' ' . $this->surname;

        if ($this->name_extension) {
            $name .= ' ' . $this->name_extension;
        }

        return $name;
    }

    public function getResidentialAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->res_house_block_lot_no,
            $this->res_street,
            $this->res_subdivision_village,
            $this->res_barangay,
            $this->res_city_municipality,
            $this->res_province,
            $this->res_zip_code,
        ]));
    }

    public function getPermanentAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->perm_house_block_lot_no,
            $this->perm_street,
            $this->perm_subdivision_village,
            $this->perm_barangay,
            $this->perm_city_municipality,
            $this->perm_province,
            $this->perm_zip_code,
        ]));
    }

    public function getSpouseFullNameAttribute(): ?string
    {
        if (! $this->spouse_first_name && ! $this->spouse_surname) {
            return null;
        }

        $name = $this->spouse_first_name;

        if ($this->spouse_middle_name) {
            $name .= ' ' . $this->spouse_middle_name;
        }

        $name .= ' ' . $this->spouse_surname;

        if ($this->spouse_name_extension) {
            $name .= ' ' . $this->spouse_name_extension;
        }

        return $name;
    }

    public function getFatherFullNameAttribute(): ?string
    {
        if (! $this->father_first_name && ! $this->father_surname) {
            return null;
        }

        $name = $this->father_first_name;

        if ($this->father_middle_name) {
            $name .= ' ' . $this->father_middle_name;
        }

        $name .= ' ' . $this->father_surname;

        if ($this->father_name_extension) {
            $name .= ' ' . $this->father_name_extension;
        }

        return $name;
    }

    public function getMotherFullNameAttribute(): ?string
    {
        if (! $this->mother_first_name && ! $this->mother_surname) {
            return null;
        }

        $name = $this->mother_first_name;

        if ($this->mother_middle_name) {
            $name .= ' ' . $this->mother_middle_name;
        }

        $name .= ' ' . $this->mother_surname;

        return $name;
    }

    /* ============================================================
       HELPER METHODS
    ============================================================ */

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Whether the record is currently locked for employee editing.
     * A record is locked when it is approved AND editing_unlocked is false.
     */
    public function isEditingLocked(): bool
    {
        return $this->status === 'approved' && ! $this->editing_unlocked;
    }
}
