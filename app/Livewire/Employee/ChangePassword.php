<?php

namespace App\Livewire\Employee;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ChangePassword extends Component
{
    public $changingPassword = false;

    public $current_password;
    public $password;
    public $password_confirmation;

    public function mount()
    {
        // Show modal only if user must change password
        $this->changingPassword = Auth::user()->must_change_password;
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The current password is incorrect.');
            return;
        }

        // Update password and mark that the user no longer needs to change it
        $user->update([
            'password' => bcrypt($this->password),
            'must_change_password' => false,
        ]);

        // Reset form fields
        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->changingPassword = false;

        // Success notification
        session()->flash('success', 'Password updated successfully!');
    }

    public function render()
    {
        return view('livewire.employee.change-password');
    }
}
