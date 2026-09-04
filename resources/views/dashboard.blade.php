@extends('layouts.app')

@section('content')
<style>
  /* Responsive font scaling */
  html {
    font-size: 16px; /* base for large screens */
  }

  @media (max-width: 1200px) {
    html {
      font-size: 15px;
    }
  }

  @media (max-width: 992px) {
    html {
      font-size: 14px;
    }
  }

  @media (max-width: 768px) {
    html {
      font-size: 13px;
    }
  }

  @media (max-width: 576px) {
    html {
      font-size: 12px;
    }
  }

  /* Optional: ensure table text doesn’t overflow */
  table {
    font-size: 0.95rem;
  }

  .small {
    font-size: 0.9rem !important;
  }

  h1, h2, h3, h4, h5, h6 {
    line-height: 1.2;
  }
  
 th {
  font-size: 12px ;
}

td{
    font-size: 12px;
}


/* Prevent dropdown from forcing page/table scroll */
.dropdown-menu {
  position: absolute !important;
  will-change: transform;
}
/* 🌟 Modern Dashboard Aesthetics */
.dashboard-section {
  padding: 0rem;
  border-radius: 12px;
}

/* === Card Styles === */
.dashboard-card {
  border: none;
  border-radius: 16px;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  position: relative;
  overflow: hidden;
}
.dashboard-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

