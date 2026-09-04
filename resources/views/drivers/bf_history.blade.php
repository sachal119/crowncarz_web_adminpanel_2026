@extends('layouts.app')

@section('content')

<div class="card shadow-sm border-0 rounded-4 mb-4">

    <!-- Header -->
    <div class="card-header bg-warning bg-opacity-25 rounded-top-4 d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-bold text-dark">
            Account Balance History — {{ $driver['name'] ?? '' }}
        </h5>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">
        ← Back
    </a>

    </div>

    <div class="card-body p-4">


        <!-- Adjustment Form -->
        <div class="p-4 rounded-4 border mb-4" style="background:#faf7ef;">
            <h6 class="fw-bold mb-3 text-dark">
                Account Balance Adjustment 
            </h6>

            <form method="POST" action="{{ route('drivers.bf.update', $driverId) }}" class="row g-3">
                @csrf

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Amount (£)</label>
                    <input type="number" step="0.01" name="amount" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Transaction Type</label>
                    <select name="type" class="form-select" required>
                        <option value="add">Credit (Increase Balance)</option>
                        <option value="subtract">Debit (Decrease Balance)</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Reason / Comment</label>
                    <input type="text" name="comment" class="form-control" placeholder="e.g. Opening balance adjustment">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-success w-100 fw-semibold">
                        Apply
                    </button>
                </div>
            </form>
        </div>

        <!-- History Table -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead style="background:#E6B04A; color:#fff;">
                    <tr>
                        <th>#</th>
                        <th>Amount (£)</th>
                        <th>Transaction</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th width="140">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $key => $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td class="fw-bold {{ $row['type'] === 'add' ? 'text-success' : 'text-danger' }}">
                            {{ $row['type'] === 'add' ? '+' : '-' }}
                            £{{ number_format($row['amount'], 2) }}
                        </td>

                        <td>
                            <span class="badge {{ $row['type'] === 'add' ? 'bg-success' : 'bg-danger' }}">
                                {{ $row['type'] === 'add' ? 'Credit' : 'Debit' }}
                            </span>
                        </td>

                        <td>{{ $row['comment'] ?? '-' }}</td>

                        <td>
                            {{ \Carbon\Carbon::parse($row['created_at'])->format('d M Y, H:i') }}
                        </td>
                        <td>
    <button 
        class="btn btn-sm btn-primary editBtn"
        data-id="{{ $key }}"
        data-amount="{{ $row['amount'] }}"
        data-type="{{ $row['type'] }}"
        data-comment="{{ $row['comment'] ?? 'N/A' }}"
    >
        Edit
    </button>

    <form method="POST" 
          action="{{ route('drivers.bf.delete', [$driverId, $key]) }}" 
          class="d-inline"
          onsubmit="return confirm('Are you sure you want to delete this transaction?');">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-danger">
            Delete
        </button>
    </form>
</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No account balance history available
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header bg-warning bg-opacity-25">
        <h5 class="modal-title fw-semibold">Edit Transaction</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form method="POST" id="editForm">
        @csrf
        @method('PUT')

        <div class="modal-body">

            <input type="hidden" name="transaction_id" id="edit_id">

            <div class="mb-3">
                <label class="form-label fw-semibold">Amount (£)</label>
                <input type="number" step="0.01" name="amount" id="edit_amount" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Transaction Type</label>
                <select name="type" id="edit_type" class="form-select" required>
                    <option value="add">Credit</option>
                    <option value="subtract">Debit</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Comment</label>
                <input type="text" name="comment" id="edit_comment" class="form-control">
            </div>

        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-success">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
        </div>

    </div>
</div>
<script>
document.querySelectorAll('.editBtn').forEach(button => {
    button.addEventListener('click', function () {

        let id = this.dataset.id;
        let amount = this.dataset.amount;
        let type = this.dataset.type;
        let comment = this.dataset.comment;

        document.getElementById('edit_id').value = id;
        document.getElementById('edit_amount').value = amount;
        document.getElementById('edit_type').value = type;
        document.getElementById('edit_comment').value = comment;

        // Set form action dynamically
        document.getElementById('editForm').action =
            "https://crowncarz.com/admin/drivers/{{ $driverId }}/bf/update/" + id;

        new bootstrap.Modal(document.getElementById('editModal')).show();
    });
});
</script>

@endsection