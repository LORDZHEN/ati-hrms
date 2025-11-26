<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class UpdateProfile extends Component
{
    use WithFileUploads;

    public $editingProfile = false;

    public $first_name;
    public $last_name;
    public $photo;
    public $position;
    public $employment_status;
    public $department;

    public function mount()
    {
        $user = Auth::user();

        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->position = $user->position;
        $this->employment_status = $user->employment_status;
        $this->department = $user->department;
    }

    public function update()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'employment_status' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:5120',
            'department' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->position = $this->position;
        $user->employment_status = $this->employment_status;
        $user->department = $this->department;

        if ($this->photo) {
            $user->profile_photo_path = $this->photo->store('profile-photos', 'public');
        }

        $user->save();

        $this->editingProfile = false;

        // Success notification
        session()->flash('success', 'Profile updated successfully!');
        $this->emit('profileUpdated'); // optional, in case you want to trigger JS or events
    }

    public function render()
    {
        return view('livewire.employee.update-profile');
    }
}
