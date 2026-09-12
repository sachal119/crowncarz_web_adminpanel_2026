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
      <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Search by Name, Ref#, Mobile" value="{{ request('search') }}">
      </div>
      <div class="col-md-4">
        <input type="text" name="pickup" class="form-control" placeholder="Pickup Address" value="{{ request('pickup') }}">
      </div>
      <div class="col-md-4">
        <input type="text" name="dropoff" class="form-control" placeholder="Dropoff Address" value="{{ request('dropoff') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">From</label>
        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">To</label>
        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">Driver</label>
        <select name="driver_id" class="form-select">
          <option value="">All</option>
          @foreach($drivers as $driver)
            <option value="{{ $driver['id'] }}" {{ request('driver_id') == $driver['id'] ? 'selected' : '' }}>
              {{ $driver['name'] ?? 'Unknown Driver' }}
            </option>
          @endforeach
        </select>
      </div>
      <!--<div class="col-md-2">-->
      <!--  <label class="form-label">Account</label>-->
      <!--  <select name="account_id" class="form-select">-->
      <!--    <option value="">All</option>-->
      <!--    @foreach($accounts as $account)-->
      <!--      <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>-->
      <!--        {{ $account->business_name }}-->
      <!--      </option>-->
      <!--    @endforeach-->
      <!--  </select>-->
      <!--</div>-->
      <div class="col-md-3">
        <label class="form-label">Payment</label>
        <select name="payment_type" class="form-select">
          <option value="">All</option>
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
@endphp

<td>
    <span class="badge rounded-pill px-3 py-2"
          style="background-color: {{ $bgColor }}; color: #fff;">
        {{ ucfirst($paymentType) }}
    </span>
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

<script>
/* ----------------------
       EMAIL BUTTON CLICK
    ---------------------- */
    document.querySelectorAll(".send-confirmation-email-btn").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            // Function body within an event listener
            let bookingId = this.getAttribute("data-booking-id");
            let row = this.closest("tr");
            // 1. Get the Passenger Name from the row (assuming it's in the second column)
            // This is used for the greeting in the message.
            let passengerName = row.querySelector("td:nth-child(2)").innerText.trim();
            // 2. Get the Email Address using a Data Attribute (BEST PRACTICE)
            // Assumes the email is stored in an attribute like 'data-passenger-email' on 'this' element.
            let emailAddressValue = this.getAttribute("data-passenger-email");
            // Fallback: If not available in a data attribute, use the booking ID to populate the email field (less ideal)
            if (!emailAddressValue) {
                // If you need the email address, ensure it is rendered in a hidden cell or a data attribute.
                // For now, setting it to a placeholder or empty string if not found.
                emailAddressValue = ''; // Or prompt the user for it later
            }
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
</script>
@endsection