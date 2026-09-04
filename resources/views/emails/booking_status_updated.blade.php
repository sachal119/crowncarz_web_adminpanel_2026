<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Status Updated</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { padding: 20px; }
        .booking-info { border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
        .booking-info img { max-width: 200px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Booking Status Update</h2>
        <p>Dear {{ $booking['passenger_name'] ?? 'Passenger' }},</p>

        <div class="booking-info">
            <p><strong>Booking Reference:</strong> {{ $booking['ref_no'] ?? 'N/A' }}</p>
            <p><strong>Status:</strong> {{ ucfirst($booking['status'] ?? 'N/A') }}</p>
            <p><strong>Pickup:</strong> {{ $booking['pickup_address'] ?? 'N/A' }}</p>
            <p><strong>Dropoff:</strong> {{ $booking['dropoff_address'] ?? 'N/A' }}</p>
            

            @if(isset($booking['driver']))
                <p><strong>Driver:</strong> {{ $booking['driver']['name'] ?? 'N/A' }} | {{ $booking['driver']['phone'] ?? '' }}</p>
            @endif

        </div>

        <p>Thank you for choosing CrownCarz!</p>
    </div>
</body>
</html>
