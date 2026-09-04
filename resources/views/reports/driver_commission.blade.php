@extends('layouts.app')

@section('content')
<style>
.print-receipt-header,
.print-receipt-footer {
    display: none;
}

@media print {
    @page {
        size: A4 landscape;
        margin: 10mm;
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
        max-width: 277mm;
        margin: 0 auto;
        padding: 0;
        font-size: 9pt;
        color: #222;
    }

    .mt-5 {
        margin-top: 0 !important;
    }

    .print-receipt-header,
    .print-receipt-footer {
        display: block !important;
    }

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
        height: 66px;
        max-width: 170px;
        object-fit: contain;
    }

    .print-company-details {
        text-align: right;
        font-size: 8.5pt;
        line-height: 1.45;
    }

    .print-company-details h1 {
        color: #343a40;
        font-size: 20pt;
        font-weight: 700;
        margin: 0 0 5px;
    }

    .print-receipt-meta {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        margin-top: 12px;
    }

    .print-meta-box {
        border: 1px solid #dee2e6;
        padding: 7px 9px;
    }

    .print-meta-label {
        color: #6c757d;
        display: block;
        font-size: 7.5pt;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .print-meta-value {
        color: #212529;
        display: block;
        font-size: 9.5pt;
        font-weight: 700;
        margin-top: 2px;
    }

    .text-center.mb-5 {
        display: none !important;
    }

    .card {
        border: 0 !important;
        box-shadow: none !important;
        margin-bottom: 14px !important;
        break-inside: auto !important;
        page-break-inside: auto !important;
    }

    .card-header,
    thead {
        break-after: avoid;
        page-break-after: avoid;
    }

    tr {
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .card-header {
        border-radius: 0 !important;
        padding: 7px 10px !important;
    }

    .card-body {
        padding: 0 !important;
    }

    .table {
        border-collapse: collapse !important;
        font-size: 7.8pt;
        margin-bottom: 10px !important;
        width: 100% !important;
    }

    .table th,
    .table td {
        border: 1px solid #dee2e6 !important;
        padding: 5px !important;
        vertical-align: top !important;
    }

    .table-responsive {
        overflow: visible !important;
    }

    tfoot td {
        font-weight: 700 !important;
    }

    /* Optional: improve print colors */
    thead {
        background: #E6B04A !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .print-receipt-footer {
        border-top: 1px solid #dee2e6;
        clear: both;
        color: #6c757d;
        font-size: 8pt;
        margin-top: 18px;
        padding-top: 9px;
        text-align: center;
    }
}
</style>

<div class="container mt-5" style="max-width:1490px">
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

    <div class="d-flex justify-content-end mb-3">
    <button class="btn btn-sm btn-outline-warning me-2" id="printBtn">
        <i class="bi bi-printer"></i> Print
    </button>
    <button class="btn btn-sm btn-warning text-white" id="emailBtn" data-driver-id="{{ $driverId ?? '' }}">
        <i class="bi bi-envelope"></i> Email
    </button>
   <script>
    console.log("Driver ID:", "{{ $driverId ?? 'null' }}");
</script>
</div>

    <!-- Page Header -->
    <div class="text-center mb-5">
        <!--<h2 class="fw-bold text-uppercase text-dark">-->
        <!--    <span class="text-warning">CrownCarz</span>-->
        <!--</h2>-->
        <h2 class="fw-semibold text-warning">Driver Commission Statement</h2>
        <p class="mb-0">
            <strong>FROM:</strong> {{ \Carbon\Carbon::parse($from)->format('d-M-Y') }} &nbsp;&nbsp;
<strong>TO:</strong> {{ \Carbon\Carbon::parse($to)->format('d-M-Y') }}

        </p>
        <!--<p class="text-muted mb-1">-->
        <!--   Fahad Khan - 1-->
        <!--</p>-->
        <p class="text-muted mb-1">
            {{ $driver['name'] ?? 'Unknown Driver' }}
            ({{ $driverId ?? '-' }})
        </p>
        

        <hr class="mt-3" style="border-color: #E6B04A; opacity: 1; width: 60%; margin: 0 auto;">
    </div>

    <!-- Account Bookings -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-warning bg-opacity-25 rounded-top-4">
        <h5 class="mb-0 text-dark fw-semibold">Account Bookings</h5>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead style="background: #E6B04A; color: #fff;">
                    <tr>
                        <!--<th>Created at</th>-->
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
        <!--<td>{{ \Carbon\Carbon::parse($b['created_at'])->format('d-M-Y') }}</td>-->
        <td>{{ \Carbon\Carbon::parse($b['date'])->format('d-M-Y') }}</td>
        <td>{{ \Carbon\Carbon::parse($b['time'])->format('H:i') }}</td>
        <td>{{ $b['booking_id'] ?? '-' }}</td>
        <td>{{ $b['from'] ?? '-' }}</td>
        <td>{{ $b['via'] ?? '-' }}</td>
        <td>{{ $b['to'] ?? '-' }}</td>
        <td>{{ $b['waiting_fee'] ?? '0' }}</td>
        <td class="text-success fw-semibold">£{{ number_format($b['fare'] ?? 0, 2) }}</td>
        <td>{{ $b['extra'] ?? 0 }}</td>
        <td>{{ number_format($b['parking'] ?? 0, 2) }}</td>
        <td>{{ $b['vehicle'] ?? 'Saloon' }}</td>
    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center text-muted py-3">
                            No Account Bookings
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($account_bookings))
                <tfoot>
                    <tr>
                        <td colspan="7" class="text-end fw-bold">Total</td>
                        <td class="text-success fw-bold">£{{ number_format($totals['account_fare'], 2) }}</td>
                        <td>{{ $totals['extra'] ?? 0 }}</td>
                        <td>{{ $totals['parking'] ?? 0 }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- Cash Bookings -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-warning bg-opacity-25 rounded-top-4">
        <h5 class="mb-0 text-dark fw-semibold">Cash Bookings</h5>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead style="background: #E6B04A; color: #fff;">
                    <tr>
                        <!--<th>Created at</th>-->
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
                        <!--<td>{{ \Carbon\Carbon::parse($b['created_at'])->format('d/m/y') }}</td>-->
        <td>{{ \Carbon\Carbon::parse($b['date'])->format('d-M-Y') }}</td>
        <td>{{ \Carbon\Carbon::parse($b['time'])->format('H:i') }}</td>
        <td>{{ $b['booking_id'] ?? '-' }}</td>
        <td>{{ $b['from'] ?? '-' }}</td>
        <td>{{ $b['via'] ?? '-' }}</td>
        <td>{{ $b['to'] ?? '-' }}</td>
        <td>{{ $b['waiting_fee'] ?? '0' }}</td>
        <td class="text-success fw-semibold">£{{ number_format($b['fare'] ?? 0, 2) }}</td>
        <td>{{ $b['extra'] ?? 0 }}</td>
        <td>{{ $b['vehicle'] ?? 'Estae' }}</td>
    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-3">
                            No Cash Bookings
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($cash_bookings))
                <tfoot>
                    <tr>
                        <td colspan="7" class="text-end fw-bold">Total</td>
                        <td class="text-success fw-bold">£{{ number_format($totals['cash_fare'], 2) }}</td>
                        <td>{{ $totals['extra_cash'] ?? 0 }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>


    <!-- Totals Summary -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-3">Summary</h5>
            <div class="row">
                <div class="col-md-6">
                   <p><strong>Total Work Amount:</strong>
            £{{ number_format($total_fare, 2) }}
        </p>

        <p><strong>Driver Commission (20%):</strong>
            £{{ number_format($commission, 2) }}
        </p>

        <p><strong>Account Jobs Total:</strong>
            £{{ number_format($totals['account_fare'], 2) }}
        </p>
        
        <p><strong>Jobs Total:</strong>
            {{ number_format($total_jobs ?? 0) }}
        </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Cash With Driver:</strong>
            £{{ number_format($totals['cash_fare'], 2) }}
        </p>

        <p><strong>Parking:</strong>
            £{{ number_format($totals['parking'], 2) }}
        </p>

        <p><strong>Brought Forward:</strong>
            £{{ number_format($brought_forward, 2) }}
        </p>

        <hr>

        <h4 class="text-success">
            Driver Earning:
            £{{ number_format($driver_earning, 2) }}
        </h4>
                </div>
            </div>
        </div>
    </div>

    <div class="print-receipt-footer">
        Thank you for choosing Crown Carz. This is a computer generated driver commission statement.
    </div>
</div>
<!-- Email Modal -->
<div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content rounded-4">
      <div class="modal-header bg-warning text-white rounded-top-4">
        <h5 class="modal-title" id="emailModalLabel">Send Commission Report</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="emailForm">
          <div class="mb-3">
            <label for="driverEmail" class="form-label">Driver Email</label>
            <input type="email" class="form-control" id="driverEmail" name="email" readonly required>
          </div>
          <div class="text-end">
            <button type="submit" class="btn btn-warning text-white px-4" id="sendEmailBtn">
              <i class="bi bi-send"></i> Send
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {

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
            // Fetch driver email from Firebase
            const response = await fetch(`/admin/get-driver-email/${driverId}`);
            const data = await response.json();

            if (data.email) {
                document.getElementById("driverEmail").value = data.email;
                new bootstrap.Modal(document.getElementById("emailModal")).show();
            } else {
                alert("Driver email not found in Firebase!");
            }
        } catch (error) {
            console.error("Error fetching driver email:", error);
        }
    });

    // 🔸 Send Email
    document.getElementById("emailForm").addEventListener("submit", async function(e) {
        e.preventDefault();

        const sendBtn = document.getElementById("sendEmailBtn");
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Sending...';

        const email = document.getElementById("driverEmail").value;

        try {
            const response = await fetch(`/admin/send-driver-commission-email`, {
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
                alert("Email sent successfully!");
                bootstrap.Modal.getInstance(document.getElementById("emailModal")).hide();
            } else {
                alert("Failed to send email.");
            }
        } catch (error) {
            console.error("Error sending email:", error);
            alert("An error occurred while sending email.");
        } finally {
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="bi bi-send"></i> Send';
        }
    });

});
</script>

@endsection

