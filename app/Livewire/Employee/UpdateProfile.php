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
    public ?string $department = null;

    protected $listeners = ['openProfileModal' => 'openModal'];

    public function mount(): void
    {
        $this->loadUserData();
    }

    /**
     * Load current user data into component properties
     */
    protected function loadUserData(): void
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

    /**
     * Open the profile editing modal
     */
    public function openModal(): void
    {
        $this->loadUserData();
        $this->editingProfile = true;
    }

    /**
     * Close the modal and reset photo upload
     */
    public function closeModal(): void
    {
        $this->editingProfile = false;
        $this->photo = null;
        $this->resetValidation();
    }

    /**
     * Validation rules
     */
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
            'employment_status' => ['nullable', 'string', Rule::in([
                'Permanent',
                'Contractual',
                'Probationary',
                'Job Order'
            ])],
            'department' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:5120|mimes:jpeg,jpg,png,gif,webp',
        ];
    }

    /**
     * Custom validation messages
     */
    protected function messages(): array
    {
        return [
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.unique' => 'This Employee ID is already taken.',
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'photo.image' => 'The file must be an image.',
            'photo.max' => 'Image size must not exceed 5MB.',
            'photo.mimes' => 'Only JPEG, JPG, PNG, GIF, and WEBP images are allowed.',
        ];
    }

    /**
     * Update user profile
     */
    public function update(): void
    {
        $this->validate();

        $user = Auth::user();

        // Handle photo upload first
        if ($this->photo) {
            $this->handlePhotoUpload($user);
        }

        // Update user data
        $user->update([
            'employee_id' => $this->employee_id,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'suffix' => $this->suffix,
            'position' => $this->position,
            'employment_status' => $this->employment_status,
            'department' => $this->department,
            'name' => $this->buildFullName(),
        ]);

        $this->closeModal();

        // Success notification
        Notification::make()
            ->title('Profile Updated')
            ->body('Your profile information has been successfully updated.')
            ->success()
            ->duration(5000)
            ->send();

        // Refresh the page component to show updated data
        $this->dispatch('profileUpdated');
    }

    /**
     * Handle profile photo upload and deletion of old photo
     */
    protected function handlePhotoUpload($user): void
    {
        // Delete old photo if exists
        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Store new photo
        $user->profile_photo_path = $this->photo->store('profile-photos', 'public');
        $user->save();
    }

    /**
     * Build full name from name components
     */
    protected function buildFullName(): string
    {
        return implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
            $this->suffix,
        ]));
    }

    /**
     * Get avatar URL for modal preview
     */
    public function getAvatarUrlProperty(): string
    {
        // Show temporary uploaded photo
        if ($this->photo) {
            return $this->photo->temporaryUrl();
        }

        // Show existing profile photo
        $user = Auth::user();
        if ($user->profile_photo_path) {
            return asset('storage/' . $user->profile_photo_path);
        }

        // Fallback to UI Avatars
        return 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'User') . '&size=256&background=10b981&color=fff&bold=true';
    }

    public function render()
    {
        return view('livewire.employee.update-profile');
    }
}
