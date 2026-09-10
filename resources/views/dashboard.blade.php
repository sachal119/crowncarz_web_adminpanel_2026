@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
  /* 🌟 Custom Modern Flatpickr Calendar Styling */
  .flatpickr-calendar {
    border-radius: 16px !important;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.18), 0 4px 12px rgba(0,0,0,0.08) !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    font-family: inherit !important;
    padding: 0 0 10px 0 !important;
    background: #ffffff !important;
    overflow: hidden !important;
  }
  .flatpickr-calendar .flatpickr-months {
    background: linear-gradient(135deg, #111827 0%, #1e293b 100%) !important;
    padding: 10px 8px !important;
    position: relative !important;
    border-radius: 14px 14px 0 0 !important;
  }
  .flatpickr-calendar .flatpickr-month {
    color: #ffffff !important;
    fill: #ffffff !important;
    height: 40px !important;
  }
  .flatpickr-calendar .flatpickr-current-month {
    padding-top: 4px !important;
    font-size: 15px !important;
    color: #ffffff !important;
  }
  .flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months,
  .flatpickr-calendar .flatpickr-current-month input.cur-year {
    color: #ffffff !important;
    font-weight: 700 !important;
    font-size: 15px !important;
  }
  .flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months {
    background: #111827 !important;
  }
  .flatpickr-calendar .flatpickr-prev-month,
  .flatpickr-calendar .flatpickr-next-month {
    color: #E6B04A !important;
    fill: #E6B04A !important;
    padding: 8px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
  }
  .flatpickr-calendar .flatpickr-prev-month:hover,
  .flatpickr-calendar .flatpickr-next-month:hover {
    background: rgba(255, 255, 255, 0.15) !important;
    color: #ffffff !important;
    fill: #ffffff !important;
  }
  .flatpickr-calendar .flatpickr-weekdays {
    background: #0f172a !important;
    padding: 4px 0 !important;
  }
  .flatpickr-calendar span.flatpickr-weekday {
    color: #E6B04A !important;
    font-weight: 700 !important;
    font-size: 12px !important;
    letter-spacing: 0.5px !important;
  }
  .flatpickr-calendar .flatpickr-days {
    padding: 8px 10px 4px 10px !important;
  }
  .flatpickr-calendar .dayContainer {
    justify-content: space-around !important;
  }
  .flatpickr-calendar .flatpickr-day {
    border-radius: 8px !important;
    font-weight: 500 !important;
    color: #1e293b !important;
    height: 38px !important;
    line-height: 38px !important;
    max-width: 38px !important;
    margin: 2px !important;
    font-size: 13.5px !important;
  }
  .flatpickr-calendar .flatpickr-day.selected, 
  .flatpickr-calendar .flatpickr-day.startRange, 
  .flatpickr-calendar .flatpickr-day.endRange {
    background: linear-gradient(135deg, #B87333 0%, #d48b48 100%) !important;
    border-color: #B87333 !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    box-shadow: 0 4px 12px rgba(184, 115, 51, 0.4) !important;
  }
  .flatpickr-calendar .flatpickr-day.inRange {
    background: rgba(184, 115, 51, 0.15) !important;
    border-color: transparent !important;
    color: #8c531b !important;
  }
  .flatpickr-calendar .flatpickr-day.today {
    border-color: #E6B04A !important;
    background: rgba(230, 176, 74, 0.12) !important;
    font-weight: 700 !important;
  }
  .flatpickr-calendar .flatpickr-day:hover:not(.selected):not(.startRange):not(.endRange) {
    background: rgba(230, 176, 74, 0.25) !important;
    border-color: transparent !important;
    color: #111827 !important;
  }
  .flatpickr-calendar:before, .flatpickr-calendar:after {
    display: none !important;
  }

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

/* === Pie Chart === */
#revenuePieChart {
  max-height: 240px;
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

.table-responsive {
    overflow: visible !important;
}

/* 🌟 Realtime Dashboard Live Animations */
@keyframes newBookingFlash {
    0% {
        background-color: #d1e7dd !important;
        box-shadow: inset 0 0 10px rgba(25, 135, 84, 0.4);
    }
    40% {
        background-color: #fff3cd !important;
    }
    100% {
        background-color: inherit;
        box-shadow: none;
    }
}

.new-booking-highlight {
    animation: newBookingFlash 4s ease-out forwards;
}

.booking-table-row {
    transition: all 0.4s ease;
}

.row-fade-out {
    opacity: 0 !important;
    transform: translateX(40px);
    pointer-events: none;
}

.toast-new-booking {
    border-left: 5px solid #ffc107 !important;
    background: #1e293b !important;
    color: #fff !important;
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

    <!-- 🔍 50% Left Card: Search & Filter Bookings -->
    <div class="col-12 col-xl-6">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 14px; background: #ffffff; border: 1px solid #eaedf1;">
            <div class="card-header bg-transparent border-0 pb-1 pt-3 px-3">
                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2" style="font-size: 14px;">
                    <i class="bi bi-search text-warning"></i> Search & Filter Bookings
                </h6>
            </div>
            <div class="card-body p-3 pt-2">
                <form action="{{ route('bookings.search.main') }}" method="GET" id="dashboardSearchForm">
                    <div class="row g-2 mb-2">
                        <!-- Universal Keyword Search Input -->
                        <div class="col-md-7 col-12">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 10px 0 0 10px;">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" 
                                       name="search" 
                                       id="universalSearchInput"
                                       class="form-control border-start-0 ps-0" 
                                       placeholder="Search passenger, phone, pickup, driver..." 
                                       value="{{ request('search') }}"
                                       style="border-radius: 0 10px 10px 0; font-size: 12.5px;">
                            </div>
                        </div>

                        <!-- Date Range Picker with Calendar Trigger -->
                        <div class="col-md-5 col-12">
                            <div class="input-group" id="calendarWrapper" title="Click to filter by Date Range" style="cursor: pointer;">
                                <span class="input-group-text bg-white border-end-0 text-warning" id="calendarIconBtn" style="border-radius: 10px 0 0 10px; cursor: pointer;">
                                    <i class="bi bi-calendar-event fs-6 text-warning"></i>
                                </span>
                                <input type="text" 
                                       name="date_range" 
                                       id="dateRangePicker" 
                                       class="form-control border-start-0 ps-0 bg-white" 
                                       placeholder="Select Dates" 
                                       value="{{ request('date_range') ?: (request('from_date') && request('to_date') ? request('from_date').' to '.request('to_date') : (request('from_date') ?: '')) }}"
                                       style="border-radius: 0 10px 10px 0; font-size: 12.5px; cursor: pointer;">
                                <input type="hidden" name="from_date" id="fromDateInput" value="{{ request('from_date') }}">
                                <input type="hidden" name="to_date" id="toDateInput" value="{{ request('to_date') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row g-2">
                        <!-- Driver Select -->
                        <div class="col-md-5 col-12">
                            <select name="driver_id" class="form-select text-dark" style="border-radius: 10px; font-size: 12.5px;">
                                <option value="">All Drivers (Any Vehicle)</option>
                                @foreach($drivers as $driver)
                                    @php
                                        $callSign = $driver['call_sign'] ?? 'D';
                                        $dName = $driver['name'] ?? 'Driver';
                                        $vehParts = [];
                                        if (!empty($driver['vehicle_type'])) $vehParts[] = $driver['vehicle_type'];
                                        $makeModel = trim(($driver['vehicle_make'] ?? '') . ' ' . ($driver['vehicle_model'] ?? ''));
                                        if (!empty($makeModel)) $vehParts[] = $makeModel;
                                        if (!empty($driver['vehicle_reg'])) $vehParts[] = $driver['vehicle_reg'];
                                        $vehStr = !empty($vehParts) ? ' (' . implode(' • ', $vehParts) . ')' : '';
                                    @endphp
                                    <option value="{{ $driver['id'] }}" {{ (string)request('driver_id') === (string)$driver['id'] ? 'selected' : '' }}>
                                        {{ $callSign }} • {{ $dName }}{{ $vehStr }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Payment Select -->
                        <div class="col-md-4 col-7">
                            <select name="payment_type" class="form-select" style="border-radius: 10px; font-size: 12.5px;">
                                <option value="">All Payments</option>
                                <option value="Cash" {{ request('payment_type') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Card" {{ request('payment_type') == 'Card' ? 'selected' : '' }}>Card</option>
                                <option value="Account" {{ request('payment_type') == 'Account' ? 'selected' : '' }}>Account</option>
                            </select>
                        </div>

                        <!-- Filter Button -->
                        <div class="col-md-3 col-5">
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn text-white fw-bold w-100 d-flex align-items-center justify-content-center gap-1 shadow-sm" style="background: linear-gradient(135deg, #B87333, #d48b48); border-radius: 10px; border: none; font-size: 12.5px; height: 36px;">
                                    <i class="bi bi-search"></i>
                                    <span>Filter</span>
                                </button>
                                @if(request('search') || request('date_range') || request('from_date') || request('to_date') || request('driver_id') || request('payment_type'))
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" title="Reset All Filters" style="border-radius: 10px; width: 36px; height: 36px; flex-shrink: 0;">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Date Presets -->
                    <div class="d-flex align-items-center gap-1 mt-2 pt-2 border-top flex-wrap" style="border-color: #f1f5f9 !important;">
                        <span class="text-muted small me-1" style="font-size: 11px;"><i class="bi bi-lightning-charge-fill text-warning"></i> Quick:</span>
                        <button type="button" class="btn btn-sm btn-light py-0 px-2 fw-semibold quick-date-btn" data-preset="today" style="border-radius: 12px; font-size: 11px; border: 1px solid #e2e8f0;">Today</button>
                        <button type="button" class="btn btn-sm btn-light py-0 px-2 fw-semibold quick-date-btn" data-preset="tomorrow" style="border-radius: 12px; font-size: 11px; border: 1px solid #e2e8f0;">Tomorrow</button>
                        <button type="button" class="btn btn-sm btn-light py-0 px-2 fw-semibold quick-date-btn" data-preset="week" style="border-radius: 12px; font-size: 11px; border: 1px solid #e2e8f0;">Next 7 Days</button>
                        <button type="button" class="btn btn-sm btn-light py-0 px-2 fw-semibold quick-date-btn" data-preset="clear" style="border-radius: 12px; font-size: 11px; border: 1px solid #e2e8f0;">All Bookings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ⚡ 50% Right Card: Get Instant Price / Quick Quote -->
    <div class="col-12 col-xl-6">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 14px; background: #ffffff; border: 1px solid #eaedf1;">
            <div class="card-header bg-transparent border-0 pb-1 pt-3 px-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2" style="font-size: 14px;">
                    <i class="bi bi-calculator-fill text-warning"></i> Get Instant Price
                </h6>
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-sm btn-light py-0 px-2 text-dark" id="swapPickupDropoffBtn" title="Swap Pickup & Dropoff" style="border-radius: 8px; font-size: 11px; border: 1px solid #e2e8f0;">
                        <i class="bi bi-arrow-left-right text-primary"></i> Swap
                    </button>
                    <button type="button" class="btn btn-sm btn-light py-0 px-2 text-dark" id="addViaToggleBtn" style="border-radius: 8px; font-size: 11px; border: 1px solid #e2e8f0;">
                        <i class="bi bi-plus-circle text-success"></i> Add Via
                    </button>
                </div>
            </div>
            <div class="card-body p-3 pt-2">
                <div class="row g-2 mb-2">
                    <!-- Pickup Address -->
                    <div class="col-md-6 col-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-success py-1" style="border-radius: 10px 0 0 10px;">
                                <i class="bi bi-geo-alt-fill"></i>
                            </span>
                            <input type="text" 
                                   id="calcPickupInput" 
                                   class="form-control border-start-0 ps-0" 
                                   placeholder="Pickup address / postcode" 
                                   style="border-radius: 0 10px 10px 0; font-size: 12.5px;">
                        </div>
                    </div>
                    <!-- Dropoff Address -->
                    <div class="col-md-6 col-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-danger py-1" style="border-radius: 10px 0 0 10px;">
                                <i class="bi bi-pin-map-fill"></i>
                            </span>
                            <input type="text" 
                                   id="calcDropoffInput" 
                                   class="form-control border-start-0 ps-0" 
                                   placeholder="Dropoff address / postcode" 
                                   style="border-radius: 0 10px 10px 0; font-size: 12.5px;">
                        </div>
                    </div>
                </div>

                <!-- Via Address Container (Toggled via button) -->
                <div class="row g-2 mb-2" id="calcViaWrapper" style="display: none;">
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-warning py-1" style="border-radius: 10px 0 0 10px;">
                                <i class="bi bi-signpost-split"></i>
                            </span>
                            <input type="text" 
                                   id="calcViaInput" 
                                   class="form-control border-start-0 ps-0" 
                                   placeholder="Via stop address or postcode (Optional)" 
                                   style="font-size: 12.5px;">
                            <button class="btn btn-outline-secondary py-1 px-2 border-start-0" type="button" id="removeViaBtn" style="border-radius: 0 10px 10px 0;" title="Remove via">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Row: Vehicle, Date, Time, Parking, Miles -->
                <div class="row g-2 mb-2">
                    <div class="col-md-3 col-6">
                        <label class="form-label mb-0 text-muted small" style="font-size: 11px;">Vehicle</label>
                        <select id="calcVehicleSelect" class="form-select" style="border-radius: 8px; font-size: 12px;">
                            <option value="Saloon" selected>Saloon</option>
                            <option value="Estate">Estate</option>
                            <option value="Executive">Executive</option>
                            <option value="MPV">MPV / 6 Seater</option>
                            <option value="8 Seater">8 Seater</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-6">
                        <label class="form-label mb-0 text-muted small" style="font-size: 11px;">Pickup Date</label>
                        <input type="date" id="calcDateInput" class="form-control" value="{{ date('Y-m-d') }}" style="border-radius: 8px; font-size: 12px;">
                    </div>
                    <div class="col-md-2 col-4">
                        <label class="form-label mb-0 text-muted small" style="font-size: 11px;">Time</label>
                        <input type="time" id="calcTimeInput" class="form-control" value="{{ date('H:i') }}" style="border-radius: 8px; font-size: 12px;">
                    </div>
                    <div class="col-md-2 col-4">
                        <label class="form-label mb-0 text-muted small" style="font-size: 11px;">Parking (£)</label>
                        <input type="number" step="0.5" id="calcParkingInput" class="form-control" placeholder="0.00" title="Parking (£)" style="border-radius: 8px; font-size: 12px;">
                    </div>
                    <div class="col-md-2 col-4">
                        <label class="form-label mb-0 text-muted small" style="font-size: 11px;">Total Miles</label>
                        <input type="text" id="calcMilesInput" class="form-control bg-light fw-bold text-primary" readonly value="0.00 mi" title="Total Journey Mileage" style="border-radius: 8px; font-size: 12px;">
                    </div>
                </div>

                <!-- Price Result Output Bar -->
                <div class="p-2 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span id="calcDistanceBadge" class="badge px-2 py-1.5 fw-bold d-flex align-items-center gap-1 shadow-sm" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-size: 12px; border-radius: 8px;">
                            <i class="bi bi-speedometer2"></i> <span id="calcDistText">0.00 Miles</span>
                        </span>
                        <span class="text-muted small fw-medium" id="calcBreakdownText" style="font-size: 11.5px;">Base: £0.00</span>
                        <span id="calcLoader" class="spinner-border spinner-border-sm text-warning ms-1" style="display: none;" role="status"></span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-dark text-warning fw-bold px-2 py-1 d-flex align-items-center gap-1 shadow-sm" id="getInstantPriceBtn" style="border-radius: 8px; font-size: 12px; border: 1px solid #334155;">
                            <i class="bi bi-lightning-fill"></i> Get Price
                        </button>
                        <div class="d-flex align-items-center gap-1">
                            <span class="text-muted small fw-semibold" style="font-size: 11px;">Total:</span>
                            <span class="badge fs-6 fw-bold px-2.5 py-1 shadow-sm" id="calcFinalPriceBadge" style="background: linear-gradient(135deg, #111827, #1e293b); color: #E6B04A !important; border: 1px solid rgba(230, 176, 74, 0.4); border-radius: 8px;">
                                £0.00
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

  
  <div class="col-lg-12 mt-4">
        <div class="card border-0">
            <div class="card-header bg-warning bg-opacity-25 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-dark">
                    📋 Future Bookings<span class="ms-2 badge bg-dark" id="bookingsTotalCount">{{ $bookings->total() }}</span>
                </h6>
                <span class="badge bg-success bg-opacity-75 text-white d-flex align-items-center gap-1" id="liveSyncStatus">
                    <i class="bi bi-broadcast"></i> Live Sync Active
                </span>
            </div>
            <div class="table-responsive" style="border-radius: 0px 0px 8px 8px;">
                <table class="table table-hover align-middle mb-0" id="futureBookingsTable">
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
        <th>Plateform</th>
        <th>Actions</th>
    </tr>
</thead>
<tbody class="small" id="bookingsTableBody">
@forelse($bookings as $booking)
    @php
        $isHighPrice   = ($booking['price'] ?? 0) > 50;
        $isHidden      = ($booking['hidden'] ?? false) == true;

        if ($isHidden) {
            $rowStyle = 'background-color: yellow; color: #000;';
        } elseif ($isHighPrice) {
            $rowStyle = 'background-color: burlywood; color: #000;';
        } else {
            $rowStyle = '';
        }
       
        $platform = (int) ($booking['platform'] ?? 1);
        $partner = strtolower((string) ($booking['partner'] ?? ''));

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

        $statusBadges = [
            'pending'      => 'warning',
            'accepted'     => 'info',
            'declined'     => 'danger',
            'onroute'      => 'primary',
            'arrived'      => 'success',
            'pickedup'     => 'success',
            'completed'    => 'primary',
            'job_cancelled'=> 'danger',
            'no_show'      => 'secondary',
        ];

        $currentStatus = $booking['status'] ?? 'pending';
        $statusBadgeClass = $statusBadges[$currentStatus] ?? 'warning';
        $statusSelectStyle = $statusBadgeClass === 'secondary' 
            ? '' 
            : "background-color: var(--bs-{$statusBadgeClass}); color: var(--bs-black); border-color: var(--bs-{$statusBadgeClass});";

        $paymentType = strtolower($booking['payment_type'] ?? 'unknown');
        $paymentColors = [
            'cash'    => '#28a745',
            'card'    => '#0d6efd',
            'account' => '#6f42c1',
        ];
        $bgColor = $paymentColors[$paymentType] ?? '#6c757d';
        $driver = collect($drivers)->firstWhere('id', $booking['driver_id'] ?? null);
    @endphp
    <tr id="booking-row-{{ $booking['id'] }}" data-booking-id="{{ $booking['id'] }}" class="booking-table-row" style="{{ $rowStyle }}">
        <td class="col-ref" style="{{ $rowStyle }}">{{ $booking['ref_no'] ?? 'N/A' }}</td>
        <td class="col-payment" style="{{ $rowStyle }}">
            <span class="badge rounded-pill px-3 py-2" style="background-color: {{ $bgColor }}; color: #fff;">
                {{ ucfirst($paymentType) }}
            </span>
        </td>
        <td class="col-passenger" style="{{ $rowStyle }}">{{ $booking['passenger_name'] ?? 'N/A' }}</td>
        <td class="col-driver driver-cell" style="{{ $rowStyle }}">
            @if($driver)
                <span class="badge rounded-pill bg-danger bg-opacity-90 px-3 py-2 me-1 driver-badge">
                    {{ $driver['call_sign'] ?? '' }}/{{ $driver['name'] ?? '' }}
                </span>
            @else
                <span class="text-muted unassigned-driver">-</span>
            @endif
        </td>
        <td class="col-phone" style="{{ $rowStyle }}">{{ $booking['phone_no'] ?? 'N/A' }}</td>
        <td class="col-pickup" style="{{ $rowStyle }}">{{ Str::limit($booking['pickup_address'], 30) }}</td>
        <td class="col-dropoff" style="{{ $rowStyle }}">{{ Str::limit($booking['dropoff_address'], 30) }}</td>
        <td class="col-vias" style="{{ $rowStyle }}">
            @if(!empty($booking['vias']))
                {{ implode(' → ', $booking['vias']) }}
            @else
                -
            @endif
        </td>
        <td class="col-date" style="{{ $rowStyle }}">{{ isset($booking['pickup_time']) ? \Carbon\Carbon::parse($booking['pickup_time'])->format('d M Y') : '-' }}</td>
        <td class="col-time" style="{{ $rowStyle }}">{{ isset($booking['pickup_time']) ? \Carbon\Carbon::parse($booking['pickup_time'])->format('H:i') : '-' }}</td>
        <td class="col-vehicle" style="{{ $rowStyle }}">
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
        <td class="col-flight" style="{{ $rowStyle }}">{{ $booking['flight_no'] ?? '-' }}</td>
        <td class="col-price" style="{{ $rowStyle }}">{{ $booking['price'] ?? '-' }}</td>
        <td class="col-comment" style="{{ $rowStyle }}">
            {{ isset($booking['job_comment']) ? Str::limit($booking['job_comment'], 20) : 'No comment' }}
        </td>
        <td class="col-status" style="{{ $rowStyle }}">
            <form method="POST" action="{{ route('bookings.updateStatusManual', $booking['id']) }}" class="statusForm">
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
                        <option value="{{ $value }}" {{ ($booking['status'] ?? '') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </form>
        </td>
        <td class="col-platform" style="{{ $rowStyle }}">
            <span class="badge {{ $platformBadge }}" style="font-size: 0.75rem;">
                {{ $platformLabel }}
            </span>
        </td>
        <td class="col-actions" style="{{ $rowStyle }}">
            <div class="dropdown actions-dropdown" style="position: static;">
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3">
                    <li class="action-dispatch-item" style="{{ !empty($booking['driver_id']) ? 'display:none;' : '' }}">
                        <a class="dropdown-item d-flex align-items-center text-secondary dispatch-driver-btn"
                           href="#"
                           data-booking-id="{{ $booking['id'] ?? '' }}"
                           data-bs-toggle="modal"
                           data-bs-target="#dispatchDriverModal">
                            <i class="bi bi-truck me-2"></i> Dispatch Driver
                        </a>
                    </li>
                    <li class="action-track-item" style="{{ empty($booking['driver_id']) ? 'display:none;' : '' }}">
                        <a class="dropdown-item d-flex align-items-center text-primary track-driver-link" href="{{ route('bookings.track', $booking['id']) }}">
                            <i class="bi bi-geo-alt me-2"></i> Track Driver
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-success" href="{{ route('bookings.receipt', $booking['id']) }}">
                            <i class="bi bi-receipt me-2"></i> Receipt
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-warning" href="{{ route('bookings.route', $booking['id']) }}">
                            <i class="bi bi-map me-2"></i> Route
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-info" href="{{ route('bookings.return', $booking['id']) }}">
                            <i class="bi bi-arrow-repeat me-2"></i> Create Return Job
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-success send-confirmation-email-btn"
                           href="#"
                           data-booking-id="{{ $booking['id'] ?? '' }}"
                           data-booking-ref="{{ $booking['ref_no'] ?? '' }}"
                           data-passenger-email="{{ $booking['email'] ?? '' }}"
                           data-passenger-name="{{ $booking['passenger_name'] ?? '' }}">
                            <i class="bi bi-envelope me-2"></i> Send Confirmation Email
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-warning send-confirmation-sms-btn"
                           href="#"
                           data-booking-id="{{ $booking['ref_no'] ?? '' }}"
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
                           data-via="{{ !empty($booking['vias']) ? implode(' → ', $booking['vias']) : '-' }}">
                            <i class="bi bi-chat-left-text me-2"></i> Send Confirmation SMS
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-danger recall-job-btn" href="#"
                           data-booking-id="{{ $booking['id'] ?? '' }}">
                            <i class="bi bi-arrow-counterclockwise me-2"></i> Recall Job
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-secondary hide-job-btn" href="#"
                           data-booking-id="{{ $booking['id'] ?? '' }}">
                            <i class="bi bi-eye-slash me-2"></i> Hide Job
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-primary" href="{{ route('bookings.edit', $booking['id']) }}">
                            <i class="bi bi-pencil-square me-2"></i> Edit Booking
                        </a>
                    </li>
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
    <tr id="emptyBookingsRow">
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
// Global state & configurations
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}';
let DRIVERS_MAP = @json(collect($drivers)->keyBy('id'));
const knownBookingIds = new Set();
let isInitialFirebaseLoadDone = false;

const STATUS_BADGES = {
    'pending': 'warning',
    'accepted': 'info',
    'declined': 'danger',
    'onroute': 'primary',
    'arrived': 'success',
    'pickedup': 'success',
    'completed': 'primary',
    'job_cancelled': 'danger',
    'no_show': 'secondary',
};

const PAYMENT_COLORS = {
    'cash': '#28a745',
    'card': '#0d6efd',
    'account': '#6f42c1',
};

const VEHICLE_BADGES = {
    'Saloon': 'primary',
    'Estate': 'success',
    'MPV': 'warning',
    '8 Seater': 'danger',
    'Executive': 'dark',
};

// 🔊 Pleasant Web Audio API notification chime (no external audio files required)
function playNewBookingChime() {
    try {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) return;
        const ctx = new AudioCtx();
        if (ctx.state === 'suspended') {
            ctx.resume();
        }

        const now = ctx.currentTime;

        // Note 1 (E5 - 659.25Hz)
        const osc1 = ctx.createOscillator();
        const gain1 = ctx.createGain();
        osc1.type = 'sine';
        osc1.frequency.setValueAtTime(659.25, now);
        gain1.gain.setValueAtTime(0.2, now);
        gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
        osc1.connect(gain1);
        gain1.connect(ctx.destination);
        osc1.start(now);
        osc1.stop(now + 0.35);

        // Note 2 (B5 - 987.77Hz)
        const osc2 = ctx.createOscillator();
        const gain2 = ctx.createGain();
        osc2.type = 'sine';
        osc2.frequency.setValueAtTime(987.77, now + 0.12);
        gain2.gain.setValueAtTime(0.25, now + 0.12);
        gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
        osc2.connect(gain2);
        gain2.connect(ctx.destination);
        osc2.start(now + 0.12);
        osc2.stop(now + 0.6);
    } catch (e) {
        console.warn('Audio chime warning:', e);
    }
}

// 🍞 Modern Toast Notification System
function showDashboardToast(title, message, type = 'success', duration = 4500, actionBtn = null) {
    let container = document.getElementById('dashboardToastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'dashboardToastContainer';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '99999';
        document.body.appendChild(container);
    }

    const toastId = 'toast_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
    let bgStyle = 'background: #1e293b; color: #fff;';
    let borderAccent = 'border-left: 5px solid #20c997;';
    let icon = 'bi-check-circle-fill text-success';

    if (type === 'danger') {
        borderAccent = 'border-left: 5px solid #e74c3c;';
        icon = 'bi-exclamation-triangle-fill text-danger';
    } else if (type === 'warning' || type === 'booking') {
        borderAccent = 'border-left: 5px solid #f39c12;';
        icon = 'bi-bell-fill text-warning';
    } else if (type === 'info') {
        borderAccent = 'border-left: 5px solid #3498db;';
        icon = 'bi-info-circle-fill text-info';
    }

    let actionBtnHtml = '';
    if (actionBtn && actionBtn.text) {
        actionBtnHtml = `<button type="button" class="btn btn-sm btn-warning ms-2 fw-semibold px-2 py-1 toast-action-btn">${actionBtn.text}</button>`;
    }

    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center border-0 shadow-lg mb-2" style="${bgStyle} ${borderAccent} min-width: 320px; border-radius: 10px;" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex p-3 align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi ${icon} fs-4"></i>
                    <div>
                        <div class="fw-bold" style="font-size: 14px;">${title}</div>
                        <div class="opacity-90" style="font-size: 13px;">${message}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    ${actionBtnHtml}
                    <button type="button" class="btn-close btn-close-white ms-1" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', toastHtml);
    const toastEl = document.getElementById(toastId);
    const bsToast = new bootstrap.Toast(toastEl, { delay: duration });

    if (actionBtn && actionBtn.onClick) {
        const btn = toastEl.querySelector('.toast-action-btn');
        if (btn) {
            btn.addEventListener('click', () => {
                actionBtn.onClick();
                bsToast.hide();
            });
        }
    }

    bsToast.show();
    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}

// 🔢 Update Total Bookings Count Badge
function updateBookingsCountBadge(delta = 0, exact = null) {
    const badge = document.getElementById('bookingsTotalCount');
    if (!badge) return;
    if (exact !== null) {
        badge.textContent = exact;
        return;
    }
    const current = parseInt(badge.textContent.replace(/\D/g, '')) || 0;
    const updated = Math.max(0, current + delta);
    badge.textContent = updated;
}

// 🎨 Helper to format payment badge
function getPaymentBadgeHtml(paymentType) {
    const type = (paymentType || 'unknown').toLowerCase();
    const bg = PAYMENT_COLORS[type] || '#6c757d';
    const label = type.charAt(0).toUpperCase() + type.slice(1);
    return `<span class="badge rounded-pill px-3 py-2" style="background-color: ${bg}; color: #fff;">${label}</span>`;
}

// 🎨 Helper to format platform badge
function getPlatformBadgeHtml(platformVal, partnerVal) {
    const platform = parseInt(platformVal ?? 1);
    const partner = String(partnerVal || '').toLowerCase();

    if (platform === 3 || partner === 'nonstop_ai') {
        return `<span class="badge bg-info text-dark" style="font-size: 0.75rem;">AI Call</span>`;
    }
    switch (platform) {
        case 0:
            return `<span class="badge bg-danger" style="font-size: 0.75rem;">App</span>`;
        case 2:
            return `<span class="badge bg-warning" style="font-size: 0.75rem;">Admin</span>`;
        case 1:
        default:
            return `<span class="badge bg-primary" style="font-size: 0.75rem;">Web</span>`;
    }
}

// 🎨 Helper to format vehicle badge
function getVehicleBadgeHtml(vehicleMake) {
    const make = vehicleMake || '-';
    const badgeClass = VEHICLE_BADGES[make] || 'secondary';
    return `<span class="badge bg-${badgeClass}" style="font-size: 0.85rem;">${make}</span>`;
}

// 🎨 Helper to format status select style
function getStatusSelectStyle(status) {
    const badgeClass = STATUS_BADGES[status] || 'warning';
    if (badgeClass === 'secondary') return '';
    return `background-color: var(--bs-${badgeClass}); color: var(--bs-black); border-color: var(--bs-${badgeClass});`;
}

// 🛠️ Render a complete table row for a booking
function buildBookingRowHtml(booking, isNew = false) {
    const id = booking.id;
    const isHidden = !!booking.hidden;
    const price = parseFloat(booking.price) || 0;
    const isHighPrice = price > 50;

    let rowStyle = '';
    if (isHidden) {
        rowStyle = 'background-color: yellow; color: #000;';
    } else if (isHighPrice) {
        rowStyle = 'background-color: burlywood; color: #000;';
    }

    const currentStatus = booking.status || 'pending';
    const statusSelectStyle = getStatusSelectStyle(currentStatus);

    let pickupDate = '-';
    let pickupTime = '-';
    if (booking.pickup_time) {
        try {
            const dt = new Date(booking.pickup_time);
            if (!isNaN(dt.getTime())) {
                pickupDate = dt.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                pickupTime = dt.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', hour12: false });
            }
        } catch (e) {}
    }

    let driver = null;
    if (booking.driver_id && DRIVERS_MAP[booking.driver_id]) {
        driver = DRIVERS_MAP[booking.driver_id];
    }

    const driverHtml = driver
        ? `<span class="badge rounded-pill bg-danger bg-opacity-90 px-3 py-2 me-1 driver-badge">${driver.call_sign || ''}/${driver.name || ''}</span>`
        : `<span class="text-muted unassigned-driver">-</span>`;

    let viasText = '-';
    if (Array.isArray(booking.via_addresses) && booking.via_addresses.length > 0) {
        viasText = booking.via_addresses.join(' → ');
    } else if (Array.isArray(booking.vias) && booking.vias.length > 0) {
        viasText = booking.vias.join(' → ');
    }

    const commentText = booking.job_comment
        ? (booking.job_comment.length > 20 ? booking.job_comment.substring(0, 20) + '...' : booking.job_comment)
        : 'No comment';

    const statusOptions = {
        'pending': 'Pending',
        'accepted': 'Accepted',
        'declined': 'Declined',
        'onroute': 'On Route',
        'arrived': 'Arrived',
        'pickedup': 'Picked Up',
        'completed': 'Completed',
        'job_cancelled': 'Job Cancelled',
        'no_show': 'No Show',
    };

    let statusOptionsHtml = '';
    for (const [val, label] of Object.entries(statusOptions)) {
        statusOptionsHtml += `<option value="${val}" ${currentStatus === val ? 'selected' : ''}>${label}</option>`;
    }

    const hasDriver = !!booking.driver_id;
    const bookingJson = JSON.stringify(booking).replace(/'/g, "&apos;");

    const highlightClass = isNew ? 'new-booking-highlight' : '';

    return `
    <tr id="booking-row-${id}" data-booking-id="${id}" class="booking-table-row ${highlightClass}" style="${rowStyle}">
        <td class="col-ref" style="${rowStyle}">${booking.ref_no || 'N/A'}</td>
        <td class="col-payment" style="${rowStyle}">${getPaymentBadgeHtml(booking.payment_type)}</td>
        <td class="col-passenger" style="${rowStyle}">${booking.passenger_name || 'N/A'}</td>
        <td class="col-driver driver-cell" style="${rowStyle}">${driverHtml}</td>
        <td class="col-phone" style="${rowStyle}">${booking.phone_no || 'N/A'}</td>
        <td class="col-pickup" style="${rowStyle}">${(booking.pickup_address || '').substring(0, 30)}</td>
        <td class="col-dropoff" style="${rowStyle}">${(booking.dropoff_address || '').substring(0, 30)}</td>
        <td class="col-vias" style="${rowStyle}">${viasText}</td>
        <td class="col-date" style="${rowStyle}">${pickupDate}</td>
        <td class="col-time" style="${rowStyle}">${pickupTime}</td>
        <td class="col-vehicle" style="${rowStyle}">${getVehicleBadgeHtml(booking.vehicle_make)}</td>
        <td class="col-flight" style="${rowStyle}">${booking.flight_no || '-'}</td>
        <td class="col-price" style="${rowStyle}">${booking.price || '-'}</td>
        <td class="col-comment" style="${rowStyle}">${commentText}</td>
        <td class="col-status" style="${rowStyle}">
            <form method="POST" action="/bookings/${id}/update-status-manual" class="statusForm">
                <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                <select class="form-select form-select-sm text-black statusSelect"
                        name="status"
                        style="${statusSelectStyle}; width:100px;"
                        data-booking-id="${id}">
                    ${statusOptionsHtml}
                </select>
            </form>
        </td>
        <td class="col-platform" style="${rowStyle}">${getPlatformBadgeHtml(booking.platform, booking.partner)}</td>
        <td class="col-actions" style="${rowStyle}">
            <div class="dropdown actions-dropdown" style="position: static;">
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3">
                    <li class="action-dispatch-item" style="${hasDriver ? 'display:none;' : ''}">
                        <a class="dropdown-item d-flex align-items-center text-secondary dispatch-driver-btn"
                           href="#"
                           data-booking-id="${id}"
                           data-bs-toggle="modal"
                           data-bs-target="#dispatchDriverModal">
                            <i class="bi bi-truck me-2"></i> Dispatch Driver
                        </a>
                    </li>
                    <li class="action-track-item" style="${!hasDriver ? 'display:none;' : ''}">
                        <a class="dropdown-item d-flex align-items-center text-primary track-driver-link" href="/bookings/${id}/track">
                            <i class="bi bi-geo-alt me-2"></i> Track Driver
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-success" href="/bookings/${id}/receipt">
                            <i class="bi bi-receipt me-2"></i> Receipt
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-warning" href="/bookings/${id}/route">
                            <i class="bi bi-map me-2"></i> Route
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-info" href="/bookings/${id}/return">
                            <i class="bi bi-arrow-repeat me-2"></i> Create Return Job
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-success send-confirmation-email-btn"
                           href="#"
                           data-booking-id="${id}"
                           data-booking-ref="${booking.ref_no || ''}"
                           data-passenger-email="${booking.email || ''}"
                           data-passenger-name="${booking.passenger_name || ''}">
                            <i class="bi bi-envelope me-2"></i> Send Confirmation Email
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-warning send-confirmation-sms-btn"
                           href="#"
                           data-booking-id="${booking.ref_no || id}"
                           data-phone="${booking.phone_no || ''}"
                           data-name="${booking.passenger_name || ''}"
                           data-date="${pickupDate}"
                           data-time="${pickupTime}"
                           data-vehicle="${booking.vehicle_make || ''}"
                           data-price="${booking.price || ''}"
                           data-payment="${booking.payment_type ? booking.payment_type.charAt(0).toUpperCase() + booking.payment_type.slice(1) : ''}"
                           data-pickup="${booking.pickup_address || ''}"
                           data-dropoff="${booking.dropoff_address || ''}"
                           data-flight_no="${booking.flight_no || ''}"
                           data-via="${viasText}">
                            <i class="bi bi-chat-left-text me-2"></i> Send Confirmation SMS
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-danger recall-job-btn" href="#"
                           data-booking-id="${id}">
                            <i class="bi bi-arrow-counterclockwise me-2"></i> Recall Job
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-secondary hide-job-btn" href="#"
                           data-booking-id="${id}">
                            <i class="bi bi-eye-slash me-2"></i> Hide Job
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-primary" href="/bookings/${id}/edit">
                            <i class="bi bi-pencil-square me-2"></i> Edit Booking
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-info view-booking-btn"
                           href="#"
                           data-bs-toggle="modal"
                           data-bs-target="#viewBookingModal"
                           data-booking='${bookingJson}'>
                            <i class="bi bi-eye me-2"></i> View Booking
                        </a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
    `;
}

// 🔄 Live Row Update in-place
function updateBookingRowData(booking) {
    const row = document.getElementById('booking-row-' + booking.id);
    if (!row) return;

    // Update status select
    const statusSelect = row.querySelector('.statusSelect');
    if (statusSelect && booking.status) {
        statusSelect.value = booking.status;
        statusSelect.style.cssText = getStatusSelectStyle(booking.status) + '; width:100px;';
    }

    // Update driver cell & actions
    const driverCell = row.querySelector('.driver-cell');
    const dispatchItem = row.querySelector('.action-dispatch-item');
    const trackItem = row.querySelector('.action-track-item');

    if (booking.driver_id && DRIVERS_MAP[booking.driver_id]) {
        const driver = DRIVERS_MAP[booking.driver_id];
        if (driverCell) {
            driverCell.innerHTML = `<span class="badge rounded-pill bg-danger bg-opacity-90 px-3 py-2 me-1 driver-badge">${driver.call_sign || ''}/${driver.name || ''}</span>`;
        }
        if (dispatchItem) dispatchItem.style.display = 'none';
        if (trackItem) trackItem.style.display = '';
    } else {
        if (driverCell) {
            driverCell.innerHTML = `<span class="text-muted unassigned-driver">-</span>`;
        }
        if (dispatchItem) dispatchItem.style.display = '';
        if (trackItem) trackItem.style.display = 'none';
    }

    // Update view-booking-btn data-booking
    const viewBtn = row.querySelector('.view-booking-btn');
    if (viewBtn) {
        viewBtn.setAttribute('data-booking', JSON.stringify(booking));
    }
}

// 🗑️ Remove Row with Smooth Animation
function removeBookingRowFromTable(bookingId) {
    const row = document.getElementById('booking-row-' + bookingId);
    if (!row) return;

    row.classList.add('row-fade-out');
    setTimeout(() => {
        if (row && row.parentNode) {
            row.remove();
            updateBookingsCountBadge(-1);

            const tbody = document.getElementById('bookingsTableBody');
            if (tbody && tbody.querySelectorAll('.booking-table-row').length === 0) {
                tbody.innerHTML = `<tr id="emptyBookingsRow"><td colspan="17" class="text-center text-muted py-4">No future bookings found.</td></tr>`;
            }
        }
    }, 450);
}

// ⚡ Bind dynamic events to interactive elements (delegated / direct)
function bindRowEvents(context = document) {
    // Status select change
    context.querySelectorAll('.statusSelect:not([data-bound="true"])').forEach(select => {
        select.setAttribute('data-bound', 'true');
        let previousStatus = select.value;

        select.addEventListener('change', function () {
            const form = this.closest('.statusForm');
            const selected = this.value;
            const bookingId = this.getAttribute('data-booking-id');

            const modalEl = document.getElementById('statusConfirmModal');
            const modal = new bootstrap.Modal(modalEl);
            const title = document.getElementById('statusModalTitle');
            const message = document.getElementById('statusModalMessage');
            const icon = document.getElementById('statusIcon');
            const confirmBtn = document.getElementById('confirmStatusBtn');
            const cancelBtn = document.getElementById('cancelStatusBtn');

            let requiresConfirm = false;
            confirmBtn.className = 'btn px-4';

            if (selected === 'completed') {
                requiresConfirm = true;
                title.innerText = 'Confirm Completion';
                message.innerText = 'Are you sure you want to mark this booking as completed?';
                icon.innerHTML = '⏳';
                confirmBtn.classList.add('btn-success');
            } else if (selected === 'declined') {
                requiresConfirm = true;
                title.innerText = 'Confirm Decline';
                message.innerText = 'Declining this booking cannot be undone. Proceed?';
                icon.innerHTML = '⚠️';
                confirmBtn.classList.add('btn-danger');
            } else if (selected === 'no_show') {
                requiresConfirm = true;
                title.innerText = 'Confirm No Show';
                message.innerText = 'Are you sure you want to mark this booking as No Show?';
                icon.innerHTML = '🚫';
                confirmBtn.classList.add('btn-warning');
            } else if (selected === 'job_cancelled') {
                requiresConfirm = true;
                title.innerText = 'Confirm Cancelled';
                message.innerText = 'Are you sure you want to mark this booking as Cancelled?';
                icon.innerHTML = '🚫';
                confirmBtn.classList.add('btn-warning');
            }

            const executeStatusUpdate = async () => {
                const formData = new FormData(form);
                formData.set('status', selected);
                select.disabled = true;
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await response.json().catch(() => null);

                    if (response.ok && data?.success) {
                        showDashboardToast('Status Updated', `Booking status changed to ${selected.toUpperCase()}`, 'success');
                        select.style.cssText = getStatusSelectStyle(selected) + '; width:100px;';
                        previousStatus = selected;

                        if (['completed', 'job_cancelled', 'no_show'].includes(selected)) {
                            removeBookingRowFromTable(bookingId);
                        }
                    } else {
                        select.value = previousStatus;
                        const errMsg = data?.errors?.status ? data.errors.status.join(', ') : (data?.message || 'Failed to update status.');
                        showDashboardToast('Error', errMsg, 'danger');
                    }
                } catch (err) {
                    console.error('Status update error:', err);
                    select.value = previousStatus;
                    showDashboardToast('Network Error', 'Could not update status. Please try again.', 'danger');
                } finally {
                    select.disabled = false;
                }
            };

            if (requiresConfirm) {
                modal.show();
                confirmBtn.onclick = () => {
                    modal.hide();
                    executeStatusUpdate();
                };
                cancelBtn.onclick = () => {
                    modal.hide();
                    this.value = previousStatus;
                };
            } else {
                executeStatusUpdate();
            }
        });
    });

    // Recall Job AJAX
    context.querySelectorAll('.recall-job-btn:not([data-bound="true"])').forEach(btn => {
        btn.setAttribute('data-bound', 'true');
        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            const bookingId = this.getAttribute('data-booking-id');
            if (!bookingId) return;

            if (!confirm('Are you sure you want to recall this job from the driver?')) return;

            const row = document.getElementById('booking-row-' + bookingId);
            try {
                const response = await fetch(`/bookings/${bookingId}/recall`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json().catch(() => null);

                if (response.ok && data?.success) {
                    showDashboardToast('Job Recalled', 'Driver unassigned from booking.', 'success');
                    if (row) {
                        const driverCell = row.querySelector('.driver-cell');
                        if (driverCell) driverCell.innerHTML = `<span class="text-muted unassigned-driver">-</span>`;
                        const dispatchItem = row.querySelector('.action-dispatch-item');
                        const trackItem = row.querySelector('.action-track-item');
                        if (dispatchItem) dispatchItem.style.display = '';
                        if (trackItem) trackItem.style.display = 'none';
                    }
                } else {
                    showDashboardToast('Error', data?.message || 'Failed to recall job.', 'danger');
                }
            } catch (err) {
                console.error('Recall error:', err);
                showDashboardToast('Network Error', 'Failed to recall job.', 'danger');
            }
        });
    });

    // Hide Job AJAX
    context.querySelectorAll('.hide-job-btn:not([data-bound="true"])').forEach(btn => {
        btn.setAttribute('data-bound', 'true');
        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            const bookingId = this.getAttribute('data-booking-id');
            if (!bookingId) return;

            if (!confirm('Are you sure you want to hide this booking?')) return;

            const row = document.getElementById('booking-row-' + bookingId);
            try {
                const response = await fetch(`/bookings/${bookingId}/hide`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({})
                });

                const data = await response.json().catch(() => null);

                if (response.ok && (data?.status === 'success' || data?.success)) {
                    showDashboardToast('Job Hidden', 'Booking marked as hidden.', 'info');
                    if (row) {
                        row.style.backgroundColor = 'yellow';
                        row.querySelectorAll('td').forEach(td => td.style.backgroundColor = 'yellow');
                    }
                } else {
                    showDashboardToast('Error', data?.message || 'Failed to hide booking.', 'danger');
                }
            } catch (err) {
                console.error('Hide error:', err);
                showDashboardToast('Network Error', 'Failed to hide booking.', 'danger');
            }
        });
    });

    // View details modal
    context.querySelectorAll('.view-booking-btn:not([data-bound="true"])').forEach(btn => {
        btn.setAttribute('data-bound', 'true');
        btn.addEventListener('click', function () {
            try {
                const raw = this.getAttribute('data-booking');
                if (!raw) return;
                const booking = JSON.parse(raw);

                document.getElementById('modal-ref_no').textContent = booking.ref_no || '-';
                document.getElementById('modal-passenger_name').textContent = booking.passenger_name || '-';
                document.getElementById('modal-phone_no').textContent = booking.phone_no || '-';
                document.getElementById('modal-email').textContent = booking.email || '-';
                document.getElementById('modal-pickup_address').textContent = booking.pickup_address || '-';
                document.getElementById('modal-dropoff_address').textContent = booking.dropoff_address || '-';

                let vias = '-';
                if (Array.isArray(booking.vias)) vias = booking.vias.join(', ');
                else if (Array.isArray(booking.via_addresses)) vias = booking.via_addresses.join(', ');
                document.getElementById('modal-vias').textContent = vias;

                let pickupFmt = booking.pickup_time || '-';
                try {
                    const dt = new Date(booking.pickup_time);
                    if (!isNaN(dt.getTime())) {
                        pickupFmt = dt.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) + ' ' + dt.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
                    }
                } catch(e) {}
                document.getElementById('modal-pickup_time').textContent = pickupFmt;

                document.getElementById('modal-flight_no').textContent = booking.flight_no || '-';
                document.getElementById('modal-vehicle_id').textContent = booking.vehicle_make || '-';
                document.getElementById('modal-price').textContent = booking.price || '-';
                document.getElementById('modal-status').textContent = booking.status || '-';
                document.getElementById('modal-comment').textContent = booking.job_comment || '-';
            } catch (err) {
                console.error('Error parsing booking json:', err);
            }
        });
    });

    // Send SMS Modal trigger
    context.querySelectorAll('.send-confirmation-sms-btn:not([data-bound="true"])').forEach(btn => {
        btn.setAttribute('data-bound', 'true');
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const bookingId = this.dataset.bookingId || '';
            const phone = this.dataset.phone || '';
            const passengerName = this.dataset.name || '';
            const pickupDate = this.dataset.date || '';
            const pickupTime = this.dataset.time || '';
            const vehicleType = this.dataset.vehicle || '';
            const price = this.dataset.price || '';
            const payment = this.dataset.payment || '';
            const pickup = this.dataset.pickup || '';
            const dropoff = this.dataset.dropoff || '';
            const via = this.dataset.via || '-';
            const flight_no = this.dataset.flight_no || '';

            document.getElementById('smsBookingId').value = bookingId;
            document.getElementById('smsPhone').value = phone;

            const messageTemplate = `Dear ${passengerName},

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

            document.getElementById('smsMessage').value = messageTemplate;
            const modal = new bootstrap.Modal(document.getElementById('sendSmsModal'));
            modal.show();
        });
    });

    // Send Email Modal trigger
    context.querySelectorAll('.send-confirmation-email-btn:not([data-bound="true"])').forEach(btn => {
        btn.setAttribute('data-bound', 'true');
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const bookingId = this.getAttribute('data-booking-id') || '';
            const bookingRef = this.getAttribute('data-booking-ref') || '';
            const passengerName = this.getAttribute('data-passenger-name') || '';
            const email = this.getAttribute('data-passenger-email') || '';

            document.getElementById('emailBookingId').value = bookingId;
            document.getElementById('emailBookingRef').value = bookingRef;
            document.getElementById('emailAddress').value = email;
            document.getElementById('emailMessage').value = `Hello ${passengerName}, your booking has been confirmed. Ref# ${bookingRef}`;

            const modal = new bootstrap.Modal(document.getElementById('sendEmailModal'));
            modal.show();
        });
    });
}

