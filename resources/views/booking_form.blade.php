@extends('layouts.app')
<style>
.via-container .via-field {
  position: relative;
  margin-bottom: 0.6rem;
  width: 100%;
}
.via-container input[type="text"] {
  width: 100%;
  padding: 0.65rem 0.9rem;
  padding-right: 3.5rem; /* Space for overlay button */
  border: 1px solid #ced4da;
  border-radius: 0.6rem;
}
.via-container .remove-via {
  position: absolute;
  top: 50%;
  right: 0.75rem;
  transform: translateY(-50%);
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  border: 1px solid #dc3545;
  background-color: white;
  color: #dc3545;
  transition: all 0.2s ease;
  font-size: 0.75rem;
}
.via-container .remove-via:hover {
  background-color: #dc3545;
  color: white;
}
/* Responsive adjustments */
@media (max-width: 991.98px) {
  .container-fluid {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
  }
 
  .card-body {
    padding: 1.5rem !important;
  }
 
  #routeMap {
    height: 300px !important;
  }
 
  .row.g-4 > .col-lg-6 {
    padding-bottom: 1.5rem;
  }
}
@media (max-width: 575.98px) {
  .form-control,
  .form-select {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
  }
 
  .mb-3 {
    margin-bottom: 0.75rem !important;
  }
 
  #routeMap {
    height: 250px !important;
  }
 
  .input-group .input-group-text {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
  }
 
  .btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
  }
 
  .card-header {
    padding: 0.75rem 1rem !important;
  }
 
  h5 {
    font-size: 1.1rem;
  }
}
#parking {
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
}
/* ✨ Slide Animation for Swap ✨ */
@keyframes slideLeft {
  0% { transform: translateX(0); opacity: 1; }
  50% { transform: translateX(-40px); opacity: 0.6; }
  100% { transform: translateX(0); opacity: 1; }
}
@keyframes slideRight {
  0% { transform: translateX(0); opacity: 1; }
  50% { transform: translateX(40px); opacity: 0.6; }
  100% { transform: translateX(0); opacity: 1; }
}

.slide-left {
  animation: slideLeft 0.25s ease-in-out;
}
.slide-right {
  animation: slideRight 0.25s ease-in-out;
}

</style>
<!-- Flatpickr 24-hour time picker with dropdown -->
<!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">-->
<!--<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>-->
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<div class="container-fluid mt-3 px-2 px-md-3">
    <div class="mb-3">
  <!--<h6 class="fw-bold mb-2">Actions</h6>-->
  <div class="d-flex flex-wrap gap-2">
    <button type="button" class="btn btn-outline-primary btn-sm" id="btn-driver-sms">Driver SMS</button>
<button type="button" class="btn btn-outline-secondary btn-sm" id="btn-confirm-sms">Confirm SMS</button>
<button type="button" class="btn btn-outline-info btn-sm" id="btn-onroute-sms">OnRoute SMS</button>
<button type="button" class="btn btn-outline-success btn-sm" id="btn-arrived-sms">Arrived SMS</button>
<button type="button" class="btn btn-outline-dark btn-sm" id="btn-complete-sms">Complete SMS</button>
<!--<input type="hidden" id="currentBookingId" value="-OeNX9fM-eba7JGomUmV">-->

<input type="hidden" id="currentBookingId" 
       value="{{ isset($booking) ? $booking['id'] : '' }}">
       
<input type="hidden" id="currentDriverId"
       value="{{ isset($booking) ? ($booking['driver_id'] ?? '') : '' }}">
       
<input type="hidden" id="currentDriverName" value="">
<input type="hidden" id="currentDriverPhone" value="">       

<button type="button" class="btn btn-outline-warning btn-sm" id="btn-confirmation-email">Confirmation Email</button>
<button type="button" class="btn btn-outline-danger btn-sm" id="btn-recurring-job">Re-Occurring Job</button>
<button type="button" class="btn btn-outline-secondary btn-sm" id="btn-return-job">Return Job</button>
<button type="button" class="btn btn-outline-success btn-sm" id="btn-send-receipt">Send Receipt Email</button>
@if(isset($booking))
    <button type="button" class="btn btn-outline-primary btn-sm" id="btn-payment">
        Payment
    </button>
@endif
    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#smsLoginModal">
    Configure SMS API
</button>
<button type="button"
        class="btn btn-outline-primary btn-sm"
        id="btn-previous-bookings"
        data-bs-toggle="modal"
        data-bs-target="#previousBookingsModal">
    View Prev Bookings
</button>


  </div>
</div>
  <div class="card shadow-sm border-0 rounded-4 mx-auto">
    <div class="card-header rounded-top-4 bg-warning bg-opacity-25 px-3 py-2">
      <h6 class="mb-0 text-dark">{{ isset($booking) ? 'Edit Booking' : 'Create Booking' }}</h6>
    </div>
    <div class="card-body p-2 p-md-4">
      <div class="row g-3 g-md-4" style="zoom:70%">
        <div class="col-12 col-lg-7">
          <form id="booking-form" action="{{ isset($booking) ? route('booking.update', $booking['id']) : route('booking.store') }}" method="POST">
            @csrf
            @if(isset($booking))
              @method('POST')
            @endif
            <!--<div class="row">-->
            <!--<div class="mb-2 mb-md-3 col-md-4">-->
            <!--  <label class="form-label  fs-6 fs-md-6">Passenger Name</label>-->
            <!--  <input type="text" name="passenger_name" class="form-control rounded-3 shadow-sm" value="{{ old('passenger_name', $booking->passenger->name ?? '') }}" required>-->
            <!--</div>-->
           
            <!--<div class="mb-2 mb-md-3 col-md-4">-->
            <!--  <label class="form-label  fs-6 fs-md-6">Phone No</label>-->
            <!--  <input type="text" name="phone_no" class="form-control rounded-3 shadow-sm" value="{{ old('phone_no', $booking->phone_no ?? '') }}" required>-->
            <!--</div>-->
           
            <!--<div class="mb-2 mb-md-3 col-md-4">-->
            <!--  <label class="form-label  fs-6 fs-md-6">Flight No (Optional)</label>-->
            <!--  <input type="text" name="flight_no" class="form-control rounded-3 shadow-sm" value="{{ old('flight_no', $booking->flight_no ?? '') }}">-->
            <!--</div>-->
            <!--</div>-->
            <input type="text"
               id="ref_no"
               name="ref_no"
               class="form-control" value="{{ old('ref_no', $booking['ref_no'] ?? '') }}" hidden>
            <div class="row">
  <!--<div class="mb-2 mb-md-3 col-md-4">-->
  <!--  <label class="form-label fs-6 fs-md-6">Passenger Name</label>-->
  <!--  <input type="text" name="passenger_name" class="form-control rounded-3 shadow-sm"-->
  <!--         value="{{ old('passenger_name', $booking['passenger_name'] ?? '') }}" required>-->
  <!--</div>-->
  <div class="mb-2 mb-md-3 col-md-4 position-relative">
        <label class="form-label">Passenger Name</label>
        <input type="text"
               id="passenger_name"
               name="passenger_name"
               class="form-control"
               autocomplete="off"
               value="{{ old('passenger_name', $booking['passenger_name'] ?? '') }}"
               required>

        <div id="customerDropdown"
             class="list-group position-absolute w-100"
             style="z-index:1000; display:none;"></div>
    </div>

  <div class="mb-2 mb-md-3 col-md-4">
    <label class="form-label fs-6 fs-md-6">Phone No</label>
    <input type="text" name="phone_no" class="form-control rounded-3 shadow-sm"
           value="{{ old('phone_no', $booking['phone_no'] ?? '') }}" required>
  </div>

  {{-- ✅ New Email Field --}}
  <div class="mb-2 mb-md-3 col-md-4">
    <label class="form-label fs-6 fs-md-6">Email</label>
    <input type="email" name="email" class="form-control rounded-3 shadow-sm"
           value="{{ old('email', $booking['email'] ?? '') }}" required>
  </div>
</div>
           
           
       
           
           
             
               <!-- Pickup -->
<!--<div class="mb-3">-->
<!--  <label class="form-label  fs-6">Pickup Type</label>-->
<!--  <div class="d-flex flex-wrap gap-3 mb-2">-->
<!--    <label><input type="radio" name="pickup_type" value="airports" class="me-1" checked> Airport</label>-->
<!--    <label><input type="radio" name="pickup_type" value="stations" class="me-1"> Station</label>-->
<!--    <label><input type="radio" name="pickup_type" value="ports" class="me-1"> Port</label>-->
<!--  </div>-->
<!--  <div class="row g-2">-->
<!--    <div class="col-12 col-md-6">-->
<!--      <select id="pickup_options" class="form-select rounded-3 shadow-sm d-none">-->
<!--        <option value="">-- Select Pickup Location --</option>-->
<!--      </select>-->
<!--    </div>-->
<!--    <div class="col-12 col-md-6">-->
<!--      <input type="text" id="pickup_address" name="pickup_address" class="form-control rounded-3 shadow-sm"-->
<!--             placeholder="Type address or postcode" autocomplete="off" required>-->
<!--      <input type="hidden" id="pickup_postcode" name="pickup_postcode">-->
<!--      <input type="hidden" id="pickup_lat" name="pickup_lat">-->
<!--      <input type="hidden" id="pickup_long" name="pickup_long">-->
<!--    </div>-->
<!--  </div>-->
<!--</div>-->

<!-- ✅ Pickup Section -->
<div class="mb-3">
  <label class="form-label fs-6">Pickup</label>
  <div class="d-flex flex-wrap gap-3 mb-2">
    <label><input type="radio" name="pickup_type" value="airports" class="me-1" checked> Airport</label>
    <label><input type="radio" name="pickup_type" value="stations" class="me-1"> Station</label>
    <label><input type="radio" name="pickup_type" value="ports" class="me-1"> Port</label>
  </div>

  <div class="position-relative">
    <input list="pickup_list" id="pickup_address" name="pickup_address"
           class="form-control rounded-3 shadow-sm"
           placeholder="Type address or select from list" value="{{ old('pickup_address', $booking['pickup_address'] ?? '') }}" autocomplete="off" required>
    <datalist id="pickup_list"></datalist>
    <input type="hidden" id="pickup_postcode" name="pickup_postcode">
    <input type="hidden" id="pickup_lat" name="pickup_lat">
    <input type="hidden" id="pickup_long" name="pickup_long">
    <input type="hidden" id="pickup_address_id" name="pickup_address_id" value="">
  </div>
</div>
             
<!--<div class="text-center my-3">-->
<!--  <button type="button" id="swap-btn" class="btn btn-outline-primary rounded-pill px-4 py-1">-->
<!--    <i class="bi bi-arrow-left-right me-1"></i> Swap Pickup & Dropoff-->
<!--  </button>-->
<!--</div>-->

             
           
           
            {{-- ✅ Dynamic Via Addresses --}}
            <div class="mb-2 mb-md-3">
              <div class="d-flex justify-content-between align-items-center mb-1 mb-md-2">
                <label class=" fs-6 fs-md-6">Via Address (Optional)</label>
                <div>
                    
                         <button type="button" id="swap-btn" class="btn btn-outline-primary rounded-pill px-4 py-1">
    <i class="bi bi-arrow-left-right me-1"></i> Swap Pickup & Dropoff
  </button>
           
                 <button type="button" id="add-via" class="btn btn-outline-success btn-sm rounded-pill px-2">
                  <i class="bi bi-plus-circle"></i> Add Via
                </button>
                    
                </div>
           
              </div>
<div id="via-container" class="via-container">
@if(isset($booking) && !empty($booking['via_addresses']))
    @foreach($booking['via_addresses'] as $index => $via)
        <div class="via-field">
            <div class="position-relative">
                <input type="text"
                       name="via_addresses[]"
                       id="via_{{ $index }}"
                       class="form-control"
                       placeholder="Type your address or postcode"
                       value="{{ $via ?? '' }}"
                       autocomplete="off">

                <button type="button" class="remove-via" title="Remove">
                    <i class="bi bi-x"></i>
                </button>
            </div>

            <input type="hidden" name="via_{{ $index }}_postcode">
            <input type="hidden" name="via_{{ $index }}_lat">
            <input type="hidden" name="via_{{ $index }}_long">
        </div>
    @endforeach
@endif
</div>

            </div>
           
       
           
            <!-- ✅ Dropoff Section -->
<div class="mb-3">
  <label class="form-label fs-6">Dropoff</label>
  <div class="d-flex flex-wrap gap-3 mb-2">
    <label><input type="radio" name="dropoff_type" value="airports" class="me-1" checked> Airport</label>
    <label><input type="radio" name="dropoff_type" value="stations" class="me-1"> Station</label>
    <label><input type="radio" name="dropoff_type" value="ports" class="me-1"> Port</label>
  </div>

  <div class="position-relative">
    <input list="dropoff_list" id="dropoff_address" name="dropoff_address"
           class="form-control rounded-3 shadow-sm"
           placeholder="Type address or select from list" value="{{ old('dropoff_address', $booking['dropoff_address'] ?? '') }}" autocomplete="off" required>
    <datalist id="dropoff_list"></datalist>
    <input type="hidden" id="dropoff_postcode" name="dropoff_postcode">
    <input type="hidden" id="drop_lat" name="drop_lat">
    <input type="hidden" id="drop_long" name="drop_long">
     <input type="hidden" id="dropoff_address_id" name="dropoff_address_id" value="">
  </div>
</div>
 <!-- Dropoff -->
<!--<div class="mb-3">-->
<!--  <label class="form-label  fs-6">Dropoff Type</label>-->
<!--  <div class="d-flex flex-wrap gap-3 mb-2">-->
<!--    <label><input type="radio" name="dropoff_type" value="airports" class="me-1" checked> Airport</label>-->
<!--    <label><input type="radio" name="dropoff_type" value="stations" class="me-1"> Station</label>-->
<!--    <label><input type="radio" name="dropoff_type" value="ports" class="me-1"> Port</label>-->
<!--  </div>-->
<!--  <div class="row g-2">-->
<!--    <div class="col-12 col-md-6">-->
<!--      <select id="dropoff_options" class="form-select rounded-3 shadow-sm d-none">-->
<!--        <option value="">-- Select Dropoff Location --</option>-->
<!--      </select>-->
<!--    </div>-->
<!--    <div class="col-12 col-md-6">-->
<!--      <input type="text" id="dropoff_address" name="dropoff_address" class="form-control rounded-3 shadow-sm"-->
<!--             placeholder="Type address or postcode" autocomplete="off" required>-->
<!--      <input type="hidden" id="dropoff_postcode" name="dropoff_postcode">-->
<!--      <input type="hidden" id="drop_lat" name="drop_lat">-->
<!--      <input type="hidden" id="drop_long" name="drop_long">-->
<!--    </div>-->
<!--  </div>-->
<!--</div>-->
            @php
              $defaultDate = now()->format('Y-m-d');
              $defaultTime = now()->addHour()->format('H:00');
            @endphp
            @php
    $pickupDate = isset($booking['pickup_time']) ? \Carbon\Carbon::parse($booking['pickup_time'])->format('Y-m-d') : now()->format('Y-m-d');
    $pickupTime = isset($booking['pickup_time']) ? \Carbon\Carbon::parse($booking['pickup_time'])->format('H:i') : now()->addHour()->format('H:i');
