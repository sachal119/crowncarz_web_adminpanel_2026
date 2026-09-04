@extends('layouts.app')

@section('content')
<style>
.print-receipt-header,
.print-receipt-footer {
    display: none;
}

@media print {
    @page {
        size: A4;
        margin: 12mm;
    }

    /* Hide everything first */
    body * {
        visibility: hidden;
    }

    /* Show only the statement container */
    .container,
    .container * {
        visibility: visible;
    }

    /* Remove buttons */
    #printBtn,
    #emailBtn,
    .modal,
    .btn,
    nav,
    header,
    .navbar,
    .sidebar {
        display: none !important;
    }

    /* Remove margins & center properly */
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
        grid-template-columns: repeat(3, 1fr);
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

    .text-center.mb-5,
    .text-center.mb-4 {
        display: none !important;
    }

    .card {
        border: 0 !important;
        box-shadow: none !important;
    }

    .card-header {
        border-radius: 0 !important;
        padding: 8px 12px !important;
    }

    .card-body {
        padding: 0 !important;
    }

    .table {
        border-collapse: collapse !important;
        font-size: 8.5pt;
        margin-bottom: 14px !important;
        width: 100% !important;
    }

    .table th,
    .table td {
        border: 1px solid #dee2e6 !important;
        padding: 6px !important;
        vertical-align: top !important;
    }

    .table-responsive {
        overflow: visible !important;
    }

    /* Optional: improve print colors */
    thead {
        background: #E6B04A !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .summary-box {
        float: none !important;
        margin-left: auto !important;
        margin-top: 12px !important;
        width: 260px !important;
    }

    .summary-box table {
        border-collapse: collapse;
        width: 100%;
    }

    .summary-box td {
        border: 1px solid #dee2e6;
        padding: 7px 10px;
    }

    .print-receipt-footer {
        border-top: 1px solid #dee2e6;
        clear: both;
        color: #6c757d;
        font-size: 8.5pt;
        margin-top: 22px;
        padding-top: 10px;
        text-align: center;
    }
}
</style>
<style>
    .header {
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 3px solid #343a40;
        display: flex;
        justify-content: space-between;
    }
    .summary-box {
        width: 300px;
        float: right;
        margin-top: 10px;
    }
    .summary-box tr td:first-child {
        font-weight: bold;
        background-color: #f8f9fa;
    }
    .total-row td {
        background-color: #343a40 !important;
        color: white;
        font-weight: bold;
        font-size: 11pt;
    }
    .action-buttons {
        text-align: right;
        margin-bottom: 20px;
    }
    .action-buttons button {
        margin-left: 10px;
        font-weight: 600;
    }
</style>

<div class="container mt-4">

    <div class="print-receipt-header">
        <div class="print-brand-row">
            <div>
                <img src="https://crowncarz.com/admin/public/images/logo.png" alt="Company Logo" class="print-logo">
            </div>
            <div class="print-company-details">
                <h1>Crown Carz Ltd</h1>
                <div>52 Elvaston Way, Reading, RG30 4LU</div>
                <div>www.crowncarz.com | info@crowncarz.com</div>
            </div>
        </div>
        <div class="print-receipt-meta">
            <div class="print-meta-box">
                <span class="print-meta-label">Receipt</span>
                <span class="print-meta-value">{{ $selectedCustomerName ?? 'Customer' }} / {{ $selectedCustomerPhone ?? 'N/A' }}</span>
            </div>
            <div class="print-meta-box">
                <span class="print-meta-label">Invoice Date</span>
                <span class="print-meta-value">{{ \Carbon\Carbon::now()->format('d M Y') }}</span>
            </div>
            <div class="print-meta-box">
                <span class="print-meta-label">Duration</span>
                <span class="print-meta-value">
    {{ \Carbon\Carbon::parse($from)->format('d M Y') }} -
    {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
</span>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <button class="btn btn-dark" onclick="window.print()">
            🖨️ Print
        </button>
        
        <button class="btn btn-warning text-dark" id="emailReport" data-customer-email="{{ $customers->email ?? '' }}">
    ✉️ Email Report
</button>

        <!--<button class="btn btn-warning text-dark" id="emailReport">-->
        <!--    ✉️ Email Report-->
        <!--</button>-->
    </div>

    <!-- Report Header -->
    <div class="text-center mb-5">
        <h2 class="fw-semibold text-secondary">Customer Report</h2>
        <p class="mb-1"><strong>INVOICE DATE:</strong> {{ \Carbon\Carbon::now()->format('d M Y') }}</p>
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

    <!-- Bookings Table -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-warning bg-opacity-25 rounded-top-4">
            <h5 class="mb-0 text-dark fw-semibold text-center">Bookings</h5>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead style="background: #E6B04A; color: #fff;">
                        <tr>
                            <th style="width: 5%;">REF</th>
                            <th style="width: 12%;">DATE/TIME</th>
                            <th style="width: 22.5%;">PICK UP</th>
                            <th style="width: 5%">VIA</th>
                            <th style="width: 22.5%;">DROP OFF</th>
                            <th style="width: 8%;">Veh Type</th>
                            <th style="width: 7.5%;">FARE (£)</th>
                            <th style="width: 7.5%;">PARKING (£)</th>
                            <th style="width: 15%;">COMMENTS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalFare = 0;
                            $totalParking = 0;
                        @endphp
                        @forelse($customers as $booking)
                            @php
                                $fare = $booking->price ?? 0.00;
                                $parking = $booking->parking ?? 0.00;
                                $comments = $booking->ref_no ?? 'N/A';
                                $totalFare += $fare;
                                $totalParking += $parking;
                            @endphp
                            <tr>
                                <td>{{ $booking->ref_no ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($booking->pickup_time)->format('d-M-Y h:i A') }}</td>
                                <td>{{ $booking->pickup_address ?? 'N/A' }}</td>
                                <td>
                                    {{ !empty($booking->via_addresses) ? implode(', ', $booking->via_addresses) : '-' }}
                                </td>
                                <td>{{ $booking->dropoff_address ?? 'N/A' }}</td>
                                <td>{{ $booking->vehicle_make ?? 'N/A' }}</td>
                                <td style="text-align: right;">{{ number_format($fare, 2) }}</td>
                                <td style="text-align: right;">{{ number_format($parking, 2) }}</td>
                                <td>{{ $booking->comments ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: #6c757d;">No records found for the selected period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            <div class="summary-box">
                <table>
                    <tbody>
                        <tr>
                            <td>PRICE</td>
                            <td style="text-align: right;">£ {{ number_format($totalFare, 2) }}</td>
                        </tr>
                        <tr>
                            <td>PARKING</td>
                            <td style="text-align: right;">£ {{ number_format($totalParking, 2) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td>TOTAL</td>
                            <td style="text-align: right;">£ {{ number_format($totalFare + $totalParking, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="print-receipt-footer">
        Thank you for choosing Crown Carz. This is a computer generated receipt.
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
</script>

@endsection
