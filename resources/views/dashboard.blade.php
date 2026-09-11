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


/* Dropdown Menu Styles & Elevation */
.actions-dropdown .dropdown-menu {
  z-index: 1060;
  min-width: 195px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
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
#bookingsPaginationContainer {
    margin: 0 !important;
}
#bookingsPaginationContainer nav {
    display: inline-flex !important;
    align-items: center !important;
    gap: 12px !important;
    margin: 0 !important;
}
#bookingsPaginationContainer p.text-muted,
#bookingsPaginationContainer p.small {
    margin-bottom: 0 !important;
    font-size: 12px !important;
    color: #64748b !important;
    white-space: nowrap !important;
}
.pagination {
    margin: 0 !important;
    padding: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
}

.pagination .page-item {
    margin: 0 2px;
}

.pagination .page-link {
    border: 1px solid #d1b96d;
    color: #6b5e2e;
    padding: 3px 9px;
    border-radius: 6px;
    background-color: #fff;
    font-weight: 500;
    font-size: 12px;
    line-height: 1.4;
    transition: all 0.15s ease;
}

.pagination .page-item.active .page-link {
    background-color: #ffe8a1;
    border-color: #bfa75a;
    color: #000;
    font-weight: 700;
}

.pagination .page-link:hover {
    background-color: #ffe8a1;
    color: #000;
}

.pagination .page-item.disabled .page-link {
    color: #9c8f5c;
    background-color: transparent;
    border-color: #e0d4a6;
    opacity: 0.6;
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
    overflow-x: auto !important;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
    width: 100%;
    min-height: 280px;
}

.table-responsive::-webkit-scrollbar {
    height: 7px;
}
.table-responsive::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}
.table-responsive::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
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

/* 🌟 Fullscreen Screen-Level Search & Filter Loader */
.dashboard-global-loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.22s ease-in-out;
}

.dashboard-global-loader.active {
    opacity: 1;
    pointer-events: all;
}

