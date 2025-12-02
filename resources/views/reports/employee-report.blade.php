<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .status-filter {
            margin-bottom: 15px;
            text-align: center;
        }

        .status-filter label {
            font-weight: bold;
            margin-right: 5px;
        }

        @media print {
            .status-filter {
                display: none;
            }
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

    <!-- Status Filter -->
    <div class="status-filter">
        <form method="GET" action="{{ url()->current() }}">
            <label for="status">Filter by Status:</label>
            <select name="status" id="status" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </form>
    </div>

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

    <table>
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Birthday</th>
                <th>Role</th>
                <th>Status</th>
                <th>Verification Status</th>
                <th>Department</th>
                <th>Position</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $emp)
            <tr>
                <td>{{ $emp->employee_id_number }}</td>
                <td>{{ $emp->name }}</td>
                <td>{{ $emp->email }}</td>
                <td>{{ $emp->birthday?->format('M d, Y') }}</td>
                <td>{{ ucfirst($emp->role) }}</td>
                <td>{{ ucfirst($emp->status) }}</td>
                <td>{{ $emp->email_verified_at ? 'Verified' : 'Not Verified' }}</td>
                <td>{{ $emp->department ?? '-' }}</td>
                <td>{{ $emp->position ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
