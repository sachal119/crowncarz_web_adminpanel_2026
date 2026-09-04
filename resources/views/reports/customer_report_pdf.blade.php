<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 5px; }
        p { text-align: center; margin: 0; font-size: 11px; color: #555; }
    </style>
</head>
<body>
    <h2>Customer Report</h2>
    <p>From {{ $from }} to {{ $to }}</p>

    <table>
        <thead>
            <tr>
                <th>Booking Ref</th>
                <th>Passenger Name</th>
                <th>Pickup</th>
                <th>Dropoff</th>
                <th>Payment Type</th>
                <th>Price (£)</th>
                <th>Date/Time</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $booking)
            <tr>
                <td>{{ $booking->ref_no }}</td>
                <td>{{ $booking->passenger->name ?? '-' }}</td>
                <td>{{ $booking->pickup_address }}</td>
                <td>{{ $booking->dropoff_address }}</td>
                <td>{{ ucfirst($booking->payment_type) }}</td>
                <td>£{{ number_format($booking->price, 2) }}</td>
                <td>{{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;">No records found for selected filters.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
