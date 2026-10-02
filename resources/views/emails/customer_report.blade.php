<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Customer Invoice Statement - Crown Carz</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 24px 12px;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }
        .email-wrapper {
            max-width: 620px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        .email-header {
            background-color: #0f172a;
            padding: 24px 30px;
            border-bottom: 3px solid #E6B04A;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .company-title {
            color: #ffffff;
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 2px 0;
        }
        .company-sub {
            color: #94a3b8;
            font-size: 11px;
            margin: 0;
        }
        .email-body {
            padding: 30px;
        }
        .greeting {
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 12px;
        }
        .intro-text {
            font-size: 13.5px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 20px;
        }
        .summary-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 18px 20px;
            margin-bottom: 22px;
        }
        .summary-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .summary-table td {
            padding: 5px 0;
        }
        .pdf-notice {
            background-color: #fefce8;
            border: 1px solid #fef08a;
            border-left: 4px solid #eab308;
            border-radius: 6px;
            padding: 12px 16px;
            font-size: 12.5px;
            color: #854d0e;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
        }
        .bank-card {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }
        .bank-card-title {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .bank-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
        }
        .bank-table td {
            padding: 4px 0;
        }
        .email-footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 20px 30px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            line-height: 1.5;
        }
    </style>
</head>
<body>

    @php
        $totalFare = 0;
        $totalParking = 0;
        foreach ($customers as $b) {
            $totalPrice = (float) ($b->price ?? 0);
            $parking = (float) ($b->parking ?? 0);
            $fare = isset($b->fare) ? (float) $b->fare : max(0.00, $totalPrice - $parking);
            $totalFare += $fare;
            $totalParking += $parking;
        }
        $grandTotal = $totalFare + $totalParking;
        $customerName = !empty($selectedCustomerName) ? $selectedCustomerName : 'Valued Customer';
        $formattedFrom = \Carbon\Carbon::parse($from)->format('d M Y');
        $formattedTo = \Carbon\Carbon::parse($to)->format('d M Y');
    @endphp

    <div class="email-wrapper">
        
        <!-- Header -->
        <div class="email-header">
            <table class="header-table">
                <tr>
                    <td style="vertical-align: middle;">
                        <div class="company-title">Crown Carz Ltd</div>
                        <div class="company-sub">52 Elvaston Way, Reading, RG30 4LU | www.crowncarz.com</div>
                    </td>
                    <td style="text-align: right; vertical-align: middle;">
                        <span style="background-color: #E6B04A; color: #0f172a; font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 4px 10px; border-radius: 4px; letter-spacing: 0.5px;">INVOICE</span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Body -->
        <div class="email-body">
            
            <div class="greeting">Dear {{ $customerName }},</div>

            <div class="intro-text">
                Please find attached your official <strong>Customer Invoice &amp; Booking Statement</strong> for the period <strong>{{ $formattedFrom }}</strong> to <strong>{{ $formattedTo }}</strong>.
            </div>

            <!-- Statement Summary Box -->
            <div class="summary-card">
                <div class="summary-title">Statement Summary</div>
                <table class="summary-table">
                    <tr>
                        <td style="color: #64748b;">Billing Period:</td>
                        <td style="text-align: right; font-weight: 600; color: #0f172a;">{{ $formattedFrom }} &ndash; {{ $formattedTo }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b;">Total Completed Bookings:</td>
                        <td style="text-align: right; font-weight: 600; color: #0284c7;">{{ count($customers) }} Booking(s)</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b;">Base Fares Total:</td>
                        <td style="text-align: right; font-weight: 600; color: #0f172a;">&pound;{{ number_format($totalFare, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b;">Parking / Extras Total:</td>
                        <td style="text-align: right; font-weight: 600; color: #0f172a;">&pound;{{ number_format($totalParking, 2) }}</td>
                    </tr>
                    <tr style="border-top: 1px solid #e2e8f0;">
                        <td style="padding-top: 8px; font-weight: 700; color: #0f172a; font-size: 14px;">Total Balance Due:</td>
                        <td style="padding-top: 8px; text-align: right; font-weight: 800; color: #b45309; font-size: 15px;">&pound;{{ number_format($grandTotal, 2) }}</td>
                    </tr>
                </table>
            </div>

            <!-- Attached PDF Notice -->
            <div class="pdf-notice">
                📎 <strong>PDF Attached:</strong> A comprehensive itemised breakdown of all journeys, routes, timings, and charges is attached to this email as a PDF document.
            </div>

            <!-- Bank Remittance Details -->
            <div class="bank-card">
                <div class="bank-card-title">Bank Transfer Details</div>
                <table class="bank-table">
                    <tr>
                        <td style="color: #64748b; width: 40%;">Bank Name:</td>
                        <td style="font-weight: 700; color: #0f172a;">HSBC Bank UK</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b;">Account Name:</td>
                        <td style="font-weight: 700; color: #0f172a;">Crown Carz Ltd</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b;">Sort Code:</td>
                        <td style="font-weight: 700; font-family: monospace; color: #0f172a;">40-38-04</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b;">Account Number:</td>
                        <td style="font-weight: 700; font-family: monospace; color: #0f172a;">85304792</td>
                    </tr>
                </table>
                <div style="font-size: 11px; color: #64748b; margin-top: 8px; border-top: 1px dashed #e2e8f0; padding-top: 6px;">
                    Kindly quote your account name or invoice reference with your bank transfer.
                </div>
            </div>

            <div style="font-size: 13px; color: #475569; line-height: 1.5;">
                If you have any questions regarding this invoice, please feel free to contact us at <a href="mailto:info@crowncarz.com" style="color: #0284c7; text-decoration: none;">info@crowncarz.com</a>.
                <br><br>
                Thank you for your business.<br>
                <strong>Crown Carz Team</strong>
            </div>

        </div>

        <!-- Footer -->
        <div class="email-footer">
            &copy; {{ date('Y') }} Crown Carz Ltd. All rights reserved.<br>
            52 Elvaston Way, Reading, RG30 4LU | Registered in England &amp; Wales
        </div>

    </div>

</body>
</html>