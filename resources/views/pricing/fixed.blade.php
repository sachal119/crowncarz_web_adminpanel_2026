{{-- @extends('layouts.app')

@section('content')
<div class="container mt-4">
  <div class="card shadow-sm border-0" style="background-color: #FFFBE6; border-left: 5px solid #AEB7BF;">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold" style="color: #6B3E26;">Fixed Pricing (Postcode to Postcode)</h4>
      </div>

      <form action="{{ route('pricing.fixed.import') }}" method="POST" enctype="multipart/form-data" class="row g-2 mb-4 align-items-center">
        @csrf
        <div class="col-auto">
          <input type="file" name="file" class="form-control form-control-sm" required>
        </div>
        <div class="col-auto">
          <button class="btn btn-sm" style="background-color: #AEB7BF; color: white;">
            Upload
          </button>
        </div>
        <div class="col-auto">
          <a href="{{ route('pricing.fixed.export') }}" class="btn bg-warning bg-opacity-50 btn-sm">Download CSV</a>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-hover table-borderless align-middle">
          <thead style="background-color: #D7E0E6; color: #6B3E26;">
            <tr>
              <th>From</th>
              <th>To</th>
              <th>Price (£)</th>
            </tr>
          </thead>
          <tbody style="background-color: #EAFDFD;">
            @forelse($fixedPrices as $fp)
              <tr style="border-bottom: 1px solid #D9E1E7;">
                <td>{{ $fp->from_postcode }}</td>
                <td>{{ $fp->to_postcode }}</td>
                <td>£{{ number_format($fp->price, 2) }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center text-muted py-3">No fixed pricing data available.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection --}}

{{-- @extends('layouts.app')

@section('content')
<div class="container mt-4">
  <div class="card shadow-sm border-0" style="background-color: #FFFBE6; border-left: 5px solid #AEB7BF;">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold" style="color: #6B3E26;">Fixed Pricing (Postcode to Postcode)</h4>
      </div>

      
      <form action="{{ route('pricing.fixed.import') }}" method="POST" enctype="multipart/form-data" class="row g-2 mb-4 align-items-center">
        @csrf
        <div class="col-auto">
          <input type="file" name="file" class="form-control form-control-sm" required>
        </div>
        <div class="col-auto">
          <button class="btn btn-sm" style="background-color: #AEB7BF; color: white;">
            Upload
          </button>
        </div>
        <div class="col-auto">
          <a href="{{ route('pricing.fixed.export') }}" class="btn bg-warning bg-opacity-50 btn-sm">Download CSV</a>
        </div>
      </form>

     
      <div class="table-responsive">
        <table class="table table-hover table-borderless align-middle">
          <thead style="background-color: #D7E0E6; color: #6B3E26;">
            <tr>
              <th>From</th>
              <th>To</th>
              <th>Saloon Price (£)</th>
              <th>6 Seater Price (£)</th>
              <th>7 to 8 Seater Price (£)</th>
              <th>10 to 12 Seater Price (£)</th>
            </tr>
          </thead>
          <tbody style="background-color: #EAFDFD;">
            @forelse($fixedPrices as $fp)
              <tr style="border-bottom: 1px solid #D9E1E7;">
                <td>{{ $fp->from_postcode }}</td>
                <td>{{ $fp->to_postcode }}</td>
                <td>£{{ number_format($fp->saloon_fare, 2) }}</td>
                <td>£{{ number_format($fp->estate_fare, 2) }}</td>
                <td>£{{ number_format($fp->seater6_fare, 2) }}</td>
                <td>£{{ number_format($fp->seater7_8_fare, 2) }}</td>
                <td>£{{ number_format($fp->seater10_12_fare, 2) }}</td>
                <!--<td>{{ $fp->type }}</td>-->
                <!--<td>{{ $fp->vehicle_type }}</td>-->
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-3">No fixed pricing data available.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
 --}}


 @extends('layouts.app')

 <style>
  .pagination {
    margin: 0;
    padding: 0;
}

.pagination .page-link {
    color: #6B3E26;
    border: 1px solid #D7E0E6;
    background-color: #EAFDFD;
    margin: 0 8px;
    /*border-radius: 8px;*/
    transition: all 0.2s ease-in-out;
}

.pagination .page-link:hover {
    background-color: #D7E0E6;
    color: #fff;
}

.pagination .active .page-link {
    background-color: #6B3E26;
    border-color: #6B3E26;
    color: #fff;
}

/* Keep Bootstrap dialogs above the admin layout's stacking contexts. */
body > .modal {
    z-index: 2055 !important;
}

.modal-backdrop {
    z-index: 2050 !important;
}

 </style>

@section('content')
<div class="container mt-4">
  <div class="card shadow-sm border-0" style="background-color: #FFFBE6; border-left: 5px solid #AEB7BF;">
    <div class="card-body">
      {{-- Header --}}
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold" style="color: #6B3E26;">Fixed Pricing (Postcode to Postcode)</h4>
        {{-- <p class="text-muted">
    Showing {{ count($fixedPrices) }} of {{ $totalCount }} total records
</p> --}}
      </div>

<div class="d-flex justify-content-between">
    {{-- Upload / Download --}}
      <form id="importForm" action="{{ route('pricing.fixed.import') }}" method="POST" enctype="multipart/form-data" class="row g-2 mb-4 align-items-center">
    @csrf
    <div class="col-auto">
      <input id="pricingFile" type="file" name="file" class="form-control form-control-sm" accept=".xlsx,.xls,.csv" required>
    </div>
    <div class="col-auto">
      <button id="uploadBtn" type="submit" class="btn btn-sm" style="background-color: #AEB7BF; color: white;">
        Upload
      </button>
<!-- Button -->
<button type="button" class="btn btn-sm"
        style="background-color: #6B3E26; color: white;" 
        data-bs-toggle="modal" 
        data-bs-target="#percentageModal">
  Set & Save Percentages
</button>
<!-- Add New Surcharge Button -->
<button type="button" class="btn btn-sm"
        style="background-color: #6B3E26; color: white;"
        onclick="window.location.href='{{ route('pricing.surcharge') }}'">
  ➕ Surcharge Screen
</button>



    </div>
    
    
    

    <!--<div class="col-auto">-->
    <!--  <a href="{{ route('pricing.fixed.export') }}" id="downloadBtn" class="btn bg-warning bg-opacity-50 btn-sm">-->
    <!--    Download CSV-->
    <!--  </a>-->
    <!--</div>-->
</form>


<!-- Add Surcharge Modal -->
<div class="modal fade col-md-6" id="addSurchargeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-4">
      <h5 class="mb-3" style="color: #6B3E26;">Add New Surcharge</h5>
      <form id="addSurchargeForm">
        <div class="mb-3">
          <label class="form-label">Surcharge %</label>
          <input type="number" name="surcharge" id="surcharge" class="form-control" required>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">From Date</label>
            <input type="date" name="from_date" id="from_date" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">To Date</label>
            <input type="date" name="to_date" id="to_date" class="form-control" required>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">From Time</label>
            <input type="time" name="from_time" id="from_time" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">To Time</label>
            <input type="time" name="to_time" id="to_time" class="form-control" required>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Pickup Postcode</label>
            <input type="text" name="pickup" id="pickup" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Dropoff Postcode</label>
            <input type="text" name="dropoff" id="dropoff" class="form-control" required>
          </div>
        </div>

        <div class="d-flex justify-content-end">
          <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Save</button>
        </div>
      </form>
      <div class="card mt-4 shadow-sm border-0">
    <div class="card-header bg-warning bg-opacity-25 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-dark">📊 Surcharge List</h5>
        <!--<button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#addSurchargeModal">➕ Add New</button>-->
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>From Time</th>
                    <th>To Time</th>
                    <th>Pickup</th>
                    <th>Dropoff</th>
                    <th>Surcharge (%)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($surcharges as $key => $item)
                    <tr>
                        <td>{{ $item['from_date'] ?? '-' }}</td>
                        <td>{{ $item['to_date'] ?? '-' }}</td>
                        <td>{{ $item['from_time'] ?? '-' }}</td>
                        <td>{{ $item['to_time'] ?? '-' }}</td>
                        <td>{{ $item['pickup'] ?? '-' }}</td>
                        <td>{{ $item['dropoff'] ?? '-' }}</td>
                        <td><strong>{{ $item['surcharge'] ?? 0 }}%</strong></td>
                        <td>
                            <button class="btn btn-success btn-sm edit-btn" 
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
                            <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $key }}">❌</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">No surcharge records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

    </div>
  </div>
</div>

<div class="modal fade" id="editSurchargeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-4">
      <h5 class="mb-3" style="color: #6B3E26;">Edit Surcharge</h5>
      <form id="editSurchargeForm">
        <input type="hidden" id="edit_id">

        <div class="mb-3">
          <label class="form-label">Surcharge %</label>
          <input type="number" name="surcharge" id="edit_surcharge" class="form-control" required>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">From Date</label>
            <input type="date" name="from_date" id="edit_from_date" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">To Date</label>
            <input type="date" name="to_date" id="edit_to_date" class="form-control" required>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">From Time</label>
            <input type="time" name="from_time" id="edit_from_time" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">To Time</label>
            <input type="time" name="to_time" id="edit_to_time" class="form-control" required>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Pickup</label>
            <input type="text" name="pickup" id="edit_pickup" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Dropoff</label>
            <input type="text" name="dropoff" id="edit_dropoff" class="form-control" required>
          </div>
        </div>

        <div class="d-flex justify-content-end">
          <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>



<!-- Modal -->
<div class="modal fade" id="percentageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-4">
      <h5 class="mb-3" style="color: #6B3E26;">Set Percentage Increase (based on Saloon Price)</h5>
      <form id="percentageForm">

        <div class="mb-3">
          <label class="form-label">Estate (%)</label>
          <input type="number" id="estatePercent" class="form-control" step="1">
        </div>

        <div class="mb-3">
          <label class="form-label">MPV (%)</label>
          <input type="number" id="mpvPercent" class="form-control" step="1">
        </div>

        <div class="mb-3">
          <label class="form-label">8 Seater (%)</label>
          <input type="number" id="seater8Percent" class="form-control" step="1">
        </div>

        <div class="mb-3">
          <label class="form-label">Executive (%)</label>
          <input type="number" id="executivePercent" class="form-control" step="1">
        </div>

        <div class="d-flex justify-content-end">
          <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success" id="applyAndSavePercentages">Apply & Save</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!--<div class="modal fade" id="percentageModal" tabindex="-1" aria-hidden="true">-->
<!--  <div class="modal-dialog modal-dialog-centered">-->
<!--    <div class="modal-content p-4">-->
<!--      <h5 class="mb-3" style="color: #6B3E26;">Set Percentage Increase (based on Saloon Price)</h5>-->
<!--      <form id="percentageForm">-->
<!--        <div class="mb-3">-->
<!--          <label class="form-label">Estate (%)</label>-->
<!--          <input type="number" id="estatePercent" class="form-control" value="" step="1">-->
<!--        </div>-->
<!--        <div class="mb-3">-->
<!--          <label class="form-label">6 Seater (%)</label>-->
<!--          <input type="number" id="seater6Percent" class="form-control" value="" step="1">-->
<!--        </div>-->
<!--        <div class="mb-3">-->
<!--          <label class="form-label">7–8 Seater (%)</label>-->
<!--          <input type="number" id="seater7_8Percent" class="form-control" value="" step="1">-->
<!--        </div>-->
<!--        <div class="mb-3">-->
<!--          <label class="form-label">10–12 Seater (%)</label>-->
<!--          <input type="number" id="seater12_16Percent" class="form-control" value="" step="1">-->
<!--        </div>-->

<!--        <div class="d-flex justify-content-end">-->
<!--          <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>-->
<!--          <button type="button" class="btn btn-success" id="applyAndSavePercentages">Apply & Save</button>-->
<!--        </div>-->
<!--      </form>-->
<!--    </div>-->
<!--  </div>-->
<!--</div>-->

<div class="row mb-3 align-items-center">
    <div class="col-auto">
        <input 
            type="text" 
            id="tableSearch" 
            class="form-control form-control-sm" 
            placeholder="Search by From or To Postcode"
            style="
                border: 2px solid #AEB7BF;
                border-radius: 8px; 
                padding: 6px 12px; 
                width: 250px;
                transition: border-color 0.3s, box-shadow 0.3s;
            "
            onfocus="this.style.borderColor='#D7E0E6'; this.style.boxShadow='0 0 5px #D7E0E6';"
            onblur="this.style.borderColor='#AEB7BF'; this.style.boxShadow='none';"
        >
    </div>
</div>
</div>
      
      
<!-- Loader Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <div class="spinner-border text-warning mb-3" role="status"></div>
      <h5 id="importProgressTitle">Uploading pricing file...</h5>
      <div class="progress my-3" style="height: 22px;">
        <div id="importProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuemin="0" aria-valuemax="100">0%</div>
      </div>
      <p id="importProgressMessage" class="mb-1">Starting upload...</p>
      <small id="importProgressCounts" class="text-muted"></small>
      <button id="importModalClose" type="button" class="btn btn-secondary btn-sm mt-3 d-none" data-bs-dismiss="modal">Close</button>
    </div>
  </div>
</div>

      {{-- Table --}}
      {{-- <div class="table-responsive">
        <table class="table table-hover table-borderless align-middle" >
          <thead style="background-color: #D7E0E6; color: #6B3E26;">
            <tr>
              <th>From Postcode</th>
              <th>To Postcode</th>
              <th>Company Price (£)</th>
              <th>Driver Price (£)</th>
              <th>Agent Commission (£)</th>
              <th>Type</th>
              <th>Vehicle Type</th>
              <th>Created At</th>
            </tr>
          </thead>
          <tbody style="background-color: #EAFDFD;">
            @forelse($fixedPrices as $fp)
              <tr style="border-bottom: 1px solid #D9E1E7;">
                <td>{{ $fp['from_postcode'] ?? '' }}</td>
                <td>{{ $fp['to_postcode'] ?? '' }}</td>
                <td>£{{ number_format($fp['company_price'] ?? 0, 2) }}</td>
                <td>£{{ number_format($fp['driver_price'] ?? 0, 2) }}</td>
                <td>£{{ number_format($fp['agent_commission'] ?? 0, 2) }}</td>
                <td>{{ $fp['type'] ?? 'default' }}</td>
                <td>{{ $fp['vehicle_type'] ?? 'default' }}</td>
                <td>{{ $fp['created_at'] ?? '-' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center text-muted py-3">
                  No fixed pricing data available.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
        
      
    </div> --}}
    {{-- <div class="table-responsive">
    <table class="table table-hover table-borderless align-middle">
        <thead style="background-color: #D7E0E6; color: #6B3E26;">
            <tr>
                <th>From Postcode</th>
                <th>To Postcode</th>
                <th>Company Price (£)</th>
                <th>Driver Price (£)</th>
                <th>Agent Commission (£)</th>
                <th>Type</th>
                <th>Vehicle Type</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody style="background-color: #EAFDFD;">
            @forelse($fixedPrices as $key => $fp)
                <tr style="border-bottom: 1px solid #D9E1E7;">
                    <td>{{ $fp['from_postcode'] ?? '' }}</td>
                    <td>{{ $fp['to_postcode'] ?? '' }}</td>
                    <td>£{{ number_format($fp['company_price'] ?? 0, 2) }}</td>
                    <td>£{{ number_format($fp['driver_price'] ?? 0, 2) }}</td>
                    <td>£{{ number_format($fp['agent_commission'] ?? 0, 2) }}</td>
                    <td>{{ $fp['type'] ?? 'default' }}</td>
                    <td>{{ $fp['vehicle_type'] ?? 'default' }}</td>
                    <td>{{ $fp['created_at'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-3">
                        No fixed pricing data available.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


<div class="d-flex justify-content-between mt-3">
    @if($startAfter)
        <a href="{{ url()->current() }}" class="btn btn-warning btn-sm">⬅ Back to Start</a>
    @endif

    @if($hasMore)
        <a href="{{ url()->current() }}?startAfter={{ $lastKey }}" class="btn btn-success btn-sm">Next ➡</a>
    @endif
</div> --}}
<div class="table-responsive">
    <table class="table table-hover table-borderless align-middle" id="fixedPricesTable">
       <thead style="background-color: #D7E0E6; color: #6B3E26;">
            <tr>
              <th>From</th>
              <th>To</th>
              <th>Saloon Price (£)</th>
              <th>Estate Price (£)</th>
              <th>MPV Price (£)</th>
              <th>8 Seater Price (£)</th>
              <th>Executive Price (£)</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody style="background-color: #EAFDFD;">
            @forelse($fixedPrices as $key => $fp)
              <tr style="border-bottom: 1px solid #D9E1E7;">
                <td>{{ $fp['from_postcode'] }}</td>
<td>{{ $fp['to_postcode'] }}</td>
<td>£{{ number_format($fp['Saloon'], 2) }}</td>
<td>£{{ number_format($fp['Estate'], 2) }}</td>
<td>£{{ number_format($fp['MPV'], 2) }}</td>
<td>£{{ number_format($fp['8 Seater'], 2) }}</td>
<td>£{{ number_format($fp['Executive'], 2) }}</td>
<td>
   <button 
    class="btn btn-sm btn-success editBtn"
    data-id="{{ $key }}"
    data-from="{{ $fp['from_postcode'] }}"
    data-to="{{ $fp['to_postcode'] }}"
    data-saloon="{{ $fp['Saloon'] }}"
    data-bs-toggle="modal"
    data-bs-target="#editPriceModal">
    Edit
</button>

</td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-3">No fixed pricing data available.</td>
              </tr>
            @endforelse
          </tbody>
    </table>
</div>
<input type="hidden" name="reset" value="1">

{{-- Info + Pagination --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
    <div class="mb-2 mb-md-0 text-muted">
        Showing <strong>{{ $from }}</strong> – <strong>{{ $to }}</strong> of <strong>{{ $totalCount }}</strong> results
    </div>

    {{-- Pagination --}}
    @php
        $perPage = 25;
        $totalPages = ceil($totalCount / $perPage);
        $range = 2; // number of pages to show around current
    @endphp

    <nav>
        <ul class="pagination pagination-sm mb-0">
            {{-- Previous button --}}
            <!--<li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">-->
            <!--    <a class="page-link"-->
            <!--       href="{{ $page > 1 ? url()->current() . '?page=' . ($page - 1) : '#' }}"-->
            <!--       tabindex="-1">-->
            <!--        ⬅ Prev-->
            <!--    </a>-->
            <!--</li>-->

            {{-- Page numbers --}}
            @for ($i = max(1, $page - $range); $i <= min($totalPages, $page + $range); $i++)
                <li class="page-item {{ $i === $page ? 'active' : '' }}">
                    <a class="page-link" href="{{ url()->current() }}?page={{ $i }}">{{ $i }}</a>
                </li>
            @endfor

            {{-- Ellipsis (optional for large sets) --}}
            @if ($page + $range < $totalPages)
                <li class="page-item disabled"><span class="page-link">…</span></li>
                <li class="page-item">
                    <a class="page-link" href="{{ url()->current() }}?page={{ $totalPages }}">{{ $totalPages }}</a>
                </li>
            @endif

            {{-- Next button --}}
            <!--<li class="page-item {{ !$hasMore ? 'disabled' : '' }}">-->
            <!--    <a class="page-link"-->
            <!--       href="{{ $hasMore ? url()->current() . '?page=' . ($page + 1) : '#' }}">-->
            <!--        Next ➡-->
            <!--    </a>-->
            <!--</li>-->
        </ul>
    </nav>
</div>


<div class="modal fade" id="editPriceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editPriceForm">
            @csrf
            <input type="hidden" id="recordId">

            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Edit Fixed Price</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>From Postcode</label>
                        <input type="text" class="form-control" id="fromPostcode" readonly>
                    </div>

                    <div class="mb-3">
                        <label>To Postcode</label>
                        <input type="text" class="form-control" id="toPostcode" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Saloon Price (£)</label>
                        <input type="number" step="0.01" class="form-control" id="saloonPrice">
                    </div>

                </div>

                <div class="modal-footer">
                    <div id="editPriceFeedback" class="small text-danger me-auto" role="alert" aria-live="polite"></div>
                    <button type="submit" class="btn btn-success" id="updateFixedPriceBtn">
                        <span class="update-label">Update</span>
                        <span class="update-loading d-none">Updating...</span>
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>

            </div>
        </form>
    </div>
</div>


  </div>
</div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // The admin content shell creates its own stacking context. Bootstrap adds
    // backdrops to <body>, so nested modals can otherwise end up underneath it.
    document.querySelectorAll('.modal').forEach(function (modalElement) {
        if (modalElement.parentElement !== document.body) {
            document.body.appendChild(modalElement);
        }

        modalElement.addEventListener('hidden.bs.modal', function () {
            if (!document.querySelector('.modal.show')) {
                document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
                    backdrop.remove();
                });
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            }
        });
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("importForm");
    const uploadBtn = document.getElementById("uploadBtn");
    const downloadBtn = document.getElementById("downloadBtn");
    const fileInput = document.getElementById("pricingFile");
    const modalElement = document.getElementById("loadingModal");
    const progressBar = document.getElementById("importProgressBar");
    const progressTitle = document.getElementById("importProgressTitle");
    const progressMessage = document.getElementById("importProgressMessage");
    const progressCounts = document.getElementById("importProgressCounts");
    const closeButton = document.getElementById("importModalClose");
    const progressUrlTemplate = @json(route('pricing.fixed.import-progress', ['importId' => '__IMPORT_ID__']));

    function updateProgress(percent, message, processed, total, failed = false) {
        const value = Math.max(0, Math.min(100, Number(percent) || 0));
        progressBar.style.width = value + '%';
        progressBar.textContent = value + '%';
        progressBar.setAttribute('aria-valuenow', value);
        progressMessage.textContent = message || 'Processing...';
        progressCounts.textContent = Number.isFinite(processed) && Number.isFinite(total) && total > 0
            ? `${processed} of ${total} rows completed`
            : '';

        if (failed) {
            progressBar.classList.remove('progress-bar-striped', 'progress-bar-animated');
            progressBar.classList.add('bg-danger');
            progressTitle.textContent = 'Import failed';
            closeButton.classList.remove('d-none');
        }
    }

    if (form) form.addEventListener("submit", function (event) {
        event.preventDefault();
        if (!fileInput.files.length) return;

        const importId = (window.crypto && crypto.randomUUID)
            ? crypto.randomUUID().replaceAll('-', '')
            : Date.now().toString(36) + Math.random().toString(36).slice(2);
        const formData = new FormData(form);
        formData.set('import_id', importId);
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement, { backdrop: 'static', keyboard: false });

        uploadBtn.disabled = true;
        closeButton.classList.add('d-none');
        progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated';
        progressTitle.textContent = 'Uploading pricing file...';
        updateProgress(0, 'Starting upload...');
        modal.show();

        let importFinished = false;
        const pollProgress = async () => {
            if (importFinished) return;
            try {
                const response = await fetch(progressUrlTemplate.replace('__IMPORT_ID__', importId), {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store'
                });
                if (response.ok) {
                    const progress = await response.json();
                    updateProgress(progress.percent, progress.message, Number(progress.processed), Number(progress.total));

                    if (progress.status === 'completed') {
                        importFinished = true;
                        uploadBtn.disabled = false;
                        progressTitle.textContent = 'Import completed';
                        progressBar.classList.remove('progress-bar-striped', 'progress-bar-animated');
                        progressBar.classList.add('bg-success');
                        window.setTimeout(() => window.location.reload(), 1200);
                        return;
                    }

                    if (progress.status === 'failed') {
                        importFinished = true;
                        uploadBtn.disabled = false;
                        updateProgress(0, progress.message, null, null, true);
                        return;
                    }
                }
            } catch (error) {}
            if (!importFinished) window.setTimeout(pollProgress, 1000);
        };

        const xhr = new XMLHttpRequest();
        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.upload.addEventListener('progress', function (upload) {
            if (upload.lengthComputable) {
                updateProgress(Math.min(5, Math.round((upload.loaded / upload.total) * 5)), 'Uploading spreadsheet to the server...');
            }
        });
        xhr.addEventListener('load', function () {
            let payload = {};
            try { payload = JSON.parse(xhr.responseText); } catch (error) {}

            if (xhr.status >= 200 && xhr.status < 300 && payload.success && payload.queued) {
                progressTitle.textContent = 'Import in progress';
                updateProgress(5, payload.message || 'Upload complete. Waiting for the background worker...');
                return;
            }

            importFinished = true;
            uploadBtn.disabled = false;
            const validationMessage = payload.errors ? Object.values(payload.errors).flat().join(' ') : null;
            const serverMessage = xhr.status === 504
                ? 'The server timed out while importing. Please deploy the optimized importer and upload the file again.'
                : null;
            updateProgress(0, payload.message || validationMessage || serverMessage || 'The import could not be completed.', null, null, true);
        });
        xhr.addEventListener('error', function () {
            importFinished = true;
            uploadBtn.disabled = false;
            updateProgress(0, 'Network error. Please check the connection and try again.', null, null, true);
        });
        xhr.send(formData);
        window.setTimeout(pollProgress, 500);
    });

    // Show loader when downloading
    if (downloadBtn) {
        downloadBtn.addEventListener("click", function () {
            const loadingModal = document.getElementById("loadingModal");
            if (!loadingModal) return;

            const modal = bootstrap.Modal.getOrCreateInstance(loadingModal);
            modal.show();

            // Hide modal automatically after a few seconds (download trigger doesn’t auto-close)
            setTimeout(() => {
                modal.hide();
            }, 5000);
        });
    }
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {

    // document.querySelectorAll('.editBtn').forEach(btn => {
    //     btn.addEventListener('click', function () {

    //         document.getElementById('recordId').value     = this.dataset.id;
    //         document.getElementById('fromPostcode').value = this.dataset.from;
    //         document.getElementById('toPostcode').value   = this.dataset.to;
    //         document.getElementById('saloonPrice').value  = this.dataset.saloon;

    //     });
    // });
    document.addEventListener('click', function (e) {
    if (!e.target.classList.contains('editBtn')) return;

    const btn = e.target;

    document.getElementById('recordId').value     = btn.dataset.id || '';
    document.getElementById('fromPostcode').value = btn.dataset.from || '';
    document.getElementById('toPostcode').value   = btn.dataset.to || '';
    document.getElementById('saloonPrice').value  = btn.dataset.saloon || '';
});

    

    const editPriceForm = document.getElementById('editPriceForm');
    if (!editPriceForm) return;

    editPriceForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const id = document.getElementById('recordId').value;
        const price = document.getElementById('saloonPrice').value;
        const token = editPriceForm.querySelector('input[name="_token"]')?.value || '';
        const submitButton = document.getElementById('updateFixedPriceBtn');
        const feedback = document.getElementById('editPriceFeedback');
        const updateUrl = @json(route('fixed-prices.update', ['id' => '__ID__']));

        if (!id || price === '' || Number(price) < 0) {
            feedback.textContent = 'Please enter a valid price.';
            return;
        }

        feedback.textContent = '';
        submitButton.disabled = true;
        submitButton.querySelector('.update-label')?.classList.add('d-none');
        submitButton.querySelector('.update-loading')?.classList.remove('d-none');

        try {
            const response = await fetch(updateUrl.replace('__ID__', encodeURIComponent(id)), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ saloon_price: price })
            });

            const contentType = response.headers.get('content-type') || '';
            const data = contentType.includes('application/json')
                ? await response.json()
                : { success: false, message: `Server returned ${response.status}.` };

            if (!response.ok || !data.success) {
                throw new Error(data.message || data.error || 'Price could not be updated.');
            }

            if (data.success) {
                feedback.classList.remove('text-danger');
                feedback.classList.add('text-success');
                feedback.textContent = 'Price updated successfully.';
                location.reload();
            }
        } catch (error) {
            console.error('Fixed price update failed:', error);
            feedback.classList.remove('text-success');
            feedback.classList.add('text-danger');
            feedback.textContent = error.message || 'Price update failed. Please try again.';
            submitButton.disabled = false;
            submitButton.querySelector('.update-label')?.classList.remove('d-none');
            submitButton.querySelector('.update-loading')?.classList.add('d-none');
        }
    });

});
</script>


<script>
let debounceTimeout;
const searchInput = document.getElementById('tableSearch');
const tbody = document.querySelector('#fixedPricesTable tbody');
let currentPercentages = {}; // Store fetched percentages globally

// Fetch percentages once on page load
async function fetchPercentages() {
    try {
        const response = await fetch("{{ route('pricing.fixed.getPercentages') }}");
        const result = await response.json();
        if (result.success && result.data && result.data.percentages) {
            currentPercentages = result.data.percentages;
            console.log("Percentages fetched:", currentPercentages);
        }
    } catch (error) {
        console.error("Fetch percentages error:", error);
    }
}

// Apply percentages to a table row
function applyPercentages(row) {
    if (!currentPercentages) return;
    
    const saloon = parseFloat(row.cells[2].textContent.replace("£","")) || 0;

    const estate = parseFloat(currentPercentages.estate || 0);
    const mpv = parseFloat(currentPercentages.mpv || 0);
    const seater8 = parseFloat(currentPercentages.seater8 || 0);
    const executive = parseFloat(currentPercentages.executive || 0);

    row.cells[3].textContent = `£${(saloon + saloon * estate / 100).toFixed(2)}`;
    row.cells[4].textContent = `£${(saloon + saloon * mpv / 100).toFixed(2)}`;
    row.cells[5].textContent = `£${(saloon + saloon * seater8 / 100).toFixed(2)}`;
    row.cells[6].textContent = `£${(saloon + saloon * executive / 100).toFixed(2)}`;
}

// Fetch table data and apply percentages
function fetchResults(query, page = 1) {
    const url = new URL(`{{ route('fixedPrices.search') }}`);
    url.searchParams.set('q', query);
    url.searchParams.set('page', page);

    fetch(url.toString())
        .then(res => {
            if (!res.ok) throw new Error(`HTTP ${res.status}: ${res.statusText}`);
            return res.json();
        })
        .then(payload => {
            const data = payload.data || [];
            const total = payload.total || 0;
            const currentPage = payload.page || 1;
            const perPage = payload.per_page || 25;
            const hasMore = payload.has_more || false;

            const showingInfo = document.getElementById('showingInfo');
            if (showingInfo) {
                const from = ((currentPage - 1) * perPage) + 1;
                const to = Math.min(currentPage * perPage, total);
                showingInfo.textContent = `Showing ${from} – ${to} of ${total}`;
            }

            updatePaginationLinks(currentPage, hasMore, total, perPage);

            tbody.innerHTML = '';
            if (!data.length) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-3">No matching records found.</td></tr>`;
                return;
            }

            const rowsHtml = data.map((fp, index) => {
                const from = fp.from_postcode ?? fp.fromPostcode ?? '';
                const to = fp.to_postcode ?? fp.toPostcode ?? '';
                return `
                    <tr style="border-bottom: 1px solid #D9E1E7;">
                        <td>${from}</td>
                        <td>${to}</td>
                        <td>£${parseFloat(fp["Saloon"] ?? 0).toFixed(2)}</td>
                        <td>£${parseFloat(fp["Estate"] ?? 0).toFixed(2)}</td>
                        <td>£${parseFloat(fp["MPV"] ?? 0).toFixed(2)}</td>
                        <td>£${parseFloat(fp["8 Seater"] ?? 0).toFixed(2)}</td>
                        <td>£${parseFloat(fp["Executive"] ?? 0).toFixed(2)}</td>
                        <td>
                <button 
                    class="btn btn-sm btn-success editBtn"
                    data-id="${fp.id ?? index}"
                    data-from="${from}"
                    data-to="${to}"
                    data-saloon="${fp.Saloon ?? 0}"
                    data-estate="${fp.Estate ?? 0}"
                    data-mpv="${fp.MPV ?? 0}"
                    data-8seater="${fp["8 Seater"] ?? 0}"
                    data-executive="${fp.Executive ?? 0}"
                    data-bs-toggle="modal"
                    data-bs-target="#editPriceModal">
                    Edit
                </button>
            </td>
                    </tr>
                `;
            }).join('');
            tbody.innerHTML = rowsHtml;

            // Apply percentages to all rows after rendering
            document.querySelectorAll("#fixedPricesTable tbody tr").forEach(row => applyPercentages(row));
        })
        .catch(err => {
            console.error('Search error:', err);
            tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-3">Search failed. Check console for details.</td></tr>`;
        });
}

