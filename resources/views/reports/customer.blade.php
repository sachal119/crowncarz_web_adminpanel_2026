@extends('layouts.app')

@section('content')
<style>
/* =========================================================
   👑 CROWN CARZ - LUXURY INVOICE & STATEMENT STYLES
   ========================================================= */
.invoice-sheet {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    padding: 32px 36px;
    margin-bottom: 40px;
    border: 1px solid #e2e8f0;
}

.invoice-brand h1 {
    font-size: 24px;
    font-weight: 800;
    color: #1e293b;
    margin: 0 0 4px;
    letter-spacing: -0.5px;
}

.invoice-brand-subtitle {
    font-size: 12px;
    color: #64748b;
    line-height: 1.5;
}

.invoice-title-badge {
    display: inline-block;
    background: linear-gradient(135deg, #1e293b, #0f172a);
    color: #f1c40f;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    padding: 6px 16px;
    border-radius: 6px;
    border-left: 3px solid #E6B04A;
}

.gold-accent-line {
    height: 3px;
    background: linear-gradient(90deg, #E6B04A, #1e293b, #E6B04A);
    margin: 18px 0 22px;
    border-radius: 2px;
}

.invoice-card-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 14px 18px;
    height: 100%;
}

.invoice-card-title {
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 6px;
}

.invoice-card-name {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.3;
}

.invoice-card-meta {
    font-size: 12px;
    color: #475569;
    margin-top: 4px;
}

.invoice-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 16px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
}

.invoice-table thead th {
    background-color: #1e293b;
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 10px 8px;
    border-bottom: 2px solid #E6B04A;
    vertical-align: middle;
}

.invoice-table tbody tr {
    background-color: #ffffff;
    transition: background-color 0.15s ease;
}

.invoice-table tbody tr:nth-child(even) {
    background-color: #f8fafc;
}

.invoice-table tbody td {
    padding: 8px 8px;
    font-size: 11px;
    color: #334155;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: top;
    line-height: 1.35;
}

.ref-pill {
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
    font-weight: 700;
    color: #0f172a;
    font-size: 11px;
}

.address-text {
    font-size: 10.5px;
    color: #1e293b;
    line-height: 1.3;
}

.veh-badge {
    display: inline-block;
    background: #e2e8f0;
    color: #334155;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 4px;
}

.amount-text {
    font-weight: 700;
    color: #0f172a;
}

.bottom-section {
    margin-top: 24px;
    page-break-inside: avoid;
}

.bank-details-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-left: 4px solid #1e293b;
    border-radius: 8px;
    padding: 14px 18px;
}

.bank-details-title {
    font-size: 12px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.bank-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px 16px;
    font-size: 11.5px;
}

.bank-item-label {
    color: #64748b;
    font-size: 10.5px;
}

.bank-item-val {
    font-weight: 700;
    color: #0f172a;
}

.totals-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
}

.totals-table {
    width: 100%;
    margin-bottom: 0;
}

.totals-table td {
    padding: 8px 14px;
    font-size: 12px;
    border-bottom: 1px solid #e2e8f0;
}

.totals-table tr.grand-total-row td {
    background: #1e293b;
    color: #ffffff;
    font-size: 14px;
    font-weight: 800;
    border-bottom: none;
}

.totals-table tr.grand-total-row .total-amount {
    color: #f1c40f;
    font-size: 16px;
}

.invoice-footer {
    border-top: 1px solid #e2e8f0;
    padding-top: 14px;
    margin-top: 24px;
    font-size: 11px;
    color: #64748b;
    text-align: center;
    page-break-inside: avoid;
}

/* =========================================================
   🖨️ HIGH-PERFORMANCE PRINT CSS (A4 PERFECT FIT)
   ========================================================= */
@media print {
    @page {
        size: A4 portrait;
        margin: 10mm 12mm;
    }

    body {
        background: #ffffff !important;
        font-size: 9.5pt !important;
        color: #000000 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    body * {
        visibility: hidden;
    }

    #printableInvoiceArea,
    #printableInvoiceArea * {
        visibility: visible;
    }

    #printableInvoiceArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        border: none !important;
        background: transparent !important;
    }

    .no-print,
    .navbar,
    .sidebar,
    header,
    footer,
    .modal,
    .btn {
        display: none !important;
    }

    .invoice-table {
        border: 1px solid #cbd5e1 !important;
        font-size: 8pt !important;
        page-break-inside: auto;
    }

    .invoice-table thead {
        display: table-header-group !important;
    }

    .invoice-table thead th {
        background-color: #1e293b !important;
        color: #ffffff !important;
        border-bottom: 2px solid #E6B04A !important;
        padding: 6px 6px !important;
        font-size: 7.5pt !important;
    }

    .invoice-table tbody tr {
        page-break-inside: avoid !important;
        page-break-after: auto !important;
    }

    .invoice-table tbody td {
        padding: 4px 6px !important;
        border-bottom: 1px solid #e2e8f0 !important;
        font-size: 7.5pt !important;
        line-height: 1.25 !important;
    }

    .bottom-section,
    .bank-details-card,
    .totals-card,
    .invoice-footer {
        page-break-inside: avoid !important;
    }

    .totals-table tr.grand-total-row td {
        background-color: #1e293b !important;
        color: #ffffff !important;
    }

    .totals-table tr.grand-total-row .total-amount {
        color: #f1c40f !important;
    }
}
</style>

