@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">🛫 Manage Airports / Stations / Ports</h5>
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addLocationModal">➕ Add New</button>
        </div>

        <div class="card-body">
            @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


            <ul class="nav nav-tabs" id="locationTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="airport-tab" data-bs-toggle="tab" data-bs-target="#airport" type="button">Airports</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="station-tab" data-bs-toggle="tab" data-bs-target="#station" type="button">Stations</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="port-tab" data-bs-toggle="tab" data-bs-target="#port" type="button">Ports</button>
                </li>
            </ul>

            <div class="tab-content mt-3">
                <div class="tab-pane fade show active" id="airport" role="tabpanel">
                    @include('locations.table', ['data' => $airports, 'type' => 'airport'])
                </div>
                <div class="tab-pane fade" id="station" role="tabpanel">
                    @include('locations.table', ['data' => $stations, 'type' => 'station'])
                </div>
                <div class="tab-pane fade" id="port" role="tabpanel">
                    @include('locations.table', ['data' => $ports, 'type' => 'port'])
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addLocationModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('locations.store') }}" method="POST">
        @csrf
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title">➕ Add Location</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Type</label>
            <select name="type" class="form-select" required>
              <option value="airport">Airport</option>
              <option value="station">Station</option>
              <option value="port">Port</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Pickup Charge (£)</label>
            <input type="number" name="pickup_charge" step="0.01" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Dropoff Charge (£)</label>
            <input type="number" name="dropoff_charge" step="0.01" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Extras</label>
            <input type="text" name="extras" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Post Code</label>
            <input type="text" name="post_code" class="form-control">
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-dark" type="submit">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