// 🚀 Core Firebase Real-Time Database Setup
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

    // 1️⃣ Live Drivers Listener (Live Dispatch cache)
    const driverRef = db.ref("drivers");
    driverRef.on("value", (snapshot) => {
        const drivers = snapshot.val();
        if (drivers) {
            for (const [id, d] of Object.entries(drivers)) {
                DRIVERS_MAP[id] = { id, ...d };
            }
        }
    });

    // 2️⃣ Live Bookings Listener (Real-Time Background Sync)
    const bookingsRef = db.ref("bookings");

    // Index existing table IDs on initial load
    document.querySelectorAll('.booking-table-row').forEach(row => {
        const id = row.getAttribute('data-booking-id');
        if (id) knownBookingIds.add(id);
    });

    // Listen for initial value once to mark initial load complete
    bookingsRef.once('value', () => {
        isInitialFirebaseLoadDone = true;
        const liveIndicator = document.getElementById('liveSyncStatus');
        if (liveIndicator) {
            liveIndicator.innerHTML = '<i class="bi bi-broadcast"></i> Live Sync Active';
            liveIndicator.className = 'badge bg-success bg-opacity-75 text-white d-flex align-items-center gap-1';
        }
    });

    // 🌟 CHILD ADDED (New booking arrives)
    bookingsRef.on('child_added', (snapshot) => {
        const booking = snapshot.val();
        const id = snapshot.key;
        if (!booking || !id) return;
        booking.id = id;

        // If booking is completed/cancelled/no-show, ignore for future bookings table
        if (['completed', 'job_cancelled', 'no_show'].includes(booking.status)) {
            return;
        }

        // If already in table, mark known and return
        if (knownBookingIds.has(id)) {
            return;
        }

        knownBookingIds.add(id);

        // Only alert and animate if this is a newly arrived booking after initial load
        if (isInitialFirebaseLoadDone) {
            playNewBookingChime();

            // Show Toast Alert with "View" action
            showDashboardToast(
                '🚗 New Booking Received!',
                `Ref: <strong>${booking.ref_no || id}</strong> &bull; ${booking.passenger_name || 'Passenger'}<br><small>${booking.pickup_address ? booking.pickup_address.substring(0, 30) : ''}</small>`,
                'booking',
                8000,
                {
                    text: 'View',
                    onClick: () => {
                        const targetRow = document.getElementById('booking-row-' + id);
                        if (targetRow) {
                            targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            targetRow.classList.remove('new-booking-highlight');
                            void targetRow.offsetWidth;
                            targetRow.classList.add('new-booking-highlight');
                        }
                    }
                }
            );

            // Prepend new row to table
            const tbody = document.getElementById('bookingsTableBody');
            if (tbody) {
                const emptyRow = document.getElementById('emptyBookingsRow');
                if (emptyRow) emptyRow.remove();

                const newRowHtml = buildBookingRowHtml(booking, true);
                tbody.insertAdjacentHTML('afterbegin', newRowHtml);

                const newRowEl = document.getElementById('booking-row-' + id);
                if (newRowEl) {
                    bindRowEvents(newRowEl);
                }

                updateBookingsCountBadge(1);
            }
        }
    });

    // 🌟 CHILD CHANGED (Live status or driver update)
    bookingsRef.on('child_changed', (snapshot) => {
        const booking = snapshot.val();
        const id = snapshot.key;
        if (!booking || !id) return;
        booking.id = id;

        // If status changed to completed/cancelled/no-show, remove from future bookings
        if (['completed', 'job_cancelled', 'no_show'].includes(booking.status)) {
            removeBookingRowFromTable(id);
            return;
        }

        // If exists in table, update in-place
        const existingRow = document.getElementById('booking-row-' + id);
        if (existingRow) {
            updateBookingRowData(booking);
        } else if (!['completed', 'job_cancelled', 'no_show'].includes(booking.status)) {
            // Re-add if it became active
            const tbody = document.getElementById('bookingsTableBody');
            if (tbody) {
                const emptyRow = document.getElementById('emptyBookingsRow');
                if (emptyRow) emptyRow.remove();

                const newRowHtml = buildBookingRowHtml(booking, false);
                tbody.insertAdjacentHTML('afterbegin', newRowHtml);

                const newRowEl = document.getElementById('booking-row-' + id);
                if (newRowEl) {
                    bindRowEvents(newRowEl);
                }
                updateBookingsCountBadge(1);
            }
        }
    });

    // 🌟 CHILD REMOVED
    bookingsRef.on('child_removed', (snapshot) => {
        const id = snapshot.key;
        if (id) {
            removeBookingRowFromTable(id);
        }
    });
}