// Debounced search
searchInput.addEventListener('keyup', () => {
    const query = searchInput.value.trim();
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => fetchResults(query, 1), 500);
});

// Initial fetch of percentages
document.addEventListener("DOMContentLoaded", fetchPercentages);

// Basic pagination update function (customize for your setup)
function updatePaginationLinks(currentPage, hasMore, total, perPage) {
    const pagination = document.getElementById('paginationLinks');
    if (!pagination) return;

    pagination.innerHTML = '';

    // Previous
    if (currentPage > 1) {
        const prevLi = document.createElement('li');
        prevLi.className = 'page-item';
        prevLi.innerHTML = `<a href="#" class="page-link" onclick="fetchResults('${searchInput.value.trim()}', ${currentPage - 1}); return false;">⬅ Previous</a>`;
        pagination.appendChild(prevLi);
    }

    // Next
    if (hasMore) {
        const nextLi = document.createElement('li');
        nextLi.className = 'page-item';
        nextLi.innerHTML = `<a href="#" class="page-link" onclick="fetchResults('${searchInput.value.trim()}', ${currentPage + 1}); return false;">Next ➡</a>`;
        pagination.appendChild(nextLi);
    }
}

// Debounced search (resets to page 1)
searchInput.addEventListener('keyup', () => {
    const query = searchInput.value.trim();
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        fetchResults(query, 1); // Always start at page 1 for new search
    }, 500);
});

    document.addEventListener("DOMContentLoaded", async function () {
    try {
        const response = await fetch("{{ route('pricing.fixed.getPercentages') }}");
        const result = await response.json();

        if (result.success && result.data && result.data.percentages) {
            const p = result.data.percentages;

            const estate = parseFloat(p.estate || 0);
            const mpv = parseFloat(p.mpv || 0);
            const seater8 = parseFloat(p.seater8 || 0);
            const executive = parseFloat(p.executive || 0);

            const rows = document.querySelectorAll("#fixedPricesTable tbody tr");

            rows.forEach(row => {
                const saloon = parseFloat(row.cells[2].textContent.replace("£","")) || 0;

                row.cells[3].textContent = `£${(saloon + saloon * estate / 100).toFixed(2)}`;
                row.cells[4].textContent = `£${(saloon + saloon * mpv / 100).toFixed(2)}`;
                row.cells[5].textContent = `£${(saloon + saloon * seater8 / 100).toFixed(2)}`;
                row.cells[6].textContent = `£${(saloon + saloon * executive / 100).toFixed(2)}`;
            });

            console.log("Updated prices:", p);
        }

    } catch (error) {
        console.error("Fetch error:", error);
    }
});

