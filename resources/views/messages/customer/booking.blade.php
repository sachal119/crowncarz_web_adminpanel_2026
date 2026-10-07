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
          <textarea name="message" id="smsMessage" rows="14"
            class="form-control rounded-3 shadow-sm"
            placeholder="Enter SMS message here...">{{ old('message', $smsTemplate->message ?? 
"Dear {name},

Please find booking confirmation for job reference: {job_ref}

Job Date: {job_date}
Job Time: {job_time}
Phone No: {mobile}
Pick up: {pickup}
{via_address}Drop off: {destination}
Flight No: {flight_no}
Vehicle Type: {vehicle_type}
Base Fare: {fare}
Parking: {car_park}
Payment Type: {payment}

Please let us know in case of any changes in your schedule.
To download our app, Leave a review or visit the website, please tap the link below:

https://linktr.ee/crowncarz

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
          <code>{job_date}</code> — Job Date (e.g. 07/Oct/2026) <br>
          <code>{job_time}</code> — Job Time (e.g. 06:00 PM) <br>
          <code>{mobile}</code> — Phone Number <br>
          <code>{pickup}</code> — Pick up Address <br>
          <code>{via_address}</code> — Via Address(es) (e.g. Via 1: ..., Via 2: ...) <br>
          <code>{destination}</code> — Drop off Address <br>
          <code>{flight_no}</code> — Flight Number <br>
          <code>{vehicle_type}</code> — Vehicle Type <br>
          <code>{fare}</code> — Base Fare <br>
          <code>{car_park}</code> — Parking <br>
          <code>{payment}</code> — Payment Type (Pay in Car / Payment Received / Account) <br>
        </div>

        {{-- Preview --}}
        <div class="mt-4">
          <label class="form-label fw-semibold">Live Preview</label>
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-light" style="font-family: monospace;">
            <div id="smsPreview" class="text-dark" style="white-space: pre-wrap;"></div>
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
    "{name}": "Neil, Peter and Ian",
    "{job_ref}": "CCZ52802",
    "{job_date}": "07/Oct/2026",
    "{job_time}": "06:00 PM",
    "{mobile}": "07798605040",
    "{pickup}": "THE BOTHY, GODDARDS FARM, GODDARDS LANE, HOOK, RG27 0EL",
    "{via_address}": "Via 1: 7, NORTHFIELD ROAD, HOOK, RG27 0DR\nVia 2: 100A, GRAZELEY ROAD, READING, RG7 1BJ\n",
    "{destination}": "Heathrow Airport (LHR) Terminal 5, HOUNSLOW TW6",
    "{flight_no}": "",
    "{vehicle_type}": "MPV",
    "{fare}": "130",
    "{car_park}": "7.00",
    "{payment}": "Payment Received"
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