// ⚡ Main DOM Loaded initialization
document.addEventListener('DOMContentLoaded', function () {
    // 🚀 Initialize Realtime Firebase Sync for bookings & drivers
    initFirebase();
    // Filter collapse toggle
    const collapseEl = document.getElementById('filtersCollapse');
    const chevron = document.querySelector('.filter-chevron');
    if (collapseEl && chevron) {
        collapseEl.addEventListener('show.bs.collapse', () => {
            chevron.classList.remove('bi-chevron-down');
            chevron.classList.add('bi-chevron-up');
        });
        collapseEl.addEventListener('hide.bs.collapse', () => {
            chevron.classList.remove('bi-chevron-up');
            chevron.classList.add('bi-chevron-down');
        });
    }

    // Initial binding of table row events
    bindRowEvents(document);

    // 🚗 Dispatch Driver Modal Submission (AJAX - Zero Reload)
    const dispatchModalEl = document.getElementById('dispatchDriverModal');
    const bookingIdField = document.getElementById('dispatchBookingId');
    const dispatchForm = document.getElementById('dispatchDriverForm');
    const driverSelect = document.getElementById('driverSelect');

    if (dispatchModalEl) {
        dispatchModalEl.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (button) {
                const bookingId = button.getAttribute('data-booking-id');
                if (bookingIdField) bookingIdField.value = bookingId;
            }
        });
    }

    if (dispatchForm) {
        dispatchForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const submitBtn = dispatchForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerText = 'Dispatching...';

            const bookingId = bookingIdField.value;
            const selectedDriverId = driverSelect ? driverSelect.value : '';

            try {
                const response = await fetch("{{ route('booking.dispatchDriver') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new FormData(dispatchForm)
                });

                const data = await response.json().catch(() => null);

                if (response.ok && data?.success) {
                    showDashboardToast('Driver Dispatched', 'Driver assigned to booking successfully!', 'success');
                    const modal = bootstrap.Modal.getInstance(dispatchModalEl);
                    if (modal) modal.hide();

                    // Update row in table immediately
                    const row = document.getElementById('booking-row-' + bookingId);
                    if (row) {
                        const driver = DRIVERS_MAP[selectedDriverId];
                        const driverName = driver ? `${driver.call_sign || ''}/${driver.name || ''}` : 'Assigned';
                        const driverCell = row.querySelector('.driver-cell');
                        if (driverCell) {
                            driverCell.innerHTML = `<span class="badge rounded-pill bg-danger bg-opacity-90 px-3 py-2 me-1 driver-badge">${driverName}</span>`;
                        }

                        const dispatchItem = row.querySelector('.action-dispatch-item');
                        const trackItem = row.querySelector('.action-track-item');
                        if (dispatchItem) dispatchItem.style.display = 'none';
                        if (trackItem) trackItem.style.display = '';
                    }
                } else {
                    const errorMsg = data?.error || data?.message || 'Failed to dispatch driver.';
                    showDashboardToast('Dispatch Failed', errorMsg, 'danger');
                }
            } catch (err) {
                console.error('Dispatch error:', err);
                showDashboardToast('Network Error', 'Could not complete dispatch.', 'danger');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Dispatch';
            }
        });
    }

    // ✉️ Send SMS Submit Handler (AJAX)
    const sendSmsBtn = document.getElementById("sendSmsNowBtn");
    if (sendSmsBtn) {
        sendSmsBtn.addEventListener("click", async function () {
            const bookingId = document.getElementById("smsBookingId").value;
            const phone = document.getElementById("smsPhone").value;
            const message = document.getElementById("smsMessage").value;

            if (!phone.trim()) {
                showDashboardToast('Required', 'Please enter phone number', 'warning');
                return;
            }
            if (!message.trim()) {
                showDashboardToast('Required', 'Please enter message content', 'warning');
                return;
            }

            this.disabled = true;
            try {
                const response = await fetch("{{ route('bookings.sendSmsDashboard') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": CSRF_TOKEN
                    },
                    body: JSON.stringify({ booking_id: bookingId, phone, message })
                });

                const data = await response.json();
                if (response.ok && data.success === true) {
                    showDashboardToast('SMS Sent', 'Confirmation SMS sent successfully!', 'success');
                    const modal = bootstrap.Modal.getInstance(document.getElementById("sendSmsModal"));
                    if (modal) modal.hide();
                } else {
                    showDashboardToast('SMS Failed', data.message || 'SMS failed to send.', 'danger');
                }
            } catch (err) {
                console.error('SMS send error:', err);
                showDashboardToast('Error', 'Error sending SMS.', 'danger');
            } finally {
                this.disabled = false;
            }
        });
    }

    // 📧 Send Email Submit Handler (AJAX)
    const sendEmailBtn = document.getElementById("sendEmailNowBtn");
    if (sendEmailBtn) {
        sendEmailBtn.addEventListener("click", async function () {
            const bookingId = document.getElementById("emailBookingId").value;
            const email = document.getElementById("emailAddress").value;
            const message = document.getElementById("emailMessage").value;

            if (!email.trim()) {
                showDashboardToast('Required', 'Please enter email address', 'warning');
                return;
            }

            this.disabled = true;
            try {
                const response = await fetch("/bookings/send-email", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": CSRF_TOKEN
                    },
                    body: JSON.stringify({ booking_id: bookingId, email: email, message: message })
                });

                const data = await response.json();
                if (response.ok && data.status === "success") {
                    showDashboardToast('Email Sent', 'Confirmation Email sent successfully!', 'success');
                    const modal = bootstrap.Modal.getInstance(document.getElementById("sendEmailModal"));
                    if (modal) modal.hide();
                } else {
                    showDashboardToast('Email Failed', data.message || 'Email failed to send.', 'danger');
                }
            } catch (err) {
                console.error('Email send error:', err);
                showDashboardToast('Error', 'Error sending email.', 'danger');
            } finally {
                this.disabled = false;
            }
        });
    }

    // Save Metrics to LocalStorage
    localStorage.setItem("totalUsers", "{{ $totalUsers ?? 0 }}");
    localStorage.setItem("todaysRevenue", "{{ $todaysRevenue ?? 0 }}");
    localStorage.setItem("revenueBreakdown", JSON.stringify({
        Cash: "{{ $revenueBreakdown['Cash'] ?? 0 }}",
        Card: "{{ $revenueBreakdown['Card'] ?? 0 }}",
        Account: "{{ $revenueBreakdown['Account'] ?? 0 }}"
    }));
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

