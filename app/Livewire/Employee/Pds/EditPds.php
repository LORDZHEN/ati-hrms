<?php

namespace App\Livewire\Employee\Pds;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\PersonalDataSheet;

class EditPds extends Component
{
    public PersonalDataSheet $pds;
    public array $form = [];
    public bool $sameAsPermanent = false;

    protected $rules = [
        'form.surname' => 'required|string|max:255',
        'form.first_name' => 'required|string|max:255',
        'form.middle_name' => 'nullable|string|max:255',
        'form.name_extension' => 'nullable|string|max:50',
        'form.date_of_birth' => 'nullable|date',
        'form.place_of_birth' => 'nullable|string|max:255',
        'form.sex' => 'nullable|in:Male,Female',
        'form.civil_status' => 'nullable|in:Single,Married,Widowed,Separated',
        'form.height' => 'nullable|numeric',
        'form.weight' => 'nullable|numeric',
        'form.blood_type' => 'nullable|string|max:10',
        'form.gsis_id_no' => 'nullable|string|max:50',
        'form.pag_ibig_id_no' => 'nullable|string|max:50',
        'form.philhealth_no' => 'nullable|string|max:50',
        'form.sss_no' => 'nullable|string|max:50',
        'form.tin_no' => 'nullable|string|max:50',
        'form.agency_employee_no' => 'nullable|string|max:50',
        'form.mobile' => 'nullable|string|max:255',
        'form.email' => 'nullable|email|max:255',
        'form.filipino' => 'nullable|boolean',
        'form.dual_citizenship' => 'nullable|boolean',
        'form.dual_citizenship_country' => 'nullable|string|max:255',

        // Address fields
        'form.perm_house_block_lot_no' => 'nullable|string|max:255',
        'form.perm_street' => 'nullable|string|max:255',
        'form.perm_subdivision_village' => 'nullable|string|max:255',
        'form.perm_barangay' => 'nullable|string|max:255',
        'form.perm_city_municipality' => 'nullable|string|max:255',
        'form.perm_province' => 'nullable|string|max:255',
        'form.perm_zip_code' => 'nullable|string|max:20',

        'form.res_house_block_lot_no' => 'nullable|string|max:255',
        'form.res_street' => 'nullable|string|max:255',
        'form.res_subdivision_village' => 'nullable|string|max:255',
        'form.res_barangay' => 'nullable|string|max:255',
        'form.res_city_municipality' => 'nullable|string|max:255',
        'form.res_province' => 'nullable|string|max:255',
        'form.res_zip_code' => 'nullable|string|max:20',

        // Family
        'form.spouse_surname' => 'nullable|string|max:255',
        'form.spouse_first_name' => 'nullable|string|max:255',
        'form.spouse_middle_name' => 'nullable|string|max:255',
        'form.spouse_name_extension' => 'nullable|string|max:50',
        'form.spouse_occupation' => 'nullable|string|max:255',
        'form.spouse_employer_business_name' => 'nullable|string|max:255',

        'form.father_surname' => 'nullable|string|max:255',
        'form.father_first_name' => 'nullable|string|max:255',
        'form.father_middle_name' => 'nullable|string|max:255',
        'form.father_name_extension' => 'nullable|string|max:50',

        'form.mother_surname' => 'nullable|string|max:255',
        'form.mother_first_name' => 'nullable|string|max:255',
        'form.mother_middle_name' => 'nullable|string|max:255',

        // Arrays
        'form.children' => 'nullable|array',
        'form.children.*.name' => 'nullable|string|max:255',
        'form.children.*.birthdate' => 'nullable|date',

        'form.education' => 'nullable|array',
        'form.education.*.level' => 'nullable|string|max:255',
        'form.education.*.school_name' => 'nullable|string|max:255',
        'form.education.*.degree' => 'nullable|string|max:255',
        'form.education.*.from_year' => 'nullable|string|max:4',
        'form.education.*.to_year' => 'nullable|string|max:4',
        'form.education.*.honors' => 'nullable|string|max:255',

        'form.civil_service_eligibility' => 'nullable|array',
        'form.work_experience' => 'nullable|array',
        'form.voluntary_work' => 'nullable|array',
        'form.learning_development' => 'nullable|array',
        'form.special_skills' => 'nullable|array',
        'form.non_academic_distinctions' => 'nullable|array',
        'form.membership_association' => 'nullable|array',

        // Questions
        'form.related_third_degree' => 'nullable|boolean',
        'form.related_third_degree_details' => 'nullable|string|max:500',
        'form.related_fourth_degree' => 'nullable|boolean',
        'form.related_fourth_degree_details' => 'nullable|string|max:500',
        'form.has_admin_case' => 'nullable|boolean',
        'form.admin_case_details' => 'nullable|string|max:500',
        'form.has_criminal_case' => 'nullable|boolean',
        'form.criminal_case_status' => 'nullable|string|max:255',
        'form.criminal_case_date_filed' => 'nullable|date',
        'form.has_conviction' => 'nullable|boolean',
        'form.conviction_details' => 'nullable|string|max:500',
        'form.has_been_separated' => 'nullable|boolean',
        'form.separation_details' => 'nullable|string|max:500',
        'form.has_election_candidacy' => 'nullable|boolean',
        'form.election_candidacy_details' => 'nullable|string|max:500',
        'form.is_indigenous' => 'nullable|boolean',
        'form.indigenous_details' => 'nullable|string|max:500',
        'form.has_disability' => 'nullable|boolean',
        'form.disability_details' => 'nullable|string|max:500',
        'form.is_solo_parent' => 'nullable|boolean',
        'form.solo_parent_details' => 'nullable|string|max:500',

        // References
        'form.references' => 'nullable|array',
        'form.references.*.name' => 'nullable|string|max:255',
        'form.references.*.address' => 'nullable|string|max:500',
        'form.references.*.tel' => 'nullable|string|max:50',

        // Gov ID
        'form.gov_id_type' => 'nullable|string|max:255',
        'form.gov_id_no' => 'nullable|string|max:255',
        'form.gov_id_issued' => 'nullable|string|max:255',
        'form.date_accomplished' => 'nullable|date',
    ];