// document.addEventListener("DOMContentLoaded", async function () {
//     try {
//         // 🔹 Fetch latest percentages from Firebase via Laravel
//         const response = await fetch("{{ route('pricing.fixed.getPercentages') }}");
//         const result = await response.json();

//         if (result.success && result.data && result.data.percentages) {
//             const p = result.data.percentages;

//             const estatePercent = parseFloat(p.estate || 0);
//             const seater6Percent = parseFloat(p.seater6 || 0);
//             const seater7_8Percent = parseFloat(p.seater7_8 || 0);
//             const seater12_16Percent = parseFloat(p.seater12_16 || 0);

//             // 🔹 Update table values dynamically
//             const rows = document.querySelectorAll("#fixedPricesTable tbody tr");
//             rows.forEach(row => {
//                 const saloonCell = row.cells[2];
//                 if (!saloonCell) return; // skip empty row

//                 const saloonPrice = parseFloat(saloonCell.textContent.replace("£", "").trim()) || 0;

//                 const estate = saloonPrice + (saloonPrice * estatePercent / 100);
//                 const seater6 = saloonPrice + (saloonPrice * seater6Percent / 100);
//                 const seater7_8 = saloonPrice + (saloonPrice * seater7_8Percent / 100);
//                 const seater12_16 = saloonPrice + (saloonPrice * seater12_16Percent / 100);

