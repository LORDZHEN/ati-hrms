<?php

namespace App\Filament\Resources\PersonalDataSheetResource\Pages;

use App\Filament\Concerns\WorkflowHelper;
use App\Filament\Resources\PersonalDataSheetResource;
use App\Models\User;
use App\Notifications\PDSSubmittedNotification;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditPersonalDataSheet extends EditRecord
{
    use WorkflowHelper;

    protected static string $resource = PersonalDataSheetResource::class;

    // =========================================================================
    //  BOOT — redirect locked employees away from the edit URL
    // =========================================================================

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if (Auth::user()->role !== 'admin' && !static::canEmployeeEdit($this->record)) {
            Notification::make()
                ->title('Record is Locked')
                ->body('This PDS is approved and cannot be edited at this time.')
                ->warning()
                ->send();

            $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
        }
    }

    // =========================================================================
    //  HEADER ACTIONS
    // =========================================================================

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Print PDS')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->visible(fn() => $this->record->status === 'approved')
                ->url(fn() => route('pds.print', $this->record->id))
                ->openUrlInNewTab(),

            Actions\DeleteAction::make()
                ->visible(
                    fn() =>
                    Auth::user()->role === 'admin' ||
                    (Auth::user()->role === 'employee' && $this->record->status !== 'approved')
                ),
        ];
    }

    // =========================================================================
    //  FORM ACTIONS — hide save/resubmit when locked
    // =========================================================================

    protected function getFormActions(): array
    {
        // If locked, give only a back button — no save/submit
        if (!static::canEmployeeEdit($this->record)) {
            return [
                Actions\Action::make('back')
                    ->label('Back to List')
                    ->url($this->getResource()::getUrl('index'))
                    ->color('gray')
                    ->icon('heroicon-o-arrow-left'),
            ];
        }

        $isEmployee = Auth::user()->role === 'employee';

        return [
            Actions\Action::make('save')
                ->label($isEmployee ? 'Resubmit PDS' : 'Save Changes')
                ->submit('save')
                ->color('primary')
                ->icon($isEmployee ? 'heroicon-o-paper-airplane' : 'heroicon-o-check'),

            Actions\Action::make('cancel')
                ->label('Cancel')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->icon('heroicon-o-x-mark'),
        ];
    }

    // =========================================================================
    //  MUTATIONS
    // =========================================================================

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Belt-and-suspenders: abort even if policy is misconfigured
        if (Auth::user()->role !== 'admin') {
            abort_unless(static::canEmployeeEdit($this->record), 403, 'This record is locked.');

            $data['status'] = 'submitted';
            $data['editing_unlocked'] = false; // re-lock after employee saves
        }

        return $data;
    }

    protected function afterSave(): void
    {
        if (Auth::user()->role !== 'employee') {
            Notification::make()->title('PDS Updated')->success()->send();
            return;
        }

        $user = Auth::user();

        // Stamp resubmission time
        $this->record->updateQuietly(['created_at' => now()]);

        // Notify all admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new PDSSubmittedNotification($user, $this->record));
        }

        Notification::make()
            ->title('PDS Resubmitted Successfully')
            ->body('Your Personal Data Sheet has been updated and sent for review.')
            ->success()
            ->send();
    }

    // =========================================================================
    //  REDIRECT
    // =========================================================================

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // =========================================================================
    //  LIVEWIRE METHODS FOR REPEATER FIELDS
    // =========================================================================

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
        $education[] = ['level' => '', 'school_name' => '', 'degree' => '', 'from_year' => '', 'to_year' => '', 'honors' => ''];
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
        $civilService[] = ['career_service' => '', 'rating' => '', 'exam_date' => '', 'place' => '', 'license_no' => '', 'validity' => ''];
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
        $work[] = ['from' => '', 'to' => '', 'position' => '', 'agency' => '', 'salary' => '', 'salary_grade' => '', 'status' => '', 'is_government' => false];
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
        $voluntary[] = ['organization_name' => '', 'from_date' => '', 'to_date' => '', 'hours' => '', 'position' => ''];
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
        $ld[] = ['training_title' => '', 'from_date' => '', 'to_date' => '', 'hours' => '', 'type' => '', 'conducted_by' => ''];
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
