@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4 px-2 px-md-4">
  <div class="card shadow-sm border-0 rounded-4 mx-auto" style="max-width: 900px;">
    {{-- Header --}}
    <div class="card-header bg-info bg-opacity-25 rounded-top-4 py-3 px-4 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-bold text-dark">
        <i class="bi bi-car-front-fill me-2 text-info"></i> Onroute SMS
      </h5>
      <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    {{-- Body --}}
    <div class="card-body p-4">
      <p class="text-secondary mb-4">
        This SMS is sent automatically when the driver is on the way to pick up the customer.
        You can customize the message below using placeholders like 
        <code>{name}</code>, <code>{driver_name}</code>, or <code>{pickup_address}</code>.
      </p>

      <form action="{{ route('customer.onroute.update') }}" method="POST">
        @csrf
        @method('POST')

        {{-- SMS Message --}}
        <div class="mb-3">
          <label class="form-label fw-semibold">SMS Message</label>
          <textarea name="message" id="smsMessage" rows="5"
                    class="form-control rounded-3 shadow-sm"
                    placeholder="Type your SMS message here...">{{ old('message', $smsTemplate->message ?? 'Dear {name}, your driver {driver_name} is on the way to {pickup_address}. Please be ready for pickup. Thank you for choosing CrownCarz!') }}</textarea>
        </div>

        {{-- Info Section --}}
        <div class="alert alert-info small mt-3">
          <strong>Available Variables:</strong><br>
          <code>{name}</code> = Passenger Name <br>
          <code>{driver_name}</code> = Driver’s Name <br>
          <code>{pickup_address}</code> = Pickup Location
        </div>

        {{-- Preview Area --}}
        <div class="mt-4">
          <label class="form-label fw-semibold">Live Preview</label>
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-light" style="font-family: monospace;">
            <div id="smsPreview" class="text-dark">
              Dear John Doe, your driver Ali Khan is on the way to Heathrow Airport. Please be ready for pickup. Thank you for choosing CrownCarz!
            </div>
          </div>
        </div>

        {{-- Submit Button --}}
        <div class="d-flex justify-content-end mt-4">
          <button type="submit" class="btn text-white fw-semibold px-4 py-2 rounded-3 shadow-sm" style="background-color: #007bff;">
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
    "{name}": "John Doe",
    "{driver_name}": "Ali Khan",
    "{pickup_address}": "Heathrow Airport"
  };

  const updatePreview = () => {
    let text = textarea.value;
    for (const key in sampleData) {
      text = text.replaceAll(key, sampleData[key]);
    }
    preview.innerText = text;
  };

  textarea.addEventListener("input", updatePreview);
  updatePreview(); // show preview on page load
});
</script>
@endsection

