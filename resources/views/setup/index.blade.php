@extends('layouts.app')

@section('content')
<style>
    :root {
        --gold-light: #fff8eb;
        --gold: #f4a261;
        --gold-dark: #b5651d;
        --brown: #7a4419;
        --primary-accent: #E6B04A;
    }

    .nav-tabs {
        border-bottom: 2px solid #eadfd5;
        gap: 6px;
    }

    .nav-tabs .nav-link {
        color: var(--brown);
        border: 1px solid transparent;
        border-radius: 10px 10px 0 0;
        font-weight: 600;
        padding: 10px 18px;
        transition: all 0.2s ease;
    }

    .nav-tabs .nav-link:hover {
        background-color: #fbf7f2;
        border-color: #eadfd5 #eadfd5 transparent;
        color: var(--gold-dark);
    }

    .nav-tabs .nav-link.active {
        background: linear-gradient(135deg, var(--gold-dark), #cf7925);
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(181, 101, 29, 0.25);
    }

    .btn-custom {
        background: linear-gradient(135deg, #E6B04A, #d49a37);
        color: #1a150d;
        border: none;
        font-weight: 700;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .btn-custom:hover {
        background: linear-gradient(135deg, #d49a37, #b5651d);
        color: white;
        box-shadow: 0 4px 10px rgba(181, 101, 29, 0.2);
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 0.2rem rgba(244, 162, 97, 0.25);
    }

    label {
        color: var(--brown);
        font-weight: 600;
        font-size: 0.88rem;
        margin-bottom: 4px;
    }

    h2, h3 {
        color: var(--gold-dark);
        font-weight: 700;
    }

    .card-header.bg-warning {
        background-color: var(--gold-dark) !important;
        color: white;
    }

    .tab-content {
        padding-top: 15px;
    }

    .form-card {
        background: #ffffff;
        border: 1px solid #eadfd5;
        border-radius: 16px;
        padding: 22px;
        box-shadow: 0 8px 24px rgba(83, 50, 24, 0.06);
        margin-bottom: 20px;
    }

    .form-card-title {
        color: var(--gold-dark);
        font-weight: 700;
        font-size: 1.15rem;
        border-bottom: 2px solid #f6ede3;
        padding-bottom: 12px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .setup-directory {
        background: #fff;
        border: 1px solid #eadfd5;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(83, 50, 24, 0.08);
        overflow: hidden;
    }

    .setup-directory-header {
        padding: 18px 20px;
        background: linear-gradient(135deg, #fffaf3, #fff);
        border-bottom: 1px solid #eadfd5;
    }

    .setup-table {
        margin-bottom: 0;
    }

    .setup-table th {
        border: 0;
        background: #fbf7f2;
        color: #7a4419;
        font-size: 0.76rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 12px 16px;
        font-weight: 700;
    }

    .setup-table td {
        border-color: #f0e8df;
        padding: 14px 16px;
        vertical-align: middle;
    }

    .driver-avatar {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: linear-gradient(135deg, #b5651d, #e49a52);
        color: white;
        font-weight: 700;
        flex: 0 0 40px;
    }

    .callsign-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
        font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, monospace;
        font-weight: 700;
        padding: 4px 9px;
        border-radius: 6px;
        font-size: 0.84rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }

    .reg-plate {
        background: #0f172a;
        color: #fbbf24;
        font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, monospace;
        font-weight: 700;
        font-size: 0.82rem;
        padding: 3px 8px;
        border-radius: 5px;
        border: 1px solid #334155;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        display: inline-block;
    }

    .vehicle-item-badge {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 10px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s ease;
    }

    .vehicle-item-badge:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .status-available { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .status-on_job { background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; }
    .status-break { background: #fef9c3; color: #854d0e; border: 1px solid #fef08a; }
    .status-waiting { background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; }

    .staff-directory {
        background: #fff;
        border: 1px solid #eadfd5;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(83, 50, 24, 0.08);
        overflow: hidden;
    }

    .staff-directory-header {
        padding: 18px 20px;
        background: linear-gradient(135deg, #fffaf3, #fff);
        border-bottom: 1px solid #eadfd5;
    }

    .staff-table {
        margin-bottom: 0;
    }

    .staff-table th {
        border: 0;
        background: #fbf7f2;
        color: #7a4419;
        font-size: 0.76rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 12px 18px;
    }

    .staff-table td {
        border-color: #f0e8df;
        padding: 14px 18px;
        vertical-align: middle;
    }

    .staff-avatar {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: linear-gradient(135deg, #b5651d, #e49a52);
        color: white;
        font-weight: 700;
        flex: 0 0 42px;
    }

    .staff-role {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .staff-role-admin {
        background: #e8f0ff;
        color: #2457a7;
    }

    .staff-role-collaborator {
        background: #fff3d6;
        color: #8b5a00;
    }

    .staff-action-btn {
        border-radius: 9px;
        font-weight: 600;
    }

    @media (max-width: 767.98px) {
        .staff-table th:nth-child(2),
        .staff-table td:nth-child(2) {
            display: none;
        }

        .staff-table th,
        .staff-table td {
            padding: 12px 10px;
        }
    }
</style>

{{-- Driver & Vehicle Mapping Logic --}}
@php
    $driverLookup = [];
    $driverCallSignLookup = [];
    $driverStatusLookup = [];
    $driverVehiclesMap = [];

    foreach ($drivers as $dKey => $driver) {
        $dId = (string)($driver['id'] ?? $dKey);
        $driverLookup[$dId] = $driver['name'] ?? 'Unknown';
        $driverCallSignLookup[$dId] = $driver['call_sign'] ?? $driver['callsign'] ?? '';
        $driverStatusLookup[$dId] = $driver['status'] ?? 'available';
        $driverVehiclesMap[$dId] = [];

        if ($dKey && (string)$dKey !== $dId) {
            $driverLookup[(string)$dKey] = $driver['name'] ?? 'Unknown';
            $driverCallSignLookup[(string)$dKey] = $driver['call_sign'] ?? $driver['callsign'] ?? '';
            $driverStatusLookup[(string)$dKey] = $driver['status'] ?? 'available';
            $driverVehiclesMap[(string)$dKey] = [];
        }
    }

    foreach ($vehicles as $vKey => $vehicle) {
        $vDriverId = (string)($vehicle['driver_id'] ?? '');
        if ($vDriverId !== '') {
            if (isset($driverVehiclesMap[$vDriverId])) {
                $driverVehiclesMap[$vDriverId][] = $vehicle;
            }
            foreach ($drivers as $dKey => $driver) {
                if ((string)($driver['id'] ?? '') === $vDriverId || (string)$dKey === $vDriverId) {
                    $dRealId = (string)($driver['id'] ?? $dKey);
                    if (isset($driverVehiclesMap[$dRealId])) {
                        $alreadyExists = false;
                        foreach ($driverVehiclesMap[$dRealId] as $existingV) {
                            if (($existingV['registration'] ?? '') === ($vehicle['registration'] ?? '') && ($existingV['make'] ?? '') === ($vehicle['make'] ?? '')) {
                                $alreadyExists = true;
                                break;
                            }
                        }
                        if (!$alreadyExists) {
                            $driverVehiclesMap[$dRealId][] = $vehicle;
                        }
                    }
                }
            }
        }
    }
@endphp

<div class="container-fluid mt-4" >
   <div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h2 class="mb-0 fw-bold">
            Setup Office Module
        </h2>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        
        <a href="{{ route('quick.links') }}" class="btn btn-gradient fw-semibold shadow-sm">
            <i class="bi bi-link me-1"></i> Quick Links 
        </a>
        
        <a href="{{ route('system.settings') }}" class="btn btn-gradient fw-semibold shadow-sm me-2">
            <i class="bi bi-gear-wide-connected me-1"></i> Settings
        </a>

        @if(session('staff_role') === 'super_admin')
            <button type="button" class="btn btn-outline-dark fw-semibold shadow-sm me-2" data-bs-toggle="modal" data-bs-target="#superAdminPasswordModal">
                <i class="bi bi-shield-lock me-1"></i> Change Password
            </button>
        @endif

        <button type="button" class="btn btn-outline-danger fw-semibold shadow-sm me-2" data-bs-toggle="modal" data-bs-target="#deleteJobByRefModal">
            <i class="bi bi-trash3 me-1"></i> Delete Job by Ref#
        </button>
        
        <a href="{{ route('notifications.history') }}" class="btn btn-gradient fw-semibold shadow-sm">
            <i class="bi bi-broadcast me-1"></i> Notification Broadcast
        </a>
        
    </div>
</div>

@if(session('staff_role') === 'super_admin')
<div class="modal fade" id="superAdminPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form method="POST" action="{{ route('setup.super-admin.password') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Change Super Admin Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" id="current_password" name="current_password" class="form-control" autocomplete="current-password" required>
                    </div>
                    <div class="mb-3">
                        <label for="super_admin_password" class="form-label">New Password</label>
                        <input type="password" id="super_admin_password" name="password" class="form-control" minlength="8" autocomplete="new-password" required>
                    </div>
                    <div>
                        <label for="super_admin_password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" id="super_admin_password_confirmation" name="password_confirmation" class="form-control" minlength="8" autocomplete="new-password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-custom">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('show_password_modal') || $errors->has('current_password') || $errors->has('password'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    bootstrap.Modal.getOrCreateInstance(document.getElementById('superAdminPasswordModal')).show();
});
</script>
@endif
@endif

<!-- Delete Job by Ref # Modal -->
<div class="modal fade" id="deleteJobByRefModal" tabindex="-1" aria-labelledby="deleteJobByRefLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title fw-bold" id="deleteJobByRefLabel">
                    <i class="bi bi-trash3 me-2"></i>Delete Job by Reference Number
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteJobByRefForm" method="POST" action="{{ route('setup.job.delete-by-ref') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-warning d-flex align-items-center mb-3">
                        <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-warning"></i>
                        <div>
                            <strong>Warning:</strong> Deleting a job will permanently remove it from the database.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="lookup_ref_no" class="form-label fw-bold">Enter Booking Reference #</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light fw-bold text-muted">#</span>
                            <input type="text" 
                                   id="lookup_ref_no" 
                                   name="ref_no" 
                                   class="form-control text-uppercase fw-semibold" 
                                   placeholder="e.g. CCW1001 or REF12345" 
                                   autocomplete="off"
                                   required>
                            <button type="button" class="btn btn-dark px-4" id="btnLookupJob">
                                <i class="bi bi-search me-1"></i> Verify Job
                            </button>
                        </div>
                        <div class="form-text">Type the Ref # and click 'Verify Job' to preview before deletion, or click 'Delete Job' below.</div>
                    </div>

                    <!-- Job Preview Card (Hidden by default) -->
                    <div id="jobLookupPreview" class="d-none mt-3">
                        <div class="card border border-danger shadow-sm rounded-3">
                            <div class="card-header bg-danger bg-opacity-10 fw-bold text-danger d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-info-circle me-1"></i> Job Found</span>
                                <span class="badge bg-danger" id="previewJobStatus">Status</span>
                            </div>
                            <div class="card-body bg-light">
                                <div class="row g-2 mb-2">
                                    <div class="col-sm-6">
                                        <div class="text-muted small">Reference No:</div>
                                        <div class="fw-bold fs-6 text-dark" id="previewJobRef"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="text-muted small">Passenger:</div>
                                        <div class="fw-bold text-dark" id="previewJobPassenger"></div>
                                    </div>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-sm-6">
                                        <div class="text-muted small">Pickup Address:</div>
                                        <div class="small fw-semibold text-dark text-break" id="previewJobPickup"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="text-muted small">Dropoff Address:</div>
                                        <div class="small fw-semibold text-dark text-break" id="previewJobDropoff"></div>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <div class="text-muted small">Date / Time:</div>
                                        <div class="small fw-semibold text-dark" id="previewJobDateTime"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="text-muted small">Total Fare:</div>
                                        <div class="fw-bold text-success" id="previewJobPrice"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="jobLookupAlert" class="alert alert-danger d-none mt-3 py-2 small" role="alert"></div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4 fw-bold" id="btnSubmitDeleteJob">
                        <i class="bi bi-trash3-fill me-1"></i> Permanently Delete Job
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
    
    <br>
    
    {{-- Display Success/Error Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h4 class="alert-heading">Validation Error!</h4>
            <p>Please correct the errors in the form before submitting.</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <ul class="nav nav-tabs" id="setupTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link @if(!session('active_tab') || session('active_tab') == 'staff') active @endif" id="staff-tab" data-bs-toggle="tab" data-bs-target="#staff" type="button">
                <i class="bi bi-people-fill me-1"></i> Staff Directory
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link @if(session('active_tab') == 'driver') active @endif" id="driver-tab" data-bs-toggle="tab" data-bs-target="#driver" type="button">
                <i class="bi bi-person-badge-fill me-1"></i> Drivers Directory
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link @if(session('active_tab') == 'vehicle') active @endif" id="vehicle-tab" data-bs-toggle="tab" data-bs-target="#vehicle" type="button">
                <i class="bi bi-car-front-fill me-1"></i> Vehicles Directory
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link @if(session('active_tab') == 'customer') active @endif" id="customer-tab" data-bs-toggle="tab" data-bs-target="#customer" type="button">
                <i class="bi bi-buildings-fill me-1"></i> Customer Accounts
            </button>
        </li>
    </ul>

    <div class="tab-content mt-3">

        {{-- 1. Staff Tab --}}
        <div class="tab-pane fade @if(!session('active_tab') || session('active_tab') == 'staff') show active @endif" id="staff" role="tabpanel">
            <div class="row">
                <div class="col-lg-8">
                    <div class="staff-directory mb-4">
                        <div class="staff-directory-header d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-1">Staff Members</h3>
                                <p class="text-muted small mb-0">Manage roles, passwords and account access.</p>
                            </div>
                            <span class="badge rounded-pill text-bg-dark px-3 py-2">{{ $staff->count() }} total</span>
                        </div>
                        <div class="table-responsive">
                        <table class="table staff-table align-middle">
                            <thead>
                                <tr>
                                    <th>Staff member</th>
                                    <th>Contact</th>
                                    <th>Role</th>
                                    <th>Dealt Bookings</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staff as $member)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="staff-avatar">{{ strtoupper(substr($member['name'] ?? 'S', 0, 1)) }}</span>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $member['name'] ?? 'N/A' }}</div>
                                                    <div class="text-muted small">Staff #{{ $member['id'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold text-dark"><i class="bi bi-envelope me-1 text-muted"></i>{{ $member['email'] ?? 'N/A' }}</div>
                                            <div class="small text-muted mt-1"><i class="bi bi-telephone me-1"></i>{{ $member['phone'] ?? 'N/A' }}</div>
                                        </td>
                                        <td>
                                            @php $memberRole = $member['role'] ?? 'collaborator'; @endphp
                                            <span class="staff-role staff-role-{{ $memberRole }}">
                                                <i class="bi {{ $memberRole === 'admin' ? 'bi-shield-check' : 'bi-person-check' }}"></i>
                                                {{ ucfirst($memberRole) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $staffBookings = $member['bookings'] ?? [];
                                                $bCount = count($staffBookings);
                                            @endphp
                                            @if($bCount > 0)
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary fw-semibold staff-bookings-btn shadow-sm"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#staffBookingsModal"
                                                        data-staff-name="{{ $member['name'] }}"
                                                        data-staff-id="{{ $member['id'] }}"
                                                        data-bookings='@json($staffBookings)'>
                                                    <i class="bi bi-briefcase me-1"></i> {{ $bCount }} {{ Str::plural('Booking', $bCount) }}
                                                </button>
                                            @else
                                                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2">
                                                    0 Bookings
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end" style="min-width: 210px;">
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-dark staff-action-btn manage-staff-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#manageStaffModal"
                                                    data-name="{{ $member['name'] }}"
                                                    data-role="{{ $memberRole }}"
                                                    data-action="{{ route('setup.staff.access', $member['id']) }}">
                                                <i class="bi bi-sliders me-1"></i>Manage
                                            </button>
                                            <form method="POST" action="{{ route('setup.staff.destroy', $member['id']) }}" class="d-inline"
                                                  onsubmit="return confirm('Delete this staff account? It will immediately lose access.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger staff-action-btn"
                                                        {{ (int) session('staff_id') === (int) $member['id'] ? 'disabled' : '' }}>
                                                    <i class="bi bi-trash3 me-1"></i>Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-people fs-2 d-block mb-2"></i>No staff members added yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-card">
                        <div class="form-card-title">
                            <i class="bi bi-person-plus-fill text-warning"></i> Add Staff Member
                        </div>
                        <form method="POST" action="{{ route('setup.staff.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Full name" required value="{{ old('name') }}">
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" placeholder="staff@crowncarz.com" required value="{{ old('email') }}">
                            </div>
                            <div class="mb-3">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" minlength="8" required>
                            </div>
                            <div class="mb-3">
                                <label>Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" minlength="8" required>
                            </div>
                            <div class="mb-3">
                                <label>Phone Number</label>
                                <input type="text" name="phone" class="form-control" placeholder="Phone number" required value="{{ old('phone') }}">
                            </div>
                            <div class="mb-3">
                                <label>Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="collaborator" {{ old('role') === 'collaborator' ? 'selected' : '' }}>Collaborator — bookings only</option>
                                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin — full access</option>
                                </select>
                            </div>
                            <div class="alert alert-light border small py-2 mb-3">
                                <i class="bi bi-envelope-check me-1"></i>Login credentials will be emailed automatically.
                            </div>
                            <button type="submit" class="btn btn-custom w-100 py-2.5">
                                <i class="bi bi-person-plus me-1"></i> Add Staff Member
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Manage Staff Modal -->
            <div class="modal fade" id="manageStaffModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow rounded-4">
                        <form id="manageStaffForm" method="POST">
                            @csrf
                            <div class="modal-header border-0 pb-0">
                                <div>
                                    <h5 class="modal-title fw-bold">Manage Staff Access</h5>
                                    <p class="text-muted small mb-0" id="manageStaffName"></p>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body pt-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Role</label>
                                    <select name="role" id="manageStaffRole" class="form-select" required>
                                        <option value="collaborator">Collaborator — limited to bookings</option>
                                        <option value="admin">Admin — full office access</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">New Password (optional)</label>
                                    <input type="password" name="password" class="form-control" minlength="8" placeholder="Leave blank to keep current password">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" minlength="8" placeholder="Confirm password if changing">
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Staff Bookings Modal -->
            <div class="modal fade" id="staffBookingsModal" tabindex="-1" aria-labelledby="staffBookingsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                        <div class="modal-header bg-dark text-white py-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-briefcase-fill text-warning fs-5"></i>
                                <div>
                                    <h5 class="modal-title fw-bold mb-0" id="staffBookingsModalLabel">
                                        Bookings Handled by <span id="staffBookingsModalName" class="text-warning"></span>
                                    </h5>
                                    <span class="badge bg-secondary rounded-pill mt-1" id="staffBookingsSummary">0 Bookings</span>
                                </div>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 bg-light">
                            <div class="mb-3">
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" class="form-control border-start-0" id="staffBookingsFilterInput" placeholder="Filter bookings by Ref #, passenger, phone, address, status...">
                                </div>
                            </div>
                            <div class="table-responsive bg-white rounded-3 border shadow-sm" style="max-height: 520px; overflow-y: auto;">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                    <thead class="table-dark sticky-top">
                                        <tr>
                                            <th>Ref #</th>
                                            <th>Passenger</th>
                                            <th>Phone</th>
                                            <th>Route (Pickup &rarr; Dropoff)</th>
                                            <th>Date / Time</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="staffBookingsTableBody">
                                        <!-- Dynamically filled via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer bg-white">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Details Modal Component -->
            @include('partials.view_booking_modal')
        </div>

        {{-- 2. Drivers Tab (With Assigned Vehicle Details) --}}
        <div class="tab-pane fade @if(session('active_tab') == 'driver') show active @endif" id="driver" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    <div class="setup-directory mb-4">
                        <div class="setup-directory-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h3 class="mb-1"><i class="bi bi-person-badge-fill text-warning me-2"></i>Existing Drivers</h3>
                                <p class="text-muted small mb-0">Driver profiles, call signs, assigned vehicles, and real-time status.</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-warning fw-bold shadow-sm d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#addDriverModal">
                                    <i class="bi bi-person-plus-fill"></i> Add Driver
                                </button>
                                <span class="badge rounded-pill text-bg-dark px-3 py-2"><i class="bi bi-people-fill me-1"></i>{{ $drivers->count() }} Drivers</span>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table setup-table align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Driver</th>
                                        <th>Call Sign</th>
                                        <th>Assigned Vehicle</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                        <th>Address & Location</th>
                                        <th>BF</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($drivers as $driver)
                                        @php
                                            $dIdKey = (string)($driver['id'] ?? $loop->index);
                                            $assignedVehicles = $driverVehiclesMap[$dIdKey] ?? ($driverVehiclesMap[(string)($driver['id'] ?? '')] ?? []);
                                            $status = strtolower($driver['status'] ?? 'available');
                                            $bfVal = (float)($driver['brought_forward'] ?? 0);
                                            $driverPhoto = $driver['image'] ?? $driver['profile_image'] ?? null;
                                        @endphp
                                        <tr>
                                            <td><span class="text-muted fw-bold">{{ $loop->iteration }}</span></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2.5">
                                                    @if(!empty($driverPhoto))
                                                        <img src="{{ $driverPhoto }}" alt="{{ $driver['name'] ?? 'Driver' }}" class="rounded-3 shadow-sm border" style="width: 40px; height: 40px; object-fit: cover; flex-shrink: 0;" onerror="this.onerror=null; this.classList.add('d-none'); this.nextElementSibling.classList.remove('d-none');">
                                                        <span class="driver-avatar d-none">{{ strtoupper(substr($driver['name'] ?? 'D', 0, 1)) }}</span>
                                                    @else
                                                        <span class="driver-avatar">{{ strtoupper(substr($driver['name'] ?? 'D', 0, 1)) }}</span>
                                                    @endif
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $driver['name'] ?? 'N/A' }}</div>
                                                        <div class="text-muted small">Driver #{{ $driver['id'] ?? $loop->iteration }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if(!empty($driver['call_sign']))
                                                    <span class="callsign-badge">
                                                        <i class="bi bi-broadcast"></i> {{ $driver['call_sign'] }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-muted border">No CallSign</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($assignedVehicles) && count($assignedVehicles) > 0)
                                                    <div class="d-flex flex-column gap-1.5" style="min-width: 170px;">
                                                        @foreach($assignedVehicles as $v)
                                                            <div class="vehicle-item-badge">
                                                                <span class="reg-plate">{{ $v['registration'] ?? 'N/A' }}</span>
                                                                <div class="small">
                                                                    <div class="fw-semibold text-dark leading-tight">{{ $v['make'] ?? '' }} {{ $v['model'] ?? '' }}</div>
                                                                    @if(!empty($v['color']))
                                                                        <span class="text-muted" style="font-size: 11px;"><i class="bi bi-circle-fill me-1 text-secondary" style="font-size: 7px;"></i>{{ $v['color'] }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="badge rounded-pill bg-light text-muted border px-2.5 py-1.5">
                                                        <i class="bi bi-dash-circle me-1"></i>No Vehicle
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="small fw-semibold text-dark"><i class="bi bi-envelope me-1 text-muted"></i>{{ $driver['email'] ?? 'N/A' }}</div>
                                                <div class="small text-muted mt-1"><i class="bi bi-telephone me-1"></i>{{ $driver['phone'] ?? 'N/A' }}</div>
                                            </td>
                                            <td>
                                                <span class="status-pill status-{{ $status }}">
                                                    <i class="bi bi-circle-fill" style="font-size: 6px;"></i>
                                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if(!empty($driver['address']) && $driver['address'] !== '-')
                                                    <div class="small text-dark text-truncate" style="max-width: 170px;" title="{{ $driver['address'] }}">
                                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $driver['address'] }}
                                                    </div>
                                                @endif
                                                @if(!empty($driver['latitude']) && $driver['latitude'] !== '-' && $driver['latitude'] !== 'N/A')
                                                    <div class="small text-muted font-monospace mt-0.5" style="font-size: 11px;">
                                                        {{ round((float)$driver['latitude'], 4) }}, {{ round((float)$driver['longitude'], 4) }}
                                                    </div>
                                                @else
                                                    <span class="text-muted small">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($bfVal > 0)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">
                                                        £{{ number_format($bfVal, 2) }}
                                                    </span>
                                                @elseif($bfVal < 0)
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold">
                                                        -£{{ number_format(abs($bfVal), 2) }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-muted border fw-semibold">
                                                        £0.00
                                                    </span>
                                                @endif
                                            </td>
                                            <td style="min-width: 160px;">
                                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                    <a href="{{ route('drivers.bf.history', $driver['id']) }}"
                                                       class="btn btn-sm btn-outline-warning fw-semibold shadow-sm d-inline-flex align-items-center gap-1" style="font-size: 11.5px; border-radius: 7px; padding: 4px 8px;">
                                                        <i class="bi bi-receipt"></i> BF Details
                                                    </a>
                                                    <button 
                                                        type="button"
                                                        class="btn btn-sm btn-outline-primary fw-semibold shadow-sm edit-driver-btn d-inline-flex align-items-center gap-1"
                                                        style="font-size: 11.5px; border-radius: 7px; padding: 4px 8px;"
                                                        data-id="{{ $driver['id'] }}"
                                                        data-name="{{ $driver['name'] }}"
                                                        data-call_sign="{{ $driver['call_sign'] ?? '' }}"
                                                        data-email="{{ $driver['email'] ?? '' }}"
                                                        data-phone="{{ $driver['phone'] ?? '' }}"
                                                        data-status="{{ $driver['status'] ?? 'available' }}"
                                                        data-address="{{ $driver['address'] ?? '' }}"
                                                        data-latitude="{{ $driver['latitude'] ?? '' }}"
                                                        data-longitude="{{ $driver['longitude'] ?? '' }}"
                                                        data-image="{{ $driverPhoto ?? '' }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editDriverModal"
                                                    >
                                                        <i class="bi bi-pencil-square"></i> Edit
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5 text-muted">
                                                <i class="bi bi-people fs-2 d-block mb-2"></i>No drivers added yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add Driver Modal --}}
        <div class="modal fade" id="addDriverModal" tabindex="-1" aria-labelledby="addDriverModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form method="POST" action="{{ route('setup.driver.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                        <div class="modal-header bg-dark text-white py-3">
                            <h5 class="modal-title fw-bold" id="addDriverModalLabel">
                                <i class="bi bi-person-plus-fill text-warning me-2"></i>Add New Driver
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body p-4 bg-light">
                            <div class="bg-white p-3 rounded-3 border">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Driver Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" placeholder="Driver Full Name" required value="{{ old('name') }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Call Sign</label>
                                        <input type="text" name="call_sign" class="form-control" placeholder="e.g. D-101, 099" value="{{ old('call_sign') }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" placeholder="driver@email.com" required value="{{ old('email') }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                                        <input type="text" name="phone" class="form-control" placeholder="07xxxxxxxxx" required value="{{ old('phone') }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Driver Photo / Profile Image <span class="badge bg-secondary-subtle text-secondary small fw-normal">Optional</span></label>
                                        <input type="file" name="image" class="form-control" accept="image/*">
                                        <div class="form-text small text-muted">Upload photo (JPEG, PNG, WEBP - Max 5MB). Stored in Firebase Storage.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-select" required>
                                            <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                                            <option value="on_job" {{ old('status') == 'on_job' ? 'selected' : '' }}>On Job</option>
                                            <option value="break" {{ old('status') == 'break' ? 'selected' : '' }}>Break</option>
                                            <option value="waiting" {{ old('status') == 'waiting' ? 'selected' : '' }}>Waiting</option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Base Address <span class="text-danger">*</span></label>
                                        <input type="text" id="driver_address" name="address" class="form-control" placeholder="Enter driver base address (e.g., Reading Train Station)" value="{{ old('address') }}" required>
                                        <div id="address-status" class="small text-muted mt-1"></div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Latitude</label>
                                        <input type="text" id="latitude" name="latitude" class="form-control" placeholder="Auto-filled from address" value="{{ old('latitude') }}" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Longitude</label>
                                        <input type="text" id="longitude" name="longitude" class="form-control" placeholder="Auto-filled from address" value="{{ old('longitude') }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-white border-top">
                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning fw-bold px-4">
                                <i class="bi bi-person-plus-fill me-1"></i> Save Driver
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Edit Driver Modal --}}
        <div class="modal fade" id="editDriverModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form method="POST" id="editDriverForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                        <div class="modal-header bg-dark text-white py-3">
                            <h5 class="modal-title fw-bold">
                                <i class="bi bi-pencil-square text-warning me-2"></i>Update Driver Profile
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body p-4 bg-light">
                            <input type="hidden" id="edit_driver_id">

                            <div class="row g-3 bg-white p-3 rounded-3 border">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Full Name</label>
                                    <input type="text" name="name" id="edit_name" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Call Sign</label>
                                    <input type="text" name="call_sign" id="edit_call_sign" class="form-control" placeholder="e.g. D-101">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email Address</label>
                                    <input type="email" name="email" id="edit_email" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phone Number</label>
                                    <input type="text" name="phone" id="edit_phone" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Driver Status</label>
                                    <select name="status" id="edit_status" class="form-select">
                                        <option value="available">Available</option>
                                        <option value="on_job">On Job</option>
                                        <option value="break">Break</option>
                                        <option value="waiting">Waiting</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Update Photo <span class="badge bg-secondary-subtle text-secondary small fw-normal">Optional</span></label>
                                    <div class="d-flex align-items-center gap-2">
                                        <img id="edit_driver_preview_img" src="" alt="Driver" class="rounded-3 border d-none" style="width: 40px; height: 40px; object-fit: cover;">
                                        <input type="file" name="image" class="form-control" accept="image/*">
                                    </div>
                                    <div class="form-text small text-muted">Leave empty to keep existing photo.</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Base Address</label>
                                    <input type="text" name="address" id="edit_address" class="form-control">
                                    <div id="edit-address-status" class="small text-muted mt-1"></div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Latitude</label>
                                    <input type="text" name="latitude" id="edit_latitude" class="form-control" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Longitude</label>
                                    <input type="text" name="longitude" id="edit_longitude" class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-white border-0">
                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                <i class="bi bi-check2-circle me-1"></i> Update Driver
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 3. Vehicles Tab (With Assigned Driver Details) --}}
        <div class="tab-pane fade @if(session('active_tab') == 'vehicle') show active @endif" id="vehicle" role="tabpanel">
            <div class="row">
                <div class="col-lg-8">
                    <div class="setup-directory mb-4">
                        <div class="setup-directory-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h3 class="mb-1"><i class="bi bi-car-front-fill text-warning me-2"></i>Existing Vehicles</h3>
                                <p class="text-muted small mb-0">Fleet categories, registration plates, colors, and assigned drivers.</p>
                            </div>
                            <span class="badge rounded-pill text-bg-dark px-3 py-2"><i class="bi bi-car-front me-1"></i>{{ $vehicles->count() }} Vehicles</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table setup-table align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Vehicle / Category</th>
                                        <th>Registration</th>
                                        <th>Color</th>
                                        <th>Assigned Driver</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vehicles as $vehicle)
                                        @php
                                            $vDriverId = (string)($vehicle['driver_id'] ?? '');
                                            $assignedDriverName = $driverLookup[$vDriverId] ?? null;
                                            $assignedDriverCallSign = $driverCallSignLookup[$vDriverId] ?? null;
                                        @endphp
                                        <tr>
                                            <td><span class="text-muted fw-bold">{{ $loop->iteration }}</span></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2.5">
                                                    <div class="driver-avatar" style="background: linear-gradient(135deg, #334155, #64748b);">
                                                        <i class="bi bi-car-front-fill text-warning fs-5"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $vehicle['make'] ?? 'N/A' }}</div>
                                                        <div class="text-muted small">{{ $vehicle['model'] ?? 'Standard' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="reg-plate fs-7 shadow-sm">
                                                    {{ $vehicle['registration'] ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if(!empty($vehicle['color']))
                                                    <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-semibold">
                                                        <i class="bi bi-palette-fill me-1 text-muted"></i>{{ $vehicle['color'] }}
                                                    </span>
                                                @else
                                                    <span class="text-muted small">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($assignedDriverName && $assignedDriverName !== 'Unknown')
                                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                                        @if(!empty($assignedDriverCallSign))
                                                            <span class="callsign-badge">
                                                                <i class="bi bi-broadcast"></i> {{ $assignedDriverCallSign }}
                                                            </span>
                                                        @endif
                                                        <span class="fw-bold text-dark">{{ $assignedDriverName }}</span>
                                                    </div>
                                                @else
                                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary border px-2.5 py-1.5">
                                                        <i class="bi bi-person-x me-1"></i>Unassigned
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="bi bi-car-front fs-2 d-block mb-2"></i>No vehicles added yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-card">
                        <div class="form-card-title">
                            <i class="bi bi-plus-circle-fill text-warning"></i> Add Vehicle
                        </div>
                        <form method="POST" action="{{ route('setup.vehicle.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label>Make / Category</label>
                                <select name="make" class="form-select" required>
                                    <option value="" disabled selected>-- Select Make --</option>
                                    <option value="Saloon" {{ old('make') == 'Saloon' ? 'selected' : '' }}>Saloon</option>
                                    <option value="Estate" {{ old('make') == 'Estate' ? 'selected' : '' }}>Estate</option>
                                    <option value="MPV" {{ old('make') == 'MPV' ? 'selected' : '' }}>MPV</option>
                                    <option value="8 Seater" {{ old('make') == '8 Seater' ? 'selected' : '' }}>8 Seater</option>
                                    <option value="Executive" {{ old('make') == 'Executive' ? 'selected' : '' }}>Executive</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Model</label>
                                <input type="text" name="model" class="form-control" placeholder="e.g. Mercedes E-Class, Toyota Prius" required value="{{ old('model') }}">
                            </div>
                            <div class="mb-3">
                                <label>Color</label>
                                <input type="text" name="color" class="form-control" placeholder="e.g. Black, Silver, White" required value="{{ old('color') }}">
                            </div>
                            <div class="mb-3">
                                <label>Registration Number</label>
                                <input type="text" name="registration" class="form-control" placeholder="e.g. LEN-2302" required value="{{ old('registration') }}">
                            </div>
                            <div class="mb-3">
                                <label>Assign to Driver</label>
                                <select name="driver_id" class="form-select" required>
                                    <option value="">-- Select Driver --</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver['id'] ?? '' }}" {{ old('driver_id') == ($driver['id'] ?? '') ? 'selected' : '' }}>
                                            {{ !empty($driver['call_sign']) ? '[' . $driver['call_sign'] . '] ' : '' }}{{ $driver['name'] ?? 'Unknown Driver' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-custom w-100 py-2.5">
                                <i class="bi bi-plus-circle me-1"></i> Add Vehicle
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Customer Accounts Tab --}}
        <div class="tab-pane fade @if(session('active_tab') == 'customer') show active @endif" id="customer" role="tabpanel">
            <div class="row">
                <div class="col-lg-8">
                    <div class="setup-directory mb-4">
                        <div class="setup-directory-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h3 class="mb-1"><i class="bi bi-buildings-fill text-warning me-2"></i>Existing Customer Accounts</h3>
                                <p class="text-muted small mb-0">Corporate and business account profiles and billing information.</p>
                            </div>
                            <span class="badge rounded-pill text-bg-dark px-3 py-2"><i class="bi bi-buildings me-1"></i>{{ $customers->count() }} Accounts</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table setup-table align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Business Name</th>
                                        <th>Address</th>
                                        <th>Contact Information</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customers as $customer)
                                        <tr>
                                            <td><span class="text-muted fw-bold">{{ $loop->iteration }}</span></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2.5">
                                                    <div class="driver-avatar" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6);">
                                                        <i class="bi bi-building fs-5 text-white"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $customer['business_name'] ?? 'N/A' }}</div>
                                                        <div class="text-muted small">Account #{{ $customer['id'] ?? $loop->iteration }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small text-dark text-truncate" style="max-width: 250px;">
                                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $customer['address'] ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small fw-semibold text-dark"><i class="bi bi-envelope me-1 text-muted"></i>{{ $customer['email'] ?? 'N/A' }}</div>
                                                <div class="small text-muted mt-1"><i class="bi bi-telephone me-1"></i>{{ $customer['phone'] ?? 'N/A' }}</div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="bi bi-buildings fs-2 d-block mb-2"></i>No customer accounts added yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-card">
                        <div class="form-card-title">
                            <i class="bi bi-plus-circle-fill text-warning"></i> Add Customer Account
                        </div>
                        <form method="POST" action="{{ route('setup.customer.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label>Business Name</label>
                                <input type="text" name="business_name" class="form-control" placeholder="e.g. Acme Corp Ltd" required value="{{ old('business_name') }}">
                            </div>
                            <div class="mb-3">
                                <label>Address</label>
                                <input type="text" name="address" class="form-control" placeholder="Office / Billing address" required value="{{ old('address') }}">
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" placeholder="billing@company.com" required value="{{ old('email') }}">
                            </div>
                            <div class="mb-3">
                                <label>Phone Number</label>
                                <input type="text" name="phone" class="form-control" placeholder="Contact phone number" required value="{{ old('phone') }}">
                            </div>
                            <button type="submit" class="btn btn-custom w-100 py-2.5">
                                <i class="bi bi-plus-circle me-1"></i> Add Customer Account
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- JavaScript to maintain active tab on error/submit --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const manageForm = document.getElementById('manageStaffForm');
    const manageName = document.getElementById('manageStaffName');
    const manageRole = document.getElementById('manageStaffRole');

    document.querySelectorAll('.manage-staff-btn').forEach(button => {
        button.addEventListener('click', function () {
            manageForm.action = this.dataset.action;
            manageName.textContent = this.dataset.name;
            manageRole.value = this.dataset.role;
            manageForm.querySelectorAll('input[type="password"]').forEach(input => input.value = '');
        });
    });

    document.querySelectorAll('.edit-driver-btn').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            document.getElementById('editDriverForm').action = `/admin/setup/driver/${id}`;

            document.getElementById('edit_name').value = this.dataset.name || '';
            document.getElementById('edit_call_sign').value = this.dataset.call_sign || '';
            document.getElementById('edit_email').value = this.dataset.email || '';
            document.getElementById('edit_phone').value = this.dataset.phone || '';
            document.getElementById('edit_status').value = this.dataset.status || 'available';
            document.getElementById('edit_address').value = this.dataset.address || '';
            document.getElementById('edit_latitude').value = this.dataset.latitude ?? '';
            document.getElementById('edit_longitude').value = this.dataset.longitude ?? '';

            const previewImg = document.getElementById('edit_driver_preview_img');
            if (previewImg) {
                if (this.dataset.image && this.dataset.image.trim() !== '') {
                    previewImg.src = this.dataset.image;
                    previewImg.classList.remove('d-none');
                } else {
                    previewImg.src = '';
                    previewImg.classList.add('d-none');
                }
            }
        });
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const activeTab = "{{ session('active_tab') }}";
        if (activeTab) {
            // Find the tab button element using its ID
            const tabEl = document.getElementById(activeTab + '-tab');
            if (tabEl) {
                // Ensure all tabs are deactivated
                document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
                document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('show', 'active'));

                // Activate the specific tab button
                tabEl.classList.add('active');
                
                // Activate the specific tab content pane
                const targetPane = document.getElementById(activeTab);
                if (targetPane) {
                    targetPane.classList.add('show', 'active');
                }
            }
        }
    });
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    function setupGeocoding(addressInputId, latInputId, lonInputId, statusDivId) {
        const addressInput = document.getElementById(addressInputId);
        const latInput = document.getElementById(latInputId);
        const lonInput = document.getElementById(lonInputId);
        const statusDiv = document.getElementById(statusDivId);

        if (addressInput && latInput && lonInput) {
            addressInput.addEventListener('blur', async function () {
                const address = this.value.trim();
                if (!address) return;

                if (statusDiv) statusDiv.textContent = "Fetching coordinates...";
                try {
                    const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`);
                    const data = await res.json();
                    if (data.length > 0) {
                        latInput.value = data[0].lat;
                        lonInput.value = data[0].lon;
                        if (statusDiv) statusDiv.textContent = "✅ Coordinates fetched successfully.";
                    } else {
                        if (statusDiv) statusDiv.textContent = "⚠️ Address not found.";
                    }
                } catch (e) {
                    console.error(e);
                    if (statusDiv) statusDiv.textContent = "❌ Error fetching coordinates.";
                }
            });
        }
    }

    setupGeocoding('driver_address', 'latitude', 'longitude', 'address-status');
    setupGeocoding('edit_address', 'edit_latitude', 'edit_longitude', 'edit-address-status');
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const lookupInput = document.getElementById('lookup_ref_no');
    const btnLookup = document.getElementById('btnLookupJob');
    const previewCard = document.getElementById('jobLookupPreview');
    const alertBox = document.getElementById('jobLookupAlert');
    const deleteForm = document.getElementById('deleteJobByRefForm');

    async function lookupJob() {
        const ref = lookupInput ? lookupInput.value.trim() : '';
        if (!ref) {
            showAlert('Please enter a Reference # to verify.');
            return;
        }

        if (alertBox) alertBox.classList.add('d-none');
        if (previewCard) previewCard.classList.add('d-none');
        if (btnLookup) {
            btnLookup.disabled = true;
            btnLookup.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Searching...';
        }

        try {
            const response = await fetch("{{ route('setup.job.find-by-ref') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ ref_no: ref })
            });

            const data = await response.json();

            if (data.success && data.jobs && data.jobs.length > 0) {
                const job = data.jobs[0];
                document.getElementById('previewJobRef').textContent = job.ref_no || ref;
                document.getElementById('previewJobPassenger').textContent = (job.passenger_name || 'N/A') + (job.phone_no ? ' (' + job.phone_no + ')' : '');
                document.getElementById('previewJobPickup').textContent = job.pickup_address || 'N/A';
                document.getElementById('previewJobDropoff').textContent = job.dropoff_address || 'N/A';
                document.getElementById('previewJobDateTime').textContent = (job.pickup_date || '') + ' ' + (job.pickup_time || '');
                document.getElementById('previewJobPrice').textContent = '£' + parseFloat(job.price || 0).toFixed(2);
                document.getElementById('previewJobStatus').textContent = job.status || 'Pending';
                
                if (previewCard) previewCard.classList.remove('d-none');
            } else {
                showAlert(data.message || 'No job found with this Reference #.');
            }
        } catch (err) {
            showAlert('Network error while searching for the job.');
        } finally {
            if (btnLookup) {
                btnLookup.disabled = false;
                btnLookup.innerHTML = '<i class="bi bi-search me-1"></i> Verify Job';
            }
        }
    }

    function showAlert(msg) {
        if (alertBox) {
            alertBox.textContent = msg;
            alertBox.classList.remove('d-none');
        }
    }

    if (btnLookup) {
        btnLookup.addEventListener('click', lookupJob);
    }

    if (lookupInput) {
        lookupInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                lookupJob();
            }
        });
    }

    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            const ref = lookupInput ? lookupInput.value.trim() : '';
            if (!ref) {
                e.preventDefault();
                showAlert('Please enter a Reference # to delete.');
                return;
            }
            if (!confirm(`Are you sure you want to PERMANENTLY DELETE job #${ref}? This action cannot be undone.`)) {
                e.preventDefault();
            }
        });
    }

    // Staff Bookings Modal Logic
    let currentStaffBookings = [];
    const staffTableBody = document.getElementById('staffBookingsTableBody');
    const staffNameSpan = document.getElementById('staffBookingsModalName');
    const staffSummaryBadge = document.getElementById('staffBookingsSummary');
    const staffFilterInput = document.getElementById('staffBookingsFilterInput');

    function renderStaffBookings(list) {
        if (!staffTableBody) return;
        staffTableBody.innerHTML = '';

        if (!list || list.length === 0) {
            staffTableBody.innerHTML = `<tr><td colspan="8" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-1"></i>No bookings found for this staff member.</td></tr>`;
            return;
        }

        list.forEach((b, index) => {
            const tr = document.createElement('tr');
            tr.className = 'staff-booking-row';
            tr.style.cursor = 'pointer';
            tr.title = 'Click to view full booking details & activity history';

            const statusLower = (b.status || '').toLowerCase();
            let statusBadge = 'bg-warning text-dark';
            if (statusLower === 'completed') statusBadge = 'bg-success';
            else if (statusLower === 'cancelled' || statusLower === 'declined' || statusLower === 'job_cancelled') statusBadge = 'bg-danger';
            else if (statusLower === 'accepted' || statusLower === 'allocated' || statusLower === 'dispatched') statusBadge = 'bg-info text-dark';

            const esc = (typeof window.escapeBookingHtml === 'function') ? window.escapeBookingHtml : (str) => {
                if (str === null || str === undefined) return '';
                return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
            };

            const refDisplay = esc(b.ref_no || b.id || '-');
            const passengerDisplay = esc(b.passenger_name || 'N/A');
            const phoneDisplay = esc(b.phone_no || '-');
            const pickupDisplay = esc(b.pickup_address || '-');
            const dropoffDisplay = esc(b.dropoff_address || '-');
            const dateDisplay = esc((b.pickup_date || '') + ' ' + (b.pickup_time || ''));
            const fareDisplay = parseFloat(b.price || 0).toFixed(2);
            const statusDisplay = esc(b.status || 'Pending');

            tr.innerHTML = `
                <td><span class="badge bg-dark fw-bold staff-ref-badge" style="cursor: pointer;">${refDisplay}</span></td>
                <td class="fw-semibold text-dark">${passengerDisplay}</td>
                <td><small class="text-muted">${phoneDisplay}</small></td>
                <td>
                    <div class="small fw-semibold text-dark" style="max-width: 280px;">
                        <div class="text-truncate text-success" title="${pickupDisplay}"><i class="bi bi-geo-alt me-1"></i>${pickupDisplay}</div>
                        <div class="text-truncate text-danger" title="${dropoffDisplay}"><i class="bi bi-flag me-1"></i>${dropoffDisplay}</div>
                    </div>
                </td>
                <td><small class="text-muted">${dateDisplay}</small></td>
                <td class="fw-bold text-success">£${fareDisplay}</td>
                <td><span class="badge ${statusBadge}">${statusDisplay}</span></td>
                <td class="text-center" onclick="event.stopPropagation();">
                    <button type="button" 
                            class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1 view-booking-staff-btn px-2.5 py-1" 
                            style="font-size: 11.5px; border-radius: 6px; font-weight: 600;"
                            data-booking-id="${esc(b.id || b.booking_id || '')}"
                            title="View Full Booking Details">
                        <i class="bi bi-eye"></i> View
                    </button>
                </td>
            `;

            // Clicking on row opens the details modal
            tr.addEventListener('click', function(e) {
                if (e.target.closest('button') || e.target.closest('a')) return;
                if (typeof window.openViewBookingModal === 'function') {
                    window.openViewBookingModal(b);
                }
            });

            // Action button click
            const viewBtn = tr.querySelector('.view-booking-staff-btn');
            if (viewBtn) {
                viewBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (typeof window.openViewBookingModal === 'function') {
                        window.openViewBookingModal(b);
                    }
                });
            }

            staffTableBody.appendChild(tr);
        });
    }

    document.querySelectorAll('.staff-bookings-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const staffName = this.dataset.staffName || 'Staff Member';
            try {
                currentStaffBookings = JSON.parse(this.dataset.bookings || '[]');
            } catch(e) {
                currentStaffBookings = [];
            }
            if (staffNameSpan) staffNameSpan.textContent = staffName;
            if (staffSummaryBadge) staffSummaryBadge.textContent = `Total Bookings: ${currentStaffBookings.length}`;
            if (staffFilterInput) staffFilterInput.value = '';
            renderStaffBookings(currentStaffBookings);
        });
    });

    if (staffFilterInput) {
        staffFilterInput.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            if (!q) {
                renderStaffBookings(currentStaffBookings);
                return;
            }
            const filtered = currentStaffBookings.filter(b => {
                return (b.ref_no && b.ref_no.toLowerCase().includes(q)) ||
                       (b.id && b.id.toLowerCase().includes(q)) ||
                       (b.passenger_name && b.passenger_name.toLowerCase().includes(q)) ||
                       (b.phone_no && b.phone_no.toLowerCase().includes(q)) ||
                       (b.pickup_address && b.pickup_address.toLowerCase().includes(q)) ||
                       (b.dropoff_address && b.dropoff_address.toLowerCase().includes(q)) ||
                       (b.status && b.status.toLowerCase().includes(q));
            });
            renderStaffBookings(filtered);
        });
    }
});
</script>
@endpush
