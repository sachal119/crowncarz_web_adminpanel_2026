<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Driver Commission Statement</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            color: #222;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px solid #E6B04A;
            margin-bottom: 12px;
            padding-bottom: 8px;
        }
        .logo {
            height: 50px;
        }
        .company-details {
            text-align: right;
            font-size: 8pt;
            line-height: 1.35;
        }
        .company-details h1 {
            color: #1e293b;
            font-size: 16pt;
            margin: 0 0 3px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .meta-box {
            width: 25%;
            border: 1px solid #dee2e6;
            background: #fafafa;
            padding: 6px 8px;
        }
        .meta-label {
            color: #64748b;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .meta-value {
            color: #0f172a;
            font-size: 9pt;
            font-weight: bold;
            margin-top: 2px;
        }
        .section-title {
            color: #1e293b;
            font-size: 10pt;
            font-weight: bold;
            margin: 10px 0 5px;
            padding-bottom: 3px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.8pt;
            margin-bottom: 10px;
        }
        .data-table th, .data-table td {
            border: 1px solid #dee2e6;
            padding: 4px 6px;
            text-align: left;
        }
        .data-table th {
            background-color: #E6B04A;
            color: #000000;
            font-weight: bold;
        }
        .data-table tfoot td {
            background-color: #f8fafc;
            font-weight: bold;
        }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .text-success { color: #16a34a; }
        .text-danger { color: #dc2626; }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .summary-box {
            width: 48%;
            vertical-align: top;
            border: 1px solid #dee2e6;
            padding: 8px 12px;
            background: #ffffff;
        }
        .summary-row {
            display: table;
            width: 100%;
            padding: 2px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .earning-box {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            padding: 6px 10px;
            margin-top: 6px;
            font-weight: bold;
        }
        .footer {
            border-top: 1px solid #dee2e6;
            color: #64748b;
            font-size: 7.5pt;
            margin-top: 14px;
            padding-top: 6px;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Header Table -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle;">
                <img src="https://crowncarz.com/admin/public/images/logo.png" alt="Crown Carz Logo" class="logo">
            </td>
            <td class="company-details" style="vertical-align: middle;">
                <h1>Crown Carz</h1>
                <div>52 Elvaston Way, Reading, RG30 4LU</div>
                <div>www.crowncarz.com | info@crowncarz.com</div>
            </td>
        </tr>
    </table>

    <!-- Meta Boxes -->
    <table class="meta-table">
        <tr>
            <td class="meta-box">
                <div class="meta-label">Statement</div>
                <div class="meta-value">Driver Commission</div>
            </td>
            <td class="meta-box">
                <div class="meta-label">Driver</div>
                <div class="meta-value">{{ $driver['name'] ?? 'Unknown Driver' }} ({{ $driverId ?? ($driver['id'] ?? '-') }})</div>
            </td>
            <td class="meta-box">
                <div class="meta-label">From</div>
                <div class="meta-value">{{ \Carbon\Carbon::parse($from)->format('d-M-Y') }}</div>
            </td>
            <td class="meta-box">
                <div class="meta-label">To</div>
                <div class="meta-value">{{ \Carbon\Carbon::parse($to)->format('d-M-Y') }}</div>
            </td>
        </tr>
    </table>

    <!-- Account Bookings -->
    <div class="section-title">Account Bookings ({{ count($account_bookings) }})</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>BookingId</th>
                <th>From</th>
                <th>Via</th>
                <th>To</th>
                <th>WT/C</th>
                <th>Income (£)</th>
                <th>Extra</th>
                <th>Park</th>
                <th>Veh</th>
            </tr>
        </thead>
        <tbody>
            @forelse($account_bookings as $b)
            <tr>
                <td>{{ \Carbon\Carbon::parse($b['date'])->format('d-M-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($b['time'])->format('H:i') }}</td>
                <td>{{ $b['booking_id'] ?? '-' }}</td>
                <td>{{ $b['from'] ?? '-' }}</td>
                <td>{{ $b['via'] ?? '-' }}</td>
                <td>{{ $b['to'] ?? '-' }}</td>
                <td>{{ $b['waiting_fee'] ?? '0' }}</td>
                <td class="text-success">£{{ number_format($b['fare'] ?? ($b['income'] ?? 0), 2) }}</td>
                <td>{{ $b['extra'] ?? 0 }}</td>
                <td>{{ number_format($b['parking'] ?? 0, 2) }}</td>
                <td>{{ $b['vehicle'] ?? 'Saloon' }}</td>
            </tr>
            @empty
            <tr><td colspan="11" class="text-center">No Account Bookings</td></tr>
            @endforelse
        </tbody>
        @if(count($account_bookings))
        <tfoot>
            <tr>
                <td colspan="7" class="text-end">Account Total</td>
                <td class="text-success">£{{ number_format($totals['account_fare'] ?? ($totals['account_total'] ?? 0), 2) }}</td>
                <td>{{ $totals['extra'] ?? 0 }}</td>
                <td>{{ number_format($totals['parking'] ?? 0, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- Cash Bookings -->
    <div class="section-title">Cash Bookings ({{ count($cash_bookings) }})</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>BookingId</th>
                <th>From</th>
                <th>Via</th>
                <th>To</th>
                <th>WT/C</th>
                <th>Income (£)</th>
                <th>Extra</th>
                <th>Veh</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cash_bookings as $b)
            <tr>
                <td>{{ \Carbon\Carbon::parse($b['date'])->format('d-M-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($b['time'])->format('H:i') }}</td>
                <td>{{ $b['booking_id'] ?? '-' }}</td>
                <td>{{ $b['from'] ?? '-' }}</td>
                <td>{{ $b['via'] ?? '-' }}</td>
                <td>{{ $b['to'] ?? '-' }}</td>
                <td>{{ $b['waiting_fee'] ?? '0' }}</td>
                <td class="text-success">£{{ number_format($b['fare'] ?? ($b['income'] ?? 0), 2) }}</td>
                <td>{{ $b['extra'] ?? 0 }}</td>
                <td>{{ $b['vehicle'] ?? 'Estate' }}</td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center">No Cash Bookings</td></tr>
            @endforelse
        </tbody>
        @if(count($cash_bookings))
        <tfoot>
            <tr>
                <td colspan="7" class="text-end">Cash Total</td>
                <td class="text-success">£{{ number_format($totals['cash_fare'] ?? ($totals['cash_total'] ?? 0), 2) }}</td>
                <td>{{ $totals['extra_cash'] ?? 0 }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- Financial Summary -->
    <table class="summary-table">
        <tr>
            <td class="summary-box" style="margin-right: 4%;">
                <table style="width: 100%;">
                    <tr><td>Total Work Amount:</td><td class="text-end"><strong>£{{ number_format($total_fare ?? 0, 2) }}</strong></td></tr>
                    <tr><td>Driver Commission (20%):</td><td class="text-end text-danger"><strong>- £{{ number_format($commission ?? 0, 2) }}</strong></td></tr>
                    <tr><td>Account Jobs Total:</td><td class="text-end"><strong>£{{ number_format($totals['account_fare'] ?? ($totals['account_total'] ?? 0), 2) }}</strong></td></tr>
                    <tr><td>Total Jobs:</td><td class="text-end"><strong>{{ number_format($total_jobs ?? (count($account_bookings) + count($cash_bookings))) }}</strong></td></tr>
                </table>
            </td>
            <td style="width: 4%;"></td>
            <td class="summary-box">
                <table style="width: 100%;">
                    <tr><td>Cash With Driver:</td><td class="text-end"><strong>£{{ number_format($totals['cash_fare'] ?? ($totals['cash_total'] ?? 0), 2) }}</strong></td></tr>
                    <tr><td>Parking:</td><td class="text-end"><strong>£{{ number_format($totals['parking'] ?? 0, 2) }}</strong></td></tr>
                    <tr><td>Brought Forward:</td><td class="text-end"><strong>£{{ number_format($brought_forward ?? 0, 2) }}</strong></td></tr>
                    <tr>
                        <td colspan="2">
                            <div class="earning-box text-success">
                                <span style="font-size: 10pt;">Driver Earning:</span>
                                <span style="float: right; font-size: 11pt;">£{{ number_format($driver_earning ?? 0, 2) }}</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        Thank you for choosing Crown Carz. This is a computer generated driver commission statement.
    </div>

</body>
</html>
