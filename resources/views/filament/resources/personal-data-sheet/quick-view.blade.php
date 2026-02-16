{{-- Comprehensive Quick View Modal Content for PDS (All Sections C1-C4) - Refactored with Dark Mode --}}
<div class="space-y-6">

    {{-- Header Section with Status --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-750 rounded-xl p-6 border border-blue-100 dark:border-gray-700">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ $record->surname }}, {{ $record->first_name }} {{ $record->middle_name }} {{ $record->name_extension }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-300 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    {{ $record->place_of_birth ?? 'Place of birth not provided' }}
                </p>
            </div>
            <div class="ml-4">
                @php
                    $statusConfig = [
                        'approved' => [
                            'class' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300 ring-2 ring-green-400',
                            'icon' => 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z'
                        ],
                        'submitted' => [
                            'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300 ring-2 ring-yellow-400',
                            'icon' => 'M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z'
                        ],
                        'disapproved' => [
                            'class' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300 ring-2 ring-red-400',
                            'icon' => 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z'
                        ],
                    ];
                    $config = $statusConfig[$record->status] ?? $statusConfig['submitted'];
                @endphp
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold shadow-sm {{ $config['class'] }}">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="{{ $config['icon'] }}" clip-rule="evenodd"></path>
                    </svg>
                    {{ ucfirst($record->status) }}
                </span>
            </div>
        </div>
    </div>

    {{-- C1: PERSONAL INFORMATION --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-center mb-4">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">I. Personal Information</h3>
        </div>
        <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @if($record->date_of_birth)
                <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-3">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date of Birth</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $record->date_of_birth->format('F d, Y') }}
                        <span class="text-xs text-gray-500 dark:text-gray-400">({{ $record->date_of_birth->age }} years old)</span>
                    </dd>
                </div>
            @endif

            @if($record->sex)
                <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-3">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sex</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $record->sex }}</dd>
                </div>
            @endif

            @if($record->civil_status)
                <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-3">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Civil Status</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $record->civil_status }}</dd>
                </div>
            @endif

            @if($record->height)
                <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-3">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Height</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $record->height }} cm</dd>
                </div>
            @endif

            @if($record->weight)
                <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-3">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Weight</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $record->weight }} kg</dd>
                </div>
            @endif

            @if($record->blood_type)
                <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-3">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Blood Type</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $record->blood_type }}</dd>
                </div>
            @endif

            @if($record->gsis_id_no)
                <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-3">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">GSIS ID</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $record->gsis_id_no }}</dd>
                </div>
            @endif

            @if($record->pag_ibig_id_no)
                <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-3">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">PAG-IBIG ID</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $record->pag_ibig_id_no }}</dd>
                </div>
            @endif

            @if($record->philhealth_no)
                <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-3">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">PhilHealth No.</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $record->philhealth_no }}</dd>
                </div>
            @endif

            @if($record->sss_no)
                <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-3">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">SSS No.</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $record->sss_no }}</dd>
                </div>
            @endif

            @if($record->tin_no)
                <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-3">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">TIN No.</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $record->tin_no }}</dd>
                </div>
            @endif

            @if($record->agency_employee_no)
                <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-3">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Agency Employee No.</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $record->agency_employee_no }}</dd>
                </div>
            @endif
        </dl>
    </div>

    {{-- Contact Information --}}
    @if($record->mobile || $record->email || $record->telephone_no)
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center mb-4">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Contact Information</h3>
            </div>
            <div class="space-y-3">
                @if($record->mobile)
                    <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-750 rounded-lg">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Mobile</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $record->mobile }}</p>
                        </div>
                    </div>
                @endif

                @if($record->telephone_no)
                    <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-750 rounded-lg">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Telephone</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $record->telephone_no }}</p>
                        </div>
                    </div>
                @endif

                @if($record->email)
                    <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-750 rounded-lg">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white break-all">{{ $record->email }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Residential Address --}}
    @php
        $hasResidentialAddress = collect([
            $record->res_house_block_lot_no,
            $record->res_street,
            $record->res_subdivision_village,
            $record->res_barangay,
            $record->res_city_municipality,
            $record->res_province,
            $record->res_zip_code
        ])->filter()->isNotEmpty();
    @endphp

    @if($hasResidentialAddress)
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center mb-4">
                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Residential Address</h3>
            </div>
            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed bg-gray-50 dark:bg-gray-750 rounded-lg p-4">
                {{ collect([
                    $record->res_house_block_lot_no,
                    $record->res_street,
                    $record->res_subdivision_village,
                    $record->res_barangay,
                    $record->res_city_municipality,
                    $record->res_province,
                    $record->res_zip_code
                ])->filter()->implode(', ') }}
            </p>
        </div>
    @endif

    {{-- Permanent Address --}}
    @php
        $hasPermanentAddress = collect([
            $record->perm_house_block_lot_no,
            $record->perm_street,
            $record->perm_subdivision_village,
            $record->perm_barangay,
            $record->perm_city_municipality,
            $record->perm_province,
            $record->perm_zip_code
        ])->filter()->isNotEmpty();
    @endphp

    @if($hasPermanentAddress)
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center mb-4">
                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Permanent Address</h3>
            </div>
            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed bg-gray-50 dark:bg-gray-750 rounded-lg p-4">
                {{ collect([
                    $record->perm_house_block_lot_no,
                    $record->perm_street,
                    $record->perm_subdivision_village,
                    $record->perm_barangay,
                    $record->perm_city_municipality,
                    $record->perm_province,
                    $record->perm_zip_code
                ])->filter()->implode(', ') }}
            </p>
        </div>
    @endif

    {{-- C1: FAMILY BACKGROUND --}}
    @php
        $hasSpouseInfo = $record->spouse_surname || $record->spouse_first_name || $record->spouse_occupation;
        $hasFatherInfo = $record->father_surname || $record->father_first_name;
        $hasMotherInfo = $record->mother_surname || $record->mother_first_name;
        $children = is_array($record->children) ? $record->children : (is_string($record->children) ? json_decode($record->children, true) : []);
        $children = $children ?? [];
        $hasFamilyInfo = $hasSpouseInfo || $hasFatherInfo || $hasMotherInfo || count($children) > 0;
    @endphp

    @if($hasFamilyInfo)
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center mb-4">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">II. Family Background</h3>
            </div>

            <div class="space-y-4">
                @if($hasSpouseInfo)
                    <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Spouse Information</h4>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            @if($record->spouse_surname || $record->spouse_first_name)
                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-400">Name</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">
                                        {{ collect([$record->spouse_surname, $record->spouse_first_name, $record->spouse_middle_name, $record->spouse_name_extension])->filter()->implode(' ') }}
                                    </dd>
                                </div>
                            @endif
                            @if($record->spouse_occupation)
                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-400">Occupation</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">{{ $record->spouse_occupation }}</dd>
                                </div>
                            @endif
                            @if($record->spouse_employer_business_name)
                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-400">Employer/Business</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">{{ $record->spouse_employer_business_name }}</dd>
                                </div>
                            @endif
                            @if($record->spouse_business_address)
                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-400">Business Address</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">{{ $record->spouse_business_address }}</dd>
                                </div>
                            @endif
                            @if($record->spouse_telephone_no)
                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-400">Telephone</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">{{ $record->spouse_telephone_no }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                @endif

                @if($hasFatherInfo)
                    <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Father's Information</h4>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ collect([$record->father_surname, $record->father_first_name, $record->father_middle_name, $record->father_name_extension])->filter()->implode(' ') }}
                        </p>
                    </div>
                @endif

                @if($hasMotherInfo)
                    <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Mother's Maiden Name</h4>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ collect([$record->mother_surname, $record->mother_first_name, $record->mother_middle_name])->filter()->implode(' ') }}
                        </p>
                    </div>
                @endif

                @if(count($children) > 0)
                    <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Children ({{ count($children) }})</h4>
                        <div class="space-y-2">
                            @foreach($children as $child)
                                <div class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-lg p-2">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $child['name'] ?? 'N/A' }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ isset($child['birthdate']) ? \Carbon\Carbon::parse($child['birthdate'])->format('M d, Y') : 'N/A' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- C1: EDUCATIONAL BACKGROUND --}}
    @php
        $education = is_array($record->education)
            ? $record->education
            : (is_string($record->education) ? json_decode($record->education, true) : []);
        $education = $education ?? [];
    @endphp

    @if(count($education) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center mb-4">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                    <path fill="currentColor" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">III. Educational Background</h3>
                <span class="ml-auto text-xs font-medium text-gray-500 dark:text-gray-400">{{ count($education) }} record(s)</span>
            </div>
            <div class="space-y-3">
                @foreach($education as $edu)
                    <div class="relative pl-4 pb-3 border-l-2 border-indigo-200 dark:border-indigo-700 last:border-0">
                        <div class="absolute w-3 h-3 bg-indigo-500 dark:bg-indigo-400 rounded-full -left-[7px] top-0"></div>
                        <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-4">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $edu['level'] ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $edu['school_name'] ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $edu['degree'] ?? 'N/A' }}</p>
                            <div class="flex items-center gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                <span>📅 {{ $edu['from_year'] ?? '?' }} - {{ $edu['to_year'] ?? '?' }}</span>
                                @if(!empty($edu['honors']))
                                    <span class="text-yellow-600 dark:text-yellow-400">🏆 {{ $edu['honors'] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- C2: CIVIL SERVICE ELIGIBILITY --}}
    @php
        $civilService = is_array($record->civil_service_eligibility)
            ? $record->civil_service_eligibility
            : (is_string($record->civil_service_eligibility) ? json_decode($record->civil_service_eligibility, true) : []);
        $civilService = $civilService ?? [];
    @endphp

    @if(count($civilService) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center mb-4">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">IV. Civil Service Eligibility</h3>
                <span class="ml-auto text-xs font-medium text-gray-500 dark:text-gray-400">{{ count($civilService) }} record(s)</span>
            </div>
            <div class="space-y-3">
                @foreach($civilService as $cs)
                    <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-4">
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $cs['career_service'] ?? 'N/A' }}</p>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-2 text-sm">
                            @if(isset($cs['rating']))
                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-400">Rating</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">{{ $cs['rating'] }}%</dd>
                                </div>
                            @endif
                            @if(isset($cs['exam_date']))
                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-400">Exam Date</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($cs['exam_date'])->format('M d, Y') }}</dd>
                                </div>
                            @endif
                            @if(!empty($cs['place']))
                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-400">Place</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">{{ $cs['place'] }}</dd>
                                </div>
                            @endif
                            @if(!empty($cs['license_no']))
                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-400">License No.</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">{{ $cs['license_no'] }}</dd>
                                </div>
                            @endif
                            @if(isset($cs['validity']))
                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-400">Validity</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($cs['validity'])->format('M d, Y') }}</dd>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- C2: WORK EXPERIENCE --}}
    @php
        $workExperience = is_array($record->work_experience)
            ? $record->work_experience
            : (is_string($record->work_experience) ? json_decode($record->work_experience, true) : []);
        $workExperience = $workExperience ?? [];
    @endphp

    @if(count($workExperience) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center mb-4">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">V. Work Experience</h3>
                <span class="ml-auto text-xs font-medium text-gray-500 dark:text-gray-400">{{ count($workExperience) }} position(s)</span>
            </div>
            <div class="space-y-3">
                @foreach($workExperience as $work)
                    <div class="relative pl-4 pb-3 border-l-2 border-emerald-200 dark:border-emerald-700 last:border-0">
                        <div class="absolute w-3 h-3 bg-emerald-500 dark:bg-emerald-400 rounded-full -left-[7px] top-0"></div>
                        <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-4">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $work['position'] ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $work['agency'] ?? 'N/A' }}</p>
                            <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                <span>📅 {{ $work['from'] ?? '?' }} - {{ $work['to'] ?? '?' }}</span>
                                @if(!empty($work['salary']))
                                    <span>💰 ₱{{ number_format($work['salary']) }}</span>
                                @endif
                                @if(!empty($work['salary_grade']))
                                    <span>📊 SG-{{ $work['salary_grade'] }}</span>
                                @endif
                                @if(isset($work['is_government']))
                                    <span class="px-2 py-0.5 rounded-full text-xs {{ $work['is_government'] ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                                        {{ $work['is_government'] ? 'Government' : 'Private' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- C3: VOLUNTARY WORK --}}
    @php
        $voluntaryWork = is_array($record->voluntary_work)
            ? $record->voluntary_work
            : (is_string($record->voluntary_work) ? json_decode($record->voluntary_work, true) : []);
        $voluntaryWork = $voluntaryWork ?? [];
    @endphp

    @if(count($voluntaryWork) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center mb-4">
                <svg class="w-5 h-5 text-pink-600 dark:text-pink-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">VI. Voluntary Work</h3>
                <span class="ml-auto text-xs font-medium text-gray-500 dark:text-gray-400">{{ count($voluntaryWork) }} record(s)</span>
            </div>
            <div class="space-y-3">
                @foreach($voluntaryWork as $vw)
                    <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-4">
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $vw['organization_name'] ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $vw['position'] ?? 'N/A' }}</p>
                        <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <span>📅 {{ $vw['from_date'] ?? '?' }} - {{ $vw['to_date'] ?? '?' }}</span>
                            @if(!empty($vw['hours']))
                                <span>⏱️ {{ $vw['hours'] }} hours</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- C3: LEARNING & DEVELOPMENT --}}
    @php
        $learningDev = is_array($record->learning_development)
            ? $record->learning_development
            : (is_string($record->learning_development) ? json_decode($record->learning_development, true) : []);
        $learningDev = $learningDev ?? [];
    @endphp

    @if(count($learningDev) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center mb-4">
                <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">VII. Learning & Development</h3>
                <span class="ml-auto text-xs font-medium text-gray-500 dark:text-gray-400">{{ count($learningDev) }} training(s)</span>
            </div>
            <div class="space-y-3">
                @foreach($learningDev as $ld)
                    <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-4">
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $ld['training_title'] ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $ld['conducted_by'] ?? 'N/A' }}</p>
                        <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <span>📅 {{ $ld['from_date'] ?? '?' }} - {{ $ld['to_date'] ?? '?' }}</span>
                            @if(!empty($ld['hours']))
                                <span>⏱️ {{ $ld['hours'] }} hours</span>
                            @endif
                            @if(!empty($ld['type']))
                                <span class="px-2 py-0.5 rounded-full text-xs bg-cyan-100 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300">
                                    {{ $ld['type'] }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- C3: OTHER INFORMATION --}}
    @php
        $specialSkills = is_array($record->special_skills)
            ? $record->special_skills
            : (is_string($record->special_skills) ? json_decode($record->special_skills, true) : []);
        $specialSkills = $specialSkills ?? [];

        $distinctions = is_array($record->non_academic_distinctions)
            ? $record->non_academic_distinctions
            : (is_string($record->non_academic_distinctions) ? json_decode($record->non_academic_distinctions, true) : []);
        $distinctions = $distinctions ?? [];

        $memberships = is_array($record->membership_association)
            ? $record->membership_association
            : (is_string($record->membership_association) ? json_decode($record->membership_association, true) : []);
        $memberships = $memberships ?? [];

        $hasOtherInfo = count($specialSkills) > 0 || count($distinctions) > 0 || count($memberships) > 0;
    @endphp

    @if($hasOtherInfo)
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center mb-4">
                <svg class="w-5 h-5 text-violet-600 dark:text-violet-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">VIII. Other Information</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @if(count($specialSkills) > 0)
                    <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Special Skills & Hobbies</h4>
                        <ul class="space-y-1">
                            @foreach($specialSkills as $skill)
                                <li class="text-sm text-gray-900 dark:text-white flex items-center">
                                    <span class="mr-2">•</span>
                                    {{ $skill['skill'] ?? 'N/A' }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(count($distinctions) > 0)
                    <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Non-Academic Distinctions</h4>
                        <ul class="space-y-1">
                            @foreach($distinctions as $distinction)
                                <li class="text-sm text-gray-900 dark:text-white flex items-center">
                                    <span class="mr-2">🏆</span>
                                    {{ $distinction['distinction'] ?? 'N/A' }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(count($memberships) > 0)
                    <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Memberships</h4>
                        <ul class="space-y-1">
                            @foreach($memberships as $membership)
                                <li class="text-sm text-gray-900 dark:text-white flex items-center">
                                    <span class="mr-2">👥</span>
                                    {{ $membership['organization'] ?? 'N/A' }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- C4: REFERENCES --}}
    @php
        $references = is_array($record->references)
            ? $record->references
            : (is_string($record->references) ? json_decode($record->references, true) : []);
        $references = $references ?? [];
        $references = array_filter($references, fn($ref) => !empty($ref['name']));
    @endphp

    @if(count($references) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center mb-4">
                <svg class="w-5 h-5 text-teal-600 dark:text-teal-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">References</h3>
                <span class="ml-auto text-xs font-medium text-gray-500 dark:text-gray-400">{{ count($references) }} person(s)</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($references as $ref)
                    <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-4">
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $ref['name'] ?? 'N/A' }}</p>
                        @if(!empty($ref['address']))
                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $ref['address'] }}</p>
                        @endif
                        @if(!empty($ref['tel']))
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">📞 {{ $ref['tel'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- C4: GOVERNMENT ID --}}
    @if($record->gov_id_type || $record->gov_id_no)
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center mb-4">
                <svg class="w-5 h-5 text-slate-600 dark:text-slate-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Government Issued ID</h3>
            </div>
            <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @if($record->gov_id_type)
                    <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-3">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID Type</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $record->gov_id_type }}</dd>
                    </div>
                @endif

                @if($record->gov_id_no)
                    <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-3">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID Number</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $record->gov_id_no }}</dd>
                    </div>
                @endif

                @if($record->gov_id_issued)
                    <div class="bg-gray-50 dark:bg-gray-750 rounded-lg p-3">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date/Place Issued</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $record->gov_id_issued }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    @endif

    {{-- Admin Remarks (if any) --}}
    @if(!blank($record->remarks))
        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 rounded-xl p-6 border-2 border-yellow-300 dark:border-yellow-700 shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-semibold text-yellow-900 dark:text-yellow-200 mb-2">Admin Remarks</h3>
                    <p class="text-sm text-yellow-800 dark:text-yellow-300 leading-relaxed">{{ $record->remarks }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Footer Timestamps --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400 pt-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Submitted: <strong class="text-gray-700 dark:text-gray-300">{{ $record->created_at->format('M d, Y h:i A') }}</strong></span>
        </div>
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Last Updated: <strong class="text-gray-700 dark:text-gray-300">{{ $record->updated_at->diffForHumans() }}</strong></span>
        </div>
    </div>
</div>

