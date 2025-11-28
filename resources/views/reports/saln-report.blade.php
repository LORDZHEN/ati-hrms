<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SALN Comprehensive Report</title>
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

        .section-title {
            margin-top: 15px;
            font-weight: bold;
            text-decoration: underline;
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

    <h2>SALN Comprehensive Report</h2>

    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>As of Date</th>
                <th>Total Assets</th>
                <th>Total Liabilities</th>
                <th>Net Worth</th>
                <th>Admin Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salns as $saln)
            <tr>
                <td>{{ $saln->user->first_name }} {{ $saln->user->last_name }}</td>
                <td>{{ $saln->as_of_date?->format('M d, Y') }}</td>
                <td>₱{{ number_format($saln->total_assets, 2) }}</td>
                <td>₱{{ number_format($saln->total_liabilities, 2) }}</td>
                <td>₱{{ number_format($saln->net_worth, 2) }}</td>
                <td>{{ $saln->remarks ?? '-' }}</td>
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