.loader-content-box {
    background: #111827;
    border: 1px solid rgba(230, 176, 74, 0.4);
    border-radius: 18px;
    padding: 22px 28px;
    text-align: center;
    min-width: 220px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 25px rgba(230, 176, 74, 0.2) !important;
    animation: loaderPopIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.spinner-crown-wrapper {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.spinner-center-icon {
    position: absolute;
    font-size: 13px;
    color: #E6B04A !important;
}

@keyframes loaderPopIn {
    0% {
        transform: scale(0.88) translateY(10px);
    }
    100% {
        transform: scale(1) translateY(0);
    }
}

/* 💎 Future Bookings Table UX Polish */
.custom-dashboard-table {
    min-width: 1380px;
    width: 100%;
    font-size: 12px;
    border-collapse: separate;
    border-spacing: 0;
}
.custom-dashboard-table thead th {
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #475569;
    background: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
    white-space: nowrap;
    padding: 11px 8px;
    vertical-align: middle;
}
.custom-dashboard-table tbody tr {
    transition: background-color 0.15s ease;
}
.custom-dashboard-table tbody tr:hover {
    background-color: #f1f5f9 !important;
}
.custom-dashboard-table td {
    padding: 9px 8px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}
.truncate-cell {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: inline-block;
    vertical-align: middle;
    cursor: help;
}
.truncate-cell:hover {
    text-decoration: underline dotted #94a3b8;
}
.statusSelect {
    cursor: pointer;
    transition: all 0.2s ease;
}
.statusSelect:focus {
    box-shadow: 0 0 0 2px rgba(184, 115, 51, 0.3);
}
</style>

<!-- 🌟 Fullscreen Screen-Level Search & Filter Loader -->
<div id="dashboardGlobalLoader" class="dashboard-global-loader" style="display: none;">
    <div class="loader-content-box shadow-lg">
        <div class="spinner-crown-wrapper mb-2">
            <div class="spinner-border" role="status" style="width: 2.6rem; height: 2.6rem; border-width: 3px; color: #E6B04A !important;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <i class="bi bi-search spinner-center-icon"></i>
        </div>
        <h6 class="fw-bold text-white mb-1" style="font-size: 14px; letter-spacing: 0.3px;">Searching Bookings...</h6>
        <p class="small mb-0" style="font-size: 11.5px; color: #94a3b8 !important;">Applying filters & updating results</p>
    </div>
</div>

<div class="container-fluid dashboard-section">
   <div class="row g-3 mb-2">

    <!-- 🔍 1/3 Left Card: Search & Filter Bookings -->
    <div class="col-12 col-xl-4 col-lg-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 14px; background: #ffffff; border: 1px solid #eaedf1;">
            <div class="card-header bg-transparent border-0 pb-1 pt-3 px-3">
                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2" style="font-size: 13.5px;">
                    <i class="bi bi-search text-warning"></i> Search & Filter Bookings
                </h6>
            </div>
            <div class="card-body p-3 pt-2 d-flex flex-column justify-content-between">
                <form action="{{ route('bookings.search.main') }}" method="GET" id="dashboardSearchForm">
                    <div class="row g-2 mb-2">
                        <!-- Universal Keyword Search Input -->
                        <div class="col-12">
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
                                       style="border-radius: 0 10px 10px 0; font-size: 12px;">
                            </div>
                        </div>

                        <!-- Date Range Picker with Calendar Trigger -->
                        <div class="col-12">
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
                                       style="border-radius: 0 10px 10px 0; font-size: 12px; cursor: pointer;">
                                <input type="hidden" name="from_date" id="fromDateInput" value="{{ request('from_date') }}">
                                <input type="hidden" name="to_date" id="toDateInput" value="{{ request('to_date') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <!-- Driver Select -->
                        <div class="col-7">
                            <select name="driver_id" class="form-select text-dark" style="border-radius: 10px; font-size: 12px;">
                                <option value="">All Drivers</option>
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
                        <div class="col-5">
                            <select name="payment_type" class="form-select" style="border-radius: 10px; font-size: 12px;">
                                <option value="">All Payments</option>
                                <option value="Cash" {{ request('payment_type') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Card" {{ request('payment_type') == 'Card' ? 'selected' : '' }}>Card</option>
                                <option value="Account" {{ request('payment_type') == 'Account' ? 'selected' : '' }}>Account</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex gap-1 mb-2" id="filterButtonsGroup">
                        <button type="submit" id="filterSubmitBtn" class="btn text-white fw-bold w-100 d-flex align-items-center justify-content-center gap-1 shadow-sm" style="background: linear-gradient(135deg, #B87333, #d48b48); border-radius: 10px; border: none; font-size: 12px; height: 34px;">
                            <i class="bi bi-search"></i>
                            <span>Filter</span>
                        </button>
                        <div id="filterResetWrapper" class="d-flex">
                            @if(request('search') || request('date_range') || request('from_date') || request('to_date') || request('driver_id') || request('payment_type'))
                            <button type="button" id="resetFiltersBtn" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" title="Reset All Filters" style="border-radius: 10px; width: 34px; height: 34px; flex-shrink: 0;">
                                <i class="bi bi-x-lg"></i>
                            </button>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Date Presets -->
                    <div class="d-flex align-items-center gap-1 pt-1 border-top flex-wrap" style="border-color: #f1f5f9 !important;">
                        <span class="text-muted small me-1" style="font-size: 10.5px;"><i class="bi bi-lightning-charge-fill text-warning"></i> Quick:</span>
                        <button type="button" class="btn btn-sm btn-light py-0 px-2 fw-semibold quick-date-btn" data-preset="today" style="border-radius: 12px; font-size: 10.5px; border: 1px solid #e2e8f0;">Today</button>
                        <button type="button" class="btn btn-sm btn-light py-0 px-2 fw-semibold quick-date-btn" data-preset="tomorrow" style="border-radius: 12px; font-size: 10.5px; border: 1px solid #e2e8f0;">Tomorrow</button>
                        <button type="button" class="btn btn-sm btn-light py-0 px-2 fw-semibold quick-date-btn" data-preset="week" style="border-radius: 12px; font-size: 10.5px; border: 1px solid #e2e8f0;">7 Days</button>
                        <button type="button" class="btn btn-sm btn-light py-0 px-2 fw-semibold quick-date-btn" data-preset="clear" style="border-radius: 12px; font-size: 10.5px; border: 1px solid #e2e8f0;">All</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ⚡ 1/3 Middle Card: Get Instant Price -->
    <div class="col-12 col-xl-4 col-lg-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 14px; background: #ffffff; border: 1px solid #eaedf1;">
            <div class="card-header bg-transparent border-0 pb-1 pt-3 px-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2" style="font-size: 13.5px;">
                    <i class="bi bi-calculator-fill text-warning"></i> Get Instant Price
                </h6>
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-sm btn-light py-0 px-2 text-dark" id="swapPickupDropoffBtn" title="Swap Pickup & Dropoff" style="border-radius: 8px; font-size: 10.5px; border: 1px solid #e2e8f0;">
                        <i class="bi bi-arrow-left-right text-primary"></i> Swap
                    </button>
                    <button type="button" class="btn btn-sm btn-light py-0 px-2 text-dark" id="addViaToggleBtn" style="border-radius: 8px; font-size: 10.5px; border: 1px solid #e2e8f0;">
                        <i class="bi bi-plus-circle text-success"></i> +Via
                    </button>
                </div>
            </div>
            <div class="card-body p-3 pt-2 d-flex flex-column justify-content-between">
                <div>
                    <div class="row g-2 mb-2">
                        <!-- Pickup Address -->
                        <div class="col-12">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-success py-1" style="border-radius: 10px 0 0 10px;">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </span>
                                <input type="text" 
                                       id="calcPickupInput" 
                                       class="form-control border-start-0 ps-0" 
                                       placeholder="Pickup address / postcode" 
                                       style="border-radius: 0 10px 10px 0; font-size: 12px;">
                            </div>
                        </div>
                        <!-- Dropoff Address -->
                        <div class="col-12">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-danger py-1" style="border-radius: 10px 0 0 10px;">
                                    <i class="bi bi-pin-map-fill"></i>
                                </span>
                                <input type="text" 
                                       id="calcDropoffInput" 
                                       class="form-control border-start-0 ps-0" 
                                       placeholder="Dropoff address / postcode" 
                                       style="border-radius: 0 10px 10px 0; font-size: 12px;">
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
                                       placeholder="Via stop address or postcode" 
                                       style="font-size: 12px;">
                                <button class="btn btn-outline-secondary py-1 px-2 border-start-0" type="button" id="removeViaBtn" style="border-radius: 0 10px 10px 0;" title="Remove via">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Row: Vehicle, Date, Time -->
                    <div class="row g-2 mb-2">
                        <div class="col-5">
                            <select id="calcVehicleSelect" class="form-select" style="border-radius: 10px; font-size: 12px;">
                                <option value="Saloon" selected>Saloon</option>
                                <option value="Estate">Estate</option>
                                <option value="Executive">Executive</option>
                                <option value="MPV">MPV</option>
                                <option value="8 Seater">8 Seater</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <input type="date" id="calcDateInput" class="form-control" value="{{ date('Y-m-d') }}" style="border-radius: 10px; font-size: 12px;">
                        </div>
                        <div class="col-3">
                            <input type="time" id="calcTimeInput" class="form-control" value="{{ date('H:i') }}" style="border-radius: 10px; font-size: 12px;">
                        </div>
                    </div>
                </div>

                <!-- Price Result Output Bar -->
                <div class="p-2 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-1 mt-1" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div class="d-flex align-items-center gap-1 flex-wrap">
                        <span id="calcDistanceBadge" class="badge px-2 py-1 fw-bold d-flex align-items-center gap-1 shadow-sm" style="background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; font-size: 11px; border-radius: 7px;">
                            <i class="bi bi-speedometer2 text-primary"></i> <span id="calcDistText">0.00 Mi</span>
                        </span>
                        <span class="text-muted small fw-semibold" id="calcBreakdownText" style="font-size: 11px;">Fare: £0.00</span>
                        <span id="calcLoader" class="spinner-border spinner-border-sm text-warning ms-1" style="display: none;" role="status"></span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <button type="button" class="btn btn-sm text-white fw-bold px-2 py-1 d-flex align-items-center gap-1 shadow-sm" id="getInstantPriceBtn" style="background: linear-gradient(135deg, #B87333, #d48b48); border-radius: 7px; font-size: 11px; border: none;">
                            <i class="bi bi-lightning-fill"></i> Get Price
                        </button>
                        <span class="badge fs-6 fw-bold px-2 py-1 shadow-sm" id="calcFinalPriceBadge" style="background: #111827; color: #E6B04A !important; border: 1px solid rgba(230, 176, 74, 0.4); border-radius: 7px; font-size: 13.5px;">
                            £0.00
                        </span>
                    </div>
                </div>

                <!-- 🚀 Make Booking Prompt (Shown after calculating price) -->
                <div id="calcBookNowPrompt" class="p-2 mt-2 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2 animate__animated animate__fadeIn" style="display: none !important; background: #f0fdf4; border: 1px solid #86efac;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill text-success fs-6"></i>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 11.5px;">Do you want to make a booking against this?</div>
                            <div class="text-muted" style="font-size: 10.5px;">Total Fare: <strong id="calcPromptPrice" class="text-success fw-bold">£0.00</strong></div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm text-white fw-bold px-2.5 py-1 d-flex align-items-center gap-1 shadow-sm" id="openQuickBookingModalBtn" style="background: #16a34a; border-radius: 7px; font-size: 11px; border: none;">
                        <i class="bi bi-calendar2-plus-fill"></i> Continue & Book
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 📊 1/3 Right Card: Day-Wise Bookings Trend Graph -->
    <div class="col-12 col-xl-4 col-lg-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 14px; background: #ffffff; border: 1px solid #eaedf1;">
            <div class="card-header bg-transparent border-0 pt-3 pb-1 px-3 d-flex justify-content-between align-items-center flex-wrap gap-1">
                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-1.5" style="font-size: 13.5px;">
                    <i class="bi bi-graph-up-arrow text-warning"></i> Bookings Trend
                </h6>
                <div class="d-flex align-items-center gap-1 flex-wrap">
                    @php
                        $chartList = $dayWiseChartData ?? [];
                        $totalWindowBookings = array_sum(array_column($chartList, 'count'));
                        $todayBookingItem = collect($chartList)->firstWhere('is_today', true);
                        $todayCount = $todayBookingItem['count'] ?? 0;
                    @endphp
                    <span class="badge px-2 py-1 shadow-sm d-inline-flex align-items-center gap-1" style="background: #111827; color: #E6B04A; border: 1px solid rgba(230, 176, 74, 0.5); font-size: 11.5px; border-radius: 7px;" title="Today's Bookings">
                        <i class="bi bi-calendar-check-fill" style="color: #E6B04A;"></i> Today: <strong class="text-white ms-0.5">{{ $todayCount }}</strong>
                    </span>
                    <span class="badge px-2 py-1 shadow-sm d-inline-flex align-items-center gap-1" style="background: #fffbeb; color: #92400e; border: 1px solid #fde68a; font-size: 11.5px; border-radius: 7px;" title="15-Day Timeline Total">
                        <i class="bi bi-collection-fill" style="color: #d97706;"></i> 15D: <strong class="text-dark ms-0.5">{{ $totalWindowBookings }}</strong>
                    </span>
                </div>
            </div>
            <div class="card-body p-3 pt-1 d-flex flex-column justify-content-center">
                <div style="position: relative; height: 185px; width: 100%;">
                    <canvas id="dayWiseBookingsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

  </div>

  <div class="col-lg-12 mt-3">
        <div class="card shadow-sm border-0" style="border-radius: 14px; border: 1px solid #eaedf1 !important;">
            <div class="card-header bg-white py-2.5 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom" style="border-top-left-radius: 13px; border-top-right-radius: 13px; border-color: #f1f5f9 !important;">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 8px; background: rgba(184, 115, 51, 0.12); color: #B87333;">
                        <i class="bi bi-calendar2-range-fill fs-6"></i>
                    </span>
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 14px; letter-spacing: -0.2px;">
                        Future Bookings
                    </h6>
                    <span class="badge bg-dark text-white rounded-pill px-2.5 py-1" id="bookingsTotalCount" style="font-size: 11px;">{{ $bookings->total() }}</span>
                </div>
                <div class="d-flex align-items-center gap-2 gap-md-3 flex-wrap">
                    <div class="d-flex align-items-center" id="bookingsPaginationContainer">
                        {{ $bookings->links('pagination::bootstrap-5') }}
                    </div>
                    <span class="badge bg-success bg-opacity-75 text-white d-flex align-items-center gap-1.5 px-2.5 py-1.5 shadow-xs" id="liveSyncStatus" style="border-radius: 8px; font-size: 11px;">
                        <i class="bi bi-broadcast"></i> Live Sync Active
                    </span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 custom-dashboard-table" id="futureBookingsTable">
                   <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
    <tr class="text-uppercase text-muted" style="font-size: 11px; letter-spacing: 0.5px; font-weight: 700;">
        <th class="py-2.5 px-3">Ref#</th>
        <th class="py-2.5 px-2">Account Type</th>
        <th class="py-2.5 px-2">Passenger</th>
        <th class="py-2.5 px-2">Driver</th>
        <th class="py-2.5 px-2">Phone</th>
        <th class="py-2.5 px-2">Pickup</th>
        <th class="py-2.5 px-2">Dropoff</th>
        <th class="py-2.5 px-2 text-center">Vias</th>
        <th class="py-2.5 px-2">Job Date</th>
        <th class="py-2.5 px-2">Job Time</th>
        <th class="py-2.5 px-2 text-center">Vehicle</th>
        <th class="py-2.5 px-2">Flight</th>
        <th class="py-2.5 px-2">Price</th>
        <th class="py-2.5 px-2">Comments</th>
        <th class="py-2.5 px-2">Status</th>
        <th class="py-2.5 px-2 text-center">Platform</th>
        <th class="py-2.5 px-3 text-center">Actions</th>
    </tr>
</thead>
<tbody class="small" id="bookingsTableBody">
@include('partials.dashboard_table_rows', ['bookings' => $bookings, 'drivers' => $drivers])
</tbody>
                </table>
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




<!-- Booking Details & Staff Activity Modal -->
<div class="modal fade" id="viewBookingModal" tabindex="-1" aria-labelledby="viewBookingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable my-4">
    <div class="modal-content border-0 shadow-lg" style="background: #f8fafc;">
      
      <!-- Modal Header -->
      <div class="modal-header text-white" style="padding: 14px 20px;">
        <div class="d-flex align-items-center flex-wrap gap-2 w-100 pe-3">
          <div class="d-flex align-items-center">
            <span class="rounded-circle bg-warning bg-opacity-25 p-2 d-inline-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
              <i class="bi bi-journal-text text-warning fs-5"></i>
            </span>
            <div>
              <h5 class="modal-title fw-bold mb-0 text-white" id="viewBookingModalLabel" style="font-size: 1.1rem;">Booking Details & Activity History</h5>
              <div class="text-white-50" style="font-size: 0.75rem;">Complete itinerary, pricing breakdown & staff audit trail</div>
            </div>
          </div>
          <div class="ms-auto d-flex align-items-center gap-2">
            <span id="modal-ref-badge" class="badge" style="background: #E6B04A; color: #111827; font-weight: 700; font-size: 0.85rem; letter-spacing: 0.5px;">-</span>
            <span id="modal-status-badge" class="badge bg-secondary text-uppercase fw-semibold" style="font-size: 0.78rem; padding: 6px 10px;">-</span>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body p-3 p-md-4" style="max-height: calc(85vh - 120px); overflow-y: auto;">
        
        <!-- Top Stats Banner -->
        <div class="row g-2 g-md-3 mb-3">
          <div class="col-6 col-md-3">
            <div class="p-2 p-md-3 rounded-3 bg-white border shadow-sm h-100 d-flex flex-column justify-content-center">
              <span class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;"><i class="bi bi-calendar3 me-1 text-primary"></i> Pickup Date & Time</span>
              <span id="modal-top-pickup-time" class="fw-bold text-dark mt-1 text-truncate" style="font-size: 0.92rem;">-</span>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="p-2 p-md-3 rounded-3 bg-white border shadow-sm h-100 d-flex flex-column justify-content-center">
              <span class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;"><i class="bi bi-car-front-fill me-1 text-info"></i> Vehicle & Driver</span>
              <span id="modal-top-driver" class="fw-bold text-dark mt-1 text-truncate" style="font-size: 0.92rem;">-</span>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="p-2 p-md-3 rounded-3 bg-white border shadow-sm h-100 d-flex flex-column justify-content-center">
              <span class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;"><i class="bi bi-cash-stack me-1 text-success"></i> Total Fare</span>
              <span id="modal-top-price" class="fw-bold text-success mt-1 text-truncate" style="font-size: 1.05rem;">-</span>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="p-2 p-md-3 rounded-3 bg-white border shadow-sm h-100 d-flex flex-column justify-content-center">
              <span class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;"><i class="bi bi-credit-card-2-front me-1 text-warning"></i> Payment Method</span>
              <span id="modal-top-payment" class="fw-bold text-dark mt-1 text-truncate text-capitalize" style="font-size: 0.92rem;">-</span>
            </div>
          </div>
        </div>

        <div class="row g-3">
          
          <!-- LEFT COLUMN: Booking & Trip Info -->
          <div class="col-lg-7">
            
            <!-- Passenger Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-3 bg-white">
              <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center">
                <i class="bi bi-person-circle text-primary me-2 fs-6"></i>
                <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.88rem;">Passenger Information</h6>
              </div>
              <div class="card-body p-3">
                <div class="row g-2">
                  <div class="col-sm-6">
                    <div class="small text-muted" style="font-size: 0.78rem;">Passenger Name</div>
                    <div id="modal-passenger_name" class="fw-semibold text-dark">-</div>
                  </div>
                  <div class="col-sm-6">
                    <div class="small text-muted" style="font-size: 0.78rem;">Phone Number</div>
                    <div id="modal-phone-container" class="fw-semibold text-dark d-flex align-items-center gap-2 flex-wrap">
                      <span id="modal-phone_no">-</span>
                      <a id="modal-phone-call" href="#" class="btn btn-xs btn-outline-primary py-0 px-2 rounded-pill" style="font-size: 0.72rem;"><i class="bi bi-telephone-fill"></i> Call</a>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="small text-muted" style="font-size: 0.78rem;">Email Address</div>
                    <div id="modal-email" class="fw-semibold text-dark text-break">-</div>
                  </div>
                  <div class="col-sm-6">
                    <div class="small text-muted" style="font-size: 0.78rem;">Flight Number</div>
                    <div id="modal-flight_no" class="fw-semibold text-dark">-</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Route & Stops Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-3 bg-white">
              <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                  <i class="bi bi-geo-alt-fill text-danger me-2 fs-6"></i>
                  <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.88rem;">Journey Route & Stops</h6>
                </div>
                <a id="modal-route-maps-link" href="#" target="_blank" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;">
                  <i class="bi bi-map me-1"></i> Open in Maps
                </a>
              </div>
              <div class="card-body p-3">
                <div class="booking-route-timeline position-relative ps-4 py-1">
                  <!-- Pickup -->
                  <div class="route-node position-relative mb-3">
                    <span class="route-dot bg-success rounded-circle position-absolute" style="left: -28px; top: 3px; width: 14px; height: 14px; border: 3px solid #d1e7dd;"></span>
                    <div class="small fw-bold text-success text-uppercase" style="font-size: 0.7rem;">Pickup Location</div>
                    <div id="modal-pickup_address" class="text-dark fw-medium" style="font-size: 0.88rem;">-</div>
                    <div id="modal-pickup_time_sub" class="text-muted small mt-0" style="font-size: 0.75rem;"></div>
                  </div>
                  
                  <!-- Via Stops (rendered dynamically) -->
                  <div id="modal-vias-container" class="mb-2"></div>

                  <!-- Dropoff -->
                  <div class="route-node position-relative">
                    <span class="route-dot bg-danger rounded-circle position-absolute" style="left: -28px; top: 3px; width: 14px; height: 14px; border: 3px solid #f8d7da;"></span>
                    <div class="small fw-bold text-danger text-uppercase" style="font-size: 0.7rem;">Dropoff Destination</div>
                    <div id="modal-dropoff_address" class="text-dark fw-medium" style="font-size: 0.88rem;">-</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Vehicle, Driver & Pricing Card -->
            <div class="card border-0 shadow-sm rounded-3 bg-white">
              <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center">
                <i class="bi bi-tag-fill text-warning me-2 fs-6"></i>
                <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.88rem;">Vehicle, Driver & Pricing Breakdown</h6>
              </div>
              <div class="card-body p-3">
                <div class="row g-2 mb-3 pb-2 border-bottom">
                  <div class="col-sm-6">
                    <div class="small text-muted" style="font-size: 0.78rem;">Vehicle Type</div>
                    <div id="modal-vehicle_id" class="fw-semibold text-dark">-</div>
                  </div>
                  <div class="col-sm-6">
                    <div class="small text-muted" style="font-size: 0.78rem;">Assigned Driver</div>
                    <div id="modal-driver-details" class="fw-semibold text-dark">-</div>
                  </div>
                </div>

                <div class="row g-2 mb-2">
                  <div class="col-4 col-sm-3">
                    <div class="small text-muted" style="font-size: 0.75rem;">Base Fare</div>
                    <div id="modal-fare" class="fw-semibold text-dark">£0.00</div>
                  </div>
                  <div class="col-4 col-sm-3">
                    <div class="small text-muted" style="font-size: 0.75rem;">Parking Fee</div>
                    <div id="modal-parking" class="fw-semibold text-dark">£0.00</div>
                  </div>
                  <div class="col-4 col-sm-3">
                    <div class="small text-muted" style="font-size: 0.75rem;">Waiting Fee</div>
                    <div id="modal-waiting" class="fw-semibold text-dark">£0.00</div>
                  </div>
                  <div class="col-4 col-sm-3">
                    <div class="small text-muted" style="font-size: 0.75rem;">Extra / Toll</div>
                    <div id="modal-extra" class="fw-semibold text-dark">£0.00</div>
                  </div>
                </div>

                <div class="p-2 rounded-2 bg-light border d-flex align-items-center justify-content-between mb-2">
                  <span class="fw-bold text-dark" style="font-size: 0.85rem;">Total Calculated Price</span>
                  <span id="modal-price" class="fw-bold text-success fs-6">£0.00</span>
                </div>

                <div class="row g-2 pt-1">
                  <div class="col-sm-6">
                    <div class="small text-muted" style="font-size: 0.78rem;">Child Seat Requested</div>
                    <div id="modal-child_seat" class="fw-semibold text-dark">-</div>
                  </div>
                  <div class="col-sm-6">
                    <div class="small text-muted" style="font-size: 0.78rem;">Payment Type</div>
                    <div id="modal-payment_type" class="fw-semibold text-dark text-capitalize">-</div>
                  </div>
                  <div class="col-12">
                    <div class="small text-muted" style="font-size: 0.78rem;">Job Comments & Special Instructions</div>
                    <div id="modal-comment" class="p-2 rounded bg-light text-secondary small text-break fst-italic" style="min-height: 38px;">-</div>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- RIGHT COLUMN: Staff Activity & Follow-up Audit Trail -->
          <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white d-flex flex-column">
              <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                  <i class="bi bi-clock-history text-warning me-2 fs-6"></i>
                  <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.88rem;">Staff Activity Log</h6>
                </div>
                <div class="d-flex align-items-center gap-1">
                  <span id="modal-activity-count" class="badge bg-secondary-subtle text-dark" style="font-size: 0.72rem;">0 logs</span>
                  <button type="button" id="modal-refresh-logs-btn" class="btn btn-xs btn-outline-secondary p-1 py-0" title="Refresh Activity Logs" style="font-size: 0.75rem;">
                    <i class="bi bi-arrow-clockwise"></i>
                  </button>
                </div>
              </div>
              
              <div class="card-body p-3 flex-grow-1" style="max-height: 520px; overflow-y: auto;">
                <!-- Logs list container -->
                <div id="modal-activity-timeline" class="activity-timeline-container position-relative ps-3">
                  <!-- Dynamic activity logs rendered here -->
                </div>

                <!-- Empty logs state -->
                <div id="modal-activity-empty" class="text-center py-5 text-muted d-none">
                  <i class="bi bi-clipboard2-check fs-2 d-block mb-2 text-secondary opacity-50"></i>
                  <div class="fw-medium">No activity records found</div>
                  <div class="small text-muted">Staff actions on this booking will appear here automatically.</div>
                </div>

                <!-- Loading spinner for logs -->
                <div id="modal-activity-loading" class="text-center py-5">
                  <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                  <div class="small text-muted mt-2">Loading staff activity history...</div>
                </div>
              </div>

              <!-- Quick Staff Note / Followup Reminder in footer of right card -->
              <div class="card-footer bg-light border-top p-2 text-muted small" style="font-size: 0.75rem;">
                <i class="bi bi-shield-check text-success me-1"></i> Logs track actions by <strong>Dispatchers</strong>, <strong>Admins</strong>, and <strong>Super Admin</strong>.
              </div>
            </div>
          </div>

        </div>

      </div>

      <!-- Modal Footer -->
      <div class="modal-footer bg-white border-top py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <a id="modal-btn-edit" href="#" class="btn btn-sm btn-warning text-dark fw-bold px-3">
            <i class="bi bi-pencil-square me-1"></i> Edit Booking
          </a>
          <a id="modal-btn-receipt" href="#" target="_blank" class="btn btn-sm btn-outline-success">
            <i class="bi bi-receipt me-1"></i> Receipt
          </a>
          <button type="button" id="modal-btn-sms" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-chat-dots me-1"></i> SMS
          </button>
          <button type="button" id="modal-btn-email" class="btn btn-sm btn-outline-info">
            <i class="bi bi-envelope me-1"></i> Email
          </button>
        </div>
        <button type="button" class="btn btn-sm btn-secondary px-3" data-bs-dismiss="modal">Close</button>
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

<!-- 🚗 Quick Booking Creation Modal -->
<div class="modal fade" id="quickBookingModal" tabindex="-1" aria-labelledby="quickBookingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
      <div class="modal-header py-3 px-4" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: #fff;">
        <div class="d-flex align-items-center gap-2">
          <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(230, 176, 74, 0.2); color: #E6B04A;">
            <i class="bi bi-calendar2-check-fill fs-6"></i>
          </div>
          <div>
            <h6 class="modal-title fw-bold mb-0 text-white" id="quickBookingModalLabel">Create Quick Booking</h6>
            <small class="text-white-50" style="font-size: 11px;">Complete passenger details to confirm this booking</small>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="quickBookingForm" method="POST" action="{{ route('booking.store') }}">
        @csrf
        <!-- Hidden Booking Parameters from Instant Price Calculator -->
        <input type="hidden" name="pickup_address" id="qb_pickup_address">
        <input type="hidden" name="dropoff_address" id="qb_dropoff_address">
        <div id="qb_via_container"></div>
        <input type="hidden" name="vehicle_make" id="qb_vehicle_make">
        <input type="hidden" name="pickup_date" id="qb_pickup_date">
        <input type="hidden" name="pickup_time" id="qb_pickup_time">
        <input type="hidden" name="price" id="qb_price">
        <input type="hidden" name="fare" id="qb_fare">
        <input type="hidden" name="parking" id="qb_parking">

        <div class="modal-body p-4" style="background: #f8fafc;">
          <!-- 🛣️ Journey Summary Card -->
          <div class="card border-0 mb-3 shadow-sm" style="border-radius: 12px; background: #ffffff; border: 1px solid #e2e8f0;">
            <div class="card-body p-3">
              <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2 pb-2 border-bottom">
                <span class="badge px-2.5 py-1.5 fw-bold" style="background: #e0f2fe; color: #0369a1; font-size: 11.5px; border-radius: 8px;">
                  <i class="bi bi-car-front-fill me-1"></i> <span id="qbSummaryVehicle">Saloon</span>
                </span>
                <span class="badge px-2.5 py-1.5 fw-bold" style="background: #fef3c7; color: #92400e; font-size: 11.5px; border-radius: 8px;">
                  <i class="bi bi-calendar3 me-1"></i> <span id="qbSummaryDateTime">--/--/---- --:--</span>
                </span>
                <span class="badge px-2.5 py-1.5 fw-bold" style="background: #ecfdf5; color: #065f46; font-size: 11.5px; border-radius: 8px;">
                  <i class="bi bi-speedometer2 me-1"></i> <span id="qbSummaryDistance">0.00 Mi</span>
                </span>
                <span class="badge px-3 py-1.5 fw-bold ms-auto" style="background: #111827; color: #E6B04A; font-size: 13.5px; border-radius: 8px; border: 1px solid rgba(230, 176, 74, 0.4);">
                  Total: <span id="qbSummaryFare">£0.00</span>
                </span>
              </div>

              <div class="row g-2 text-dark small" style="font-size: 12px;">
                <div class="col-12 col-md-6">
                  <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-geo-alt-fill text-success mt-0.5"></i>
                    <div>
                      <span class="text-muted fw-semibold d-block" style="font-size: 10.5px;">PICKUP</span>
                      <span id="qbSummaryPickup" class="fw-bold text-dark">-</span>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-pin-map-fill text-danger mt-0.5"></i>
                    <div>
                      <span class="text-muted fw-semibold d-block" style="font-size: 10.5px;">DROPOFF</span>
                      <span id="qbSummaryDropoff" class="fw-bold text-dark">-</span>
                    </div>
                  </div>
                </div>
                <div class="col-12" id="qbSummaryViaRow" style="display: none;">
                  <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-signpost-split-fill text-warning mt-0.5"></i>
                    <div>
                      <span class="text-muted fw-semibold d-block" style="font-size: 10.5px;">VIA STOP</span>
                      <span id="qbSummaryVia" class="fw-bold text-dark">-</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 👤 Passenger & Booking Inputs -->
          <div class="row g-3">
            <!-- Passenger Name (Required) -->
            <div class="col-12 col-md-6">
              <label class="form-label fw-bold small text-dark mb-1">
                Passenger Name <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-person-fill"></i></span>
                <input type="text" name="passenger_name" id="qb_passenger_name" class="form-control border-start-0" placeholder="e.g. John Smith" required style="border-radius: 0 8px 8px 0; font-size: 12.5px;">
              </div>
            </div>

            <!-- Phone Number (Required) -->
            <div class="col-12 col-md-6">
              <label class="form-label fw-bold small text-dark mb-1">
                Phone Number <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-telephone-fill"></i></span>
                <input type="tel" name="phone_no" id="qb_phone_no" class="form-control border-start-0" placeholder="e.g. 07123456789" required style="border-radius: 0 8px 8px 0; font-size: 12.5px;">
              </div>
            </div>

            <!-- Payment Type -->
            <div class="col-12 col-md-6">
              <label class="form-label fw-bold small text-dark mb-1">Payment Type</label>
              <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-credit-card-2-front-fill"></i></span>
                <select name="payment_type" id="qb_payment_type" class="form-select border-start-0" style="border-radius: 0 8px 8px 0; font-size: 12.5px;">
                  <option value="cash" selected>Cash (Pay in car)</option>
                  <option value="card">Card (Stripe payment link)</option>
                  <option value="account">Account</option>
                </select>
              </div>
            </div>

            <!-- Assign Driver (Optional) -->
            <div class="col-12 col-md-6">
              <label class="form-label fw-bold small text-dark mb-1">
                Assign Driver <span class="text-muted fw-normal" style="font-size: 11px;">(Optional)</span>
              </label>
              <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-person-badge"></i></span>
                <select name="driver_id" id="qb_driver_id" class="form-select border-start-0" style="border-radius: 0 8px 8px 0; font-size: 12.5px;">
                  <option value="">-- Unallocated (Assign Later) --</option>
                  @if(isset($drivers) && count($drivers) > 0)
                    @foreach($drivers as $driver)
                      @php
                        $callSign = $driver['call_sign'] ?? 'D';
                        $dName = $driver['name'] ?? 'Driver';
                      @endphp
                      <option value="{{ $driver['id'] }}">{{ $callSign }} • {{ $dName }}</option>
                    @endforeach
                  @endif
                </select>
              </div>
            </div>

            <!-- Account Selection (Hidden unless payment_type == 'account') -->
            <div class="col-12" id="qb_account_wrapper" style="display: none;">
              <label class="form-label fw-bold small text-dark mb-1">Select Account</label>
              <select name="account_id" id="qb_account_id" class="form-select" style="border-radius: 8px; font-size: 12.5px;">
                <option value="">-- Select Account --</option>
                @if(isset($accounts) && count($accounts) > 0)
                  @foreach($accounts as $acc)
                    <option value="{{ $acc['id'] ?? '' }}" data-name="{{ $acc['business_name'] ?? ($acc['name'] ?? '') }}">
                      {{ $acc['business_name'] ?? ($acc['name'] ?? 'Account') }} {{ !empty($acc['phone']) ? ' - ' . $acc['phone'] : '' }}
                    </option>
                  @endforeach
                @endif
              </select>
              <input type="hidden" name="account_name" id="qb_account_name">
            </div>

            <!-- Email Address (Required) -->
            <div class="col-12 col-md-6">
              <label class="form-label fw-bold small text-dark mb-1">
                Email Address <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-envelope-fill"></i></span>
                <input type="email" name="email" id="qb_email" class="form-control border-start-0" placeholder="passenger@example.com" required style="border-radius: 0 8px 8px 0; font-size: 12.5px;">
              </div>
            </div>

            <!-- Flight No (Optional) -->
            <div class="col-12 col-md-6">
              <label class="form-label fw-bold small text-dark mb-1">
                Flight Number <span class="text-muted fw-normal" style="font-size: 11px;">(Optional)</span>
              </label>
              <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-airplane-fill"></i></span>
                <input type="text" name="flight_no" id="qb_flight_no" class="form-control border-start-0" placeholder="e.g. BA123" style="border-radius: 0 8px 8px 0; font-size: 12.5px;">
              </div>
            </div>

            <!-- Job Comments / Notes (Optional) -->
            <div class="col-12">
              <label class="form-label fw-bold small text-dark mb-1">
                Comments / Special Instructions <span class="text-muted fw-normal" style="font-size: 11px;">(Optional)</span>
              </label>
              <textarea name="job_comment" id="qb_job_comment" rows="2" class="form-control" placeholder="Notes for driver, luggage details, child seats, etc." style="border-radius: 8px; font-size: 12.5px;"></textarea>
            </div>
          </div>

          <!-- Alert Container for submission errors -->
          <div id="qbAlertError" class="alert alert-danger mt-3 py-2 px-3 small" style="display: none; border-radius: 8px;"></div>
        </div>

        <div class="modal-footer py-2.5 px-4 bg-white border-top d-flex justify-content-between align-items-center">
          <button type="button" class="btn btn-sm btn-light border px-3 fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px;">
            Cancel
          </button>
          <button type="submit" id="qbSubmitBtn" class="btn btn-sm text-white fw-bold px-4 py-2 d-flex align-items-center gap-2 shadow-sm" style="background: linear-gradient(135deg, #B87333, #d48b48); border-radius: 8px; border: none;">
            <span id="qbSubmitSpinner" class="spinner-border spinner-border-sm" style="display: none;" role="status"></span>
            <i class="bi bi-check2-circle fs-6" id="qbSubmitIcon"></i>
            <span id="qbSubmitText">Confirm & Create Booking</span>
          </button>
        </div>
      </form>
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

/* 🌟 View Booking & Staff Activity Timeline Modal Styling */
#viewBookingModal {
  z-index: 1095 !important;
}
#viewBookingModal .modal-dialog {
  margin-top: 30px !important;
  margin-bottom: 30px !important;
}
#viewBookingModal .modal-content {
  border-radius: 16px !important;
  overflow: hidden;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45) !important;
}
#viewBookingModal .modal-header {
  background: linear-gradient(135deg, #111827 0%, #1e293b 100%) !important;
  border-bottom: 2px solid #E6B04A !important;
}

