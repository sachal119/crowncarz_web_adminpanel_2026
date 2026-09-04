<table class="table table-bordered table-striped align-middle">
    <thead class="table-dark text-center">
        <tr>
            <th>#</th>
            <th>Address</th>
            <th>Pickup Charge (£)</th>
            <th>Dropoff Charge (£)</th>
            <th>Extras</th>
            <th>Post Code</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="text-center">
        @if($data)
            @foreach($data as $key => $loc)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $loc['address'] ?? '-' }}</td>
                    <td>{{ $loc['pickup_charge'] ?? '0' }}</td>
                    <td>{{ $loc['dropoff_charge'] ?? '0' }}</td>
                    <td>{{ $loc['extras'] ?? '-' }}</td>
                    <td>{{ $loc['post_code'] ?? '-' }}</td>
                    <td>
                        <!-- Edit Button -->
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $key }}">
                            ✏️
                        </button>

                        <!-- Delete Button -->
                        <form action="{{ route('locations.destroy', [$type, $key]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this {{ $type }}?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">🗑</button>
                        </form>
                    </td>
                </tr>

                <!-- 🔸 Edit Modal -->
                <div class="modal fade" id="editModal{{ $key }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('locations.update', [$type, $key]) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header bg-warning text-dark">
                                    <h5 class="modal-title">✏️ Edit {{ ucfirst($type) }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <input type="text" name="address" class="form-control" value="{{ $loc['address'] ?? '' }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Pickup Charge (£)</label>
                                        <input type="number" name="pickup_charge" step="0.01" class="form-control" value="{{ $loc['pickup_charge'] ?? '0' }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Dropoff Charge (£)</label>
                                        <input type="number" name="dropoff_charge" step="0.01" class="form-control" value="{{ $loc['dropoff_charge'] ?? '0' }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Extras</label>
                                        <input type="text" name="extras" class="form-control" value="{{ $loc['extras'] ?? '' }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Post Code</label>
                                        <input type="text" name="post_code" class="form-control" value="{{ $loc['post_code'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-warning text-white">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <tr>
                <td colspan="7" class="text-muted text-center">No {{ $type }}s added yet.</td>
            </tr>
        @endif
    </tbody>
</table>
