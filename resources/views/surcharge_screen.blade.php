@extends('layouts.app')

@section('content')
<style>
/* 🌟 Premium Look & Feel */
.card {
  border: none;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.card-header {
  background: linear-gradient(135deg, #6B3E26, #C69C6D);
  color: #fff;
  border-top-left-radius: 16px;
  border-top-right-radius: 16px;
  padding: 1rem 1.25rem;
}
.btn-custom {
  background-color: #6B3E26;
  color: #fff;
  border-radius: 8px;
  transition: 0.3s;
}
.table-hover tbody tr:hover {
  background-color: rgba(107, 62, 38, 0.05);
}
.table thead {
  background-color: rgba(107, 62, 38, 0.1);
}
.modal-content {
  border-radius: 16px;
  box-shadow: 0 6px 24px rgba(0,0,0,0.1);
}
.modal-header {
  background-color: #6B3E26;
  color: #fff;
  border-top-left-radius: 16px;
  border-top-right-radius: 16px;
}
.modal-footer button {
  border-radius: 8px;
}
.badge-custom {
  background-color: #C69C6D;
  color: #fff;
  font-size: 0.85rem;
}
.btn-custom:hover {
    background-color: #8B573A; /* slightly lighter brown for hover */
    color: white !important;   /* ensures text stays white */
  }
.fade-in {
  animation: fadeIn 0.4s ease-in-out;
}
@keyframes fadeIn {
  from {opacity: 0; transform: translateY(10px);}
  to {opacity: 1; transform: translateY(0);}
}
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
  <h4 class="fw-bold text-dark mb-0">⚙️ Manage Surcharges</h4>
  <button class="btn btn-custom shadow-sm" data-bs-toggle="modal" data-bs-target="#addSurchargeModal">
    ➕ Add New Surcharge
  </button>
</div>

<div class="card fade-in">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">📊 Active Surcharges</h5>
    <span class="badge badge-custom">{{ count($surcharges) }} Total</span>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0 text-center">
      <thead class="table-light">
        <tr>
          <th>From Date</th>
          <th>To Date</th>
          <th>From Time</th>
          <th>To Time</th>
          <th>Pickup</th>
          <th>Dropoff</th>
          <th>Surcharge (%)</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($surcharges as $key => $item)
          <tr>
            <td>{{ $item['from_date'] ?? '-' }}</td>
            <td>{{ $item['to_date'] ?? '-' }}</td>
            <td>{{ $item['from_time'] ?? '-' }}</td>
            <td>{{ $item['to_time'] ?? '-' }}</td>
            <td><span class="badge bg-secondary">{{ $item['pickup'] ?? '-' }}</span></td>
            <td><span class="badge bg-secondary">{{ $item['dropoff'] ?? '-' }}</span></td>
            <td><strong class="text-success">{{ $item['surcharge'] ?? 0 }}%</strong></td>
            <td>
              <button class="btn btn-sm btn-outline-success edit-btn"
                      data-id="{{ $key }}"
                      data-surcharge="{{ $item['surcharge'] ?? '' }}"
                      data-from_date="{{ $item['from_date'] ?? '' }}"
                      data-to_date="{{ $item['to_date'] ?? '' }}"
                      data-from_time="{{ $item['from_time'] ?? '' }}"
                      data-to_time="{{ $item['to_time'] ?? '' }}"
                      data-pickup="{{ $item['pickup'] ?? '' }}"
                      data-dropoff="{{ $item['dropoff'] ?? '' }}">
                ✏️
              </button>
              <button class="btn btn-sm btn-outline-danger delete-btn" data-id="{{ $key }}">
                🗑️
              </button>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="text-muted py-4">No surcharge records found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- 🟢 Add Modal -->
<div class="modal fade" id="addSurchargeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="mb-0">➕ Add New Surcharge</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="addSurchargeForm">
          <div class="mb-3">
            <label class="form-label fw-semibold">Surcharge %</label>
            <input type="number" id="surcharge" class="form-control" required>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">From Date</label>
              <input type="date" id="from_date" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">To Date</label>
              <input type="date" id="to_date" class="form-control" required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">From Time</label>
              <input type="time" id="from_time" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">To Time</label>
              <input type="time" id="to_time" class="form-control" required>
            </div>
          </div>
          <div class="form-check form-switch mb-3">
  <input class="form-check-input" type="checkbox" id="overallToggle">
  <label class="form-check-label fw-semibold" for="overallToggle">
    Apply Surcharge to Entire UK (Overall)
  </label>
</div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Pickup Postcode</label>
              <input type="text" id="pickup" class="form-control text-uppercase" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Dropoff Postcode</label>
              <input type="text" id="dropoff" class="form-control text-uppercase" required>
            </div>
          </div>
          <div class="text-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-custom">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ✏️ Edit Modal -->
<div class="modal fade" id="editSurchargeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5>Edit Surcharge</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editSurchargeForm">
          <input type="hidden" id="edit_id">

          <div class="mb-3">
            <label class="form-label fw-semibold">Surcharge %</label>
            <input type="number" id="edit_surcharge" class="form-control" required>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">From Date</label>
              <input type="date" id="edit_from_date" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">To Date</label>
              <input type="date" id="edit_to_date" class="form-control" required>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">From Time</label>
              <input type="time" id="edit_from_time" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">To Time</label>
              <input type="time" id="edit_to_time" class="form-control" required>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Pickup</label>
              <input type="text" id="edit_pickup" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Dropoff</label>
              <input type="text" id="edit_dropoff" class="form-control" required>
            </div>
          </div>

          <div class="text-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- 🧠 JS Section -->
<script>
    
    const overallToggle = document.getElementById('overallToggle');
const pickupInput = document.getElementById('pickup');
const dropoffInput = document.getElementById('dropoff');

overallToggle.addEventListener('change', () => {
  if (overallToggle.checked) {
    pickupInput.value = "OVERALL";
    dropoffInput.value = "OVERALL";

    pickupInput.setAttribute("disabled", true);
    dropoffInput.setAttribute("disabled", true);
  } else {
    pickupInput.value = "";
    dropoffInput.value = "";

    pickupInput.removeAttribute("disabled");
    dropoffInput.removeAttribute("disabled");
  }
});

document.getElementById('addSurchargeForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  

  const isOverall = overallToggle.checked;

  const data = {
    surcharge: parseFloat(document.getElementById('surcharge').value),
    from_date: document.getElementById('from_date').value,
    to_date: document.getElementById('to_date').value,
    from_time: document.getElementById('from_time').value,
    to_time: document.getElementById('to_time').value,
    pickup: isOverall ? "OVERALL" : document.getElementById('pickup').value.trim().toUpperCase(),
    dropoff: isOverall ? "OVERALL" : document.getElementById('dropoff').value.trim().toUpperCase()
  };

  try {
    const response = await fetch("{{ route('pricing.fixed.addSurcharge') }}", {
      method: 'POST',
      headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
      body: JSON.stringify(data)
    });
    const result = await response.json();
    
    console.log(result);

    if (result.success) {
      alert('✅ Surcharge added successfully!');
      location.reload();
    } else {
      alert('❌ Failed to add surcharge.');
      console.error(result);
    }
  } catch (err) {
    alert('⚠️ Network error. Please try again.');
    console.error(err);
  }
});

document.addEventListener('DOMContentLoaded', function() {
    
    
    
    let editFromPicker, editToPicker;

  // Initialize add form pickers
  flatpickr("#from_time", { enableTime: true, noCalendar: true, dateFormat: "H:i", time_24hr: true });
  flatpickr("#to_time", { enableTime: true, noCalendar: true, dateFormat: "H:i", time_24hr: true });

  // Edit buttons
  document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      // Fill form values
      document.getElementById('edit_id').value = btn.dataset.id;
      document.getElementById('edit_surcharge').value = btn.dataset.surcharge;
      document.getElementById('edit_from_date').value = btn.dataset.from_date;
      document.getElementById('edit_to_date').value = btn.dataset.to_date;
      document.getElementById('edit_from_time').value = btn.dataset.from_time;
      document.getElementById('edit_to_time').value = btn.dataset.to_time;
      document.getElementById('edit_pickup').value = btn.dataset.pickup;
      document.getElementById('edit_dropoff').value = btn.dataset.dropoff;

      // Destroy old pickers if exist
      if (editFromPicker) editFromPicker.destroy();
      if (editToPicker) editToPicker.destroy();

      // Initialize Flatpickr with current input values
      editFromPicker = flatpickr("#edit_from_time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        defaultDate: document.getElementById('edit_from_time').value
      });

      editToPicker = flatpickr("#edit_to_time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        defaultDate: document.getElementById('edit_to_time').value
      });

      new bootstrap.Modal(document.getElementById('editSurchargeModal')).show();
    });
  });
    
    
    
    
    
    
    
    
    