//                 row.cells[3].textContent = `£${estate.toFixed(2)}`;
//                 row.cells[4].textContent = `£${seater6.toFixed(2)}`;
//                 row.cells[5].textContent = `£${seater7_8.toFixed(2)}`;
//                 row.cells[6].textContent = `£${seater12_16.toFixed(2)}`;
//             });

//             console.log("✅ Prices updated based on Firebase percentages:", p);
//         } else {
//             console.warn("⚠️ No percentages found or fetch failed.");
//         }

//     } catch (error) {
//         console.error("❌ Failed to fetch or apply percentages:", error);
//     }
// });
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {

    // 🔹 When modal opens, load the existing percentages from Firebase
    // const modalElement = document.getElementById('percentageModal');
    // modalElement.addEventListener('show.bs.modal', async function () {
    //     try {
    //         const response = await fetch("{{ route('pricing.fixed.getPercentages') }}");
    //         const result = await response.json();

    //         if (result.success && result.data && result.data.percentages) {
    //             const p = result.data.percentages;
    //             document.getElementById('estatePercent').value = p.estate ?? 0;
    //             document.getElementById('seater6Percent').value = p.seater6 ?? 0;
    //             document.getElementById('seater7_8Percent').value = p.seater7_8 ?? 0;
    //             document.getElementById('seater12_16Percent').value = p.seater12_16 ?? 0;
    //         } else {
    //             console.warn("No previous percentages found in Firebase.");
    //         }
    //     } catch (error) {
    //         console.error("Failed to load old percentages:", error);
    //     }
    // });
    
    document.getElementById('percentageModal')
    .addEventListener('show.bs.modal', async function () {
        try {
            const response = await fetch("{{ route('pricing.fixed.getPercentages') }}");
            const result = await response.json();

            if (result.success && result.data.percentages) {
                const p = result.data.percentages;

                document.getElementById('estatePercent').value = p.estate ?? 0;
                document.getElementById('mpvPercent').value = p.mpv ?? 0;
                document.getElementById('seater8Percent').value = p.seater8 ?? 0;
                document.getElementById('executivePercent').value = p.executive ?? 0;
            }
        } catch (error) {
            console.error(error);
        }
});


    // 🔹 Save and apply percentages
    document.getElementById('applyAndSavePercentages').addEventListener('click', async function () {
    const estate = parseFloat(document.getElementById('estatePercent').value) || 0;
    const mpv = parseFloat(document.getElementById('mpvPercent').value) || 0;
    const seater8 = parseFloat(document.getElementById('seater8Percent').value) || 0;
    const executive = parseFloat(document.getElementById('executivePercent').value) || 0;

    const rows = document.querySelectorAll('#fixedPricesTable tbody tr');
    const newData = [];

    rows.forEach(row => {
        const from = row.cells[0].textContent.trim();
        const to = row.cells[1].textContent.trim();
        const saloon = parseFloat(row.cells[2].textContent.replace('£','')) || 0;

        const estateFare = saloon + saloon * estate / 100;
        const mpvFare = saloon + saloon * mpv / 100;
        const seater8Fare = saloon + saloon * seater8 / 100;
        const executiveFare = saloon + saloon * executive / 100;

        row.cells[3].textContent = `£${estateFare.toFixed(2)}`;
        row.cells[4].textContent = `£${mpvFare.toFixed(2)}`;
        row.cells[5].textContent = `£${seater8Fare.toFixed(2)}`;
        row.cells[6].textContent = `£${executiveFare.toFixed(2)}`;

        newData.push({
            from_postcode: from,
            to_postcode: to,
            saloon_fare: saloon,
            estate_fare: estateFare,
            mpv_fare: mpvFare,
            seater8_fare: seater8Fare,
            executive_fare: executiveFare
        });
    });

    const response = await fetch("{{ route('pricing.fixed.savePercentages') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            percentages: {
                estate,
                mpv,
                seater8,
                executive
            }
        })
    });

    const result = await response.json();

    if (result.success) {
        alert("Saved successfully!");
    } else {
        alert(result.message || result.error || "Error saving.");
        console.error(result);
    }

    bootstrap.Modal.getInstance(document.getElementById('percentageModal')).hide();
});

    // document.getElementById('applyAndSavePercentages').addEventListener('click', async function () {
    //     const estatePercent = parseFloat(document.getElementById('estatePercent').value) || 0;
    //     const seater6Percent = parseFloat(document.getElementById('seater6Percent').value) || 0;
    //     const seater7_8Percent = parseFloat(document.getElementById('seater7_8Percent').value) || 0;
    //     const seater12_16Percent = parseFloat(document.getElementById('seater12_16Percent').value) || 0;

    //     const rows = document.querySelectorAll('#fixedPricesTable tbody tr');
    //     const newData = [];

    //     rows.forEach(row => {
    //         const from = row.cells[0].textContent.trim();
    //         const to = row.cells[1].textContent.trim();
    //         const saloon = parseFloat(row.cells[2].textContent.replace('£', '')) || 0;

    //         const estate = saloon + (saloon * estatePercent / 100);
    //         const seater6 = saloon + (saloon * seater6Percent / 100);
    //         const seater7_8 = saloon + (saloon * seater7_8Percent / 100);
    //         const seater12_16 = saloon + (saloon * seater12_16Percent / 100);

    //         // Update the table visually
    //         row.cells[3].textContent = `£${estate.toFixed(2)}`;
    //         row.cells[4].textContent = `£${seater6.toFixed(2)}`;
    //         row.cells[5].textContent = `£${seater7_8.toFixed(2)}`;
    //         row.cells[6].textContent = `£${seater12_16.toFixed(2)}`;

    //         // Prepare data for saving
    //         newData.push({
    //             from_postcode: from,
    //             to_postcode: to,
    //             saloon_fare: saloon,
    //             estate_fare: estate,
    //             seater6_fare: seater6,
    //             seater7_8_fare: seater7_8,
    //             seater12_16_fare: seater12_16
    //         });
    //     });

    //     // Send updated percentages to Laravel → Firebase
    //     const response = await fetch("{{ route('pricing.fixed.savePercentages') }}", {
    //         method: "POST",
    //         headers: {
    //             "Content-Type": "application/json",
    //             "X-CSRF-TOKEN": "{{ csrf_token() }}"
    //         },
    //         body: JSON.stringify({
    //             data: newData,
    //             percentages: {
    //                 estate: estatePercent,
    //                 seater6: seater6Percent,
    //                 seater7_8: seater7_8Percent,
    //                 seater12_16: seater12_16Percent
    //             }
    //         })
    //     });

    //     const result = await response.json();

    //     if (result.success) {
    //         alert("✅ Percentages applied and saved successfully in Firebase!");
    //     } else {
    //         alert("❌ Failed to save data in Firebase. Check console.");
    //         console.error(result);
    //     }

    //     // Close modal
    //     const modal = bootstrap.Modal.getInstance(modalElement);
    //     modal.hide();
    // });
});
</script>

