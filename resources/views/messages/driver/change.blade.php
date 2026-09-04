@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4 px-2 px-md-4">
  <div class="card shadow-sm border-0 rounded-4 mx-auto" style="max-width: 900px;">
    
    {{-- Header --}}
    <div class="card-header bg-info bg-opacity-25 rounded-top-4 py-3 px-4 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-bold text-dark">
        <i class="bi bi-arrow-repeat me-2 text-info"></i> Job Change SMS
      </h5>
      <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    {{-- Body --}}
    <div class="card-body p-4">
      <p class="text-secondary mb-4">
        This SMS is sent automatically to the <strong>driver</strong> whenever a job is updated or reassigned.
        You can customize this message using placeholders like 
        <code>{driver_name}</code>, <code>{pickup_address}</code>, <code>{pickup_time}</code>, or <code>{job_id}</code>.
      </p>

      <form action="{{ route('messages.driver.change.update') }}" method="POST">
        @csrf
        @method('POST')

        {{-- SMS Message --}}
        <div class="mb-3">
          <label class="form-label fw-semibold">SMS Message</label>
          <textarea name="message" id="smsMessage" rows="5"
                    class="form-control rounded-3 shadow-sm"
                    placeholder="Type your SMS message here...">{{ old('message', $smsTemplate->message ?? 'Hello {driver_name}, job #{job_id} has been updated. Pickup from {pickup_address} at {pickup_time}. Please review the new details in your CrownCarz driver app.') }}</textarea>
        </div>

        {{-- Info Section --}}
        <div class="alert alert-info small mt-3">
          <strong>Available Variables:</strong><br>
          <code>{driver_name}</code> = Driver Name <br>
          <code>{job_id}</code> = Job ID or Reference Number <br>
          <code>{pickup_address}</code> = Pickup Location <br>
          <code>{pickup_time}</code> = Pickup Date & Time
        </div>

        {{-- Live Preview --}}
        <div class="mt-4">
          <label class="form-label fw-semibold">Live Preview</label>
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-light" style="font-family: monospace;">
            <div id="smsPreview" class="text-dark">
              Hello Ali Khan, job #1024 has been updated. Pickup from Heathrow Airport at 09:30 AM. Please review the new details in your CrownCarz driver app.
            </div>
          </div>
        </div>

        {{-- Submit Button --}}
        <div class="d-flex justify-content-end mt-4">
          <button type="submit" class="btn text-white fw-semibold px-4 py-2 rounded-3 shadow-sm" style="background-color: #0dcaf0;">
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
    "{driver_name}": "Ali Khan",
    "{job_id}": "1024",
    "{pickup_address}": "Heathrow Airport",
    "{pickup_time}": "09:30 AM"
  };

  const updatePreview = () => {
    let text = textarea.value;
    for (const key in sampleData) {
      text = text.replaceAll(key, sampleData[key]);
    }
    preview.innerText = text;
  };

  textarea.addEventListener("input", updatePreview);
  updatePreview(); // Initial load
});
</script>
@endsection
