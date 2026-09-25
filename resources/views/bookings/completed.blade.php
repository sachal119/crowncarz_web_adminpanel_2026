@extends('layouts.app')
@section('content')
<style>
    .table-responsive {
    overflow: visible !important;
}
</style>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3 fw-bold" style="color: #6B3E26;">Completed Jobs</h1>
</div>
<!-- Search & Filters -->
<div class="card shadow-sm border-0 mb-4">
  <div class="card-body bg-light rounded">
    <form action="{{ route('bookings.search') }}" method="GET" class="row g-3 align-items-end">
      <div class="col-md-12">
        <label class="form-label fw-semibold text-dark">Overall Search</label>
        <div class="input-group">
          <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
          <input type="text" name="search" class="form-control" placeholder="Search overall by Ref#, Name, Mobile, Pickup, Dropoff, Account, Driver, Flight#, Postcode, Comments..." value="{{ request('search') }}">
        </div>
      </div>
      <div class="col-md-2">
        <label class="form-label">From</label>
        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
      </div>
      <div class="col-md-2">
        <label class="form-label">To</label>
        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">Driver</label>
        <select name="driver_id" class="form-select">
          <option value="">All Drivers</option>
          @foreach($drivers as $driver)
            <option value="{{ $driver['id'] }}" {{ request('driver_id') == $driver['id'] ? 'selected' : '' }}>
              {{ $driver['name'] ?? 'Unknown Driver' }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Account</label>
        <select name="account_id" class="form-select">
          <option value="">All Accounts</option>
          @foreach($accounts as $account)
            @php
              $accId = is_object($account) ? ($account->id ?? '') : ($account['id'] ?? '');
              $accName = is_object($account) ? ($account->business_name ?? $account->name ?? '') : ($account['business_name'] ?? $account['name'] ?? '');
            @endphp
            @if(!empty($accName))
              <option value="{{ $accId }}" {{ (string) request('account_id') === (string) $accId ? 'selected' : '' }}>
                {{ $accName }}
              </option>
            @endif
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Payment</label>
        <select name="payment_type" class="form-select">
          <option value="">All Payments</option>
          <option value="Cash" {{ request('payment_type') == 'Cash' ? 'selected' : '' }}>Cash</option>
          <option value="Card" {{ request('payment_type') == 'Card' ? 'selected' : '' }}>Card</option>
          <option value="Account" {{ request('payment_type') == 'Account' ? 'selected' : '' }}>Account</option>
        </select>
      </div>
      <div class="col-md-12 text-end">
        <button type="submit" class="btn" style="background-color:#B87333; color:white;">
          <i class="bi bi-search me-1"></i> Search
        </button>
        <!-- 🔄 Reset Button -->
        <a href="{{ route('completed.jobs') }}" class="btn btn-outline-secondary ms-2">
          <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
        </a>
      </div>
    </form>
  </div>
</div>
<!-- Completed Bookings Table -->
<div class="card shadow-sm border-0" style="background-color: #FFF5CC; border-left: 5px solid #D39F61;">
  <div class="card-body">
    <h5 class="card-title mb-3 fw-semibold" style="color: #A86B32;">Completed Bookings<span class="ms-2">({{ count($completedBookings) }})</span></h5>
    <div class="table-responsive">
      <table class="table table-hover table-borderless align-middle">
        <thead style="background-color: #FAD788; color: #6B3E26;">
          <tr>
            <th scope="col">Ref#</th>
            <th scope="col">Passenger/Phone No</th>
            <th scope="col">Pickup</th>
            <th scope="col">Dropoff</th>
            <th scope="col">Date/Time</th>
            <th scope="col">Driver Name</th>
            <th scope="col">Price</th>
            <th scope="col">Payment</th>
            <th scope="col">Actions</th>
          </tr>
        </thead>
       
        <tbody>
          @forelse ($completedBookings as $booking)
          <tr style="border-bottom: 1px solid #F5DEB3;">
            <td class="text-dark fw-semibold">{{ $booking['ref_no'] ?? $booking['id'] ?? '-' }}</td>
            <td>{{ $booking['passenger_name'] ?? '-' }}<br><small>{{ $booking['phone_no'] ?? '' }}</small></td>
            <td>
              {{ Str::limit($booking['pickup_address'] ?? '-', 35) }}
              <br><small class="text-muted">Pickup Location</small>
            </td>
            <td>
              {{ Str::limit($booking['dropoff_address'] ?? '-', 35) }}
              <br><small class="text-muted">Dropoff Location</small>
            </td>
            <td>{{ !empty($booking['pickup_time']) ? \Carbon\Carbon::parse($booking['pickup_time'])->format('d M Y, h:i A') : '-' }}</td>
            <td>{{ !empty($booking['driver_name']) ? $booking['driver_name'] : 'Not Assigned' }}</td>
            <td>{{ !empty($booking['price']) ? $booking['price'] : '-' }}</td>
            @php
    $paymentType = strtolower($booking['payment_type'] ?? 'unknown');

    $paymentColors = [
        'cash'    => '#28a745', // green
        'card'    => '#0d6efd', // blue
        'account' => '#6f42c1', // purple
    ];

    $bgColor = $paymentColors[$paymentType] ?? '#6c757d'; // grey fallback
    $accName = $booking['account_name'] ?? '';
    if (empty($accName) && !empty($booking['account'])) {
        $accName = is_string($booking['account']) ? $booking['account'] : ($booking['account']['business_name'] ?? ($booking['account']['name'] ?? ''));
    }
@endphp

<td>
    <div class="d-flex flex-column align-items-start">
        <span class="badge rounded-pill px-2.5 py-1 text-white shadow-xs"
              style="background-color: {{ $bgColor }}; font-size: 10.5px; font-weight: 600;">
            {{ ucfirst($paymentType) }}
        </span>
        @if($paymentType === 'account' && !empty($accName))
            <span class="text-truncate fw-bold text-dark mt-1" style="max-width: 120px; font-size: 10.5px; line-height: 1.2;" title="{{ $accName }}">
                {{ $accName }}
            </span>
        @endif
    </div>
</td>
            <!--<td>-->
            <!--  <span class="badge rounded-pill px-3 py-2" style="background-color: #D39F61;">-->
            <!--    {{ ucfirst($booking['payment_type']) }}-->
            <!--  </span>-->
            <!--</td>-->
            <td>
              <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item d-flex align-items-center" href="{{ route('bookings.edit', $booking['id'] ?? '') }}">
                      <i class="bi bi-pencil me-2"></i> Edit Job
                    </a>
                  </li>
                  <!-- Send Confirmation Email -->
                  <li>
                    <a class="dropdown-item d-flex align-items-center text-success send-confirmation-email-btn"
                       href="#"
                       data-booking-id="{{ $booking['id'] ?? '' }}"
                       data-passenger-email="{{ $booking['email'] ?? '' }}"
                       data-passenger-name="{{ $booking['passenger_name'] ?? '' }}">
                        <i class="bi bi-envelope me-2"></i> Send Confirmation Email
                    </a>
                  </li>
                  <!-- Send Confirmation WhatsApp -->
                  <li>
                    <a class="dropdown-item d-flex align-items-center text-success send-confirmation-whatsapp-btn"
                       href="#"
                       data-booking-id="{{ $booking['ref_no'] ?? ($booking['id'] ?? '') }}"
                       data-raw-id="{{ $booking['id'] ?? '' }}"
                       data-phone="{{ $booking['phone_no'] ?? '' }}"
                       data-name="{{ $booking['passenger_name'] ?? '' }}"
                       data-date="{{ isset($booking['pickup_time']) ? \Carbon\Carbon::parse($booking['pickup_time'])->format('d M Y') : '' }}"
                       data-time="{{ isset($booking['pickup_time']) ? \Carbon\Carbon::parse($booking['pickup_time'])->format('H:i') : '' }}"
                       data-vehicle="{{ $booking['vehicle_make'] ?? '' }}"
                       data-price="{{ $booking['price'] ?? '' }}"
                       data-payment="{{ ucfirst($booking['payment_type'] ?? '') }}"
                       data-pickup="{{ $booking['pickup_address'] ?? '' }}"
                       data-dropoff="{{ $booking['dropoff_address'] ?? '' }}"
                       data-flight_no="{{ $booking['flight_no'] ?? '' }}"
                       data-via="{{ !empty($booking['vias']) ? (is_array($booking['vias']) ? implode(' → ', $booking['vias']) : $booking['vias']) : '-' }}">
                        <i class="bi bi-whatsapp me-2"></i> Send WhatsApp Confirmation
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item d-flex align-items-center text-success send-receipt-whatsapp-btn"
                       href="#"
                       data-booking-id="{{ $booking['id'] ?? '' }}"
                       data-phone="{{ $booking['phone_no'] ?? '' }}">
                        <i class="bi bi-file-earmark-pdf me-2"></i> Send Receipt via WhatsApp
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item d-flex align-items-center text-success" href="{{ route('bookings.receipt', $booking['id'] ?? '') }}">
                      <i class="bi bi-receipt me-2"></i> Receipt
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item d-flex align-items-center text-info" href="{{ route('bookings.return', $booking['id'] ?? '') }}">
                      <i class="bi bi-arrow-repeat me-2"></i> Create Return Job
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item d-flex align-items-center" href="{{ route('bookings.edit', $booking['id'] ?? '') }}">
                      <i class="bi bi-box-arrow-in-right me-2"></i> ReOpen Job
                    </a>
                  </li>
                </ul>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center text-muted py-4">No completed bookings found.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Send Email Modal -->
<div class="modal fade" id="sendEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success bg-opacity-25">
                <h6 class="modal-title">Send Confirmation Email</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="emailBookingId">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" id="emailAddress" class="form-control" placeholder="example@domain.com">
                </div>
                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea id="emailMessage" class="form-control" rows="4"></textarea>
                </div>
                <button class="btn btn-success w-100" id="sendEmailNowBtn">
                    Send Email
                </button>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

@if (count($completedBookings) > 0)
 <script>
// $(document).ready(function() {
//   const table = $('.table').DataTable({
//     "order": [[4, "desc"]],
//     "columnDefs": [
//       { "orderable": true, "targets": 4 },
//       { "orderable": false, "targets": "_all" }
//     ],
//     "dom": 't<"dt-pagination"p>', // hides search + entries, keeps pagination
//   });

//   table.on('order.dt', function () {
//     $('.dt-header-icon').remove();
//     const order = table.order();
//     const colIndex = order[0][0];
//     const dir = order[0][1];

//   });

//   table.order([4, 'desc']).draw();
// });
// </script>
<script>
$(document).ready(function() {
  $('.table').DataTable({
    ordering: false,      // ❌ Disable sorting completely
    //searching: false,     // ❌ Disable search
    //info: true,          // ❌ Hide "Showing X to Y of Z entries"
    lengthChange: false,  // ❌ Hide "Show entries" dropdown
    pageLength: 20,         // ✅ Show 20 bookings per page
    dom: 't<"dt-pagination"p>' // ✅ Table + Pagination only
  });
});
</script>
@endif

<!-- Send WhatsApp Modal -->
<div class="modal fade" id="sendWhatsAppModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h6 class="modal-title"><i class="bi bi-whatsapp me-2"></i> Send Confirmation WhatsApp</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="waBookingId">
                <div class="mb-3">
                    <label class="form-label fw-bold">Phone Number (International format e.g. 447...)</label>
                    <input type="text" id="waRecipientPhone" class="form-control" placeholder="447123456789">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">WhatsApp Message</label>
                    <textarea id="waMessageText" class="form-control font-monospace" rows="7" style="font-size: 13px;"></textarea>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-success flex-grow-1" id="btnSendWhatsAppNow">
                        <i class="bi bi-send me-1"></i> Send Text
                    </button>
                    <button class="btn btn-outline-success" id="btnSendWhatsAppReceiptModal" title="Generate and send official PDF receipt">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Send PDF Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
/* ----------------------
       EMAIL BUTTON CLICK
    ---------------------- */
    document.querySelectorAll(".send-confirmation-email-btn").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            let bookingId = this.getAttribute("data-booking-id");
            let row = this.closest("tr");
            let passengerName = row.querySelector("td:nth-child(2)").innerText.trim();
            let emailAddressValue = this.getAttribute("data-passenger-email") || '';
            document.getElementById("emailBookingId").value = bookingId;
            document.getElementById("emailAddress").value = emailAddressValue;
            document.getElementById("emailMessage").value = "Hello " + passengerName + ", your booking has been confirmed. Ref# " + bookingId;
            let modal = new bootstrap.Modal(document.getElementById("sendEmailModal"));
            modal.show();
        });
    });
 
 document.getElementById("sendEmailNowBtn").addEventListener("click", function () {
        let bookingId = document.getElementById("emailBookingId").value;
        let email = document.getElementById("emailAddress").value;
        let message = document.getElementById("emailMessage").value;
        if (!email.trim()) { alert("Please enter email"); return; }
        fetch("{{ route('bookings.sendEmailDashboard') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ booking_id: bookingId, email: email, message: message })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                alert("Email sent successfully!");
                bootstrap.Modal.getInstance(document.getElementById("sendEmailModal")).hide();
            } else {
                alert("Email failed to send.");
            }
        })
        .catch(err => {
            console.error(err);
            alert("Error sending email.");
        });
    });

