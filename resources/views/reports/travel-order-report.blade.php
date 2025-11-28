<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Travel Order Report</title>
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

    <h2>Travel Order Report</h2>

    <table>
        <thead>
            <tr>
                <th>Travel Order No.</th>
                <th>Traveler(s)</th>
                <th>Travel Type</th>
                <th>Destination</th>
                <th>Departure Date</th>
                <th>Return Date</th>
                <th>Status</th>
                <th>Recommended By</th>
                <th>Approved By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($travelOrders as $order)
            <tr>
                <td>{{ $order->travel_order_no }}</td>
                <td>
                    @if($order->travel_type === 'batch' && $order->employee_ids)
                        @php
                            $users = \App\Models\User::whereIn('id', $order->employee_ids)->pluck('full_name')->toArray();
                        @endphp
                        {{ count($users) <= 3 ? implode(', ', $users) : implode(', ', array_slice($users, 0, 3)) . ' +' . (count($users) - 3) . ' more' }}
                    @else
                        {{ $order->solo_employee ?? 'Not specified' }}
                    @endif
                </td>
                <td>{{ ucfirst($order->travel_type) }}</td>
                <td>{{ $order->destination }}</td>
                <td>{{ $order->departure_date?->format('M d, Y') }}</td>
                <td>{{ $order->return_date?->format('M d, Y') }}</td>
                <td>{{ ucfirst($order->status) }}</td>
                <td>{{ $order->recommended_by?->name ?? '-' }}</td>
                <td>{{ $order->approved_by?->name ?? '-' }}</td>
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