@endphp
            <div class="row g-2 g-md-3">
              <div class="col-12 col-md-3 mb-2 mb-md-3">
                <label class="form-label  fs-6 fs-md-6">Pickup Date</label>
                <input type="date" name="pickup_date"  id="pickup_date"  class="form-control rounded-3 shadow-sm"
                       value="{{ old('pickup_date', $pickupDate) }}" required>
              </div>
              <div class="col-12 col-md-3 mb-2 mb-md-3">
  <label class="form-label fs-6 fs-md-6">Pickup Time</label>
  <input type="time" name="pickup_time" id="pickup_time" class="form-control rounded-3 shadow-sm"
         value="{{ old('pickup_time', $pickupTime) }}" required>
</div>



              
              
              

<div class="col-12 col-md-2 mb-2 mb-md-3">
  <label class="form-label fs-6 fs-md-6">Flight No (Optional)</label>
  <input type="text" name="flight_no" class="form-control rounded-3 shadow-sm" id="flight_no"
         value="{{ old('flight_no', $booking['flight_no'] ?? '') }}">
</div>
<div class="col-12 col-md-2 mb-2 mb-md-3">
            <label class="form-label fs-6 fs-md-6">Payment Type</label>
            <select name="payment_type" id="payment_type" class="form-select rounded-3 shadow-sm" required>
                <option value="cash" {{ (old('payment_type', $booking['payment_type'] ?? '') == 'cash') ? 'selected' : '' }}>Cash</option>
                <option value="card" {{ (old('payment_type', $booking['payment_type'] ?? '') == 'card') ? 'selected' : '' }}>Card</option>
                <option value="account" {{ (old('payment_type', $booking['payment_type'] ?? '') == 'account') ? 'selected' : '' }}>Account</option>
            </select>
        </div>
         @php
    // Fixed dropdown options in correct order
    $fixedVehicles = [
        ['make' => 'Saloon', 'id' => 1],
        ['make' => 'Estate', 'id' => 2],
        ['make' => 'MPV', 'id' => 3],
        ['make' => '8 Seater', 'id' => 4],
        ['make' => 'Executive', 'id' => 5],
    ];
    
    // Default = Saloon (ID = 1)
    $defaultVehicleId = 1;

    // Keep old selection logic as it is
    $selectedVehicleId = old('vehicle_id', $booking['vehicle_id'] ?? $defaultVehicleId);
@endphp


<div class="col-12 col-md-2 mb-2 mb-md-3">
  <label class="form-label fs-6 fs-md-6">Vehicle</label>
  <input type="hidden" name="vehicle_make" id="vehicle_make">
<select name="vehicle_id" id="vehicle_id" class="form-select rounded-3 shadow-sm">
    <option value="">-- Select Vehicle --</option>

    @foreach($fixedVehicles as $vehicle)
        <option data-make="{{ $vehicle['make'] }}" value="{{ $vehicle['id'] }}"
            {{ $selectedVehicleId == $vehicle['id'] ? 'selected' : '' }}>
            {{ $vehicle['make'] }}
        </option>
    @endforeach
</select>

</div>
            </div>
           
           
            <!--<div class="row g-2 g-md-3">-->
            <!--  <div class="col-12 col-md-3 mb-2 mb-md-3">-->
            <!--    <label class="form-label  fs-6 fs-md-6">Payment Type</label>-->
            <!--    <select name="payment_type" class="form-select rounded-3 shadow-sm" required>-->
            <!--      <option value="cash" {{ (old('payment_type', $booking['payment_type'] ?? '') == 'cash') ? 'selected' : '' }}>Cash</option>-->
            <!--      <option value="card" {{ (old('payment_type', $booking['payment_type'] ?? '') == 'card') ? 'selected' : '' }}>Card</option>-->
            <!--      <option value="account" {{ (old('payment_type', $booking['payment_type'] ?? '') == 'account') ? 'selected' : '' }}>Account</option>-->
            <!--    </select>-->
            <!--  </div>-->
            
            {{-- Payment Type and Account Selection --}}
    <div class="row g-2 g-md-3">
<!--        <div class="col-12 col-md-3 mb-2 mb-md-3">-->
<!--            <label class="form-label fs-6 fs-md-6">Payment Type</label>-->
<!--            <select name="payment_type" id="payment_type" class="form-select rounded-3 shadow-sm" required>-->
<!--                <option value="cash" {{ (old('payment_type', $booking['payment_type'] ?? '') == 'cash') ? 'selected' : '' }}>Cash</option>-->
<!--                <option value="card" {{ (old('payment_type', $booking['payment_type'] ?? '') == 'card') ? 'selected' : '' }}>Card</option>-->
<!--                <option value="account" {{ (old('payment_type', $booking['payment_type'] ?? '') == 'account') ? 'selected' : '' }}>Account</option>-->
<!--            </select>-->
<!--        </div>-->

        
             
              
              
              
              
<!--              @php-->
<!--    // Fixed dropdown options in correct order-->
<!--    $fixedVehicles = [-->
<!--        ['make' => 'Saloon', 'id' => 1],-->
<!--        ['make' => 'Estate', 'id' => 2],-->
<!--        ['make' => 'MPV', 'id' => 3],-->
<!--        ['make' => '8 Seater', 'id' => 4],-->
<!--        ['make' => 'Executive', 'id' => 5],-->
<!--    ];-->
    
<!--    // Default = Saloon (ID = 1)-->
<!--    $defaultVehicleId = 1;-->

<!--    // Keep old selection logic as it is-->
<!--    $selectedVehicleId = old('vehicle_id', $booking['vehicle_id'] ?? $defaultVehicleId);-->
<!--@endphp-->


<!--<div class="col-12 col-md-3 mb-2 mb-md-3">-->
<!--  <label class="form-label fs-6 fs-md-6">Vehicle</label>-->
<!--  <input type="hidden" name="vehicle_make" id="vehicle_make">-->
<!--<select name="vehicle_id" class="form-select rounded-3 shadow-sm">-->
<!--    <option value="">-- Select Vehicle --</option>-->

<!--    @foreach($fixedVehicles as $vehicle)-->
<!--        <option data-make="{{ $vehicle['make'] }}" value="{{ $vehicle['id'] }}"-->
<!--            {{ $selectedVehicleId == $vehicle['id'] ? 'selected' : '' }}>-->
<!--            {{ $vehicle['make'] }}-->
<!--        </option>-->
<!--    @endforeach-->
<!--</select>-->

<!--</div>-->
<div class="col-12 col-md-2 mb-2 mb-md-3">
    <label class="form-label  fs-6 fs-md-6">Fare (£)</label>
    <input type="number" name="fare" id="fare"
           class="form-control rounded-3 shadow-sm"
           value="{{ old('fare', $booking['fare'] ?? '') }}" step="0.01">
  </div>
  {{-- ✅ Location Charges (Pickup Fee + Dropoff Fee combined, readonly) --}}
  <div class="col-12 col-md-2 mb-2 mb-md-3">
    <label class="form-label  fs-6 fs-md-6">Parking (£)</label>
    <input type="number" name="parking" id="parking"
           class="form-control rounded-3 shadow-sm bg-light "
           value="{{ old('parking', $booking['parking'] ?? '0') }}" step="0.01">
  </div>
  <div class="col-12 col-md-2 mb-2 mb-md-3">
    <label class="form-label  fs-6 fs-md-6">Extra (£)</label>
    <input type="number" name="extra" id="extra"
           class="form-control rounded-3 shadow-sm" value="{{ old('extra', $booking['extra'] ?? '0') }}" min="0" step="0.01">
  </div>
  <div class="col-12 col-md-2 mb-2 mb-md-3">
    <label class="form-label  fs-6 fs-md-6">Waiting Fee (£)</label>
    <input type="number" name="waiting_fee" id="waiting_fee"
           class="form-control rounded-3 shadow-sm" value="{{ old('waiting_fee', $booking['waiting_fee'] ?? '0') }}" min="0" step="0.01">
  </div>
              
              
              <div class="col-12 col-md-2 mb-2 mb-md-3">
                <label class="form-label  fs-6 fs-md-6">Total Price (£)</label>
                <div class="input-group">
                    <input
  type="number"
  name="price"
  id="price"
  class="form-control rounded-start-3 shadow-sm bg-light"
  value="{{ old('price', $booking['price'] ?? '') }}"
  readonly
>

                    <span class="input-group-text rounded-end-3" id="price-loader-container" style="display: none; background-color: #f8f9fa;">
                         <div id="price-loader" class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </span>
                </div>
              </div>
              <div class="col-12 col-md-2 mb-2 mb-md-3">
                <label for="mileage" class="form-label  text-dark fs-6 fs-md-6">
                  <i class="bi bi-speedometer2 me-2 text-secondary"></i> Mileage
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-warning bg-opacity-25 text-dark fw-bold rounded-start-3">M</span>
                  <input type="number" name="mileage" id="mileage" class="form-control rounded-end-3 shadow-sm" value="" readonly style="background-color: #f8f9fa; border: 1px solid #dee2e6;">
                </div>
               
              </div>
            </div>
           
<!--<div class="row g-2 g-md-3">-->
<!--  {{-- ✅ Fare (was Price) --}}-->
<!--  <div class="col-6 col-md-3 mb-2 mb-md-3">-->
<!--    <label class="form-label  fs-6 fs-md-6">Fare (£)</label>-->
<!--    <input type="number" name="fare" id="fare"-->
<!--           class="form-control rounded-3 shadow-sm"-->
<!--           value="{{ old('fare', $booking->price ?? '') }}" step="0.01">-->
<!--  </div>-->
<!--  {{-- ✅ Location Charges (Pickup Fee + Dropoff Fee combined, readonly) --}}-->
<!--  <div class="col-6 col-md-3 mb-2 mb-md-3">-->
<!--    <label class="form-label  fs-6 fs-md-6">Parking (£)</label>-->
<!--    <input type="number" name="parking" id="parking"-->
<!--           class="form-control rounded-3 shadow-sm bg-light "-->
<!--           value="0" step="0.01">-->
<!--  </div>-->
<!--  <div class="col-6 col-md-3 mb-2 mb-md-3">-->
<!--    <label class="form-label  fs-6 fs-md-6">Extra (£)</label>-->
<!--    <input type="number" name="extra" id="extra"-->
<!--           class="form-control rounded-3 shadow-sm" value="0" min="0" step="0.01">-->
<!--  </div>-->
<!--  <div class="col-6 col-md-3 mb-2 mb-md-3">-->
<!--    <label class="form-label  fs-6 fs-md-6">Waiting Fee (£)</label>-->
<!--    <input type="number" name="waiting_fee" id="waiting_fee"-->
<!--           class="form-control rounded-3 shadow-sm" value="0" min="0" step="0.01">-->
<!--  </div>-->
<!--</div>-->
<!-- ✅ Job Comment Field -->
<div class="mb-3">
    <label class="form-label fs-6 fs-md-6">Job Comment (Optional)</label>
    <textarea name="job_comment" class="form-control rounded-3 shadow-sm" id="job_comment"
              rows="3" placeholder="Enter any special instructions or notes...">{{ old('job_comment', $booking['job_comment'] ?? '') }}</textarea>
</div>


           
            <button type="submit" class="btn w-100 rounded-3 py-2 fw-bold text-white mb-2 mb-md-0" style="background-color:#B87333;">
              <i class="bi bi-save me-1"></i> {{ isset($booking) ? 'Update Booking' : 'Create Booking' }}
            </button>
          
        </div>
        {{-- ✅ Map Section --}}
        <div class="col-12 col-lg-5">
          <div class="card border-0 shadow rounded-4 h-50">
            <div class="card-header rounded-top-4 px-2 py-1 px-md-3" style="background-color: #202c3f; color: white;">
              Map View
            </div>
            <div class="card-body p-0 rounded-bottom-4">
              <div id="routeMap" style="height: 400px; min-height: 250px; border-radius: 0 0 1rem 1rem;"></div>
              <hr class="my-2" style="margin-top: 1.5rem !important;">
              {{-- ✅ Child Seat Checkbox --}}
            <input type="hidden" name="child_seat" value="0">

<div class="form-check mb-md-4 mt-4">
    <input
        class="form-check-input"
        type="checkbox"
        value="1"
        id="child_seat"
        name="child_seat"
        {{ old('child_seat', $booking['child_seat'] ?? 0) == 1 ? 'checked' : '' }}
    >
    <label class="form-check-label fw-semibold fs-6 fs-md-6" for="child_seat">
        Add Child Seat (+£5)
    </label>
