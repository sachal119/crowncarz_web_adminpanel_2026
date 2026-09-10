@php
$paymentType = strtolower($booking['payment_type'] ?? '');

if ($paymentType === 'cash') {
    $paymentLabel = 'Pay in Car';
    $paymentBadgeColor = '#92400e';
    $paymentBadgeBg = '#fef3c7';
} elseif ($paymentType === 'card') {
    $paymentLabel = 'Payment Received (Card)';
    $paymentBadgeColor = '#065f46';
    $paymentBadgeBg = '#d1fae5';
} elseif ($paymentType === 'account') {
    $paymentLabel = 'Account Invoice';
    $paymentBadgeColor = '#1e40af';
    $paymentBadgeBg = '#dbeafe';
} else {
    $paymentLabel = ucfirst($paymentType ?: 'Cash');
    $paymentBadgeColor = '#374151';
    $paymentBadgeBg = '#f3f4f6';
}

$logoData = '';
$logoPath = public_path('images/logo_black.png');
if (file_exists($logoPath)) {
    $logoData = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
} elseif (file_exists(public_path('images/logo.png'))) {
    $logoData = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/logo.png')));
}

$pickupDateFormatted = '';
if (!empty($booking['pickup_time'])) {
    try {
        $pickupDateFormatted = \Carbon\Carbon::parse($booking['pickup_time'])->format('d M Y, H:i');
    } catch (\Exception $e) {
        $pickupDateFormatted = $booking['pickup_time'];
    }
}
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Receipt - {{ $booking['ref_no'] ?? $booking['id'] }}</title>
    <style>
        @page {
            margin: 20px 25px 25px 25px;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            background: #ffffff;
            font-size: 13px;
            line-height: 1.45;
        }
        .receipt-wrapper {
            width: 100%;
            background: #ffffff;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .company-col {
            width: 58%;
            vertical-align: top;
        }
        .receipt-meta-col {
            width: 42%;
            vertical-align: top;
            text-align: right;
        }
        .logo-img {
            max-height: 52px;
            max-width: 180px;
            margin-bottom: 6px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .company-subtitle {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .company-contacts {
            font-size: 11px;
            color: #475569;
            line-height: 1.4;
        }
        .receipt-title {
            font-size: 22px;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: 1px;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .ref-box {
            display: inline-block;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 5px 12px;
            margin-top: 4px;
            text-align: right;
        }
        .ref-label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .ref-value {
            font-size: 14px;
            font-weight: 700;
            color: #d97706;
        }
        .meta-line {
            font-size: 11px;
            color: #64748b;
            margin-top: 3px;
        }
        .gold-divider {
            height: 3px;
            background: #d97706;
            margin: 10px 0 16px 0;
            border-radius: 2px;
        }
        .info-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin-bottom: 14px;
        }
        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            vertical-align: top;
        }
        .card-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #0f172a;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
        .kv-table {
            width: 100%;
            border-collapse: collapse;
        }
        .kv-table td {
            padding: 3px 0;
            font-size: 12px;
            vertical-align: top;
        }
        .kv-key {
            color: #64748b;
            width: 38%;
            font-weight: 500;
        }
        .kv-val {
            color: #0f172a;
            font-weight: 600;
            width: 62%;
        }
        .journey-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 16px;
        }
        .journey-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #0f172a;
            margin-bottom: 10px;
        }
        .route-step-table {
            width: 100%;
            border-collapse: collapse;
        }
        .route-step-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .step-icon-col {
            width: 28px;
            text-align: center;
        }
        .badge-pickup {
            display: inline-block;
            background: #10b981;
            color: #ffffff;
            font-size: 9px;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-via {
            display: inline-block;
            background: #f59e0b;
            color: #ffffff;
            font-size: 9px;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-dropoff {
            display: inline-block;
            background: #ef4444;
            color: #ffffff;
            font-size: 9px;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .step-content {
            padding-left: 8px;
        }
        .step-address {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }
        .step-time {
            font-size: 11px;
            color: #64748b;
            margin-top: 1px;
        }
        .fare-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }
        .fare-table th {
            background: #0f172a;
            color: #f8fafc;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 12px;
            text-align: left;
            font-weight: 600;
        }
        .fare-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 12px;
        }
        .fare-table tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .total-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .total-container td {
            vertical-align: top;
        }
        .payment-status-box {
            padding: 10px 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 12px;
        }
        .total-box {
            width: 250px;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            background-color: #0f172a;
            color: #ffffff;
            border-radius: 8px;
            padding: 12px 18px;
            text-align: right;
            border-right: 4px solid #d97706;
        }
        .total-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            font-weight: 600;
        }
        .total-amount {
            font-size: 24px;
            font-weight: 800;
            color: #fbbf24;
            margin-top: 2px;
        }
        .footer-note {
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
            margin-top: 10px;
            color: #64748b;
            font-size: 11px;
            line-height: 1.5;
        }
        .footer-thanks {
            font-weight: 700;
            color: #0f172a;
            font-size: 12px;
            margin-bottom: 2px;
        }
    </style>
