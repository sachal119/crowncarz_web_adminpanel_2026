@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Header -->
    <div class="text-center mb-5">
        <!--<h2 class="fw-bold text-uppercase text-dark">-->
        <!--    <span class="text-warning">CrownCarz</span>-->
        <!--</h2>-->
        <h2 class="fw-semibold text-secondary">Turnover Report</h2>
        <p class="mb-1"><strong>INVOICE DATE:</strong> {{ $invoiceDate }}</p>
        <p class="mb-0"><strong>TRAVEL PERIOD:</strong> {{ $from }} - {{ $to }}</p>
        <hr class="mt-3" style="border-color: #E6B04A; opacity: 1; width: 60%; margin: 0 auto;">
    </div>

    <!-- Company Info -->
    <div class="text-center mb-4">
        <p class="mb-0">
            Office address: 52 Elvaston Way, Reading, RG30 4LU
        </p>
        <p class="mb-0">
            <a href="https://www.crowncarz.com" class="text-primary text-decoration-none">www.crowncarz.com</a> |
            <a href="mailto:info@crowncarz.com" class="text-primary text-decoration-none">info@crowncarz.com</a>
        </p>
    </div>

    <!-- Totals Table -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <table class="table table-borderless table-sm align-middle">
                <tbody class="fs-6">
                    <tr><th>Total Fare (Driver Fare)</th><td class="text-end">£{{ number_format($totals['fare_total'], 2) }}</td></tr>
                    <tr><th>Total Fare (Driver Fare - 20%)</th><td class="text-end">£{{ number_format($totals['fare_after_commission'], 2) }}</td></tr>
                    <tr><th>Total Markup Fare</th><td class="text-end">£{{ number_format($totals['markup_fare'], 2) }}</td></tr>
                    <tr><th>Total Service Charge</th><td class="text-end">£{{ number_format($totals['service_charge'], 2) }}</td></tr>
                    <tr><th>Total Extra</th><td class="text-end">£{{ number_format($totals['extras'], 2) }}</td></tr>
                    <tr><th>Total Markup Extras</th><td class="text-end">£{{ number_format($totals['markup_extras'], 2) }}</td></tr>
                    <tr><th>Total Waiting</th><td class="text-end">£{{ number_format($totals['waiting'], 2) }}</td></tr>
                    <tr><th>Total Parking</th><td class="text-end">£{{ number_format($totals['parking'], 2) }}</td></tr>
                    <tr><th>Total Markup Parking</th><td class="text-end">£{{ number_format($totals['markup_parking'], 2) }}</td></tr>
                    <tr><th>Total Customer Toll</th><td class="text-end">£{{ number_format($totals['customer_toll'], 2) }}</td></tr>
                    <tr><th>Total Driver Toll</th><td class="text-end">£{{ number_format($totals['driver_toll'], 2) }}</td></tr>
                    <tr><th>Total Customer ULEZ</th><td class="text-end">£{{ number_format($totals['customer_ulez'], 2) }}</td></tr>
                    <tr><th>Total Driver ULEZ</th><td class="text-end">£{{ number_format($totals['driver_ulez'], 2) }}</td></tr>
                    <tr><th>Drivers Earnings % of Turnover</th><td class="text-end">£0.00</td></tr>
                    <tr><th>Drivers Earnings % of Turnover (Markup)</th><td class="text-end">£0.00</td></tr>
                    <tr><th>Company Earning 100% of Turnover</th><td class="text-end fw-semibold">£{{ number_format($totals['company_earning'], 2) }}</td></tr>
                    <tr><th>Company Earning 100% of Turnover (Markup)</th><td class="text-end fw-semibold">£{{ number_format($totals['company_earning_markup'], 2) }}</td></tr>
                    <tr><th>Total Paid to Drivers</th><td class="text-end text-success fw-bold">£{{ number_format($totals['paid_to_drivers'], 2) }}</td></tr>
                    <tr><th>Money in Account</th><td class="text-end text-primary fw-bold">£{{ number_format($totals['money_in_account'], 2) }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