</div>
            {{-- Account Selection (Hidden by default) --}}
        <div class="col-12 col-md-9 mb-2 mb-md-3" id="account-select-container" style="display: none;">
            <label class="form-label fs-6 fs-md-6">Select Account</label>
            <select name="account_id" id="account_id" class="form-select rounded-3 shadow-sm">
                <option value="">-- Select Account --</option>
                @if(isset($accounts) && count($accounts) > 0)
                    @foreach($accounts as $account)
                        <option value="{{ $account['id'] }}" 
                                data-name="{{ $account['business_name'] }}" 
                                data-phone="{{ $account['phone'] }}" 
                                data-email="{{ $account['email'] }}">
                            {{ $account['business_name'] }} - {{ $account['phone'] }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
              <div id="flight-details" class="p-3 bg-light rounded-3 shadow-sm mt-3" style="display:none;">
  <h6 class="fw-bold mb-3">Flight Details</h6>

  <div id="flight-info">
    <p><strong>Airline:</strong> <span id="airline">-</span></p>
    <p><strong>Flight Status:</strong> <span id="status">-</span></p>

    <hr>
    <h6 class="text-primary">Departure</h6>
    <p><strong>Airport:</strong> <span id="dep-airport">-</span></p>
    <p><strong>Terminal:</strong> <span id="dep-terminal">-</span></p>
    <p><strong>Gate:</strong> <span id="dep-gate">-</span></p>
    <p><strong>Scheduled:</strong> <span id="dep-scheduled">-</span></p>
    <p><strong>Actual:</strong> <span id="dep-actual">-</span></p>

    <hr>
    <h6 class="text-success">Arrival</h6>
    <p><strong>Airport:</strong> <span id="arr-airport">-</span></p>
    <p><strong>Terminal:</strong> <span id="arr-terminal">-</span></p>
    <p><strong>Gate:</strong> <span id="arr-gate">-</span></p>
    <p><strong>Scheduled:</strong> <span id="arr-scheduled">-</span></p>
    <p><strong>Actual:</strong> <span id="arr-actual">-</span></p>
  </div>
</div>

            
            </div>
          </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!--Previous Bookings Modal -->
<div class="modal fade" id="previousBookingsModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Previous Bookings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
<tr>
    <th>Ref</th>
    <th>Date</th>
    <th>Pickup</th>
    <th>Via</th>   <!-- ✅ NEW -->
    <th>Dropoff</th>
    <th>Vehicle</th>
    <th>Price</th>
    <th>PaymentType</th>
    <th>Status</th>
</tr>
</thead>

                    <tbody id="previousBookingsBody">
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                Enter phone or email to load bookings
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="noDriverModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="
    width: 380px;
">
    <div class="modal-content shadow-lg border-0 rounded-4">

      <!-- Header -->
      <div class="modal-header border-0 pb-0">
        <div class="d-flex align-items-center gap-3">
          <div class="icon-circle bg-warning bg-opacity-10 text-warning">
            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
          </div>
          <h5 class="modal-title fw-semibold mb-0">
            Driver Required
          </h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Body -->
      <div class="modal-body pt-3 text-muted">
        <p class="mb-0">
          You can’t send an SMS without assigning a driver first.
          Please select a driver from the dashboard to continue.
        </p>
      </div>

      <!-- Footer -->
      <div class="modal-footer border-0 pt-0" style="flex-wrap: nowrap;">
        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
          Cancel
        </button>
        <button type="button" class="btn btn-primary px-4" id="goDashboardBtn">
          Go to Dashboard
        </button>
      </div>

    </div>
  </div>
</div>


<!-- SMS Preview Modal -->
<div class="modal fade" id="smsModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow">

      <div class="modal-header bg-dark text-white rounded-top-4">
        <h6 class="modal-title">SMS Preview</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
          <!-- Recipient Input -->
<div class="mb-3">
  <!--<label for="sms_recipient" class="form-label">Recipient Number (44)</label>-->
  <label for="sms_recipient" class="form-label">Recipient Number</label>
  <input type="text" id="sms_recipient" class="form-control" placeholder="+XXXXXXXXXX" required>
  <div id="sms_recipient_error" class="text-danger small mt-1" style="display:none;">Invalid number.</div>
</div>
        <textarea id="smsMessage" class="form-control" rows="6"></textarea>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-primary" id="btn-send-sms-modal">Send SMS</button>
      </div>

    </div>
  </div>
</div>

<!-- Loader Modal -->
<div class="modal" id="loadingModal" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4 border-0 shadow-sm rounded-4">
      <!--<div class="spinner-border text-warning mb-3" style="width: 3rem; height: 3rem;" role="status"></div>-->
      <img src="{{ asset('public/images/favicon.png')}}"
           alt="Loader Image"
           class="mb-3 rounded-circle border border-2 border-warning shadow-sm mx-auto d-block"
           style="width: 80px; height: 80px; object-fit: none;border: 2px solid #ffc107 !important;">
      <p class="text-secondary mb-0" id="loadingModalMessage">Please wait, this may take a few moments.</p>
    </div>
  </div>
</div>
<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">Make Payment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <label for="payment_amount" class="form-label">Amount (GBP)</label>
          <input type="number" id="payment_amount" class="form-control"
                 placeholder="Enter amount" min="1" step="0.01" required>
        </div>
      </div>

      <div class="modal-footer d-flex">

        <!-- SEND LINK ONLY (Email) -->
  <button type="button" id="btn-send-payment-link" class="btn btn-warning" style="font-size:14px">
    Send Link via Email
  </button>

  <!-- SEND LINK ONLY (SMS) -->
  <button type="button" id="btn-send-sms-link" class="btn btn-success"style="font-size:14px">
    Send Link via Phone
  </button>

        <!-- GO TO STRIPE CHECKOUT -->
        <button type="button" id="btn-proceed-payment" class="btn btn-primary"style="font-size:14px">
          Pay Now
        </button>

        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"style="font-size:14px">
          Cancel
        </button>
      </div>

    </div>
  </div>
</div>



<!-- Login Modal -->
<div class="modal fade" id="smsLoginModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow">

      <div class="modal-header bg-primary text-white rounded-top-4">
        <h5 class="modal-title">MySMS API Login</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <div class="mb-3">
          <label class="form-label">API Key</label>
          <input type="text" id="sms_api_key" class="form-control" placeholder="Enter API key">
        </div>

        <div class="mb-3">
          <label class="form-label">MSISDN</label>
          <input type="text" id="sms_msisdn" class="form-control" placeholder="00447xxxxxx">
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" id="sms_password" class="form-control" placeholder="••••••">
        </div>

        <div id="smsLoginMsg"></div>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-primary" id="btnLoginSMS">Login</button>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="recurringModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create Recurring Booking</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label>From Date</label>
        <input type="date" id="recurring_from" class="form-control">
        <label>To Date</label>
        <input type="date" id="recurring_to" class="form-control mt-2">
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" id="btn-create-recurring">Create</button>
      </div>
    </div>
  </div>
</div>
<script>
// One shared, transition-free loader instance is used by price lookup and
// booking submission. This prevents duplicate/orphan Bootstrap backdrops.
window.getLoadingModal = function () {
  return bootstrap.Modal.getOrCreateInstance(
    document.getElementById('loadingModal'),
    { backdrop: 'static', keyboard: false }
  );
};

window.showLoadingModal = function (message) {
  const messageElement = document.getElementById('loadingModalMessage');
  if (messageElement && message) messageElement.textContent = message;
  window.getLoadingModal().show();
};

window.hideLoadingModal = function () {
  window.getLoadingModal().hide();
};

    
    document.addEventListener('DOMContentLoaded', function () {

  const form = document.getElementById('booking-form');
  const submitBtn = form.querySelector('button[type="submit"]');

  form.addEventListener('submit', function () {
    // 👇 double submit protection
    submitBtn.disabled = true;

    // 👇 show loader
    window.showLoadingModal('Creating booking, please wait...');
  });

});
document.getElementById('btn-previous-bookings').addEventListener('click', function () {

    const phone = document.querySelector('input[name="phone_no"]').value.trim();
    const email = document.querySelector('input[name="email"]').value.trim();
    const tbody = document.getElementById('previousBookingsBody');

    if (!phone && !email) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-danger py-4">
                    Please enter phone or email first
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = `
        <tr>
            <td colspan="8" class="text-center py-4">Loading...</td>
        </tr>
    `;

    fetch(`{{ route('bookings.previous') }}?phone=${encodeURIComponent(phone)}&email=${encodeURIComponent(email)}`)
        .then(res => res.json())
        .then(bookings => {

            tbody.innerHTML = '';

            if (!bookings.length) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            No previous bookings found
                        </td>
                    </tr>
                `;
                return;
            }

            const statusStyles = {
                pending:   'bg-warning text-dark',
                upcoming: 'bg-primary',
                completed:'bg-success',
                cancelled:'bg-danger',
                onroute:  'bg-info text-dark',
                arrived:  'bg-secondary',
            };

            bookings.forEach(b => {

                const status = (b.status || '').toLowerCase();
                const badgeClass = statusStyles[status] || 'bg-dark';

                const viaText = (b.vias && b.vias.length)
                    ? b.vias.map(v => v.address).join(' → ')
                    : '-';

                const tr = document.createElement('tr');
                tr.style.cursor = 'pointer';

                tr.innerHTML = `
                    <td><strong>${b.ref_no}</strong></td>
                    <td>${b.pickup_date} ${b.pickup_time}</td>
                    <td>${b.pickup_address}</td>
                    <td>${viaText}</td>
                    <td>${b.dropoff_address}</td>
                    <td>${b.vehicle_make}</td>
                    <td>£${parseFloat(b.price).toFixed(2)}</td>
                    <td>${b.payment_type}</td>
                    <td>
                        <span class="badge ${badgeClass} px-3 py-2 text-uppercase" style="font-size: 0.7rem">
                            ${b.status}
                        </span>
                    </td>
                `;

                // ✅ CLICK → FILL FORM
                tr.addEventListener('click', () => fillBookingForm(b));

                tbody.appendChild(tr);
            });
        });
});
</script>
<script>
function fillBookingForm(b) {

    console.log(b);

    // Basic fields
    document.querySelector('[name="passenger_name"]').value = b.passenger_name || '';
    document.querySelector('[name="email"]').value = b.email || '';
    document.querySelector('[name="phone_no"]').value = b.phone_no || '';

    document.getElementById('pickup_address').value = b.pickup_address || '';
    document.getElementById('dropoff_address').value = b.dropoff_address || '';
    
    setTimeout(() => {
    recalcFeesAndTotal();
}, 300);

    // Payment type
    document.getElementById('payment_type').value = b.payment_type || 'cash';

    // Vehicle (ID-based ✅)
    document.getElementById('vehicle_id').value = b.vehicle_id || 1;
    
    document.getElementById('flight_no').value = b.flight_no;
    
    document.getElementById('job_comment').value = b.job_comment;

    // Child seat
    document.getElementById('child_seat').checked = b.child_seat == 1;

    // Clear vias
    const viaContainer = document.getElementById('via-container');
    viaContainer.innerHTML = '';

    // Restore vias
    if (Array.isArray(b.vias) && b.vias.length) {
        b.vias.forEach(via => {

            const div = document.createElement('div');
            div.classList.add('via-field');

            div.innerHTML = `
                <div class="position-relative">
                    <input type="text"
                           name="via_addresses[]"
                           class="form-control"
                           value="${via.address ?? ''}"
                           autocomplete="off">
                    <button type="button" class="remove-via">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            `;

            div.querySelector('.remove-via').onclick = () => div.remove();
            viaContainer.appendChild(div);
        });
    }
    
    // window.calculateTotal();

    // Close modal
    bootstrap.Modal.getInstance(
        document.getElementById('previousBookingsModal')
    ).hide();
}
</script>



<script>
let debounceTimer;

const nameInput  = document.getElementById('passenger_name');
const phoneInput = document.querySelector('input[name="phone_no"]');
const emailInput = document.querySelector('input[name="email"]');
const dropdown   = document.getElementById('customerDropdown');

nameInput.addEventListener('keyup', function () {
    const query = this.value.trim();
    clearTimeout(debounceTimer);

    if (query.length < 2) {
        dropdown.style.display = 'none';
        return;
    }

    debounceTimer = setTimeout(() => {
        fetch(`{{ route('customers.search') }}?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(customers => {
                dropdown.innerHTML = '';

                if (!customers.length) {
                    dropdown.style.display = 'none';
                    return;
                }

                customers.forEach(c => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action';
                    item.innerHTML = `
                        <strong>${c.business_name}</strong><br>
                        <small>${c.phone} • ${c.email}</small>
                    `;

                    item.onclick = () => {
                        nameInput.value  = c.business_name;
                        phoneInput.value = c.phone;
                        emailInput.value = c.email;
                        dropdown.style.display = 'none';
                    };

                    dropdown.appendChild(item);
                });

                dropdown.style.display = 'block';
            });
    }, 300);
});

// Hide dropdown on outside click
document.addEventListener('click', function (e) {
    if (!e.target.closest('#customerDropdown') && e.target !== nameInput) {
        dropdown.style.display = 'none';
    }
});
</script>

 <script>
// flatpickr("#pickup_time", {
//     enableTime: true,
//     noCalendar: true,
//     dateFormat: "H:i",
//     time_24hr: true,
//     defaultDate: "{{ old('pickup_time', $pickupTime) }}",
//     minuteIncrement: 15,  // increments for dropdown
// });
// </script>

@endsection




<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentTypeSelect = document.getElementById('payment_type');
    const accountSelectContainer = document.getElementById('account-select-container');
    const accountSelect = document.getElementById('account_id');
    const passengerNameInput = document.querySelector('input[name="passenger_name"]');
    const phoneNoInput = document.querySelector('input[name="phone_no"]');
    const emailInput = document.querySelector('input[name="email"]');
    const accountInfoNote = document.getElementById('account-info-note');

    // Toggle account select visibility
    paymentTypeSelect.addEventListener('change', function() {
        if (this.value === 'account') {
            accountSelectContainer.style.display = 'block';
            accountInfoNote.style.display = 'block';
            // Clear fields if switching to account
            if (passengerNameInput && phoneNoInput && emailInput) {
                passengerNameInput.value = '';
                phoneNoInput.value = '';
                emailInput.value = '';
            }
        } else {
            accountSelectContainer.style.display = 'none';
            accountInfoNote.style.display = 'none';
            // Clear account select
            if (accountSelect) {
                accountSelect.value = 'hello';
            }
        }
    });

    // Populate fields on account selection
    if (accountSelect) {
        accountSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                // if (passengerNameInput) passengerNameInput.value = selectedOption.dataset.name || '';
                if (phoneNoInput) phoneNoInput.value = selectedOption.dataset.phone || '';
                if (emailInput) emailInput.value = selectedOption.dataset.email || '';
            }
        });
    }

    // Initial state check
    if (paymentTypeSelect.value === 'account') {
        accountSelectContainer.style.display = 'block';
        accountInfoNote.style.display = 'block';
        // If editing and account was selected, populate
        const savedAccountId = '{{ old("account_id", $booking["account_id"] ?? "") }}';
        if (savedAccountId && accountSelect) {
            accountSelect.value = savedAccountId;
            // Trigger change to populate
            accountSelect.dispatchEvent(new Event('change'));
        }
    }
});
</script>
<script type="module">
    

    
    document.addEventListener("DOMContentLoaded", function () {
    const vehicleSelect = document.querySelector("select[name='vehicle_id']");
    const vehicleMakeInput = document.getElementById("vehicle_make");

    vehicleSelect.addEventListener("change", function () {
        const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
        vehicleMakeInput.value = selectedOption.dataset.make || "";
    });

    // Auto-fill at load if already selected
    const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
    if (selectedOption) {
        vehicleMakeInput.value = selectedOption.dataset.make || "";
    }
});
import { initializeApp } from "https://www.gstatic.com/firebasejs/11.0.1/firebase-app.js";
import { getDatabase, ref, get } from "https://www.gstatic.com/firebasejs/11.0.1/firebase-database.js";

const firebaseConfig = {
    apiKey: "AIzaSyDf9Ujb0fAaER",
    authDomain: "crown-carz-default-rtdb.firebaseapp.com",
    databaseURL: "https://crown-carz-default-rtdb.firebaseio.com",
    projectId: "crown-carz",
};

