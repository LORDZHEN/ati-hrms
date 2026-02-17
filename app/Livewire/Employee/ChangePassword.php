<?php

namespace App\Livewire\Employee;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Filament\Notifications\Notification;

class ChangePassword extends Component
{
    public bool $changingPassword = false;
    public ?string $current_password = null;
    public ?string $password = null;
    public ?string $password_confirmation = null;

    // Password strength indicators
    public string $passwordStrength = 'weak';
    public bool $hasUppercase = false;
    public bool $hasLowercase = false;
    public bool $hasNumber = false;
    public bool $hasSpecial = false;
    public bool $hasMinLength = false;

    // Password match state
    public ?bool $passwordsMatch = null;

    protected $listeners = ['openPasswordModal' => 'openModal'];

    public function mount(): void
    {
        // Auto-open modal if user must change password
        if (Auth::user()->must_change_password ?? false) {
            $this->changingPassword = true;
        }
    }

    /**
     * Open the password change modal
     */
    public function openModal(): void
    {
        $this->changingPassword = true;
    }

    /**
     * Close modal and reset all fields
     */
    public function closeModal(): void
    {
        $this->reset([
            'changingPassword',
            'current_password',
            'password',
            'password_confirmation',
            'passwordStrength',
            'hasUppercase',
            'hasLowercase',
            'hasNumber',
            'hasSpecial',
            'hasMinLength',
            'passwordsMatch',
        ]);
        $this->resetValidation();
    }

    /**
     * Monitor new password for strength calculation
     */
    public function updatedPassword(?string $value): void
    {
        if (!$value) {
            $this->resetPasswordStrength();
            return;
        }

        $this->calculatePasswordStrength($value);
        $this->checkPasswordMatch();
    }

    /**
     * Monitor password confirmation for match checking
     */
    public function updatedPasswordConfirmation(): void
    {
        $this->checkPasswordMatch();
    }

    /**
     * Calculate password strength based on criteria
     */
    protected function calculatePasswordStrength(string $password): void
    {
        $this->hasUppercase = preg_match('/[A-Z]/', $password) === 1;
        $this->hasLowercase = preg_match('/[a-z]/', $password) === 1;
        $this->hasNumber = preg_match('/[0-9]/', $password) === 1;
        $this->hasSpecial = preg_match('/[\W_]/', $password) === 1;
        $this->hasMinLength = strlen($password) >= 8;

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
            default => 'strong',
        };
    }

    /**
     * Reset password strength indicators
     */
    protected function resetPasswordStrength(): void
    {
        $this->passwordStrength = 'weak';
        $this->hasUppercase = false;
        $this->hasLowercase = false;
        $this->hasNumber = false;
        $this->hasSpecial = false;
        $this->hasMinLength = false;
    }

    /**
     * Check if password and confirmation match
     */
    protected function checkPasswordMatch(): void
    {
        if (!$this->password || !$this->password_confirmation) {
            $this->passwordsMatch = null;
            return;
        }

        $this->passwordsMatch = $this->password === $this->password_confirmation;
    }

    /**
     * Validation rules
     */
    protected function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ];
    }

    /**
     * Custom validation messages
     */
    protected function messages(): array
    {
        return [
            'current_password.required' => 'Please enter your current password.',
            'password.required' => 'Please enter a new password.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password_confirmation.required' => 'Please confirm your new password.',
        ];
    }

    /**
     * Update user password
     */
    public function updatePassword(): void
    {
        $this->validate();

        // Additional strength validation
        if ($this->passwordStrength !== 'strong') {
            $this->addError(
                'password',
                'Password must be strong (include uppercase, lowercase, number, and special character).'
            );
            return;
        }

        // Additional match validation
        if ($this->passwordsMatch !== true) {
            $this->addError('password_confirmation', 'Passwords do not match.');
            return;
        }

        $user = Auth::user();

        // Verify current password
        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The current password is incorrect.');
            return;
        }

        // Prevent reusing the same password
        if (Hash::check($this->password, $user->password)) {
            $this->addError('password', 'New password cannot be the same as your current password.');
            return;
        }

        // Update password
        $user->update([
            'password' => Hash::make($this->password),
            'must_change_password' => false,
        ]);

        $this->closeModal();

        // Success notification
        Notification::make()
            ->title('Password Changed')
            ->body('Your password has been successfully updated.')
            ->success()
            ->duration(5000)
            ->send();

        // Optional: Dispatch event for other components
        $this->dispatch('passwordUpdated');
    }

    /**
     * Get strength indicator color
     */
    public function getStrengthColorProperty(): string
    {
        return match ($this->passwordStrength) {
            'weak' => 'red',
            'medium' => 'amber',
            'strong' => 'emerald',
            default => 'gray',
        };
    }

    /**
     * Get strength indicator width percentage
     */
    public function getStrengthWidthProperty(): string
    {
        return match ($this->passwordStrength) {
            'weak' => '33%',
            'medium' => '66%',
            'strong' => '100%',
            default => '0%',
        };
    }

    public function render()
    {
        return view('livewire.employee.change-password');
    }
}