/* === Gradient backgrounds for cards === */
.bg-success {
  background: linear-gradient(135deg, #16a085, #1abc9c) !important;
}
.bg-info {
  background: linear-gradient(135deg, #3498db, #5dade2) !important;
}
.bg-danger {
  background: linear-gradient(135deg, #e74c3c, #ff7675) !important;
}
.bg-secondary {
  background: linear-gradient(135deg, #7f8c8d, #95a5a6) !important;
}
.bg-primary {
  background: linear-gradient(135deg, #2e86de, #54a0ff) !important;
}
.bg-dark {
  background: linear-gradient(135deg, #2f3640, #353b48) !important;
}

/* === Card content === */
.dashboard-card .card-body {
  padding: 1.3rem 1rem;
}
.dashboard-card h1 {
  font-size: 2rem;
  margin-bottom: 0.4rem;
}
.dashboard-card h4 {
  font-size: 1.6rem;
  font-weight: 700;
}
.dashboard-card p {
  font-size: 0.9rem;
  opacity: 0.9;
}

/* === Section headers === */
.card-header {
  border-bottom: 1px solid #e3e6f0;
  font-weight: 600;
}

/* === Map container === */
#map {
  border-radius: 0 0 16px 16px;
}

/* === Pie Chart === */
#revenuePieChart {
  max-height: 240px;
}


.map-fullscreen-btn {
  position: absolute;
  bottom: 12px;
  left: 12px;
  z-index: 10;
  border-radius: 10px;
  padding: 6px 8px;
  background: rgba(255, 255, 255, 0.95);
}

.map-fullscreen-btn:hover {
  background: #fff;
}

/* === Responsive tweaks === */
@media (max-width: 768px) {
  .dashboard-card h1 {
    font-size: 1.6rem;
  }
  .dashboard-card h4 {
    font-size: 1.3rem;
  }
}

/* ===== Custom Pagination Styling ===== */
.pagination {
    padding: 10px 16px;
    border-radius: 6px;
}

.pagination .page-item {
    margin: 0 4px;
}

.pagination .page-link {
    border: 1px solid #d1b96d;
    color: #6b5e2e;
    padding: 6px 14px;
    border-radius: 4px;
    background-color: transparent;
    font-weight: 500;
}

.pagination .page-item.active .page-link {
    background-color: #ffe8a1;
    border-color: #bfa75a;
    color: #000;
    font-weight: 600;
}

.pagination .page-link:hover {
    background-color: #ffe8a1;
    color: #000;
}

.pagination .page-item.disabled .page-link {
    color: #9c8f5c;
    background-color: transparent;
    border-color: #e0d4a6;
}


/*.driver-infowindow {*/
/*    min-width: 260px;  */
/*    max-width: 320px;*/
/*    padding: 8px 10px;*/
/*    font-size: 13px;*/
/*}*/

.driver-infowindow {
  min-width: 140px;
  padding: 10px 12px;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.18);
  font-size: 13px;
  line-height: 1.4;
}

.driver-name {
  font-weight: 600;
  font-size: 14px;
  margin-bottom: 4px;
}

.driver-status {
  margin-bottom: 2px;
}


</style>
<style>
    .table-responsive {
    overflow: visible !important;
}
</style>


<!--<div class="d-flex justify-content-between align-items-center mb-4">-->
<!--    <h1 class="h4 text-dark fw-bold mb-0">🚗 Dashboard Overview</h1>-->
<!--    <div>-->
<!--        <a href="{{ route('completed.jobs') }}" class="btn btn-outline-secondary me-2">-->
<!--            <i class="bi bi-check-circle me-1"></i> Completed Jobs-->
<!--        </a>-->
<!--        <a href="{{ route('booking.create') }}" class="btn" style="background-color:#B87333; color:white;" target="_blank">-->
<!--            <i class="bi bi-plus-circle me-1"></i> Make Booking-->
<!--        </a>-->
<!--    </div>-->
<!--</div>-->
<div class="container-fluid dashboard-section">
    
    
    
  <div class="row g-4">

   <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light border-0">
                <h6 class="mb-0 text-dark">
                    <a class="text-decoration-none text-dark d-block"
   data-bs-toggle="collapse"
   href="#filtersCollapse"
   role="button"
   aria-expanded="true"
   aria-controls="filtersCollapse">
    <i class="bi bi-sliders me-2"></i> Search & Filters
    <i class="bi bi-chevron-down float-end filter-chevron"></i>
</a>
                </h6>
            </div>
            <div class="collapse" id="filtersCollapse">
                <div class="card-body bg-light rounded-bottom" style="
    height: 264px;
">
                    <form action="{{ route('bookings.search.main') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search by Name, Ref#, Mobile" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="pickup" class="form-control" placeholder="Pickup Address" value="{{ request('pickup') }}">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="dropoff" class="form-control" placeholder="Dropoff Address" value="{{ request('dropoff') }}">
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-3">
                                <label class="form-label">From</label>
                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" min="{{ date('Y-m-d') }}">
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
                                            {{ $driver['call_sign'] }} / {{ $driver['name'] ?? 'Unknown Driver' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!--<div class="col-md-2">-->
                            <!--    <label class="form-label">Account</label>-->
                            <!--    <select name="account_id" class="form-select">-->
                            <!--        <option value="">All</option>-->
                            <!--        @foreach($accounts as $account)-->
                            <!--            <option value="{{ $account['id'] }}" {{ request('account_id') == $account['id'] ? 'selected' : '' }}>-->
                            <!--                {{ $account['business_name'] }}-->
                            <!--            </option>-->
                            <!--        @endforeach-->
                            <!--    </select>-->
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
                        </div>
                        <div class="col-md-12 text-end mt-3">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                            </a>
                            <button type="submit" class="btn ms-2" style="background-color:#B87333; color:white;">
                                <i class="bi bi-search me-1"></i> Search
                            </button>
                        </div>
                    </form>
                    <p style="
    margin-top: 18px;
    margin-bottom: 0px;
">Note: Tap on Top Right Icon to Collapse the search</p>
                </div>
            </div>
        </div>
   </div>
    

    <!-- 🔹 LIVE DRIVER MAP -->
  <!--  <div class="col-lg-4 col-md-6">-->
  <!--    <div class="card shadow-sm border-0 h-100" style="-->
  <!--  border-radius: 16px;">-->
  <!--      <div class="card-header bg-light d-flex justify-content-between align-items-center">-->
  <!--        <h6 class="mb-0 text-dark">🗺️ Live Driver Map</h6>-->
  <!--        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#mapModal" title="Expand Map">-->
  <!--          <i class="bi bi-arrows-fullscreen"></i>-->
  <!--        </button>-->
  <!--      </div>-->
  <!--      <div class="card-body p-0">-->
  <!--        <div id="map" style="height: 300px;"></div>-->
  <!--      </div>-->
  <!--    </div>-->
  <!--  </div>-->
  <!--</div>-->
  <div class="col-lg-4 col-md-6">
  <div class="card shadow-sm border-0 h-100" style="border-radius: 8px;">
    
    <!-- ❌ Header removed -->

    <div class="card-body p-0 position-relative">
      
      <!-- Map -->
      <div id="map" style="height: 300px; border-radius: 8px;"></div>

      <!-- 🔳 Fullscreen Button (Bottom Left, Overlay) -->
      <button
        class="btn btn-sm btn-light shadow map-fullscreen-btn"
        data-bs-toggle="modal"
        data-bs-target="#mapModal"
        title="Expand Map"
      >
        <i class="bi bi-arrows-fullscreen"></i>
      </button>

    </div>
  </div>
</div>

  
  <div class="col-lg-12 mt-4">
       

        <div class="card border-0">
            <div class="card-header bg-warning bg-opacity-25"  >
                <h6 class="mb-0 text-dark">📋 Future Bookings<span class="ms-2">({{ $bookings->total() }})</span></h6>
            </div>
            <div class="table-responsive"  style="
    border-radius: 0px 0px 8px 8px;
    ">
                <table class="table table-hover align-middle mb-0">
                   <thead class="table-light">
    <tr>
        <th>Ref#</th>
        <th>Account Type</th>
        <th>Passenger</th>
        <th>Driver</th>
        <th>Phone</th>
        <th>Pickup</th>
        <th>Dropoff</th>
        <th>Vias</th>
        <th>Job Date</th>
        <th>Job Time</th>
        <th>Vehicle</th>
        <th>Flight</th>
        <th>Price</th>
        <th>Comments</th>
        <th>Status</th>
        <th>Plateform</th> <!-- ✅ New column -->
        <th>Actions</th>
    </tr>
</thead>
<tbody class="small">
@forelse($bookings as $booking)
    @php
        //$isHighPrice = ($booking['price'] ?? 0) > 50;
        //$rowStyle = $isHighPrice ? 'background-color: burlywood; color: #000;' : '';
        $isHighPrice   = ($booking['price'] ?? 0) > 50;
$isHidden      = ($booking['hidden'] ?? false) == true;   // or == 1 if your DB stores 0/1

// Row style priority: hidden → yellow, otherwise high price → burlywood
if ($isHidden) {
    $rowStyle = 'background-color: yellow; color: #000;';
} elseif ($isHighPrice) {
    $rowStyle = 'background-color: burlywood; color: #000;';
} else {
    $rowStyle = '';
}
       
$platform = (int) ($booking['platform'] ?? 1);
$partner = strtolower((string) ($booking['partner'] ?? ''));

// Nonstop AI bookings: new platform=3 and existing partner records
if ($platform === 3 || $partner === 'nonstop_ai') {
    $platformLabel = 'AI Call';
    $platformBadge = 'bg-info text-dark';
} else {
    switch ($platform) {
        case 0:
            $platformLabel = 'App';
            $platformBadge = 'bg-danger';
            break;

        case 2:
            $platformLabel = 'Admin';
            $platformBadge = 'bg-warning';
            break;

        case 1:
        default:
            $platformLabel = 'Web';
            $platformBadge = 'bg-primary';
            break;
    }
}

        // Status badge classes for styling the select dropdown
$statusBadges = [
    'pending'      => 'warning',   // yellow
    'accepted'     => 'info',      // light blue
    'declined'     => 'danger',    // red
    'onroute'      => 'primary',   // blue
    'arrived'      => 'success',   // green
    'pickedup'     => 'success',   // green
    'completed'    => 'primary',      // dark gray/black
    'job_cancelled'=> 'danger',    // red
    'no_show'      => 'secondary', // gray
];

$currentStatus = $booking['status'] ?? 'pending';
$statusBadgeClass = $statusBadges[$currentStatus] ?? 'warning';
$statusSelectStyle = $statusBadgeClass === 'secondary' 
    ? '' 
    : "background-color: var(--bs-{$statusBadgeClass}); color: var(--bs-black); border-color: var(--bs-{$statusBadgeClass});";

    @endphp
    <tr style="{{ $rowStyle }}">
        <td style="{{ $rowStyle }}">{{ $booking['ref_no'] ?? 'N/A' }}</td>
        @php
    $paymentType = strtolower($booking['payment_type'] ?? 'unknown');

    $paymentColors = [
        'cash'    => '#28a745', // green
        'card'    => '#0d6efd', // blue
        'account' => '#6f42c1', // purple
    ];

    $bgColor = $paymentColors[$paymentType] ?? '#6c757d'; // grey fallback
@endphp

<td style="{{ $rowStyle }}">
    <span class="badge rounded-pill px-3 py-2"
          style="background-color: {{ $bgColor }}; color: #fff;">
        {{ ucfirst($paymentType) }}
    </span>
</td>
        <td style="{{ $rowStyle }}">{{ $booking['passenger_name'] ?? 'N/A' }}</td>
        <!--<td style="{{ $rowStyle }}">-->
        <!--    @php-->
        <!--        $driver = collect($drivers)->firstWhere('id', $booking['driver_id'] ?? null);-->
        <!--    @endphp-->
        <!--    {{ $driver['name'] ?? '-' }}-->
        <!--</td>-->
        <td style="{{ $rowStyle }}">
    @php
        $driver = collect($drivers)->firstWhere('id', $booking['driver_id'] ?? null);
    @endphp

    @if($driver)
        <span class="badge rounded-pill bg-danger bg-opacity-90 px-3 py-2 me-1">
            {{ $driver['call_sign'] }}/{{ $driver['name'] }}
        </span>

    @else
        <span class="text-muted">-</span>
    @endif
</td>
        <td style="{{ $rowStyle }}">{{ $booking['phone_no'] ?? 'N/A' }}</td>
        <td style="{{ $rowStyle }}">{{ Str::limit($booking['pickup_address'], 30) }}</td>
        <td style="{{ $rowStyle }}">{{ Str::limit($booking['dropoff_address'], 30) }}</td>
        <td style="{{ $rowStyle }}">
    @if(!empty($booking['vias']))
        {{ implode(' → ', $booking['vias']) }}
    @else
        -
    @endif
</td>
        <td style="{{ $rowStyle }}">{{ \Carbon\Carbon::parse($booking['pickup_time'])->format('d M Y') ?? '-' }}</td>
        <td style="{{ $rowStyle }}">{{ \Carbon\Carbon::parse($booking['pickup_time'])->format('H:i') ?? '-' }}</td>
        <td style="{{ $rowStyle }}">
            @php
                $type = $booking['vehicle_make'] ?? '-';
                $badges = [
                    'Saloon' => 'primary',
                    'Estate' => 'success',
                    'MPV' => 'warning',
                    '8 Seater' => 'danger',
                    'Executive' => 'dark',
                ];
                $badgeClass = $badges[$type] ?? 'secondary';
            @endphp
            <span class="badge bg-{{ $badgeClass }}" style="font-size: 0.85rem;">{{ $type }}</span>
        </td>
        <td style="{{ $rowStyle }}">{{ $booking['flight_no'] ?? '-' }}</td>
        <td style="{{ $rowStyle }}">{{ $booking['price'] ?? '-' }}</td>
        <!--<td style="{{ $rowStyle }}">{{$booking['job_comment'] ?? '-'}}</td>-->
        <td style="{{ $rowStyle }}">
    {{ isset($booking['job_comment']) 
        ? Str::limit($booking['job_comment'], 20) 
        : 'No comment' }}
</td>

        <td style="{{ $rowStyle }}">
    <!-- Status Form -->
<form method="POST"
      action="{{ route('bookings.updateStatusManual', $booking['id']) }}"
      class="statusForm">
    @csrf

    <select class="form-select form-select-sm text-black statusSelect"
            name="status"
            style="{{ $statusSelectStyle }}; width:100px;"
            data-booking-id="{{ $booking['id'] }}">
        @php
            $statusOptions = [
                'pending'       => 'Pending',
                'accepted'      => 'Accepted',
                'declined'      => 'Declined',
                'onroute'       => 'On Route',
                'arrived'       => 'Arrived',
                'pickedup'      => 'Picked Up',
                'completed'     => 'Completed',
                'job_cancelled' => 'Job Cancelled',
                'no_show'       => 'No Show',
            ];
        @endphp
        @foreach($statusOptions as $value => $label)
            <option value="{{ $value }}"
                {{ ($booking['status'] ?? '') == $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
</form>

</td>
        <!-- ✅ Platform Badge -->
        <td style="{{ $rowStyle }}">
            <span class="badge {{ $platformBadge }}" style="font-size: 0.75rem;">
                {{ $platformLabel }}
            </span>
        </td>
        <td style="{{ $rowStyle }}">
            <div class="dropdown" style="position: static;">
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3">
                    @if(empty($booking['driver_id']))
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-secondary dispatch-driver-btn"
                           href="#"
                           data-booking-id="{{ $booking['id'] ?? '' }}"
                           data-bs-toggle="modal"
                           data-bs-target="#dispatchDriverModal">
                            <i class="bi bi-truck me-2"></i> Dispatch Driver
                        </a>
                    </li>
                    @endif
                    @if(!empty($booking['driver_id']))
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-primary" href="{{ route('bookings.track', $booking['id']) }}">
                            <i class="bi bi-geo-alt me-2"></i> Track Driver
                        </a>
                    </li>
                    @endif
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-success" href="{{ route('bookings.receipt', $booking['id']) }}">
                            <i class="bi bi-receipt me-2"></i> Receipt
                        </a>
                    </li>
                    <!--<li>-->
                    <!--    <a class="dropdown-item d-flex align-items-center text-primary" href="{{ route('bookings.duplicate', $booking['id']) }}">-->
                    <!--        <i class="bi bi-files me-2"></i> Duplicate Job-->
                    <!--    </a>-->
                    <!--</li>-->
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-warning" href="{{ route('bookings.route', $booking['id']) }}">
                            <i class="bi bi-map me-2"></i> Route
                        </a>
                    </li>
                    <!-- Create Return Job -->
    <li>
        <a class="dropdown-item d-flex align-items-center text-info" href="{{ route('bookings.return', $booking['id']) }}">
            <i class="bi bi-arrow-repeat me-2"></i> Create Return Job
        </a>
    </li>
    <!-- Send Confirmation Email -->
<!-- Send Confirmation Email -->
<li>
    <a class="dropdown-item d-flex align-items-center text-success send-confirmation-email-btn"
       href="#"
       data-booking-id="{{ $booking['id'] ?? '' }}"
       data-booking-ref="{{ $booking['ref_no'] ?? '' }}"
       data-passenger-email="{{ $booking['email'] ?? '' }}"
       data-passenger-name="{{ $booking['passenger_name'] }}">
        <i class="bi bi-envelope me-2"></i> Send Confirmation Email
    </a>
</li>
<!--<a class="dropdown-item d-flex align-items-center text-warning send-confirmation-sms-btn"-->
<!-- href="#"-->
<!-- data-booking-id="{{ $booking['ref_no'] ?? '' }}">-->
<a class="dropdown-item d-flex align-items-center text-warning send-confirmation-sms-btn"
   href="#"
   data-booking-id="{{ $booking['ref_no'] ?? '' }}"
   data-phone="{{ $booking['phone_no'] ?? '' }}"
   data-name="{{ $booking['passenger_name'] ?? '' }}"
   data-date="{{ \Carbon\Carbon::parse($booking['pickup_time'])->format('d M Y') }}"
   data-time="{{ \Carbon\Carbon::parse($booking['pickup_time'])->format('H:i') }}"
   data-vehicle="{{ $booking['vehicle_make'] ?? '' }}"
   data-price="{{ $booking['price'] ?? '' }}"
   data-payment="{{ ucfirst($booking['payment_type'] ?? '') }}"
   data-pickup="{{ $booking['pickup_address'] ?? '' }}"
   data-dropoff="{{ $booking['dropoff_address'] ?? '' }}"
   data-flight_no="{{ $booking['flight_no'] ?? '' }}"
   data-via="{{ !empty($booking['vias']) ? implode(' → ', $booking['vias']) : '-' }}">
 <i class="bi bi-chat-left-text me-2"></i> Send Confirmation SMS
</a>
    <!-- Recall Job -->
    <li>
        <a class="dropdown-item d-flex align-items-center text-danger recall-job-btn" href="#"
           data-booking-id="{{ $booking['id'] ?? '' }}">
            <i class="bi bi-arrow-counterclockwise me-2"></i> Recall Job
        </a>
    </li>
    <!-- Hide Job -->
    <li>
        <a class="dropdown-item d-flex align-items-center text-secondary hide-job-btn" href="#"
           data-booking-id="{{ $booking['id'] ?? '' }}">
            <i class="bi bi-eye-slash me-2"></i> Hide Job
        </a>
    </li>
    <!-- Edit Booking -->
    <li>
        <a class="dropdown-item d-flex align-items-center text-primary" href="{{ route('bookings.edit', $booking['id']) }}">
            <i class="bi bi-pencil-square me-2"></i> Edit Booking
        </a>
    </li>
    <!-- View Booking -->
<li>
    <a class="dropdown-item d-flex align-items-center text-info view-booking-btn"
       href="#"
       data-bs-toggle="modal"
       data-bs-target="#viewBookingModal"
       data-booking='@json($booking)'>
        <i class="bi bi-eye me-2"></i> View Booking
    </a>
</li>
                </ul>
        
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="17" class="text-center text-muted py-4">No future bookings found.</td>
    </tr>
@endforelse
</tbody>


                </table>
<div class="justify-content-center mt-4">
    {{ $bookings->links('pagination::bootstrap-5') }}
</div>

            </div>
        </div>
<!-- Confirmation Modal -->
<!-- Status Confirmation Modal -->
<div class="modal fade" id="statusConfirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold" id="statusModalTitle"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body text-center px-4">
        <div id="statusIcon" class="mb-3 fs-1"></div>
        <p id="statusModalMessage" class="fs-6 text-muted"></p>
      </div>

      <div class="modal-footer border-0 justify-content-center">
        <button type="button" class="btn btn-light px-4" id="cancelStatusBtn">
          Cancel
        </button>
        <button type="button" class="btn px-4" id="confirmStatusBtn">
          Confirm
        </button>
      </div>

    </div>
  </div>
</div>

    </div>
</div>
<form id="recallJobForm" method="POST" style="display:none;">
    @csrf
</form>

<!-- Send SMS Modal -->
<div class="modal fade" id="sendSmsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning bg-opacity-25">
                <h6 class="modal-title">Send Confirmation SMS</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="smsBookingId">

                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" id="smsPhone" class="form-control" placeholder="+447123456789">
                </div>

                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea id="smsMessage" class="form-control" rows="3" ></textarea>
                </div>

                <button class="btn btn-warning w-100" id="sendSmsNowBtn">
                    Send SMS
                </button>
            </div>
        </div>
    </div>
</div>




<!-- Booking Details Modal -->
<div class="modal fade" id="viewBookingModal" tabindex="-1" aria-labelledby="viewBookingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning bg-opacity-25">
        <h5 class="modal-title" id="viewBookingModalLabel">Booking Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-borderless mb-0">
          <tbody>
            <tr>
              <th>Ref No:</th>
              <td id="modal-ref_no"></td>
            </tr>
            <tr>
              <th>Passenger Name:</th>
              <td id="modal-passenger_name"></td>
            </tr>
            <tr>
              <th>Phone:</th>
              <td id="modal-phone_no"></td>
            </tr>
            <tr>
              <th>Email:</th>
              <td id="modal-email"></td>
            </tr>
            <tr>
              <th>Pickup:</th>
              <td id="modal-pickup_address"></td>
            </tr>
            <tr>
              <th>Dropoff:</th>
              <td id="modal-dropoff_address"></td>
            </tr>
            <tr>
              <th>Via Addresses:</th>
              <td id="modal-vias"></td>
            </tr>
            <tr>
              <th>Pickup Time:</th>
              <td id="modal-pickup_time"></td>
            </tr>
            <tr>
              <th>Flight No:</th>
              <td id="modal-flight_no"></td>
            </tr>
            <tr>
              <th>Vehicle:</th>
              <td id="modal-vehicle_id"></td>
            </tr>
            <tr>
              <th>Price:</th>
              <td id="modal-price"></td>
            </tr>
            <tr>
              <th>Status:</th>
              <td id="modal-status"></td>
            </tr>
            <tr>
              <th>Comment:</th>
              <td id="modal-comment"></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
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
                <input type="hidden" id="emailBookingRef">
                

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

    

<!-- 🚗 Dispatch Driver Modal -->
<div class="modal fade" id="dispatchDriverModal" tabindex="-1" aria-labelledby="dispatchDriverLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h6 class="modal-title" id="dispatchDriverLabel">Dispatch Driver</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="dispatchDriverForm">
            @csrf
            <input type="hidden" name="booking_id" id="dispatchBookingId">
            
            <div class="mb-3">
                <label for="driverSelect" class="form-label fw-bold">Select Driver</label>
                <select class="form-select" id="driverSelect" name="driver_id" required>
                    <option value="" selected disabled>-- Choose Driver --</option>
                    @foreach($drivers as $key => $driver)
                        <option value="{{ $driver['id'] }}">{{$driver['call_sign'] ?? 'No Call Driver'}} / {{ $driver['name'] ?? 'Unnamed Driver' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">Dispatch</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

    


<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="mapModalLabel">Expanded Live Map</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="mapExpanded" style="height: 75vh;"></div>
            </div>
        </div>
    </div>
</div>


<div
    id="callSnackbar"
    class="call-snackbar"
    role="status"
    aria-live="assertive"
    aria-atomic="true"
>
    <div class="call-snackbar__icon">
        <i class="bi bi-telephone-fill"></i>
    </div>

    <div class="call-snackbar__content">
        <strong id="callSnackbarTitle">New call</strong>
        <span id="callSnackbarDetails"></span>
        <small id="callSnackbarTime"></small>
    </div>
    
    <button
    type="button"
    id="callSnackbarView"
    class="call-snackbar__view"
>
    View
</button>

    <button
        type="button"
        id="callSnackbarClose"
        class="call-snackbar__close"
        aria-label="Dismiss notification"
    >
        <i class="bi bi-x-lg"></i>
    </button>
</div>

<style>
.call-snackbar {
    position: fixed;
    z-index: 1090;
    left: 50%;
    bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    width: min(460px, calc(100vw - 32px));
    padding: 14px 16px;
    color: #fff;
    background: #17202a;
    border-left: 5px solid #20c997;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, .28);
    opacity: 0;
    pointer-events: none;
    transform: translate(-50%, 24px);
    transition: opacity .2s ease, transform .2s ease;
}

.call-snackbar.is-visible {
    opacity: 1;
    pointer-events: auto;
    transform: translate(-50%, 0);
}

.call-snackbar__view {
    flex: 0 0 auto;
    padding: 7px 14px;
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    background: #198754;
    border: 1px solid #20c997;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color .2s ease;
}

.call-snackbar__view:hover {
    color: #fff;
    background: #157347;
}

.call-snackbar__icon {
    display: grid;
    flex: 0 0 42px;
    width: 42px;
    height: 42px;
    place-items: center;
    background: #198754;
    border-radius: 50%;
    font-size: 1.1rem;
}

.call-snackbar__content {
    display: flex;
    flex: 1;
    min-width: 0;
    flex-direction: column;
    line-height: 1.35;
}

.call-snackbar__content span,
.call-snackbar__content small {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.call-snackbar__content small {
    margin-top: 2px;
    color: rgba(255, 255, 255, .72);
}

.call-snackbar__close {
    padding: 8px;
    color: #fff;
    background: transparent;
    border: 0;
    opacity: .75;
}

.call-snackbar__close:hover {
    opacity: 1;
}
</style>


@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>






<script>
document.addEventListener('DOMContentLoaded', function () {
  const collapseEl = document.getElementById('filtersCollapse');
  const chevron = document.querySelector('.filter-chevron');

  collapseEl.addEventListener('show.bs.collapse', () => {
    chevron.classList.remove('bi-chevron-down');
    chevron.classList.add('bi-chevron-up');
  });

  collapseEl.addEventListener('hide.bs.collapse', () => {
    chevron.classList.remove('bi-chevron-up');
    chevron.classList.add('bi-chevron-down');
  });
});
</script>



<script>
document.addEventListener('DOMContentLoaded', function () {
    
    console.log('Status script loaded');


    document.querySelectorAll('.statusSelect').forEach(select => {

    let previousStatus = select.value;

    select.addEventListener('change', function () {

        const form = this.closest('.statusForm');
        const selected = this.value;

        const modalEl = document.getElementById('statusConfirmModal');
        const modal = new bootstrap.Modal(modalEl);

        const title = document.getElementById('statusModalTitle');
        const message = document.getElementById('statusModalMessage');
        const icon = document.getElementById('statusIcon');
        const confirmBtn = document.getElementById('confirmStatusBtn');
        const cancelBtn = document.getElementById('cancelStatusBtn');

        let requiresConfirm = false;

        // Reset styles
        confirmBtn.className = 'btn px-4';

        if (selected === 'completed') {
            requiresConfirm = true;
            title.innerText = 'Confirm Completion';
            message.innerText = 'This job still has remaining time. Are you sure you want to mark it as completed?';
            icon.innerHTML = '⏳';
            confirmBtn.classList.add('btn-success');
        }

        if (selected === 'declined') {
            requiresConfirm = true;
            title.innerText = 'Confirm Decline';
            message.innerText = 'Declining this booking cannot be undone. Proceed?';
            icon.innerHTML = '⚠️';
            confirmBtn.classList.add('btn-danger');
        }

        if (selected === 'no_show') {
            requiresConfirm = true;
            title.innerText = 'Confirm No Show';
            message.innerText = 'Are you sure you want to mark this booking as No Show?';
            icon.innerHTML = '🚫';
            confirmBtn.classList.add('btn-warning');
        }
        
        if (selected === 'job_cancelled') {
            requiresConfirm = true;
            title.innerText = 'Confirm Cancelled';
            message.innerText = 'Are you sure you want to mark this booking as Cancelled?';
            icon.innerHTML = '🚫';
            confirmBtn.classList.add('btn-warning');
        }

        if (requiresConfirm) {
            modal.show();

            confirmBtn.onclick = () => {
                modal.hide();
                form.submit();
            };

            cancelBtn.onclick = () => {
                modal.hide();
                this.value = previousStatus;
                form.submit();
            };
        } else {
            form.submit();
        }
        
        console.log(this.value);
        console.log(previousStatus);
        

        previousStatus = selected;
    });
});

});
</script>



<script>
document.addEventListener('DOMContentLoaded', function () {
    const viewButtons = document.querySelectorAll('.view-booking-btn');
    
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const booking = JSON.parse(this.dataset.booking);

            document.getElementById('modal-ref_no').textContent = booking.ref_no ?? '-';
            document.getElementById('modal-passenger_name').textContent = booking.passenger_name ?? '-';
            document.getElementById('modal-phone_no').textContent = booking.phone_no ?? '-';
            document.getElementById('modal-email').textContent = booking.email ?? '-';
            document.getElementById('modal-pickup_address').textContent = booking.pickup_address ?? '-';
            document.getElementById('modal-dropoff_address').textContent = booking.dropoff_address ?? '-';
            document.getElementById('modal-vias').textContent = booking.vias ? booking.vias.join(', ') : '-';
           document.getElementById('modal-pickup_time').textContent =
    "{{ isset($booking['pickup_time']) 
        ? \Carbon\Carbon::parse($booking['pickup_time'])->format('d M Y H:i') 
        : '-' }}";
            document.getElementById('modal-flight_no').textContent = booking.flight_no ?? '-';
            document.getElementById('modal-vehicle_id').textContent = booking.vehicle_make ?? '-';
            document.getElementById('modal-price').textContent = booking.price ?? '-';
            document.getElementById('modal-status').textContent = booking.status ?? '-';
            document.getElementById('modal-comment').textContent = booking.job_comment ?? '-';
        });
    });
});
</script>

<script>
    var map;
    var expandedMap;
    var markers = {};
    var expandedMarkers = {};
    
    
    // 🇬🇧 UK Bounds
// 🇬🇧 Adjusted UK Bounds
const ukBounds = {
    north: 55.9,
    south: 50.5, // Increased from 49.8 to hide the extra ocean
    west: -3.4,
    east: 2.1
};

    function initFirebase() {
        const firebaseConfig = {
            apiKey: "AIzaSyDf9Ujb0fAaE7CWAtkb5qCUJn5RDsRvPAQ",
            authDomain: "crown-carz.firebaseapp.com",
            databaseURL: "https://crown-carz-default-rtdb.firebaseio.com",
            projectId: "crown-carz",
            storageBucket: "crown-carz.firebasestorage.app",
            messagingSenderId: "854843072109",
            appId: "1:854843072109:web:dd5ff34f1ed03521d5a964",
            measurementId: "G-VCG3S5E63H"
        };
        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
        }
        const db = firebase.database();
        const driverRef = db.ref("drivers");
        driverRef.on("value", (snapshot) => {
            const drivers = snapshot.val();
            showDriversOnMap(drivers, map, markers);
            if (expandedMap) {
                 showDriversOnMap(drivers, expandedMap, expandedMarkers);
            }
        });
    }



// ... inside your code ...

function initMap() {
    // Increased the latitude to shift the default camera view further North
    const ukCenter = { lat: 52.849586, lng: -1.221341 }; 

    map = new google.maps.Map(document.getElementById("map"), {
        center: ukCenter,
        zoom: 11,
        minZoom: 7,
        zoomControl: true,
        disableDefaultUI: true,
        restriction: {
            latLngBounds: ukBounds,
            strictBounds: false
        }
    });

    expandedMap = new google.maps.Map(document.getElementById("mapExpanded"), {
        center: ukCenter,
        zoom: 7, // Consider slightly increasing this from 6 to 7 to crop out the sea
        minZoom: 5,
        // restriction: {
        //     latLngBounds: ukBounds,
        //     strictBounds: false
        // }
    });

    initFirebase();
}
    

    function showDriversOnMap(drivers, targetMap, markerCache) {
    if (!drivers || !targetMap) return;

    let visibleMarkers = {};
    const bounds = new google.maps.LatLngBounds();

    for (const id in drivers) {
        const driver = drivers[id];
        if (!driver.latitude || !driver.longitude) continue;

        const pos = {
            lat: parseFloat(driver.latitude),
            lng: parseFloat(driver.longitude)
        };

        visibleMarkers[id] = true;
        bounds.extend(pos); // 🔥 bounds me add

        if (markerCache[id]) {
            markerCache[id].setPosition(pos);
            markerCache[id].setIcon(getMarkerIcon(driver.status));
        } else {
           const marker = new google.maps.Marker({
  position: pos,
  map: targetMap,
  title: driver.name || "Unnamed Driver",
  icon: {
    url: "https://crowncarz.com/admin/public/images/Car.png",
    scaledSize: new google.maps.Size(100, 100),   // 👈 REDUCE SIZE
    // anchor: new google.maps.Point(40, 90)       // 👈 BOTTOM CENTER
  }
});


    const info = new google.maps.InfoWindow({
  content: `
    <div class="driver-infowindow">
      <div class="driver-name">${driver.name || "Unnamed Driver"}</div>
      <div class="driver-status">
        Status: <strong>${driver.status || "N/A"}</strong>
      </div>
      <div class="driver-phone">
        📞 ${driver.phone || "N/A"}
      </div>
    </div>
  `,
  disableAutoPan: false
});



            // 🔍 CLICK → ZOOM ON DRIVER
            marker.addListener("click", () => {
                targetMap.panTo(pos);
                targetMap.setZoom(16); // 👈 zoom level on click
                info.open(targetMap, marker);
            });

            markerCache[id] = marker;
        }
    }

    // 🧹 Remove disappeared markers
    for (const id in markerCache) {
        if (!visibleMarkers[id]) {
            markerCache[id].setMap(null);
            delete markerCache[id];
        }
    }

    // 🎯 AUTO FIT ALL DRIVERS (only if >1)
    const markerCount = Object.keys(visibleMarkers).length;
    if (markerCount > 1) {
        targetMap.fitBounds(bounds);
    } else if (markerCount === 1) {
        targetMap.setCenter(bounds.getCenter());
        targetMap.setZoom(14);
    }
}


   function getMarkerIcon(status = "") {
    return {
        url: "https://crowncarz.com/admin/public/images/Car.png",

        // 🚗 Proper car ratio
        scaledSize: new google.maps.Size(180, 200),

        // 🎯 Center anchor
        // anchor: new google.maps.Point(24, 12)
    };
}


    
    var mapModal = document.getElementById('mapModal')
    mapModal.addEventListener('shown.bs.modal', function () {
        const center = map.getCenter();
        expandedMap.setCenter(center);
        google.maps.event.trigger(expandedMap, "resize"); 
    })

    window.initMap = initMap;
    
    
</script>



<script>
document.addEventListener("DOMContentLoaded", function () {

    /* ----------------------
       SMS BUTTON CLICK
    ---------------------- */
    // document.querySelectorAll(".send-confirmation-sms-btn").forEach(btn => {
    //     btn.addEventListener("click", function (e) {
    //         e.preventDefault();

    //         let bookingId = this.getAttribute("data-booking-id");
    //         let row = this.closest("tr");
    //         let phone = row.querySelector("td:nth-child(5)").innerText.trim();

    //         document.getElementById("smsBookingId").value = bookingId;
    //         document.getElementById("smsPhone").value = phone;
    //         document.getElementById("smsMessage").value = "Your booking has been confirmed. Ref# " + bookingId;

    //         let modal = new bootstrap.Modal(document.getElementById("sendSmsModal"));
    //         modal.show();
    //     });
    // });
//     document.querySelectorAll(".send-confirmation-sms-btn").forEach(btn => {
//     btn.addEventListener("click", function (e) {
//         e.preventDefault();

//         let bookingId = this.getAttribute("data-booking-id");
//         let row = this.closest("tr");

//         let phone = row.querySelector("td:nth-child(5)").innerText.trim();
//         let passengerName = row.querySelector("td:nth-child(3)").innerText.trim();
//         let pickupDate = row.querySelector("td:nth-child(9)").innerText.trim();
//         let pickupTime = row.querySelector("td:nth-child(10)").innerText.trim();
//         let vehicleType = row.querySelector("td:nth-child(11)").innerText.trim();
//         let price = row.querySelector("td:nth-child(13)").innerText.trim();
//         let payment = row.querySelector("td:nth-child(2)").innerText.trim();
//         let pickup = row.querySelector("td:nth-child(6)").innerText.trim();
//         let dropoff = row.querySelector("td:nth-child(7)").innerText.trim();
//         let via = row.querySelector("td:nth-child(8)").innerText.trim();

//         // Set basic fields
//         document.getElementById("smsBookingId").value = bookingId;
//         document.getElementById("smsPhone").value = phone;

//         // Message Template
//         let messageTemplate = `Dear {caller},

// Please find booking confirmation for job reference: {job_ref}
// Job Date: {job_date}
// Job Time: {job_time}
// Phone No: {mobile}
// Pickup: {pickup}
// Dropoff: {dropoff}
// Via: {via}
// Vehicle Type: {vehicle_type}
// Total Fare: {fare}
// Payment Type: {payment}

// Kind Regards,
// Crown Carz Ltd.
// Tel: +44(0)1189 47 47 47
// Email: info@crowncarz.com
// Website: www.crowncarz.com`;

//         // Replace placeholders
//         let finalMessage = messageTemplate
//             .replace("{caller}", passengerName)
//             .replace("{job_ref}", bookingId)
//             .replace("{job_date}", pickupDate)
//             .replace("{job_time}", pickupTime)
//             .replace("{mobile}", phone)
//             .replace("{vehicle_type}", vehicleType)
//             .replace("{fare}", price)
//             .replace("{payment}", payment)
//             .replace("{pickup}", pickup)
//             .replace("{dropoff}", dropoff)
//             .replace("{via}", via);

//         document.getElementById("smsMessage").value = finalMessage;

//         let modal = new bootstrap.Modal(document.getElementById("sendSmsModal"));
//         modal.show();
//     });
// });
document.querySelectorAll(".send-confirmation-sms-btn").forEach(btn => {
    btn.addEventListener("click", function (e) {
        e.preventDefault();

        let bookingId   = this.dataset.bookingId;
        let phone       = this.dataset.phone;
        let passengerName = this.dataset.name;
        let pickupDate  = this.dataset.date;
        let pickupTime  = this.dataset.time;
        let vehicleType = this.dataset.vehicle;
        let price       = this.dataset.price;
        let payment     = this.dataset.payment;
        let pickup      = this.dataset.pickup;
        let dropoff     = this.dataset.dropoff;
        let via         = this.dataset.via;
        let flight_no         = this.dataset.flight_no;

        document.getElementById("smsBookingId").value = bookingId;
        document.getElementById("smsPhone").value = phone;

        let messageTemplate = `Dear ${passengerName},

Please find booking confirmation for job reference: ${bookingId}
Job Date: ${pickupDate}
Job Time: ${pickupTime}
Phone No: ${phone}
Pickup: ${pickup}
Dropoff: ${dropoff}
Via: ${via}
Flight No: ${flight_no}
Vehicle Type: ${vehicleType}
Total Fare: ${price}
Payment Type: ${payment}

Kind Regards,
Crown Carz Ltd.
Tel: +44(0)1189 47 47 47
Email: info@crowncarz.com
Website: www.crowncarz.com`;

        document.getElementById("smsMessage").value = messageTemplate;

        let modal = new bootstrap.Modal(document.getElementById("sendSmsModal"));
        modal.show();
    });
});

    document.getElementById("sendSmsNowBtn").addEventListener("click", function () {
        let bookingId = document.getElementById("smsBookingId").value;
        let phone = document.getElementById("smsPhone").value;
        let message = document.getElementById("smsMessage").value;

        if (!phone.trim()) { alert("Please enter phone number"); return; }

        fetch("/admin/bookings/send-sms", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ booking_id: bookingId, phone: phone, message: message })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                alert("SMS sent successfully!");
                bootstrap.Modal.getInstance(document.getElementById("sendSmsModal")).hide();
            } else {
                alert("SMS failed to send.");
            }
        })
        .catch(err => {
            console.error(err);
            alert("Error sending SMS.");
        });
    });


    /* ----------------------
       EMAIL BUTTON CLICK
    ---------------------- */
    document.querySelectorAll(".send-confirmation-email-btn").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();

            // Function body within an event listener
let bookingId = this.getAttribute("data-booking-id");
let bookingRef = this.getAttribute("data-booking-ref");
let row = this.closest("tr");

// 1. Get the Passenger Name from the row (assuming it's in the second column)
// This is used for the greeting in the message.
let passengerName = row.querySelector("td:nth-child(3)").innerText.trim();

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
document.getElementById("emailMessage").value = "Hello " + passengerName + ", your booking has been confirmed. Ref# " + bookingRef;

            let modal = new bootstrap.Modal(document.getElementById("sendEmailModal"));
            modal.show();
        });
    });

    document.getElementById("sendEmailNowBtn").addEventListener("click", function () {
        let bookingId = document.getElementById("emailBookingId").value;
        let email = document.getElementById("emailAddress").value;
        let message = document.getElementById("emailMessage").value;

        if (!email.trim()) { alert("Please enter email"); return; }

        fetch("/admin/bookings/send-email", {
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

});
</script>






<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmaqBQNz2RLPPwXl4hcQwELLgzwwbBbNA&callback=initMap&loading=async"></script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const dispatchModal = document.getElementById('dispatchDriverModal');
    const bookingIdField = document.getElementById('dispatchBookingId');
    const form = document.getElementById('dispatchDriverForm');

    // Set booking ID when modal opens
    dispatchModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const bookingId = button.getAttribute('data-booking-id');
        bookingIdField.value = bookingId;
    });

    // Handle dispatch form submission
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const button = form.querySelector('button[type="submit"]');
        button.disabled = true;
        button.innerText = 'Dispatching...';

        let data;

        try {
            const response = await fetch("{{ route('booking.dispatchDriver') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: new FormData(form)
            });

            try {
                data = await response.json();
            } catch (jsonError) {
                console.error("Failed to parse server response as JSON.", jsonError);
            }

            if (response.ok && data?.success) {
                alert('✅ Driver dispatched successfully!');
                const modal = bootstrap.Modal.getOrCreateInstance(dispatchModal);
                modal.hide();

                // Add a short delay before reload to allow the modal to close smoothly
                setTimeout(() => window.location.reload(), 500);

            } else {
                let errorMessage = 'Failed to dispatch driver. Unknown error.';

                if (data?.message) {
                    errorMessage = data.message;
                } else if (data?.errors) {
                    errorMessage = Object.values(data.errors).flat().join('\n');
                } else if (!response.ok) {
                    errorMessage = `Server Error: ${response.status} ${response.statusText}`;
                }

                alert('❌ ' + errorMessage);
            }

        } catch (err) {
            console.error('Fetch error:', err);
            alert('⚠️ Network connection error. Please check your internet.');
        
        } finally {
            button.disabled = false;
            button.innerText = 'Dispatch';
        }
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {

    // Select all Recall Job buttons in the table
    document.querySelectorAll(".recall-job-btn").forEach(btn => {
        btn.addEventListener("click", function(e) {
            e.preventDefault();

            const bookingId = this.getAttribute("data-booking-id");
            if (!bookingId) return alert("Booking ID not found.");

            // Optional confirmation
            if (!confirm("Are you sure you want to recall this job?")) return;

            const form = document.getElementById("recallJobForm");
            form.action = `https://crowncarz.com/admin/bookings/${bookingId}/recall`; // Update route prefix if different
            form.submit();
        });
    });

});
    
    
document.addEventListener("DOMContentLoaded", function() {

    document.querySelectorAll(".hide-job-btn").forEach(btn => {
        btn.addEventListener("click", function(e) {
            e.preventDefault();

            const bookingId = this.getAttribute("data-booking-id");
            if (!bookingId) return alert("Booking ID not found!");

            if (!confirm("Are you sure you want to hide this booking?")) return;

            fetch(`https://crowncarz.com/admin/bookings/${bookingId}/hide`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    alert(data.message);
                    // Optionally hide the row in the table
                    const row = btn.closest("tr");
                    row.style.display = "none";
                } else {
                    alert("Failed to hide booking");
                }
            })
            .catch(err => {
                console.error(err);
                alert("Error hiding booking");
            });
        });
    });

});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    // Save Metrics to LocalStorage
    localStorage.setItem("totalUsers", "{{ $totalUsers ?? 0 }}");
    localStorage.setItem("todaysRevenue", "{{ $todaysRevenue ?? 0 }}");

    localStorage.setItem("revenueBreakdown", JSON.stringify({
        Cash: "{{ $revenueBreakdown['Cash'] ?? 0 }}",
        Card: "{{ $revenueBreakdown['Card'] ?? 0 }}",
        Account: "{{ $revenueBreakdown['Account'] ?? 0 }}"
    }));

    console.log("Dashboard metrics saved to LocalStorage successfully.");
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const snackbar = document.getElementById('callSnackbar');
    const title = document.getElementById('callSnackbarTitle');
    const details = document.getElementById('callSnackbarDetails');
    const time = document.getElementById('callSnackbarTime');
    const closeButton = document.getElementById('callSnackbarClose');
    
    const viewButton = document.getElementById('callSnackbarView');