<script>
document.getElementById('addSurchargeForm').addEventListener('submit', async function(e) {
  e.preventDefault();

  const data = {
    surcharge: parseFloat(document.getElementById('surcharge').value),
    from_date: document.getElementById('from_date').value,
    to_date: document.getElementById('to_date').value,
    from_time: document.getElementById('from_time').value,
    to_time: document.getElementById('to_time').value,
    pickup: document.getElementById('pickup').value.trim().toUpperCase(),
    dropoff: document.getElementById('dropoff').value.trim().toUpperCase()
  };

  try {
    const response = await fetch("{{ route('pricing.fixed.addSurcharge') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify(data)
    });

    const result = await response.json();

    if (result.success) {
      alert('✅ Surcharge added successfully!');
      location.reload();
    } else {
      alert('❌ Failed to add surcharge. Check console for details.');
      console.error(result);
    }

  } catch (error) {
    console.error('Error:', error);
    alert('⚠️ Network error. Please try again.');
  }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

  // 🟢 Open Edit Modal
  document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.getElementById('edit_id').value = btn.dataset.id;
      document.getElementById('edit_surcharge').value = btn.dataset.surcharge;
      document.getElementById('edit_from_date').value = btn.dataset.from_date;
      document.getElementById('edit_to_date').value = btn.dataset.to_date;
      document.getElementById('edit_from_time').value = btn.dataset.from_time;
      document.getElementById('edit_to_time').value = btn.dataset.to_time;
      document.getElementById('edit_pickup').value = btn.dataset.pickup;
      document.getElementById('edit_dropoff').value = btn.dataset.dropoff;
      new bootstrap.Modal(document.getElementById('editSurchargeModal')).show();
    });
  });

  // 🟢 Handle Edit Submit
  document.getElementById('editSurchargeForm').addEventListener('submit', async function (e) {
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

    const response = await fetch(`/admin/pricing/update-surcharge/${id}`, {
      method: 'POST',
      headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
      body: JSON.stringify(data)
    });

    const result = await response.json();
    if (result.success) {
      alert('✅ Surcharge updated successfully!');
      location.reload();
    } else {
      alert('❌ Failed to update surcharge.');
    }
  });

  // 🔴 Handle Delete
  document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
      if (!confirm('Are you sure you want to delete this surcharge?')) return;

      const id = btn.dataset.id;
      const response = await fetch(`/admin/pricing/delete-surcharge/${id}`, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
      });

      const result = await response.json();
      if (result.success) {
        alert('🗑️ Deleted successfully!');
        location.reload();
      } else {
        alert('❌ Failed to delete surcharge.');
      }
    });
  });

});
</script>




