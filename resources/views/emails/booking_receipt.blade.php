<!--<h2>Booking Receipt</h2>-->
<!--<p><strong>Reference:</strong> {{ $booking->ref_no ?? 'N/A' }}</p>-->
<!--<p><strong>Pickup:</strong> {{ $booking->pickup_address ?? 'N/A' }}</p>-->
<!--<p><strong>Dropoff:</strong> {{ $booking->dropoff_address ?? 'N/A' }}</p>-->
<!--<p><strong>Fare:</strong> £{{ number_format($booking->price ?? 0, 2) }}</p>-->
<!--<p><strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->pickup_time ?? now())->format('d/M/Y H:i:s') }}</p>-->

<!--<p>Thank you for choosing CrownCarz!</p>-->
@php
$paymentType = strtolower($booking->payment_type ?? '');

if ($paymentType === 'cash') {
    $paymentLabel = 'Pay in Car';
} elseif ($paymentType === 'card') {
    $paymentLabel = 'Payment Received';
} elseif ($paymentType === 'account') {
    $paymentLabel = 'Account';
} else {
    $paymentLabel = 'N/A';
}
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Booking Receipt</title>

    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .receipt-container {
            width: 90%;
            margin: auto;
            padding: 30px;
            border: 2px solid #f5c518;
            border-radius: 10px;
            background: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo {
            width: 120px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 26px;
            font-weight: bold;
            color: #000;
        }

        .section-title {
            font-weight: 600;
            font-size: 18px;
            margin-top: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        td {
            padding: 6px 0;
        }

        .summary-table td {
            font-size: 16px;
        }

        .total {
            font-size: 20px;
            font-weight: bold;
            color: #000;
        }

        .footer {
            margin-top: 25px;
            text-align: center;
            color: #777;
            font-size: 14px;
        }
    </style>
</head>

<body>

<div class="receipt-container">

    <div class="header">
        <img src="https://crowncarz.com/admin/public/images/logo.png" alt="Crown Airport Travels Logo" class="logo">
        <div class="title">Booking Receipt</div>
        <div>REF: {{ $booking->ref_no ?? $booking->id }}</div>
    </div>

    <div class="section-title">Customer Details</div>
    <table>
        <tr><td>Name:</td><td>{{ $booking->passenger_name }}</td></tr>
        <tr><td>Email:</td><td>{{ $booking->email ?? 'N/A' }}</td></tr>
        <tr><td>Phone:</td><td>{{ $booking->phone_no }}</td></tr>
    </table>

    <div class="section-title">Journey Details</div>
    <table>
        <tr><td>Pickup:</td><td>{{ $booking->pickup_address }}</td></tr>
        <tr><td>Dropoff:</td><td>{{ $booking->dropoff_address }}</td></tr>
        <tr><td>Date & Time:</td><td>{{ $booking->pickup_time }}</td></tr>
        <!--<tr><td>Time:</td><td>{{ $booking->pickup_time }}</td></tr>-->
        <tr><td>Vehicle:</td><td>{{ $booking->vehicle_make }}</td></tr>
    </table>

    <div class="section-title">Payment Summary</div>
    <table>
        <tr><td>Payment:</td><td>{{ $paymentLabel }}</td></tr>
        <tr><td>Total:</td><td>£{{ $booking->price }}</td></tr>
        <tr>
        <td colspan="2" style="text-align:center; padding-top:15px;">
            <a href="{{ $booking->receipt_link }}"
               style="background:#28a745; padding:12px 30px; color:white; font-size:16px; border-radius:6px; display:inline-block; text-decoration:none;">
                Download Receipt
            </a>
        </td>
    </tr>
    </table>

    <div class="footer">
        Thank you for choosing CrownCarz — We appreciate your business!
    </div>

</div>

</body>
</html>
