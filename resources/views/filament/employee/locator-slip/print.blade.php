<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locator Slip - Print</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }

        .container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .copy {
            width: 48%;
            border: 1px solid #000;
            padding: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
        }

        .header img {
            width: 60px;
            height: 60px;
            margin-bottom: 5px;
        }

        .header .org-name {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 2px;
        }

        .header .dept-name {
            font-size: 10px;
            margin-bottom: 2px;
        }

        .header .address {
            font-size: 10px;
        }

        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 16px;
            font-size: 13px;
        }

        .section {
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }

        /* Manual fill line for handwritten entries */
        .fill-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 250px;
            padding: 0 5px;
        }

        .time-fill-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 100px;
            padding: 0 5px;
        }

        .transaction-type {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            margin-bottom: 16px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: bold;
        }

        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            text-align: center;
            line-height: 12px;
            font-weight: bold;
            font-size: 10px;
            flex-shrink: 0;
        }

        .checkbox.checked {
            background-color: #28a745;
            border-color: #28a745;
        }

        .checkbox.checked::before {
            content: '✓';
            color: white;
            display: block;
            text-align: center;
            line-height: 12px;
        }

        .signature-block {
            margin-top: 20px;
        }

        .signature-block strong {
            display: block;
            margin-bottom: 5px;
        }

        .signature-name {
            font-weight: bold;
            text-align: center;
            margin-bottom: 3px;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            width: 200px;
            margin: 0 auto 8px auto;
        }

        .time-section {
            display: flex;
            align-items: center;
            gap: 40px;
        }

        @media print {
            @page {
                size: landscape;
                margin: 12mm;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="container">
        @foreach (['Copy 1', 'Copy 2'] as $copyLabel)
        <div class="copy">

            {{-- Header Section --}}
            <div class="header">
                <img src="{{ asset('images/ati_logo.png') }}" alt="ATI Logo">
                <div class="org-name">AGRICULTURAL TRAINING INSTITUTE</div>
                <div class="dept-name">REGIONAL TRAINING CENTER</div>
                <div class="address">Brgy. Datu Abdul Dadia, Panabo City, Davao del Norte, 8105</div>
            </div>

            {{-- Title --}}
            <div class="title">LOCATOR SLIP</div>

            {{-- Transaction Type --}}
            <div class="section transaction-type">
                <label class="checkbox-label">
                    <span class="checkbox {{ $record->transaction_type === 'personal' ? 'checked' : '' }}"></span>
                    <span>Personal Transaction</span>
                </label>
                <label class="checkbox-label">
                    <span class="checkbox {{ $record->transaction_type === 'official' ? 'checked' : '' }}"></span>
                    <span>Official Business</span>
                </label>
            </div>

            {{-- Employee Information - Manual Fill Lines --}}
            <div class="section">
                <span class="label">Name:</span>
                <span class="fill-line">{{ $record->employee_name }}</span>
            </div>

            <div class="section">
                <span class="label">Position:</span>
                <span class="fill-line">{{ $record->position }}</span>
            </div>

            <div class="section">
                <span class="label">Department:</span>
                <span class="fill-line">{{ $record->office_department }}</span>
            </div>

            {{-- Trip Details --}}
            <div class="section">
                <span class="label">Destination:</span>
                {{ $record->destination }}
            </div>

            @if($record->purpose)
            <div class="section">
                <span class="label">Purpose:</span>
                {{ $record->purpose }}
            </div>
            @endif

            <div class="section">
                <span class="label">Inclusive Date:</span>
                {{ \Carbon\Carbon::parse($record->inclusive_date)->format('F d, Y') }}
            </div>

            {{-- Time Section - Manual Fill Lines --}}
            <div class="section time-section">
                <div>
                    <span class="label">Out:</span>
                    <span class="time-fill-line"></span>
                </div>
                <div>
                    <span class="label">In:</span>
                    <span class="time-fill-line"></span>
                </div>
            </div>

            {{-- Remarks for Disapproved --}}
            @if($record->status === 'disapproved' && $record->admin_remarks)
            <div class="section" style="margin-top: 15px; padding: 8px; border: 1px solid #dc3545; background-color: #f8d7da;">
                <span class="label" style="color: #721c24;">Remarks:</span>
                <span style="color: #721c24;">{{ $record->admin_remarks }}</span>
            </div>
            @endif

            {{-- Requested By Signature --}}
            <div class="signature-block">
                <strong>Requested By:</strong>
                <div class="signature-name">{{ $record->requested_by }}</div>
                <div class="signature-line"></div>
            </div>

            {{-- Approved By Signature --}}
            <div class="signature-block">
                <strong>Approved By:</strong>
                <div class="signature-name">{{ $record->approved_by ?? 'Pending' }}</div>
                <div class="signature-line"></div>

                @if($record->approved_at)
                <div style="text-align: center; font-size: 10px; margin-top: 5px;">
                    Date: {{ \Carbon\Carbon::parse($record->approved_at)->format('F d, Y') }}
                </div>
                @endif
            </div>

        </div>
        @endforeach
    </div>

</body>
</html>
