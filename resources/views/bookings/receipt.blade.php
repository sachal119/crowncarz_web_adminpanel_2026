@extends('layouts.app')

@section('content')
@php
$paymentType = strtolower($booking['payment_type'] ?? '');

if ($paymentType === 'cash') {
    $paymentLabel = 'Pay in Car';
    $paymentBadgeClass = 'badge bg-warning text-dark';
} elseif ($paymentType === 'card') {
    $paymentLabel = 'Payment Received (Card)';
    $paymentBadgeClass = 'badge bg-success';
} elseif ($paymentType === 'account') {
    $paymentLabel = 'Account Invoice';
    $paymentBadgeClass = 'badge bg-primary';
} else {
    $paymentLabel = ucfirst($paymentType ?: 'Cash');
    $paymentBadgeClass = 'badge bg-secondary';
}

$pickupDateFormatted = '';
if (!empty($booking['pickup_time'])) {
    try {
        $pickupDateFormatted = \Carbon\Carbon::parse($booking['pickup_time'])->format('d M Y, H:i');
    } catch (\Exception $e) {
        $pickupDateFormatted = $booking['pickup_time'];
    }
}
@endphp

<div class="container py-4" id="receipt-section">
    <!-- Top Action Bar (hidden in print) -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 print-hide">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button onclick="printReceipt()" class="btn btn-outline-dark btn-sm px-3 shadow-sm">
                <i class="bi bi-printer me-1"></i> Print Receipt
            </button>
            <a href="{{ route('receipt.download', $bid) }}" target="_blank" class="btn btn-outline-success btn-sm px-3 shadow-sm">
                <i class="bi bi-download me-1"></i> Download PDF
            </a>
            <button type="button" class="btn btn-primary btn-sm px-3 send-email-btn shadow-sm" data-bs-toggle="modal" data-bs-target="#emailModal" data-booking-id="{{ $bid }}">
                <i class="bi bi-envelope me-1"></i> Send to Email
            </button>
        </div>
    </div>

    <!-- Luxury Executive Receipt Card -->
    <div class="card shadow border-0 mx-auto receipt-card" style="max-width: 860px; border-radius: 12px; overflow: hidden; background: #fff;">
        
        <!-- Header Banner -->
        <div class="receipt-header p-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-bottom: 3px solid #d97706; color: #fff;">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <img src="{{ asset('public/images/logo.png') }}" alt="Crown Carz" height="48" class="mb-2" onerror="this.onerror=null; this.src='{{ asset('public/images/logo_black.png') }}';">
                    <h5 class="mb-0 text-white fw-bold letter-spacing-1">CROWN CARZ</h5>
                    <div class="text-warning small text-uppercase fw-semibold" style="letter-spacing: 0.8px;">Premium Airport & Chauffeur Services</div>
                    <div class="small text-light opacity-75 mt-1" style="font-size: 11px;">
                        Tel: +44 (0)1189 47 47 47 &nbsp;|&nbsp; info@crowncarz.com &nbsp;|&nbsp; www.crowncarz.com
                    </div>
                </div>
                <div class="text-md-end">
                    <span class="badge bg-warning text-dark text-uppercase px-3 py-1 mb-2 fw-bold" style="font-size: 13px; letter-spacing: 1px;">
                        BOOKING RECEIPT
                    </span>
                    <div class="bg-white bg-opacity-10 rounded p-2 text-start text-md-end mt-1 border border-white border-opacity-10">
                        <div class="small text-light opacity-75" style="font-size: 10px; text-transform: uppercase;">Reference Number</div>
                        <div class="fw-bold text-warning fs-6">{{ $booking['ref_no'] ?? $booking['id'] }}</div>
                        <div class="small text-light opacity-75" style="font-size: 11px;">
                            Issued: {{ date('d M Y, H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            
            <!-- Passenger & Service Overview Grid -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="p-3 rounded border h-100" style="background: #f8fafc;">
                        <div class="d-flex align-items-center mb-2 pb-1 border-bottom">
                            <i class="bi bi-person-circle text-primary me-2 fs-6"></i>
                            <span class="small fw-bold text-uppercase text-secondary" style="letter-spacing: 0.5px;">Passenger Information</span>
                        </div>
                        <table class="table table-borderless table-sm mb-0 small">
                            <tbody>
                                <tr>
                                    <td class="text-muted ps-0" style="width: 35%;">Name:</td>
                                    <td class="fw-bold text-dark">{{ $booking['passenger_name'] ?? 'Guest Passenger' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Phone:</td>
                                    <td class="fw-bold text-dark">{{ $booking['phone_no'] ?? $booking['phone'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Email:</td>
                                    <td class="fw-bold text-dark">{{ $booking['email'] ?? 'N/A' }}</td>
                                </tr>
                                @if(!empty($booking['account_name']))
                                <tr>
                                    <td class="text-muted ps-0">Account:</td>
                                    <td class="fw-bold text-dark">{{ $booking['account_name'] }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 rounded border h-100" style="background: #f8fafc;">
                        <div class="d-flex align-items-center mb-2 pb-1 border-bottom">
                            <i class="bi bi-car-front text-success me-2 fs-6"></i>
                            <span class="small fw-bold text-uppercase text-secondary" style="letter-spacing: 0.5px;">Service & Vehicle Details</span>
                        </div>
                        <table class="table table-borderless table-sm mb-0 small">
                            <tbody>
                                <tr>
                                    <td class="text-muted ps-0" style="width: 38%;">Date & Time:</td>
                                    <td class="fw-bold text-dark">{{ $pickupDateFormatted ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Vehicle:</td>
                                    <td class="fw-bold text-dark">{{ ucfirst($booking['vehicle_make'] ?? $booking['vehicle_id'] ?? 'Saloon') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Flight No:</td>
                                    <td class="fw-bold text-dark">{{ !empty($booking['flight_no']) ? $booking['flight_no'] : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Payment:</td>
                                    <td><span class="{{ $paymentBadgeClass }} px-2 py-1">{{ $paymentLabel }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Journey Itinerary Box -->
            <div class="p-3 rounded border mb-4" style="background: #ffffff;">
                <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                    <i class="bi bi-geo-alt-fill text-danger me-2 fs-6"></i>
                    <span class="small fw-bold text-uppercase text-secondary" style="letter-spacing: 0.5px;">Journey Itinerary</span>
                </div>

                <div class="position-relative ps-2">
                    <!-- Pickup -->
                    <div class="d-flex align-items-start mb-3">
                        <span class="badge bg-success text-white me-3 mt-1 px-2 py-1" style="font-size: 10px; min-width: 65px;">PICKUP</span>
                        <div>
                            <div class="fw-bold text-dark">{{ $booking['pickup_address'] ?? 'N/A' }}</div>
                            <small class="text-muted">Scheduled: {{ $pickupDateFormatted ?: 'N/A' }}</small>
                        </div>
                    </div>

                    <!-- Via Points -->
                    @php
                        $vias = [];
                        if (!empty($booking['via_points'])) {
                            $vias = is_array($booking['via_points']) ? $booking['via_points'] : explode(',', $booking['via_points']);
                        } elseif (!empty($booking['vias'])) {
                            $vias = is_array($booking['vias']) ? $booking['vias'] : explode(',', $booking['vias']);
                        }
                    @endphp
                    @foreach($vias as $idx => $via)
                        @if(trim($via))
                        <div class="d-flex align-items-start mb-3">
                            <span class="badge bg-warning text-dark me-3 mt-1 px-2 py-1" style="font-size: 10px; min-width: 65px;">VIA {{ $idx + 1 }}</span>
                            <div class="fw-semibold text-dark">{{ trim($via) }}</div>
                        </div>
                        @endif
                    @endforeach

                    <!-- Dropoff -->
                    <div class="d-flex align-items-start">
                        <span class="badge bg-danger text-white me-3 mt-1 px-2 py-1" style="font-size: 10px; min-width: 65px;">DROPOFF</span>
                        <div>
                            <div class="fw-bold text-dark">{{ $booking['dropoff_address'] ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>

                @if(!empty($booking['comment']) || !empty($booking['comments']) || !empty($booking['note']))
                <div class="mt-3 pt-2 border-top border-dashed small text-secondary">
                    <strong>Special Notes:</strong> {{ $booking['comment'] ?? $booking['comments'] ?? $booking['note'] }}
                </div>
                @endif
            </div>

            <!-- Itemized Pricing Table -->
            @php
                $baseFare = (float)($booking['base_fare'] ?? $booking['price'] ?? 0);
                $parkingFee = (float)($booking['parking_charge'] ?? 0);
                $waitingFee = (float)($booking['waiting_charge'] ?? 0);
                $extraFee = (float)($booking['extra_charge'] ?? 0);
                $totalFare = (float)($booking['price'] ?? ($baseFare + $parkingFee + $waitingFee + $extraFee));
            @endphp
            <div class="table-responsive rounded border mb-4 overflow-hidden">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr class="small text-uppercase" style="letter-spacing: 0.5px;">
                            <th class="ps-3 py-2" style="width: 50%;">Description</th>
                            <th class="py-2" style="width: 25%;">Type</th>
                            <th class="pe-3 py-2 text-end" style="width: 25%;">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark">Chauffeur Journey Fare</div>
                                <div class="text-muted" style="font-size: 11px;">Standard transfer rate</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">Standard Rate</span></td>
                            <td class="pe-3 text-end fw-bold">£{{ number_format($baseFare > 0 ? $baseFare : $totalFare, 2) }}</td>
                        </tr>
                        @if($parkingFee > 0)
                        <tr>
                            <td class="ps-3">Airport Parking / Drop-off Fee</td>
                            <td><span class="badge bg-light text-dark border">Surcharge</span></td>
                            <td class="pe-3 text-end fw-bold">£{{ number_format($parkingFee, 2) }}</td>
                        </tr>
                        @endif
                        @if($waitingFee > 0)
                        <tr>
                            <td class="ps-3">Waiting Time Charges</td>
                            <td><span class="badge bg-light text-dark border">Additional</span></td>
                            <td class="pe-3 text-end fw-bold">£{{ number_format($waitingFee, 2) }}</td>
                        </tr>
                        @endif
                        @if($extraFee > 0)
                        <tr>
                            <td class="ps-3">Extra Stops / Area Surcharge</td>
                            <td><span class="badge bg-light text-dark border">Additional</span></td>
                            <td class="pe-3 text-end fw-bold">£{{ number_format($extraFee, 2) }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Total Fare Box -->
            <div class="row align-items-center g-3 mb-4">
                <div class="col-md-7">
                    <div class="p-3 rounded border bg-light small">
                        <div class="fw-bold text-dark mb-1">Payment Method: {{ $paymentLabel }}</div>
                        <div class="text-muted">
                            @if($paymentType === 'card')
                                Payment completed securely via card.
                            @elseif($paymentType === 'account')
                                Charged directly to client corporate account.
                            @else
                                Payable in cash directly to the driver in the vehicle.
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="p-3 rounded text-white text-end shadow-sm" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-right: 4px solid #d97706;">
                        <div class="small text-uppercase text-light opacity-75 fw-semibold" style="letter-spacing: 0.8px;">Total Amount Due</div>
                        <div class="fs-3 fw-bold text-warning">£{{ number_format($totalFare, 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Receipt Footer Note -->
            <div class="text-center pt-3 border-top small text-muted">
                <div class="fw-bold text-dark mb-1">Thank you for choosing Crown Carz!</div>
                <div>Crown Carz Ltd is a licensed private hire vehicle operator.</div>
                <div style="font-size: 11px;" class="mt-1">
                    For inquiries or reservations, please visit <a href="https://crowncarz.com" target="_blank" class="text-decoration-none fw-semibold">crowncarz.com</a> or call <strong>+44 (0)1189 47 47 47</strong>.
                </div>
            </div>

        </div>
    </div>
</div>

<!-- 📧 Send Email Modal -->
<div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
      <div class="modal-header bg-dark text-white" style="border-bottom: 2px solid #d97706;">
        <h6 class="modal-title fw-bold" id="emailModalLabel">
            <i class="bi bi-envelope-paper me-2 text-warning"></i> Send Booking Receipt via Email
        </h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form id="sendEmailForm" method="POST" action="{{ route('booking.sendEmail') }}">
            @csrf
            <input type="hidden" name="booking_id" id="bookingIdField" value="{{ $bid }}">
            <div class="mb-3">
                <label for="emailAddress" class="form-label small fw-semibold">Recipient Email Address</label>
                <input type="email" class="form-control" id="emailAddress" name="email" value="{{ $booking['email'] ?? '' }}" placeholder="customer@example.com" required>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-success px-3">
                    <i class="bi bi-send me-1"></i> Send Receipt
                </button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
    const bookingId = "{{ $bid }}";
</script>
<script>
function printReceipt() {
    window.print();
}

document.addEventListener('DOMContentLoaded', function() {
    const bookingIdField = document.getElementById('bookingIdField');
    const emailModal = document.getElementById('emailModal');
    
    if (emailModal) {
        emailModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const rawDataId = button ? button.getAttribute('data-booking-id') : null;
            const targetBookingId = rawDataId || (bookingIdField ? bookingIdField.value : '') || bookingId;
            if (bookingIdField) {
                bookingIdField.value = targetBookingId;
            }
        });
    }

    const form = document.getElementById('sendEmailForm');
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            if (!bookingIdField.value) {
                alert('❌ Booking ID is required. Please refresh the page.');
                return;
            }
            
            const button = this.querySelector('button[type="submit"]');
            const originalHtml = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';

            try {
                const response = await fetch("{{ route('booking.sendEmail') }}", {
                    method: "POST",
                    headers: { 
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: new URLSearchParams(new FormData(this)).toString()
                });
                const data = await response.json();
                button.disabled = false;
                button.innerHTML = originalHtml;

                if (data.success === true || data.status === true) {
                    alert('✅ Email sent successfully!');
                    const modalInstance = bootstrap.Modal.getInstance(emailModal);
                    if (modalInstance) modalInstance.hide();
                } else {
                    alert('❌ Failed: ' + (data.message || 'Please try again.'));
                }
            } catch (err) {
                console.error('Fetch error:', err);
                button.disabled = false;
                button.innerHTML = originalHtml;
                alert('⚠️ Network error or server issue.');
            }
        });
    }
});
</script>

<style>
/* 🌟 Print-Friendly Styling */
@media print {
    body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        background: #fff !important;
    }
    .print-hide, nav, header, footer, .modal, .btn {
        display: none !important;
    }
    #receipt-section {
        margin: 0 !important;
        padding: 0 !important;
        max-width: 100% !important;
    }
    .receipt-card {
        box-shadow: none !important;
        border: none !important;
        max-width: 100% !important;
    }
    @page {
        margin: 15mm;
        size: A4 portrait;
    }
}
</style>
@endsection