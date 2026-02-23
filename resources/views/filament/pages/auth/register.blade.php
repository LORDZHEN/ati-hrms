<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - HRMS Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url('{{ asset('images/ATI XI.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: 0;
        }

        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background: linear-gradient(
                135deg,
                rgba(22, 163, 74, 0.55) 0%,
                rgba(15, 118, 55, 0.45) 50%,
                rgba(5, 46, 22, 0.60) 100%
            );
            z-index: 1;
        }

        .register-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 560px;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 20px;
            padding: 40px 36px;
            box-shadow:
                0 8px 32px rgba(0, 0, 0, 0.25),
                0 0 0 1px rgba(255, 255, 255, 0.3);
            animation: fadeInUp 0.4s ease;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .register-logo {
            width: 90px;
            height: 90px;
            object-fit: contain;
            display: block;
            margin: 0 auto 16px;
        }

        .register-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #14532d;
            text-align: center;
        }

        .register-subtitle {
            font-size: 0.9rem;
            color: #4b7a5c;
            text-align: center;
            margin-top: 4px;
            margin-bottom: 20px;
        }

        .success-alert {
            background: #f0fdf4;
            border: 1px solid #86efac;
            color: #166534;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 16px;
            text-align: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .error-alert {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #dc2626;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 16px;
            text-align: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .register-button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 16px;
            letter-spacing: 0.4px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .register-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(34, 197, 94, 0.35);
        }

        .register-button:active {
            transform: translateY(0);
        }

        .login-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            font-size: 0.875rem;
            color: #4b7a5c;
        }

        .login-link a {
            color: #16a34a;
            font-weight: 600;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* ── Strip Filament Section card backgrounds ── */
        .fi-section,
        .fi-section-content,
        .fi-section-header,
        [class*="fi-section"],
        .fi-section-transparent,
        .fi-section-transparent > .fi-section-content,
        .fi-section-transparent > .fi-section-header {
            background: transparent !important;
            box-shadow: none !important;
            border: none !important;
            ring: none !important;
        }

        /* ── Address Picker Styles ── */
        .address-section {
            margin-top: 8px;
        }

        .address-section-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 2px solid #e5e7eb;
        }

        .address-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .address-field {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .address-field label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
        }

        .address-field label .required-star {
            color: #ef4444;
            margin-left: 2px;
        }

        .address-select {
            width: 100%;
            padding: 9px 32px 9px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.875rem;
            color: #111827;
            background-color: #fff;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 16px;
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .address-select:focus {
            outline: none;
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15);
        }

        .address-select:disabled {
            background-color: #f3f4f6;
            color: #9ca3af;
            cursor: not-allowed;
        }

        .address-select.loading {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24'%3E%3Ccircle cx='12' cy='12' r='10' stroke='%2316a34a' stroke-width='2' opacity='0.25'/%3E%3Cpath fill='%2316a34a' d='M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z' opacity='0.75'%3E%3CanimateTransform attributeName='transform' type='rotate' from='0 12 12' to='360 12 12' dur='0.8s' repeatCount='indefinite'/%3E%3C/path%3E%3C/svg%3E");
            background-size: 16px;
        }

        .address-field-full {
            grid-column: 1 / -1;
        }

        .address-error {
            font-size: 0.75rem;
            color: #ef4444;
            margin-top: 2px;
            display: none;
        }

        .address-error.visible {
            display: block;
        }

        /* ── Dark mode ── */
        @media (prefers-color-scheme: dark) {
            .register-container {
                background: rgba(17, 24, 39, 0.92);
                box-shadow:
                    0 8px 32px rgba(0, 0, 0, 0.5),
                    0 0 0 1px rgba(255, 255, 255, 0.08);
            }

            .register-title {
                color: #86efac;
            }

            .register-subtitle {
                color: #6ee7b7;
            }

            .success-alert {
                background: #052e16;
                border-color: #166534;
                color: #86efac;
            }

            .error-alert {
                background: #3b0f0f;
                border-color: #b91c1c;
                color: #fca5a5;
            }

            .login-link {
                color: #6ee7b7;
            }

            .login-link a {
                color: #4ade80;
            }

            .address-section-label {
                color: #e5e7eb;
                border-bottom-color: #374151;
            }

            .address-field label {
                color: #e5e7eb;
            }

            .address-select {
                background-color: rgba(255, 255, 255, 0.08);
                border-color: rgba(255, 255, 255, 0.2);
                color: #ffffff;
            }

            .address-select:disabled {
                background-color: rgba(255, 255, 255, 0.04);
                color: rgba(255, 255, 255, 0.3);
            }

            .address-select:focus {
                border-color: #4ade80;
                box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.15);
            }
        }
    </style>
    @livewireStyles
