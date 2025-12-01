<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class UpdateProfile extends Component
{
    use WithFileUploads;

    public $editingProfile = false;

    public $employee_id;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $suffix;
    public $photo;
    public $position;
    public $employment_status;
    public $department;

    public function mount()
    {
        $user = Auth::user();

        $this->employee_id = $user->employee_id;
        $this->first_name = $user->first_name;
        $this->middle_name = $user->middle_name;
        $this->last_name = $user->last_name;
        $this->suffix = $user->suffix;
        $this->position = $user->position;
        $this->employment_status = $user->employment_status;
        $this->department = $user->department;
    }

    public function update()
    {
        $this->validate([
            'employee_id' => 'required|string|max:255|unique:users,employee_id,' . Auth::id(),
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'position' => 'nullable|string|max:255',
            'employment_status' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:5120',
        ]);

        $user = Auth::user();

        $user->employee_id = $this->employee_id;
        $user->first_name = $this->first_name;
        $user->middle_name = $this->middle_name;
        $user->last_name = $this->last_name;
        $user->suffix = $this->suffix;
        $user->position = $this->position;
        $user->employment_status = $this->employment_status;
        $user->department = $this->department;

        if ($this->photo) {
            $user->profile_photo_path = $this->photo->store('profile-photos', 'public');
        }

        $user->name = implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
            $this->suffix,
        ]));

        $user->save();

        $this->editingProfile = false;

        // Filament toast notification
        Notification::make()
            ->title('Profile Updated')
            ->body('Your profile has been updated successfully.')
            ->success()
            ->send();

        return redirect()->route('filament.hrms.pages.profile');
    }

    public function render()
    {
        return view('livewire.employee.update-profile');
    }
}
