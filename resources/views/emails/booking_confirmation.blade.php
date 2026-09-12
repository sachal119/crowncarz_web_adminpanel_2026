@php
$paymentType = strtolower($booking['payment_type'] ?? 'cash');

if ($paymentType === 'cash') {
    $paymentLabel = 'Pay in Car';
} elseif ($paymentType === 'card') {
    $paymentLabel = 'Payment Received';
} elseif ($paymentType === 'account') {
    $paymentLabel = 'Account';
} else {
    $paymentLabel = ucfirst($paymentType);
}
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Crown Carz Booking Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f9f9f9; padding:20px;">

<div style="max-width:800px; margin:auto; background:white; padding:20px; border-radius:10px;">

    <div style="text-align:center; margin-bottom:20px;">
        <img src="https://crowncarz.com/admin/public/images/logo.png"
             alt="Crown Carz" style="width:180px;">
    </div>

    <p>Dear <strong>{{ $booking['passenger_name'] ?? 'Passenger' }}</strong>,</p>

    <p>{{ $messageContent }}</p>

    <p>
        Your booking has been received successfully.
        Please keep this reference number for future communication.
    </p>

    <h3>Summary</h3>
    <p>
        <strong>Booking number:</strong> {{ $refNo }}<br>
        <strong>Passenger name:</strong> {{ $booking['passenger_name'] ?? 'N/A' }}<br>
        <strong>Passenger email:</strong> {{ $booking['email'] ?? 'N/A' }}<br>
        <strong>Contact no:</strong> {{ $booking['phone_no'] ?? '-' }}<br>
        <strong>Vehicle Category:</strong> {{ $booking['vehicle_make'] ?? '-' }}
    </p>

    <h3>Travel Information</h3>
    <p>
        <strong>Date:</strong> {{ $formattedPickupDate }}<br>
        <strong>Time:</strong> {{ $booking['pickup_time'] ?? '-' }}<br>
        <strong>Pickup:</strong> {{ $booking['pickup_address'] ?? '-' }}<br>
        <strong>Dropoff:</strong> {{ $booking['dropoff_address'] ?? '-' }}
    </p>

    <h3>Payment Details</h3>
    <p>
        <strong>Payment Type:</strong> {{ $paymentLabel ?? '-' }}<br>
        <strong>Total Cost:</strong> £ {{ $booking['price'] ?? '-' }}
    </p>

    <p>
        If any information is incorrect, please contact
        <strong>info@crowncarz.com</strong> or call
        <strong>01189 474747</strong> at least 24 hours before pickup.
    </p>

    <br>
    <p>Best Regards,<br><strong>Crown Carz</strong></p>

</div>
</body>
</html>
