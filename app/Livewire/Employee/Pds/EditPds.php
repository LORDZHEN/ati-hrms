<?php

namespace App\Livewire\Employee\Pds;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\PersonalDataSheet;

class EditPds extends Component
{
    public PersonalDataSheet $pds;

    /* =====================================================
       MOUNT / INITIALIZE PDS
    ====================================================== */
    public function mount()
    {

        // Load or create PDS for the logged-in user
        $this->pds = PersonalDataSheet::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                // Default fields when creating a new record
                'surname' => '',
                'first_name' => '',
                'middle_name' => '',
                'name_extension' => '',
                'date_of_birth' => '',
                'place_of_birth' => '',
                'sex' => '',
                'civil_status' => '',
                'filipino' => false,
                'dual_citizenship' => false,
                'dual_citizenship_country' => '',
                'residential_address' => '',
                'res_zip' => '',
                'res_tel' => '',
                'permanent_address' => '',
                'perm_zip' => '',
                'perm_tel' => '',
                'mobile' => '',
                'email' => '',
                'spouse_surname' => '',
                'spouse_firstname' => '',
                'spouse_occupation' => '',
                'spouse_employer' => '',
                'father_name' => '',
                'mother_name' => '',
                'children' => [],
                'education' => [],
                'civil_service_eligibility' => [],
                'work_experience' => [],
                'voluntary_work' => [],
                'learning_development' => [],
                'special_skills' => [],
                'non_academic_distinctions' => [],
                'membership_association' => [],
                'related_third_degree' => false,
                'related_third_degree_details' => '',
                'related_fourth_degree' => false,
                'related_fourth_degree_details' => '',
                'has_admin_case' => false,
                'admin_case_details' => '',
                'has_criminal_case' => false,
                'criminal_case_status' => '',
                'criminal_case_date_filed' => null,
                'has_conviction' => false,
                'conviction_details' => '',
                'has_been_separated' => false,
                'separation_details' => '',
                'has_election_candidacy' => false,
                'election_candidacy_details' => '',
                'is_indigenous' => false,
                'indigenous_details' => '',
                'has_disability' => false,
                'disability_details' => '',
                'is_solo_parent' => false,
                'solo_parent_details' => '',
                'references' => [],
                'gov_id_type' => '',
                'gov_id_no' => '',
                'gov_id_issued' => '',
                'date_accomplished' => null,
            ]
        );

        // -----------------------------
        // CHILDREN
        // -----------------------------
        $this->pds->children = $this->pds->children ?? [
            ['name' => '', 'birthdate' => ''],
        ];

        // -----------------------------
        // EDUCATION
        // -----------------------------
        $this->pds->education = $this->pds->education ?? [
            ['level' => '', 'school_name' => '', 'degree' => '', 'from_year' => '', 'to_year' => '', 'honors' => ''],
        ];

        // -----------------------------
        // CIVIL SERVICE ELIGIBILITY
        // -----------------------------
        $this->pds['civil_service_eligibility'] = $this->pds['civil_service_eligibility'] ?? [
            ['career_service' => '', 'rating' => '', 'exam_date' => '', 'place' => '', 'license_no' => '', 'validity' => ''],
        ];

        // -----------------------------
        // WORK EXPERIENCE
        // -----------------------------
        $this->pds['work_experience'] = $this->pds['work_experience'] ?? [
            ['from' => '', 'to' => '', 'position' => '', 'agency' => '', 'salary' => '', 'salary_grade' => '', 'status' => '', 'is_government' => false],
        ];

        // -----------------------------
        // VOLUNTARY WORK
        // -----------------------------
        $this->pds['voluntary_work'] = $this->pds['voluntary_work'] ?? [
            ['organization_name' => '', 'from_date' => '', 'to_date' => '', 'hours' => '', 'position' => ''],
        ];

        // -----------------------------
        // LEARNING & DEVELOPMENT
        // -----------------------------
        $this->pds['learning_development'] = $this->pds['learning_development'] ?? [
            ['training_title' => '', 'from_date' => '', 'to_date' => '', 'hours' => '', 'type' => '', 'conducted_by' => ''],
        ];

        // -----------------------------
        // OTHER INFORMATION
        // -----------------------------
        $this->pds['special_skills'] = $this->pds['special_skills'] ?? [['skill' => '']];
        $this->pds['non_academic_distinctions'] = $this->pds['non_academic_distinctions'] ?? [['distinction' => '']];
        $this->pds['membership_association'] = $this->pds['membership_association'] ?? [['organization' => '']];

        // -----------------------------
        // C4 SECTION – OTHER PERSONAL INFORMATION
        // -----------------------------
        $this->pds->related_third_degree = $this->pds->related_third_degree ?? false;
        $this->pds->related_third_degree_details = $this->pds->related_third_degree_details ?? '';

        $this->pds->related_fourth_degree = $this->pds->related_fourth_degree ?? false;
        $this->pds->related_fourth_degree_details = $this->pds->related_fourth_degree_details ?? '';

        $this->pds->has_admin_case = $this->pds->has_admin_case ?? false;
        $this->pds->admin_case_details = $this->pds->admin_case_details ?? '';

        $this->pds->has_criminal_case = $this->pds->has_criminal_case ?? false;
        $this->pds->criminal_case_status = $this->pds->criminal_case_status ?? '';
        $this->pds->criminal_case_date_filed = $this->pds->criminal_case_date_filed ?? null;

        $this->pds->has_conviction = $this->pds->has_conviction ?? false;
        $this->pds->conviction_details = $this->pds->conviction_details ?? '';

        $this->pds->has_been_separated = $this->pds->has_been_separated ?? false;
        $this->pds->separation_details = $this->pds->separation_details ?? '';

        $this->pds->has_election_candidacy = $this->pds->has_election_candidacy ?? false;
        $this->pds->election_candidacy_details = $this->pds->election_candidacy_details ?? '';

        $this->pds->is_indigenous = $this->pds->is_indigenous ?? false;
        $this->pds->indigenous_details = $this->pds->indigenous_details ?? '';

        $this->pds->has_disability = $this->pds->has_disability ?? false;
        $this->pds->disability_details = $this->pds->disability_details ?? '';

        $this->pds->is_solo_parent = $this->pds->is_solo_parent ?? false;
        $this->pds->solo_parent_details = $this->pds->solo_parent_details ?? '';

        // -----------------------------
        // REFERENCES – repeater
        // -----------------------------
        $this->pds->references = $this->pds->references ?? [
            ['name' => '', 'address' => '', 'tel' => ''],
            ['name' => '', 'address' => '', 'tel' => ''],
            ['name' => '', 'address' => '', 'tel' => ''],
        ];

        // -----------------------------
        // GOVERNMENT ID
        // -----------------------------
        $this->pds->gov_id_type = $this->pds->gov_id_type ?? '';
        $this->pds->gov_id_no = $this->pds->gov_id_no ?? '';
        $this->pds->gov_id_issued = $this->pds->gov_id_issued ?? '';

        // -----------------------------
        // DATE ACCOMPLISHED
        // -----------------------------
        $this->pds->date_accomplished = $this->pds->date_accomplished ?? null;
    }

    /* =====================================================
       CHILDREN REPEATER
    ====================================================== */
    public function addChild()
    {
        $this->pds->children[] = ['name' => '', 'birthdate' => ''];
    }
    public function removeChild($index)
    {
        unset($this->pds->children[$index]);
        $this->pds->children = array_values($this->pds->children);
    }

    /* =====================================================
       EDUCATION REPEATER
    ====================================================== */
    public function addEducation()
    {
        $this->pds->education[] = ['level' => '', 'school_name' => '', 'degree' => '', 'from_year' => '', 'to_year' => '', 'honors' => ''];
    }
    public function removeEducation($index)
    {
        unset($this->pds->education[$index]);
        $this->pds->education = array_values($this->pds->education);
    }

    /* =====================================================
       CIVIL SERVICE ELIGIBILITY REPEATER
    ====================================================== */
    public function addEligibility()
    {
        $this->pds['civil_service_eligibility'][] = ['career_service' => '', 'rating' => '', 'exam_date' => '', 'place' => '', 'license_no' => '', 'validity' => ''];
    }
    public function removeEligibility($index)
    {
        unset($this->pds['civil_service_eligibility'][$index]);
        $this->pds['civil_service_eligibility'] = array_values($this->pds['civil_service_eligibility']);
    }

    /* =====================================================
       WORK EXPERIENCE REPEATER
    ====================================================== */
    public function addWork()
    {
        $this->pds['work_experience'][] = ['from' => '', 'to' => '', 'position' => '', 'agency' => '', 'salary' => '', 'salary_grade' => '', 'status' => '', 'is_government' => false];
    }
    public function removeWork($index)
    {
        unset($this->pds['work_experience'][$index]);
        $this->pds['work_experience'] = array_values($this->pds['work_experience']);
    }

    /* =====================================================
       VOLUNTARY WORK REPEATER
    ====================================================== */
    public function addVoluntary()
    {
        $this->pds['voluntary_work'][] = ['organization_name' => '', 'from_date' => '', 'to_date' => '', 'hours' => '', 'position' => ''];
    }
    public function removeVoluntary($index)
    {
        unset($this->pds['voluntary_work'][$index]);
        $this->pds['voluntary_work'] = array_values($this->pds['voluntary_work']);
    }

    /* =====================================================
       LEARNING & DEVELOPMENT REPEATER
    ====================================================== */
    public function addLD()
    {
        $this->pds['learning_development'][] = ['training_title' => '', 'from_date' => '', 'to_date' => '', 'hours' => '', 'type' => '', 'conducted_by' => ''];
    }
    public function removeLD($index)
    {
        unset($this->pds['learning_development'][$index]);
        $this->pds['learning_development'] = array_values($this->pds['learning_development']);
    }

    /* =====================================================
       OTHER INFORMATION REPEATER
    ====================================================== */
    public function addOther()
    {
        $this->pds['special_skills'][] = ['skill' => ''];
        $this->pds['non_academic_distinctions'][] = ['distinction' => ''];
        $this->pds['membership_association'][] = ['organization' => ''];
    }
    public function removeOther($index)
    {
        unset($this->pds['special_skills'][$index], $this->pds['non_academic_distinctions'][$index], $this->pds['membership_association'][$index]);
        $this->pds['special_skills'] = array_values($this->pds['special_skills']);
        $this->pds['non_academic_distinctions'] = array_values($this->pds['non_academic_distinctions']);
        $this->pds['membership_association'] = array_values($this->pds['membership_association']);
    }

    /* =====================================================
       REFERENCES REPEATER (C4)
    ====================================================== */
    public function addReference()
    {
        $this->pds->references[] = ['name' => '', 'address' => '', 'tel' => ''];
    }
    public function removeReference($index)
    {
        unset($this->pds->references[$index]);
        $this->pds->references = array_values($this->pds->references);
    }

    /* =====================================================
       SAVE PDS
    ====================================================== */
    public function save()
    {
        $this->pds->save();
        session()->flash('success', 'PDS saved successfully.');
    }

    /* =====================================================
       RENDER
    ====================================================== */
    public function render()
    {
        return view('livewire.employee.pds.edit-pds');
    }
}
