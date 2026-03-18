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
    public bool    $editingProfile    = false;
    public ?string $employee_id       = null;
    public ?string $first_name        = null;
    public ?string $middle_name       = null;
    public ?string $last_name         = null;
    public ?string $suffix            = null;
    public ?string $position          = null; // ← persisted + validated
    public ?string $employment_status = null;
    public ?string $email             = null;

    /**
     * BUG FIX #1 — photoBase64 is synced via a direct JS → Livewire call
     * ($wire.call) instead of wire:model on a hidden input.
     * Hidden inputs do not reliably fire the events Livewire 3 needs for
     * two-way binding, so the value was silently dropped on every save.
     */
    public ?string $photoBase64 = null;
    public ?string $photoMime   = null;

    protected $listeners = [
        'open-update-profile'  => 'openModal',
        'close-update-profile' => 'closeModal',
        'openProfileModal'     => 'openModal',
        'profileUpdated'       => '$refresh',
        'photo-selected'       => 'receivePhoto',
    ];

    public function mount(): void
    {
        $this->loadUserData();
    }

    // -------------------------------------------------------------------------
    // Data loading
    // -------------------------------------------------------------------------

    protected function loadUserData(): void
    {
        $user = Auth::user()->fresh();

        $this->employee_id       = $user->employee_id;
        $this->first_name        = $user->first_name;
        $this->middle_name       = $user->middle_name;
        $this->last_name         = $user->last_name;
        $this->suffix            = $user->suffix;
        $this->position          = $user->position;     // ← loaded from DB
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

    // -------------------------------------------------------------------------
    // Photo receiver (called from Alpine via $wire.call)
    // -------------------------------------------------------------------------

    /**
     * BUG FIX #1 (cont.) — reliable way to push a large base64 string from
     * JS into a Livewire property without hidden-input binding issues.
     */
    public function receivePhoto(string $dataUri): void
    {
        if (str_starts_with($dataUri, 'data:image')) {
            $this->photoBase64 = $dataUri;
        }
    }

    // -------------------------------------------------------------------------
    // Modal helpers
    // -------------------------------------------------------------------------

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

    // -------------------------------------------------------------------------
    // Validation
    // -------------------------------------------------------------------------

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

            // ─── FIX: position was saved but never validated ───────────────
            'position'    => 'nullable|string|max:255',
            // ──────────────────────────────────────────────────────────────

            'email' => [
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

            // ─── FIX: validation message for position ─────────────────────
            'position.max'         => 'Position may not exceed 255 characters.',
            'position.string'      => 'Position must be a text value.',
            // ──────────────────────────────────────────────────────────────
        ];
    }

    // -------------------------------------------------------------------------
    // Save
    // -------------------------------------------------------------------------

    public function update(): void
    {
        $this->validate();

        $user         = Auth::user()->fresh();
        $newPhotoPath = null;

        // ── Photo processing ─────────────────────────────────────────────────
        if (!empty($this->photoBase64) && str_starts_with($this->photoBase64, 'data:image')) {
            try {
                if (!preg_match('/^data:(image\/(?:jpeg|jpg|png|gif|webp));base64,(.+)$/i', $this->photoBase64, $matches)) {
                    $this->addError('photo', 'Invalid image format. Please select a JPG, PNG, GIF, or WEBP file.');
                    return;
                }

                $mime       = strtolower($matches[1]);
                $base64Data = $matches[2];
                $binary     = base64_decode($base64Data, strict: true);

                if ($binary === false) {
                    $this->addError('photo', 'Image data is corrupted. Please try selecting the file again.');
                    return;
                }

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

                if (!is_dir(dirname($destination))) {
                    mkdir(dirname($destination), 0755, true);
                }

                if (file_put_contents($destination, $binary) === false) {
                    $this->addError('photo', 'Could not write photo to storage. Check folder permissions.');
                    return;
                }

                // Delete the old photo if present
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

        // ── Persist to DB ─────────────────────────────────────────────────────
        $payload = [
            'employee_id' => $this->employee_id,
            'first_name'  => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name'   => $this->last_name,
            'suffix'      => $this->suffix,
            'position'    => $this->position,   // ← always included in payload
            'email'       => $this->email,
            'name'        => $this->buildFullName(),
        ];

        if ($newPhotoPath !== null) {
            $payload['profile_photo_path'] = $newPhotoPath;
        }

        $user->update($payload);
        $user->refresh();

        // Keep the auth singleton up-to-date for the rest of the request
        Auth::setUser($user);

        // ── Reset component state ─────────────────────────────────────────────
        $this->photoBase64    = null;
        $this->photoMime      = null;
        $this->editingProfile = false;
        $this->resetValidation();

        /**
         * BUG FIX #2 & #3 — Do NOT redirect.
         * redirect() tears down the Livewire component tree, causing the
         * modal trigger button to vanish and Alpine state to reset.
         */

        // 1. Close the modal
        $this->dispatch('close-update-profile');

        // 2. Show the Filament toast — works perfectly without a redirect
        Notification::make()
            ->title('Profile Updated')
            ->body('Your profile information has been successfully updated.')
            ->success()
            ->duration(5000)
            ->send();

        /**
         * BUG FIX #4 — Dispatch a browser event carrying the new avatar URL
         * so the parent hero section and the modal preview can both update
         * their <img> src without needing a page reload.
         */
        $newAvatarUrl = $this->buildAvatarUrl($user);
        $this->dispatch('profile-saved', avatarUrl: $newAvatarUrl);

        // Re-load so our own component reflects the saved values
        $this->loadUserData();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    protected function buildFullName(): string
    {
        return implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
            $this->suffix,
        ]));
    }

    protected function buildAvatarUrl($user): string
    {
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

    /**
     * Livewire 3 computed property — Alpine reads this via $wire.get('avatarUrl').
     * Returns the pending preview base64 while a photo is staged, otherwise
     * falls back to the persisted DB path (or the ui-avatars fallback).
     *
     * NOTE: Livewire 3 supports the getXxxProperty() convention for computed
     * properties when accessed through $wire.get() on the JS side.
     */
    public function getAvatarUrlProperty(): string
    {
        if ($this->photoBase64) {
            return $this->photoBase64;
        }

        return $this->buildAvatarUrl(Auth::user()->fresh());
    }

    public function render()
    {
        return view('livewire.employee.update-profile');
    }
}