const app = initializeApp(firebaseConfig);
const db  = getDatabase(app);

// ------------------------------------
// READ ALL FORM DATA FOR SMS TEMPLATE
// ------------------------------------
function getFormData() {

  // Get selected vehicle text (Saloon, Estate, etc.)
  const vehicleSelect = document.querySelector("select[name='vehicle_id']");
  const vehicle_type =
    vehicleSelect && vehicleSelect.selectedOptions.length
      ? vehicleSelect.selectedOptions[0].dataset.make
      : "";
      
   const pickupDateValue = document.querySelector("input[name='pickup_date']").value; // e.g., "2025-11-16"
   
   console.log(pickupDateValue);
   

let formattedDate = '';
// if (pickupDateValue) {
//     const parts = pickupDateValue.split('-'); // ["2025", "11", "16"]
//     formattedDate = `${parts[2]}/${parts[1]}/${parts[0]}`; // "16/11/2025"
// }
if (pickupDateValue) {
    const parts = pickupDateValue.split('-'); // ["2025", "11", "16"]

    const months = [
        "Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
    ];

    const year = parts[0];
    const month = months[parseInt(parts[1]) - 1]; 
    const day = parts[2];

    formattedDate = `${day}/${month}/${year}`;
}

console.log(formattedDate); // dd/mm/yyyy 



  return {
    caller: document.querySelector("input[name='passenger_name']").value,
    mobile: document.querySelector("input[name='phone_no']").value,
    email: document.querySelector("input[name='email']").value,
    
    job_ref: document.querySelector("input[name='ref_no']").value,
    pickup: document.querySelector("#pickup_address").value,
    dropoff: document.querySelector("#dropoff_address").value,

    job_date: formattedDate,
    job_time: document.querySelector("input[name='pickup_time']").value,

    flight_no: document.querySelector("input[name='flight_no']").value,
    via_address: getViaAddresses(),

    payment: document.querySelector("select[name='payment_type']").value,

    fare: Math.ceil(Number(document.querySelector("input[name='price']").value || 0)),
base_price: Math.ceil(Number(document.querySelector("input[name='fare']").value || 0)),

    
    mcar_park: document.querySelector("input[name='parking']").value,
    comments: document.querySelector("textarea[name='comments']")?.value || "",
    
    Job_comments: document.querySelector("textarea[name='job_comment']")?.value || "",

    vehicle_type: vehicle_type,
    
    // NEW
        driver_name: document.getElementById("currentDriverName")?.value || "",
        driver_phone: document.getElementById("currentDriverPhone")?.value || ""
  };
}

// VIA HANDLER
function getViaAddresses() {
  let vias = document.querySelectorAll("input[name='via_addresses[]']");
  if (!vias.length) return "";
  return Array.from(vias).map(v => v.value).join(", ");
}

// ---------------------------
// PLACEHOLDER REPLACER
// Supports: {key} and {$key}
// ---------------------------
function replacePlaceholders(template, data) {
  return template.replace(/\{\$?(\w+)\}/g, (match, key) => data[key] || "");
}

// -------------------
// RECIPIENT VALIDATION
function getRecipientNumber() {
    const input = document.getElementById("sms_recipient");
    const number = input.value.trim();
    const errorEl = document.getElementById("sms_recipient_error");

    // if (!/^44\d{9,12}$/.test(number)) {
    //     errorEl.style.display = "block";
    //     return null;
    // } else {
    //     errorEl.style.display = "none";
    //     return number;
    // }
    if (!/^\d{9,12}$/.test(number)) {
    errorEl.style.display = "block";
    return null;
} else {
    errorEl.style.display = "none";
    return number;
}

}


// ---------------------------
// SHOW SMS PREVIEW MODAL
// ---------------------------
// function showModal(message) {
//   document.querySelector("#smsMessage").value = message;
//   new bootstrap.Modal(document.getElementById("smsModal")).show();
 
// }

function showModal(message, phone_no) {
  // set sms message
  document.querySelector("#smsMessage").value = message;
  
  const phoneNoInput = document.querySelector('input[name="phone_no"]').value || "";
  
  console.log(phoneNoInput);

  // auto set recipient number
  document.querySelector("#sms_recipient").value = phoneNoInput;

  // hide previous error (optional)
  document.querySelector("#sms_recipient_error").style.display = "none";

  // show modal
  new bootstrap.Modal(document.getElementById("smsModal")).show();
}



// -------------------
// SEND SMS VIA mySMS
// -------------------
// async function sendSMS(mobile, message) {
//     try {
//         const authToken = import.meta.env.MY_SMS_AUTH_TOKEN; // Ensure env variable

//         const response = await fetch("https://api.mysms.com/json/remote/sms/send", {
//             method: "POST",
//             headers: { "Content-Type": "application/json" },
//             body: JSON.stringify({
//                 authToken: authToken,
//                 recipients: [mobile],
//                 message: message,
//                 store: true
//             }),
//         });

//         const result = await response.json();
//         console.log("SMS Result:", result);

//         const code = result?.statusCode || result?.code;
//         const messages = {
//             2: "A required parameter was not given",
//             97: "Access to API was denied",
//             98: "Too many requests in a short time",
//             99: "Service is currently not available",
//             100: "Auth token is invalid",
//             600: "API key is invalid",
//             700: "Recipient number is invalid",
//             701: "Only allowed to send SMS to 50 recipients per request",
//             702: "One or more recipients are blocked"
//         };

//         if (code === 0 || result.success) {
//             alert("SMS Sent Successfully!");
//         } else {
//             alert(messages[code] || "Failed to send SMS. Code: " + code);
//         }

//     } catch (err) {
//         console.error("SMS Sending Failed:", err);
//         alert("Failed to send SMS");
//     }
// }

async function sendSMS(mobile, message) {
    try {
        const response = await fetch(@json(route('sms.send')), {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ mobile, message })
        });

        const result = await response.json();
        console.log("SMS Result:", result);

        // Extract errorCode safely
        const errorCode = result?.details?.errorCode ?? result?.errorCode ?? null;

        console.log("Error Code:", errorCode);

        if (response.ok && (result?.success === true || errorCode === 0)) {
            alert("✅ SMS Sent Successfully!");
            return;
        }

        // Otherwise show message
        const errorMessage = result?.message || "Failed to send SMS.";
        alert(`❌ ${errorMessage} (Code: ${errorCode})`);

    } catch (err) {
        console.error("SMS Sending Failed:", err);
        alert("Failed to send SMS");
    }
}


// ---------------------------
// LOAD TEMPLATE FROM FIREBASE
// ---------------------------
async function loadSMS(templateName) {
  const snap = await get(ref(db, "sms_templates/" + templateName));
  if (!snap.exists()) return alert("Template not found!");

  const template = snap.val().message;
  const data = getFormData();

  const finalText = replacePlaceholders(template, data);

  showModal(finalText);
}

// ----------------------------------
// BUTTON EVENT HANDLERS
// ----------------------------------
    // ----------------------------------
// BUTTON EVENT HANDLERS
// ----------------------------------

document.getElementById("btn-driver-sms").addEventListener("click", async () => {

    const bookingId = document.getElementById("currentBookingId")?.value;
    const driverId  = document.getElementById("currentDriverId")?.value;

    if (!bookingId) {
        console.error("Booking ID not found");
        return;
    }

    if (!driverId) {
        const modal = new bootstrap.Modal(document.getElementById("noDriverModal"));
        modal.show();
        return;
    }

    // ✅ Fetch driver data FIRST, then load SMS
    try {
        const driverSnap = await get(ref(db, "drivers/" + driverId));

        if (driverSnap.exists()) {
            const driver = driverSnap.val();

            // ✅ Set hidden fields so getFormData() picks them up
            document.getElementById("currentDriverName").value = driver.name  || "";
            document.getElementById("currentDriverPhone").value = driver.phone || "";
        }
    } catch (e) {
        console.error("Failed to fetch driver:", e);
    }

    // ✅ Now load SMS (driver_name will be resolved correctly)
    const snap = await get(ref(db, "sms_templates/driver_details"));
    if (!snap.exists()) return alert("Template not found!");

    const template = snap.val().message;
    const data = getFormData();
    const finalText = replacePlaceholders(template, data);

    // ✅ Show modal with DRIVER phone, not passenger phone
    const driverPhone = document.getElementById("currentDriverPhone").value || "";
    document.querySelector("#smsMessage").value = finalText;
    document.querySelector("#sms_recipient").value = driverPhone;
    document.querySelector("#sms_recipient_error").style.display = "none";

    new bootstrap.Modal(document.getElementById("smsModal")).show();
});

// redirect on OK
document.getElementById("goDashboardBtn").addEventListener("click", () => {
    //window.location.href = "/dashboard";
    window.location.href = "/admin/dashboard";
});
document.getElementById("btn-confirm-sms").addEventListener("click", () => loadSMS("booking_confirmation"));
document.getElementById("btn-onroute-sms").addEventListener("click", () => loadSMS("onroute"));
document.getElementById("btn-arrived-sms").addEventListener("click", () => loadSMS("arrival"));
document.getElementById("btn-complete-sms").addEventListener("click", () => loadSMS("complete"));

// Send directly from modal preview
document.getElementById("btn-send-sms-modal").addEventListener("click", () => {
    const message = document.querySelector("#smsMessage").value;
    const recipient = getRecipientNumber();
    if (!recipient) return;
    sendSMS(recipient, message);
});


document.getElementById("btnLoginSMS").addEventListener("click", async () => {

    let apiKey = document.getElementById("sms_api_key").value.trim();
    let msisdn = document.getElementById("sms_msisdn").value.trim();
    let password = document.getElementById("sms_password").value.trim();

    if (!apiKey || !msisdn || !password) {
        return showSMSMsg("All fields are required!", "danger");
    }

    //showSMSMsg("Logging in...", "info");

    try {
        const res = await fetch("https://crowncarz.com/admin/sms/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                apiKey, msisdn, password
            })
        });
        
        
         const data = await res.json(); // parse JSON once

    console.log(data); // debug server response

        if (data.status === "success") {
        alert("Login successful. Token saved!", "success");
    } else if (data.status === "error" && data.details?.code) {
        // Map the error code to a friendly message
        const errorMessages = {
            97: "Access denied to API",
            98: "Too many requests, please try later",
            99: "Service is currently unavailable",
            101: "Wrong credentials",
            107: "User does not exist",
            108: "Wrong API key",
            109: "Login blocked temporarily due to wrong attempts",
            600: "API key is invalid"
        };
        const msg = errorMessages[data.details.code] || "Login failed";
        alert(msg, "danger");
    } else {
        alert("Login failed!", "danger");
    }

    } catch (err) {
        alert("Error: " + err.message, "danger");
    }
});

document.addEventListener("DOMContentLoaded", () => {

    // -----------------------------
    // Helper: get current booking ID
    // -----------------------------
    function getCurrentBookingId() {
        return document.getElementById("currentBookingId")?.value || null;
    }

//     // -----------------------------
//     // 1. Confirmation Email
//     // -----------------------------
//     document.getElementById("btn-confirmation-email").addEventListener("click", async () => {
//     const bookingId = getCurrentBookingId();
//     if (!bookingId) return alert("Booking ID not found.");

//     // ⬇️ Get ALL form values automatically
//     const form = document.getElementById("booking-form");
//     const formData = new FormData(form);

//     // Convert FormData → JSON
//     const bookingData = {}; // Renamed 'data' to 'bookingData' here
//     formData.forEach((value, key) => {
//         if (bookingData[key]) {
//             // If field exists already, convert to array (handles via[])
//             if (!Array.isArray(bookingData[key])) bookingData[key] = [bookingData[key]];
//             bookingData[key].push(value);
//         } else {
//             bookingData[key] = value;
//         }
//     });

//     try {
//         const res = await fetch(`https://crowncarz.com/admin/booking/${bookingId}/send-confirmation-email`, {
//             method: "POST",
//             headers: {
//                 "Content-Type": "application/json",
//                 "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
//             },
//             body: JSON.stringify(bookingData) // Use the renamed variable
//         });

//         // The fix is here: renamed 'data' to 'responseData'
//         const responseData = await res.json();
        
//         alert(responseData.message || "Confirmation email sent!");
//     } catch (err) {
//         console.error(err);
//         alert("Failed to send confirmation email.");
//     }
// });

document.getElementById("btn-confirmation-email").addEventListener("click", async () => {
    const bookingId = getCurrentBookingId();
    if (!bookingId) return alert("Booking ID not found.");
    
    // ⬇️ Get ALL form values automatically
    const form = document.getElementById("booking-form");
    const formData = new FormData(form);
    
    console.log(FormData);
    
    // Convert FormData → JSON
    const bookingData = {};
    formData.forEach((value, key) => {
        if (bookingData[key]) {
            // If field exists already, convert to array (handles via[])
            if (!Array.isArray(bookingData[key])) bookingData[key] = [bookingData[key]];
            bookingData[key].push(value);
        } else {
            bookingData[key] = value;
        }
    });
    
    try {
        const res = await fetch(`https://crowncarz.com/admin/booking/${bookingId}/send-confirmation-email`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(bookingData)
        });
        
        if (!res.ok) {
            // Log the raw error response for debugging (HTML or plain text)
            const errorText = await res.text();
            console.error('Server Error Response:', errorText);
            alert(`Server error (${res.status}): Check console for details.`);
            return;
        }
        
        const responseData = await res.json();
        alert(responseData.message || "Confirmation email sent!");
    } catch (err) {
        console.error(err);
        alert("Failed to send confirmation email.");
    }
});


    // -----------------------------
    // 2. Re-Occurring Job
    // -----------------------------
    // document.getElementById("btn-recurring-job").addEventListener("click", async () => {
    //     const bookingId = getCurrentBookingId();
    //     if (!bookingId) return alert("Booking ID not found.");

    //     try {
    //         const res = await fetch(`https://crowncarz.com/admin/booking/${bookingId}/recurring`, {
    //             method: "POST",
    //             headers: {
    //                 "Content-Type": "application/json",
    //                 "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
    //             }
    //         });
    //         const data = await res.json();
    //         alert(data.message || "Recurring job created!");
    //     } catch (err) {
    //         console.error(err);
    //         alert("Failed to create recurring job.");
    //     }
    // });
//     document.getElementById("btn-recurring-job").addEventListener("click", () => {
//     new bootstrap.Modal(document.getElementById("recurringModal")).show();
// });

// document.getElementById("btn-create-recurring").addEventListener("click", async () => {
//     const bookingId = getCurrentBookingId();
//     const fromDate = document.getElementById("recurring_from").value;
//     const toDate = document.getElementById("recurring_to").value;
//     if (!fromDate || !toDate) return alert("Please select dates.");
    
//     const form = document.getElementById("booking-form");
//     const formData = new FormData(form);
//     const formValues = {};
//     formData.forEach((value, key) => formValues[key] = value);
//     formValues.from_date = fromDate;
//     formValues.to_date = toDate;
    
//     try {
//         const res = await fetch(`https://crowncarz.com/admin/bookings/${bookingId}/recurring`, {
//             method: 'POST',
//             headers: {
//                 "Content-Type": "application/json",
//                 "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
//             },
//             body: JSON.stringify(formValues)
//         });
        
        
//         console.log(res);
        
//         if (!res.ok) {
//             // Handle non-2xx responses (e.g., 405, 404)
//             const errorText = await res.text(); // Get raw text instead of assuming JSON
//             console.error('Request failed:', res.status, errorText);
//             return alert(`Error ${res.status}: ${res.statusText}`);
//         }
        
//         const data = await res.json();
//         alert(data.message || "Recurring bookings created!");
//         bootstrap.Modal.getInstance(document.getElementById("recurringModal")).hide();
//     } catch (error) {
//         console.error('Fetch error:', error);
//         alert("Something went wrong—check console.");
//     }
// });



    // -----------------------------
    // 3. Return Job
    // -----------------------------
    document.getElementById("btn-return-job").addEventListener("click", async () => {
    const bookingId = getCurrentBookingId();
    if (!bookingId) return alert("Booking ID not found.");

    // Get all form values
    const form = document.querySelector("#booking-form"); // Replace with your form ID
    const formData = new FormData(form);
    const jsonData = Object.fromEntries(formData.entries());

    try {
        const res = await fetch(`https://crowncarz.com/admin/bookings/${bookingId}/return-job`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(jsonData)
        });

        // const data = await res.json();
        alert("Return job created!");
    } catch (err) {
        console.error(err);
        alert("Failed to create return job.");
    }
});

   document.getElementById("btn-recurring-job").addEventListener("click", () => {
    new bootstrap.Modal(document.getElementById("recurringModal")).show();
});