/* 🌟 Layering for Stacked Secondary Modals (SMS, Email, Dispatch, Status Confirm) */
#sendSmsModal,
#sendEmailModal,
#dispatchDriverModal,
#statusConfirmModal {
  z-index: 1120 !important;
}
#sendSmsModal .modal-dialog,
#sendEmailModal .modal-dialog,
#dispatchDriverModal .modal-dialog,
#statusConfirmModal .modal-dialog {
  z-index: 1121 !important;
}
.booking-route-timeline {
  border-left: 2px dashed #cbd5e1;
  margin-left: 14px;
}
.route-node {
  padding-left: 12px;
}
.route-dot {
  box-shadow: 0 0 0 2px #ffffff;
}
.activity-timeline-container {
  border-left: 2px solid #e2e8f0;
  margin-left: 10px;
}
.activity-timeline-item {
  position: relative;
  padding-left: 18px;
  padding-bottom: 16px;
}
.activity-timeline-item:last-child {
  padding-bottom: 4px;
}
.activity-timeline-item::before {
  content: '';
  position: absolute;
  left: -5px;
  top: 5px;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background-color: #E6B04A;
  border: 2px solid #ffffff;
  box-shadow: 0 0 0 2px rgba(230, 176, 74, 0.35);
}
.activity-timeline-item.action-status::before {
  background-color: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.35);
}
.activity-timeline-item.action-dispatch::before {
  background-color: #10b981;
  box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.35);
}
.activity-timeline-item.action-recall::before {
  background-color: #ef4444;
  box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.35);
}
.activity-timeline-item.action-sms::before {
  background-color: #8b5cf6;
  box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.35);
}
.activity-timeline-item.action-email::before {
  background-color: #06b6d4;
  box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.35);
}
.activity-timeline-item.action-edit::before {
  background-color: #f59e0b;
  box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.35);
}
.activity-timeline-item.action-create::before {
  background-color: #64748b;
  box-shadow: 0 0 0 2px rgba(100, 116, 139, 0.35);
}
</style>