</head>
<body>

<div class="receipt-wrapper">

    <!-- Header Table -->
    <table class="header-table">
        <tr>
            <td class="company-col">
                @if(!empty($logoData))
                    <img src="{{ $logoData }}" alt="Crown Carz" class="logo-img">
                @else
                    <div class="company-name">CROWN CARZ</div>
                @endif
                <div class="company-subtitle">Premium Airport & Chauffeur Services</div>
                <div class="company-contacts">
                    Tel: +44 (0)1189 47 47 47 &nbsp;|&nbsp; info@crowncarz.com<br>
                    Website: www.crowncarz.com &nbsp;|&nbsp; Reading, Berkshire, UK
                </div>
            </td>
            <td class="receipt-meta-col">
                <div class="receipt-title">RECEIPT</div>
                <div class="ref-box">
                    <div class="ref-label">Booking Reference</div>
                    <div class="ref-value">{{ $booking['ref_no'] ?? $booking['id'] }}</div>
                </div>
                <div class="meta-line">
                    <strong>Issued:</strong> {{ date('d M Y, H:i') }}
                </div>
                <div class="meta-line">
                    <strong>Status:</strong> <span style="color: #047857; font-weight: 700;">{{ strtoupper($booking['status'] ?? 'CONFIRMED') }}</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Gold Accent Divider -->
    <div class="gold-divider"></div>

    <!-- Customer & Service Overview Cards -->
    <table class="info-grid">
        <tr>
            <td class="info-card" style="width: 50%;">
                <div class="card-title">👤 Passenger Information</div>
                <table class="kv-table">
                    <tr>
                        <td class="kv-key">Name:</td>
                        <td class="kv-val">{{ $booking['passenger_name'] ?? 'Guest Passenger' }}</td>
                    </tr>
                    <tr>
                        <td class="kv-key">Phone:</td>
                        <td class="kv-val">{{ $booking['phone_no'] ?? $booking['phone'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="kv-key">Email:</td>
                        <td class="kv-val">{{ $booking['email'] ?? 'N/A' }}</td>
                    </tr>
                    @if(!empty($booking['account_name']))
                    <tr>
                        <td class="kv-key">Account:</td>
                        <td class="kv-val">{{ $booking['account_name'] }}</td>
                    </tr>
                    @endif
                </table>
            </td>
            <td class="info-card" style="width: 50%;">
                <div class="card-title">🚘 Service & Vehicle Details</div>
                <table class="kv-table">
                    <tr>
                        <td class="kv-key">Date & Time:</td>
                        <td class="kv-val">{{ $pickupDateFormatted ?: 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="kv-key">Vehicle Type:</td>
                        <td class="kv-val">{{ ucfirst($booking['vehicle_make'] ?? $booking['vehicle_id'] ?? 'Saloon') }}</td>
                    </tr>
                    <tr>
                        <td class="kv-key">Flight No:</td>
                        <td class="kv-val">{{ !empty($booking['flight_no']) ? $booking['flight_no'] : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="kv-key">Payment:</td>
                        <td class="kv-val"><span style="color: {{ $paymentBadgeColor }}; font-weight: 700;">{{ $paymentLabel }}</span></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Journey Itinerary Box -->
    <div class="journey-box">
        <div class="journey-title">📍 Journey Itinerary</div>
        <table class="route-step-table">
            <tr>
                <td class="step-icon-col">
                    <span class="badge-pickup">PICKUP</span>
                </td>
                <td class="step-content">
                    <div class="step-address">{{ $booking['pickup_address'] ?? 'N/A' }}</div>
                    <div class="step-time">Scheduled: {{ $pickupDateFormatted ?: 'N/A' }}</div>
                </td>
            </tr>

            @php
                $vias = [];
                if (!empty($booking['via_points'])) {
                    $vias = is_array($booking['via_points']) ? $booking['via_points'] : explode(',', $booking['via_points']);
                } elseif (!empty($booking['vias'])) {
                    $vias = is_array($booking['vias']) ? $booking['vias'] : explode(',', $booking['vias']);
                }
            @endphp

            @foreach($vias as $idx => $via)
                @if(trim($via))
                <tr>
                    <td class="step-icon-col" style="padding-top: 6px;">
                        <span class="badge-via">VIA {{ $idx + 1 }}</span>
                    </td>
                    <td class="step-content" style="padding-top: 6px;">
                        <div class="step-address">{{ trim($via) }}</div>
                    </td>
                </tr>
                @endif
            @endforeach

            <tr>
                <td class="step-icon-col" style="padding-top: 6px;">
                    <span class="badge-dropoff">DROPOFF</span>
                </td>
                <td class="step-content" style="padding-top: 6px;">
                    <div class="step-address">{{ $booking['dropoff_address'] ?? 'N/A' }}</div>
                </td>
            </tr>
        </table>

        @if(!empty($booking['comment']) || !empty($booking['comments']) || !empty($booking['note']))
        <div style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed #e2e8f0; font-size: 11px; color: #475569;">
            <strong>Special Notes:</strong> {{ $booking['comment'] ?? $booking['comments'] ?? $booking['note'] }}
        </div>
        @endif
    </div>

    <!-- Itemized Fare Table -->
    <table class="fare-table">
        <thead>
            <tr>
                <th style="width: 55%;">Description</th>
                <th style="width: 25%;">Type</th>
                <th style="width: 20%;" class="text-right">Amount (£)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $baseFare = (float)($booking['base_fare'] ?? $booking['price'] ?? 0);
                $parkingFee = (float)($booking['parking_charge'] ?? 0);
                $waitingFee = (float)($booking['waiting_charge'] ?? 0);
                $extraFee = (float)($booking['extra_charge'] ?? 0);
                $totalFare = (float)($booking['price'] ?? ($baseFare + $parkingFee + $waitingFee + $extraFee));
            @endphp
            <tr>
                <td>
                    <strong>Chauffeur & Transfer Journey</strong>
                    <div style="font-size: 11px; color: #64748b;">{{ $booking['vehicle_make'] ?? 'Standard' }} class transfer service</div>
                </td>
                <td>Standard Fare</td>
                <td class="text-right">£{{ number_format($baseFare > 0 ? $baseFare : $totalFare, 2) }}</td>
            </tr>
            @if($parkingFee > 0)
            <tr>
                <td><strong>Airport Parking / Drop-off Fee</strong></td>
                <td>Surcharge</td>
                <td class="text-right">£{{ number_format($parkingFee, 2) }}</td>
            </tr>
            @endif
            @if($waitingFee > 0)
            <tr>
                <td><strong>Waiting Time Charges</strong></td>
                <td>Additional</td>
                <td class="text-right">£{{ number_format($waitingFee, 2) }}</td>
            </tr>
            @endif
            @if($extraFee > 0)
            <tr>
                <td><strong>Extra Stops / Out of Area Surcharge</strong></td>
                <td>Additional</td>
                <td class="text-right">£{{ number_format($extraFee, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Total and Payment Summary Table -->
    <table class="total-container">
        <tr>
            <td style="width: 55%; padding-right: 12px;">
                <div class="payment-status-box">
                    <div style="font-weight: 700; color: #0f172a; margin-bottom: 2px;">Payment Method: {{ $paymentLabel }}</div>
                    <div style="color: #64748b; font-size: 11px;">
                        @if($paymentType === 'card')
                            Card payment settled securely online / terminal.
                        @elseif($paymentType === 'account')
                            Charged to approved corporate customer account.
                        @else
                            Payable in cash directly to the chauffeur inside the vehicle.
                        @endif
                    </div>
                </div>
            </td>
            <td style="width: 45%; text-align: right;">
                <div class="total-box" style="float: right;">
                    <div class="total-label">Total Fare</div>
                    <div class="total-amount">£{{ number_format($totalFare, 2) }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Footer Note -->
    <div class="footer-note">
        <div class="footer-thanks">Thank you for choosing Crown Carz!</div>
        Crown Carz Ltd is a licensed private hire operator registered in the UK.<br>
        For inquiries or future reservations, please visit <strong>www.crowncarz.com</strong> or call <strong>+44 (0)1189 47 47 47</strong>.
    </div>

</div>

</body>
</html>