document.getElementById("btn-create-recurring").addEventListener("click", async () => {

    const bookingId = getCurrentBookingId();

    // Get recurring date range
    const fromDate = document.getElementById("recurring_from").value;
    const toDate = document.getElementById("recurring_to").value;

    if (!fromDate || !toDate) return alert("Please select dates.");

    // ⬇️ Get ALL fields from form automatically
    const form = document.getElementById("booking-form");
    const formData = new FormData(form);

    const formValues = {};
    formData.forEach((value, key) => formValues[key] = value);

    // Add recurring dates
    formValues.from_date = fromDate;
    formValues.to_date   = toDate;
    
    
    console.log("sachal");
    console.log(formValues.from_date);
    console.log(formValues.to_date);

    const res = await fetch(`https://crowncarz.com/admin/booking/${bookingId}/recurring`, {
    method: 'POST',
    headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify(formValues)
});

console.log("Status:", res.status);

const data = await res.json();
    
    console.log(res);
    alert(data.message || "Recurring bookings created!");

    bootstrap.Modal.getInstance(document.getElementById("recurringModal")).hide();
});



//     // -----------------------------
//     // 3. Return Job
//     // -----------------------------
//     document.getElementById("btn-return-job").addEventListener("click", async () => {
//     const bookingId = getCurrentBookingId();
//     if (!bookingId) return alert("Booking ID not found.");

//     // Get all form values
//     const form = document.querySelector("#booking-form"); // Replace with your form ID
//     const formData = new FormData(form);
//     const jsonData = Object.fromEntries(formData.entries());

//     try {
//         const res = await fetch(`https://crowncarz.com/admin/booking/${bookingId}/return-job`, {
//             method: "POST",
//             headers: {
//                 "Content-Type": "application/json",
//                 "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
//             },
//             body: JSON.stringify(jsonData)
//         });

//         const data = await res.json();
//         alert(data.message || "Return job created!");
//     } catch (err) {
//         console.error(err);
//         alert("Failed to create return job.");
//     }
// });


    // -----------------------------
    // 4. Send Receipt Email
    // -----------------------------
    document.getElementById("btn-send-receipt").addEventListener("click", async () => {

    const bookingId = getCurrentBookingId();
    if (!bookingId) return alert("Booking ID not found.");

    // ✅ Get all form fields
    const form = document.getElementById("booking-form");
    const formData = new FormData(form);

    // Convert FormData → JSON
    let data = {};
    formData.forEach((value, key) => {
        if (data[key]) {
            // If field exists already, convert to array (handles via[])
            if (!Array.isArray(data[key])) data[key] = [data[key]];
            data[key].push(value);
        } else {
            data[key] = value;
        }
    });

    try {
        const res = await fetch(`https://crowncarz.com/admin/booking/${bookingId}/send-receipt-email`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)  // ✅ Send full form data
        });

        const response = await res.json();
        alert(response.message || "Receipt email sent!");

    } catch (err) {
        console.error(err);
        alert("Failed to send receipt email.");
    }
});

    
    
    

    // -----------------------------
    // 5. Payment
    // -----------------------------
    // JS Module
document.getElementById("btn-payment").addEventListener("click", () => {
    // Open modal
    new bootstrap.Modal(document.getElementById("paymentModal")).show();
});



// Proceed payment
// document.getElementById("btn-proceed-payment").addEventListener("click", async () => {
//     const bookingId = getCurrentBookingId();
//     if (!bookingId) return alert("Booking ID not found.");

//     const amount = parseFloat(document.getElementById("payment_amount").value);
//     if (!amount || amount <= 0) return alert("Enter a valid amount.");

//     try {
//         // Call backend to create Stripe session
//         const res = await fetch(`https://crowncarz.com/admin/booking/${bookingId}/payment`, {
//     method: "POST",
//     headers: {
//         "Content-Type": "application/json",
//         "Accept": "application/json",  // ← ADD THIS
//         "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
//     },
//     body: JSON.stringify({ amount })
// });
        
//         if (!res.ok) {
//     const errorText = await res.text(); // Get raw response
//     console.error('Server error:', res.status, errorText);
//     alert(`Error ${res.status}: ${errorText.includes('<!DOCTYPE') ? 'Server crash - check logs' : 'Unknown'}`);
//     return;
// }
// const data = await res.json();

//         // const data = await res.json();

//         if (data.status === "success" && data.payment_url) {
//             // Redirect to Stripe checkout
//             window.location.href = data.payment_url;
//         } else {
//             alert("Payment initialization failed.");
//         }

//     } catch (err) {
//         console.error(err);
//         alert("Error creating payment session.");
//     }
// });
// Pay Now → sendOnlyLink = false
document.getElementById("btn-proceed-payment").addEventListener("click", () => {
    makePayment(false);
});

// Send Payment Link → sendOnlyLink = true
document.getElementById("btn-send-payment-link").addEventListener("click", () => {
    makePayment(true);
});

document.getElementById("btn-send-sms-link").addEventListener("click", () => {
    makePayment("sms");
});

// let modal = bootstrap.Modal.getInstance(document.getElementById("paymentModal"));


async function makePayment(mode = false) {
    
    
    // mode:
    // false = pay now
    // true = email only
    // "sms" = send sms onl

    const bookingId = getCurrentBookingId();
    if (!bookingId) return alert("Booking ID not found.");

    const amount = parseFloat(document.getElementById("payment_amount").value);
    if (!amount || amount <= 0) return alert("Enter a valid amount.");

    try {
        // Call backend
        const res = await fetch(`https://crowncarz.com/admin/booking/${bookingId}/payment`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                amount: amount,
                method: mode
            })
        });
        
        // Get modal instance safely
    const modalInstance = bootstrap.Modal.getInstance(document.getElementById("paymentModal"))
                        ?? new bootstrap.Modal(document.getElementById("paymentModal"));

        // Get raw response first
        const rawText = await res.text();

        if (!res.ok) {
            console.error("Server Error:", rawText);

            alert(
                "Server Error " + res.status + ": " +
                (rawText.includes("<!DOCTYPE") ? "Server returned HTML. Check logs." : rawText)
            );
            return;
        }

        let data;
        try {
            data = JSON.parse(rawText);
        } catch (e) {
            console.error("JSON Parse Error:", rawText);
            alert("Invalid JSON returned from server.");
            return;
        }

        if (data.status === "success") {

            // If SEND EMAIL ONLY
    if (mode === true) {
        alert("Payment link emailed successfully.");
        modalInstance.hide();
        return;
    }

    // If SEND SMS ONLY
    if (mode === "sms") {
        alert("Payment link sent to customer mobile number.");
        modalInstance.hide();
        return;
    }

            // If Pay Now → redirect to Stripe
            window.location.href = data.payment_url;
            return;
        }

        alert("Payment initialization failed.");

    } catch (error) {
        console.error(error);
        alert("Error creating payment session.");
    }
}

});


</script>





 <script type="module">
//   import { initializeApp } from "https://www.gstatic.com/firebasejs/10.11.0/firebase-app.js";
//   import { getDatabase, ref, get, child } from "https://www.gstatic.com/firebasejs/10.11.0/firebase-database.js";
//   // --- Firebase init (single) ---
//   const app = initializeApp({ databaseURL: "https://crown-carz-default-rtdb.firebaseio.com/" });
//   const db = getDatabase(app);
//   // --- Helpers ---
//   const $ = (sel) => document.querySelector(sel);
//   const $all = (sel) => Array.from(document.querySelectorAll(sel));
//   const normPC = (pc) => (pc || "").toString().replace(/\s+/g, "").toUpperCase();
//   // Cache all locations for quick postcode lookups
//   const firebaseLocations = { airports:{}, stations:{}, ports:{} };
//   async function preloadAllLocations() {
//     const cats = ["airports","stations","ports"];
//     for (const type of cats) {
//       try {
//         const snap = await get(child(ref(db), type));
//         firebaseLocations[type] = snap.exists() ? snap.val() : {};
//       } catch (e) { console.error("FB load error", type, e); }
//     }
//   }
//   function findByPostcode(type, postcode) {
//     const data = firebaseLocations[type] || {};
//     const list = Object.values(data);
//     return list.find(item => normPC(item.post_code) === normPC(postcode)) || null;
//   }
//   // Populate dropdown for a type; each option carries data attributes
//   async function loadOptions(type, dropdownId) {
//     const select = document.getElementById(dropdownId);
//     select.innerHTML = `<option value="">-- Select Location --</option>`;
//     // ensure cache is ready
//     if (!Object.keys(firebaseLocations[type] || {}).length) await preloadAllLocations();
//     const data = firebaseLocations[type] || {};
//     Object.keys(data).forEach(key => {
//       const item = data[key];
//       const opt = document.createElement('option');
//       opt.value = item.address || '';
//       opt.text = `${item.address} ${item.post_code || 'N/A'}`;
//       opt.dataset.postcode = item.post_code || '';
//       opt.dataset.pickupCharge = item.pickup_charge || 0;
//       opt.dataset.dropoffCharge = item.dropoff_charge || 0;
//       select.appendChild(opt);
//     });
//     select.classList.remove('d-none');
//   }
//   // Write combined fees into Parking field and recompute total
//   function recalcFeesAndTotal() {
//     const pickupType = $('input[name="pickup_type"]:checked')?.value;
//     const dropoffType = $('input[name="dropoff_type"]:checked')?.value;
//     const pickupPC = $('#pickup_postcode')?.value?.trim();
//     const dropoffPC = $('#dropoff_postcode')?.value?.trim();
//     let pickupFee = 0;
//     let dropoffFee = 0;
//     if (pickupType && pickupPC) {
//       const hit = findByPostcode(pickupType, pickupPC);
//       if (hit) pickupFee = parseFloat(hit.pickup_charge || 0);
//     }
//     if (dropoffType && dropoffPC) {
//       const hit = findByPostcode(dropoffType, dropoffPC);
//       if (hit) dropoffFee = parseFloat(hit.dropoff_charge || 0);
//     }
//     // Update Parking field with sum of fees
//     const parkingEl = document.getElementById('parking');
//     if (parkingEl) {
//       parkingEl.value = (pickupFee + dropoffFee).toFixed(2);
//     }
//     // recompute total (reuses your existing fields)
//     const fare = parseFloat($('#fare').value) || 0;
//     const parking = parseFloat($('#parking').value) || 0;
//     const waiting = parseFloat($('#waiting_fee').value) || 0;
//     const extra = parseFloat($('#extra').value) || 0;
//     const seat = $('#child_seat')?.checked ? 5 : 0;
//     $('#price').value = (fare + parking + waiting + extra + seat).toFixed(2);

//     console.log("DEBUG:", {
//       pickupType, pickupPC, pickupFee,
//       dropoffType, dropoffPC, dropoffFee,
//       sumFees: (pickupFee + dropoffFee).toFixed(2)
//     });
//   }
//   // Make callable from non-module scripts
//   window.applyFirebaseCharges = recalcFeesAndTotal;
//   // --- Wiring: radios → load dropdowns immediately ---
//   $all('input[name="pickup_type"]').forEach(r => r.addEventListener('change', (e)=>{
//     loadOptions(e.target.value, 'pickup_options');
//   }));
//   $all('input[name="dropoff_type"]').forEach(r => r.addEventListener('change', (e)=>{
//     loadOptions(e.target.value, 'dropoff_options');
//   }));
//   // initial show (Airports default)
//   loadOptions($('input[name="pickup_type"]:checked').value, 'pickup_options');
//   loadOptions($('input[name="dropoff_type"]:checked').value, 'dropoff_options');
//   // Dropdown selection → fill address **and** postcode, then recalc
//   $('#pickup_options').addEventListener('change', (e)=>{
//     const opt = e.target.selectedOptions[0];
//     if (!opt) return;
//     $('#pickup_address').value = `${opt.value} ${opt.dataset.postcode}`;
//     $('#pickup_postcode').value = opt.dataset.postcode || '';
//     recalcFeesAndTotal();
//     document.getElementById('pickup_address').dispatchEvent(new Event('blur')); // keep your mileage/price flow
//   });
//   $('#dropoff_options').addEventListener('change', (e)=>{
//     const opt = e.target.selectedOptions[0];
//     if (!opt) return;
//     $('#dropoff_address').value = `${opt.value} ${opt.dataset.postcode}`;
//     $('#dropoff_postcode').value = opt.dataset.postcode || '';
//     recalcFeesAndTotal();
//     document.getElementById('dropoff_address').dispatchEvent(new Event('blur'));
//   });
//   // When Taxibase fills the postcode, recompute fees
//   document.addEventListener('taxibase:select', () => recalcFeesAndTotal());
 
//   // Also try once after cache loads (edit mode)
//   preloadAllLocations().then(()=> setTimeout(recalcFeesAndTotal, 300));
// 



