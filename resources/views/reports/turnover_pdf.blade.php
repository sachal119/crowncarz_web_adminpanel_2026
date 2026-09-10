@extends('layouts.app')

@section('content')
<style>
.report-container {
    max-width: 900px;
    margin: 0 auto;
}

.action-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-bottom: 24px;
}

.report-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #edf2f7;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    padding: 35px 40px;
}

.report-logo {
    height: 65px;
    max-width: 160px;
    object-fit: contain;
}

.gold-divider {
    height: 3px;
    background: linear-gradient(90deg, #E6B04A, #f3d289, #E6B04A);
    border-radius: 2px;
    width: 100%;
}

.report-title-badge {
    display: inline-block;
    background: #212529;
    color: #ffffff;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 0.8px;
    padding: 6px 16px;
    border-radius: 6px;
    text-transform: uppercase;
}

.meta-box {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 10px 14px;
}

.meta-label {
    font-size: 11px;
    font-weight: 700;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.meta-value {
    font-size: 13.5px;
    font-weight: 700;
    color: #212529;
}

.turnover-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}

.turnover-table th, 
.turnover-table td {
    padding: 11px 16px;
    font-size: 13.5px;
    border-bottom: 1px solid #f1f3f5;
}

.turnover-table tbody tr:hover {
    background-color: #f8fafc;
}

.turnover-table th {
    font-weight: 500;
    color: #495057;
    width: 70%;
}

.turnover-table td.text-end {
    text-align: right;
    font-weight: 600;
    color: #212529;
}

.row-highlight-gold {
    background-color: #fffdf5 !important;
}

.row-highlight-gold th {
    color: #856404 !important;
    font-weight: 700 !important;
}

.row-highlight-gold td {
    color: #856404 !important;
    font-weight: 700 !important;
}

.row-highlight-green th,
.row-highlight-green td {
    color: #198754 !important;
    font-weight: 700 !important;
}

.row-highlight-blue {
    background-color: #f0f7ff !important;
}

.row-highlight-blue th,
.row-highlight-blue td {
    color: #0d6efd !important;
    font-weight: 700 !important;
}

.report-footer {
    border-top: 1px solid #dee2e6;
    margin-top: 25px;
    padding-top: 15px;
    font-size: 12px;
    color: #6c757d;
    text-align: center;
}

@media print {
    @page {
        size: A4 portrait;
        margin: 10mm;
    }

    body * {
        visibility: hidden;
    }

    .report-container,
    .report-container * {
        visibility: visible;
    }

    .action-buttons,
    .modal,
    nav,
    header,
    .navbar,
    .sidebar {
        display: none !important;
    }

    .report-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .report-card {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }

    .turnover-table th, 
    .turnover-table td {
        padding: 6px 8px !important;
        font-size: 9pt !important;
    }

    .report-logo {
        height: 55px !important;
    }

    .gold-divider {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .row-highlight-gold,
    .row-highlight-blue {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>

<div class="container mt-4 report-container">
    <!-- Action Buttons -->
    <div class="action-buttons">
        <button class="btn btn-dark shadow-sm px-3" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print
        </button>
        <a href="{{ route('reports.turnover.download', ['from' => $from, 'to' => $to]) }}" class="btn btn-danger shadow-sm px-3 text-white">
            <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
        </a>
        <button class="btn btn-warning shadow-sm px-3 text-dark fw-semibold" id="emailTurnoverReport">
            <i class="bi bi-envelope me-1"></i> Email Report
        </button>
    </div>

    <!-- Official Report Card -->
    <div class="report-card">
        <!-- Header Row -->
        <div class="row align-items-center mb-3">
            <div class="col-sm-7 col-12 d-flex align-items-center gap-3">
                <img src="https://crowncarz.com/admin/public/images/logo.png" alt="Crown Carz Logo" class="report-logo">
                <div>
                    <h3 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">Crown Carz</h3>
                    <div class="text-muted small mt-1">52 Elvaston Way, Reading, RG30 4LU</div>
                    <div class="small">
                        <a href="https://www.crowncarz.com" target="_blank" class="text-decoration-none text-muted">www.crowncarz.com</a> 
                        <span class="text-muted">|</span> 
                        <a href="mailto:info@crowncarz.com" class="text-decoration-none text-muted">info@crowncarz.com</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-5 col-12 text-sm-end mt-3 mt-sm-0">
                <div class="report-title-badge mb-2">Turnover Report</div>
                <div class="text-muted small"><strong>INVOICE DATE:</strong> {{ $invoiceDate }}</div>
                <div class="text-muted small"><strong>TRAVEL PERIOD:</strong> {{ $from }} - {{ $to }}</div>
            </div>
        </div>

        <div class="gold-divider mb-4"></div>

        <!-- Meta summary cards -->
        <div class="row g-2 mb-4">
            <div class="col-md-6 col-12">
                <div class="meta-box d-flex justify-content-between align-items-center">
                    <span class="meta-label">Invoice Date</span>
                    <span class="meta-value">{{ $invoiceDate }}</span>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="meta-box d-flex justify-content-between align-items-center">
                    <span class="meta-label">Travel Period</span>
                    <span class="meta-value">{{ $from }} &nbsp;to&nbsp; {{ $to }}</span>
                </div>
            </div>
        </div>

        <!-- Data Breakdown Table -->
        <div class="table-responsive">
            <table class="table turnover-table align-middle mb-0">
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
                    <tr class="row-highlight-gold"><th>Company Earning 100% of Turnover</th><td class="text-end">£{{ number_format($totals['company_earning'] ?? 0, 2) }}</td></tr>
                    <tr class="row-highlight-gold"><th>Company Earning 100% of Turnover (Markup)</th><td class="text-end">£{{ number_format($totals['company_earning_markup'] ?? 0, 2) }}</td></tr>
                    <tr class="row-highlight-green"><th>Total Paid to Drivers</th><td class="text-end">£{{ number_format($totals['paid_to_drivers'] ?? 0, 2) }}</td></tr>
                    <tr class="row-highlight-blue"><th>Money in Account</th><td class="text-end">£{{ number_format($totals['money_in_account'] ?? 0, 2) }}</td></tr>
                </tbody>
            </table>
        </div>

        <div class="report-footer">
            Thank you for choosing Crown Carz. This is a computer generated Turnover Report.
        </div>
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
