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

    /* 🔹 Checkbox Dropdown Styles */
    .status-dropdown-btn {
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 6px;
        min-height: 38px;
        cursor: pointer;
        font-size: 0.92rem;
    }
    .status-dropdown-btn:focus, .status-dropdown-btn:active {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .status-dropdown-menu {
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        border: 1px solid #e2e8f0;
        min-width: 220px;
        max-height: 290px;
        overflow-y: auto;
        padding: 8px 10px;
        z-index: 1050;
    }
    .status-dropdown-menu .form-check {
        padding: 4px 6px 4px 26px;
        border-radius: 4px;
        transition: background-color 0.15s ease;
        margin-bottom: 2px;
    }
    .status-dropdown-menu .form-check:hover {
        background-color: #f1f5f9;
    }
    .status-dropdown-menu .form-check-label {
        cursor: pointer;
        font-size: 0.88rem;
        user-select: none;
        width: 100%;
        display: block;
    }
    .status-dropdown-menu .form-check-input {
        cursor: pointer;
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
                            <label class="form-label fw-semibold">Select Driver No</label>
                            <select name="driver_id" id="driverSelect" class="form-select" required>
                                <option value="">-- Select Driver --</option>
                                @foreach($driversList as $driver)
                                    <option value="{{ $driver['id'] }}">{{ !empty($driver['call_sign']) ? '[' . $driver['call_sign'] . '] ' : '' }}{{ $driver['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label fw-semibold">From Date</label>
                                <input type="date" name="from_date" class="form-control" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label fw-semibold">To Date</label>
                                <input type="date" name="to_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Booking Status</label>
                            <div class="dropdown status-dropdown-wrapper">
                                <button class="form-select text-start status-dropdown-btn d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                    <span class="status-btn-text text-truncate">Completed (1)</span>
                                </button>
                                <div class="dropdown-menu status-dropdown-menu w-100">
                                    <div class="form-check mb-2 pb-2 border-bottom">
                                        <input class="form-check-input status-select-all" type="checkbox" id="dc_all">
                                        <label class="form-check-label fw-bold text-primary" for="dc_all">All Statuses</label>
                                    </div>
                                    <div class="status-items-container">
                                        <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="completed" id="dc_s_completed" checked><label class="form-check-label" for="dc_s_completed">Completed</label></div>
                                        <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="pending" id="dc_s_pending"><label class="form-check-label" for="dc_s_pending">Pending</label></div>
                                        <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="accepted" id="dc_s_accepted"><label class="form-check-label" for="dc_s_accepted">Accepted</label></div>
                                        <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="onroute" id="dc_s_onroute"><label class="form-check-label" for="dc_s_onroute">On Route</label></div>
                                        <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="arrived" id="dc_s_arrived"><label class="form-check-label" for="dc_s_arrived">Arrived</label></div>
                                        <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="pickedup" id="dc_s_pickedup"><label class="form-check-label" for="dc_s_pickedup">Picked Up</label></div>
                                        <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="job_cancelled" id="dc_s_cancelled"><label class="form-check-label" for="dc_s_cancelled">Cancelled</label></div>
                                        <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="no_show" id="dc_s_noshow"><label class="form-check-label" for="dc_s_noshow">No Show</label></div>
                                    </div>
                                </div>
                                <input type="hidden" name="booking_status" class="status-hidden-input" value="completed">
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
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label fw-semibold">From Date</label>
                                <input type="date" name="from_date" class="form-control" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label fw-semibold">To Date</label>
                                <input type="date" name="to_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Booking Status</label>
                            <div class="dropdown status-dropdown-wrapper">
                                <button class="form-select text-start status-dropdown-btn d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                    <span class="status-btn-text text-truncate">Completed (1)</span>
                                </button>
                                <div class="dropdown-menu status-dropdown-menu w-100">
                                    <div class="form-check mb-2 pb-2 border-bottom">
                                        <input class="form-check-input status-select-all" type="checkbox" id="to_all">
                                        <label class="form-check-label fw-bold text-primary" for="to_all">All Statuses</label>
                                    </div>
                                    <div class="status-items-container">
                                        <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="completed" id="to_s_completed" checked><label class="form-check-label" for="to_s_completed">Completed</label></div>
                                        <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="pending" id="to_s_pending"><label class="form-check-label" for="to_s_pending">Pending</label></div>
                                        <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="accepted" id="to_s_accepted"><label class="form-check-label" for="to_s_accepted">Accepted</label></div>
                                        <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="onroute" id="to_s_onroute"><label class="form-check-label" for="to_s_onroute">On Route</label></div>
                                        <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="arrived" id="to_s_arrived"><label class="form-check-label" for="to_s_arrived">Arrived</label></div>
                                        <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="pickedup" id="to_s_pickedup"><label class="form-check-label" for="to_s_pickedup">Picked Up</label></div>
                                        <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="job_cancelled" id="to_s_cancelled"><label class="form-check-label" for="to_s_cancelled">Cancelled</label></div>
                                        <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="no_show" id="to_s_noshow"><label class="form-check-label" for="to_s_noshow">No Show</label></div>
                                    </div>
                                </div>
                                <input type="hidden" name="booking_status" class="status-hidden-input" value="completed">
                            </div>
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
                            <div class="col-md-2 mb-3">
                                <label class="form-label fw-semibold">From Date</label>
                                <input type="date" name="from_date" class="form-control" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label fw-semibold">To Date</label>
                                <input type="date" name="to_date" class="form-control" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label fw-semibold">Customer Type</label>
                                <select name="customer_type" class="form-select">
                                    <option value="">All</option>
                                    <option value="account">Account</option>
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label fw-semibold">Booking Status</label>
                                <div class="dropdown status-dropdown-wrapper">
                                    <button class="form-select text-start status-dropdown-btn d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                        <span class="status-btn-text text-truncate">Completed (1)</span>
                                    </button>
                                    <div class="dropdown-menu status-dropdown-menu w-100" style="min-width: 200px;">
                                        <div class="form-check mb-2 pb-2 border-bottom">
                                            <input class="form-check-input status-select-all" type="checkbox" id="cust_all">
                                            <label class="form-check-label fw-bold text-primary" for="cust_all">All Statuses</label>
                                        </div>
                                        <div class="status-items-container">
                                            <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="completed" id="cust_s_completed" checked><label class="form-check-label" for="cust_s_completed">Completed</label></div>
                                            <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="pending" id="cust_s_pending"><label class="form-check-label" for="cust_s_pending">Pending</label></div>
                                            <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="accepted" id="cust_s_accepted"><label class="form-check-label" for="cust_s_accepted">Accepted</label></div>
                                            <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="onroute" id="cust_s_onroute"><label class="form-check-label" for="cust_s_onroute">On Route</label></div>
                                            <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="arrived" id="cust_s_arrived"><label class="form-check-label" for="cust_s_arrived">Arrived</label></div>
                                            <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="pickedup" id="cust_s_pickedup"><label class="form-check-label" for="cust_s_pickedup">Picked Up</label></div>
                                            <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="job_cancelled" id="cust_s_cancelled"><label class="form-check-label" for="cust_s_cancelled">Cancelled</label></div>
                                            <div class="form-check"><input class="form-check-input status-item-cb" type="checkbox" value="no_show" id="cust_s_noshow"><label class="form-check-label" for="cust_s_noshow">No Show</label></div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="booking_status" class="status-hidden-input" value="completed">
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Select Customer No</label>
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
    $('#driverSelect, #customerSelect').select2({
        placeholder: "Search...",
        allowClear: true,
        width: '100%'
    });

    // 🔹 Function to update status dropdown display & hidden input value
    function updateStatusDropdown(wrapper) {
        const selectAllCb = wrapper.querySelector('.status-select-all');
        const itemCbs = wrapper.querySelectorAll('.status-item-cb');
        const btnText = wrapper.querySelector('.status-btn-text');
        const hiddenInput = wrapper.querySelector('.status-hidden-input');

        const total = itemCbs.length;
        const checked = Array.from(itemCbs).filter(cb => cb.checked);
        const checkedValues = checked.map(cb => cb.value);
        const checkedLabels = checked.map(cb => cb.nextElementSibling.innerText.trim());

        if (checked.length === total) {
            selectAllCb.checked = true;
            selectAllCb.indeterminate = false;
            btnText.innerText = 'All Statuses (' + total + ')';
            hiddenInput.value = 'all';
        } else if (checked.length === 0) {
            selectAllCb.checked = false;
            selectAllCb.indeterminate = false;
            btnText.innerText = 'Select Status';
            hiddenInput.value = '';
        } else {
            selectAllCb.checked = false;
            selectAllCb.indeterminate = true;
            if (checked.length <= 2) {
                btnText.innerText = checkedLabels.join(', ');
            } else {
                btnText.innerText = checked.length + ' Selected';
            }
            hiddenInput.value = checkedValues.join(',');
        }
    }

    document.querySelectorAll('.status-dropdown-wrapper').forEach(wrapper => {
        const selectAllCb = wrapper.querySelector('.status-select-all');
        const itemCbs = wrapper.querySelectorAll('.status-item-cb');

        if (selectAllCb) {
            selectAllCb.addEventListener('change', function() {
                itemCbs.forEach(cb => cb.checked = selectAllCb.checked);
                updateStatusDropdown(wrapper);
            });
        }

        itemCbs.forEach(cb => {
            cb.addEventListener('change', function() {
                updateStatusDropdown(wrapper);
            });
        });

        // Initialize display
        updateStatusDropdown(wrapper);
    });
});
</script>

@endsection
