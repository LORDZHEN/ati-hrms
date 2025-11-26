<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalDataSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        // Personal Information
        'surname',
        'first_name',
        'name_extension',
        'middle_name',
        'date_of_birth',
        'place_of_birth',
        'sex',
        'civil_status',
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
        'year',

        // Citizenship
        'filipino',
        'dual_citizenship',
        'by_birth',
        'by_naturalization',
        'country',

        // Residential Address
        'res_house_block_lot_no',
        'res_street',
        'res_subdivision_village',
        'res_barangay',
        'res_city_municipality',
        'res_province',
        'res_zip_code',

        // Permanent Address
        'perm_house_block_lot_no',
        'perm_street',
        'perm_subdivision_village',
        'perm_barangay',
        'perm_city_municipality',
        'perm_province',
        'perm_zip_code',

        // Contact Information
        'telephone_no',
        'mobile_no',
        'email_address',

        // Family Background - Spouse
        'spouse_surname',
        'spouse_first_name',
        'spouse_name_extension',
        'spouse_middle_name',
        'spouse_occupation',
        'spouse_employer_business_name',
        'spouse_business_address',
        'spouse_telephone_no',

        // Family Background - Father
        'father_surname',
        'father_first_name',
        'father_name_extension',
        'father_middle_name',

        // Family Background - Mother
        'mother_surname',
        'mother_first_name',
        'mother_middle_name',

        // Children and Education (JSON fields)
        'children',
        'education',
        'eligibilities',
        'work_experience',
        'user_id',
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
        'eligibilities' => 'array',
        'work_experience' => 'array',
    ];

    // Accessor for full name
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

    // Accessor for complete residential address
    public function getResidentialAddressAttribute(): string
    {
        $address = [];

        if ($this->res_house_block_lot_no) {
            $address[] = $this->res_house_block_lot_no;
        }
        if ($this->res_street) {
            $address[] = $this->res_street;
        }
        if ($this->res_subdivision_village) {
            $address[] = $this->res_subdivision_village;
        }
        if ($this->res_barangay) {
            $address[] = $this->res_barangay;
        }
        if ($this->res_city_municipality) {
            $address[] = $this->res_city_municipality;
        }
        if ($this->res_province) {
            $address[] = $this->res_province;
        }
        if ($this->res_zip_code) {
            $address[] = $this->res_zip_code;
        }

        return implode(', ', $address);
    }

    // Accessor for complete permanent address
    public function getPermanentAddressAttribute(): string
    {
        $address = [];

        if ($this->perm_house_block_lot_no) {
            $address[] = $this->perm_house_block_lot_no;
        }
        if ($this->perm_street) {
            $address[] = $this->perm_street;
        }
        if ($this->perm_subdivision_village) {
            $address[] = $this->perm_subdivision_village;
        }
        if ($this->perm_barangay) {
            $address[] = $this->perm_barangay;
        }
        if ($this->perm_city_municipality) {
            $address[] = $this->perm_city_municipality;
        }
        if ($this->perm_province) {
            $address[] = $this->perm_province;
        }
        if ($this->perm_zip_code) {
            $address[] = $this->perm_zip_code;
        }

        return implode(', ', $address);
    }

    // Accessor for spouse full name
    public function getSpouseFullNameAttribute(): ?string
    {
        if (!$this->spouse_first_name && !$this->spouse_surname) {
            return null;
        }

        $name = $this->spouse_first_name;

        if ($this->spouse_middle_name) {
            $name .= ' ' . $this->spouse_middle_name;
        }

        if ($this->spouse_surname) {
            $name .= ' ' . $this->spouse_surname;
        }

        if ($this->spouse_name_extension) {
            $name .= ' ' . $this->spouse_name_extension;
        }

        return $name;
    }

    // Accessor for father full name
    public function getFatherFullNameAttribute(): ?string
    {
        if (!$this->father_first_name && !$this->father_surname) {
            return null;
        }

        $name = $this->father_first_name;

        if ($this->father_middle_name) {
            $name .= ' ' . $this->father_middle_name;
        }

        if ($this->father_surname) {
            $name .= ' ' . $this->father_surname;
        }

        if ($this->father_name_extension) {
            $name .= ' ' . $this->father_name_extension;
        }

        return $name;
    }

    // Accessor for mother full name
    public function getMotherFullNameAttribute(): ?string
    {
        if (!$this->mother_first_name && !$this->mother_surname) {
            return null;
        }

        $name = $this->mother_first_name;

        if ($this->mother_middle_name) {
            $name .= ' ' . $this->mother_middle_name;
        }

        if ($this->mother_surname) {
            $name .= ' ' . $this->mother_surname;
        }

        return $name;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
