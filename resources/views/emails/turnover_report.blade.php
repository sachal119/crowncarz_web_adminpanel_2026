<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Turnover Report - CrownCarz</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .page-container {
            width: 210mm;
            max-width: 700px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            box-sizing: border-box;
        }
        .header-dark {
            background-color: #3d3d3d;
            color: white;
            padding: 25px 30px;
            margin: -30px -30px 25px -30px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            overflow: auto;
        }
        .header-left {
            float: left;
            width: 45%;
        }
        .header-right {
            float: right;
            width: 55%;
            text-align: right;
        }
        .header-right h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 400;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .meta-details {
            overflow: auto;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #E6B04A;
        }
        .billing-from {
            float: left;
            font-size: 12px;
            line-height: 1.5;
            color: #555;
        }
        .billing-from-label {
            font-weight: bold;
            font-size: 11px;
            color: #222;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .report-meta {
            float: right;
            font-size: 12px;
            text-align: right;
        }
        .report-meta table {
            margin-left: auto;
            border-collapse: collapse;
        }
        .report-meta td {
            padding: 3px 6px;
        }
        .meta-label {
            font-weight: bold;
            color: #555;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 13px;
        }
        .data-table th, .data-table td {
            padding: 10px 14px;
            border-bottom: 1px solid #e9ecef;
        }
        .data-table th {
            text-align: left;
            background-color: #fafafa;
            color: #495057;
            font-weight: 600;
        }
        .data-table td.text-end, .data-table th.text-end {
            text-align: right;
        }
        .highlight-row {
            background-color: #fffaf0;
            font-weight: bold;
        }
        .total-paid {
            color: #198754;
            font-weight: bold;
        }
        .money-account {
            color: #0d6efd;
            font-weight: bold;
        }
        .footer-area {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #6c757d;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="header-dark">
            <div class="header-left">
                <img src="https://crowncarz.com/admin/public/images/logo.png" alt="Crown Carz Logo" width="130" style="max-width: 130px; display: block;">
            </div>
            <div class="header-right">
                <h1>Turnover Report</h1>
            </div>
        </div>

        <div class="meta-details">
            <div class="billing-from">
                <div class="billing-from-label">Crown Carz</div>
                52 Elvaston Way, Reading, RG30 4LU<br>
                www.crowncarz.com | info@crowncarz.com
            </div>
            <div class="report-meta">
                <table>
                    <tr>
                        <td class="meta-label">INVOICE DATE:</td>
                        <td><strong>{{ $invoiceDate }}</strong></td>
                    </tr>
                    <tr>
                        <td class="meta-label">TRAVEL PERIOD:</td>
                        <td><strong>{{ $from }} - {{ $to }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

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
                <tr class="highlight-row"><th>Company Earning 100% of Turnover</th><td class="text-end">£{{ number_format($totals['company_earning'] ?? 0, 2) }}</td></tr>
                <tr class="highlight-row"><th>Company Earning 100% of Turnover (Markup)</th><td class="text-end">£{{ number_format($totals['company_earning_markup'] ?? 0, 2) }}</td></tr>
                <tr><th>Total Paid to Drivers</th><td class="text-end total-paid">£{{ number_format($totals['paid_to_drivers'] ?? 0, 2) }}</td></tr>
                <tr class="highlight-row"><th>Money in Account</th><td class="text-end money-account">£{{ number_format($totals['money_in_account'] ?? 0, 2) }}</td></tr>
            </tbody>
        </table>

        <div class="footer-area">
            Thank you for choosing Crown Carz. This is a computer generated Turnover Report.
        </div>
    </div>
</body>
</html>
