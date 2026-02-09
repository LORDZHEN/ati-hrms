<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Calibri, Arial, sans-serif;
            font-size: 13px;
            line-height: 1.5;
            margin: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .logo-section {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 10px 0;
        }

        .logo-section .logo img {
            height: 70px;
            object-fit: contain;
        }

        .title-section {
            text-align: center;
            margin: 0 15px;
        }

        .title-section .main-title {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 2px;
        }

        .title-section .sub-title {
            font-weight: bold;
            font-size: 15px;
        }

        .org-info {
            text-align: center;
            font-size: 11px;
            line-height: 1.3;
            margin-top: 5px;
        }

        h2 {
            text-align: center;
            margin-top: 25px;
            margin-bottom: 15px;
            text-decoration: underline;
            font-size: 17px;
        }

        .report-summary p {
            font-size: 13px;
            margin-bottom: 12px;
            text-align: justify;
        }

        .report-summary strong {
            color: #000;
        }

        .employee-list {
            margin-top: 15px;
        }

        .employee-card {
            border: 1px solid #aaa;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 8px;
            background-color: #f9f9f9;
        }

        .employee-card strong {
            display: block;
            font-size: 13px;
            color: #222;
            margin-bottom: 2px;
        }

        .employee-card span {
            font-size: 12px;
        }

        table.signature {
            width: 100%;
            border: none;
            margin-top: 30px;
        }

        table.signature td {
            width: 50%;
            text-align: center;
            border: none;
        }

        @media print {
            .status-filter {
                display: none;
            }
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <div>Republic of the Philippines</div>
    <div>Department of Agriculture</div>

    <div class="logo-section">
        <div class="logo">
            <img src="{{ asset('images/ati_logo.png') }}" alt="ATI Logo">
        </div>

        <div class="title-section">
            <div class="main-title">AGRICULTURAL TRAINING INSTITUTE</div>
            <div class="sub-title">REGIONAL TRAINING CENTER XI</div>
        </div>

        <div class="logo">
            <img src="{{ asset('images/bagong-pilipinas-logo.png') }}" alt="Bagong Pilipinas Logo">
        </div>
    </div>

    <div class="org-info">
        Brgy. Data Abdul Datla, Panabo City, Davao del Norte 8105<br>
        ☎ (084) 217-3345 📧 ati11.addp4@gmail.com<br>
        🌐 ati.da.gov.ph/region11 Facebook @atiregion11
    </div>
</div>

<h2>Employee Comprehensive Report</h2>

@if($from && $to)
    <p style="text-align:center; font-size:12px;">
        Status: <strong>{{ ucfirst($status) }}</strong> |
        Period: <strong>{{ ucfirst($period) }}</strong> |
        Coverage: {{ \Carbon\Carbon::parse($from)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}
    </p>
@endif

@php
    $count = $employees->count();
@endphp

<div class="report-summary">
    @if($status === 'active')
        <p>As of <strong>{{ \Carbon\Carbon::parse($from)->format('M d, Y') }}</strong>, there are a total of <strong>{{ $count }}</strong> active employee accounts.
        These personnel are currently engaged in various departments and positions across the organization.
        This report provides a detailed overview of the active workforce during the selected period.</p>
    @elseif($status === 'inactive')
        <p>As of <strong>{{ \Carbon\Carbon::parse($from)->format('M d, Y') }}</strong>, there are <strong>{{ $count }}</strong> inactive employee accounts.
        These individuals are currently not active in the system due to resignation, termination, or temporary deactivation.
        This report summarizes the status of these employees within the selected period.</p>
    @else
        <p>Between <strong>{{ \Carbon\Carbon::parse($from)->format('M d, Y') }}</strong> and <strong>{{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</strong>,
        there are <strong>{{ $count }}</strong> employee accounts in the system, including both active and inactive personnel.
        The following sections provide details for each employee during this period.</p>
    @endif
</div>

@if($count > 0)
    <div class="employee-list">
        @foreach($employees as $emp)
            <div class="employee-card">
                <strong>{{ $emp->name }} ({{ $emp->employee_id }})</strong>
                <span>Department: {{ $emp->department ?? '-' }} | Position: {{ $emp->position ?? '-' }}</span><br>
                <span>Role: {{ ucfirst($emp->role) }} | Status: {{ ucfirst($emp->status) }} | Verification: {{ $emp->email_verified_at ? 'Verified' : 'Not Verified' }}</span>
            </div>
        @endforeach
    </div>
@endif

<table class="signature">
<tr>
    <td>
        <strong>Prepared by:</strong><br><br><br>
        <u>{{ auth()->user()->name }}</u><br>
        <span>System Administrator</span>
    </td>
    <td>
        <strong>Accomplished by:</strong><br><br><br>
        <u>{{ auth()->user()->name }}</u><br>
        <span>HR Officer</span>
    </td>
</tr>
</table>

</body>
</html>