<script>
// document.getElementById('applyAndSavePercentages').addEventListener('click', async function () {
//     const estatePercent = parseFloat(document.getElementById('estatePercent').value) || 0;
//     const seater6Percent = parseFloat(document.getElementById('seater6Percent').value) || 0;
//     const seater7_8Percent = parseFloat(document.getElementById('seater7_8Percent').value) || 0;
//     const seater12_16Percent = parseFloat(document.getElementById('seater12_16Percent').value) || 0;

//     const rows = document.querySelectorAll('#fixedPricesTable tbody tr');
//     const newData = [];

//     rows.forEach(row => {
//         const from = row.cells[0].textContent.trim();
//         const to = row.cells[1].textContent.trim();
//         const saloon = parseFloat(row.cells[2].textContent.replace('£', '')) || 0;

//         const estate = saloon + (saloon * estatePercent / 100);
//         const seater6 = saloon + (saloon * seater6Percent / 100);
//         const seater7_8 = saloon + (saloon * seater7_8Percent / 100);
//         const seater12_16 = saloon + (saloon * seater12_16Percent / 100);

//         // Update the table visually
//         row.cells[3].textContent = `£${estate.toFixed(2)}`;
//         row.cells[4].textContent = `£${seater6.toFixed(2)}`;
//         row.cells[5].textContent = `£${seater7_8.toFixed(2)}`;
//         row.cells[6].textContent = `£${seater12_16.toFixed(2)}`;

