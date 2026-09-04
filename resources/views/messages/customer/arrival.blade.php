@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4 px-2 px-md-4">
  <div class="card shadow-sm border-0 rounded-4 mx-auto" style="max-width: 900px;">
    
    {{-- Header --}}
    <div class="card-header bg-warning bg-opacity-25 rounded-top-4 py-3 px-4 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-bold text-dark">
        <i class="bi bi-truck-front-fill me-2 text-warning"></i> Arrival SMS
      </h5>
      <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    {{-- Body --}}
    <div class="card-body p-4">
      <p class="text-secondary mb-4">
        This SMS template is sent automatically to customers when the driver arrives at the pickup location.
        You can personalize the message using dynamic variables like 
        <code>{name}</code> and <code>{pickup}</code>.
      </p>

      <form action="{{ route('customer.arrival.update') }}" method="POST">
        @csrf
        @method('POST')

        {{-- SMS Message --}}
        <div class="mb-3">
          <label class="form-label fw-semibold">SMS Message</label>
          <textarea 
            name="message" 
            id="smsMessage" 
            rows="6" 
            class="form-control rounded-3 shadow-sm"
          >{{ old('message', $template['message'] ?? "default text") }}</textarea>
        </div>

        {{-- Info Section --}}
        <div class="alert alert-info small mt-3">
          <strong>Available Variables:</strong><br>
          <code>{name}</code> = Passenger Name <br>
          <code>{pickup}</code> = Pickup Location <br>
        </div>

        {{-- Preview Area --}}
        <div class="mt-4">
          <label class="form-label fw-semibold">Live Preview</label>
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-light" style="font-family: monospace;">
            <div id="smsPreview" class="text-dark">
              Dear John Doe,
              
              This is your driver. Just to let you know that I am at Heathrow Airport.
              
              For all future bookings please call on:
              +44(0)1189 47 47 47
              Email: info@crowncarz.com
            </div>
          </div>
        </div>

        {{-- Submit Button --}}
        <div class="d-flex justify-content-end mt-4">
          <button type="submit" class="btn text-white fw-semibold px-4 py-2 rounded-3 shadow-sm" style="background-color: #B87333;">
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
    "{pickup}": "Heathrow Airport",
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
