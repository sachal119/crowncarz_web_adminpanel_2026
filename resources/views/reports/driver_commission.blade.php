@extends('layouts.app')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
/* 🔸 Screen Styles */
.screen-header-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}

.statement-title {
    color: #0f172a;
    font-weight: 700;
    font-size: 1.65rem;
    letter-spacing: -0.4px;
    margin-bottom: 6px;
}

.meta-pill {
    display: inline-flex;
    align-items: center;
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #334155;
    padding: 6px 14px;
    border-radius: 9999px;
    font-size: 0.85rem;
    font-weight: 500;
}

.meta-pill i {
    font-size: 0.95rem;
}

.btn-export-pdf {
    background-color: #dc2626;
    border-color: #dc2626;
    color: #ffffff !important;
    font-weight: 600;
    padding: 7px 16px;
    border-radius: 8px;
    transition: all 0.2s ease;
}
.btn-export-pdf:hover {
    background-color: #b91c1c;
    border-color: #b91c1c;
    box-shadow: 0 4px 10px rgba(220, 38, 38, 0.25);
}

.btn-print-action {
    background-color: #ffffff;
    border: 1px solid #cbd5e1;
    color: #1e293b !important;
    font-weight: 600;
    padding: 7px 16px;
    border-radius: 8px;
    transition: all 0.2s ease;
}
.btn-print-action:hover {
    background-color: #f1f5f9;
    border-color: #94a3b8;
}

.btn-email-action {
    background-color: #E6B04A;
    border-color: #E6B04A;
    color: #1e293b !important;
    font-weight: 600;
    padding: 7px 16px;
    border-radius: 8px;
    transition: all 0.2s ease;
}
.btn-email-action:hover {
    background-color: #d49f39;
    border-color: #d49f39;
    box-shadow: 0 4px 10px rgba(230, 176, 74, 0.3);
}

.report-section-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
}

.report-section-header {
    background-color: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 14px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.report-section-title {
    color: #1e293b;
    font-weight: 700;
    font-size: 1.05rem;
    margin: 0;
    display: flex;
    align-items: center;
}

.report-table thead th {
    background: #E6B04A;
    color: #111827;
    font-weight: 700;
    font-size: 0.85rem;
    padding: 10px 12px;
    border-color: #d8a23b;
}

.report-table tbody td {
    padding: 10px 12px;
    font-size: 0.88rem;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
}

.report-table tfoot td {
    background-color: #f8fafc;
    font-weight: 700;
    padding: 10px 12px;
    border-top: 2px solid #e2e8f0;
}

/* 🔸 Print & PDF Template Styles */
.print-receipt-header,
.print-receipt-footer {
    display: none;
}

@media print {
    @page {
        size: A4 landscape;
        margin: 10mm;
    }

    /* Hide screen-only content */
    body * {
        visibility: hidden;
    }

    /* Show print container */
    #statementMainContainer,
    #statementMainContainer * {
        visibility: visible;
    }

    .screen-header-card,
    #screenActionsBar,
    .navbar,
    nav,
    header,
    .sidebar,
    .btn,
    .modal {
        display: none !important;
    }

    #statementMainContainer {
        position: absolute;
        left: 0;
        top: 0;
        width: 100% !important;
        max-width: 277mm !important;
        margin: 0 !important;
        padding: 0 !important;
        font-size: 8.8pt !important;
        color: #222 !important;
        background: transparent !important;
    }

    .print-receipt-header,
    .print-receipt-footer {
        display: block !important;
    }

    .report-section-card {
        border: 0 !important;
        box-shadow: none !important;
        margin-bottom: 14px !important;
        break-inside: auto !important;
        page-break-inside: auto !important;
    }

    .report-section-header {
        background: #f1f5f9 !important;
        border-bottom: 1px solid #dee2e6 !important;
        padding: 6px 10px !important;
        border-radius: 0 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .report-table {
        border-collapse: collapse !important;
        font-size: 7.8pt !important;
        margin-bottom: 8px !important;
        width: 100% !important;
    }

    .report-table th,
    .report-table td {
        border: 1px solid #dee2e6 !important;
        padding: 5px !important;
        vertical-align: top !important;
    }

    .report-table thead th {
        background: #E6B04A !important;
        color: #000000 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .report-table tfoot td {
        background: #f8fafc !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    tr {
        break-inside: avoid !important;
        page-break-inside: avoid !important;
    }
}

