<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Booking Assigned</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #fff; border-radius: 8px; border: 1px solid #ddd; }
        .booking-info { border: 1px solid #eee; padding: 15px; border-radius: 5px; margin-top: 15px; }
        .booking-info img { max-width: 200px; margin-top: 10px; border-radius: 5px; }
        h2 { color: #333; }
        p { color: #555; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="container">
        <h2>New Booking Assigned</h2>
        <p>Dear {{ $booking['driver']['name'] ?? 'Driver' }},</p>

        <p>A new booking has been assigned to you. Please check the details below:</p>

        <div class="booking-info">
            <p><strong>Booking Reference:</strong> {{ $booking['ref_no'] ?? 'N/A' }}</p>
            <p><strong>Status:</strong> {{ ucfirst($booking['status'] ?? 'N/A') }}</p>
            <p><strong>Pickup Address:</strong> {{ $booking['pickup_address'] ?? 'N/A' }}</p>
            <p><strong>Dropoff Address:</strong> {{ $booking['dropoff_address'] ?? 'N/A' }}</p>
            <p><strong>Passenger Name:</strong> {{ $booking['passenger_name'] ?? 'N/A' }}</p>
        </div>

        <p>Thank you for being part of CrownCarz!</p>
    </div>
</body>
</html>