<!-- Flatpickr JS & Toolbar Initialization -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarIconBtn = document.getElementById('calendarIconBtn');
    const fromInput = document.getElementById('fromDateInput');
    const toInput = document.getElementById('toDateInput');
    const searchForm = document.getElementById('dashboardSearchForm');

    // Default dates from request
    let defaultDates = [];
    const reqFrom = "{{ request('from_date') }}";
    const reqTo = "{{ request('to_date') }}";
    const reqRange = "{{ request('date_range') }}";

    if (reqFrom && reqTo) {
        defaultDates = [reqFrom, reqTo];
    } else if (reqFrom) {
        defaultDates = [reqFrom];
    } else if (reqRange) {
        const parts = reqRange.split(' to ');
        if (parts.length === 2) defaultDates = [parts[0].trim(), parts[1].trim()];
        else if (parts.length === 1 && parts[0].trim()) defaultDates = [parts[0].trim()];
    }

    const fp = flatpickr("#dateRangePicker", {
        mode: "range",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d M Y",
        altInputClass: "form-control border-start-0 ps-0 bg-white",
        defaultDate: defaultDates.length > 0 ? defaultDates : null,
        onChange: function (selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                fromInput.value = instance.formatDate(selectedDates[0], "Y-m-d");
                toInput.value = instance.formatDate(selectedDates[1], "Y-m-d");
            } else if (selectedDates.length === 1) {
                fromInput.value = instance.formatDate(selectedDates[0], "Y-m-d");
                toInput.value = instance.formatDate(selectedDates[0], "Y-m-d");
            } else {
                fromInput.value = '';
                toInput.value = '';
            }
        }
    });

    if (calendarIconBtn) {
        calendarIconBtn.addEventListener('click', function () {
            fp.open();
        });
    }

    // Quick Date Preset Buttons
    document.querySelectorAll('.quick-date-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const preset = this.getAttribute('data-preset');
            const today = new Date();
            let fromD = null;
            let toD = null;

            if (preset === 'today') {
                fromD = today;
                toD = today;
            } else if (preset === 'tomorrow') {
                const tom = new Date();
                tom.setDate(today.getDate() + 1);
                fromD = tom;
                toD = tom;
            } else if (preset === 'week') {
                const in7 = new Date();
                in7.setDate(today.getDate() + 7);
                fromD = today;
                toD = in7;
            } else if (preset === 'clear') {
                fp.clear();
                fromInput.value = '';
                toInput.value = '';
                document.getElementById('dateRangePicker').value = '';
                searchForm.submit();
                return;
            }

            if (fromD && toD) {
                fp.setDate([fromD, toD], true);
                fromInput.value = fp.formatDate(fromD, "Y-m-d");
                toInput.value = fp.formatDate(toD, "Y-m-d");
                searchForm.submit();
            }
        });
    });

    // =========================================================================
    // ⚡ INSTANT PRICE CALCULATOR INTERACTION
    // =========================================================================
    const calcPickupInput = document.getElementById('calcPickupInput');
    const calcDropoffInput = document.getElementById('calcDropoffInput');
    const calcViaInput = document.getElementById('calcViaInput');
    const calcViaWrapper = document.getElementById('calcViaWrapper');
    const addViaToggleBtn = document.getElementById('addViaToggleBtn');
    const removeViaBtn = document.getElementById('removeViaBtn');
    const swapBtn = document.getElementById('swapPickupDropoffBtn');
    const calcVehicleSelect = document.getElementById('calcVehicleSelect');
    const calcDateInput = document.getElementById('calcDateInput');
    const calcTimeInput = document.getElementById('calcTimeInput');
    const calcParkingInput = document.getElementById('calcParkingInput');
    const getPriceBtn = document.getElementById('getInstantPriceBtn');
    const calcFinalBadge = document.getElementById('calcFinalPriceBadge');
    const calcDistText = document.getElementById('calcDistText');
    const calcMilesInput = document.getElementById('calcMilesInput');
    const calcBreakdownText = document.getElementById('calcBreakdownText');
    const calcLoader = document.getElementById('calcLoader');

    let currentBaseFare = 0;

    // Toggle Via Input
    if (addViaToggleBtn && calcViaWrapper) {
        addViaToggleBtn.addEventListener('click', function () {
            calcViaWrapper.style.display = 'block';
            this.style.display = 'none';
            if (calcViaInput) calcViaInput.focus();
        });
    }

    // Remove Via Input
    if (removeViaBtn && calcViaWrapper) {
        removeViaBtn.addEventListener('click', function () {
            calcViaWrapper.style.display = 'none';
            if (calcViaInput) calcViaInput.value = '';
            if (addViaToggleBtn) addViaToggleBtn.style.display = 'inline-block';
            fetchDashboardInstantPrice();
        });
    }

    // Swap Pickup & Dropoff
    if (swapBtn && calcPickupInput && calcDropoffInput) {
        swapBtn.addEventListener('click', function () {
            const temp = calcPickupInput.value;
            calcPickupInput.value = calcDropoffInput.value;
            calcDropoffInput.value = temp;
            if (calcPickupInput.value && calcDropoffInput.value) {
                fetchDashboardInstantPrice();
            }
        });
    }

    // Extract UK Postcode or fallback to full address
    function extractUkPostcode(addr) {
        if (!addr) return '';
        const postcodeRegex = /\b[A-Z]{1,2}\d{1,2}[A-Z]?\s*\d?[A-Z]{0,2}\b/i;
        const match = addr.match(postcodeRegex);
        return match ? match[0].toUpperCase().trim() : addr.trim();
    }

    // Compute Total Price with Parking
    function updateInstantTotalDisplay() {
        const parking = parseFloat(calcParkingInput?.value) || 0;
        const total = currentBaseFare + parking;
        if (calcFinalBadge) {
            calcFinalBadge.textContent = '£' + (total > 0 ? total.toFixed(2) : '0.00');
        }
        if (calcBreakdownText) {
            calcBreakdownText.textContent = `Base: £${currentBaseFare.toFixed(2)}` + (parking > 0 ? ` + Park: £${parking.toFixed(2)}` : '');
        }
    }

    // Fetch Instant Price from Backend API
    async function fetchDashboardInstantPrice() {
        const pickupRaw = calcPickupInput?.value.trim();
        const dropoffRaw = calcDropoffInput?.value.trim();

        if (!pickupRaw || !dropoffRaw) {
            return;
        }

        const pickup = extractUkPostcode(pickupRaw);
        const dropoff = extractUkPostcode(dropoffRaw);
        const vehicle = calcVehicleSelect?.value || 'Saloon';
        const date = calcDateInput?.value || '';
        const time = calcTimeInput?.value || '';
        const viaVal = calcViaWrapper?.style.display !== 'none' ? calcViaInput?.value.trim() : '';
        const via = viaVal ? extractUkPostcode(viaVal) : '';

        if (calcLoader) calcLoader.style.display = 'inline-block';
        if (getPriceBtn) getPriceBtn.disabled = true;

        try {
            let url = `/admin/booking/get-price?vehicle_id=${encodeURIComponent(vehicle)}&pickup=${encodeURIComponent(pickup)}&dropoff=${encodeURIComponent(dropoff)}&pickup_date=${encodeURIComponent(date)}&pickup_time=${encodeURIComponent(time)}`;
            if (via) {
                url += `&vias[]=${encodeURIComponent(via)}`;
            }

            const response = await fetch(url);
            const data = await response.json();

            if (data && data.success) {
                currentBaseFare = parseFloat(data.price) || 0;
                const distance = parseFloat(data.journey_distance || data.total_distance || 0);
                const formattedMiles = `${distance.toFixed(2)} Miles`;
                if (calcDistText) {
                    calcDistText.textContent = formattedMiles;
                }
                if (calcMilesInput) {
                    calcMilesInput.value = `${distance.toFixed(2)} mi`;
                }
                updateInstantTotalDisplay();
            } else {
                if (calcBreakdownText) {
                    calcBreakdownText.textContent = data.message || 'No route found';
                }
            }
        } catch (err) {
            console.error('Price calculation error:', err);
        } finally {
            if (calcLoader) calcLoader.style.display = 'none';
            if (getPriceBtn) getPriceBtn.disabled = false;
        }
    }

    // Attach Price Calculation Event Listeners
    if (getPriceBtn) {
        getPriceBtn.addEventListener('click', fetchDashboardInstantPrice);
    }
    if (calcVehicleSelect) {
        calcVehicleSelect.addEventListener('change', fetchDashboardInstantPrice);
    }
    if (calcDateInput) {
        calcDateInput.addEventListener('change', fetchDashboardInstantPrice);
    }
    if (calcTimeInput) {
        calcTimeInput.addEventListener('change', fetchDashboardInstantPrice);
    }
    if (calcParkingInput) {
        calcParkingInput.addEventListener('input', updateInstantTotalDisplay);
    }

    // Google Places Autocomplete initialization on Dashboard inputs
    function initDashboardPlaces() {
        if (!window.google || !google.maps || !google.maps.places) return;
        const opts = { componentRestrictions: { country: 'gb' } };

        if (calcPickupInput) {
            const autoPickup = new google.maps.places.Autocomplete(calcPickupInput, opts);
            autoPickup.addListener('place_changed', function () {
                const place = autoPickup.getPlace();
                if (place && place.formatted_address) {
                    calcPickupInput.value = place.formatted_address;
                }
                if (calcDropoffInput && calcDropoffInput.value.trim()) {
                    fetchDashboardInstantPrice();
                }
            });
        }

        if (calcDropoffInput) {
            const autoDropoff = new google.maps.places.Autocomplete(calcDropoffInput, opts);
            autoDropoff.addListener('place_changed', function () {
                const place = autoDropoff.getPlace();
                if (place && place.formatted_address) {
                    calcDropoffInput.value = place.formatted_address;
                }
                if (calcPickupInput && calcPickupInput.value.trim()) {
                    fetchDashboardInstantPrice();
                }
            });
        }

        if (calcViaInput) {
            const autoVia = new google.maps.places.Autocomplete(calcViaInput, opts);
            autoVia.addListener('place_changed', function () {
                const place = autoVia.getPlace();
                if (place && place.formatted_address) {
                    calcViaInput.value = place.formatted_address;
                }
                fetchDashboardInstantPrice();
            });
        }
    }

    if (window.google && window.google.maps && window.google.maps.places) {
        initDashboardPlaces();
    } else {
        // Load Google Places library asynchronously if not loaded
        const placesScript = document.createElement('script');
        placesScript.src = "https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=initDashboardPlacesGlobal";
        placesScript.async = true;
        placesScript.defer = true;
        window.initDashboardPlacesGlobal = initDashboardPlaces;
        document.head.appendChild(placesScript);
    }
});
</script>

@endpush