//   document.querySelectorAll('.edit-btn').forEach(btn => {
//     btn.addEventListener('click', () => {
//       document.getElementById('edit_id').value = btn.dataset.id;
//       document.getElementById('edit_surcharge').value = btn.dataset.surcharge;
//       document.getElementById('edit_from_date').value = btn.dataset.from_date;
//       document.getElementById('edit_to_date').value = btn.dataset.to_date;
//       document.getElementById('edit_from_time').value = btn.dataset.from_time;
//       document.getElementById('edit_to_time').value = btn.dataset.to_time;
//       document.getElementById('edit_pickup').value = btn.dataset.pickup;
//       document.getElementById('edit_dropoff').value = btn.dataset.dropoff;
//       new bootstrap.Modal(document.getElementById('editSurchargeModal')).show();
//     });
//   });

  document.getElementById('editSurchargeForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const id = document.getElementById('edit_id').value;
    const data = {
      surcharge: document.getElementById('edit_surcharge').value,
      from_date: document.getElementById('edit_from_date').value,
      to_date: document.getElementById('edit_to_date').value,
      from_time: document.getElementById('edit_from_time').value,
      to_time: document.getElementById('edit_to_time').value,
      pickup: document.getElementById('edit_pickup').value,
      dropoff: document.getElementById('edit_dropoff').value
    };
    
    console.log(data);
    const baseUrl = "{{ url('pricing/update-surcharge') }}";

const res = await fetch(`${baseUrl}/${id}`, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': '{{ csrf_token() }}'
  },
  body: JSON.stringify(data)
});
    let result;
    console.log(res);
try {
    result = await res.json();
} catch (err) {
    console.error("Not JSON:", err);
    alert("🔥 Server error — check Laravel logs");
    return;
}

    if (result.success) {
      alert('✅ Updated successfully!');
      location.reload();
    } else {
      alert('❌ Update failed.');
    }
  });

  document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
      if (!confirm('Are you sure you want to delete this surcharge?')) return;
      const baseUrl = "{{ url('pricing/delete-surcharge') }}";

const res = await fetch(`${baseUrl}/${btn.dataset.id}`, {
    //   const res = await fetch(`/admin/pricing/delete-surcharge/${btn.dataset.id}`, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
      });
      const result = await res.json();
      if (result.success) {
        alert('🗑️ Deleted successfully!');
        location.reload();
      } else {
        alert('❌ Delete failed.');
      }
    });
  });
});
</script>
<script>
flatpickr("#from_time", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});

flatpickr("#to_time", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});

// flatpickr("#edit_from_time", {
//     enableTime: true,
//     noCalendar: true,
//     dateFormat: "H:i",
//     time_24hr: true
// });

// flatpickr("#edit_to_time", {
//     enableTime: true,
//     noCalendar: true,
//     dateFormat: "H:i",
//     time_24hr: true
// });
</script>

@endsection