document.addEventListener('DOMContentLoaded', function() {
  const flightInput = document.querySelector('input[name="flight_no"]');
  const detailsDiv = document.getElementById('flight-details');

  // Add transition style
  detailsDiv.style.transition = 'opacity 0.5s ease';
  detailsDiv.style.opacity = 0;
  detailsDiv.style.display = 'none';

  flightInput.addEventListener('blur', async function() {
    const flightNo = this.value.trim();

    // ✅ If flight number is empty → clear and fade out
    if (!flightNo) {
      detailsDiv.style.opacity = 0;
      setTimeout(() => {
        detailsDiv.style.display = 'none';
        detailsDiv.innerHTML = ''; // clear content
      }, 500);
      return;
    }

    // Show loading
    detailsDiv.style.display = 'block';
    detailsDiv.style.opacity = 1;
    detailsDiv.innerHTML = `
      <div class="text-center p-2">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    `;

    try {
      const res = await fetch(`http://api.aviationstack.com/v1/flights?access_key=b314b241911f491f8e76da0c4a4e3cca&flight_iata=${flightNo}`);
      const data = await res.json();

      if (data.data && data.data.length > 0) {
        const flight = data.data[0];
        const dep = flight.departure || {};
        const arr = flight.arrival || {};
        const airline = flight.airline || {};
        const status = flight.flight_status || 'N/A';

        detailsDiv.innerHTML = `
          <h6 class="fw-bold mb-3">Flight Details</h6>
          <p><strong>Airline:</strong> ${airline.name ?? 'N/A'}</p>
          <p><strong>Flight Status:</strong> 
            <span class="${status === 'landed' ? 'text-success' : 'text-warning'}">
              ${status.toUpperCase()}
            </span>
          </p>

          <hr>
          <h6 class="text-primary">Departure</h6>
          <p><strong>Airport:</strong> ${dep.airport ?? 'N/A'} (${dep.iata ?? ''})</p>
          <p><strong>Terminal:</strong> ${dep.terminal ?? 'N/A'}</p>
          <p><strong>Gate:</strong> ${dep.gate ?? 'N/A'}</p>
          <p><strong>Scheduled:</strong> ${dep.scheduled ? new Date(dep.scheduled).toLocaleString() : 'N/A'}</p>
          <p><strong>Actual:</strong> ${dep.actual ? new Date(dep.actual).toLocaleString() : 'N/A'}</p>

          <hr>
          <h6 class="text-success">Arrival</h6>
          <p><strong>Airport:</strong> ${arr.airport ?? 'N/A'} (${arr.iata ?? ''})</p>
          <p><strong>Terminal:</strong> ${arr.terminal ?? 'N/A'}</p>
          <p><strong>Gate:</strong> ${arr.gate ?? 'N/A'}</p>
          <p><strong>Scheduled:</strong> ${arr.scheduled ? new Date(arr.scheduled).toLocaleString() : 'N/A'}</p>
          <p><strong>Actual:</strong> ${arr.actual ? new Date(arr.actual).toLocaleString() : 'N/A'}</p>
        `;
      } else {
        detailsDiv.innerHTML = `
          <div class="text-danger p-2">
            No flight details found for <strong>${flightNo}</strong>.
          </div>`;
      }
    } catch (err) {
      console.error(err);
      detailsDiv.innerHTML = `<div class="text-danger p-2">Error fetching flight details.</div>`;
    }
  });

  // Also clear on input change (optional)
  flightInput.addEventListener('input', function() {
    if (!this.value.trim()) {
      detailsDiv.style.opacity = 0;
      setTimeout(() => {
        detailsDiv.style.display = 'none';
        detailsDiv.innerHTML = '';
      }, 500);
    }
  });
});


</script>


<script type="module">
   
    // Add this to the Firebase script (in the <script type="module"> block)
document.addEventListener('DOMContentLoaded', function() {
  // Extract postcodes from pre-filled addresses on edit and set hidden fields
  const extractPostcodeFromAddress = (address) => {
    if (!address) return '';
    const postcodeRegex = /\b[A-Z]{1,2}\d{1,2}[A-Z]?\s*\d?[A-Z]{0,2}\b/i;
    const match = address.match(postcodeRegex);
    return match ? match[0].toUpperCase().trim() : '';
  };
  const pickupAddr = document.getElementById('pickup_address').value.trim();
  if (pickupAddr) {
    const pickupPC = extractPostcodeFromAddress(pickupAddr);
    document.getElementById('pickup_postcode').value = pickupPC;
  }
  const dropoffAddr = document.getElementById('dropoff_address').value.trim();
  if (dropoffAddr) {
    const dropoffPC = extractPostcodeFromAddress(dropoffAddr);
    document.getElementById('dropoff_postcode').value = dropoffPC;
  }
  // After preload, recalc for edit mode
  preloadAllLocations().then(() => {
    setTimeout(recalcFeesAndTotal, 300);
  });
});
   
// import { initializeApp } from "https://www.gstatic.com/firebasejs/10.11.0/firebase-app.js";
// import { getDatabase, ref, get, child } from "https://www.gstatic.com/firebasejs/10.11.0/firebase-database.js";
// const app = initializeApp({ databaseURL: "https://crown-carz-default-rtdb.firebaseio.com/" });
// const db = getDatabase(app);
// const firebaseLocations = { airports: {}, stations: {}, ports: {} };
// const $ = (sel) => document.querySelector(sel);
// const normPC = (pc) => (pc || "").toString().replace(/\s+/g, "").toUpperCase();
// async function preloadAllLocations() {
//   const cats = ["airports", "stations", "ports"];
//   for (const type of cats) {
//     try {
//       const snap = await get(child(ref(db), type));
//       firebaseLocations[type] = snap.exists() ? snap.val() : {};
//     } catch (e) {
//       console.error("Firebase load error", type, e);
//     }
//   }
// }
// function populateDatalist(type, listId) {
//   const datalist = document.getElementById(listId);
//   datalist.innerHTML = ""; // clear old options
// //   const items = Object.values(firebaseLocations[type] || {});
// //   items.forEach(item => {
// //     const opt = document.createElement("option");
// //     opt.value = `${item.address || ""} ${item.post_code || ""}`;
// //     opt.dataset.postcode = item.post_code || "";
// //     datalist.appendChild(opt);
// //   });
// const items = Object.entries(firebaseLocations[type] || {});
//   items.forEach(([firebaseKey, item]) => {
//     const opt = document.createElement("option");
//     opt.value = `${item.address || ""} ${item.post_code || ""}`;
//     opt.dataset.id = firebaseKey;                  // CORRECT ID
//     opt.dataset.postcode = item.post_code || "";
    
//     datalist.appendChild(opt);
//   });
// }
// // function recalcFeesAndTotal() {
// //   const pickupType = $('input[name="pickup_type"]:checked')?.value;
// //   const dropoffType = $('input[name="dropoff_type"]:checked')?.value;
// //   const pickupPC = $('#pickup_postcode')?.value?.trim();
// //   const dropoffPC = $('#dropoff_postcode')?.value?.trim();
// //   let pickupFee = 0, dropoffFee = 0;
// //   const findByPostcode = (type, pc) => {
// //     const list = Object.values(firebaseLocations[type] || {});
// //     return list.find(item => normPC(item.post_code) === normPC(pc)) || null;
// //   };
// //   if (pickupPC) {
// //     const hit = findByPostcode(pickupType, pickupPC);
// //     if (hit) pickupFee = parseFloat(hit.pickup_charge || 0);
// //   }
// //   if (dropoffPC) {
// //     const hit = findByPostcode(dropoffType, dropoffPC);
// //     if (hit) dropoffFee = parseFloat(hit.dropoff_charge || 0);
// //   }
// //   $('#parking').value = (pickupFee + dropoffFee).toFixed(2);
// //   const fare = parseFloat($('#fare').value) || 0;
// //   const waiting = parseFloat($('#waiting_fee').value) || 0;
// //   const extra = parseFloat($('#extra').value) || 0;
// //   const seat = $('#child_seat')?.checked ? 5 : 0;
// //   const parking = parseFloat($('#parking').value) || 0;
// //   $('#price').value = (fare + parking + waiting + extra + seat).toFixed(2);
// // }
// function recalcFeesAndTotal() {
//   const pickupType = $('input[name="pickup_type"]:checked')?.value;
//   const dropoffType = $('input[name="dropoff_type"]:checked')?.value;
//   const pickupPC = $('#pickup_postcode')?.value?.trim();
//   const dropoffPC = $('#dropoff_postcode')?.value?.trim();
//   let pickupFee = 0, dropoffFee = 0, locationExtra = 0;
//   const findByPostcode = (type, pc) => {
//     const list = Object.entries(firebaseLocations[type] || {});
//     return list.find(item => normPC(item.dataset.id) === normPC(pc)) || null;
//   };
//   if (pickupPC) {
//     const hit = findByPostcode(pickupType, pickupPC);
//     if (hit) {
//       pickupFee = parseFloat(hit.pickup_charge || 0);
//       locationExtra = parseFloat(hit.extras || 0); // Include location-based extra
//     }
//   }
//   if (dropoffPC) {
//     const hit = findByPostcode(dropoffType, dropoffPC);
//     if (hit) {
//       dropoffFee = parseFloat(hit.dropoff_charge || 0);
//       locationExtra = parseFloat(hit.extras || 0); // Include location-based extra
//     }
//   }
//   $('#parking').value = (pickupFee + dropoffFee).toFixed(2);
//   $('#extra').value = locationExtra.toFixed(2);
//   const fare = parseFloat($('#fare').value) || 0;
//   const waiting = parseFloat($('#waiting_fee').value) || 0;
//   const extra = parseFloat($('#extra').value) || 0;
//   const seat = $('#child_seat')?.checked ? 5 : 0;
//   const parking = parseFloat($('#parking').value) || 0;
//   $('#price').value = (fare + parking + waiting + extra + seat).toFixed(2);
// }
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.11.0/firebase-app.js";
import { getDatabase, ref, get, child } from "https://www.gstatic.com/firebasejs/10.11.0/firebase-database.js";

const app = initializeApp({ databaseURL: "https://crown-carz-default-rtdb.firebaseio.com/" });
const db = getDatabase(app);

const firebaseLocations = { airports: {}, stations: {}, ports: {} };
const $ = (sel) => document.querySelector(sel);
const normPC = (pc) => (pc || "").toString().replace(/\s+/g, "").toUpperCase();

async function preloadAllLocations() {
  const cats = ["airports", "stations", "ports"];
  for (const type of cats) {
    try {
      const snap = await get(child(ref(db), type));
      firebaseLocations[type] = snap.exists() ? snap.val() : {};
    } catch (e) {
      console.error("Firebase load error", type, e);
    }
  }
}

// Populate datalist with Firebase IDs
function populateDatalist(type, listId) {
  const datalist = document.getElementById(listId);
  datalist.innerHTML = "";
  Object.entries(firebaseLocations[type] || {}).forEach(([id, item]) => {
    const opt = document.createElement("option");
    opt.value = `${item.address || ""} ${item.post_code || ""}`;
    opt.dataset.id = id; // store Firebase ID
    opt.dataset.postcode = item.post_code || "";
    datalist.appendChild(opt);
  });
}

// Get Firebase ID of selected option
function getSelectedId(inputId, listId) {
  const val = $(`#${inputId}`).value;
  const datalist = $(`#${listId}`);
  const option = Array.from(datalist.options).find(opt => opt.value === val);
  return option ? option.dataset.id : null;
}

