@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4 px-2 px-md-4">
  <div class="card shadow-sm border-0 rounded-4 mx-auto" style="max-width: 900px;">

    {{-- Header --}}
    <div class="card-header bg-warning bg-opacity-25 rounded-top-4 py-3 px-4 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-bold text-dark">
        <i class="bi bi-chat-text-fill me-2 text-warning"></i> Booking Confirmation SMS
      </h5>
      <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    {{-- Body --}}
    <div class="card-body p-4">
      <p class="text-secondary mb-4">
        This SMS template is sent to customers automatically when a booking is confirmed.
        You can customize the message below. Use the variables listed to auto-insert booking details.
      </p>

      <form action="{{ route('customer.booking_sms.update') }}" method="POST">
        @csrf
        @method('POST')

        {{-- SMS Message --}}
        <div class="mb-3">
          <label class="form-label fw-semibold">SMS Message</label>
          <textarea name="message" id="smsMessage" rows="8"
            class="form-control rounded-3 shadow-sm"
            placeholder="Enter SMS message here...">{{ old('message', $smsTemplate->message ?? 
"Dear {name},

Please find booking confirmation for job reference: {job_ref}

Job Date: {job_date}
Job Time: {job_time}
Phone No: {mobile}
Pick up: {pickup}
Via address: {via_address}
Drop off: {destination}
Flight No: {flight_no}
Vehicle Type: {vehicle_type}
Fare: {fare}
Parking: {car_park}
Payment Type: {payment}

Please let us know in case of any changes in your schedule.

Kind Regards,
Crown Carz Ltd.
Tel: +44(0)1189 47 47 47
Email: info@crowncarz.com
Website: www.crowncarz.com") }}</textarea>
        </div>

        {{-- Info Section --}}
        <div class="alert alert-info small mt-3">
          <strong>Available Variables:</strong><br>
          <code>{name}</code> — Passenger Name <br>
          <code>{job_ref}</code> — Job Reference <br>
          <code>{job_date}</code> — Job Date <br>
          <code>{job_time}</code> — Job Time <br>
          <code>{mobile}</code> — Phone Number <br>
          <code>{pickup}</code> — Pickup <br>
          <code>{via_address}</code> — Via Address <br>
          <code>{destination}</code> — Dropoff <br>
          <code>{flight_no}</code> — Flight Number <br>
          <code>{vehicle_type}</code> — Vehicle Type <br>
          <code>{fare}</code> — Fare <br>
          <code>{car_park}</code> — Parking <br>
          <code>{payment}</code> — Payment Type <br>
        </div>

        {{-- Preview --}}
        <div class="mt-4">
          <label class="form-label fw-semibold">Live Preview</label>
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-light" style="font-family: monospace;">
            <div id="smsPreview" class="text-dark"></div>
          </div>
        </div>

        {{-- Submit Button --}}
        <div class="d-flex justify-content-end mt-4">
          <button type="submit" class="btn text-white fw-semibold px-4 py-2 rounded-3 shadow-sm" style="background-color:#B87333;">
            <i class="bi bi-save me-1"></i> Save SMS Template
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

{{-- Preview Script --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
  const textarea = document.getElementById("smsMessage");
  const preview  = document.getElementById("smsPreview");

  const sampleData = {
    "{name}": "John Doe",
    "{job_ref}": "CC-58293",
    "{job_date}": "12 Nov 2025",
    "{job_time}": "10:30 AM",
    "{mobile}": "+447912345678",
    "{pickup}": "Heathrow Airport",
    "{via_address}": "Stop 1, Oxford Street",
    "{destination}": "Reading Station",
    "{flight_no}": "BA044",
    "{vehicle_type}": "Saloon",
    "{fare}": "£70",
    "{car_park}": "£5",
    "{payment}": "Card"
  };

  function updatePreview() {
    let text = textarea.value;
    for (let key in sampleData) {
      text = text.replaceAll(key, sampleData[key]);
    }
    preview.innerText = text;
  }

  textarea.addEventListener("input", updatePreview);
  updatePreview();
});
</script>
@endsection
