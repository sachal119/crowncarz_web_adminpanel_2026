@extends('layouts.app')

@section('content')
<style>
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .card-header {
        font-weight: 600;
        font-size: 1.1rem;
    }
    .btn-indigo {
        background-color: #4b4bb7;
        color: white;
    }
    .btn-indigo:hover {
        background-color: #3939a3;
    }
    .btn-mint {
        background-color: #2dd4bf;
        color: white;
    }
    .btn-mint:hover {
        background-color: #14b8a6;
    }
    .btn-orange {
        background-color: #f97316;
        color: white;
    }
    .btn-orange:hover {
        background-color: #ea580c;
    }
    .select2-container .select2-selection--single {
    height: 38px !important;
    border: 1px solid #ced4da;
    border-radius: 6px;
    display: flex;
    align-items: center;
    padding-left: 8px;
    padding-top: 3px;
}

.select2-selection__arrow {
    top: 6px !important;
}

</style>
<!-- ✅ Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- ✅ jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- ✅ Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-dark">📊 Reports Module</h1>
    </div>

    <div class="row">
        <!-- Driver Commission Report -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header text-white" style="background-color:#B87333;">
                    🚕 Driver Commission Report
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.driver_commission') }}">
                        <div class="mb-3">
    <label class="form-label">Select Driver No</label>
    <select name="driver_id" id="driverSelect" class="form-select" required>
        <option value="">-- Select Driver --</option>
        @foreach($driversList as $driver)
            <option value="{{ $driver['id'] }}">{{ $driver['name'] }} - {{ $driver['id'] }}</option>
        @endforeach
    </select>
</div>


                        <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">From Date</label>
                            <input type="date" name="from_date" class="form-control" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">To Date</label>
                            <input type="date" name="to_date" class="form-control" required>
                        </div>
                        </div>
                        <button type="submit" class="btn btn-indigo w-100">
                            <i class="bi bi-bar-chart-line me-1"></i> Generate Report
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Turnover Report -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header text-white bg-warning bg-opacity-50">
                    💷 Turnover Report
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.turnover') }}">
                        <div class="mb-3">
                            <label class="form-label">From Date</label>
                            <input type="date" name="from_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">To Date</label>
                            <input type="date" name="to_date" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-mint w-100">
                            <i class="bi bi-cash-coin me-1"></i> Generate Report
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Customer Report -->
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header text-white" style="background-color: #ac8138;">
                    👥 Customer Report
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.customer') }}">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">From Date</label>
                                <input type="date" name="from_date" class="form-control" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">To Date</label>
                                <input type="date" name="to_date" class="form-control" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Customer Type</label>
                                <select name="customer_type" class="form-select">
                                    <option value="">All</option>
                                    <option value="account">Account</option>
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
    <label class="form-label">Select Customer No</label>
    <select name="customer_id" id="customerSelect" class="form-select">
        <option value="">-- Select Customer --</option>
        @foreach($customersList as $customer)
            <option value="{{ $customer['id'] }}">{{ $customer['name'] }} - {{ $customer['id'] }}</option>
        @endforeach
    </select>
</div>

                            <div class="col-md-1 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-orange w-100">
                                    <i class="bi bi-search me-1"></i> Go
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#driverSelect').select2({
        placeholder: "Search driver...",
        allowClear: true,
        width: '100%'
    });
});
</script>
<script>
$(document).ready(function() {
    $('#driverSelect, #customerSelect').select2({
        placeholder: "Search...",
        allowClear: true,
        width: '100%'
    });
});
</script>


@endsection