// Recalculate fees using selected Firebase IDs
function recalcFeesAndTotal() {
  const pickupType = $('input[name="pickup_type"]:checked')?.value;
  const dropoffType = $('input[name="dropoff_type"]:checked')?.value;

  const pickupId = getSelectedId('pickup_address', 'pickup_list');
  const dropoffId = getSelectedId('dropoff_address', 'dropoff_list');

  $('#pickup_address_id').value = pickupId || '';
  $('#dropoff_address_id').value = dropoffId || '';

  let pickupFee = 0, dropoffFee = 0, locationExtra = 0;

  if (pickupId) {
    const hit = firebaseLocations[pickupType][pickupId];
    if (hit) {
      pickupFee = parseFloat(hit.pickup_charge || 0);
      locationExtra = parseFloat(hit.extras || 0);
      $('#pickup_postcode').value = hit.post_code || '';
    }
  }
  console.log("pickupFee",locationExtra);

  if (dropoffId) {
    const hit = firebaseLocations[dropoffType][dropoffId];
    if (hit) {
      dropoffFee = parseFloat(hit.dropoff_charge || 0);
      locationExtra += parseFloat(hit.extras || 0);
      $('#dropoff_postcode').value = hit.post_code || '';
    }
  }
  console.log("dropoffFee",locationExtra);

  $('#parking').value = (pickupFee + dropoffFee).toFixed(2);
  $('#extra').value = locationExtra.toFixed(2);

  const fare = parseFloat($('#fare').value) || 0;
  const waiting = parseFloat($('#waiting_fee').value) || 0;
  const extra = parseFloat($('#extra').value) || 0;
  const seat = $('#child_seat')?.checked ? 5 : 0;
  const parking = parseFloat($('#parking').value) || 0;

  $('#price').value = (fare + parking + waiting + extra + seat).toFixed(2);
}
// Expose recalcFeesAndTotal globally for use in swap
window.recalcFeesAndTotal = recalcFeesAndTotal;
preloadAllLocations().then(() => {
  const pickupType = $('input[name="pickup_type"]:checked').value;
  const dropoffType = $('input[name="dropoff_type"]:checked').value;
  populateDatalist(pickupType, 'pickup_list');
  populateDatalist(dropoffType, 'dropoff_list');
});
document.querySelectorAll('input[name="pickup_type"]').forEach(r =>
  r.addEventListener('change', e => populateDatalist(e.target.value, 'pickup_list'))
);
document.querySelectorAll('input[name="dropoff_type"]').forEach(r =>
  r.addEventListener('change', e => populateDatalist(e.target.value, 'dropoff_list'))
);
// Auto-fill postcode when selecting a datalist option
// $('#pickup_address').addEventListener('input', (e) => {
//   const val = e.target.value;
//   const type = $('input[name="pickup_type"]:checked')?.value;
//   const match = Object.values(firebaseLocations[type] || {}).find(item =>
//     val.includes(item.post_code) || val.includes(item.address)
//   );
//   if (match) {
//     $('#pickup_postcode').value = match.post_code || '';
//     recalcFeesAndTotal();
//   }
// });
// $('#dropoff_address').addEventListener('input', (e) => {
//   const val = e.target.value;
//   const type = $('input[name="dropoff_type"]:checked')?.value;
//   const match = Object.values(firebaseLocations[type] || {}).find(item =>
//     val.includes(item.post_code) || val.includes(item.address)
//   );
//   if (match) {
//     $('#dropoff_postcode').value = match.post_code || '';
//     recalcFeesAndTotal();
//   }
// });
$('#pickup_address').addEventListener('input', (e) => {
  const val = e.target.value;
  const type = $('input[name="pickup_type"]:checked')?.value;
  const match = Object.values(firebaseLocations[type] || {}).find(item =>
    val.includes(item.post_code) || val.includes(item.address)
  );
  if (match) {
    $('#pickup_postcode').value = match.post_code || '';
    // ✅ Signal to AddressAutocomplete that Firebase already handled this
    $('#pickup_address')._firebaseSelected = true;
    recalcFeesAndTotal();
  } else {
    $('#pickup_address')._firebaseSelected = false;
  }
});
$('#dropoff_address').addEventListener('input', (e) => {
  const val = e.target.value;
  const type = $('input[name="dropoff_type"]:checked')?.value;
  const match = Object.values(firebaseLocations[type] || {}).find(item =>
    val.includes(item.post_code) || val.includes(item.address)
  );
  if (match) {
    $('#dropoff_postcode').value = match.post_code || '';
    // ✅ Signal to AddressAutocomplete that Firebase already handled this
    $('#dropoff_address')._firebaseSelected = true;
    recalcFeesAndTotal();
  } else {
    $('#dropoff_address')._firebaseSelected = false;
  }
});
// Add radio change listeners to recalc fees
document.querySelectorAll('input[name="pickup_type"], input[name="dropoff_type"]').forEach(r => {
  r.addEventListener('change', () => {
    setTimeout(recalcFeesAndTotal, 100); // Delay to ensure checked state is updated
  });
});
document.addEventListener('DOMContentLoaded', function() {
  const swapBtn = document.getElementById('swap-btn');
 
  const pickupAddress = document.getElementById('pickup_address');
  const dropoffAddress = document.getElementById('dropoff_address');
 
  swapBtn.addEventListener('click', function() {
    // --- Pickup fields ---
    const pickupType = document.querySelector('input[name="pickup_type"]:checked');
    const pickupPostcode = document.getElementById('pickup_postcode');
    const pickupLat = document.getElementById('pickup_lat');
    const pickupLong = document.getElementById('pickup_long');
    // --- Dropoff fields ---
    const dropoffType = document.querySelector('input[name="dropoff_type"]:checked');
    const dropoffPostcode = document.getElementById('dropoff_postcode');
    const dropLat = document.getElementById('drop_lat');
    const dropLong = document.getElementById('drop_long');
    // --- Animate both fields ---
    pickupAddress.classList.add('slide-left');
    dropoffAddress.classList.add('slide-right');
    // Wait for animation, then swap values
    setTimeout(() => {
      // Swap address values
      const tempAddress = pickupAddress.value;
      pickupAddress.value = dropoffAddress.value;
      dropoffAddress.value = tempAddress;
      // Swap hidden fields
      const tempPostcode = pickupPostcode.value;
      const tempLat = pickupLat.value;
      const tempLong = pickupLong.value;
      pickupPostcode.value = dropoffPostcode.value;
      pickupLat.value = dropLat.value;
      pickupLong.value = dropLong.value;
      dropoffPostcode.value = tempPostcode;
      dropLat.value = tempLat;
      dropLong.value = tempLong;
      // Swap radio selections (no need to deselect explicitly; setting checked handles it)
      if (pickupType && dropoffType) {
        const originalPickupTypeValue = pickupType.value;
        const originalDropoffTypeValue = dropoffType.value;
        // Set new states
        const newPickupRadio = document.querySelector(`input[name="pickup_type"][value="${originalDropoffTypeValue}"]`);
        const newDropoffRadio = document.querySelector(`input[name="dropoff_type"][value="${originalPickupTypeValue}"]`);
        if (newPickupRadio) newPickupRadio.checked = true;
        if (newDropoffRadio) newDropoffRadio.checked = true;
        // Dispatch change events to trigger listeners (populate and recalc)
        if (newPickupRadio) newPickupRadio.dispatchEvent(new Event('change'));
        if (newDropoffRadio) newDropoffRadio.dispatchEvent(new Event('change'));
      }
      // Remove animations
      pickupAddress.classList.remove('slide-left');
      dropoffAddress.classList.remove('slide-right');
      // Highlight swapped inputs briefly
      pickupAddress.classList.add('bg-warning', 'bg-opacity-25');
      dropoffAddress.classList.add('bg-warning', 'bg-opacity-25');
      setTimeout(() => {
        pickupAddress.classList.remove('bg-warning', 'bg-opacity-25');
        dropoffAddress.classList.remove('bg-warning', 'bg-opacity-25');
      }, 1000);
      // ✅ Re-run price calculation function
      if (typeof window.fetchPrice === 'function') {
        window.markPricingInteraction?.();
        setTimeout(() => {
          window.fetchPrice(); // re-hit after animation
          // Extra delay for any async from radio changes
          setTimeout(() => {
            window.recalcFeesAndTotal(); // Use global
            if (typeof window.calculateTotal === 'function') window.calculateTotal();
          }, 200);
        }, 400); // slight delay to match animation timing
      } else {
        console.warn("⚠️ fetchPrice() not found");
      }
    }, 250); // Match animation duration
  });
});
</script>


<script>
var map, directionsService, directionsRenderer;
function initRouteMap() {
  console.log('Map initializing...'); // Debug log
  map = new google.maps.Map(document.getElementById("routeMap"), {
    zoom: 7,
    center: { lat: 51.5074, lng: -0.1278 }, // London
  });
  directionsService = new google.maps.DirectionsService();
  directionsRenderer = new google.maps.DirectionsRenderer({
    map: map,
    suppressMarkers: false,
  });
  // draw initial route if addresses exist
  updateRoute();
}
function getViaAddresses() {
  const vias = [];
  document.querySelectorAll('input[name="via_addresses[]"]').forEach(input => {
    if (input.value.trim()) vias.push({ location: input.value.trim(), stopover: true });
  });
  return vias;
}
function updateRoute() {
  const origin = document.getElementById('pickup_address')?.value?.trim();
  const destination = document.getElementById('dropoff_address')?.value?.trim();
  const waypoints = getViaAddresses();
  console.log('Updating route:', { origin, destination, waypoints: waypoints.length }); // Debug log
  if (!origin || !destination) {
    console.log('Skipping route: Missing origin or destination'); // Debug log
    return; // wait until both entered
  }
  directionsService.route(
    {
      origin: origin,
      destination: destination,
      waypoints: waypoints,
      travelMode: google.maps.TravelMode.DRIVING,
      optimizeWaypoints: false,
    },
    function (response, status) {
      if (status === google.maps.DirectionsStatus.OK) {
        directionsRenderer.setDirections(response);
        console.log('Route drawn successfully'); // Debug log
      } else {
        console.error("Directions request failed due to " + status); // Enhanced error
      }
    }
  );
}
// ✅ Update route whenever address is selected (from TaxiBase)
document.addEventListener("taxibase:select", function () {
  updateRoute();
});
</script>
<!-- ✅ Load Google Maps JS API -->
<script async defer
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAEgGulK8GkRGGBbfmX46LxHPeq6iGiyUU&callback=initRouteMap&libraries=places">
</script>
{{-- ✅ Enhanced Event Listeners for Manual Input --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Debounce helper
    function debounce(fn, delay) {
        let timeout;
        return function() {
            clearTimeout(timeout);
            timeout = setTimeout(fn, delay);
        };
    }
    const debouncedUpdate = debounce(updateRoute, 500);
    const pickupInput = document.getElementById('pickup_address');
    const dropoffInput = document.getElementById('dropoff_address');
    if (pickupInput) {
        pickupInput.addEventListener('input', debouncedUpdate);
        pickupInput.addEventListener('blur', updateRoute);
    }
    if (dropoffInput) {
        dropoffInput.addEventListener('input', debouncedUpdate);
        dropoffInput.addEventListener('blur', updateRoute);
    }
    // For dynamic vias: Re-attach on add/remove
    const addViaBtn = document.getElementById('add-via');
    if (addViaBtn) {
        addViaBtn.addEventListener('click', function() {
            setTimeout(() => {
                document.querySelectorAll('input[name="via_addresses[]"]').forEach(input => {
                    input.addEventListener('input', debouncedUpdate);
                    input.addEventListener('blur', updateRoute);
                });
            }, 100);
        });
    }
    // Force initial update after DOM loads (for edit forms)
    setTimeout(updateRoute, 1000);
});
</script>
{{-- ✅ Via Remove Trigger --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const viaContainer = document.getElementById("via-container");
    if (viaContainer) {
        // Handle existing remove buttons (for edit forms)
        viaContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-via')) {
                e.target.closest('.via-field').remove();
                updateRoute(); // Refresh map after remove
            }
        });
        // For newly added vias (in add-via click handler, but this catches all)
        document.getElementById('add-via')?.addEventListener('click', function() {
            setTimeout(() => {
                const newRemoveBtn = viaContainer.querySelector('.remove-via:last-of-type');
                if (newRemoveBtn) {
                    newRemoveBtn.addEventListener('click', function(e) {
                        e.target.closest('.via-field').remove();
                        updateRoute(); // Refresh map after remove
                    });
                }
            }, 100);
        });
    }
});
</script>
{{-- ✅ Price + Mileage Calculation Script (FIXED: Call calculateTotal after API success) --}}
@push('scripts')
<script>
    const IS_EDIT_MODE = @json(isset($booking));
</script>

<script>
// function extractPostcodeFromAddress(address) {
// if (!address) return '';
// const postcodeRegex = /[A-Za-z]{1,2}[0-9]{1,2}[A-Za-z]?\s*[0-9][A-Za-z]{2}/i;
// const match = address.match(postcodeRegex);
// return match ? match[0].toUpperCase().trim() : address.toUpperCase().trim();
// }
function extractPostcodeFromAddress(address) {
  if (!address) return '';
  // Updated regex — now also matches shorter postcodes like "TW6"
  const postcodeRegex = /\b[A-Z]{1,2}\d{1,2}[A-Z]?\s*\d?[A-Z]{0,2}\b/i;
  const match = address.match(postcodeRegex);
  return match ? match[0].toUpperCase().trim() : '';
}
document.addEventListener("DOMContentLoaded", function () {
    var vehicleSelect = document.querySelector('select[name="vehicle_id"]');
    var pickupInput = document.querySelector('input[name="pickup_address"]');
    var dropoffInput = document.querySelector('input[name="dropoff_address"]');
    var priceInput = document.querySelector('#fare');
    var mileageInput = document.querySelector('#mileage');
    const priceLoader = document.getElementById('price-loader-container'); // ✅ Get loader
    function getViaAddresses() {
        const viaInputs = document.querySelectorAll('input[name="via_addresses[]"]');
        const viaList = [];
        viaInputs.forEach((input) => {
            const val = input.value.trim();
            if (val) viaList.push(extractPostcodeFromAddress(val));
        });
        return viaList;
    }
    // if (IS_EDIT_MODE) {
    //     console.log('Edit mode detected — skipping price fetch');
    //     // window.calculateTotal();
    //     return;
    // }
    window.calculateTotal = function() { // Expose globally for easy call
        const fareInput = document.getElementById("fare");
        const totalInput = document.getElementById("price");
        const parkingInput = document.getElementById("parking");
        const waitingInput = document.getElementById("waiting_fee");
        const extraInput = document.getElementById("extra");
        const childSeat = document.getElementById("child_seat");
        const fare = parseFloat(fareInput.value) || 0;
        const parking = parseFloat(parkingInput.value) || 0;
        const waiting = parseFloat(waitingInput.value) || 0;
        const extra = parseFloat(extraInput.value) || 0;
        const seat = childSeat.checked ? 5 : 0;
        const total = fare + parking + waiting + extra + seat;
        totalInput.value = Math.ceil(total);

        // totalInput.value = total.toFixed(2);
    };
//     async function fetchPrice() {
//     // window.fetchPrice = async function () {
//         var vehicleId = vehicleSelect.value;
//         var pickupAddress = pickupInput.value.trim();
//         var dropoffAddress = dropoffInput.value.trim();
//         var pickupPostcode = extractPostcodeFromAddress(pickupAddress);
//         var dropoffPostcode = extractPostcodeFromAddress(dropoffAddress);
//         var vias = getViaAddresses();
//         var selected = vehicleSelect.options[vehicleSelect.selectedIndex];
//         var vehicleMake = selected ? selected.getAttribute("data-make") : null;
//         // ✅ Reset fields before check
//         priceInput.value = '';
//         mileageInput.value = '';
       
       
//         const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'), { backdrop: 'static', keyboard: false });
       
       
//         console.log(vehicleMake);
//         console.log(pickupPostcode);
//         console.log(dropoffPostcode);
       
// if (vehicleId && pickupPostcode && dropoffPostcode) {
//     loadingModal.show(); // ✅ Show modal before fetching
//     try {
//         let url = `/admin/booking/get-price?vehicle_id=${encodeURIComponent(vehicleMake)}&pickup=${encodeURIComponent(pickupPostcode)}&dropoff=${encodeURIComponent(dropoffPostcode)}`;
//         vias.forEach(v => url += `&vias[]=${encodeURIComponent(v)}`);
//         const response = await fetch(url);
//         const data = await response.json();
//         if (data.success) {
//             priceInput.value =  data.price;
//             //mileageInput.value = data.total_distance;
//              mileageInput.value = data.journey_distance;
//              console.log(data.price);
//              console.log(data.type);
//              console.log(data.applied_percentages);
//              console.log(data.vehicle_type);
             
//             // ✅ Auto-update total after setting fare
//             window.calculateTotal();
//         } else {
//             console.error("Price calculation failed:", data.message);
//         }
//     } catch (error) {
//         console.error("Error fetching price:", error);
//     } finally {
//         loadingModal.hide(); // ✅ Hide modal after done
        
        
//     }
// } else {
//     loadingModal.hide(); // ✅ Hide if data incomplete
//     // If incomplete, still try to calculate with current values (e.g., manual fare)
//     window.calculateTotal();
// }
//     }

async function fetchPrice() {
    const requestId = ++window.latestPriceRequestId;
    var vehicleId = vehicleSelect.value;
    var pickupAddress = pickupInput.value.trim();
    var dropoffAddress = dropoffInput.value.trim();
    var pickupPostcode = extractPostcodeFromAddress(pickupAddress);
    var dropoffPostcode = extractPostcodeFromAddress(dropoffAddress);
    var vias = getViaAddresses();
    var selected = vehicleSelect.options[vehicleSelect.selectedIndex];
    var vehicleMake = selected ? selected.getAttribute("data-make") : null;

    // ✅ New: pickup date and time
    var pickupDate = document.getElementById('pickup_date')?.value || '';
    var pickupTime = document.getElementById('pickup_time')?.value || '';
    
    
    console.log("pickupTime",pickupTime);
    console.log("pickupDate",pickupDate);

    // Reset fields
    priceInput.value = '';
    mileageInput.value = '';
    
    // 🚫 Do NOT fetch price again if editing booking
    // if (IS_EDIT_MODE) {
    //     console.log('Edit mode detected — skipping price fetch');
    //     // window.calculateTotal();
    //     return;
    // }

    if (vehicleId && pickupPostcode && dropoffPostcode) {
        // Block form interaction until the current fare/mileage response is ready.
        priceLoader.style.display = 'inline-flex';
        window.showLoadingModal('Calculating fare and mileage, please wait...');
        try {
            let url = `/admin/booking/get-price?vehicle_id=${encodeURIComponent(vehicleMake)}&pickup=${encodeURIComponent(pickupPostcode)}&dropoff=${encodeURIComponent(dropoffPostcode)}&pickup_date=${encodeURIComponent(pickupDate)}&pickup_time=${encodeURIComponent(pickupTime)}`;
            vias.forEach(v => url += `&vias[]=${encodeURIComponent(v)}`);

            const response = await fetch(url);
            const data = await response.json();

            // A newer route/vehicle request has already started, so this older
            // response must not overwrite its price.
            if (requestId !== window.latestPriceRequestId) return;

            if (data.success) {
                priceInput.value = data.price;
                mileageInput.value = data.journey_distance;
                
                
                console.log("Base Fare:", data.base_price);
                console.log("Surcharge %:", data.surcharge_percent);
                console.log("Applied Surcharge:", data.surcharge_amount);
                console.log("Final Fare:", data.price);

                // Auto-update total
                window.calculateTotal();
            } else {
                console.error("Price calculation failed:", data.message);
            }
        } catch (error) {
            if (requestId === window.latestPriceRequestId) {
                console.error("Error fetching price:", error);
            }
        } finally {
            if (requestId === window.latestPriceRequestId) {
                priceLoader.style.display = 'none';
                window.hideLoadingModal();
            }
        }
    } else {
        priceLoader.style.display = 'none';
        window.hideLoadingModal();
        window.calculateTotal();
    }
}

    window.latestPriceRequestId = 0;
    // ✅ Trigger on load if vehicle/addresses are present (for edit forms)
    // if (vehicleSelect.value && pickupInput.value.trim() && dropoffInput.value.trim()) {
    //     fetchPrice();
    // }
    // // ✅ Initial calculation on load
    // window.calculateTotal();
    // ─── Edit-mode guard ─────────────────────────────────────────────────────────
// In edit mode we must NOT auto-fetch a new price on load; the saved price is
// already in the form. We only fetch once the user actively changes vehicle,
// pickup, or dropoff.
let userHasInteracted = false;   // becomes true on the first real user gesture

const _originalFetchPrice = fetchPrice;
window.fetchPrice = async function () {
    if (IS_EDIT_MODE && !userHasInteracted) {
        // Just recompute the total from the already-correct saved values.
        window.calculateTotal();
        return;
    }
    return _originalFetchPrice();
};

// Mark as interacted on any change the user makes to the key fields
function markInteracted() { userHasInteracted = true; }
window.markPricingInteraction = markInteracted;
vehicleSelect.addEventListener('change', markInteracted);
pickupInput.addEventListener('blur',    markInteracted);
dropoffInput.addEventListener('blur',   markInteracted);

// Re-wire the event listeners to use the guarded version
vehicleSelect.addEventListener('change', window.fetchPrice);
pickupInput.addEventListener('blur',    window.fetchPrice);
dropoffInput.addEventListener('blur',   window.fetchPrice);

// Existing via fields also use one guarded price request.
document.querySelectorAll('input[name="via_addresses[]"]').forEach(input => {
    input.addEventListener('blur', function () {
        markInteracted();
        window.fetchPrice();
    });
});

// ✅ On load: only recalc total from saved values — no API call in edit mode
window.calculateTotal();
});
</script>
@endpush
{{-- ✅ TaxiBase Address Autocomplete Integration (ENHANCED: Attach price listener on new via) --}}
@push('scripts')

