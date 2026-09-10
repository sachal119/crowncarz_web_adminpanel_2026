@extends('layouts.app')

@section('content')
<style>
.print-receipt-header,
.print-receipt-footer {
    display: none;
}

.action-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-bottom: 20px;
}

@media print {
    @page {
        size: A4;
        margin: 12mm;
    }

    body * {
        visibility: hidden;
    }

    .container,
    .container * {
        visibility: visible;
    }

    .action-buttons,
    .modal,
    .btn,
    nav,
    header,
    .navbar,
    .sidebar {
        display: none !important;
    }

    .container {
        position: absolute;
        left: 50%;
        top: 0;
        transform: translateX(-50%);
        width: 100%;
        max-width: 190mm;
        margin: 0 auto;
        padding: 0;
        font-size: 10pt;
        color: #222;
    }

    .print-receipt-header,
    .print-receipt-footer {
        display: block !important;
    }

    .print-receipt-header {
        border-bottom: 3px solid #E6B04A;
        margin-bottom: 18px;
        padding-bottom: 14px;
    }

    .print-brand-row {
        align-items: center;
        display: flex;
        justify-content: space-between;
        gap: 20px;
    }

    .print-logo {
        height: 72px;
        max-width: 180px;
        object-fit: contain;
    }

    .print-company-details {
        text-align: right;
        font-size: 9pt;
        line-height: 1.5;
    }

    .print-company-details h1 {
        color: #343a40;
        font-size: 20pt;
        font-weight: 700;
        margin: 0 0 6px;
    }

    .print-receipt-meta {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-top: 14px;
    }

    .print-meta-box {
        border: 1px solid #dee2e6;
        padding: 8px 10px;
    }

    .print-meta-label {
        color: #6c757d;
        display: block;
        font-size: 8pt;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .print-meta-value {
        color: #212529;
        display: block;
        font-size: 10pt;
        font-weight: 700;
        margin-top: 2px;
    }

    .web-header {
        display: none !important;
    }

    .card {
        border: 0 !important;
        box-shadow: none !important;
    }

    .card-body {
        padding: 0 !important;
    }

    .table {
        font-size: 8.5pt;
    }

    .print-receipt-footer {
        border-top: 1px solid #dee2e6;
        clear: both;
        color: #6c757d;
        font-size: 8pt;
        margin-top: 24px;
        padding-top: 10px;
        text-align: center;
    }
}
</style>

<div class="container mt-4" style="max-width: 1000px;">
    <!-- Print Specific Header -->
    <div class="print-receipt-header">
        <div class="print-brand-row">
            <div>
                <img src="https://crowncarz.com/admin/public/images/logo.png" alt="Crown Carz Logo" class="print-logo">
            </div>
            <div class="print-company-details">
                <h1>Crown Carz</h1>
                <div>52 Elvaston Way, Reading, RG30 4LU</div>
                <div>www.crowncarz.com | info@crowncarz.com</div>
            </div>
        </div>
        <div class="print-receipt-meta">
            <div class="print-meta-box">
                <span class="print-meta-label">INVOICE DATE</span>
                <span class="print-meta-value">{{ $invoiceDate }}</span>
            </div>
            <div class="print-meta-box">
                <span class="print-meta-label">TRAVEL PERIOD</span>
                <span class="print-meta-value">{{ $from }} - {{ $to }}</span>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <button class="btn btn-dark" onclick="window.print()">
            🖨️ Print
        </button>
        <a href="{{ route('reports.turnover.download', ['from' => $from, 'to' => $to]) }}" class="btn btn-danger text-white">
            📥 Export PDF
        </a>
        <button class="btn btn-warning text-dark fw-semibold" id="emailTurnoverReport">
            ✉️ Email Report
        </button>
    </div>

    <!-- Web Header -->
    <div class="web-header text-center mb-4">
        <h2 class="fw-semibold text-secondary">Turnover Report</h2>
        <p class="mb-1"><strong>INVOICE DATE:</strong> {{ $invoiceDate }}</p>
        <p class="mb-0"><strong>TRAVEL PERIOD:</strong> {{ $from }} - {{ $to }}</p>
        <hr class="mt-3" style="border-color: #E6B04A; opacity: 1; width: 60%; margin: 0 auto;">
    </div>

    <!-- Company Info -->
    <div class="web-header text-center mb-4">
        <p class="mb-0">
            Office address: 52 Elvaston Way, Reading, RG30 4LU
        </p>
        <p class="mb-0">
            <a href="https://www.crowncarz.com" class="text-primary text-decoration-none">www.crowncarz.com</a> |
            <a href="mailto:info@crowncarz.com" class="text-primary text-decoration-none">info@crowncarz.com</a>
        </p>
    </div>

    <!-- Totals Table -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <table class="table table-striped table-hover align-middle mb-0">
                <tbody class="fs-6">
                    <tr><th style="width: 70%;">Total Fare (Driver Fare)</th><td class="text-end fw-semibold">£{{ number_format($totals['fare_total'], 2) }}</td></tr>
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
                    <tr class="table-warning"><th><strong>Company Earning 100% of Turnover</strong></th><td class="text-end fw-bold">£{{ number_format($totals['company_earning'], 2) }}</td></tr>
                    <tr class="table-warning"><th><strong>Company Earning 100% of Turnover (Markup)</strong></th><td class="text-end fw-bold">£{{ number_format($totals['company_earning_markup'], 2) }}</td></tr>
                    <tr><th><strong>Total Paid to Drivers</strong></th><td class="text-end text-success fw-bold">£{{ number_format($totals['paid_to_drivers'], 2) }}</td></tr>
                    <tr class="table-primary"><th><strong>Money in Account</strong></th><td class="text-end text-primary fw-bold">£{{ number_format($totals['money_in_account'], 2) }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="print-receipt-footer">
        Thank you for choosing Crown Carz. This is a computer generated Turnover Report.
    </div>
</div>

<!-- Email Modal -->
<div class="modal fade" id="turnoverEmailModal" tabindex="-1" aria-labelledby="turnoverEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-warning bg-opacity-25">
                <h5 class="modal-title fw-semibold text-dark" id="turnoverEmailModalLabel">
                    <i class="bi bi-envelope-fill me-1"></i> Send Turnover Report via Email
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="sendTurnoverEmailForm">
                    @csrf
                    <div class="mb-3">
                        <label for="turnoverEmailInput" class="form-label fw-semibold">Recipient Email Address:</label>
                        <input type="email" class="form-control" id="turnoverEmailInput" name="email" placeholder="e.g. accounts@crowncarz.com" required>
                    </div>

                    <input type="hidden" name="from" value="{{ $from }}">
                    <input type="hidden" name="to" value="{{ $to }}">

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning text-dark fw-semibold" id="btnSendTurnoverEmail">
                            <i class="bi bi-send me-1"></i> Send Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const emailBtn = document.getElementById('emailTurnoverReport');
    const modalEl = document.getElementById('turnoverEmailModal');
    const form = document.getElementById('sendTurnoverEmailForm');
    const submitBtn = document.getElementById('btnSendTurnoverEmail');

    if (emailBtn && modalEl) {
        emailBtn.addEventListener('click', function () {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        });
    }

    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';
            }

            const formData = new FormData(this);

            try {
                const response = await fetch("{{ route('reports.turnover.send-email') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    alert('📩 ' + (data.message || 'Turnover report sent successfully!'));
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();
                    form.reset();
                } else {
                    alert('❌ ' + (data.message || 'Failed to send email. Please try again.'));
                }
            } catch (err) {
                console.error(err);
                alert('⚠️ Network error while sending email.');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-send me-1"></i> Send Email';
                }
            }
        });
    }
});
</script>
@endpush
@endsection