/* 🔸 Print Header & Meta Box Styles */
.print-receipt-header {
    border-bottom: 3px solid #E6B04A;
    margin-bottom: 16px;
    padding-bottom: 12px;
}

.print-brand-row {
    align-items: center;
    display: flex;
    justify-content: space-between;
    gap: 20px;
}

.print-logo {
    height: 60px;
    max-width: 180px;
    object-fit: contain;
}

.print-company-details {
    text-align: right;
    font-size: 8.5pt;
    line-height: 1.45;
}

.print-company-details h1 {
    color: #1e293b;
    font-size: 18pt;
    font-weight: 700;
    margin: 0 0 4px;
}

.print-receipt-meta {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin-top: 12px;
}

.print-meta-box {
    border: 1px solid #dee2e6;
    background-color: #fafafa;
    padding: 7px 10px;
    border-radius: 4px;
}

.print-meta-label {
    color: #64748b;
    display: block;
    font-size: 7.5pt;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
}

.print-meta-value {
    color: #0f172a;
    display: block;
    font-size: 9.5pt;
    font-weight: 700;
    margin-top: 2px;
}

.print-receipt-footer {
    border-top: 1px solid #dee2e6;
    clear: both;
    color: #64748b;
    font-size: 8pt;
    margin-top: 18px;
    padding-top: 9px;
    text-align: center;
}
</style>

