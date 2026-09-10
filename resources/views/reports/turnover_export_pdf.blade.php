<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Turnover Report - Crown Carz</title>
    <style>
        @page {
            margin: 12mm 15mm;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #2c3e50;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .logo-img {
            max-height: 60px;
            max-width: 160px;
        }
        .company-title {
            font-size: 18px;
            font-weight: bold;
            color: #222222;
            margin: 0 0 2px 0;
        }
        .company-subtitle {
            font-size: 9.5px;
            color: #666666;
            line-height: 1.4;
        }
        .report-title-box {
            text-align: right;
        }
        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a1a1a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 5px 0;
        }
        .meta-text {
            font-size: 10px;
            color: #555555;
            line-height: 1.4;
        }
        .meta-text strong {
            color: #222222;
        }
        .gold-line {
            height: 3px;
            background-color: #E6B04A;
            width: 100%;
            margin: 10px 0 14px 0;
            border: none;
        }
        .meta-boxes-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-bottom: 12px;
        }
        .meta-box-td {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 6px 10px;
            font-size: 10px;
        }
        .meta-box-label {
            color: #6c757d;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .meta-box-val {
            color: #212529;
            font-size: 11px;
            font-weight: bold;
            margin-top: 2px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        table.data-table th, table.data-table td {
            padding: 7px 10px;
            border-bottom: 1px solid #e9ecef;
            font-size: 10px;
        }
        table.data-table tr:nth-child(even) {
            background-color: #fcfcfd;
        }
        table.data-table th {
            text-align: left;
            font-weight: normal;
            color: #495057;
            width: 70%;
        }
        table.data-table td.text-end {
            text-align: right;
            font-weight: 600;
            color: #212529;
        }
        .highlight-gold {
            background-color: #fffdf5 !important;
        }
        .highlight-gold th, .highlight-gold td {
            color: #856404 !important;
            font-weight: bold !important;
            border-top: 1px solid #ffeeba;
            border-bottom: 1px solid #ffeeba;
        }
        .text-success {
            color: #198754 !important;
            font-weight: bold;
        }
        .highlight-blue {
            background-color: #f0f7ff !important;
        }
        .highlight-blue th, .highlight-blue td {
            color: #0d6efd !important;
            font-weight: bold !important;
            border-top: 1px solid #cce5ff;
            border-bottom: 1px solid #cce5ff;
        }
        .footer {
            margin-top: 25px;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
            text-align: center;
            font-size: 9px;
            color: #888888;
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('images/logo.png');
        $logoSrc = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : 'https://crowncarz.com/admin/public/images/logo.png';
    @endphp
    
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 70px; vertical-align: middle;">
                            <img src="{{ $logoSrc }}" alt="Crown Carz" class="logo-img">
                        </td>
                        <td style="vertical-align: middle; padding-left: 10px;">
                            <div class="company-title">Crown Carz</div>
                            <div class="company-subtitle">
                                52 Elvaston Way, Reading, RG30 4LU<br>
                                www.crowncarz.com | info@crowncarz.com
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 45%;" class="report-title-box">
                <div class="report-title">Turnover Report</div>
                <div class="meta-text"><strong>INVOICE DATE:</strong> {{ $invoiceDate ?? date('d M Y') }}</div>
                <div class="meta-text"><strong>TRAVEL PERIOD:</strong> {{ $from }} - {{ $to }}</div>
            </td>
        </tr>
    </table>

    <div class="gold-line"></div>

    <table class="data-table">
        <tbody>
            <tr><th>Total Fare (Driver Fare)</th><td class="text-end">£{{ number_format($totals['fare_total'] ?? 0, 2) }}</td></tr>
            <tr><th>Total Fare (Driver Fare - 20%)</th><td class="text-end">£{{ number_format($totals['fare_after_commission'] ?? 0, 2) }}</td></tr>
            <tr><th>Total Markup Fare</th><td class="text-end">£{{ number_format($totals['markup_fare'] ?? 0, 2) }}</td></tr>
            <tr><th>Total Service Charge</th><td class="text-end">£{{ number_format($totals['service_charge'] ?? 0, 2) }}</td></tr>
            <tr><th>Total Extra</th><td class="text-end">£{{ number_format($totals['extras'] ?? 0, 2) }}</td></tr>
            <tr><th>Total Markup Extras</th><td class="text-end">£{{ number_format($totals['markup_extras'] ?? 0, 2) }}</td></tr>
            <tr><th>Total Waiting</th><td class="text-end">£{{ number_format($totals['waiting'] ?? 0, 2) }}</td></tr>
            <tr><th>Total Parking</th><td class="text-end">£{{ number_format($totals['parking'] ?? 0, 2) }}</td></tr>
            <tr><th>Total Markup Parking</th><td class="text-end">£{{ number_format($totals['markup_parking'] ?? 0, 2) }}</td></tr>
            <tr><th>Total Customer Toll</th><td class="text-end">£{{ number_format($totals['customer_toll'] ?? 0, 2) }}</td></tr>
            <tr><th>Total Driver Toll</th><td class="text-end">£{{ number_format($totals['driver_toll'] ?? 0, 2) }}</td></tr>
            <tr><th>Total Customer ULEZ</th><td class="text-end">£{{ number_format($totals['customer_ulez'] ?? 0, 2) }}</td></tr>
            <tr><th>Total Driver ULEZ</th><td class="text-end">£{{ number_format($totals['driver_ulez'] ?? 0, 2) }}</td></tr>
            <tr><th>Drivers Earnings % of Turnover</th><td class="text-end">£0.00</td></tr>
            <tr><th>Drivers Earnings % of Turnover (Markup)</th><td class="text-end">£0.00</td></tr>
            <tr class="highlight-gold"><th>Company Earning 100% of Turnover</th><td class="text-end">£{{ number_format($totals['company_earning'] ?? 0, 2) }}</td></tr>
            <tr class="highlight-gold"><th>Company Earning 100% of Turnover (Markup)</th><td class="text-end">£{{ number_format($totals['company_earning_markup'] ?? 0, 2) }}</td></tr>
            <tr><th><strong>Total Paid to Drivers</strong></th><td class="text-end text-success">£{{ number_format($totals['paid_to_drivers'] ?? 0, 2) }}</td></tr>
            <tr class="highlight-blue"><th><strong>Money in Account</strong></th><td class="text-end">£{{ number_format($totals['money_in_account'] ?? 0, 2) }}</td></tr>
        </tbody>
    </table>

    <div class="footer">
        Thank you for choosing Crown Carz. This is a computer generated Turnover Report.
    </div>
</body>
</html>
