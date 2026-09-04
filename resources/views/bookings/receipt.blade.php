@extends('layouts.app')

@section('content')
<div class="container py-4" id="receipt-section">
    <div class="card shadow border-0">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">🧾 Booking Receipt</h5>
            <div>
                <a href="{{ url()->previous() }}" class="btn btn-warning btn-sm me-2">
        ⬅️ Back
    </a>
                <!-- Open Email Modal -->
                <button type="button" class="btn btn-light btn-sm me-2 send-email-btn" data-bs-toggle="modal" data-bs-target="#emailModal" data-booking-id="{{ $bid }}">
                    📧 Send to Email
                </button>
                
                <!-- Print Button -->
                <!--<button onclick="printReceipt()" class="btn btn-light btn-sm">-->
                <!--    🖨️ Print / Save as PDF-->
                <!--</button>-->
            </div>
        </div>

        <div class="card-body">
            <div class="text-center mb-4">
                <img src="{{ asset('public/images/logo_black.png') }}" alt="Company Logo" height="60">
                <h4 class="mt-2 mb-0">CrownCarz</h4>
                <small class="text-muted">Premium Airport & Chauffeur Services</small>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <p><strong>Booking Reference:</strong> {{ $booking['ref_no'] ?? 'N/A' }}</p>
                    <p><strong>Customer:</strong> {{ $booking['passenger_name'] ?? 'N/A' }}</p>
                    <p><strong>Driver:</strong> {{ $booking['driver_id'] ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>
    <strong>Date:</strong> 
    {{ \Carbon\Carbon::parse($booking['pickup_time'] ?? now())->format('d/M/Y H:i:s') }}
</p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-info text-dark">{{ ucfirst($booking['status'] ?? 'N/A') }}</span>
                    </p>
                    <p><strong>Fare:</strong> £{{ number_format($booking['price'] ?? 0, 2) }}</p>
                </div>
            </div>

            <hr>

            <div class="row mt-3">
                <div class="col-md-6">
                    <h6 class="text-success fw-bold">🚘 Pickup Address</h6>
                    <p>{{ $booking['pickup_address'] ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-success fw-bold">🏁 Dropoff Address</h6>
                    <p>{{ $booking['dropoff_address'] ?? 'N/A' }}</p>
                </div>
            </div>

            @if(!empty($booking['via_points']))
                <hr>
                <h6 class="text-success fw-bold">📍 Via Points</h6>
                <ul>
                    @foreach($booking['via_points'] as $via)
                        <li>{{ $via }}</li>
                    @endforeach
                </ul>
            @endif

            <hr>
            <div class="text-center mt-4">
                <p class="text-muted mb-0">Thank you for choosing <strong>CrownCarz</strong>!</p>
                <small class="text-muted">Visit us at 
                    <a href="https://crowncarz.com" target="_blank">
                        crowncarz.com
                    </a>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- 📧 Email Modal -->
<div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="emailModalLabel">Send Booking Receipt</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="sendEmailForm" method="POST" action="{{ route('booking.sendEmail') }}">
            @csrf
            <input type="hidden" name="booking_id" id="bookingIdField" value="{{ $bid}}">
            <div class="mb-3">
                <label for="emailAddress" class="form-label">Enter Email Address</label>
                <input type="email" class="form-control" id="emailAddress" name="email" placeholder="example@email.com" required>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-success">Send Receipt</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>


@endsection

@section('scripts')
<script>
    const bookingId = "{{ $bid }}";  // Blade will replace $id with actual value
    console.log("Booking ID from Blade:", bookingId);
</script>
<script>
    
// 🖨️ Print as PDF
function printReceipt() {
    const elementsToHide = document.querySelectorAll('.btn, nav, header, footer, .modal');
    elementsToHide.forEach(el => el.style.display = 'none');
    window.print();
    setTimeout(() => elementsToHide.forEach(el => el.style.display = ''), 1000);
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== Receipt Page Script Loaded ==='); // Confirm script runs

    // ✅ Reference to hidden field
    const bookingIdField = document.getElementById('bookingIdField');
    console.log("Booking ID field found:", bookingIdField); // Should NOT be null
    console.log("Initial Booking ID value:", bookingIdField ? bookingIdField.value : 'Field missing');

    // ✅ Log the data-booking-id from the button IMMEDIATELY on load (force early log)
    const sendEmailButton = document.querySelector('.send-email-btn'); // Use class for specificity
    if (sendEmailButton) {
        const dataBookingId = sendEmailButton.getAttribute('data-booking-id');
        console.log("=== KEY LOG: Data-booking-id from button on page load ===", dataBookingId); // This is the value you want
        console.log("Full button HTML:", sendEmailButton.outerHTML); // Log full button for inspection
    } else {
        console.warn("=== ERROR: Send Email button not found! ===");
        // Fallback: Log all buttons
        const allButtons = document.querySelectorAll('button');
        console.log("All buttons on page:", allButtons);
    }

    // ✅ When modal opens, fill booking_id (fallback if not set)
    const emailModal = document.getElementById('emailModal');
    
    
    
    
    
    if (emailModal) {
        emailModal.addEventListener('show.bs.modal', function (event) {
            console.log('=== Modal Opening Event Fired ===');
            const button = event.relatedTarget;
            const rawDataId = button ? button.getAttribute('data-booking-id') : 'Button missing';
            console.log("Raw data-booking-id from button on modal open:", rawDataId);
            
            const bookingId = rawDataId || bookingIdField.value; // Use hidden value as fallback
            bookingIdField.value = bookingId || '';
            console.log("Final Booking ID set to:", bookingIdField.value);
            console.log("Full relatedTarget:", button);
            
            // ✅ Validate before proceeding
            if (!bookingIdField.value) {
                console.error('=== CRITICAL: Booking ID is empty after set! ===');
                alert('❌ Booking ID is missing. Please refresh the page.');
                const modalInstance = bootstrap.Modal.getInstance(emailModal);
                if (modalInstance) modalInstance.hide();
                return;
            }
        });
    } else {
        console.warn('=== ERROR: Email modal not found! ===');
    }

    // ✅ Handle form submission
    const form = document.getElementById('sendEmailForm');
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('=== Form Submit Event Fired ===');
            
            // ✅ Double-check booking_id before submit
            if (!bookingIdField.value) {
                console.error('=== CRITICAL: Booking ID empty on submit! ===');
                alert('❌ Booking ID is required. Please try again.');
                return;
            }
            console.log("Form submitting with booking_id:", bookingIdField.value); // Log before send
            console.log("Full form data:", new FormData(this)); // Log form contents
            
            const button = this.querySelector('button[type="submit"]');
            button.disabled = true;
            button.innerText = 'Sending...';

            try {
                const response = await fetch("{{ route('booking.sendEmail') }}", {
                    method: "POST",
                    headers: { 
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/x-www-form-urlencoded" // Ensure proper form encoding
                    },
                    body: new URLSearchParams(new FormData(this)).toString() // Convert FormData to URLSearchParams for reliability
                });
                console.log("Fetch response status:", response.status); // Log response
                const data = await response.json();
                console.log("Server response:", data); // Log full response
                button.disabled = false;
                button.innerText = 'Send Receipt';

                if (data.success === true) {  // ← Check 'status' instead
    alert('✅ Email sent successfully!');
    const modalInstance = bootstrap.Modal.getInstance(emailModal);
    if (modalInstance) modalInstance.hide();
    form.reset();
} else {
    alert('❌ Failed: ' + (data.message || 'Please try again.'));
}
            } catch (err) {
                console.error('=== Fetch Error Details ===', err);
                button.disabled = false;
                button.innerText = 'Send Receipt';
                alert('⚠️ Network error or server issue.');
            }
        });
    } else {
        console.warn('=== ERROR: Send Email form not found! ===');
    }

    console.log('=== End of Receipt Script ===');
});
</script>




<style>
/* 🌟 Print-Friendly Styling */
@media print {
    body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        background: #fff;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }

    .card-header {
        background-color: #198754 !important; 
        color: white !important;
    }

    .btn, nav, header, footer, .modal {
        display: none !important;
    }

    #receipt-section {
        margin: 0;
        padding: 0;
    }

    @page {
        margin: 20mm;
    }
}
</style>
@endsection