<div class="container my-4" id="statementMainContainer" style="max-width: 1400px;">
    
    <!-- 🔸 Modern Color-Friendly Screen Header -->
    <div class="screen-header-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge" style="background-color: rgba(230, 176, 74, 0.2); color: #92400e; font-weight: 600; font-size: 0.8rem; border: 1px solid rgba(230, 176, 74, 0.4);">
                        Commission Report
                    </span>
                </div>
                <h2 class="statement-title">Driver Commission Statement</h2>
                <div class="d-flex align-items-center flex-wrap gap-2 mt-2">
                    <div class="meta-pill">
                        <i class="bi bi-person-circle text-primary me-2"></i>
                        <span><strong>Driver:</strong> {{ $driver['name'] ?? 'Unknown Driver' }} <span class="text-muted">({{ $driverId ?? '-' }})</span></span>
                    </div>
                    <div class="meta-pill">
                        <i class="bi bi-calendar3 text-warning me-2"></i>
                        <span><strong>Period:</strong> {{ \Carbon\Carbon::parse($from)->format('d-M-Y') }} &nbsp;&rarr;&nbsp; {{ \Carbon\Carbon::parse($to)->format('d-M-Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex align-items-center flex-wrap gap-2" id="screenActionsBar">
                <button type="button" class="btn btn-export-pdf shadow-sm d-inline-flex align-items-center gap-2" id="exportPdfBtn">
                    <i class="bi bi-file-earmark-pdf-fill"></i>
                    <span>Export as PDF</span>
                </button>
                <button type="button" class="btn btn-print-action shadow-sm d-inline-flex align-items-center gap-2" id="printBtn">
                    <i class="bi bi-printer-fill text-secondary"></i>
                    <span>Print</span>
                </button>
                <button type="button" class="btn btn-email-action shadow-sm d-inline-flex align-items-center gap-2" id="emailBtn" data-driver-id="{{ $driverId ?? '' }}">
                    <i class="bi bi-envelope-fill"></i>
                    <span>Email</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 🔸 Printable Header (Shown in Print & PDF Export) -->
    <div class="print-receipt-header">
        <div class="print-brand-row">
            <div>
                <img src="https://crowncarz.com/admin/public/images/logo.png" alt="Crown Carz Logo" class="print-logo" onerror="this.onerror=null; this.src='{{ asset('public/images/logo.png') }}';">
            </div>
            <div class="print-company-details">
                <h1>Crown Carz</h1>
                <div>52 Elvaston Way, Reading, RG30 4LU</div>
                <div>www.crowncarz.com | info@crowncarz.com</div>
            </div>
        </div>
        <div class="print-receipt-meta">
            <div class="print-meta-box">
                <span class="print-meta-label">Statement</span>
                <span class="print-meta-value">Driver Commission</span>
            </div>
            <div class="print-meta-box">
                <span class="print-meta-label">Driver</span>
                <span class="print-meta-value">{{ $driver['name'] ?? 'Unknown Driver' }} ({{ $driverId ?? '-' }})</span>
            </div>
            <div class="print-meta-box">
                <span class="print-meta-label">From</span>
                <span class="print-meta-value">{{ \Carbon\Carbon::parse($from)->format('d-M-Y') }}</span>
            </div>
            <div class="print-meta-box">
                <span class="print-meta-label">To</span>
                <span class="print-meta-value">{{ \Carbon\Carbon::parse($to)->format('d-M-Y') }}</span>
            </div>
        </div>
    </div>

    <!-- 🔸 Account Bookings Table -->
    <div class="report-section-card">
        <div class="report-section-header">
            <h5 class="report-section-title">
                <i class="bi bi-bank text-primary me-2"></i> Account Bookings
            </h5>
            <span class="badge bg-light text-dark border px-2 py-1">{{ count($account_bookings) }} Bookings</span>
        </div>
        <div class="table-responsive p-0">
            <table class="table table-hover report-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>BookingId</th>
                        <th>From</th>
                        <th>Via</th>
                        <th>To</th>
                        <th>WT/C</th>
                        <th>Income (£)</th>
                        <th>Extra</th>
                        <th>Park</th>
                        <th>Veh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($account_bookings as $b)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($b['date'])->format('d-M-Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($b['time'])->format('H:i') }}</td>
                        <td class="fw-semibold text-dark">{{ $b['booking_id'] ?? '-' }}</td>
                        <td>{{ $b['from'] ?? '-' }}</td>
                        <td>{{ $b['via'] ?? '-' }}</td>
                        <td>{{ $b['to'] ?? '-' }}</td>
                        <td>{{ $b['waiting_fee'] ?? '0' }}</td>
                        <td class="text-success fw-bold">£{{ number_format($b['fare'] ?? 0, 2) }}</td>
                        <td>{{ $b['extra'] ?? 0 }}</td>
                        <td>{{ number_format($b['parking'] ?? 0, 2) }}</td>
                        <td><span class="badge bg-light text-secondary border">{{ $b['vehicle'] ?? 'Saloon' }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">
                            No Account Bookings Found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($account_bookings))
                <tfoot>
                    <tr>
                        <td colspan="7" class="text-end fw-bold">Account Total</td>
                        <td class="text-success fw-bold">£{{ number_format($totals['account_fare'], 2) }}</td>
                        <td class="fw-bold">{{ $totals['extra'] ?? 0 }}</td>
                        <td class="fw-bold">{{ number_format($totals['parking'] ?? 0, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- 🔸 Cash Bookings Table -->
    <div class="report-section-card">
        <div class="report-section-header">
            <h5 class="report-section-title">
                <i class="bi bi-cash-stack text-success me-2"></i> Cash Bookings
            </h5>
            <span class="badge bg-light text-dark border px-2 py-1">{{ count($cash_bookings) }} Bookings</span>
        </div>
        <div class="table-responsive p-0">
            <table class="table table-hover report-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>BookingId</th>
                        <th>From</th>
                        <th>Via</th>
                        <th>To</th>
                        <th>WT/C</th>
                        <th>Income (£)</th>
                        <th>Extra</th>
                        <th>Veh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cash_bookings as $b)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($b['date'])->format('d-M-Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($b['time'])->format('H:i') }}</td>
                        <td class="fw-semibold text-dark">{{ $b['booking_id'] ?? '-' }}</td>
                        <td>{{ $b['from'] ?? '-' }}</td>
                        <td>{{ $b['via'] ?? '-' }}</td>
                        <td>{{ $b['to'] ?? '-' }}</td>
                        <td>{{ $b['waiting_fee'] ?? '0' }}</td>
                        <td class="text-success fw-bold">£{{ number_format($b['fare'] ?? 0, 2) }}</td>
                        <td>{{ $b['extra'] ?? 0 }}</td>
                        <td><span class="badge bg-light text-secondary border">{{ $b['vehicle'] ?? 'Estate' }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            No Cash Bookings Found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($cash_bookings))
                <tfoot>
                    <tr>
                        <td colspan="7" class="text-end fw-bold">Cash Total</td>
                        <td class="text-success fw-bold">£{{ number_format($totals['cash_fare'], 2) }}</td>
                        <td class="fw-bold">{{ $totals['extra_cash'] ?? 0 }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- 🔸 Totals Summary -->
    <div class="report-section-card">
        <div class="report-section-header">
            <h5 class="report-section-title">
                <i class="bi bi-calculator text-dark me-2"></i> Financial Summary
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6 border-end-md">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Total Work Amount:</span>
                        <strong class="text-dark">£{{ number_format($total_fare, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Driver Commission (20%):</span>
                        <strong class="text-danger">- £{{ number_format($commission, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Account Jobs Total:</span>
                        <strong class="text-dark">£{{ number_format($totals['account_fare'], 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Total Jobs Completed:</span>
                        <strong class="badge bg-light text-dark border">{{ number_format($total_jobs ?? (count($account_bookings) + count($cash_bookings))) }}</strong>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Cash With Driver:</span>
                        <strong class="text-dark">£{{ number_format($totals['cash_fare'], 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Parking:</span>
                        <strong class="text-dark">£{{ number_format($totals['parking'], 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Brought Forward:</span>
                        <strong class="text-dark">£{{ number_format($brought_forward, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 p-3 rounded-3" style="background-color: #ecfdf5; border: 1px solid #a7f3d0;">
                        <span class="fw-bold text-success" style="font-size: 1.1rem;">Driver Earning:</span>
                        <span class="fw-bold text-success" style="font-size: 1.35rem;">£{{ number_format($driver_earning, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 🔸 Printable Footer -->
    <div class="print-receipt-footer">
        Thank you for choosing Crown Carz. This is a computer generated driver commission statement.
    </div>

</div>

<!-- 🔸 Email Modal -->
<div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header rounded-top-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff;">
                <h5 class="modal-title fw-bold" id="emailModalLabel">
                    <i class="bi bi-envelope-paper me-2 text-warning"></i> Send Commission Report
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="emailForm">
                    <div class="mb-3">
                        <label for="driverEmail" class="form-label fw-semibold text-secondary">Driver Email Address</label>
                        <input type="email" class="form-control" id="driverEmail" name="email" readonly required>
                    </div>
                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-light px-3 me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning px-4 text-dark fw-semibold" id="sendEmailBtn" style="background-color: #E6B04A; border-color: #E6B04A;">
                            <i class="bi bi-send-fill me-1"></i> Send Statement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {

    // 🔸 Helper to generate filename: drivername_callsign_date_minutes
    function getStatementFilename() {
        const rawDriverName = @json($driver['name'] ?? 'Driver');
        const rawCallsign = @json((string)($driverId ?? ($driver['callsign'] ?? '0')));
        
        // Clean special characters
        const driverName = rawDriverName.trim().replace(/[^a-zA-Z0-9]/g, '_').replace(/_+/g, '_');
        const callsign = rawCallsign.trim().replace(/[^a-zA-Z0-9]/g, '_').replace(/_+/g, '_');
        
        const now = new Date();
        const pad = (n) => String(n).padStart(2, '0');
        const dateStr = `${pad(now.getDate())}-${pad(now.getMonth() + 1)}-${now.getFullYear()}`;
        const minutesStr = `${pad(now.getHours())}${pad(now.getMinutes())}`;
        
        return `${driverName}_${callsign}_${dateStr}_${minutesStr}.pdf`;
    }

    // 🔸 Export as PDF (Exact Print View Output)
    const exportPdfBtn = document.getElementById("exportPdfBtn");
    if (exportPdfBtn) {
        exportPdfBtn.addEventListener("click", async function() {
            const originalBtnHtml = exportPdfBtn.innerHTML;
            exportPdfBtn.disabled = true;
            exportPdfBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Generating PDF...';

            try {
                // Build a dedicated printable clone element with exact print styles
                const printContainer = document.createElement("div");
                printContainer.style.width = "1100px";
                printContainer.style.padding = "20px";
                printContainer.style.background = "#ffffff";
                printContainer.style.color = "#222222";
                printContainer.style.fontFamily = "Arial, sans-serif";
                printContainer.style.fontSize = "9pt";

                // Print header HTML
                const printHeaderHtml = `
                    <div style="border-bottom: 3px solid #E6B04A; margin-bottom: 16px; padding-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 20px;">
                            <div>
                                <img src="https://crowncarz.com/admin/public/images/logo.png" alt="Crown Carz Logo" style="height: 60px; max-width: 180px; object-fit: contain;">
                            </div>
                            <div style="text-align: right; font-size: 8.5pt; line-height: 1.45;">
                                <h1 style="color: #1e293b; font-size: 18pt; font-weight: 700; margin: 0 0 4px;">Crown Carz</h1>
                                <div>52 Elvaston Way, Reading, RG30 4LU</div>
                                <div>www.crowncarz.com | info@crowncarz.com</div>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-top: 12px;">
                            <div style="border: 1px solid #dee2e6; background: #fafafa; padding: 7px 10px; border-radius: 4px;">
                                <span style="color: #64748b; display: block; font-size: 7.5pt; font-weight: 700; text-transform: uppercase;">Statement</span>
                                <span style="color: #0f172a; display: block; font-size: 9.5pt; font-weight: 700; margin-top: 2px;">Driver Commission</span>
                            </div>
                            <div style="border: 1px solid #dee2e6; background: #fafafa; padding: 7px 10px; border-radius: 4px;">
                                <span style="color: #64748b; display: block; font-size: 7.5pt; font-weight: 700; text-transform: uppercase;">Driver</span>
                                <span style="color: #0f172a; display: block; font-size: 9.5pt; font-weight: 700; margin-top: 2px;">{{ $driver['name'] ?? 'Unknown Driver' }} ({{ $driverId ?? '-' }})</span>
                            </div>
                            <div style="border: 1px solid #dee2e6; background: #fafafa; padding: 7px 10px; border-radius: 4px;">
                                <span style="color: #64748b; display: block; font-size: 7.5pt; font-weight: 700; text-transform: uppercase;">From</span>
                                <span style="color: #0f172a; display: block; font-size: 9.5pt; font-weight: 700; margin-top: 2px;">{{ \Carbon\Carbon::parse($from)->format('d-M-Y') }}</span>
                            </div>
                            <div style="border: 1px solid #dee2e6; background: #fafafa; padding: 7px 10px; border-radius: 4px;">
                                <span style="color: #64748b; display: block; font-size: 7.5pt; font-weight: 700; text-transform: uppercase;">To</span>
                                <span style="color: #0f172a; display: block; font-size: 9.5pt; font-weight: 700; margin-top: 2px;">{{ \Carbon\Carbon::parse($to)->format('d-M-Y') }}</span>
                            </div>
                        </div>
                    </div>
                `;

                // Clone tables & summary
                const cards = document.querySelectorAll("#statementMainContainer .report-section-card");
                let sectionsHtml = "";
                cards.forEach(card => {
                    sectionsHtml += `<div style="margin-bottom: 14px;">` + card.innerHTML + `</div>`;
                });

                // Print footer HTML
                const printFooterHtml = `
                    <div style="border-top: 1px solid #dee2e6; color: #64748b; font-size: 8pt; margin-top: 16px; padding-top: 8px; text-align: center;">
                        Thank you for choosing Crown Carz. This is a computer generated driver commission statement.
                    </div>
                `;

                printContainer.innerHTML = printHeaderHtml + sectionsHtml + printFooterHtml;

                // Make sure internal headers & tables in the clone match print styling
                printContainer.querySelectorAll(".badge").forEach(b => {
                    b.style.border = "1px solid #dee2e6";
                });
                printContainer.querySelectorAll("table").forEach(t => {
                    t.style.width = "100%";
                    t.style.borderCollapse = "collapse";
                    t.style.fontSize = "7.8pt";
                });
                printContainer.querySelectorAll("th").forEach(th => {
                    th.style.background = "#E6B04A";
                    th.style.color = "#000000";
                    th.style.border = "1px solid #dee2e6";
                    th.style.padding = "5px";
                });
                printContainer.querySelectorAll("td").forEach(td => {
                    td.style.border = "1px solid #dee2e6";
                    td.style.padding = "5px";
                });

                const filename = getStatementFilename();

                const opt = {
                    margin: [6, 6, 6, 6],
                    filename: filename,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { 
                        scale: 2, 
                        useCORS: true, 
                        logging: false,
                        scrollY: 0
                    },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' },
                    pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
                };

                await html2pdf().set(opt).from(printContainer).save();

            } catch (error) {
                console.error("PDF Export Error:", error);
                // Fallback to direct download endpoint if needed
                window.location.href = `/reports/driver-commission/download?driver_id={{ $driverId ?? '' }}&from={{ $from }}&to={{ $to }}`;
            } finally {
                exportPdfBtn.disabled = false;
                exportPdfBtn.innerHTML = originalBtnHtml;
            }
        });
    }

    // 🔸 Print Button
    document.getElementById("printBtn").addEventListener("click", () => {
        window.print();
    });

    // 🔸 Email Button
    document.getElementById("emailBtn").addEventListener("click", async function() {
        const driverId = this.dataset.driverId;

        if (!driverId) {
            alert("Driver ID not found!");
            return;
        }

        try {
            const response = await fetch(`/get-driver-email/${driverId}`);
            const data = await response.json();

            if (data.email) {
                document.getElementById("driverEmail").value = data.email;
                new bootstrap.Modal(document.getElementById("emailModal")).show();
            } else {
                alert("Driver email not found!");
            }
        } catch (error) {
            console.error("Error fetching driver email:", error);
            alert("Failed to fetch driver email.");
        }
    });

    // 🔸 Send Email Form Submit
    document.getElementById("emailForm").addEventListener("submit", async function(e) {
        e.preventDefault();

        const sendBtn = document.getElementById("sendEmailBtn");
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';

        const email = document.getElementById("driverEmail").value;

        try {
            const response = await fetch(`/send-driver-commission-email`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    email: email,
                    from: "{{ $from }}",
                    to: "{{ $to }}",
                    driver_id: "{{ $driverId ?? '' }}"
                })
            });

            const result = await response.json();
            if (result.success) {
                alert("Statement emailed successfully!");
                bootstrap.Modal.getInstance(document.getElementById("emailModal")).hide();
            } else {
                alert("Failed to send email.");
            }
        } catch (error) {
            console.error("Error sending email:", error);
            alert("An error occurred while sending email.");
        } finally {
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Send Statement';
        }
    });

});
</script>

@endsection

