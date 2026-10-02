<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Customer Invoice Statement - Crown Carz Ltd</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 12mm 12mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
            color: #334155;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            line-height: 1.35;
        }

        /* 👑 HEADER SECTION */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .header-logo-cell {
            width: 58%;
            vertical-align: middle;
        }

        .header-meta-cell {
            width: 42%;
            text-align: right;
            vertical-align: middle;
        }

        .company-name {
            font-size: 16pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 3px 0;
            letter-spacing: -0.3px;
        }

        .company-details {
            font-size: 7.5pt;
            color: #64748b;
            line-height: 1.4;
        }

        .invoice-title-badge {
            display: inline-block;
            background-color: #1e293b;
            color: #ffffff;
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 5px 12px;
            border-radius: 4px;
            border-left: 3px solid #E6B04A;
            margin-bottom: 6px;
        }

        .meta-line {
            font-size: 7.5pt;
            color: #64748b;
            margin-top: 2px;
        }

        .meta-line strong {
            color: #1e293b;
        }

        /* 👑 GOLD ACCENT DIVIDER */
        .accent-bar {
            width: 100%;
            height: 3px;
            background-color: #E6B04A;
            margin: 8px 0 14px 0;
        }

        /* 👑 CLIENT & STATEMENT SUMMARY CARDS */
        .summary-boxes-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .summary-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            vertical-align: top;
        }

        .summary-card-title {
            font-size: 7pt;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
        }

        .summary-client-name {
            font-size: 9.5pt;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .summary-meta-text {
            font-size: 7.5pt;
            color: #475569;
            line-height: 1.35;
        }

        /* 👑 BOOKINGS TABLE */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .data-table thead th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 6px 5px;
            text-align: left;
            border-bottom: 2px solid #E6B04A;
            vertical-align: middle;
        }

        .data-table tbody tr {
            page-break-inside: avoid;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .data-table tbody td {
            font-size: 7pt;
            color: #334155;
            padding: 5px 5px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
            line-height: 1.25;
        }

        .ref-code {
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
            font-weight: bold;
            color: #0f172a;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }

        .amount-col {
            font-weight: bold;
            color: #0f172a;
            text-align: right;
        }

        /* 👑 BOTTOM SECTION (BANK DETAILS & TOTALS) */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
            margin-top: 10px;
        }

        .bank-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #1e293b;
            border-radius: 6px;
            padding: 8px 12px;
            vertical-align: top;
        }

        .bank-title {
            font-size: 7.5pt;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .bank-grid-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-bottom: 4px;
        }

        .bank-grid-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .bank-label {
            color: #64748b;
            font-size: 7pt;
        }

        .bank-val {
            font-weight: bold;
            color: #0f172a;
        }

        .bank-notes {
            font-size: 6.5pt;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            margin-top: 4px;
            line-height: 1.3;
        }

        .totals-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
            vertical-align: top;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 5px 8px;
            font-size: 7.5pt;
            border-bottom: 1px solid #e2e8f0;
        }

        .totals-table tr.grand-total-row td {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 8.5pt;
            font-weight: bold;
            border-bottom: none;
        }

        .totals-table tr.grand-total-row .total-val {
            color: #f1c40f;
            font-size: 9.5pt;
            text-align: right;
            font-weight: bold;
        }

        /* 👑 FOOTER */
        .footer-text {
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            margin-top: 14px;
            font-size: 6.5pt;
            color: #94a3b8;
            text-align: center;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

    <!-- 👑 COMPANY & STATEMENT HEADER -->
    <table class="header-table">
        <tr>
            <td class="header-logo-cell">
                <table style="border-collapse: collapse;">
                    <tr>
                        <td style="padding-right: 10px; vertical-align: middle;">
                            @php
                                $logoPath = public_path('images/logo.png');
                            @endphp
                            @if(file_exists($logoPath))
                                <img src="{{ $logoPath }}" alt="Crown Carz" style="height: 44px; max-width: 120px;">
                            @else
                                <img src="https://crowncarz.com/admin/public/images/logo.png" alt="Crown Carz" style="height: 44px; max-width: 120px;">
                            @endif
                        </td>
                        <td style="vertical-align: middle;">
                            <div class="company-name">Crown Carz Ltd</div>
                            <div class="company-details">
                                52 Elvaston Way, Reading, RG30 4LU<br>
                                www.crowncarz.com &nbsp;|&nbsp; info@crowncarz.com
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="header-meta-cell">
                <div class="invoice-title-badge">Customer Invoice / Statement</div>
                <div class="meta-line"><strong>INVOICE DATE:</strong> {{ \Carbon\Carbon::now()->format('d M Y') }}</div>
                <div class="meta-line"><strong>DURATION:</strong> {{ \Carbon\Carbon::parse($from)->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</div>
            </td>
        </tr>
    </table>

    <div class="accent-bar"></div>

    <!-- 👑 BILLED TO & STATEMENT SUMMARY -->
    <table class="summary-boxes-table">
        <tr>
            <td class="summary-card" style="width: 49%; padding-right: 8px;">
                <div class="summary-card-title">Billed To (Client / Account)</div>
                <div class="summary-client-name" style="font-size: 11pt; margin-top: 4px;">
                    {{ !empty($selectedCustomerName) ? $selectedCustomerName : 'All Account Customers' }}
                </div>
            </td>
            <td style="width: 2%;"></td>
            <td class="summary-card" style="width: 49%; padding-left: 8px;">
                <div class="summary-card-title">Statement Summary</div>
                <table style="width: 100%; border-collapse: collapse; font-size: 7.5pt;">
                    <tr>
                        <td style="color: #64748b; padding: 1px 0;">Billing Duration:</td>
                        <td style="text-align: right; font-weight: bold; color: #1e293b; padding: 1px 0;">
                            {{ \Carbon\Carbon::parse($from)->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 1px 0;">Booking Status:</td>
                        <td style="text-align: right; font-weight: bold; color: #1e293b; padding: 1px 0;">
                            {{ strtoupper(str_replace('_', ' ', $status ?? 'completed')) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 1px 0;">Total Bookings:</td>
                        <td style="text-align: right; font-weight: bold; color: #0284c7; padding: 1px 0;">
                            {{ count($customers) }} Booking(s)
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 👑 BOOKINGS DATA TABLE -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 9%;">Ref#</th>
                <th style="width: 12%;">Date / Time</th>
                <th style="width: 21%;">Pickup Location</th>
                <th style="width: 6%;">Via</th>
                <th style="width: 21%;">Dropoff Location</th>
                <th style="width: 8%;">Veh Type</th>
                <th style="width: 7%;" class="text-right">Fare (£)</th>
                <th style="width: 6%;" class="text-right">Parking</th>
                <th style="width: 10%;">Comments</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalFare = 0;
                $totalParking = 0;
            @endphp
            @forelse($customers as $booking)
                @php
                    $totalPrice = (float) ($booking->price ?? 0.00);
                    $parking = (float) ($booking->parking ?? 0.00);
                    $fare = isset($booking->fare) ? (float) $booking->fare : max(0.00, $totalPrice - $parking);
                    $totalFare += $fare;
                    $totalParking += $parking;

                    $viaList = !empty($booking->via_addresses) 
                        ? (is_array($booking->via_addresses) ? implode(', ', $booking->via_addresses) : (string)$booking->via_addresses)
                        : (!empty($booking->via) ? (is_array($booking->via) ? implode(', ', $booking->via) : (string)$booking->via) : '');
                @endphp
                <tr>
                    <td>
                        <span class="ref-code">{{ $booking->ref_no ?? ($booking->id ?? '-') }}</span>
                    </td>
                    <td>
                        <div class="fw-bold" style="color: #0f172a;">
                            {{ !empty($booking->pickup_time) ? \Carbon\Carbon::parse($booking->pickup_time)->format('d-M-Y') : '-' }}
                        </div>
                        <div style="font-size: 6.5pt; color: #64748b;">
                            {{ !empty($booking->pickup_time) ? \Carbon\Carbon::parse($booking->pickup_time)->format('H:i') : '' }}
                        </div>
                    </td>
                    <td>
                        {{ $booking->pickup_address ?? '-' }}
                    </td>
                    <td class="text-center">
                        @if(!empty($viaList) && $viaList !== '-')
                            <span style="font-size: 6.5pt; font-weight: bold; color: #475569;">{{ \Illuminate\Support\Str::limit($viaList, 15) }}</span>
                        @else
                            <span style="color: #94a3b8;">&ndash;</span>
                        @endif
                    </td>
                    <td>
                        {{ $booking->dropoff_address ?? '-' }}
                    </td>
                    <td>
                        {{ $booking->vehicle_make ?? ($booking->vehicle_type ?? 'Saloon') }}
                    </td>
                    <td class="amount-col">
                        {{ number_format($fare, 2) }}
                    </td>
                    <td class="text-right" style="color: #64748b;">
                        {{ number_format($parking, 2) }}
                    </td>
                    <td style="font-size: 6.5pt; color: #475569;">
                        {{ !empty($booking->comments) && $booking->comments !== 'N/A' ? $booking->comments : ($booking->passenger_name ?? 'N/A') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 12px; color: #64748b;">
                        No records found for the selected period and customer filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- 👑 BOTTOM REMITTANCE & FINANCIAL TOTALS -->
    <table class="bottom-table">
        <tr>
            <!-- Left: Bank Transfer Details -->
            <td class="bank-card" style="width: 58%; padding-right: 10px;">
                <div class="bank-title">Bank Transfer &amp; Payment Details</div>
                <table class="bank-grid-table">
                    <tr>
                        <td style="width: 50%;">
                            <div class="bank-label">Bank Name:</div>
                            <div class="bank-val">HSBC Bank UK</div>
                        </td>
                        <td style="width: 50%;">
                            <div class="bank-label">Account Name:</div>
                            <div class="bank-val">Crown Carz Ltd</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="bank-label">Sort Code:</div>
                            <div class="bank-val" style="font-family: monospace;">40-38-04</div>
                        </td>
                        <td>
                            <div class="bank-label">Account Number:</div>
                            <div class="bank-val" style="font-family: monospace;">85304792</div>
                        </td>
                    </tr>
                </table>

                @php
                    $sysSettings = app('App\Services\FirebaseService')->getData('system_settings') ?? [];
                    $customReceiptNote = $sysSettings['receipt_note'] ?? null;
                @endphp
                @if(!empty($customReceiptNote))
                    <div class="bank-notes">
                        {!! nl2br(e($customReceiptNote)) !!}
                    </div>
                @else
                    <div class="bank-notes">
                        Please arrange payment of the outstanding invoice amount using the bank details above. Kindly quote your invoice reference or account name with the transfer.
                    </div>
                @endif
            </td>

            <td style="width: 4%;"></td>

            <!-- Right: Totals Box -->
            <td class="totals-card" style="width: 38%;">
                <table class="totals-table">
                    <tr>
                        <td style="color: #64748b;">Total Base Fare:</td>
                        <td class="text-right fw-bold" style="color: #0f172a;">&pound;{{ number_format($totalFare, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b;">Total Parking / Tolls:</td>
                        <td class="text-right fw-bold" style="color: #0f172a;">&pound;{{ number_format($totalParking, 2) }}</td>
                    </tr>
                    <tr class="grand-total-row">
                        <td>TOTAL DUE:</td>
                        <td class="total-val">&pound;{{ number_format($totalFare + $totalParking, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 👑 FOOTER -->
    <div class="footer-text">
        Thank you for choosing <strong>Crown Carz Ltd</strong>. | Registered in England &amp; Wales | Computer Generated Invoice &amp; Statement
    </div>

</body>
</html>
