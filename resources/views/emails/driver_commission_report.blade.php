<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Driver Commission Report - CrownCarz</title>

    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .page-container {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: white;
            padding: 20px 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }

        .header-dark {
            background-color: #3d3d3d;
            color: white;
            padding: 40px;
            margin: -20px -40px 30px -40px;
            overflow: auto;
        }

        .header-left { float: left; width: 50%; }
        .header-right { float: right; width: 50%; text-align: right; }

        .header-right h1 {
            margin: 0;
            font-size: 30px;
            font-weight: 300;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .meta-details { overflow: auto; margin: 20px 0; }

        .billing-from {
            float: left;
            width: 40%;
            font-size: 13px;
            line-height: 1.6;
        }

        .billing-from-label {
            color: #888;
            text-transform: uppercase;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .report-meta {
            float: right;
            width: 50%;
            text-align: right;
            font-size: 13px;
        }

        .report-meta table {
            border-collapse: collapse;
            margin-left: auto;
        }

        .report-meta td { padding: 2px 0 2px 20px; }

        .meta-label {
            text-transform: uppercase;
            font-size: 11px;
            font-weight: bold;
        }

        .booking-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 13px;
        }

        .booking-table thead th {
            text-align: left;
            padding: 10px;
            border-top: 3px solid #b09300;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
            text-transform: uppercase;
        }

        .booking-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #eee;
        }

        .text-right { text-align: right; }

        .section-title {
            margin-top: 30px;
            margin-bottom: 10px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }

        .summary-box {
            margin-top: 20px;
            font-size: 14px;
        }

        .summary-box p {
            margin: 6px 0;
        }

        .footer-area {
            margin-top: 40px;
            text-align: right;
        }

    </style>
</head>

<body>

<div class="page-container">

    <!-- HEADER -->
    <div class="header-dark">
        <div class="header-left">
            <img src="https://crowncarz.com/admin/public/images/logo.png" width="150">
        </div>
        <div class="header-right">
            <h1>Driver Commission Report</h1>
        </div>
    </div>

    <!-- META -->
    <div class="meta-details">
        <div class="billing-from">
            <div class="billing-from-label">Driver Details</div>
            <strong>{{ $data['driver']['name'] ?? 'N/A' }}</strong><br>
            {{ $data['driver']['email'] ?? 'N/A' }}<br>
            {{ $data['driver']['phone'] ?? 'N/A' }}
        </div>

        <div class="report-meta">
            <table>
                <tr>
                    <td class="meta-label">Report Date</td>
                    <td>{{ \Carbon\Carbon::now()->format('d-M-Y') }}</td>
                </tr>
                <tr>
                    <td class="meta-label">From</td>
                    <td>{{ \Carbon\Carbon::parse($data['from'])->format('d-M-Y') }}</td>
                </tr>
                <tr>
                    <td class="meta-label">To</td>
                    <td>{{ \Carbon\Carbon::parse($data['to'])->format('d-M-Y') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- ACCOUNT BOOKINGS -->
    <div class="section-title">Account Bookings</div>
    <table class="booking-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>ID</th>
                <th>From</th>
                <th>To</th>
                <th>Vehicle</th>
                <th class="text-right">Fare (£)</th>
                <th class="text-right">Parking (£)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['account_bookings'] as $b)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($b['date'])->format('d-M-Y') }}</td>
                    <td>{{ $b['booking_id'] }}</td>
                    <td>{{ $b['from'] }}</td>
                    <td>{{ $b['to'] }}</td>
                    <td>{{ $b['vehicle'] }}</td>
                    <td class="text-right">{{ number_format($b['fare'],2) }}</td>
                    <td class="text-right">{{ number_format($b['parking'],2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" align="center">No Account Bookings</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- CASH BOOKINGS -->
    <div class="section-title">Cash Bookings</div>
    <table class="booking-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>ID</th>
                <th>From</th>
                <th>To</th>
                <th>Vehicle</th>
                <th class="text-right">Fare (£)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['cash_bookings'] as $b)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($b['date'])->format('d-M-Y') }}</td>
                    <td>{{ $b['booking_id'] }}</td>
                    <td>{{ $b['from'] }}</td>
                    <td>{{ $b['to'] }}</td>
                    <td>{{ $b['vehicle'] }}</td>
                    <td class="text-right">{{ number_format($b['fare'],2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" align="center">No Cash Bookings</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- SUMMARY -->
    <div class="section-title">Summary</div>
    <div class="summary-box">
        <p><strong>Account Fare:</strong> £{{ number_format($data['totals']['account_fare'] ?? 0, 2) }}</p>
        <p><strong>Cash Fare:</strong> £{{ number_format($data['totals']['cash_fare'] ?? 0, 2) }}</p>
        <p><strong>Total Fare:</strong> £{{ number_format($data['total_fare'] ?? 0, 2) }}</p>
        <p><strong>Total Jobs:</strong> {{ $data['total_jobs'] ?? 0 }}</p>
        <p><strong>Parking:</strong> £{{ number_format($data['totals']['parking'] ?? 0, 2) }}</p>
        <p><strong>Commission (20%):</strong> £{{ number_format($data['commission'] ?? 0, 2) }}</p>
        <p><strong>Brought Forward:</strong> £{{ number_format($data['brought_forward'] ?? 0, 2) }}</p>

        <hr>

        <p style="font-size:16px;"><strong>Driver Earning:</strong> 
            £{{ number_format($data['driver_earning'] ?? 0, 2) }}
        </p>
    </div>

    <!-- FOOTER -->
    <div class="footer-area">
        <p>Kind regards,<br><br><strong>CrownCarz Team</strong></p>
    </div>

</div>

</body>
</html>