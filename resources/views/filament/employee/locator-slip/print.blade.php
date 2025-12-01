<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Locator Slip - Print</title>

    <style>
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
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
        }

        .header img {
            width: 60px;
            height: 60px;
        }

        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 16px;
        }

        .section {
            margin-bottom: 10px;
        }

        .label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }

        .signature-block {
            margin-top: 30px;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            width: 200px;
            margin: 0 auto 4px auto;
        }

        .signature-name {
            font-weight: bold;
            text-align: center;
            margin-bottom: 3px;
        }

        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            text-align: center;
        }

        .checkbox.checked::before {
            content: '✓';
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="container">

        @foreach (['Copy 1', 'Copy 2'] as $copy)
        <div class="copy">

            <div class="header">
                <img src="{{ asset('images/ati_logo.png') }}" alt="ATI Logo">
                <div><strong>AGRICULTURAL TRAINING INSTITUTE</strong><br>REGIONAL TRAINING CENTER</div>
                <div>Brgy. Datu Abdul Dadia, Panabo City, Davao del Norte, 8105</div>
            </div>

            <div class="title">LOCATOR SLIP</div>

            {{-- Transaction Type --}}
            <div class="section">
                <span class="checkbox {{ $record->transaction_type === 'personal' ? 'checked' : '' }}"></span> Personal Transaction
                <span class="checkbox {{ $record->transaction_type === 'official' ? 'checked' : '' }}" style="margin-left: 20px;"></span> Official Business
            </div>

            <div class="section"><span class="label">Name:</span> {{ $record->employee_name }}</div>
            <div class="section"><span class="label">Position:</span> {{ $record->position }}</div>

            {{-- FIXED: office_department --}}
            <div class="section"><span class="label">Department:</span> {{ $record->office_department }}</div>

            <div class="section"><span class="label">Destination:</span> {{ $record->destination }}</div>
            <div class="section"><span class="label">Purpose:</span> {{ $record->purpose }}</div>

            <div class="section">
                <span class="label">Inclusive Date:</span>
                {{ \Carbon\Carbon::parse($record->inclusive_date)->format('F d, Y') }}
            </div>

            <div class="section">
                <span class="label">Out:</span>
                {{ \Carbon\Carbon::parse($record->out_time)->format('h:i A') }}

                <span class="label" style="margin-left: 40px;">In:</span>
                {{ $record->in_time ? \Carbon\Carbon::parse($record->in_time)->format('h:i A') : 'N/A' }}
            </div>

            {{-- Status --}}
            <div class="section">
                <span class="label">Status:</span>

                <span class="checkbox {{ $record->status === 'pending' ? 'checked' : '' }}"></span> Pending
                <span class="checkbox {{ $record->status === 'approved' ? 'checked' : '' }}" style="margin-left: 15px;"></span> Approved
                <span class="checkbox {{ $record->status === 'rejected' ? 'checked' : '' }}" style="margin-left: 15px;"></span> Rejected
            </div>

            @if($record->status === 'rejected')
            <div class="section"><span class="label">Rejection Reason:</span> {{ $record->rejection_reason }}</div>
            @endif

            {{-- REQUESTED BY --}}
            <div class="signature-block">
                <strong>Requested By:</strong>
                <div class="signature-name">{{ $record->requested_by }}</div>
                <div class="signature-line"></div>
            </div>

            {{-- APPROVED BY --}}
            <div class="signature-block">
                <strong>Approved By:</strong>
                <div class="signature-name">{{ $record->approved_by ?? 'N/A' }}</div>
                <div class="signature-line"></div>

                @if($record->approved_at)
                <div class="section">
                    <span class="label">Date Approved:</span>
                    {{ \Carbon\Carbon::parse($record->approved_at)->format('F d, Y') }}
                </div>
                @endif
            </div>

        </div>
        @endforeach
    </div>

</body>
</html>