</head>
<body>

    <div class="register-container">

        <img
            src="{{ asset('images/ati_logo.png') }}"
            alt="ATI Logo"
            class="register-logo"
        >
        <h1 class="register-title">Employee Registration</h1>
        <p class="register-subtitle">Create your HRMS account below</p>

        @if ($showSuccessMessage)
            <div class="success-alert">{{ $successMessage }}</div>
        @endif

        @if (session()->has('registration_error'))
            <div class="error-alert">{{ session('registration_error') }}</div>
        @endif

        <form wire:submit.prevent="register">
            {{--
                Filament renders all fields EXCEPT the 4 address dropdowns.
                Those are replaced by Hidden fields in the PHP form schema,
                and the actual dropdowns are handled by Alpine.js below.
            --}}
            {{ $this->form }}

            {{-- ── Address Picker (Alpine.js + PSGC API) ── --}}
            <div
                class="address-section"
                wire:ignore
                x-data="psgcAddressPicker()"
                x-init="init()"
            >
                <p class="address-section-label">Address</p>

                <div class="address-grid">

                    {{-- Region --}}
                    <div class="address-field">
                        <label for="pick_region">Region <span class="required-star">*</span></label>
                        <select
                            id="pick_region"
                            class="address-select"
                            :class="{ loading: loadingRegions }"
                            :disabled="loadingRegions"
                            x-model="selectedRegion"
                            @change="onRegionChange()"
                        >
                            <option value="">-- Select Region --</option>
                            <template x-for="r in regions" :key="r.code">
                                <option :value="r.code" x-text="r.name"></option>
                            </template>
                        </select>
                        <span class="address-error" :class="{ visible: errors.region }" x-text="errors.region"></span>
                    </div>

                    {{-- Province --}}
                    <div class="address-field">
                        <label for="pick_province">Province <span class="required-star">*</span></label>
                        <select
                            id="pick_province"
                            class="address-select"
                            :class="{ loading: loadingProvinces }"
                            :disabled="!selectedRegion || loadingProvinces"
                            x-model="selectedProvince"
                            @change="onProvinceChange()"
                        >
                            <option value="">-- Select Province --</option>
                            <template x-for="p in provinces" :key="p.code">
                                <option :value="p.code" x-text="p.name"></option>
                            </template>
                        </select>
                        <span class="address-error" :class="{ visible: errors.province }" x-text="errors.province"></span>
                    </div>

                    {{-- City / Municipality --}}
                    <div class="address-field">
                        <label for="pick_city">City / Municipality <span class="required-star">*</span></label>
                        <select
                            id="pick_city"
                            class="address-select"
                            :class="{ loading: loadingCities }"
                            :disabled="!selectedProvince || loadingCities"
                            x-model="selectedCity"
                            @change="onCityChange()"
                        >
                            <option value="">-- Select City --</option>
                            <template x-for="c in cities" :key="c.code">
                                <option :value="c.code" x-text="c.name"></option>
                            </template>
                        </select>
                        <span class="address-error" :class="{ visible: errors.city }" x-text="errors.city"></span>
                    </div>

                    {{-- Barangay --}}
                    <div class="address-field">
                        <label for="pick_barangay">Barangay <span class="required-star">*</span></label>
                        <select
                            id="pick_barangay"
                            class="address-select"
                            :class="{ loading: loadingBarangays }"
                            :disabled="!selectedCity || loadingBarangays"
                            x-model="selectedBarangay"
                            @change="onBarangayChange()"
                        >
                            <option value="">-- Select Barangay --</option>
                            <template x-for="b in barangays" :key="b.code">
                                <option :value="b.code" x-text="b.name"></option>
                            </template>
                        </select>
                        <span class="address-error" :class="{ visible: errors.barangay }" x-text="errors.barangay"></span>
                    </div>

                    {{-- Purok / Street (full width) --}}
                    <div class="address-field address-field-full">
                        <label for="pick_purok">Purok / Street</label>
                        <input
                            id="pick_purok"
                            type="text"
                            class="address-select"
                            placeholder="e.g. Purok 3, Rizal St."
                            x-model="purokStreet"
                        >
                    </div>

                </div>
            </div>
            {{-- ── End Address Picker ── --}}

            <button type="submit" class="register-button">
                Register
            </button>
        </form>

        <p class="login-link">
            Already have an account?
            <a href="{{ route('filament.hrms.auth.login') }}">Sign in</a>
        </p>

    </div>

    @livewireScripts

    <script>
    function psgcAddressPicker() {
        const BASE = 'https://psgc.gitlab.io/api';

        return {
            // State
            regions:          [],
            provinces:        [],
            cities:           [],
            barangays:        [],

            selectedRegion:   '',
            selectedProvince: '',
            selectedCity:     '',
            selectedBarangay: '',
            purokStreet:      '',

            // Loading flags
            loadingRegions:   false,
            loadingProvinces: false,
            loadingCities:    false,
            loadingBarangays: false,

            // Validation errors
            errors: {
                region:   '',
                province: '',
                city:     '',
                barangay: '',
            },

            // ── Helpers ──────────────────────────────────────────────
            async fetchJSON(url) {
                const res = await fetch(url);
                if (!res.ok) throw new Error(`PSGC API error: ${res.status}`);
                return res.json();
            },

            sortByName(arr) {
                return arr.slice().sort((a, b) => a.name.localeCompare(b.name));
            },

            // ── Push all address values to Livewire hidden fields in ONE batch ──
            // Called only at form submit — avoids re-render white screen
            pushToLivewire() {
                this.$wire.set('data.region_id',   this.selectedRegion,   false);
                this.$wire.set('data.province_id', this.selectedProvince, false);
                this.$wire.set('data.city_id',     this.selectedCity,     false);
                this.$wire.set('data.barangay_id', this.selectedBarangay, false);
                this.$wire.set('data.purok_street',this.purokStreet,      false);
            },

            // ── Lifecycle ─────────────────────────────────────────────
            async init() {
                this.loadingRegions = true;
                try {
                    const data = await this.fetchJSON(`${BASE}/regions/`);
                    this.regions = this.sortByName(data);
                } catch (e) {
                    console.error('Failed to load regions:', e);
                } finally {
                    this.loadingRegions = false;
                }

                // Intercept the form submit to push values before Livewire fires
                this.$nextTick(() => {
                    const form = this.$el.closest('form');
                    if (form) {
                        form.addEventListener('submit', (e) => {
                            // Validate first
                            if (!this.validate()) {
                                e.preventDefault();
                                e.stopImmediatePropagation();
                                return false;
                            }
                            // Push all values to hidden fields synchronously
                            this.pushToLivewire();
                        }, true); // capture phase so it runs before Livewire
                    }
                });
            },

            // ── Cascade Handlers (pure Alpine — NO $wire.set calls here) ──────
            async onRegionChange() {
                this.selectedProvince = '';
                this.selectedCity     = '';
                this.selectedBarangay = '';
                this.provinces        = [];
                this.cities           = [];
                this.barangays        = [];
                this.errors.region    = '';

                if (!this.selectedRegion) return;

                this.loadingProvinces = true;
                try {
                    const data = await this.fetchJSON(`${BASE}/regions/${this.selectedRegion}/provinces/`);
                    this.provinces = this.sortByName(data);

                    // NCR edge case: no provinces, load cities directly
                    if (this.provinces.length === 0) {
                        this.loadingCities = true;
                        const cities = await this.fetchJSON(`${BASE}/regions/${this.selectedRegion}/cities-municipalities/`);
                        this.cities = this.sortByName(cities);
                        // Set province to region code as fallback so validation passes
                        this.selectedProvince = this.selectedRegion;
                        this.loadingCities = false;
                    }
                } catch (e) {
                    console.error('Failed to load provinces:', e);
                } finally {
                    this.loadingProvinces = false;
                }
            },

            async onProvinceChange() {
                this.selectedCity     = '';
                this.selectedBarangay = '';
                this.cities           = [];
                this.barangays        = [];
                this.errors.province  = '';

                if (!this.selectedProvince) return;

                this.loadingCities = true;
                try {
                    const data = await this.fetchJSON(`${BASE}/provinces/${this.selectedProvince}/cities-municipalities/`);
                    this.cities = this.sortByName(data);
                } catch (e) {
                    console.error('Failed to load cities:', e);
                } finally {
                    this.loadingCities = false;
                }
            },

            async onCityChange() {
                this.selectedBarangay = '';
                this.barangays        = [];
                this.errors.city      = '';

                if (!this.selectedCity) return;

                this.loadingBarangays = true;
                try {
                    const data = await this.fetchJSON(`${BASE}/cities-municipalities/${this.selectedCity}/barangays/`);
                    this.barangays = this.sortByName(data);
                } catch (e) {
                    console.error('Failed to load barangays:', e);
                } finally {
                    this.loadingBarangays = false;
                }
            },

            onBarangayChange() {
                this.errors.barangay = '';
            },

            // ── Validation ────────────────────────────────────────────
            validate() {
                let valid = true;

                if (!this.selectedRegion) {
                    this.errors.region = 'Region is required.';
                    valid = false;
                }
                if (!this.selectedProvince) {
                    this.errors.province = 'Province is required.';
                    valid = false;
                }
                if (!this.selectedCity) {
                    this.errors.city = 'City / Municipality is required.';
                    valid = false;
                }
                if (!this.selectedBarangay) {
                    this.errors.barangay = 'Barangay is required.';
                    valid = false;
                }

                return valid;
            },
        };
    }
    </script>

</body>
</html>
