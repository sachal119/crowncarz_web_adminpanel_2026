<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Turnover Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #222; margin: 0; padding: 15px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { color: #555; margin: 0 0 5px; font-size: 20px; }
        .header p { margin: 2px 0; color: #666; font-size: 11px; }
        .header hr { border: none; border-top: 2px solid #E6B04A; width: 60%; margin: 10px auto; }
        .company-info { text-align: center; margin-bottom: 20px; font-size: 11px; color: #444; }
        .company-info a { color: #0d6efd; text-decoration: none; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 7px 10px; border-bottom: 1px solid #e0e0e0; }
        th { text-align: left; font-weight: normal; color: #333; }
        td.text-end { text-align: right; font-weight: 600; }
        .fw-bold { font-weight: bold; }
        .text-success { color: #198754; }
        .text-primary { color: #0d6efd; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #888; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Turnover Report</h2>
        <p><strong>INVOICE DATE:</strong> {{ $invoiceDate ?? date('d M Y') }}</p>
        <p><strong>TRAVEL PERIOD:</strong> {{ $from }} - {{ $to }}</p>
        <hr>
    </div>

    <div class="company-info">
        <p style="margin: 2px 0;">Office address: 52 Elvaston Way, Reading, RG30 4LU</p>
        <p style="margin: 2px 0;">www.crowncarz.com | info@crowncarz.com</p>
    </div>

    <table>
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
            <tr><th><strong>Company Earning 100% of Turnover</strong></th><td class="text-end"><strong>£{{ number_format($totals['company_earning'] ?? 0, 2) }}</strong></td></tr>
            <tr><th><strong>Company Earning 100% of Turnover (Markup)</strong></th><td class="text-end"><strong>£{{ number_format($totals['company_earning_markup'] ?? 0, 2) }}</strong></td></tr>
            <tr><th><strong>Total Paid to Drivers</strong></th><td class="text-end text-success fw-bold">£{{ number_format($totals['paid_to_drivers'] ?? 0, 2) }}</td></tr>
            <tr><th><strong>Money in Account</strong></th><td class="text-end text-primary fw-bold">£{{ number_format($totals['money_in_account'] ?? 0, 2) }}</td></tr>
        </tbody>
    </table>

    <div class="footer">
        Thank you for choosing Crown Carz. This is a computer generated Turnover Report.
    </div>
</body>
</html>