<script>
// 1. Define the drop-in replacement class for AddressAutocomplete
class AddressAutocomplete {
    constructor(options) {
        this.field = document.getElementById(options.fieldId);
        this.apiKey = options.apiKey;
        this.postcodeField = document.getElementById(options.postcodeFieldId);
        this.latField = document.getElementById(options.latFieldId);
        this.longField = document.getElementById(options.longFieldId);
        this.onSelectCallback = options.onSelect;

        if (this.field) {
            this.init();
        }
    }

    init() {
        // Create a wrapper for the input and dropdown
        this.wrapper = document.createElement('div');
        this.wrapper.style.position = 'relative';
        this.field.parentNode.insertBefore(this.wrapper, this.field);
        this.wrapper.appendChild(this.field);

        // Create the dropdown list container
        this.listContainer = document.createElement('ul');
        this.listContainer.style.cssText = 'position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid #ced4da; border-radius:4px; z-index:1000; list-style:none; padding:0; margin:4px 0 0 0; display:none; max-height:250px; overflow-y:auto; box-shadow:0 4px 6px rgba(0,0,0,0.1);';
        this.wrapper.appendChild(this.listContainer);

        let debounceTimer;

        // Listen to input changes
        // this.field.addEventListener('input', () => {
        //     clearTimeout(debounceTimer);
        //     const query = this.field.value.trim();
            
        //     if (query.length < 3) {
        //         this.listContainer.style.display = 'none';
        //         return;
        //     }

        //     debounceTimer = setTimeout(() => this.fetchOSPlaces(query), 400);
        // });
        this.field.addEventListener('input', () => {
    clearTimeout(debounceTimer);

    // ✅ If Firebase already matched this value, skip OS Places entirely
    if (this.field._firebaseSelected) {
        this.listContainer.style.display = 'none';
        // Reset flag so typing a new value re-enables OS Places
        this.field._firebaseSelected = false;
        return;
    }

    const query = this.field.value.trim();

    if (query.length < 3) {
        this.listContainer.style.display = 'none';
        return;
    }

    debounceTimer = setTimeout(() => this.fetchOSPlaces(query), 400);
});

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!this.wrapper.contains(e.target)) {
                this.listContainer.style.display = 'none';
            }
        });
    }

    async fetchOSPlaces(query) {
        // output_srs=EPSG:4326 ensures we get standard LAT and LNG instead of X/Y coordinates
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
        
        if (results.length === 0) {
            this.listContainer.style.display = 'none';
            return;
        }

        results.forEach(item => {
            // Data is usually inside DPA (Postal Address) or LPI (Local Property Identifier)
            const place = item.DPA || item.LPI;
if (!place) return;

// Build a better formatted address
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
li.style.cssText = 'padding: 10px; cursor: pointer; border-bottom: 1px solid #f8f9fa; font-size: 14px;';
li.textContent = fullAddress;

            // Hover effect
            li.addEventListener('mouseenter', () => li.style.backgroundColor = '#f1f3f5');
            li.addEventListener('mouseleave', () => li.style.backgroundColor = 'transparent');

            // Select action
            // Select action — mousedown fires BEFORE blur, preventing address loss
li.addEventListener('mousedown', (e) => {
    e.preventDefault(); // prevents input from losing focus before value is set
    
    this.field.value = fullAddress;
    if (this.postcodeField) this.postcodeField.value = place.POSTCODE || '';
    if (this.latField) this.latField.value = place.LAT || '';
    if (this.longField) this.longField.value = place.LNG || '';

    this.listContainer.style.display = 'none';

    if (typeof this.onSelectCallback === 'function') {
        setTimeout(() => this.onSelectCallback(), 50); // slight delay ensures field value is committed
    }
});

            this.listContainer.appendChild(li);
        });

        this.listContainer.style.display = 'block';
    }
}

// 2. Your original logic remains exactly the same below this line
document.addEventListener('DOMContentLoaded', async  function() {
    // Replaced with your OS Places API Key
    // const API_KEY = 'bY7g2DcT8gGRxdfR1K46FWKFYMbdprRy';
    // ✅ Fetch API key from Firebase system_settings

    let API_KEY = '';
    try {
        const res = await fetch(
            'https://crown-carz-default-rtdb.firebaseio.com/system_settings/autocomplete_key.json'
        );
        API_KEY = await res.json();
        console.log('API_KEY is fetch successfully')
    } catch (e) {
        console.error('Failed to load autocomplete_key from Firebase:', e);
    }

    if (!API_KEY) {
        console.warn('AddressAutocomplete: no API key, suggestions disabled.');
        return;
    }
    
    // helper to dispatch a single custom event the map will listen to
    function notifyAddressSelected(fieldId) {
        document.dispatchEvent(new CustomEvent('taxibase:select', { detail: { fieldId } }));
    }

    // Pickup autocomplete (stores postcode / lat / long in hidden fields)
    new AddressAutocomplete({
        fieldId: 'pickup_address',
        apiKey: API_KEY,
        postcodeFieldId: 'pickup_postcode',
        latFieldId: 'pickup_lat',
        longFieldId: 'pickup_long',
        onSelect: function () {
    notifyAddressSelected('pickup_address');
    setTimeout(() => {
        document.getElementById('pickup_address').dispatchEvent(new Event('blur'));
    }, 100);
}
    });

    // Dropoff autocomplete (stores postcode / lat / long in hidden fields)
    new AddressAutocomplete({
        fieldId: 'dropoff_address',
        apiKey: API_KEY,
        postcodeFieldId: 'dropoff_postcode',
        latFieldId: 'drop_lat',
        longFieldId: 'drop_long',
        onSelect: function () {
    notifyAddressSelected('dropoff_address');
    setTimeout(() => {
        document.getElementById('dropoff_address').dispatchEvent(new Event('blur'));
    }, 100);
}
    });

    // Via fields: when creating a new via input, also append hidden lat/long fields
    const viaContainer = document.getElementById("via-container");
    const addViaBtn = document.getElementById("add-via");
   
    let viaCount = {{ isset($booking) && !empty($booking['vias']) ? count($booking['vias']) : 0 }};

    addViaBtn.addEventListener("click", function() {
        viaCount++;
        const viaId = `via_${viaCount}`;
        const wrapper = document.createElement("div");
        wrapper.classList.add("via-field");
        // create visible input + hidden lat/long with overlay remove button
        wrapper.innerHTML = `
            <div class="position-relative">
              <input type="text" name="via_addresses[]" id="${viaId}"
                       class="form-control" placeholder="Type your address or postcode" autocomplete="off">
              <button type="button" class="remove-via" title="Remove">
                  <i class="bi bi-x"></i>
              </button>
            </div>
            <input type="hidden" id="${viaId}_postcode" name="${viaId}_postcode">
            <input type="hidden" id="${viaId}_lat" name="${viaId}_lat">
            <input type="hidden" id="${viaId}_long" name="${viaId}_long">
        `;
        viaContainer.appendChild(wrapper);

        // Initialize Autocomplete for this via input and populate hidden lat/long
        new AddressAutocomplete({
            fieldId: viaId,
            apiKey: API_KEY,
            postcodeFieldId: `${viaId}_postcode`,
            latFieldId: `${viaId}_lat`,
            longFieldId: `${viaId}_long`,
            onSelect: function () {
                // notify map that a via was selected (map will read the hidden lat/long)
                notifyAddressSelected(viaId);
                setTimeout(() => document.getElementById(viaId).dispatchEvent(new Event('blur')), 50);
            }
        });

        // ✅ Attach price fetch on blur for the new via input
        document.getElementById(viaId).addEventListener('blur', function () {
            window.markPricingInteraction?.();
            window.fetchPrice?.();
        });

        // Remove via field
        wrapper.querySelector(".remove-via").addEventListener("click", () => {
            wrapper.remove();
            // notify map to update
            updateRoute();
            // Optionally refetch price after remove
            window.markPricingInteraction?.();
            setTimeout(() => window.fetchPrice?.(), 100);
        });
    });

    // Initialize autocompletes for pre-existing via fields (edit mode)
    document.querySelectorAll('#via-container .via-field input[name="via_addresses[]"]').forEach((input, index) => {
        const viaId = input.id || `via_${index}`;
        new AddressAutocomplete({
            fieldId: viaId,
            apiKey: API_KEY,
            postcodeFieldId: `${viaId}_postcode`,
            latFieldId: `${viaId}_lat`,
            longFieldId: `${viaId}_long`,
            onSelect: function () {
                notifyAddressSelected(viaId);
                setTimeout(() => input.dispatchEvent(new Event('blur')), 50);
            }
        });
    });
});

// after Taxibase setup
['pickup_postcode','dropoff_postcode'].forEach(id=>{
  const el = document.getElementById(id);
  if (el) {
    el.addEventListener('change', () => window.applyFirebaseCharges());
    el.addEventListener('blur', () => window.applyFirebaseCharges());
  }
});

document.querySelectorAll('input[name="pickup_type"], input[name="dropoff_type"]').forEach(r=>{
  r.addEventListener('change', () => window.applyFirebaseCharges());
});

document.getElementById('pickup_options')?.addEventListener('change', () => window.applyFirebaseCharges());
document.getElementById('dropoff_options')?.addEventListener('change', () => window.applyFirebaseCharges());
</script>
{{-- ✅ Total Price Calculation (INTEGRATED into fetchPrice script) --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    // ✅ Additional listeners for manual changes to fare/parking/etc.
    const fareInput = document.getElementById("fare");
    const parkingInput = document.getElementById("parking");
    const waitingInput = document.getElementById("waiting_fee");
    const extraInput = document.getElementById("extra");
    const childSeat = document.getElementById("child_seat");
    [fareInput, parkingInput, waitingInput, extraInput, childSeat].forEach(el => {
        if (el) {
            el.addEventListener("input", window.calculateTotal);
            el.addEventListener("change", window.calculateTotal);
        }
    });
    // ✅ Initial calculation on load (already in fetchPrice, but safe double-call)
    setTimeout(window.calculateTotal, 500);
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const storageKey = 'callswitch:booking-phone';

    const callerPhone =
        sessionStorage.getItem(storageKey);

    const phoneInput =
        document.querySelector(
            'input[name="phone_no"]'
        );

    if (callerPhone && phoneInput) {
        /*
         * Existing value overwrite nahi hogi.
         * Create screen par blank field auto-fill hogi.
         */
        if (phoneInput.value.trim() === '') {
            phoneInput.value = callerPhone;

            phoneInput.dispatchEvent(
                new Event('input', {
                    bubbles: true
                })
            );

            phoneInput.dispatchEvent(
                new Event('change', {
                    bubbles: true
                })
            );
        }

        sessionStorage.removeItem(storageKey);
    }
});
</script>
@endpush
