<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Order: {{ $record->travel_order_no }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 15px;
            line-height: 1.2;
            margin: 10px;
            color: #333;
        }
        .travel-order-form {
            width: 100%;
            margin-bottom: 20px;
            padding: 10px;
            page-break-inside: avoid;
            height: 45vh;
        }
        .header { text-align: center; margin-bottom: 10px; }
        .logo-section { display: flex; align-items: flex-start; justify-content: center; margin-bottom: 8px; }
        .logos-container { display: flex; gap: 8px; margin-right: 10px; }
        .logo { width: 40px; height: 40px; }
        .logo img { width: 100%; height: 100%; object-fit: contain; }
        .org-info { text-align: center; font-size: 8px; line-height: 1.1; }
        .org-info .main-title { font-weight: bold; font-size: 9px; color: #2563eb; }
        .org-info .sub-title { font-weight: bold; font-size: 8px; color: #059669; }
        .travel-order-info { display: flex; justify-content: space-between; align-items: center; margin-top: 10px; margin-bottom: 10px; }
        .travel-order-info .form-title-section { text-align: left; }
        .travel-order-info .form-title { font-size: 14px; font-weight: bold; margin-bottom: 3px; }
        .travel-order-info .form-number { font-size: 11px; }
        .date-field { text-align: right; }
        .date-field .field-label { font-weight: bold; margin-right: 5px; }
        .form-fields { margin-bottom: 15px; }
        .field-row { display: flex; margin-bottom: 4px; min-height: 14px; }
        .field-label { font-weight: bold; margin-right: 8px; white-space: nowrap; font-size: 8px; }
        .field-value {
            flex: 1;
            border-bottom: 1px solid #333;
            padding: 1px 3px;
            min-height: 12px;
            font-size: 12px;
            word-wrap: break-word;
            white-space: normal;
        }
        .signature-section { margin-top: 10px; display: flex; justify-content: space-between; }
        .signature-box { text-align: center; width: 45%; }
        .signature-line { border-bottom: 1px solid #333; margin-bottom: 3px; height: 15px; }
        .signature-title { font-weight: bold; margin-bottom: 3px; font-size: 11px; }
        .footer-info { text-align: center; font-size: 7px; font-style: italic; margin-top: 8px; }
        @media print {
            body { margin: 5px; font-size: 8px; }
            .travel-order-form { break-inside: avoid; height: 45vh; margin-bottom: 15px; }
        }
    </style>
</head>
<body>
    @php
        /**
         * Map role slug → human-readable employment type label.
         * Used for the POSITION field on batch orders.
         */
        $roleLabel = function (string $role): string {
            return match ($role) {
                'admin'     => 'Administrator',
                'regular'   => 'Regular Employee',
                'job_order' => 'Job Order',
                default     => ucwords(str_replace('_', ' ', $role)),
            };
        };

        $isBatch = $record->travel_type === 'batch'
                   && ! empty($record->employee_details)
                   && is_array($record->employee_details);

        $employees = $isBatch ? collect($record->employee_details) : collect();

        /**
         * POSITION display for batch:
         *   - Use role_label if stored (new records after this fix)
         *   - Fall back to position string for older records
         * For solo: use the record's own position field directly.
         */
        $batchPositions = $employees->map(function ($e) use ($roleLabel) {
            if (! empty($e['role_label'])) {
                return $e['role_label'];
            }
            if (! empty($e['role'])) {
                return $roleLabel($e['role']);
            }
            return $e['position'] ?? '—';
        })->filter()->implode(', ');
    @endphp

    @foreach ([1, 2] as $copy)
        <div class="travel-order-form">

            {{-- HEADER --}}
            <div class="header">
                <div class="logo-section">
                    <div class="logos-container">
                        <div class="logo">
                            <img src="{{ asset('images/bagong-pilipinas-logo.png') }}" alt="Bagong Pilipinas Logo">
                        </div>
                        <div class="logo">
                            <img src="{{ asset('images/Main Logo.png') }}" alt="Department of Agriculture Logo">
                        </div>
                    </div>
                    <div class="org-info">
                        <div>Republic of the Philippines</div>
                        <div>Department of Agriculture</div>
                        <div class="main-title">AGRICULTURAL TRAINING INSTITUTE</div>
                        <div class="sub-title">REGIONAL TRAINING CENTER XI</div>
                        <div style="font-size: 7px; margin-top: 3px;">
                            Brgy. Data Abdul Datla, Panabo City, Davao del Norte 8105<br>
                            ☎ (084) 217-3345 📧 ati11.addp4@gmail.com<br>
                            🌐 ati.da.gov.ph/region11 Facebook @atiregion11
                        </div>
                    </div>
                </div>
                <div class="travel-order-info">
                    <div class="form-title-section">
                        <div class="form-title">TRAVEL ORDER</div>
                        <div class="form-number">No. {{ $record->travel_order_no }}</div>
                    </div>
                    <div class="date-field">
                        <span class="field-label">DATE:</span>
                        <span style="border-bottom: 1px solid #333; padding: 1px 15px;">
                            {{ $record->date ? $record->date->format('M d, Y') : '' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- BODY FIELDS --}}
            <div class="form-fields">

                {{-- NAME: plain comma-separated, no numbering --}}
                <div class="field-row">
                    <span class="field-label">NAME:</span>
                    <div class="field-value">
                        @if ($isBatch)
                            {{ $employees->pluck('name')->filter()->implode(', ') }}
                        @else
                            {{ $record->name ?? 'Not specified' }}
                        @endif
                    </div>
                </div>

                {{-- POSITION: role label for batch (Regular Employee / Job Order), job title for solo --}}
                <div class="field-row">
                    <span class="field-label">POSITION:</span>
                    <div class="field-value">
                        @if ($isBatch)
                            {{ $batchPositions ?: '—' }}
                        @else
                            {{ $record->position ?? '—' }}
                        @endif
                    </div>
                </div>

                {{-- SALARY + STATION --}}
                <div class="field-row">
                    <span class="field-label">SALARY (PER ANNUM):</span>
                    <div class="field-value" style="flex: 0.6;">
                        ₱{{ $record->salary_per_annum ? number_format($record->salary_per_annum, 2) : '—' }}
                    </div>
                    <span class="field-label" style="margin-left: 20px;">STATION:</span>
                    <div class="field-value">{{ $record->station ?? '—' }}</div>
                </div>

                {{-- DEPARTURE + RETURN DATE --}}
                <div class="field-row">
                    <span class="field-label">DEPARTURE DATE:</span>
                    <div class="field-value" style="flex: 0.6;">
                        {{ $record->departure_date ? $record->departure_date->format('M d, Y') : '' }}
                    </div>
                    <span class="field-label" style="margin-left: 20px;">RETURN DATE:</span>
                    <div class="field-value">
                        {{ $record->return_date ? $record->return_date->format('M d, Y') : '' }}
                    </div>
                </div>

                {{-- REPORT TO --}}
                <div class="field-row">
                    <span class="field-label">REPORT TO:</span>
                    <div class="field-value">{{ $record->report_to ?? '—' }}</div>
                </div>

                {{-- DESTINATION --}}
                <div class="field-row">
                    <span class="field-label">DESTINATION/S:</span>
                    <div class="field-value">{{ $record->destination ?? '—' }}</div>
                </div>

                {{-- PURPOSE --}}
                <div class="field-row">
                    <span class="field-label">PURPOSE OF TRIP:</span>
                    <div class="field-value">{{ $record->purpose_of_trip ?? '—' }}</div>
                </div>

                {{-- ASSISTANT / LABORER --}}
                <div class="field-row">
                    <span class="field-label">ASSISTANT AND/OR LABORER ALLOWED:</span>
                    <div class="field-value">{{ $record->assistant_laborer_allowed ?? '—' }}</div>
                </div>

                {{-- PER DIEMS --}}
                <div class="field-row">
                    <span class="field-label">PER DIEMS/EXPENSES ALLOWED:</span>
                    <div class="field-value">{{ $record->per_diems_expenses_allowed ?? '—' }}</div>
                </div>

                {{-- APPROPRIATION / FUNDS --}}
                <div class="field-row">
                    <span class="field-label">APPROPRIATION/FUNDS:</span>
                    <div class="field-value">{{ $record->appropriation_funds ?? '—' }}</div>
                </div>

                {{-- REMARKS --}}
                <div class="field-row">
                    <span class="field-label">REMARKS:</span>
                    <div class="field-value">{{ $record->remarks_special_instructions ?? '—' }}</div>
                </div>

            </div>

            {{-- SIGNATURES — blank lines for pen signing, no printed names --}}
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-title">RECOMMENDED:</div>
                    <div class="signature-line"></div>
                </div>
                <div class="signature-box">
                    <div class="signature-title">APPROVED:</div>
                    <div class="signature-line"></div>
                </div>
            </div>

            <div class="footer-info">
                ATI-QF/HRMO-14 Rev. 03 Effectivity Date: July 25, 2024
            </div>
        </div>
    @endforeach

    <script>
        window.onload = function () { window.print(); };
    </script>
</body>
</html>
