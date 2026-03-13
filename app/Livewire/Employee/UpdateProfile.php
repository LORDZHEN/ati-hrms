<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UpdateProfile extends Component
{
    use WithFileUploads;

    public bool $editingProfile = false;
    public ?string $employee_id = null;
    public ?string $first_name = null;
    public ?string $middle_name = null;
    public ?string $last_name = null;
    public ?string $suffix = null;
    public $photo = null;
    public ?string $position = null;
    public ?string $employment_status = null;
    public ?string $email = null;

    // Removed: department

    protected $listeners = [
        'openProfileModal' => 'openModal',
        'profileUpdated' => '$refresh',
    ];

    public function mount(): void
    {
        $this->loadUserData();
    }

    protected function loadUserData(): void
    {
        $user = Auth::user();
        $this->employee_id = $user->employee_id;
        $this->first_name = $user->first_name;
        $this->middle_name = $user->middle_name;
        $this->last_name = $user->last_name;
        $this->suffix = $user->suffix;
        $this->position = $user->position;
        $this->email = $user->email;

        // Employment status is derived from the user's role — not editable
        $this->employment_status = $this->resolveEmploymentStatus($user->role);
    }

    /**
     * Map role → employment status label shown in the UI.
     */
    protected function resolveEmploymentStatus(string $role): string
    {
        return match ($role) {
            'admin' => 'Admin',
            'regular' => 'Regular',
            'job_order' => 'Job Order',
            default => ucfirst($role),
        };
    }

    public function openModal(): void
    {
        $this->loadUserData();
        $this->editingProfile = true;
    }

    public function closeModal(): void
    {
        $this->editingProfile = false;
        $this->photo = null;
        $this->resetValidation();
    }

    protected function rules(): array
    {
        return [
            'employee_id' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'employee_id')->ignore(Auth::id()),
            ],
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'position' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore(Auth::id()),
            ],
            'photo' => 'nullable|image|max:5120|mimes:jpeg,jpg,png,gif,webp',
        ];
    }

    protected function messages(): array
    {
        return [
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.unique' => 'This Employee ID is already taken.',
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already in use.',
            'photo.image' => 'The file must be an image.',
            'photo.max' => 'Image size must not exceed 5MB.',
            'photo.mimes' => 'Only JPEG, JPG, PNG, GIF, and WEBP images are allowed.',
        ];
    }

    public function update(): void
    {
        $this->validate();

        $user = Auth::user();

        if ($this->photo) {
            $this->handlePhotoUpload($user);
        }

        $user->update([
            'employee_id' => $this->employee_id,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'suffix' => $this->suffix,
            'position' => $this->position,
            'email' => $this->email,
            'name' => $this->buildFullName(),
            // employment_status and department are NOT updated here — role is the source of truth
        ]);

        $this->closeModal();

        Notification::make()
            ->title('Profile Updated')
            ->body('Your profile information has been successfully updated.')
            ->success()
            ->duration(5000)
            ->send();

        $this->dispatch('profileUpdated');
        $this->dispatch('profile-updated');
    }

    protected function handlePhotoUpload($user): void
    {
        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->profile_photo_path = $this->photo->store('profile-photos', 'public');
        $user->save();
    }

    protected function buildFullName(): string
    {
        return implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
            $this->suffix,
        ]));
    }

    public function getAvatarUrlProperty(): string
    {
        if ($this->photo) {
            return $this->photo->temporaryUrl();
        }

        $user = Auth::user();
        if ($user->profile_photo_path) {
            return asset('storage/' . $user->profile_photo_path);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'User')
            . '&size=256&background=10b981&color=fff&bold=true';
    }

    /**
     * Expose a formatted badge color for the employment status pill in the blade.
     */
    public function getEmploymentStatusColorProperty(): string
    {
        return match (Auth::user()->role) {
            'admin' => 'amber',
            'regular' => 'green',
            'job_order' => 'blue',
            default => 'gray',
        };
    }

    public function render()
    {
        return view('livewire.employee.update-profile');
    }
}
