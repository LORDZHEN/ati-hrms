<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave Application Report</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table, th, td {
            border: 1px solid #aaa;
        }

        th, td {
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }

        th {
            background-color: #f2f2f2;
        }

        .signature {
            width: 100%;
            border: none;
            margin-top: 30px;
        }

        .signature td {
            width: 50%;
            text-align: center;
            border: none;
        }

        @media print {
            .status-filter { display: none; }
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

<h2>Leave Application Report</h2>

@if($from && $to)
    <p style="text-align:center; font-size:12px;">
        Period: <strong>{{ ucfirst($period) }}</strong><br>
        Coverage: {{ \Carbon\Carbon::parse($from)->format('M d, Y') }}
        to {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}
    </p>
@endif

@php
    $total = $leaveApplications->count();
    $approved = $leaveApplications->where('status', 'approved')->count();
    $disapproved = $leaveApplications->where('status', 'disapproved')->count();
@endphp

<div class="report-summary">
    @if($status === 'approved')
        <p>Between <strong>{{ \Carbon\Carbon::parse($from)->format('M d, Y') }}</strong> and
        <strong>{{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</strong>, a total of
        <strong>{{ $approved }}</strong> leave applications were approved. This report provides
        details of all approved leave requests processed during the selected period.</p>
    @elseif($status === 'disapproved')
        <p>Between <strong>{{ \Carbon\Carbon::parse($from)->format('M d, Y') }}</strong> and
        <strong>{{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</strong>, a total of
        <strong>{{ $disapproved }}</strong> leave applications were disapproved. This report provides
        details of all disapproved leave requests processed during the selected period.</p>
    @else
        <p>Between <strong>{{ \Carbon\Carbon::parse($from)->format('M d, Y') }}</strong> and
        <strong>{{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</strong>, a total of
        <strong>{{ $total }}</strong> leave applications were submitted. Of these,
        <strong>{{ $approved }}</strong> were approved and
        <strong>{{ $disapproved }}</strong> were disapproved. This report provides a detailed
        overview of all leave requests processed during the selected period.</p>
    @endif
</div>

@if($total > 0)
    <table>
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Type of Leave</th>
                <th>From</th>
                <th>To</th>
                <th>Days</th>
                <th>Status</th>
                <th>Processed By</th>
                <th>Date Processed</th>
                <th>Reason for Disapproval</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leaveApplications as $leave)
            <tr>
                <td>{{ trim($leave->employee?->first_name . ' ' . ($leave->employee?->middle_name ? $leave->employee->middle_name . ' ' : '') . $leave->employee?->last_name) ?? '-' }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $leave->type_of_leave)) }}</td>
                <td>{{ $leave->leave_date_from?->format('M d, Y') }}</td>
                <td>{{ $leave->leave_date_to?->format('M d, Y') }}</td>
                <td>{{ $leave->number_of_working_days }}</td>
                <td>{{ ucfirst($leave->status) }}</td>
                <td>{{ $leave->authorized_officer ?? 'Not yet processed' }}</td>
                <td>{{ $leave->date_approved_disapproved?->format('M d, Y') ?? '-' }}</td>
                <td>{{ $leave->disapproval_reason ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
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

<script>
    window.onload = function() {
        window.print();
    };
</script>

</body>
</html>