<div class="container-fluid px-3 px-md-4 py-3">

    <!-- Top Action Toolbar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 no-print gap-2">
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Reports
        </a>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-dark shadow-sm px-3" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print Invoice
            </button>
            <a href="{{ route('reports.customer.download', ['from' => $from, 'to' => $to, 'customer_type' => $type, 'customer_id' => $customerId, 'booking_status' => $status ?? 'completed']) }}" class="btn btn-danger shadow-sm px-3 text-white">
                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
            </a>
            <button class="btn btn-warning shadow-sm px-3 text-dark fw-semibold" id="emailReport" data-customer-email="{{ $selectedCustomerEmail ?? '' }}">
                <i class="bi bi-envelope me-1"></i> Email Invoice
            </button>
            <button class="btn btn-success shadow-sm px-3 fw-semibold text-white" id="whatsappCustomerReport" style="background-color: #128C7E; border-color: #128C7E;">
                <i class="bi bi-whatsapp me-1"></i> WhatsApp Invoice
            </button>
        </div>
    </div>

    <!-- Printable Invoice Sheet -->
    <div class="invoice-sheet" id="printableInvoiceArea">
        
        <!-- Header Row -->
        <div class="row align-items-center">
            <div class="col-sm-7 col-12 d-flex align-items-center gap-3">
                <img src="https://crowncarz.com/admin/public/images/logo.png" alt="Crown Carz" style="height: 56px; max-width: 140px; object-fit: contain;">
                <div class="invoice-brand">
                    <h1>Crown Carz Ltd</h1>
                    <div class="invoice-brand-subtitle">
                        52 Elvaston Way, Reading, RG30 4LU<br>
                        <span>www.crowncarz.com</span> &nbsp;|&nbsp; <span>info@crowncarz.com</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-5 col-12 text-sm-end mt-3 mt-sm-0">
                <div class="invoice-title-badge mb-2">Customer Invoice / Statement</div>
                <div class="text-muted small"><strong>DATE:</strong> {{ \Carbon\Carbon::now()->format('d M Y') }}</div>
                <div class="text-muted small"><strong>PERIOD:</strong> {{ \Carbon\Carbon::parse($from)->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</div>
            </div>
        </div>

        <div class="gold-accent-line"></div>

        <!-- Meta Summary Boxes -->
        <div class="row g-3 mb-2">
            <div class="col-md-6 col-12">
                <div class="invoice-card-box">
                    <div class="invoice-card-title"><i class="bi bi-person-badge me-1"></i> Billed To (Client / Account)</div>
                    <div class="invoice-card-name">
                        {{ !empty($selectedCustomerName) ? $selectedCustomerName : 'All Account Customers' }}
                    </div>
                    <div class="invoice-card-meta">
                        @if(!empty($selectedCustomerPhone))
                            <div><i class="bi bi-telephone text-muted me-1"></i> {{ $selectedCustomerPhone }}</div>
                        @endif
                        @if(!empty($selectedCustomerEmail))
                            <div><i class="bi bi-envelope text-muted me-1"></i> {{ $selectedCustomerEmail }}</div>
                        @endif
                        @if(!empty($type))
                            <div class="mt-1"><span class="badge bg-secondary" style="font-size: 9.5px;">Payment Type: {{ strtoupper($type) }}</span></div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="invoice-card-box">
                    <div class="invoice-card-title"><i class="bi bi-receipt me-1"></i> Statement Summary</div>
                    <div class="d-flex justify-content-between py-1 border-bottom border-light">
                        <span class="text-muted small">Billing Duration:</span>
                        <strong class="text-dark small">{{ \Carbon\Carbon::parse($from)->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom border-light">
                        <span class="text-muted small">Booking Status:</span>
                        <span class="badge bg-dark text-warning" style="font-size: 9.5px;">{{ strtoupper(str_replace('_', ' ', $status ?? 'completed')) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted small">Total Completed Jobs:</span>
                        <strong class="text-primary small">{{ count($customers) }} Booking(s)</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookings Data Table -->
        <div class="table-responsive">
            <table class="table invoice-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 9%;">Ref#</th>
                        <th style="width: 12%;">Date &amp; Time</th>
                        <th style="width: 21%;">Pickup Location</th>
                        <th style="width: 7%;">Via</th>
                        <th style="width: 21%;">Dropoff Location</th>
                        <th style="width: 8%;">Vehicle</th>
                        <th style="width: 7%;" class="text-end">Fare</th>
                        <th style="width: 6%;" class="text-end">Parking</th>
                        <th style="width: 9%;">Comments</th>
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
                                : '';
                        @endphp
                        <tr>
                            <td>
                                <span class="ref-pill">{{ $booking->ref_no ?? ($booking->id ?? '-') }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark" style="white-space: nowrap;">
                                    {{ !empty($booking->pickup_time) ? \Carbon\Carbon::parse($booking->pickup_time)->format('d M Y') : '-' }}
                                </div>
                                <div class="text-muted" style="font-size: 10px; white-space: nowrap;">
                                    {{ !empty($booking->pickup_time) ? \Carbon\Carbon::parse($booking->pickup_time)->format('h:i A') : '' }}
                                </div>
                            </td>
                            <td>
                                <div class="address-text">{{ $booking->pickup_address ?? '-' }}</div>
                            </td>
                            <td>
                                @if(!empty($viaList))
                                    <span class="badge bg-light text-dark border" style="font-size: 9.5px;" title="{{ $viaList }}">Via</span>
                                @else
                                    <span class="text-muted">&ndash;</span>
                                @endif
                            </td>
                            <td>
                                <div class="address-text">{{ $booking->dropoff_address ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="veh-badge">{{ $booking->vehicle_make ?? ($booking->vehicle_type ?? 'Saloon') }}</span>
                            </td>
                            <td class="text-end amount-text">
                                &pound;{{ number_format($fare, 2) }}
                            </td>
                            <td class="text-end text-muted">
                                &pound;{{ number_format($parking, 2) }}
                            </td>
                            <td>
                                <div class="text-muted" style="font-size: 10px; line-height: 1.25;">
                                    {{ !empty($booking->comments) && $booking->comments !== 'N/A' ? $booking->comments : ($booking->passenger_name ?? '&ndash;') }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                No records found for the selected period and customer filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Bottom Totals & Remittance Section -->
        <div class="row bottom-section g-3 align-items-start">
            <!-- Bank & Remittance Advice -->
            <div class="col-md-7 col-12">
                <div class="bank-details-card">
                    <div class="bank-details-title">
                        <i class="bi bi-bank2 text-primary"></i> Bank Transfer &amp; Payment Details
                    </div>
                    <div class="bank-grid mb-2">
                        <div>
                            <span class="bank-item-label">Bank Name:</span>
                            <div class="bank-item-val">HSBC Bank UK</div>
                        </div>
                        <div>
                            <span class="bank-item-label">Account Name:</span>
                            <div class="bank-item-val">Crown Carz Ltd</div>
                        </div>
                        <div>
                            <span class="bank-item-label">Sort Code:</span>
                            <div class="bank-item-val font-monospace">40-38-04</div>
                        </div>
                        <div>
                            <span class="bank-item-label">Account Number:</span>
                            <div class="bank-item-val font-monospace">85304792</div>
                        </div>
                    </div>
                    
                    @php
                        $sysSettings = app('App\Services\FirebaseService')->getData('system_settings') ?? [];
                        $customReceiptNote = $sysSettings['receipt_note'] ?? null;
                    @endphp
                    @if(!empty($customReceiptNote))
                        <div class="small text-muted border-top border-light pt-2 mt-2" style="font-size: 10.5px; line-height: 1.35;">
                            {!! nl2br(e($customReceiptNote)) !!}
                        </div>
                    @else
                        <div class="small text-muted border-top border-light pt-2 mt-2" style="font-size: 10px; line-height: 1.35;">
                            Please arrange payment of the outstanding invoice amount using the bank details above. Kindly use your account name or invoice reference as the payment reference.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Financial Totals Summary -->
            <div class="col-md-5 col-12">
                <div class="totals-card shadow-sm">
                    <table class="table totals-table">
                        <tbody>
                            <tr>
                                <td class="text-muted">Total Base Fare:</td>
                                <td class="text-end fw-semibold text-dark">&pound;{{ number_format($totalFare, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Total Parking / Tolls:</td>
                                <td class="text-end fw-semibold text-dark">&pound;{{ number_format($totalParking, 2) }}</td>
                            </tr>
                            <tr class="grand-total-row">
                                <td>TOTAL AMOUNT DUE:</td>
                                <td class="text-end total-amount">&pound;{{ number_format($totalFare + $totalParking, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Clean One-Line Footer -->
        <div class="invoice-footer">
            Thank you for choosing <strong>Crown Carz Ltd</strong>. | Registered in England &amp; Wales | Computer Generated Invoice &amp; Statement
        </div>

    </div>

</div>

<!-- Email Modal -->
<div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header bg-warning bg-opacity-25">
        <h5 class="modal-title fw-semibold text-dark" id="emailModalLabel">Send Report via Email</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="sendEmailForm">
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label fw-semibold">Recipient Email:</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>

    <!-- Existing -->
    <input type="hidden" name="from" value="{{ $from }}">
    <input type="hidden" name="to" value="{{ $to }}">
    <input type="hidden" name="booking_status" value="{{ $status ?? 'completed' }}">

    <!-- 🔥 ADD THESE -->
    <input type="hidden" name="customer_id" value="{{ $customerId }}">
    <input type="hidden" name="customer_type" value="{{ $type }}">

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-warning text-dark fw-semibold">Send</button>
    </div>
</form>
      </div>
    </div>
  </div>
</div>

<!-- WhatsApp Modal -->
<div class="modal fade" id="customerWhatsAppModal" tabindex="-1" aria-labelledby="customerWhatsAppModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header bg-success bg-opacity-25" style="background-color: #e8f5e9;">
        <h5 class="modal-title fw-semibold text-dark" id="customerWhatsAppModalLabel">
            <i class="bi bi-whatsapp text-success me-1"></i> Send Customer Report via WhatsApp
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form id="sendCustomerWhatsAppForm">
            @csrf

            <div class="mb-3">
                <label for="customerWhatsAppPhone" class="form-label fw-semibold">Recipient WhatsApp Number:</label>
                <input type="text" class="form-control" id="customerWhatsAppPhone" name="phone" value="{{ $selectedCustomerPhone ?? '' }}" placeholder="e.g. 07123456789 or 447123456789" required>
                <div class="form-text text-muted">The complete Customer Statement will be generated as PDF and delivered on WhatsApp.</div>
            </div>

            <input type="hidden" name="from" value="{{ $from }}">
            <input type="hidden" name="to" value="{{ $to }}">
            <input type="hidden" name="customer_id" value="{{ $customerId }}">
            <input type="hidden" name="customer_type" value="{{ $type }}">
            <input type="hidden" name="booking_status" value="{{ $status ?? 'completed' }}">

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success fw-semibold" id="btnSendCustomerWhatsApp" style="background-color: #128C7E; border-color: #128C7E;">
                    <i class="bi bi-whatsapp me-1"></i> Send PDF via WhatsApp
                </button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('emailReport').addEventListener('click', function() {
    var modal = new bootstrap.Modal(document.getElementById('emailModal'));
    modal.show();
});

document.getElementById('sendEmailForm').addEventListener('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    fetch("{{ route('send.customer.report') }}", {
        method: "POST",
        body: formData,
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            alert('📩 Report sent successfully to ' + formData.get('email'));
            bootstrap.Modal.getInstance(document.getElementById('emailModal')).hide();
        } else {
            alert('❌ Failed to send email. Please try again.');
        }
    })
    .catch(() => alert('⚠️ Something went wrong!'));
});

// WhatsApp Customer Report
const waCustBtn = document.getElementById('whatsappCustomerReport');
const waCustModalEl = document.getElementById('customerWhatsAppModal');
const waCustForm = document.getElementById('sendCustomerWhatsAppForm');
const waCustSubmitBtn = document.getElementById('btnSendCustomerWhatsApp');

if (waCustBtn && waCustModalEl) {
    waCustBtn.addEventListener('click', function () {
        const modal = new bootstrap.Modal(waCustModalEl);
        modal.show();
    });
}

if (waCustForm) {
    waCustForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        if (waCustSubmitBtn) {
            waCustSubmitBtn.disabled = true;
            waCustSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending PDF...';
        }

        const formData = new FormData(this);

        try {
            const response = await fetch("{{ route('send.customer.report.whatsapp') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok && (data.success || data.status === 'success')) {
                alert('✅ ' + (data.message || 'Customer Report PDF sent successfully on WhatsApp!'));
                const modalInstance = bootstrap.Modal.getInstance(waCustModalEl);
                if (modalInstance) modalInstance.hide();
            } else {
                alert('❌ ' + (data.message || 'Failed to send WhatsApp report.'));
            }
        } catch (err) {
            console.error(err);
            alert('⚠️ Network error while sending WhatsApp report.');
        } finally {
            if (waCustSubmitBtn) {
                waCustSubmitBtn.disabled = false;
                waCustSubmitBtn.innerHTML = '<i class="bi bi-whatsapp me-1"></i> Send PDF via WhatsApp';
            }
        }
    });
}
</script>

@endsection
