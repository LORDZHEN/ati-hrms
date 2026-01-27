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

    // Password strength state
    public $passwordStrength = 'weak';
    public $hasUppercase = false;
    public $hasLowercase = false;
    public $hasNumber = false;
    public $hasSpecial = false;
    public $hasMinLength = false;

    // Password match state
    public $passwordsMatch = null; // null | true | false

    public function mount()
    {
        $this->changingPassword = Auth::user()->must_change_password;
    }

    /**
     * Runs whenever NEW PASSWORD changes
     */
    public function updatedPassword($value)
    {
        // Strength detection
        $this->hasUppercase = preg_match('/[A-Z]/', $value);
        $this->hasLowercase = preg_match('/[a-z]/', $value);
        $this->hasNumber    = preg_match('/[0-9]/', $value);
        $this->hasSpecial   = preg_match('/[\W_]/', $value);
        $this->hasMinLength = strlen($value) >= 8;

        $score = collect([
            $this->hasUppercase,
            $this->hasLowercase,
            $this->hasNumber,
            $this->hasSpecial,
            $this->hasMinLength,
        ])->filter()->count();

        $this->passwordStrength = match (true) {
            $score <= 2 => 'weak',
            $score <= 4 => 'medium',
            default     => 'strong',
        };

        $this->checkPasswordMatch();
    }

    /**
     * Runs whenever CONFIRM PASSWORD changes
     */
    public function updatedPasswordConfirmation()
    {
        $this->checkPasswordMatch();
    }

    protected function checkPasswordMatch()
    {
        if (!$this->password || !$this->password_confirmation) {
            $this->passwordsMatch = null;
            return;
        }

        $this->passwordsMatch = $this->password === $this->password_confirmation;
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($this->passwordStrength !== 'strong') {
            $this->addError(
                'password',
                'Password must be STRONG (uppercase, lowercase, number, and special character).'
            );
            return;
        }

        if ($this->passwordsMatch !== true) {
            $this->addError(
                'password_confirmation',
                'Passwords do not match.'
            );
            return;
        }

        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The current password is incorrect.');
            return;
        }

        $user->update([
            'password' => bcrypt($this->password),
            'must_change_password' => false,
        ]);

        $this->reset([
            'current_password',
            'password',
            'password_confirmation',
            'passwordStrength',
            'passwordsMatch',
        ]);

        $this->changingPassword = false;

        $this->dispatch('password-updated', ['message' => 'Your password has been changed successfully.']);


    }

    public function render()
    {
        return view('livewire.employee.change-password');
    }
}
