<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Driver Commission Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        h2, h3 { text-align: center; margin: 0; }
    </style>
</head>
<body>
    <h2>Driver Commission Report</h2>
    <h3>{{ $from }} — {{ $to }}</h3>

    <p><strong>Driver:</strong> {{ $driver['name'] ?? 'N/A' }}<br>
       <strong>Email:</strong> {{ $driver['email'] ?? 'N/A' }}<br>
       <strong>Phone:</strong> {{ $driver['phone'] ?? 'N/A' }}</p>

    <h4>Account Bookings</h4>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Booking ID</th>
                <th>From</th>
                <th>To</th>
                <th>Vehicle</th>
                <th>Income (£)</th>
                <th>Tip (£)</th>
                <th>Parking (£)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($account_bookings as $b)
            <tr>
                <td>{{ $b['date'] }}</td>
                <td>{{ $b['booking_id'] }}</td>
                <td>{{ $b['from'] }}</td>
                <td>{{ $b['to'] }}</td>
                <td>{{ $b['vehicle'] }}</td>
                <td>{{ number_format($b['income'], 2) }}</td>
                <td>{{ number_format($b['tip'], 2) }}</td>
                <td>{{ number_format($b['parking'], 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;">No account bookings found</td></tr>
            @endforelse
        </tbody>
    </table>

    <h4>Cash Bookings</h4>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Booking ID</th>
                <th>From</th>
                <th>To</th>
                <th>Vehicle</th>
                <th>Income (£)</th>
                <th>Tip (£)</th>
                <th>Parking (£)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cash_bookings as $b)
            <tr>
                <td>{{ $b['date'] }}</td>
                <td>{{ $b['booking_id'] }}</td>
                <td>{{ $b['from'] }}</td>
                <td>{{ $b['to'] }}</td>
                <td>{{ $b['vehicle'] }}</td>
                <td>{{ number_format($b['income'], 2) }}</td>
                <td>{{ number_format($b['tip'], 2) }}</td>
                <td>{{ number_format($b['parking'], 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;">No cash bookings found</td></tr>
            @endforelse
        </tbody>
    </table>

    <h4>Totals</h4>
    <table>
        <tr><td>Account Total (£):</td><td>{{ number_format($totals['account_total'], 2) }}</td></tr>
        <tr><td>Cash Total (£):</td><td>{{ number_format($totals['cash_total'], 2) }}</td></tr>
        <tr><td>Tips (£):</td><td>{{ number_format($totals['tips'], 2) }}</td></tr>
        <tr><td>Parking (£):</td><td>{{ number_format($totals['parking'], 2) }}</td></tr>
        <tr><td>Waiting (£):</td><td>{{ number_format($totals['waiting'], 2) }}</td></tr>
        <tr><td><strong>Commission (20%) (£):</strong></td><td><strong>{{ number_format($commission, 2) }}</strong></td></tr>
    </table>
</body>
</html>