/* ----------------------
       WHATSAPP HANDLERS
    ---------------------- */
    document.querySelectorAll(".send-confirmation-whatsapp-btn").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            const refNo   = this.getAttribute("data-booking-id") || "";
            const rawId   = this.getAttribute("data-raw-id") || "";
            const phone   = this.getAttribute("data-phone") || "";
            const name    = this.getAttribute("data-name") || "";
            const date    = this.getAttribute("data-date") || "";
            const time    = this.getAttribute("data-time") || "";
            const vehicle = this.getAttribute("data-vehicle") || "";
            const price   = this.getAttribute("data-price") || "";
            const payment = this.getAttribute("data-payment") || "";
            const pickup  = this.getAttribute("data-pickup") || "";
            const dropoff = this.getAttribute("data-dropoff") || "";
            const flight  = this.getAttribute("data-flight_no") || "";
            const via     = this.getAttribute("data-via") || "";

            let message = `*Crown Carz - Booking Confirmation*\n` +
                          `━━━━━━━━━━━━━━━━━━\n` +
                          `*Ref #:* ${refNo}\n` +
                          `*Passenger:* ${name}\n` +
                          `*Pickup Date/Time:* ${date} ${time}\n` +
                          `*From:* ${pickup}\n` +
                          `*To:* ${dropoff}\n`;

            if (via && via !== '-' && via !== 'undefined') {
                message += `*Via:* ${via}\n`;
            }
            if (flight && flight !== '-' && flight !== 'undefined') {
                message += `*Flight #:* ${flight}\n`;
            }
            if (vehicle) {
                message += `*Vehicle:* ${vehicle}\n`;
            }
            if (price) {
                message += `*Price:* £${price} (${payment})\n`;
            }
            message += `━━━━━━━━━━━━━━━━━━\nThank you for choosing Crown Carz!`;

            document.getElementById("waBookingId").value = rawId;
            document.getElementById("waRecipientPhone").value = phone;
            document.getElementById("waMessageText").value = message;

            const modal = new bootstrap.Modal(document.getElementById("sendWhatsAppModal"));
            modal.show();
        });
    });

    document.getElementById("btnSendWhatsAppNow")?.addEventListener("click", async function() {
        const rawId   = document.getElementById("waBookingId").value;
        const phone   = document.getElementById("waRecipientPhone").value.trim();
        const message = document.getElementById("waMessageText").value.trim();

        if (!phone) { alert("Please enter a valid phone number."); return; }
        if (!message) { alert("Please enter a message to send."); return; }

        const origText = this.innerHTML;
        this.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Sending...`;
        this.disabled = true;

        try {
            const res = await fetch("{{ route('bookings.sendWhatsAppDashboard') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ booking_id: rawId, phone: phone, message: message })
            });
            const data = await res.json();
            if (data.success) {
                alert("✅ " + (data.message || "WhatsApp message sent successfully!"));
                bootstrap.Modal.getInstance(document.getElementById("sendWhatsAppModal")).hide();
            } else {
                alert("❌ " + (data.message || "Failed to send WhatsApp message."));
            }
        } catch (err) {
            console.error(err);
            alert("❌ An error occurred while sending WhatsApp message.");
        } finally {
            this.innerHTML = origText;
            this.disabled = false;
        }
    });

    document.getElementById("btnSendWhatsAppReceiptModal")?.addEventListener("click", async function() {
        const rawId = document.getElementById("waBookingId").value;
        const phone = document.getElementById("waRecipientPhone").value.trim();
        if (!rawId) { alert("Invalid booking ID for receipt."); return; }
        sendReceiptViaWhatsApp(rawId, phone);
    });

    document.querySelectorAll(".send-receipt-whatsapp-btn").forEach(btn => {
        btn.addEventListener("click", function(e) {
            e.preventDefault();
            const rawId = this.getAttribute("data-booking-id");
            const phone = this.getAttribute("data-phone");
            sendReceiptViaWhatsApp(rawId, phone);
        });
    });

    async function sendReceiptViaWhatsApp(bookingId, defaultPhone = '') {
        const targetPhone = prompt("Enter recipient WhatsApp phone number for PDF Receipt:", defaultPhone || "");
        if (!targetPhone) return;

        try {
            const url = `{{ url('/booking') }}/${bookingId}/send-receipt-whatsapp`;
            const res = await fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ phone: targetPhone })
            });
            const data = await res.json();
            if (data.success) {
                alert("✅ " + (data.message || "PDF Receipt sent successfully via WhatsApp!"));
                const modalEl = document.getElementById("sendWhatsAppModal");
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            } else {
                alert("❌ " + (data.message || "Failed to send PDF receipt."));
            }
        } catch (err) {
            console.error(err);
            alert("❌ Error sending PDF receipt on WhatsApp.");
        }
    }
</script>
@endsection