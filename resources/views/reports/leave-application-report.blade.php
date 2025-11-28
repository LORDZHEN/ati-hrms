<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave Application Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo-section {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 5px 0;
        }

        .logo-section .logo img {
            height: 60px;
            object-fit: contain;
        }

        .title-section {
            text-align: center;
            margin: 0 15px;
        }

        .title-section .main-title {
            font-weight: bold;
            font-size: 16px;
        }

        .title-section .sub-title {
            font-weight: bold;
            font-size: 14px;
        }

        .org-info {
            text-align: center;
            font-size: 10px;
            line-height: 1.2;
            margin-top: 3px;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 15px;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #000;
        }

        th, td {
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
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
                <td>
    {{ trim(
        $leave->employee?->first_name . ' ' .
        ($leave->employee?->middle_name ? $leave->employee->middle_name . ' ' : '') .
        $leave->employee?->last_name
    ) ?? '-' }}
</td>

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

    <script>
        // Auto-print
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
