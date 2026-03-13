<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Illuminate\Validation\Rule;

class UpdateProfile extends Component
{
    // NOTE: No WithFileUploads trait — we handle the file via base64 instead

    public bool    $editingProfile    = false;
    public ?string $employee_id       = null;
    public ?string $first_name        = null;
    public ?string $middle_name       = null;
    public ?string $last_name         = null;
    public ?string $suffix            = null;
    public ?string $position          = null;
    public ?string $employment_status = null;
    public ?string $email             = null;

    // Base64 data-URI set by JS when user picks a file — e.g. "data:image/jpeg;base64,/9j/..."
    public ?string $photoBase64       = null;

    // MIME type extracted from the data-URI prefix
    public ?string $photoMime         = null;

    protected $listeners = [
        'openProfileModal' => 'openModal',
        'profileUpdated'   => '$refresh',
    ];

    public function mount(): void
    {
        $this->loadUserData();
    }

    protected function loadUserData(): void
    {
        $user = Auth::user()->fresh();
        $this->employee_id       = $user->employee_id;
        $this->first_name        = $user->first_name;
        $this->middle_name       = $user->middle_name;
        $this->last_name         = $user->last_name;
        $this->suffix            = $user->suffix;
        $this->position          = $user->position;
        $this->email             = $user->email;
        $this->employment_status = $this->resolveEmploymentStatus($user->role);
    }

    protected function resolveEmploymentStatus(string $role): string
    {
        return match ($role) {
            'admin'     => 'Admin',
            'regular'   => 'Regular',
            'job_order' => 'Job Order',
            default     => ucfirst($role),
        };
    }

    public function openModal(): void
    {
        $this->loadUserData();
        $this->photoBase64    = null;
        $this->photoMime      = null;
        $this->editingProfile = true;
    }

    public function closeModal(): void
    {
        $this->editingProfile = false;
        $this->photoBase64    = null;
        $this->photoMime      = null;
        $this->resetValidation();
        $this->dispatch('modal-closed');
    }

    protected function rules(): array
    {
        return [
            'employee_id' => [
                'required', 'string', 'max:255',
                Rule::unique('users', 'employee_id')->ignore(Auth::id()),
            ],
            'first_name'  => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name'   => 'required|string|max:255',
            'suffix'      => 'nullable|string|max:10',
            'position'    => 'nullable|string|max:255',
            'email'       => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore(Auth::id()),
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.unique'   => 'This Employee ID is already taken.',
            'first_name.required'  => 'First name is required.',
            'last_name.required'   => 'Last name is required.',
            'email.required'       => 'Email address is required.',
            'email.email'          => 'Please enter a valid email address.',
            'email.unique'         => 'This email is already in use.',
        ];
    }

    public function update(): void
    {
        $this->validate();

        $user = Auth::user()->fresh();
        $newPhotoPath = null;

        if ($this->photoBase64) {
            try {
                // Validate the data-URI structure
                if (!preg_match('/^data:(image\/(?:jpeg|jpg|png|gif|webp));base64,(.+)$/i', $this->photoBase64, $matches)) {
                    $this->addError('photo', 'Invalid image format. Please select a JPG, PNG, GIF, or WEBP file.');
                    return;
                }

                $mime       = strtolower($matches[1]); // e.g. "image/jpeg"
                $base64Data = $matches[2];
                $binary     = base64_decode($base64Data, strict: true);

                if ($binary === false) {
                    $this->addError('photo', 'Image data is corrupted. Please try selecting the file again.');
                    return;
                }

                // Check decoded size — reject if over 5 MB
                if (strlen($binary) > 5 * 1024 * 1024) {
                    $this->addError('photo', 'Image must be 5 MB or smaller.');
                    return;
                }

                $ext = match ($mime) {
                    'image/jpeg', 'image/jpg' => 'jpg',
                    'image/png'               => 'png',
                    'image/gif'               => 'gif',
                    'image/webp'              => 'webp',
                    default                   => 'jpg',
                };

                $filename    = 'profile-photos/' . Str::random(40) . '.' . $ext;
                $destination = storage_path('app/public/' . $filename);

                // Ensure directory exists
                if (!is_dir(dirname($destination))) {
                    mkdir(dirname($destination), 0755, true);
                }

                // Write binary directly — no Livewire temp disk involved at all
                if (file_put_contents($destination, $binary) === false) {
                    $this->addError('photo', 'Could not write photo to storage. Check folder permissions.');
                    return;
                }

                if (!file_exists($destination)) {
                    $this->addError('photo', 'Photo write appeared to succeed but file is missing.');
                    return;
                }

                // Delete old photo only after new one is confirmed
                if (!empty($user->profile_photo_path)) {
                    $oldAbs = storage_path('app/public/' . $user->profile_photo_path);
                    if (file_exists($oldAbs)) {
                        @unlink($oldAbs);
                    }
                }

                $newPhotoPath = $filename;

            } catch (\Throwable $e) {
                $this->addError('photo', 'Photo save failed: ' . $e->getMessage());
                return;
            }
        }

        $payload = [
            'employee_id' => $this->employee_id,
            'first_name'  => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name'   => $this->last_name,
            'suffix'      => $this->suffix,
            'position'    => $this->position,
            'email'       => $this->email,
            'name'        => $this->buildFullName(),
        ];

        if ($newPhotoPath !== null) {
            $payload['profile_photo_path'] = $newPhotoPath;
        }

        $user->update($payload);
        $user->refresh();
        Auth::setUser($user);

        $this->editingProfile = false;
        $this->photoBase64    = null;
        $this->photoMime      = null;
        $this->resetValidation();

        Notification::make()
            ->title('Profile Updated')
            ->body('Your profile information has been successfully updated.')
            ->success()
            ->duration(5000)
            ->send();

        $this->redirect(
            request()->header('Referer') ?? route('filament.hrms.pages.profile'),
            navigate: false
        );
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
        // If a photo has been selected, show the base64 preview directly
        if ($this->photoBase64) {
            return $this->photoBase64;
        }

        $user = Auth::user()->fresh();

        if (!empty($user->profile_photo_path)) {
            $absPath = storage_path('app/public/' . $user->profile_photo_path);
            if (file_exists($absPath)) {
                return asset('storage/' . $user->profile_photo_path) . '?v=' . filemtime($absPath);
            }
            if (Storage::disk('public')->exists($user->profile_photo_path)) {
                return Storage::disk('public')->url($user->profile_photo_path) . '?v=' . time();
            }
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'User')
            . '&size=256&background=10b981&color=fff&bold=true';
    }

    public function getEmploymentStatusColorProperty(): string
    {
        return match (Auth::user()->role) {
            'admin'     => 'amber',
            'regular'   => 'green',
            'job_order' => 'blue',
            default     => 'gray',
        };
    }

    public function render()
    {
        return view('livewire.employee.update-profile');
    }
}
