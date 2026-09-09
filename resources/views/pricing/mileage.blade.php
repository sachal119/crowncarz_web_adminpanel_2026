{{-- @extends('layouts.app')

@section('content')
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="card shadow rounded-4 border-0">
        <div class="card-header text-white" style="background-color: #F3D166;">
          <h5 class="mb-0" style="color: #4B3621;">Mileage Pricing</h5>
        </div>

        <div class="card-body" style="background-color: #FAF0C3;">
          <form action="{{ route('pricing.mileage.save') }}" method="POST" class="row g-3 mb-4">
            @csrf
            <div class="col-md-3">
              <label class="form-label">From Miles</label>
              <input type="number" step="0.1" name="from_miles" class="form-control" placeholder="e.g. 0" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">To Miles</label>
              <input type="number" step="0.1" name="to_miles" class="form-control" placeholder="e.g. 10" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Price (£)</label>
              <input type="number" step="0.01" name="price" class="form-control" placeholder="e.g. 5.00" required>
            </div>
            <div class="col-md-3 d-flex align-items-end">
              <button type="submit" class="btn w-100" style="background-color: #B87333; color: white;">Add Pricing</button>
            </div>
          </form>

          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead style="background-color: #E6B04A; color: #fff;">
                <tr>
                  <th scope="col">From (miles)</th>
                  <th scope="col">To (miles)</th>
                  <th scope="col">Price (£)</th>
                </tr>
              </thead>
              <tbody>
                @forelse($mileagePrices as $m)
                  <tr style="background-color: #FFF9E5;">
                    <td>{{ number_format($m->from_miles, 1) }}</td>
                    <td>{{ number_format($m->to_miles, 1) }}</td>
                    <td>£{{ number_format($m->price, 2) }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" class="text-center text-muted">No pricing records available.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection --}}
@extends('layouts.app')

<style>
    .cc-pagination-btn {
        padding: 10px 22px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 14px;
        transition: 0.2s ease-in-out;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        cursor: pointer;
    }

    .cc-pagination-btn:hover:not(.disabled):not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .cc-prev {
        background: #6c757d;
        color: white;
        border: none;
    }

    .cc-prev:hover:not(.disabled):not(:disabled) {
        color: white;
        background: #5a6268;
    }

    .cc-next {
        background: #FAD788;
        color: #6B3E26;
        border: none;
    }

    .cc-next:hover:not(.disabled):not(:disabled) {
        color: #6B3E26;
        background: #f7cb67;
    }

    .cc-prev.disabled,
    .cc-prev:disabled,
    .cc-next.disabled,
    .cc-next:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        box-shadow: none !important;
        pointer-events: none;
    }
</style>

@section('content')
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-lg-12">
      <div class="card shadow rounded-4 border-0">
        <div class="card-header text-white" style="background-color: #F3D166;">
          <h5 class="mb-0" style="color: #4B3621;">Mileage Pricing (Postcode to Postcode)</h5>
        </div>

        <div class="card-body" style="background-color: #FAF0C3;">
          {{-- Save Pricing Form --}}
          <form action="{{ route('pricing.mileage.save') }}" method="POST" id="mileageForm">
            @csrf
             <div class="row g-2 mb-3">
      <div class="col-md-6">
        <label class="form-label">Minimum Price (£)</label>
        <input type="number" min="10" name="minimum_price" class="form-control no-spinner" required>
      </div>
      <!--<div class="col-md-6">-->
      <!--  <label class="form-label">Select Car Type</label>-->
      <!--  <select name="car_type" class="form-control" required>-->
      <!--    <option value="" disabled selected>-- Select Car Type --</option>-->
      <!--    <option value="Saloon">Saloon</option>-->
      <!--    <option value="Estate">Estate</option>-->
      <!--    <option value="6 Seater">6 Seater</option>-->
      <!--    <option value="7 to 8 Seater">7 to 8 Seater</option>-->
      <!--    <option value="10 to 12 Seater">10 to 12 Seater</option>-->
      <!--  </select>-->
      <!--</div>-->
      <div class="col-md-6">
    <label class="form-label">Select Car Type</label>
    <select name="car_type" class="form-control" required>
        <option value="" disabled selected>-- Select Car Type --</option>
        <option value="Saloon">Saloon</option>
        <option value="Estate">Estate</option>
        <option value="MPV">MPV</option>
        <option value="8 Seater">8 Seater</option>
        <option value="Executive">Executive</option>
    </select>
</div>

    </div>

           <div id="mileageRows">
  <div class="row g-2 mb-3 mileage-row flex-nowrap overflow-auto">
    <!-- From Mileage -->
    <div class="col-auto">
      <label class="form-label">From Mileage</label>
      <input type="number" step="0.01" name="from_mileage[]" class="form-control" placeholder="e.g. 0" required>
    </div>

    <!-- To Mileage -->
    <div class="col-auto">
      <label class="form-label">To Mileage</label>
      <input type="number" step="0.01" name="to_mileage[]" class="form-control" placeholder="e.g. 33.99" required>
    </div>

    <!-- Cost per Mile -->
    <div class="col-auto">
      <label class="form-label">Cost per Mileage (£)</label>
      <input type="number" step="0.01" name="cost_per_mileage[]" class="form-control" placeholder="e.g. 1.20" required>
    </div>

    <div class="col-auto d-flex align-items-end">
      <button type="button" class="btn btn-danger removeRow">Remove</button>
    </div>
  </div>
</div>



            <div class="mb-3">
              <button type="button" id="addRow" class="btn btn-success">+ Add Another Row</button>
            </div>

            <div class="mb-4">
              <button type="submit" class="btn w-100" style="background-color: #B87333; color: white;">Save All Pricing</button>
            </div>
            
          </form>
          
          <div class="table-responsive">
    <table class="table table-bordered align-middle">
        <thead style="background-color: #FAD788; color: #6B3E26;">
            <tr>
                <th>Car Type</th>
                <th>Mileage Bracket</th>
                <th>Cost Per Mile (£)</th>
                <th>Minimum Price (£)</th>
                 <th>Action</th>
                <!--<th>Record Key (ID)</th> <th>Created At</th>-->
            </tr>
        </thead>
        <tbody>
            @forelse($mileagePrices as $id => $m)
                <tr style="background-color: #FFF9E5;">
                    <td>{{ $m['car_type'] ?? '-' }}</td>
                   <td>{{ ($m['from_mileage'] ?? '-') . ' – ' . ($m['to_mileage'] ?? '-') }}</td>
                    <td>£{{ number_format($m['cost_per_mileage'] ?? 0, 2) }}</td>
                    <td>£{{ number_format($m['minimum_price'] ?? 0, 2) }}</td>
                     <td>
                        <!--<button class="btn btn-sm btn-warning px-3 edit-btn"-->
                        <!--    data-id="{{ $id }}"-->
                        <!--    data-car-type="{{ $m['car_type'] ?? '-' }}"-->
                        <!--    data-mileage="{{ ($m['from_mileage'] ?? '-') . ' – ' . ($m['to_mileage'] ?? '-') }}"-->
                        <!--    data-cost="{{ $m['cost_per_mileage'] ?? 0 }}"-->
                        <!--    data-min="{{ $m['minimum_price'] ?? 0 }}">-->
                        <!--    <i class="bi bi-pencil-square"></i> Edit-->
                        <!--</button>-->
<!--                        <button class="btn btn-sm btn-warning px-3 edit-btn"-->
<!--        data-id="{{ $id }}"-->
<!--        data-car-type="{{ $m['car_type'] ?? '-' }}"-->
<!--        data-from-mileage="{{ $m['from_mileage'] ?? '' }}"-->
<!--        data-to-mileage="{{ $m['to_mileage'] ?? '' }}"-->
<!--        data-mileage="{{ ($m['from_mileage'] ?? '-') . ' – ' . ($m['to_mileage'] ?? '-') }}"-->
<!--        data-cost="{{ $m['cost_per_mileage'] ?? 0 }}"-->
<!--        data-min="{{ $m['minimum_price'] ?? 0 }}">-->
<!--        <i class="bi bi-pencil-square"></i> Edit-->
<!--</button>-->
<button class="btn btn-sm btn-warning px-3 edit-btn"
    data-id="{{ $id }}"
    data-car-type="{{ $m['car_type'] ?? '-' }}"
    data-from-mileage="{{ isset($m['from_mileage']) ? $m['from_mileage'] : '' }}"
    data-to-mileage="{{ isset($m['to_mileage']) ? $m['to_mileage'] : '' }}"
    data-cost="{{ isset($m['cost_per_mileage']) ? $m['cost_per_mileage'] : '' }}"
    data-min="{{ isset($m['minimum_price']) ? $m['minimum_price'] : '' }}">
    <i class="bi bi-pencil-square"></i> Edit
</button>

<!-- Delete Button -->
                    <button class="btn btn-sm btn-danger px-3 delete-btn"
                        data-id="{{ $id }}">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                    </td>
                    
                    <!--<td><small>{{ $id }}</small></td>-->
                    <!--<td>{{ $m['created_at'] ?? '-' }}</td>-->
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No mileage pricing records available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex flex-column align-items-center mt-4">
      @if($totalCount > 0)
        <div class="mb-3 text-muted" style="font-size: 14px;">
          Showing <strong>{{ $from }}</strong> – <strong>{{ $to }}</strong> of <strong>{{ $totalCount }}</strong> records (Page {{ $page }} of {{ $mileagePaginator->lastPage() }})
        </div>
      @endif

      <div class="d-flex justify-content-center gap-3">
        {{-- Previous --}}
        @if($mileagePaginator->previousPageUrl())
            <a href="{{ $mileagePaginator->previousPageUrl() }}" class="cc-pagination-btn cc-prev">
                <i class="bi bi-arrow-left-circle me-1"></i> Previous
            </a>
        @else
            <button class="cc-pagination-btn cc-prev disabled" disabled>
                <i class="bi bi-arrow-left-circle me-1"></i> Previous
            </button>
        @endif

        {{-- Next --}}
        @if($mileagePaginator->nextPageUrl())
            <a href="{{ $mileagePaginator->nextPageUrl() }}" class="cc-pagination-btn cc-next">
                Next <i class="bi bi-arrow-right-circle ms-1"></i>
            </a>
        @else
            <button class="cc-pagination-btn cc-next disabled" disabled>
                Next <i class="bi bi-arrow-right-circle ms-1"></i>
            </button>
        @endif
      </div>
    </div>

</div>

<!-- Edit Modal -->
<div class="modal fade" id="editMileageModal" tabindex="-1" aria-labelledby="editMileageLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content rounded-3 shadow">
      <form id="editMileageForm" action="{{ url('/mileage/update') }}" method="POST">
        @csrf
        <input type="hidden" id="recordId" name="id">
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-semibold text-dark" id="editMileageLabel">Edit Mileage Price</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Car Type</label>
            <input type="text" id="carType" class="form-control" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">From Mileage</label>
            <input type="number" id="fromMileage" class="form-control" name="from_mileage" min="0" step="0.01" required>
          </div>
          <div class="mb-3">
            <label class="form-label">To Mileage</label>
            <input type="number" id="toMileage" class="form-control" name="to_mileage" min="0" step="0.01" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Minimum Price (£)</label>
            <input type="number" step="0.01" id="minPrice" class="form-control" name="minimum_price" min="0" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Cost Per Mile (£)</label>
            <input type="number" step="0.01" name="cost_per_mileage" id="costPerMile" class="form-control border-primary" min="0" required>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

        </div>
      </div>
    </div>
  </div>
</div>

{{-- JavaScript for Dynamic Rows --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
  const rowsContainer = document.getElementById("mileageRows");
  const addRowBtn = document.getElementById("addRow");

  // 🟩 Add new row
  addRowBtn.addEventListener("click", function () {
    const lastRow = rowsContainer.querySelector(".mileage-row:last-child");
    const newRow = lastRow.cloneNode(true);

    // Reset all inputs in cloned row
    newRow.querySelectorAll("input").forEach(input => input.value = "");

    // Get last "To Mileage" value
    const lastTo = parseFloat(lastRow.querySelector("input[name='to_mileage[]']").value);

    // Autofill next "From Mileage" = lastTo + 0.01 (continuous range)
    if (!isNaN(lastTo)) {
      const nextFrom = (lastTo + 0.01).toFixed(2);
      newRow.querySelector("input[name='from_mileage[]']").value = nextFrom;
    }

    rowsContainer.appendChild(newRow);
  });

  // 🟥 Remove a row
  document.addEventListener("click", function (e) {
    if (e.target.classList.contains("removeRow")) {
      const allRows = document.querySelectorAll(".mileage-row");
      if (allRows.length > 1) {
        e.target.closest(".mileage-row").remove();
      } else {
        alert("You must keep at least one row.");
      }
    }
  });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const editButtons = document.querySelectorAll(".edit-btn");
    const modalElement = document.getElementById("editMileageModal");
    const modal = new bootstrap.Modal(modalElement);
    const recordIdInput = document.getElementById("recordId");
    const carTypeInput = document.getElementById("carType");
    const fromMileageInput = document.getElementById("fromMileage");
    const toMileageInput = document.getElementById("toMileage");
    const minPriceInput = document.getElementById("minPrice");
    const costPerMileInput = document.getElementById("costPerMile");
    
    
    
    console.log(fromMileageInput);
    console.log(toMileageInput);
    console.log(minPriceInput);
    console.log(costPerMileInput);
    
    
    // Open modal with data
    editButtons.forEach(btn => {
        btn.addEventListener("click", function () {
            recordIdInput.value = this.dataset.id;
            carTypeInput.value = this.dataset.carType;
            // fromMileageInput.value = this.dataset.fromMileage || '';
            // toMileageInput.value = this.dataset.toMileage || '';
            // minPriceInput.value = this.dataset.min || '';
            // costPerMileInput.value = this.dataset.cost || '';
            
            fromMileageInput.value = this.dataset.fromMileage ?? '';
toMileageInput.value = this.dataset.toMileage ?? '';
minPriceInput.value = this.dataset.min ?? '';
costPerMileInput.value = this.dataset.cost ?? '';
            modal.show();
        });
    });
    
    // Save button (POST request)
    document.getElementById("saveMileageBtn")?.addEventListener("click", async function (e) {
        e.preventDefault();
        const id = recordIdInput.value;
        const fromMileage = parseInt(fromMileageInput.value);
        const toMileage = parseInt(toMileageInput.value);
        const minPrice = parseFloat(minPriceInput.value);
        const cost = parseFloat(costPerMileInput.value);
        
        if (isNaN(cost) || cost < 0) {
            alert("⚠️ Please enter a valid cost per mile.");
            return;
        }
        if (isNaN(minPrice) || minPrice < 0) {
            alert("⚠️ Please enter a valid minimum price.");
            return;
        }
        if (isNaN(fromMileage) || fromMileage < 0 || isNaN(toMileage) || toMileage < 0 || fromMileage >= toMileage) {
            alert("⚠️ Please enter valid from and to mileage values (from < to).");
            return;
        }
        
        try {
            const response = await fetch(`/admin/mileage/update/${id}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    from_mileage: fromMileage,
                    to_mileage: toMileage,
                    minimum_price: minPrice,
                    cost_per_mileage: cost
                })
            });
            const data = await response.json();
            if (data.success) {
                modal.hide();
                alert("✅ Mileage price updated successfully!");
                window.location.reload();
            } else {
                alert("❌ Update failed: " + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error("Error updating:", error);
            alert("⚠️ Something went wrong while saving.");
        }
    });
});
</script>


<script>
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.delete-btn');
    if (!btn) return;

    const id = btn.dataset.id;
    if (!id) return;

    if (confirm('Are you sure you want to delete this record?')) {
        fetch(`/admin/mileage/delete/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message || 'Failed to delete record.');
            }
        })
        .catch(() => alert('Something went wrong!'));
    }
});
</script>




@endsection