//         // Prepare data for saving
//         newData.push({
//             from_postcode: from,
//             to_postcode: to,
//             saloon_fare: saloon,
//             estate_fare: estate,
//             seater6_fare: seater6,
//             seater7_8_fare: seater7_8,
//             seater12_16_fare: seater12_16
//         });
//     });

//     // Send data to Laravel to save in Firebase
//     const response = await fetch("{{ route('pricing.fixed.savePercentages') }}", {
//         method: "POST",
//         headers: {
//             "Content-Type": "application/json",
//             "X-CSRF-TOKEN": "{{ csrf_token() }}"
//         },
//         body: JSON.stringify({
//             data: newData,
//             percentages: {
//                 estate: estatePercent,
//                 seater6: seater6Percent,
//                 seater7_8: seater7_8Percent,
//                 seater12_16: seater12_16Percent
//             }
//         })
//     });

//     const result = await response.json();

//     if (result.success) {
//         alert("✅ Percentages applied and saved successfully in Firebase!");
//     } else {
//         alert("❌ Failed to save data in Firebase. Please check console.");
//         console.error(result);
//     }

//     // Close modal
//     const modal = bootstrap.Modal.getInstance(document.getElementById('percentageModal'));
//     modal.hide();
// });
</script>


<script>
// document.getElementById('applyPercentages').addEventListener('click', function () {
//     const estatePercent = parseFloat(document.getElementById('estatePercent').value) || 0;
//     const seater6Percent = parseFloat(document.getElementById('seater6Percent').value) || 0;
//     const seater7_8Percent = parseFloat(document.getElementById('seater7_8Percent').value) || 0;
//     const seater12_16Percent = parseFloat(document.getElementById('seater12_16Percent').value) || 0;

