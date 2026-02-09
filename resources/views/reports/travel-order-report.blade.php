<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Travel Order Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Calibri, Arial, sans-serif;
            font-size: 13px;
            line-height: 1.5;
            margin: 20px;
            color: #333;
        }

        .header { text-align:center; margin-bottom:15px; }

        .logo-section {
            display:flex; align-items:center; justify-content:center; margin:10px 0;
        }

        .logo-section .logo img { height:70px; object-fit:contain; }

        .title-section { text-align:center; margin:0 15px; }
        .title-section .main-title { font-weight:bold; font-size:18px; margin-bottom:2px; }
        .title-section .sub-title { font-weight:bold; font-size:15px; }

        .org-info { text-align:center; font-size:11px; line-height:1.3; margin-top:5px; }

        h2 { text-align:center; margin-top:25px; margin-bottom:15px; text-decoration:underline; font-size:17px; }

        .report-summary p { font-size:13px; margin-bottom:12px; text-align:justify; }
        .report-summary strong { color:#000; }

        table { width:100%; border-collapse:collapse; margin-top:15px; }
        table, th, td { border:1px solid #aaa; }
        th, td { padding:8px; text-align:left; font-size:12px; }
        th { background-color:#f2f2f2; }

        .signature { width:100%; border:none; margin-top:30px; }
        .signature td { width:50%; text-align:center; border:none; }

        @media print { .status-filter { display:none; } }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <div>Republic of the Philippines</div>
    <div>Department of Agriculture</div>

    <div class="logo-section">
        <div class="logo"><img src="{{ asset('images/ati_logo.png') }}" alt="ATI Logo"></div>

        <div class="title-section">
            <div class="main-title">AGRICULTURAL TRAINING INSTITUTE</div>
            <div class="sub-title">REGIONAL TRAINING CENTER XI</div>
        </div>

        <div class="logo"><img src="{{ asset('images/bagong-pilipinas-logo.png') }}" alt="Bagong Pilipinas Logo"></div>
    </div>

    <div class="org-info">
        Brgy. Data Abdul Datla, Panabo City, Davao del Norte 8105<br>
        ☎ (084) 217-3345 📧 ati11.addp4@gmail.com<br>
        🌐 ati.da.gov.ph/region11 Facebook @atiregion11
    </div>
</div>

<h2>Travel Order Report</h2>

@if($from && $to)
<p style="text-align:center; font-size:12px;">
    Period: <strong>{{ ucfirst($period) }}</strong><br>
    Coverage: {{ \Carbon\Carbon::parse($from)->format('M d, Y') }}
    to {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}<br>
    Status: <strong>{{ ucfirst($status) }}</strong>
</p>
@endif

@php
$total = $travelOrders->count();
$approved = $travelOrders->where('status','approved')->count();
$recommended = $travelOrders->where('status','recommended')->count();
$rejected = $travelOrders->where('status','rejected')->count();
@endphp

<div class="report-summary">
    @if($status === 'approved')
        <p>Between <strong>{{ \Carbon\Carbon::parse($from)->format('M d, Y') }}</strong> and
        <strong>{{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</strong>, a total of
        <strong>{{ $approved }}</strong> travel orders were approved. This report provides
        details of all approved travel orders processed during the selected period.</p>
    @elseif($status === 'recommended')
        <p>Between <strong>{{ \Carbon\Carbon::parse($from)->format('M d, Y') }}</strong> and
        <strong>{{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</strong>, a total of
        <strong>{{ $recommended }}</strong> travel orders were recommended. This report provides
        details of all recommended travel orders during the selected period.</p>
    @elseif($status === 'rejected')
        <p>Between <strong>{{ \Carbon\Carbon::parse($from)->format('M d, Y') }}</strong> and
        <strong>{{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</strong>, a total of
        <strong>{{ $rejected }}</strong> travel orders were rejected. This report provides
        details of all rejected travel orders during the selected period.</p>
    @else
        <p>Between <strong>{{ \Carbon\Carbon::parse($from)->format('M d, Y') }}</strong> and
        <strong>{{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</strong>, a total of
        <strong>{{ $total }}</strong> travel orders were submitted. Of these,
        <strong>{{ $recommended }}</strong> were recommended,
        <strong>{{ $approved }}</strong> were approved, and
        <strong>{{ $rejected }}</strong> were rejected. This report provides a detailed
        overview of all travel orders processed during the selected period.</p>
    @endif
</div>

@if($total > 0)
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
                    {{ count($users) <= 3 ? implode(', ', $users) : implode(', ', array_slice($users,0,3)) . ' +' . (count($users)-3) . ' more' }}
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
window.onload = function() { window.print(); }
</script>

</body>
</html>