    public function mount(): void
    {
        $this->pds = PersonalDataSheet::firstOrCreate(
            ['user_id' => Auth::id()]
        );

        // Load all fillable fields into form
        $this->form = $this->pds->toArray();

        $this->initializeArrays();
    }

    protected function initializeArrays(): void
    {
        // Initialize arrays only if they're null or empty
        if (empty($this->form['children'])) {
            $this->form['children'] = array_fill(0, 4, [
                'name' => '',
                'birthdate' => '',
            ]);
        }

        if (empty($this->form['education'])) {
            $this->form['education'] = array_fill(0, 4, [
                'level' => '',
                'school_name' => '',
                'degree' => '',
                'from_year' => '',
                'to_year' => '',
                'honors' => '',
            ]);
        }

        if (empty($this->form['civil_service_eligibility'])) {
            $this->form['civil_service_eligibility'] = array_fill(0, 7, [
                'career_service' => '',
                'rating' => '',
                'exam_date' => '',
                'place' => '',
                'license_no' => '',
                'validity' => '',
            ]);
        }

        if (empty($this->form['work_experience'])) {
            $this->form['work_experience'] = array_fill(0, 28, [
                'from' => '',
                'to' => '',
                'position' => '',
                'agency' => '',
                'salary' => '',
                'salary_grade' => '',
                'status' => '',
                'is_government' => false,
            ]);
        }

        if (empty($this->form['voluntary_work'])) {
            $this->form['voluntary_work'] = array_fill(0, 7, [
                'organization_name' => '',
                'from_date' => '',
                'to_date' => '',
                'hours' => '',
                'position' => '',
            ]);
        }

        if (empty($this->form['learning_development'])) {
            $this->form['learning_development'] = array_fill(0, 21, [
                'training_title' => '',
                'from_date' => '',
                'to_date' => '',
                'hours' => '',
                'type' => '',
                'conducted_by' => '',
            ]);
        }

        if (empty($this->form['special_skills'])) {
            $this->form['special_skills'] = array_fill(0, 7, [
                'skill' => '',
            ]);
        }

        if (empty($this->form['non_academic_distinctions'])) {
            $this->form['non_academic_distinctions'] = array_fill(0, 7, [
                'distinction' => '',
            ]);
        }

        if (empty($this->form['membership_association'])) {
            $this->form['membership_association'] = array_fill(0, 7, [
                'organization' => '',
            ]);
        }

        if (empty($this->form['references'])) {
            $this->form['references'] = array_fill(0, 3, [
                'name' => '',
                'address' => '',
                'tel' => '',
            ]);
        }
    }