@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>






<script>
// Global state & configurations
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}';
const DETAILS_JSON_BASE_URL = "{{ url('bookings/details-json') }}";
let DRIVERS_MAP = (function() {
    const map = {};
    const rawDrivers = @json($drivers);
    const addDriver = (d) => {
        if (!d || typeof d !== 'object') return;
        const k = d.id || d.key || d.firebase_key || d.raw_id;
        if (k) map[String(k)] = d;
        if (d.id) map[String(d.id)] = d;
        if (d.key) map[String(d.key)] = d;
        if (d.firebase_key) map[String(d.firebase_key)] = d;
        if (d.raw_id) map[String(d.raw_id)] = d;
        if (d.driver_id) map[String(d.driver_id)] = d;
    };
    if (Array.isArray(rawDrivers)) {
        rawDrivers.forEach(addDriver);
    } else if (rawDrivers && typeof rawDrivers === 'object') {
        Object.entries(rawDrivers).forEach(([k, d]) => {
            if (d && typeof d === 'object') {
                d.id = d.id || k;
                addDriver(d);
                map[String(k)] = d;
            }
        });
    }
    return map;
})();
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

// 🎨 Helper to format driver badge
function getDriverBadgeHtml(booking) {
    if (!booking) return `<span class="badge bg-light text-muted border px-2 py-1 unassigned-driver" style="font-size: 11px; font-weight: 500;">Not Assigned</span>`;

    const dId = String(booking.driver_id || booking.driverId || booking.driver || '').trim();
    const dName = String(booking.driver_name || '').trim();
    const dCall = String(booking.driver_call_sign || booking.call_sign || '').trim();

    let driver = null;
    if (dId && DRIVERS_MAP[dId]) {
        driver = DRIVERS_MAP[dId];
    } else if (dId) {
        driver = Object.values(DRIVERS_MAP).find(d => 
            String(d.id || '') === dId || 
            String(d.key || '') === dId || 
            String(d.firebase_key || '') === dId || 
            String(d.raw_id || '') === dId || 
            String(d.driver_id || '') === dId ||
            String(d.name || '').toLowerCase() === dId.toLowerCase() ||
            String(d.call_sign || '').toLowerCase() === dId.toLowerCase()
        );
    }
    if (!driver && dName) {
        driver = Object.values(DRIVERS_MAP).find(d => 
            String(d.name || '').toLowerCase() === dName.toLowerCase() || 
            String(d.call_sign || '').toLowerCase() === dName.toLowerCase()
        );
    }
    if (!driver && dCall) {
        driver = Object.values(DRIVERS_MAP).find(d => 
            String(d.call_sign || '').toLowerCase() === dCall.toLowerCase()
        );
    }

    if (driver) {
        const callSign = driver.call_sign ? `${driver.call_sign}/` : '';
        return `<span class="badge rounded-pill bg-danger bg-opacity-90 px-2.5 py-1.5 me-1 driver-badge" style="font-size: 11.5px;">${callSign}${driver.name || 'Driver'}</span>`;
    } else if (dName || dCall || (booking.driver && typeof booking.driver === 'string' && booking.driver.trim() !== '')) {
        const fallbackCall = dCall ? `${dCall}/` : '';
        const fallbackName = dName || booking.driver;
        return `<span class="badge rounded-pill bg-danger bg-opacity-90 px-2.5 py-1.5 me-1 driver-badge" style="font-size: 11.5px;">${fallbackCall}${fallbackName}</span>`;
    }

    return `<span class="badge bg-light text-muted border px-2 py-1 unassigned-driver" style="font-size: 11px; font-weight: 500;">Not Assigned</span>`;
}