const bookingCreateUrl = @json(
    route('booking.create')
);

const bookingPhoneStorageKey =
    'callswitch:booking-phone';

let selectedCallPhone = '';

    const latestCallUrl = @json(
        \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'call-notifications.latest',
            now()->addHours(12)
        )
    );

    const seenCallStorageKey = 'callswitch:last-seen-call';

    let hideTimer;
    let pollingStopped = false;

    function hideSnackbar() {
        snackbar.classList.remove('is-visible');
    }

    // function showSnackbar(call) {
    //     const callType = String(
    //         call.call_type || ''
    //     ).toLowerCase();

    //     title.textContent = callType === 'inbound'
    //         ? 'Incoming call'
    //         : (
    //             callType === 'outbound'
    //                 ? 'Outgoing call'
    //                 : 'New call'
    //         );

    //     details.textContent =
    //         `${call.from || 'Unknown'} → ${call.to || 'Unknown'}`;

    //     time.textContent = call.start
    //         ? `Started: ${call.start}`
    //         : '';

    //     snackbar.classList.add('is-visible');

    //     window.clearTimeout(hideTimer);

    //     hideTimer = window.setTimeout(
    //         hideSnackbar,
    //         12000
    //     );
    // }


    function showSnackbar(call) {
    const callType = String(
        call.call_type || ''
    ).toLowerCase();

    selectedCallPhone = callType === 'outbound'
        ? String(call.to || '')
        : String(call.from || '');

    title.textContent = callType === 'inbound'
        ? 'Incoming call'
        : (
            callType === 'outbound'
                ? 'Outgoing call'
                : 'New call'
        );

    details.textContent =
        `${call.from || 'Unknown'} → ${call.to || 'Unknown'}`;

    time.textContent = call.start
        ? `Started: ${call.start}`
        : '';

    snackbar.classList.add('is-visible');

    window.clearTimeout(hideTimer);

    hideTimer = window.setTimeout(
        hideSnackbar,
        12000
    );
}
    
    async function pollLatestCall() {
        if (pollingStopped) {
            return;
        }

        try {
            const response = await fetch(latestCallUrl, {
                headers: {
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                cache: 'no-store'
            });

            if (
                response.status === 401 ||
                response.status === 403
            ) {
                pollingStopped = true;
                return;
            }

            if (response.ok) {
                const data = await response.json();
                const call = data.call;

                const lastSeenCall =
                    sessionStorage.getItem(
                        seenCallStorageKey
                    );

                if (
                    call &&
                    call.id &&
                    call.id !== lastSeenCall
                ) {
                    sessionStorage.setItem(
                        seenCallStorageKey,
                        call.id
                    );

                    showSnackbar(call);
                }
            }
        } catch (error) {
            console.warn(
                'Call notification check failed.',
                error
            );
        } finally {
            if (!pollingStopped) {
                window.setTimeout(
                    pollLatestCall,
                    3000
                );
            }
        }
    }

    closeButton.addEventListener(
        'click',
        hideSnackbar
    );
    
    viewButton.addEventListener('click', function () {
    if (selectedCallPhone !== '') {
        sessionStorage.setItem(
            bookingPhoneStorageKey,
            selectedCallPhone
        );
    }

    window.location.href = bookingCreateUrl;
});

    pollLatestCall();
});
</script>
  


@endpush