//     const rows = document.querySelectorAll('#fixedPricesTable tbody tr');

//     rows.forEach(row => {
//         const saloonCell = row.cells[2];
//         const estateCell = row.cells[3];
//         const seater6Cell = row.cells[4];
//         const seater7_8Cell = row.cells[5];
//         const seater12_16Cell = row.cells[6];

//         const saloonPrice = parseFloat(saloonCell.textContent.replace('£', '')) || 0;

//         // Calculate new prices
//         const estatePrice = saloonPrice + (saloonPrice * estatePercent / 100);
//         const seater6Price = saloonPrice + (saloonPrice * seater6Percent / 100);
//         const seater7_8Price = saloonPrice + (saloonPrice * seater7_8Percent / 100);
//         const seater12_16Price = saloonPrice + (saloonPrice * seater12_16Percent / 100);

//         // Update cells
//         estateCell.textContent = `£${estatePrice.toFixed(2)}`;
//         seater6Cell.textContent = `£${seater6Price.toFixed(2)}`;
//         seater7_8Cell.textContent = `£${seater7_8Price.toFixed(2)}`;
//         seater12_16Cell.textContent = `£${seater12_16Price.toFixed(2)}`;
//     });

//     // Close modal after applying
//     const modal = bootstrap.Modal.getInstance(document.getElementById('percentageModal'));
//     modal.hide();
// });
</script>


@endpush
@endsection