// 🎨 Helper to format vias badge
function getViasBadgeHtml(booking) {
    let viasArr = [];
    if (Array.isArray(booking.vias) && booking.vias.length > 0) {
        viasArr = booking.vias;
    } else if (Array.isArray(booking.via_addresses) && booking.via_addresses.length > 0) {
        viasArr = booking.via_addresses;
    } else if (typeof booking.vias === 'string' && booking.vias.trim()) {
        viasArr = [booking.vias.trim()];
    }

    viasArr = viasArr.filter(v => v && String(v).trim() !== '');
    if (viasArr.length === 0) {
        return '<span class="text-muted">-</span>';
    }

    const viasFull = viasArr.join(' → ').replace(/"/g, '&quot;');
    const truncated = viasFull.length > 16 ? viasFull.substring(0, 16) + '...' : viasFull;
    return `<span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11px; font-weight: 500; max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block; vertical-align: middle;" title="${viasFull}"><i class="bi bi-signpost-split text-warning me-1"></i>${truncated}</span>`;
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

    const driverHtml = getDriverBadgeHtml(booking);
    const viasText = getViasBadgeHtml(booking);

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

    const hasDriver = !!(booking.driver_id || booking.driverId || booking.driver_name || booking.driver || booking.driver_call_sign || booking.call_sign);
    const bookingJson = JSON.stringify(booking).replace(/'/g, "&apos;");
    const pickupAddress = booking.pickup_address || '-';
    const dropoffAddress = booking.dropoff_address || '-';
    const passengerName = booking.passenger_name || 'N/A';
    const flightNo = (booking.flight_no && booking.flight_no !== '-' && booking.flight_no !== 'undefined') ? booking.flight_no : '-';
    const formattedPrice = (booking.price && !isNaN(Number(booking.price))) ? `£${Number(booking.price).toFixed(2)}` : (booking.price || '-');

    const highlightClass = isNew ? 'new-booking-highlight' : '';

    return `
    <tr id="booking-row-${id}" data-booking-id="${id}" class="booking-table-row align-middle ${highlightClass}" style="${rowStyle}">
        <td class="col-ref fw-bold" style="${rowStyle}">
            <span class="font-monospace text-dark" style="font-size: 11.5px; letter-spacing: 0.3px;">${booking.ref_no || 'N/A'}</span>
        </td>
        <td class="col-payment" style="${rowStyle}">${getPaymentBadgeHtml(booking.payment_type)}</td>
        <td class="col-passenger fw-semibold" style="${rowStyle}" title="${passengerName}">
            <span class="truncate-cell" style="max-width: 125px;">${passengerName}</span>
        </td>
        <td class="col-driver driver-cell" style="${rowStyle}">${driverHtml}</td>
        <td class="col-phone font-monospace" style="${rowStyle}">
            <a href="tel:${booking.phone_no || ''}" class="text-decoration-none text-dark" style="font-size: 11.5px;" title="${booking.phone_no || 'N/A'}">
                ${booking.phone_no || 'N/A'}
            </a>
        </td>
        <td class="col-pickup" style="${rowStyle}" title="${pickupAddress}">
            <span class="truncate-cell text-dark" style="max-width: 140px;">${pickupAddress}</span>
        </td>
        <td class="col-dropoff" style="${rowStyle}" title="${dropoffAddress}">
            <span class="truncate-cell text-dark" style="max-width: 140px;">${dropoffAddress}</span>
        </td>
        <td class="col-vias text-center" style="${rowStyle}">${viasText}</td>
        <td class="col-date fw-semibold text-nowrap" style="${rowStyle}">${pickupDate}</td>
        <td class="col-time font-monospace text-nowrap fw-bold" style="${rowStyle}">${pickupTime}</td>
        <td class="col-vehicle text-center" style="${rowStyle}">${getVehicleBadgeHtml(booking.vehicle_make)}</td>
        <td class="col-flight text-nowrap" style="${rowStyle}" title="${flightNo}">
            ${flightNo !== '-' ? `<span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11px;">${flightNo}</span>` : '<span class="text-muted small">-</span>'}
        </td>
        <td class="col-price fw-bold text-dark text-nowrap" style="${rowStyle}">${formattedPrice}</td>
        <td class="col-comment" style="${rowStyle}" title="${commentText}">
            <span class="truncate-cell text-muted" style="max-width: 120px; font-size: 11px;">${commentText}</span>
        </td>
        <td class="col-status" style="${rowStyle}">
            <form method="POST" action="{{ url('bookings') }}/${encodeURIComponent(id)}/update-status-manual" class="statusForm">
                <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                <select class="form-select form-select-sm text-black statusSelect fw-semibold"
                        name="status"
                        style="${statusSelectStyle}; min-width: 106px; font-size: 11px; border-radius: 6px; padding: 2px 22px 2px 8px;"
                        data-booking-id="${id}">
                    ${statusOptionsHtml}
                </select>
            </form>
        </td>
        <td class="col-platform text-center" style="${rowStyle}">${getPlatformBadgeHtml(booking.platform, booking.partner)}</td>
        <td class="col-actions" style="${rowStyle}">
            <div class="dropdown actions-dropdown">
                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' data-bs-boundary="viewport" aria-expanded="false" style="width: 28px; height: 28px; border-radius: 6px;">
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
                    <li class="action-recall-item" style="${hasDriver ? '' : 'display:none;'}">
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
                        <a class="dropdown-item d-flex align-items-center text-primary" href="{{ url('/bookings') }}/${encodeURIComponent(id)}/edit">
                            <i class="bi bi-pencil-square me-2"></i> Edit Booking
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-info view-booking-btn"
                           href="#"
                           data-bs-toggle="modal"
                           data-bs-target="#viewBookingModal"
                           data-booking-id="${id}"
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
    const recallItem = row.querySelector('.action-recall-item');

    if (driverCell) {
        driverCell.innerHTML = getDriverBadgeHtml(booking);
    }

    const hasDriver = !!(booking.driver_id || booking.driverId || booking.driver_name || booking.driver || booking.driver_call_sign || booking.call_sign);
    if (dispatchItem) dispatchItem.style.display = hasDriver ? 'none' : '';
    if (trackItem) trackItem.style.display = hasDriver ? '' : 'none';
    if (recallItem) recallItem.style.display = hasDriver ? '' : 'none';

    // Update view-booking-btn data-booking & data-booking-id
    const viewBtn = row.querySelector('.view-booking-btn');
    if (viewBtn) {
        viewBtn.setAttribute('data-booking-id', booking.id);
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
        }
    }, 300);
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
                formData.set('booking_id', bookingId);
                formData.set('_token', CSRF_TOKEN);
                select.disabled = true;
                try {
                    let actionUrl = form ? form.action : '';
                    if (!actionUrl || actionUrl === window.location.href) {
                        actionUrl = `{{ url('bookings') }}/${encodeURIComponent(bookingId)}/update-status-manual`;
                    }
                    const response = await fetch(actionUrl, {
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
                const handleConfirm = () => {
                    executeStatusUpdate();
                    modal.hide();
                    cleanup();
                };

                const handleCancel = () => {
                    select.value = previousStatus;
                    modal.hide();
                    cleanup();
                };

                const cleanup = () => {
                    confirmBtn.removeEventListener('click', handleConfirm);
                    cancelBtn.removeEventListener('click', handleCancel);
                    modalEl.removeEventListener('hidden.bs.modal', handleCancel);
                };

                confirmBtn.addEventListener('click', handleConfirm);
                cancelBtn.addEventListener('click', handleCancel);
                modalEl.addEventListener('hidden.bs.modal', handleCancel, { once: true });

                modal.show();
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
                const recallUrl = `{{ url('bookings') }}/${encodeURIComponent(bookingId)}/recall`;
                const response = await fetch(recallUrl, {
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
                        if (driverCell) driverCell.innerHTML = `<span class="badge bg-light text-muted border px-2 py-1 unassigned-driver" style="font-size: 11px; font-weight: 500;">Not Assigned</span>`;
                        const dispatchItem = row.querySelector('.action-dispatch-item');
                        const trackItem = row.querySelector('.action-track-item');
                        const recallItem = row.querySelector('.action-recall-item');
                        if (dispatchItem) dispatchItem.style.display = '';
                        if (trackItem) trackItem.style.display = 'none';
                        if (recallItem) recallItem.style.display = 'none';
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
                const hideUrl = `{{ url('bookings') }}/${encodeURIComponent(bookingId)}/hide`;
                const response = await fetch(hideUrl, {
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

    // View details modal & Staff Activity Log Handler
    let activeViewBookingId = null;
    let activeViewBookingData = null;

    function formatBookingDateTime(dtStr) {
        if (!dtStr) return '-';
        try {
            const dt = new Date(dtStr);
            if (isNaN(dt.getTime())) return dtStr;
            return dt.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) + ' ' + dt.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
        } catch(e) {
            return dtStr;
        }
    }

    function formatActivityDateTime(dtStr) {
        if (!dtStr) return '-';
        try {
            const dt = new Date(dtStr);
            if (isNaN(dt.getTime())) return dtStr;
            return dt.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) + ' ' + dt.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        } catch(e) {
            return dtStr;
        }
    }

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getActivityActionBadge(action) {
        action = action || 'Activity';
        const lower = action.toLowerCase();
        let bgClass = 'bg-primary-subtle text-primary border border-primary-subtle';
        let icon = 'bi-activity';
        let typeClass = 'action-default';

        if (lower.includes('status')) {
            bgClass = 'bg-info-subtle text-info-emphasis border border-info-subtle';
            icon = 'bi-arrow-repeat';
            typeClass = 'action-status';
        } else if (lower.includes('dispatch') || lower.includes('assign')) {
            bgClass = 'bg-success-subtle text-success-emphasis border border-success-subtle';
            icon = 'bi-person-check';
            typeClass = 'action-dispatch';
        } else if (lower.includes('recall') || lower.includes('cancel')) {
            bgClass = 'bg-danger-subtle text-danger-emphasis border border-danger-subtle';
            icon = 'bi-person-x';
            typeClass = 'action-recall';
        } else if (lower.includes('sms')) {
            bgClass = 'bg-purple-subtle text-purple-emphasis border border-purple-subtle';
            icon = 'bi-chat-dots';
            typeClass = 'action-sms';
        } else if (lower.includes('email')) {
            bgClass = 'bg-cyan-subtle text-cyan-emphasis border border-cyan-subtle';
            icon = 'bi-envelope';
            typeClass = 'action-email';
        } else if (lower.includes('edit') || lower.includes('update')) {
            bgClass = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
            icon = 'bi-pencil-square';
            typeClass = 'action-edit';
        } else if (lower.includes('create') || lower.includes('initial')) {
            bgClass = 'bg-secondary-subtle text-dark border border-secondary-subtle';
            icon = 'bi-plus-circle';
            typeClass = 'action-create';
        }

        return {
            badgeHtml: `<span class="badge ${bgClass} fw-semibold" style="font-size: 0.75rem;"><i class="bi ${icon} me-1"></i>${escapeHtml(action)}</span>`,
            typeClass: typeClass
        };
    }

    function populateViewBookingModal(booking) {
        if (!booking) return;
        activeViewBookingData = booking;

        const bookingId = booking.id || booking.booking_id || '';
        const refNo = booking.ref_no || bookingId || '-';
        const status = (booking.status || 'pending').toLowerCase();

        // Header badges
        const refBadge = document.getElementById('modal-ref-badge');
        if (refBadge) refBadge.textContent = 'REF# ' + refNo;

        const statusBadge = document.getElementById('modal-status-badge');
        if (statusBadge) {
            statusBadge.textContent = status.toUpperCase();
            let statusBg = '#6c757d';
            if (status === 'completed') statusBg = '#198754';
            else if (status === 'dispatched' || status === 'accepted' || status === 'allocated') statusBg = '#0d6efd';
            else if (status === 'pickedup' || status === 'onway') statusBg = '#0dcaf0';
            else if (status === 'cancelled' || status === 'rejected') statusBg = '#dc3545';
            else if (status === 'pending') statusBg = '#ffc107';
            statusBadge.style.backgroundColor = statusBg;
            statusBadge.style.color = (status === 'pending') ? '#111827' : '#ffffff';
        }

        // Top Stats Banner
        const formattedPickupTime = formatBookingDateTime(booking.pickup_time);
        const topPickupTime = document.getElementById('modal-top-pickup-time');
        if (topPickupTime) topPickupTime.textContent = formattedPickupTime;

        // Driver details
        let driverName = booking.driver_name || booking.driver || '';
        let driverCallSign = booking.driver_call_sign || booking.call_sign || '';
        let driverText = 'Not Assigned';
        if (driverCallSign || driverName) {
            driverText = (driverCallSign ? `[${driverCallSign}] ` : '') + (driverName || 'Driver');
        } else if (booking.driver_id || booking.driverId) {
            driverText = 'Driver Assigned';
        }

        const topDriver = document.getElementById('modal-top-driver');
        if (topDriver) topDriver.textContent = driverText;

        const topPrice = document.getElementById('modal-top-price');
        if (topPrice) topPrice.textContent = '£' + (parseFloat(booking.price || 0).toFixed(2));

        const topPayment = document.getElementById('modal-top-payment');
        if (topPayment) topPayment.textContent = booking.payment_type || 'Cash';

        // Passenger Info
        const passName = document.getElementById('modal-passenger_name');
        if (passName) passName.textContent = booking.passenger_name || '-';

        const phoneNo = document.getElementById('modal-phone_no');
        if (phoneNo) phoneNo.textContent = booking.phone_no || '-';

        const phoneCall = document.getElementById('modal-phone-call');
        if (phoneCall) {
            if (booking.phone_no) {
                phoneCall.href = 'tel:' + booking.phone_no;
                phoneCall.style.display = '';
            } else {
                phoneCall.style.display = 'none';
            }
        }

        const emailEl = document.getElementById('modal-email');
        if (emailEl) emailEl.textContent = booking.email || '-';

        const flightEl = document.getElementById('modal-flight_no');
        if (flightEl) flightEl.textContent = booking.flight_no || '-';

        // Route & Stops
        const pickupAddr = document.getElementById('modal-pickup_address');
        if (pickupAddr) pickupAddr.textContent = booking.pickup_address || '-';

        const pickupTimeSub = document.getElementById('modal-pickup_time_sub');
        if (pickupTimeSub) pickupTimeSub.textContent = 'Pickup: ' + formattedPickupTime;

        const dropoffAddr = document.getElementById('modal-dropoff_address');
        if (dropoffAddr) dropoffAddr.textContent = booking.dropoff_address || '-';

        // Render Via Stops
        const viasContainer = document.getElementById('modal-vias-container');
        if (viasContainer) {
            viasContainer.innerHTML = '';
            let vias = [];
            if (Array.isArray(booking.vias)) vias = booking.vias;
            else if (Array.isArray(booking.via_addresses)) vias = booking.via_addresses;
            else if (typeof booking.vias === 'object' && booking.vias !== null) vias = Object.values(booking.vias);

            vias = vias.filter(v => v && String(v).trim().length > 0);

            if (vias.length > 0) {
                vias.forEach((via, idx) => {
                    const viaNode = document.createElement('div');
                    viaNode.className = 'route-node position-relative mb-3';
                    viaNode.innerHTML = `
                        <span class="route-dot bg-warning rounded-circle position-absolute" style="left: -28px; top: 3px; width: 14px; height: 14px; border: 3px solid #fff3cd;"></span>
                        <div class="small fw-bold text-warning-emphasis text-uppercase" style="font-size: 0.7rem;">Via Stop #${idx + 1}</div>
                        <div class="text-dark fw-medium" style="font-size: 0.88rem;">${escapeHtml(via)}</div>
                    `;
                    viasContainer.appendChild(viaNode);
                });
            }
        }

        // Google Maps Route Link
        const mapsLink = document.getElementById('modal-route-maps-link');
        if (mapsLink) {
            if (booking.pickup_address && booking.dropoff_address) {
                mapsLink.href = `https://www.google.com/maps/dir/?api=1&origin=${encodeURIComponent(booking.pickup_address)}&destination=${encodeURIComponent(booking.dropoff_address)}`;
                mapsLink.style.display = '';
            } else {
                mapsLink.style.display = 'none';
            }
        }

        // Vehicle, Driver & Pricing Breakdown
        const vehicleEl = document.getElementById('modal-vehicle_id');
        if (vehicleEl) vehicleEl.textContent = booking.vehicle_make || booking.vehicle_id || '-';

        const driverDetailsEl = document.getElementById('modal-driver-details');
        if (driverDetailsEl) {
            if (driverCallSign || driverName) {
                driverDetailsEl.innerHTML = `<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-2 py-1">${driverCallSign ? `<strong>[${escapeHtml(driverCallSign)}]</strong> ` : ''}${escapeHtml(driverName || 'Driver')}</span>`;
            } else {
                driverDetailsEl.innerHTML = `<span class="badge bg-light text-muted border px-2 py-1">Not Assigned</span>`;
            }
        }

        const fareEl = document.getElementById('modal-fare');
        if (fareEl) fareEl.textContent = '£' + (parseFloat(booking.fare || booking.price || 0).toFixed(2));

        const parkingEl = document.getElementById('modal-parking');
        if (parkingEl) parkingEl.textContent = '£' + (parseFloat(booking.parking || 0).toFixed(2));

        const waitingEl = document.getElementById('modal-waiting');
        if (waitingEl) waitingEl.textContent = '£' + (parseFloat(booking.waiting_fee || 0).toFixed(2));

        const extraEl = document.getElementById('modal-extra');
        if (extraEl) extraEl.textContent = '£' + (parseFloat(booking.extra || 0).toFixed(2));

        const priceEl = document.getElementById('modal-price');
        if (priceEl) priceEl.textContent = '£' + (parseFloat(booking.price || 0).toFixed(2));

        const childSeatEl = document.getElementById('modal-child_seat');
        if (childSeatEl) {
            childSeatEl.innerHTML = (booking.child_seat == 1 || booking.child_seat === true || booking.child_seat === '1')
                ? '<span class="badge bg-success px-2 py-1">Yes (Requested)</span>'
                : '<span class="badge bg-light text-muted border px-2 py-1">No</span>';
        }

        const payTypeEl = document.getElementById('modal-payment_type');
        if (payTypeEl) payTypeEl.textContent = booking.payment_type || 'Cash';

        const commentEl = document.getElementById('modal-comment');
        if (commentEl) commentEl.textContent = booking.job_comment || 'No special comments or instructions provided.';

        // Modal Action buttons
        const editBtn = document.getElementById('modal-btn-edit');
        if (editBtn) editBtn.href = `{{ url('/bookings') }}/${encodeURIComponent(bookingId)}/edit`;

        const receiptBtn = document.getElementById('modal-btn-receipt');
        if (receiptBtn) receiptBtn.href = `{{ url('/receipt') }}/${encodeURIComponent(bookingId)}`;
    }

    async function loadBookingDetailsAndLogs(bookingId) {
        if (!bookingId) return;
        activeViewBookingId = bookingId;

        const timelineContainer = document.getElementById('modal-activity-timeline');
        const emptyContainer = document.getElementById('modal-activity-empty');
        const loadingContainer = document.getElementById('modal-activity-loading');
        const countBadge = document.getElementById('modal-activity-count');

        if (loadingContainer) loadingContainer.classList.remove('d-none');
        if (emptyContainer) emptyContainer.classList.add('d-none');
        if (timelineContainer) timelineContainer.innerHTML = '';

        try {
            const detailsUrl = `${DETAILS_JSON_BASE_URL}?id=${encodeURIComponent(bookingId)}&booking_id=${encodeURIComponent(bookingId)}`;
            const res = await fetch(detailsUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!res.ok) throw new Error('Failed to load booking details');

            const data = await res.json();
            if (data?.booking) {
                populateViewBookingModal(data.booking);
            }

            const logs = data?.activity_logs || [];
            if (countBadge) countBadge.textContent = `${logs.length} record${logs.length === 1 ? '' : 's'}`;

            if (loadingContainer) loadingContainer.classList.add('d-none');

            if (logs.length === 0) {
                if (emptyContainer) emptyContainer.classList.remove('d-none');
            } else {
                if (emptyContainer) emptyContainer.classList.add('d-none');
                
                let timelineHtml = '';
                logs.forEach(log => {
                    const actionInfo = getActivityActionBadge(log.action);
                    const formattedTime = formatActivityDateTime(log.created_at || log.timestamp);
                    const staffName = log.staff_name || 'Staff Member';
                    const staffRole = log.staff_role ? `<span class="badge bg-secondary-subtle text-secondary py-0 px-1 ms-1" style="font-size: 0.68rem;">${escapeHtml(log.staff_role)}</span>` : '';
                    const desc = log.description || '';

                    timelineHtml += `
                        <div class="activity-timeline-item ${actionInfo.typeClass}">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-dark text-white fw-medium py-1 px-2" style="font-size: 0.75rem;">
                                        <i class="bi bi-person-fill text-warning me-1"></i>${escapeHtml(staffName)}
                                    </span>
                                    ${staffRole}
                                </div>
                                <span class="text-muted small" style="font-size: 0.72rem;">
                                    <i class="bi bi-clock me-1"></i>${escapeHtml(formattedTime)}
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-1 mb-1">
                                ${actionInfo.badgeHtml}
                            </div>
                            <div class="text-secondary small" style="font-size: 0.82rem; line-height: 1.4;">
                                ${escapeHtml(desc)}
                            </div>
                        </div>
                    `;
                });

                if (timelineContainer) timelineContainer.innerHTML = timelineHtml;
            }
        } catch (err) {
            console.error('Error fetching booking details/logs:', err);
            if (loadingContainer) loadingContainer.classList.add('d-none');
            if (emptyContainer) {
                emptyContainer.classList.remove('d-none');
                emptyContainer.innerHTML = `
                    <i class="bi bi-exclamation-triangle text-danger fs-3 d-block mb-2"></i>
                    <div class="text-danger fw-medium">Failed to load activity logs</div>
                    <div class="small text-muted">Please click refresh to retry.</div>
                `;
            }
        }
    }

    // Refresh logs button inside modal
    const refreshLogsBtn = document.getElementById('modal-refresh-logs-btn');
    if (refreshLogsBtn) {
        refreshLogsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (activeViewBookingId) {
                loadBookingDetailsAndLogs(activeViewBookingId);
            }
        });
    }

    // Quick SMS trigger from inside View Booking modal
    const modalQuickSmsBtn = document.getElementById('modal-btn-sms');
    if (modalQuickSmsBtn) {
        modalQuickSmsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (!activeViewBookingData) return;
            const b = activeViewBookingData;
            const bookingId = b.id || b.booking_id || '';
            const phone = b.phone_no || '';
            const passengerName = b.passenger_name || '';
            const pickupDate = b.pickup_date || '';
            const pickupTime = b.pickup_time || '';
            const vehicleType = b.vehicle_make || b.vehicle_id || '';
            const price = b.price || '';
            const payment = b.payment_type || '';
            const pickup = b.pickup_address || '';
            const dropoff = b.dropoff_address || '';
            const via = Array.isArray(b.vias) ? b.vias.join(', ') : (b.vias || '-');
            const flight_no = b.flight_no || '';

            const smsModalEl = document.getElementById('sendSmsModal');
            if (!smsModalEl) return;

            document.getElementById('smsBookingId').value = bookingId;
            document.getElementById('smsPhone').value = phone;

            const messageTemplate = `Dear ${passengerName},

Please find booking confirmation for job reference: ${b.ref_no || bookingId}
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
            const smsModal = bootstrap.Modal.getOrCreateInstance(smsModalEl);
            smsModal.show();
        });
    }

    // Quick Email trigger from inside View Booking modal
    const modalQuickEmailBtn = document.getElementById('modal-btn-email');
    if (modalQuickEmailBtn) {
        modalQuickEmailBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (!activeViewBookingData) return;
            const b = activeViewBookingData;
            const emailModalEl = document.getElementById('sendEmailModal');
            if (!emailModalEl) return;

            document.getElementById('emailBookingId').value = b.id || b.booking_id || '';
            document.getElementById('emailBookingRef').value = b.ref_no || b.id || '';
            document.getElementById('emailAddress').value = b.email || '';
            document.getElementById('emailMessage').value = `Hello ${b.passenger_name || 'Customer'}, your booking (Ref# ${b.ref_no || b.id}) has been confirmed.`;

            const emailModal = bootstrap.Modal.getOrCreateInstance(emailModalEl);
            emailModal.show();
        });
    }

    // View details modal trigger binding
    context.querySelectorAll('.view-booking-btn:not([data-bound="true"])').forEach(btn => {
        btn.setAttribute('data-bound', 'true');
        btn.addEventListener('click', function () {
            try {
                const bookingId = this.getAttribute('data-booking-id') || '';
                const raw = this.getAttribute('data-booking');
                let booking = null;
                if (raw) {
                    booking = JSON.parse(raw);
                }

                if (booking) {
                    populateViewBookingModal(booking);
                }

                const actualId = bookingId || (booking ? (booking.id || booking.booking_id) : '');
                if (actualId) {
                    loadBookingDetailsAndLogs(actualId);
                }
            } catch (err) {
                console.error('Error opening view booking modal:', err);
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
        if (drivers && typeof drivers === 'object') {
            for (const [id, d] of Object.entries(drivers)) {
                if (d && typeof d === 'object') {
                    const dObj = { id, key: id, firebase_key: id, ...d };
                    DRIVERS_MAP[id] = dObj;
                    if (d.id) DRIVERS_MAP[String(d.id)] = dObj;
                    if (d.key) DRIVERS_MAP[String(d.key)] = dObj;
                    if (d.firebase_key) DRIVERS_MAP[String(d.firebase_key)] = dObj;
                    if (d.raw_id) DRIVERS_MAP[String(d.raw_id)] = dObj;
                    if (d.driver_id) DRIVERS_MAP[String(d.driver_id)] = dObj;
                }
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

    // 🌟 Automatic Modal Stacking & Z-Index Layering
    document.addEventListener('show.bs.modal', function (event) {
        const modal = event.target;
        if (!modal || !modal.classList || !modal.classList.contains('modal')) return;
        const openModals = Array.from(document.querySelectorAll('.modal.show')).filter(m => m !== modal);
        if (openModals.length > 0) {
            const topZ = 1120 + (openModals.length * 10);
            modal.style.zIndex = topZ;
            setTimeout(() => {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                if (backdrops.length > 0) {
                    backdrops[backdrops.length - 1].style.zIndex = topZ - 5;
                }
            }, 10);
        }
    });

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
                        const driver = DRIVERS_MAP[selectedDriverId] || Object.values(DRIVERS_MAP).find(d => 
                            String(d.id || '') === selectedDriverId || 
                            String(d.key || '') === selectedDriverId || 
                            String(d.firebase_key || '') === selectedDriverId || 
                            String(d.raw_id || '') === selectedDriverId
                        );
                        const callSign = (driver && driver.call_sign) ? `${driver.call_sign}/` : '';
                        const dName = driver ? (driver.name || 'Driver') : 'Assigned';
                        const driverCell = row.querySelector('.driver-cell');
                        if (driverCell) {
                            driverCell.innerHTML = `<span class="badge rounded-pill bg-danger bg-opacity-90 px-2.5 py-1.5 me-1 driver-badge" style="font-size: 11.5px;">${callSign}${dName}</span>`;
                        }

                        const dispatchItem = row.querySelector('.action-dispatch-item');
                        const trackItem = row.querySelector('.action-track-item');
                        const recallItem = row.querySelector('.action-recall-item');
                        if (dispatchItem) dispatchItem.style.display = 'none';
                        if (trackItem) trackItem.style.display = '';
                        if (recallItem) recallItem.style.display = '';
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
                const response = await fetch("{{ route('bookings.sendEmailDashboard') }}", {
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
    const tableContainer = document.getElementById('bookingsTableBody');
    const paginationContainer = document.getElementById('bookingsPaginationContainer');
    const totalCountBadge = document.getElementById('bookingsTotalCount');
    const filterResetWrapper = document.getElementById('filterResetWrapper');
    const tableCard = document.querySelector('#futureBookingsTable')?.closest('.card');

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
                applyDashboardFilter();
            } else if (selectedDates.length === 1) {
                fromInput.value = instance.formatDate(selectedDates[0], "Y-m-d");
                toInput.value = instance.formatDate(selectedDates[0], "Y-m-d");
            } else {
                fromInput.value = '';
                toInput.value = '';
                applyDashboardFilter();
            }
        }
    });

    if (calendarIconBtn) {
        calendarIconBtn.addEventListener('click', function () {
            fp.open();
        });
    }

    // =========================================================================
    // ⚡ AJAX DYNAMIC FILTERING & PAGINATION (NO FULL PAGE RELOAD)
    // =========================================================================
    const globalLoader = document.getElementById('dashboardGlobalLoader');
    let searchDebounceTimer = null;
    let currentFilterAbortController = null;

    function showGlobalLoader() {
        if (globalLoader) {
            globalLoader.style.display = 'flex';
            globalLoader.offsetHeight;
            globalLoader.classList.add('active');
        }
    }

    function hideGlobalLoader() {
        if (globalLoader) {
            globalLoader.classList.remove('active');
            setTimeout(() => {
                if (!globalLoader.classList.contains('active')) {
                    globalLoader.style.display = 'none';
                }
            }, 230);
        }
    }

    async function applyDashboardFilter(customUrl = null, updateBrowserHistory = true) {
        if (!searchForm || !tableContainer) return;

        // Abort previous in-flight filter request
        if (currentFilterAbortController) {
            currentFilterAbortController.abort();
        }
        currentFilterAbortController = new AbortController();

        // Construct search URL
        let fetchUrl;
        if (customUrl) {
            fetchUrl = customUrl;
        } else {
            const formData = new FormData(searchForm);
            const params = new URLSearchParams();
            for (const [k, v] of formData.entries()) {
                if (v && v.trim() !== '') {
                    params.set(k, v.trim());
                }
            }
            fetchUrl = `${searchForm.action}?${params.toString()}`;
        }

        // Show Fullscreen Loader & Table Loading State
        showGlobalLoader();
        if (tableCard) {
            tableCard.style.transition = 'opacity 0.2s ease';
            tableCard.style.opacity = '0.55';
            tableCard.style.pointerEvents = 'none';
        }

        try {
            const response = await fetch(fetchUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                signal: currentFilterAbortController.signal
            });

            if (!response.ok) {
                throw new Error(`Server status ${response.status}`);
            }

            const data = await response.json();

            if (data && data.success) {
                // Update table rows
                tableContainer.innerHTML = data.table_html;

                // Update pagination links
                if (paginationContainer) {
                    paginationContainer.innerHTML = data.pagination_html || '';
                }

                // Update total badge count
                if (totalCountBadge && typeof data.total !== 'undefined') {
                    totalCountBadge.textContent = data.total;
                }

                // Re-bind interactive row event listeners (status change, modals, action dropdowns)
                if (typeof bindRowEvents === 'function') {
                    bindRowEvents(tableContainer);
                }

                // Smoothly update browser address bar
                if (updateBrowserHistory && window.history && window.history.pushState) {
                    window.history.pushState({ path: fetchUrl }, '', fetchUrl);
                }

                // Update reset button dynamic state
                updateResetButtonState();
            }
        } catch (err) {
            if (err.name !== 'AbortError') {
                console.error('Filter AJAX error:', err);
            }
        } finally {
            hideGlobalLoader();
            if (tableCard) {
                tableCard.style.opacity = '1';
                tableCard.style.pointerEvents = 'auto';
            }
        }
    }

    function updateResetButtonState() {
        const sVal = document.getElementById('universalSearchInput')?.value.trim() || '';
        const dRangeVal = document.getElementById('dateRangePicker')?.value.trim() || '';
        const fDateVal = fromInput?.value.trim() || '';
        const tDateVal = toInput?.value.trim() || '';
        const driverVal = searchForm.querySelector('select[name="driver_id"]')?.value || '';
        const payVal = searchForm.querySelector('select[name="payment_type"]')?.value || '';

        const hasFilter = sVal || dRangeVal || fDateVal || tDateVal || driverVal || payVal;
        let resetBtn = document.getElementById('resetFiltersBtn');
        if (hasFilter) {
            if (!resetBtn && filterResetWrapper) {
                resetBtn = document.createElement('button');
                resetBtn.type = 'button';
                resetBtn.id = 'resetFiltersBtn';
                resetBtn.className = 'btn btn-outline-secondary d-flex align-items-center justify-content-center';
                resetBtn.title = 'Reset All Filters';
                resetBtn.style.cssText = 'border-radius: 10px; width: 36px; height: 36px; flex-shrink: 0;';
                resetBtn.innerHTML = '<i class="bi bi-x-lg"></i>';
                filterResetWrapper.appendChild(resetBtn);
                attachResetListener(resetBtn);
            }
        } else {
            if (resetBtn) resetBtn.remove();
        }
    }

    function attachResetListener(btn) {
        if (!btn) return;
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            searchForm.reset();
            if (fp) fp.clear();
            fromInput.value = '';
            toInput.value = '';
            const drp = document.getElementById('dateRangePicker');
            if (drp) drp.value = '';
            const usi = document.getElementById('universalSearchInput');
            if (usi) usi.value = '';
            searchForm.querySelectorAll('select').forEach(sel => sel.value = '');
            showDashboardToast('Filters Cleared', 'Resetting and loading all future bookings...', 'info');
            applyDashboardFilter("{{ route('dashboard') }}");
        });
    }

    // Attach listener to initial reset button if rendered by blade
    const initialResetBtn = document.getElementById('resetFiltersBtn');
    if (initialResetBtn) attachResetListener(initialResetBtn);

    // Form submit listener (Filter button / Enter key)
    searchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        applyDashboardFilter();
    });

    // Select dropdowns auto-apply filter on change
    searchForm.querySelectorAll('select').forEach(sel => {
        sel.addEventListener('change', function () {
            applyDashboardFilter();
        });
    });

    // Live search on keyword typing (400ms debounce)
    const searchInput = document.getElementById('universalSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(() => {
                applyDashboardFilter();
            }, 380);
        });
    }

    // Quick Date Preset Buttons (Instant AJAX filter)
    document.querySelectorAll('.quick-date-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
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
                if (fp) fp.clear();
                fromInput.value = '';
                toInput.value = '';
                const drp = document.getElementById('dateRangePicker');
                if (drp) drp.value = '';
                const usi = document.getElementById('universalSearchInput');
                if (usi) usi.value = '';
                searchForm.querySelectorAll('select').forEach(sel => sel.value = '');
                showDashboardToast('Filters Cleared', 'Showing all future bookings.', 'info');
                applyDashboardFilter("{{ route('dashboard') }}");
                return;
            }

            if (fromD && toD) {
                fp.setDate([fromD, toD], true);
                fromInput.value = fp.formatDate(fromD, "Y-m-d");
                toInput.value = fp.formatDate(toD, "Y-m-d");
                applyDashboardFilter();
            }
        });
    });

    // Intercept Pagination Clicks (Smooth AJAX page change)
    if (paginationContainer) {
        paginationContainer.addEventListener('click', function (e) {
            const pageLink = e.target.closest('a.page-link');
            if (pageLink && pageLink.href) {
                e.preventDefault();
                applyDashboardFilter(pageLink.href);
                const tableEl = document.getElementById('futureBookingsTable');
                if (tableEl) {
                    tableEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    }

    // Browser Back / Forward button navigation
    window.addEventListener('popstate', function () {
        applyDashboardFilter(window.location.href, false);
    });

    // =========================================================================
    // ⚡ INSTANT PRICE CALCULATOR & ADDRESS AUTOCOMPLETE (OS UK PLACES API)
    // =========================================================================
    class DashboardAddressAutocomplete {
        constructor(options) {
            this.field = document.getElementById(options.fieldId);
            this.apiKey = options.apiKey;
            this.onSelectCallback = options.onSelect;
            this.activeIndex = -1;
            if (this.field) {
                this.init();
            }
        }

        init() {
            // Wrapper around input field
            this.wrapper = document.createElement('div');
            this.wrapper.style.cssText = 'position: relative; flex: 1 1 auto; width: 1%;';
            this.field.parentNode.insertBefore(this.wrapper, this.field);
            this.wrapper.appendChild(this.field);

            // Dropdown List Container
            this.listContainer = document.createElement('ul');
            this.listContainer.style.cssText = 'position: absolute; top: 100%; left: 0; right: 0; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; z-index: 1060; list-style: none; padding: 0; margin: 4px 0 0 0; display: none; max-height: 220px; overflow-y: auto; box-shadow: 0 10px 25px rgba(0,0,0,0.15);';
            this.wrapper.appendChild(this.listContainer);

            let debounceTimer;

            this.field.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                const query = this.field.value.trim();
                this.activeIndex = -1;
                if (query.length < 3) {
                    this.listContainer.style.display = 'none';
                    return;
                }
                debounceTimer = setTimeout(() => this.fetchOSPlaces(query), 350);
            });

            // Handle keyboard navigation & Enter key
            this.field.addEventListener('keydown', (e) => {
                const items = this.listContainer.querySelectorAll('li');
                const isDropdownOpen = this.listContainer.style.display === 'block' && items.length > 0;

                if (e.key === 'ArrowDown') {
                    if (isDropdownOpen) {
                        e.preventDefault();
                        this.activeIndex = (this.activeIndex + 1) % items.length;
                        this.updateActiveItem(items);
                    }
                } else if (e.key === 'ArrowUp') {
                    if (isDropdownOpen) {
                        e.preventDefault();
                        this.activeIndex = (this.activeIndex - 1 + items.length) % items.length;
                        this.updateActiveItem(items);
                    }
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    e.stopPropagation();

                    if (isDropdownOpen) {
                        const targetItem = this.activeIndex >= 0 ? items[this.activeIndex] : items[0];
                        if (targetItem) {
                            targetItem.dispatchEvent(new MouseEvent('mousedown', { bubbles: true }));
                            return;
                        }
                    }

                    this.listContainer.style.display = 'none';
                    if (typeof fetchDashboardInstantPrice === 'function') {
                        fetchDashboardInstantPrice();
                    }
                } else if (e.key === 'Escape') {
                    this.listContainer.style.display = 'none';
                    this.activeIndex = -1;
                }
            });

            document.addEventListener('click', (e) => {
                if (!this.wrapper.contains(e.target)) {
                    this.listContainer.style.display = 'none';
                    this.activeIndex = -1;
                }
            });
        }

        updateActiveItem(items) {
            items.forEach((item, index) => {
                if (index === this.activeIndex) {
                    item.style.backgroundColor = '#e0f2fe';
                    item.style.color = '#0369a1';
                    item.scrollIntoView({ block: 'nearest' });
                } else {
                    item.style.backgroundColor = 'transparent';
                    item.style.color = '#1e293b';
                }
            });
        }

        async fetchOSPlaces(query) {
            if (!this.apiKey) return;
            const url = `https://api.os.uk/search/places/v1/find?query=${encodeURIComponent(query)}&key=${this.apiKey}&output_srs=EPSG:4326&maxresults=10`;
            try {
                const response = await fetch(url);
                const data = await response.json();
                this.renderDropdown(data.results || []);
            } catch (error) {
                console.error('OS Places API Error:', error);
            }
        }

        renderDropdown(results) {
            this.listContainer.innerHTML = '';
            this.activeIndex = -1;
            if (!results || results.length === 0) {
                this.listContainer.style.display = 'none';
                return;
            }

            results.forEach((item, idx) => {
                const place = item.DPA || item.LPI;
                if (!place) return;

                const addressParts = [
                    place.ORGANISATION_NAME,
                    place.BUILDING_NAME,
                    place.BUILDING_NUMBER,
                    place.THOROUGHFARE_NAME,
                    place.POST_TOWN,
                    place.POSTCODE
                ].filter(Boolean);

                const fullAddress = addressParts.join(', ');

                const li = document.createElement('li');
                li.setAttribute('data-index', idx);
                li.style.cssText = 'padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #f1f5f9; font-size: 11.5px; color: #1e293b; line-height: 1.4;';
                li.textContent = fullAddress;

                li.addEventListener('mouseenter', () => {
                    this.activeIndex = idx;
                    const items = this.listContainer.querySelectorAll('li');
                    this.updateActiveItem(items);
                });

                li.addEventListener('mouseleave', () => {
                    li.style.backgroundColor = 'transparent';
                    li.style.color = '#1e293b';
                });

                li.addEventListener('mousedown', (e) => {
                    e.preventDefault();
                    this.field.value = fullAddress;
                    this.listContainer.style.display = 'none';
                    this.activeIndex = -1;
                    if (typeof this.onSelectCallback === 'function') {
                        setTimeout(() => this.onSelectCallback(fullAddress, place), 60);
                    }
                });

                this.listContainer.appendChild(li);
            });

            this.listContainer.style.display = 'block';
        }
    }

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
    const getPriceBtn = document.getElementById('getInstantPriceBtn');
    const calcFinalBadge = document.getElementById('calcFinalPriceBadge');
    const calcDistText = document.getElementById('calcDistText');
    const calcBreakdownText = document.getElementById('calcBreakdownText');
    const calcLoader = document.getElementById('calcLoader');
    const calcBookNowPrompt = document.getElementById('calcBookNowPrompt');
    const calcPromptPrice = document.getElementById('calcPromptPrice');
    const openQuickBookingModalBtn = document.getElementById('openQuickBookingModalBtn');

    // Store calculated trip details in memory
    window.currentCalculatedBooking = null;

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

    // Extract UK Postcode or fallback to address
    function extractUkPostcode(addr) {
        if (!addr) return '';
        const postcodeRegex = /\b[A-Z]{1,2}\d{1,2}[A-Z]?\s*\d?[A-Z]{0,2}\b/i;
        const match = addr.match(postcodeRegex);
        return match ? match[0].toUpperCase().trim() : addr.trim();
    }

    // Fetch Instant Price from Backend API
    async function fetchDashboardInstantPrice() {
        const pickupRaw = calcPickupInput?.value.trim();
        const dropoffRaw = calcDropoffInput?.value.trim();

        if (!pickupRaw || !dropoffRaw) {
            if (calcBookNowPrompt) calcBookNowPrompt.style.setProperty('display', 'none', 'important');
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
                const baseFare = parseFloat(data.base_price || data.price || 0);
                const parkingFee = parseFloat(data.parking_fee || 0);
                const totalFare = parseFloat(data.total_with_parking || (baseFare + parkingFee));
                const distance = parseFloat(data.journey_distance || data.total_distance || 0);
                const formattedMiles = distance > 0 ? `${distance.toFixed(2)} Miles` : `0.00 Miles`;

                if (calcDistText) {
                    calcDistText.textContent = formattedMiles;
                }

                if (calcFinalBadge) {
                    calcFinalBadge.textContent = `£${totalFare.toFixed(2)}`;
                }

                if (calcBreakdownText) {
                    if (parkingFee > 0) {
                        calcBreakdownText.textContent = `Fare: £${baseFare.toFixed(2)} + Park: £${parkingFee.toFixed(2)}`;
                    } else {
                        calcBreakdownText.textContent = `Fare: £${baseFare.toFixed(2)}`;
                    }
                }

                // Store in memory for Quick Booking Modal
                window.currentCalculatedBooking = {
                    pickupRaw,
                    dropoffRaw,
                    viaRaw: viaVal,
                    vehicle,
                    date,
                    time,
                    baseFare,
                    parkingFee,
                    totalFare,
                    distance: formattedMiles
                };

                // Show Prompt: "Do you want to make a booking against this?"
                if (calcPromptPrice) {
                    calcPromptPrice.textContent = `£${totalFare.toFixed(2)}`;
                }
                if (calcBookNowPrompt) {
                    calcBookNowPrompt.style.setProperty('display', 'flex', 'important');
                }
            } else {
                if (calcBreakdownText) {
                    calcBreakdownText.textContent = data.message || 'No route found';
                }
                if (calcBookNowPrompt) {
                    calcBookNowPrompt.style.setProperty('display', 'none', 'important');
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

    // Fetch Firebase Ordnance Survey Key and initialize Address Autocomplete
    (async function initDashboardAddressAutocomplete() {
        let apiKey = '';
        try {
            const res = await fetch('https://crown-carz-default-rtdb.firebaseio.com/system_settings/autocomplete_key.json');
            apiKey = await res.json();
        } catch (e) {
            console.error('Failed to fetch autocomplete key from Firebase:', e);
        }

        if (!apiKey) {
            console.warn('AddressAutocomplete: No API key loaded from Firebase.');
            return;
        }

        if (calcPickupInput) {
            new DashboardAddressAutocomplete({
                fieldId: 'calcPickupInput',
                apiKey: apiKey,
                onSelect: function () {
                    if (calcDropoffInput && calcDropoffInput.value.trim()) {
                        fetchDashboardInstantPrice();
                    }
                }
            });
        }

        if (calcDropoffInput) {
            new DashboardAddressAutocomplete({
                fieldId: 'calcDropoffInput',
                apiKey: apiKey,
                onSelect: function () {
                    if (calcPickupInput && calcPickupInput.value.trim()) {
                        fetchDashboardInstantPrice();
                    }
                }
            });
        }

        if (calcViaInput) {
            new DashboardAddressAutocomplete({
                fieldId: 'calcViaInput',
                apiKey: apiKey,
                onSelect: function () {
                    fetchDashboardInstantPrice();
                }
            });
        }
    })();

    // =========================================================================
    // 🚗 QUICK BOOKING MODAL & AJAX FORM SUBMISSION
    // =========================================================================
    const quickBookingModalEl = document.getElementById('quickBookingModal');
    const quickBookingForm = document.getElementById('quickBookingForm');
    const qbPaymentTypeSelect = document.getElementById('qb_payment_type');
    const qbAccountWrapper = document.getElementById('qb_account_wrapper');
    const qbAccountIdSelect = document.getElementById('qb_account_id');
    const qbAccountNameInput = document.getElementById('qb_account_name');
    const qbAlertError = document.getElementById('qbAlertError');
    const qbSubmitBtn = document.getElementById('qbSubmitBtn');
    const qbSubmitSpinner = document.getElementById('qbSubmitSpinner');
    const qbSubmitIcon = document.getElementById('qbSubmitIcon');
    const qbSubmitText = document.getElementById('qbSubmitText');

    // Payment type change (Toggle Account select)
    if (qbPaymentTypeSelect && qbAccountWrapper) {
        qbPaymentTypeSelect.addEventListener('change', function () {
            if (this.value === 'account') {
                qbAccountWrapper.style.display = 'block';
            } else {
                qbAccountWrapper.style.display = 'none';
                if (qbAccountIdSelect) qbAccountIdSelect.value = '';
                if (qbAccountNameInput) qbAccountNameInput.value = '';
            }
        });
    }

    // Account change -> store account name
    if (qbAccountIdSelect && qbAccountNameInput) {
        qbAccountIdSelect.addEventListener('change', function () {
            const selectedOpt = this.options[this.selectedIndex];
            qbAccountNameInput.value = selectedOpt ? (selectedOpt.getAttribute('data-name') || '') : '';
        });
    }

    // Open Quick Booking Modal on button click
    if (openQuickBookingModalBtn && quickBookingModalEl) {
        openQuickBookingModalBtn.addEventListener('click', function () {
            const bookingData = window.currentCalculatedBooking || {
                pickupRaw: calcPickupInput?.value.trim() || '',
                dropoffRaw: calcDropoffInput?.value.trim() || '',
                viaRaw: calcViaWrapper?.style.display !== 'none' ? calcViaInput?.value.trim() : '',
                vehicle: calcVehicleSelect?.value || 'Saloon',
                date: calcDateInput?.value || '',
                time: calcTimeInput?.value || '',
                baseFare: 0,
                parkingFee: 0,
                totalFare: 0,
                distance: '0.00 Mi'
            };

            // Populate Hidden Inputs
            const qbPickup = document.getElementById('qb_pickup_address');
            const qbDropoff = document.getElementById('qb_dropoff_address');
            const qbViaContainer = document.getElementById('qb_via_container');
            const qbVehicle = document.getElementById('qb_vehicle_make');
            const qbDate = document.getElementById('qb_pickup_date');
            const qbTime = document.getElementById('qb_pickup_time');
            const qbPrice = document.getElementById('qb_price');
            const qbFare = document.getElementById('qb_fare');
            const qbParking = document.getElementById('qb_parking');

            if (qbPickup) qbPickup.value = bookingData.pickupRaw;
            if (qbDropoff) qbDropoff.value = bookingData.dropoffRaw;
            if (qbVehicle) qbVehicle.value = bookingData.vehicle;
            if (qbDate) qbDate.value = bookingData.date;
            if (qbTime) qbTime.value = bookingData.time;
            if (qbPrice) qbPrice.value = bookingData.totalFare;
            if (qbFare) qbFare.value = bookingData.baseFare;
            if (qbParking) qbParking.value = bookingData.parkingFee;

            if (qbViaContainer) {
                qbViaContainer.innerHTML = '';
                if (bookingData.viaRaw) {
                    const viaHidden = document.createElement('input');
                    viaHidden.type = 'hidden';
                    viaHidden.name = 'via_addresses[]';
                    viaHidden.value = bookingData.viaRaw;
                    qbViaContainer.appendChild(viaHidden);
                }
            }

            // Populate Summary Display
            const qbSummaryVehicle = document.getElementById('qbSummaryVehicle');
            const qbSummaryDateTime = document.getElementById('qbSummaryDateTime');
            const qbSummaryDistance = document.getElementById('qbSummaryDistance');
            const qbSummaryFare = document.getElementById('qbSummaryFare');
            const qbSummaryPickup = document.getElementById('qbSummaryPickup');
            const qbSummaryDropoff = document.getElementById('qbSummaryDropoff');
            const qbSummaryViaRow = document.getElementById('qbSummaryViaRow');
            const qbSummaryVia = document.getElementById('qbSummaryVia');

            if (qbSummaryVehicle) qbSummaryVehicle.textContent = bookingData.vehicle;
            if (qbSummaryDateTime) qbSummaryDateTime.textContent = `${bookingData.date} ${bookingData.time}`;
            if (qbSummaryDistance) qbSummaryDistance.textContent = bookingData.distance;
            if (qbSummaryFare) qbSummaryFare.textContent = `£${parseFloat(bookingData.totalFare || 0).toFixed(2)}`;
            if (qbSummaryPickup) qbSummaryPickup.textContent = bookingData.pickupRaw || '-';
            if (qbSummaryDropoff) qbSummaryDropoff.textContent = bookingData.dropoffRaw || '-';

            if (qbSummaryViaRow && qbSummaryVia) {
                if (bookingData.viaRaw) {
                    qbSummaryVia.textContent = bookingData.viaRaw;
                    qbSummaryViaRow.style.display = 'block';
                } else {
                    qbSummaryViaRow.style.display = 'none';
                }
            }

            // Reset passenger input errors
            if (qbAlertError) qbAlertError.style.display = 'none';

            // Show Modal
            const modalInstance = bootstrap.Modal.getOrCreateInstance(quickBookingModalEl);
            modalInstance.show();
        });
    }

    // Submit Quick Booking Form via AJAX
    if (quickBookingForm) {
        quickBookingForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            if (qbAlertError) qbAlertError.style.display = 'none';

            // Button Loading State
            if (qbSubmitBtn) qbSubmitBtn.disabled = true;
            if (qbSubmitSpinner) qbSubmitSpinner.style.display = 'inline-block';
            if (qbSubmitIcon) qbSubmitIcon.style.display = 'none';
            if (qbSubmitText) qbSubmitText.textContent = 'Creating Booking...';

            const formData = new FormData(quickBookingForm);

            try {
                const response = await fetch(quickBookingForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Hide Modal
                    const modalInstance = bootstrap.Modal.getInstance(quickBookingModalEl);
                    if (modalInstance) modalInstance.hide();

                    // Reset form & prompt
                    quickBookingForm.reset();
                    if (calcBookNowPrompt) calcBookNowPrompt.style.setProperty('display', 'none', 'important');

                    // Show Toast Notification
                    showDashboardToast(
                        'Booking Created',
                        `Ref: ${data.ref_no || ''} - Booking created successfully!`,
                        'success',
                        5000
                    );

                    // Seamlessly refresh the bookings table without full page reload
                    applyDashboardFilter(window.location.href, false);
                } else {
                    let errMsg = data.message || 'Failed to create booking. Please check required fields.';
                    if (data.errors) {
                        errMsg = Object.values(data.errors).flat().join('<br>');
                    }
                    if (qbAlertError) {
                        qbAlertError.innerHTML = errMsg;
                        qbAlertError.style.display = 'block';
                    } else {
                        showDashboardToast('Error', errMsg, 'danger');
                    }
                }
            } catch (err) {
                console.error('Quick booking submission error:', err);
                if (qbAlertError) {
                    qbAlertError.innerHTML = 'A network error occurred while submitting booking. Please try again.';
                    qbAlertError.style.display = 'block';
                } else {
                    showDashboardToast('Error', 'Network error occurred.', 'danger');
                }
            } finally {
                if (qbSubmitBtn) qbSubmitBtn.disabled = false;
                if (qbSubmitSpinner) qbSubmitSpinner.style.display = 'none';
                if (qbSubmitIcon) qbSubmitIcon.style.display = 'inline-block';
                if (qbSubmitText) qbSubmitText.textContent = 'Confirm & Create Booking';
            }
        });
    }
    // =========================================================================
    // 📊 DAY-WISE BOOKINGS CHART.JS INITIALIZATION (1/3 TOP COLUMN)
    // =========================================================================
    const chartDataRaw = @json($dayWiseChartData ?? []);
    const chartCanvas = document.getElementById('dayWiseBookingsChart');

    if (chartCanvas && Array.isArray(chartDataRaw) && chartDataRaw.length > 0 && typeof Chart !== 'undefined') {
        const ctx = chartCanvas.getContext('2d');
        const shortLabels = chartDataRaw.map(d => (d.is_today ? '⭐ ' : '') + d.short_label);
        const counts = chartDataRaw.map(d => d.count || 0);
        const backgroundColors = chartDataRaw.map(d => d.is_today ? '#111827' : (d.is_future ? 'rgba(184, 115, 51, 0.75)' : 'rgba(100, 116, 139, 0.45)'));
        const borderColors = chartDataRaw.map(d => d.is_today ? '#E6B04A' : (d.is_future ? '#B87333' : '#64748b'));

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: shortLabels,
                datasets: [
                    {
                        type: 'line',
                        label: 'Trend',
                        data: counts,
                        borderColor: '#E6B04A',
                        borderWidth: 2,
                        pointBackgroundColor: chartDataRaw.map(d => d.is_today ? '#111827' : '#E6B04A'),
                        pointBorderColor: chartDataRaw.map(d => d.is_today ? '#E6B04A' : '#ffffff'),
                        pointBorderWidth: 1.5,
                        pointRadius: chartDataRaw.map(d => d.is_today ? 5 : 2.5),
                        pointHoverRadius: 6,
                        tension: 0.35,
                        fill: false,
                        order: 1
                    },
                    {
                        type: 'bar',
                        label: 'Daily Bookings',
                        data: counts,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 1.2,
                        borderRadius: 4,
                        borderSkipped: false,
                        barPercentage: 0.6,
                        categoryPercentage: 0.85,
                        order: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#111827',
                        titleColor: '#E6B04A',
                        bodyColor: '#ffffff',
                        padding: 8,
                        cornerRadius: 8,
                        borderColor: 'rgba(230, 176, 74, 0.4)',
                        borderWidth: 1,
                        callbacks: {
                            title: function(items) {
                                if (!items.length) return '';
                                const idx = items[0].dataIndex;
                                const item = chartDataRaw[idx];
                                return (item.is_today ? '⭐ TODAY - ' : '') + (item.label || item.date);
                            },
                            label: function(context) {
                                if (context.dataset.type === 'line') return null;
                                const idx = context.dataIndex;
                                const item = chartDataRaw[idx];
                                const count = item.count || 0;
                                const rev = parseFloat(item.revenue || 0).toFixed(2);
                                return [
                                    `📋 Bookings: ${count}`,
                                    `💷 Est. Total: £${rev}`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 9,
                            color: function(ctx) {
                                const idx = ctx.index;
                                return (chartDataRaw[idx] && chartDataRaw[idx].is_today) ? '#B87333' : '#64748b';
                            },
                            font: function(ctx) {
                                const idx = ctx.index;
                                return {
                                    size: 9.5,
                                    weight: (chartDataRaw[idx] && chartDataRaw[idx].is_today) ? 'bold' : 'normal'
                                };
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                            color: '#94a3b8',
                            font: {
                                size: 9.5
                            }
                        },
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        }
                    }
                }
            }
        });
    }
});
</script>

@endpush
