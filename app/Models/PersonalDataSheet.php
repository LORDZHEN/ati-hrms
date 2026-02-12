<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalDataSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        // Personal Information
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
        'spouse_surname',
        'spouse_first_name',
        'spouse_occupation',
        'father_first_name',
        'mother_first_name',
        'children',
        'education',
        'civil_service_eligibility', // Standardized to match component usage
        'work_experience',
        'voluntary_work',
        'learning_development',
        'special_skills',
        'non_academic_distinctions',
        'membership_association',
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
        'references',
        'gov_id_type',
        'gov_id_no',
        'gov_id_issued',
        'date_accomplished',
        // Retained original fields for compatibility (you can remove unused ones)
        'height',
        'weight',
        'blood_type',
        'gsis_id_no',
        'pag_ibig_id_no',
        'philhealth_no',
        'sss_no',
        'tin_no',
        'agency_employee_no',
        'remarks',
        'status',
        'year',
        // 'by_birth',
        // 'by_naturalization',
        // 'country',
        'res_house_block_lot_no',
        'res_street',
        'res_subdivision_village',
        'res_barangay',
        'res_city_municipality',
        'res_province',
        'res_zip_code',
        'perm_house_block_lot_no',
        'perm_street',
        'perm_subdivision_village',
        'perm_barangay',
        'perm_city_municipality',
        'perm_province',
        'perm_zip_code',
        'telephone_no',
        'mobile_no',
        'email_address',
        'spouse_name_extension',
        'spouse_middle_name',
        'spouse_employer_business_name',
        'spouse_business_address',
        'spouse_telephone_no',
        'father_surname',
        'father_first_name',
        'father_name_extension',
        'father_middle_name',
        'mother_surname',
        'mother_first_name',
        'mother_middle_name',

    ];


    protected $casts = [
        'date_of_birth' => 'date',
        'filipino' => 'boolean',
        'dual_citizenship' => 'boolean',
        'by_birth' => 'boolean',
        'by_naturalization' => 'boolean',
        'height' => 'decimal:2',
        'weight' => 'decimal:1',
        'children' => 'array',
        'education' => 'array',
        'civil_service_eligibility' => 'array',
        'work_experience' => 'array',
        'voluntary_work' => 'array',
        'learning_development' => 'array',
        'special_skills' => 'array',
        'non_academic_distinctions' => 'array',
        'membership_association' => 'array',
        'related_third_degree' => 'boolean',
        'related_fourth_degree' => 'boolean',
        'is_indigenous' => 'boolean',
        'has_disability' => 'boolean',
        'is_solo_parent' => 'boolean',
        'criminal_case_date_filed' => 'date',
        'has_admin_case' => 'boolean',
        'has_criminal_case' => 'boolean',
        'has_conviction' => 'boolean',
        'has_been_separated' => 'boolean',
        'has_election_candidacy' => 'boolean',
        'references' => 'array',
    ];

    /* ============================================================
       RELATIONSHIPS
    ============================================================ */

    /**
     * Each PDS belongs to a User (employee)
     */
    public function employee()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }


    /* ============================================================
       ACCESSORS
    ============================================================ */

    public function getFullNameAttribute(): string
    {
        $name = $this->first_name;

        if ($this->middle_name)
            $name .= ' ' . $this->middle_name;
        $name .= ' ' . $this->surname;
        if ($this->name_extension)
            $name .= ' ' . $this->name_extension;

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
        if (!$this->spouse_first_name && !$this->spouse_surname)
            return null;

        $name = $this->spouse_first_name;
        if ($this->spouse_middle_name)
            $name .= ' ' . $this->spouse_middle_name;
        $name .= ' ' . $this->spouse_surname;
        if ($this->spouse_name_extension)
            $name .= ' ' . $this->spouse_name_extension;

        return $name;
    }

    public function getFatherFullNameAttribute(): ?string
    {
        if (!$this->father_first_name && !$this->father_surname)
            return null;

        $name = $this->father_first_name;
        if ($this->father_middle_name)
            $name .= ' ' . $this->father_middle_name;
        $name .= ' ' . $this->father_surname;
        if ($this->father_name_extension)
            $name .= ' ' . $this->father_name_extension;

        return $name;
    }

    public function getMotherFullNameAttribute(): ?string
    {
        if (!$this->mother_first_name && !$this->mother_surname)
            return null;

        $name = $this->mother_first_name;
        if ($this->mother_middle_name)
            $name .= ' ' . $this->mother_middle_name;
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
}