    public function updatedSameAsPermanent(): void
    {
        if ($this->sameAsPermanent) {
            $this->form['res_house_block_lot_no'] = $this->form['perm_house_block_lot_no'] ?? '';
            $this->form['res_street'] = $this->form['perm_street'] ?? '';
            $this->form['res_subdivision_village'] = $this->form['perm_subdivision_village'] ?? '';
            $this->form['res_barangay'] = $this->form['perm_barangay'] ?? '';
            $this->form['res_city_municipality'] = $this->form['perm_city_municipality'] ?? '';
            $this->form['res_province'] = $this->form['perm_province'] ?? '';
            $this->form['res_zip_code'] = $this->form['perm_zip_code'] ?? '';
        }
    }

    public function addRow(string $key): void
    {
        $this->form[$key][] = $this->form[$key][0] ?? [];
    }

    public function removeRow(string $key, int $index): void
    {
        unset($this->form[$key][$index]);
        $this->form[$key] = array_values($this->form[$key]);
    }

    public function save(): void
    {
        logger('Save method called for user: ' . Auth::id());
        logger('Form data before validation: ' . json_encode($this->form));

        try {
            $this->validate();
            logger('Validation passed');
        } catch (\Illuminate\Validation\ValidationException $e) {
            logger('Validation failed: ' . json_encode($e->errors()));
            session()->flash('error', 'Validation failed. Please check your inputs.');
            return;
        }

        // Clean up empty array entries before saving
        $dataToSave = $this->form;

        foreach (['children', 'education', 'civil_service_eligibility', 'work_experience',
                  'voluntary_work', 'learning_development', 'special_skills',
                  'non_academic_distinctions', 'membership_association', 'references'] as $arrayField) {
            if (isset($dataToSave[$arrayField]) && is_array($dataToSave[$arrayField])) {
                // Filter out completely empty entries
                $dataToSave[$arrayField] = array_values(array_filter($dataToSave[$arrayField], function($item) {
                    return !empty(array_filter($item, fn($v) => !empty($v)));
                }));

                // If array is now empty, set to null to avoid saving empty JSON arrays
                if (empty($dataToSave[$arrayField])) {
                    $dataToSave[$arrayField] = null;
                }
            }
        }

        // Convert boolean values for radio buttons (they come as "1" or "0" strings)
        $booleanFields = [
            'filipino', 'dual_citizenship', 'related_third_degree', 'related_fourth_degree',
            'has_admin_case', 'has_criminal_case', 'has_conviction', 'has_been_separated',
            'has_election_candidacy', 'is_indigenous', 'has_disability', 'is_solo_parent'
        ];

        foreach ($booleanFields as $field) {
            if (isset($dataToSave[$field])) {
                $dataToSave[$field] = filter_var($dataToSave[$field], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            }
        }

        logger('Form data after processing: ' . json_encode($dataToSave));

        try {
            $result = $this->pds->update($dataToSave);
            logger('Update result: ' . ($result ? 'Success' : 'Failed'));

            // Refresh to get latest data
            $this->pds->refresh();
            logger('Model after update: ' . json_encode($this->pds->toArray()));

            session()->flash('success', 'Personal Data Sheet saved successfully.');

        } catch (\Exception $e) {
            logger('Update failed: ' . $e->getMessage());
            logger('Stack trace: ' . $e->getTraceAsString());
            session()->flash('error', 'Failed to save: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.employee.pds.edit-pds');
    }
}
