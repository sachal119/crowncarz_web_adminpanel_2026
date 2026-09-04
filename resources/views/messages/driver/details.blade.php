@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4 px-2 px-md-4">
  <div class="card shadow-sm border-0 rounded-4 mx-auto" style="max-width: 900px;">
    
    {{-- Header --}}
    <div class="card-header bg-primary bg-opacity-25 rounded-top-4 py-3 px-4 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-bold text-dark">
        <i class="bi bi-info-circle-fill me-2 text-primary"></i> Job Details SMS
      </h5>
      <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    {{-- Body --}}
    <div class="card-body p-4">
      <p class="text-secondary mb-4">
        This message is automatically sent to the <strong>driver</strong> when a new job is allocated.  
        You can customize the message below. Use the available variables to insert job details dynamically.
      </p>

      <form action="{{ route('messages.driver.details.update') }}" method="POST">
        @csrf
        @method('POST')

        {{-- SMS Message --}}
        <div class="mb-3">
          <label class="form-label fw-semibold">SMS Message</label>
          <textarea 
            name="message" 
            id="smsMessage" 
            rows="10"
            class="form-control rounded-3 shadow-sm">{{ old('message', $smsTemplate->message ?? 
"Dear {driver_name}

You have been allocated a job

Date/Time: {job_date}, {job_time}
Name: {name}
Pickup: {pickup}
Via address: {via_address}
Drop off: {destination}
Phone: {mobile}
Payment Type: {payment}
Fare: {fare}
Parking: {car_park}
Flight No: {flight_no}
Vehicle Type: {vehicle_type}
Comments: {comments}

Kind regards,
Crown Carz LTD") }}</textarea>
        </div>

        {{-- Info Section --}}
        <div class="alert alert-info small mt-3">
          <strong>Available Variables:</strong><br>
          <code>{driver_name}</code> = Driver Name <br>
          <code>{job_date}</code> = Job Date <br>
          <code>{job_time}</code> = Job Time <br>
          <code>{name}</code> = Passenger Name <br>
          <code>{pickup}</code> = Pickup Location <br>
          <code>{via_address}</code> = Via Address <br>
          <code>{destination}</code> = Drop-off Location <br>
          <code>{mobile}</code> = Passenger Mobile Number <br>
          <code>{payment}</code> = Payment Type <br>
          <code>{fare}</code> = Fare Amount <br>
          <code>{car_park}</code> = Parking Charges <br>
          <code>{flight_no}</code> = Flight Number <br>
          <code>{vehicle_type}</code> = Vehicle Type <br>
          <code>{comments}</code> = Job Comments <br>
        </div>

        {{-- Live Preview --}}
        <div class="mt-4">
          <label class="form-label fw-semibold">Live Preview</label>
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-light" style="font-family: monospace;">
            <div id="smsPreview" class="text-dark"></div>
          </div>
        </div>

        {{-- Submit Button --}}
        <div class="d-flex justify-content-end mt-4">
          <button type="submit" class="btn text-white fw-semibold px-4 py-2 rounded-3 shadow-sm" style="background-color: #0d6efd;">
            <i class="bi bi-save me-1"></i> Save SMS Template
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- JS for Live Preview --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
  const textarea = document.getElementById("smsMessage");
  const preview = document.getElementById("smsPreview");

  const sampleData = {
    "{driver_name}": "David Wilson",
    "{job_date}": "15 Nov 2025",
    "{job_time}": "09:30 AM",
    "{name}": "John Doe",
    "{pickup}": "Heathrow Terminal 3",
    "{via_address}": "Oxford Street",
    "{destination}": "Reading Station",
    "{mobile}": "+447912345678",
    "{payment}": "Card",
    "{fare}": "£75",
    "{car_park}": "£5",
    "{flight_no}": "BA204",
    "{vehicle_type}": "Estate",
    "{comments}": "Handle with care"
  };

  const updatePreview = () => {
    let text = textarea.value;
    for (const key in sampleData) {
      text = text.replaceAll(key, sampleData[key]);
    }
    preview.innerText = text;
  };

  textarea.addEventListener("input", updatePreview);
  updatePreview();
});
</script>
@endsection
