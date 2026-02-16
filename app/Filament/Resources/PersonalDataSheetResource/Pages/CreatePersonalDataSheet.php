<?php

namespace App\Filament\Resources\PersonalDataSheetResource\Pages;

use App\Filament\Resources\PersonalDataSheetResource;
use App\Models\User;
use App\Notifications\PDSSubmittedNotification;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as LaravelNotification;

class CreatePersonalDataSheet extends CreateRecord
{
    protected static string $resource = PersonalDataSheetResource::class;

    /**
     * 🔑 Attach PDS to logged-in user and set initial status
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['status'] = 'submitted'; // Set initial status

        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Submit PDS')
                ->submit('create')
                ->color('primary'),

            Actions\Action::make('cancel')
                ->label('Cancel')
                ->url($this->getResource()::getUrl('index'))
                ->color('secondary'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * 🔔 Notify admins after successful submission
     */
    protected function afterCreate(): void
    {
        $admins = User::where('role', 'admin')->get();

        LaravelNotification::send(
            $admins,
            new PDSSubmittedNotification(Auth::user(), $this->record)
        );

        Notification::make()
            ->title('PDS Submitted Successfully!')
            ->body('Your Personal Data Sheet has been sent for review.')
            ->success()
            ->send();
    }

    public function addChild()
{
    $children = $this->data['children'] ?? [];
    $children[] = ['name' => '', 'birthdate' => ''];
    $this->data['children'] = $children;
}

public function removeChild($index)
{
    $children = $this->data['children'] ?? [];
    unset($children[$index]);
    $this->data['children'] = array_values($children);
}

public function addEducation()
{
    $education = $this->data['education'] ?? [];
    $education[] = [
        'level' => '',
        'school_name' => '',
        'degree' => '',
        'from_year' => '',
        'to_year' => '',
        'honors' => ''
    ];
    $this->data['education'] = $education;
}

public function removeEducation($index)
{
    $education = $this->data['education'] ?? [];
    unset($education[$index]);
    $this->data['education'] = array_values($education);
}

public function addCivilService()
{
    $civilService = $this->data['civil_service_eligibility'] ?? [];
    $civilService[] = [
        'career_service' => '',
        'rating' => '',
        'exam_date' => '',
        'place' => '',
        'license_no' => '',
        'validity' => ''
    ];
    $this->data['civil_service_eligibility'] = $civilService;
}

public function removeCivilService($index)
{
    $civilService = $this->data['civil_service_eligibility'] ?? [];
    unset($civilService[$index]);
    $this->data['civil_service_eligibility'] = array_values($civilService);
}

public function addWorkExperience()
{
    $work = $this->data['work_experience'] ?? [];
    $work[] = [
        'from' => '',
        'to' => '',
        'position' => '',
        'agency' => '',
        'salary' => '',
        'salary_grade' => '',
        'status' => '',
        'is_government' => false
    ];
    $this->data['work_experience'] = $work;
}

public function removeWorkExperience($index)
{
    $work = $this->data['work_experience'] ?? [];
    unset($work[$index]);
    $this->data['work_experience'] = array_values($work);
}

public function addVoluntaryWork()
{
    $voluntary = $this->data['voluntary_work'] ?? [];
    $voluntary[] = [
        'organization_name' => '',
        'from_date' => '',
        'to_date' => '',
        'hours' => '',
        'position' => ''
    ];
    $this->data['voluntary_work'] = $voluntary;
}

public function removeVoluntaryWork($index)
{
    $voluntary = $this->data['voluntary_work'] ?? [];
    unset($voluntary[$index]);
    $this->data['voluntary_work'] = array_values($voluntary);
}

public function addLearningDevelopment()
{
    $ld = $this->data['learning_development'] ?? [];
    $ld[] = [
        'training_title' => '',
        'from_date' => '',
        'to_date' => '',
        'hours' => '',
        'type' => '',
        'conducted_by' => ''
    ];
    $this->data['learning_development'] = $ld;
}

public function removeLearningDevelopment($index)
{
    $ld = $this->data['learning_development'] ?? [];
    unset($ld[$index]);
    $this->data['learning_development'] = array_values($ld);
}

public function addSpecialSkill()
{
    $skills = $this->data['special_skills'] ?? [];
    $skills[] = ['skill' => ''];
    $this->data['special_skills'] = $skills;
}

public function removeSpecialSkill($index)
{
    $skills = $this->data['special_skills'] ?? [];
    unset($skills[$index]);
    $this->data['special_skills'] = array_values($skills);
}

public function addDistinction()
{
    $distinctions = $this->data['non_academic_distinctions'] ?? [];
    $distinctions[] = ['distinction' => ''];
    $this->data['non_academic_distinctions'] = $distinctions;
}

public function removeDistinction($index)
{
    $distinctions = $this->data['non_academic_distinctions'] ?? [];
    unset($distinctions[$index]);
    $this->data['non_academic_distinctions'] = array_values($distinctions);
}

public function addMembership()
{
    $memberships = $this->data['membership_association'] ?? [];
    $memberships[] = ['organization' => ''];
    $this->data['membership_association'] = $memberships;
}

public function removeMembership($index)
{
    $memberships = $this->data['membership_association'] ?? [];
    unset($memberships[$index]);
    $this->data['membership_association'] = array_values($memberships);
}

public function addReference()
{
    $references = $this->data['references'] ?? [];
    if (count($references) < 3) {
        $references[] = ['name' => '', 'address' => '', 'tel' => ''];
        $this->data['references'] = $references;
    }
}

public function removeReference($index)
{
    $references = $this->data['references'] ?? [];
    if (count($references) > 3) {
        unset($references[$index]);
        $this->data['references'] = array_values($references);
    }
}

// Add this to auto-copy residential to permanent address
public function updatedDataSameAsResidential($value)
{
    if ($value) {
        $this->data['perm_house_block_lot_no'] = $this->data['res_house_block_lot_no'] ?? '';
        $this->data['perm_street'] = $this->data['res_street'] ?? '';
        $this->data['perm_subdivision_village'] = $this->data['res_subdivision_village'] ?? '';
        $this->data['perm_barangay'] = $this->data['res_barangay'] ?? '';
        $this->data['perm_city_municipality'] = $this->data['res_city_municipality'] ?? '';
        $this->data['perm_province'] = $this->data['res_province'] ?? '';
        $this->data['perm_zip_code'] = $this->data['res_zip_code'] ?? '';
    }
}
}
