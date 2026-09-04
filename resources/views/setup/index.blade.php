@extends('layouts.app')

@section('content')
<style>
    :root {
        --gold-light: #fce38a;
        --gold: #f4a261;
        --gold-dark: #b5651d;
        --brown: #7a4419;
    }

    .nav-tabs .nav-link {
        color: var(--brown);
        border: 1px solid transparent;
    }

    .nav-tabs .nav-link.active {
        background-color: var(--gold-dark);
        color: white;
        border-radius: 5px 5px 0 0;
    }

    .btn-custom {
        background-color: var(--gold);
        color: white;
        border: none;
    }

    .btn-custom:hover {
        background-color: var(--gold-dark);
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 0.2rem rgba(244, 162, 97, 0.25);
    }

    label {
        color: var(--brown);
        font-weight: 500;
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
        background-color: var(--gold-light);
        border: 1px solid var(--gold-dark);
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

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

{{-- Create a Driver Lookup Map for the Vehicles Table --}}
@php
    // Assuming $drivers is a Laravel Collection or array of arrays from Firebase
    $driverLookup = [];
    foreach ($drivers as $driver) {
        // Use a unique ID or a generated key for lookup, assuming Firebase pushes have a unique key.
        // If the 'id' field is present in the driver object/array, use that.
        // If not, you'll need to pass the Firebase key from the controller.
        // For this example, we assume a key named 'id' exists on the driver array.
        if (isset($driver['id'])) {
             $driverLookup[$driver['id']] = $driver['name'];
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
    
    <br>
    
    {{-- Display Success/Error Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
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
            <button class="nav-link @if(!session('active_tab') || session('active_tab') == 'staff') active @endif" id="staff-tab" data-bs-toggle="tab" data-bs-target="#staff" type="button">Add Staff</button>
        </li>
        <li class="nav-item">
            <button class="nav-link @if(session('active_tab') == 'driver') active @endif" id="driver-tab" data-bs-toggle="tab" data-bs-target="#driver" type="button">Add Driver</button>
        </li>
        <li class="nav-item">
            <button class="nav-link @if(session('active_tab') == 'vehicle') active @endif" id="vehicle-tab" data-bs-toggle="tab" data-bs-target="#vehicle" type="button">Add Vehicle</button>
        </li>
        <li class="nav-item">
            <button class="nav-link @if(session('active_tab') == 'customer') active @endif" id="customer-tab" data-bs-toggle="tab" data-bs-target="#customer" type="button">Add Customer Account</button>
        </li>
    </ul>

    <div class="tab-content mt-3">

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
                                        <td colspan="4" class="text-center py-5 text-muted">
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
                    <h3 class="text-center">Add Staff</h3>
                    <div class="form-card">
                        <form method="POST" action="{{ route('setup.staff.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
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
                                <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}">
                            </div>
                            <div class="mb-3">
                                <label>Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="collaborator" {{ old('role') === 'collaborator' ? 'selected' : '' }}>Collaborator — bookings only</option>
                                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin — full access</option>
                                </select>
                            </div>
                            <div class="alert alert-light border small py-2">
                                <i class="bi bi-envelope-check me-1"></i>Login credentials will be emailed automatically.
                            </div>
                            <button type="submit" class="btn btn-custom w-100">
                                <i class="bi bi-person-plus me-1"></i>Create &amp; Email Credentials
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="manageStaffModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow rounded-4">
                        <form method="POST" id="manageStaffForm">
                            @csrf
                            @method('PUT')
                            <div class="modal-header border-0 pb-0">
                                <div>
                                    <h5 class="modal-title fw-bold">Manage Staff Access</h5>
                                    <div class="text-muted small" id="manageStaffName"></div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body pt-4">
                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <select name="role" id="manageStaffRole" class="form-select" required>
                                        <option value="collaborator">Collaborator — bookings only</option>
                                        <option value="admin">Admin — full access</option>
                                    </select>
                                </div>
                                <div class="alert alert-light border small">
                                    Password fields blank chhor dein agar sirf role update karna hai.
                                </div>
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="form-label">New Password</label>
                                        <input type="password" name="password" class="form-control" minlength="8" autocomplete="new-password">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="password" name="password_confirmation" class="form-control" minlength="8" autocomplete="new-password">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-custom px-4"><i class="bi bi-check2-circle me-1"></i>Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!--<div class="tab-pane fade @if(session('active_tab') == 'driver') show active @endif" id="driver" role="tabpanel">-->
        <!--    <div class="row">-->
        <!--        <div class="col-md-8">-->
        <!--            <h3>Existing Drivers</h3>-->
        <!--            <div class="table-responsive">-->
        <!--                <table class="table table-striped table-bordered">-->
        <!--                    <thead class="bg-warning card-header">-->
        <!--                        <tr>-->
        <!--                            <th>#</th>-->
        <!--                            <th>Name</th>-->
        <!--                            <th>Email</th>-->
        <!--                            <th>Phone</th>-->
        <!--                            <th>Status</th>-->
        <!--                            <th>Location</th>-->
        <!--                        </tr>-->
        <!--                    </thead>-->
        <!--                    <tbody>-->
        <!--                        @forelse($drivers as $driver)-->
        <!--                            <tr>-->
        <!--                                {{-- Accessing array elements --}}-->
        <!--                                <td>{{ $loop->iteration }}</td>-->
        <!--                                <td>{{ $driver['name'] ?? 'N/A' }}</td>-->
        <!--                                <td>{{ $driver['email'] ?? 'N/A' }}</td>-->
        <!--                                <td>{{ $driver['phone'] ?? 'N/A' }}</td>-->
        <!--                                <td>-->
        <!--                                    @php $status = $driver['status'] ?? 'N/A'; @endphp-->
        <!--                                    <span class="badge bg-{{ $status == 'available' ? 'success' : ($status == 'on_job' ? 'primary' : 'secondary') }}">-->
        <!--                                        {{ ucfirst(str_replace('_', ' ', $status)) }}-->
        <!--                                    </span>-->
        <!--                                </td>-->
        <!--                                <td>{{ $driver['latitude'] ?? 'N/A' }}, {{ $driver['longitude'] ?? 'N/A' }}</td>-->
        <!--                            </tr>-->
        <!--                        @empty-->
        <!--                            <tr>-->
        <!--                                <td colspan="6" class="text-center">No drivers added yet.</td>-->
        <!--                            </tr>-->
        <!--                        @endforelse-->
        <!--                    </tbody>-->
        <!--                </table>-->
        <!--            </div>-->
        <!--        </div>-->

        <!--        <div class="col-md-4">-->
        <!--            <h3 class="text-center">Add Driver</h3>-->
        <!--            <div class="form-card">-->
        <!--                <form method="POST" action="{{ route('setup.driver.store') }}">-->
        <!--                    @csrf-->
        <!--                    <div class="mb-3">-->
        <!--                        <label>Name</label>-->
        <!--                        <input type="text" name="name" class="form-control" required value="{{ old('name') }}">-->
        <!--                    </div>-->
        <!--                    <div class="mb-3">-->
        <!--                        <label>Email</label>-->
        <!--                        <input type="email" name="email" class="form-control" required value="{{ old('email') }}">-->
        <!--                    </div>-->
        <!--                    <div class="mb-3">-->
        <!--                        <label>Phone Number</label>-->
        <!--                        <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}">-->
        <!--                    </div>-->
        <!--                    <div class="mb-3">-->
        <!--                        <label>Status</label>-->
        <!--                        <select name="status" class="form-select" required>-->
        <!--                            <option value="">-- Select Status --</option>-->
        <!--                            <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>-->
        <!--                            <option value="on_job" {{ old('status') == 'on_job' ? 'selected' : '' }}>On Job</option>-->
        <!--                            <option value="break" {{ old('status') == 'break' ? 'selected' : '' }}>Break</option>-->
        <!--                            <option value="waiting" {{ old('status') == 'waiting' ? 'selected' : '' }}>Waiting</option>-->
        <!--                        </select>-->
        <!--                    </div>-->
        <!--                    <div class="mb-3">-->
        <!--                        <label>Latitude</label>-->
        <!--                        <input type="text" name="latitude" class="form-control" value="{{ old('latitude') }}">-->
        <!--                    </div>-->
        <!--                    <div class="mb-3">-->
        <!--                        <label>Longitude</label>-->
        <!--                        <input type="text" name="longitude" class="form-control" value="{{ old('longitude') }}">-->
        <!--                    </div>-->
        <!--                    <button type="submit" class="btn btn-custom w-100">Add Driver</button>-->
        <!--                </form>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
        
        <div class="tab-pane fade @if(session('active_tab') == 'driver') show active @endif" id="driver" role="tabpanel">
    <div class="row">
        <div class="col-md-9">
            <h3>Existing Drivers</h3>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="bg-warning card-header">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Call Sign</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Address</th>
                            <th>Location</th>
                            <th>BF</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drivers as $driver)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $driver['name'] ?? 'N/A' }}</td>
                                <td>{{ $driver['call_sign'] ?? 'N/A' }}</td>
                                <td>{{ $driver['email'] ?? 'N/A' }}</td>
                                <td>{{ $driver['phone'] ?? 'N/A' }}</td>
                                <td>
                                    @php $status = $driver['status'] ?? 'N/A'; @endphp
                                    <span class="badge bg-{{ $status == 'available' ? 'success' : ($status == 'on_job' ? 'primary' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </span>
                                </td>
                                <td>{{ $driver['address'] ?? 'N/A' }}</td>
                                <td>{{ $driver['latitude'] ?? 'N/A' }}, {{ $driver['longitude'] ?? 'N/A' }}</td>
                                <td>{{ $driver['brought_forward'] ?? 'N/A'}}</td>
<td>
    <a href="{{ route('drivers.bf.history', $driver['id']) }}"
       class="btn btn-sm btn-outline-warning">
        BF Details
    </a>
    <button 
        class="btn btn-sm btn-outline-primary edit-driver-btn"
        data-id="{{ $driver['id'] }}"
        data-name="{{ $driver['name'] }}"
        data-call_sign="{{ $driver['call_sign'] }}"
        data-email="{{ $driver['email'] }}"
        data-phone="{{ $driver['phone'] }}"
        data-status="{{ $driver['status'] }}"
        data-address="{{ $driver['address'] ?? '-' }}"
        data-latitude="{{ $driver['latitude'] ?? '-' }}"
        data-longitude="{{ $driver['longitude'] ?? '-' }}"
        data-bs-toggle="modal"
        data-bs-target="#editDriverModal"
    >
        Edit
    </button>
</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No drivers added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-3">
            <h3 class="text-center">Add Driver</h3>
            <div class="form-card">
                <form method="POST" action="{{ route('setup.driver.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                    </div>

                    <div class="mb-3">
                        <label>Call Sign</label>
                        <input type="text" name="call_sign" class="form-control" placeholder="e.g. D-101" value="{{ old('call_sign') }}">
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                    </div>

                    <div class="mb-3">
                        <label>Phone Number</label>
                        <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}">
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select" required>
                            <option value="">-- Select Status --</option>
                            <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="on_job" {{ old('status') == 'on_job' ? 'selected' : '' }}>On Job</option>
                            <option value="break" {{ old('status') == 'break' ? 'selected' : '' }}>Break</option>
                            <option value="waiting" {{ old('status') == 'waiting' ? 'selected' : '' }}>Waiting</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Address</label>
                        <input type="text" id="driver_address" name="address" class="form-control" placeholder="Enter driver address" value="{{ old('address') }}" required>
                        <div id="address-status" class="small text-muted mt-1"></div>
                    </div>

                    <div class="mb-3">
                        <label>Latitude</label>
                        <input type="text" id="latitude" name="latitude" class="form-control" value="{{ old('latitude') }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Longitude</label>
                        <input type="text" id="longitude" name="longitude" class="form-control" value="{{ old('longitude') }}" readonly>
                    </div>

                    <button type="submit" class="btn btn-custom w-100">Add Driver</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editDriverModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="editDriverForm">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Update Driver</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="edit_driver_id">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Call Sign</label>
                            <input type="text" name="call_sign" id="edit_call_sign" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Phone</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="available">Available</option>
                                <option value="on_job">On Job</option>
                                <option value="break">Break</option>
                                <option value="waiting">Waiting</option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Address</label>
                            <input type="text" name="address" id="edit_address" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Latitude</label>
                            <input type="text" name="latitude" id="edit_latitude" class="form-control" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Longitude</label>
                            <input type="text" name="longitude" id="edit_longitude" class="form-control" readonly>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success w-100">
                        Update Driver
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>



        <div class="tab-pane fade @if(session('active_tab') == 'vehicle') show active @endif" id="vehicle" role="tabpanel">
            <div class="row">
                <div class="col-md-8">
                    <h3>Existing Vehicles</h3>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="bg-warning card-header">
                                <tr>
                                    <th>#</th>
                                    <th>Make</th>
                                    <th>Model</th>
                                    <th>Color</th>
                                    <th>Registration</th>
                                    <th>Assigned Driver</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vehicles as $vehicle)
                                    <tr>
                                        {{-- Accessing array elements --}}
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $vehicle['make'] ?? 'N/A' }}</td>
                                        <td>{{ $vehicle['model'] ?? 'N/A' }}</td>
                                        <td>{{ $vehicle['color'] ?? 'N/A' }}</td>
                                        <td>{{ $vehicle['registration'] ?? 'N/A' }}</td>
                                        {{-- Lookup driver name from the map created above --}}
                                        <td>{{ $driverLookup[$vehicle['driver_id']] ?? 'Unassigned' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No vehicles added yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-4">
                    <h3 class="text-center">Add Vehicle</h3>
                    <div class="form-card">
                        <form method="POST" action="{{ route('setup.vehicle.store') }}">
                            @csrf
                            <!--<div class="mb-3">-->
                            <!--    <label>Make</label>-->
                            <!--    <input type="text" name="make" class="form-control" required value="{{ old('make') }}">-->
                            <!--</div>-->
                            <div class="mb-3">
    <label>Make</label>
    <select name="make" class="form-control" required>
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
                                <input type="text" name="model" class="form-control" required value="{{ old('model') }}">
                            </div>
                            <div class="mb-3">
                                <label>Color</label>
                                <input type="text" name="color" class="form-control" required value="{{ old('color') }}">
                            </div>
                            <div class="mb-3">
                                <label>Registration Number</label>
                                <input type="text" name="registration" class="form-control" required value="{{ old('registration') }}">
                            </div>
                            <div class="mb-3">
                                <label>Driver</label>
                                <select name="driver_id" class="form-select" required>
                                    <option value="">-- Select Driver --</option>
                                    {{-- Iterating over the drivers data from Firebase --}}
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver['id'] ?? '' }}" {{ old('driver_id') == ($driver['id'] ?? '') ? 'selected' : '' }}>
                                            {{ $driver['name'] ?? 'Unknown Driver' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-custom w-100">Add Vehicle</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade @if(session('active_tab') == 'customer') show active @endif" id="customer" role="tabpanel">
            <div class="row">
                <div class="col-md-8">
                    <h3>Existing Customer Accounts</h3>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="bg-warning card-header">
                                <tr>
                                    <th>#</th>
                                    <th>Business Name</th>
                                    <th>Address</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                    <tr>
                                        {{-- Accessing array elements --}}
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $customer['business_name'] ?? 'N/A' }}</td>
                                        <td>{{ $customer['address'] ?? 'N/A' }}</td>
                                        <td>{{ $customer['email'] ?? 'N/A' }}</td>
                                        <td>{{ $customer['phone'] ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No customer accounts added yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-4">
                    <h3 class="text-center">Add Customer Account</h3>
                    <div class="form-card">
                        <form method="POST" action="{{ route('setup.customer.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label>Business Name</label>
                                <input type="text" name="business_name" class="form-control" required value="{{ old('business_name') }}">
                            </div>
                            <div class="mb-3">
                                <label>Address</label>
                                <input type="text" name="address" class="form-control" required value="{{ old('address') }}">
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                            </div>
                            <div class="mb-3">
                                <label>Phone Number</label>
                                <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}">
                            </div>
                            <button type="submit" class="btn btn-custom w-100">Add Customer Account</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

console.log(id);
            document.getElementById('editDriverForm').action =
                `/admin/setup/driver/${id}`;

            document.getElementById('edit_name').value = this.dataset.name;
            document.getElementById('edit_call_sign').value = this.dataset.call_sign;
            document.getElementById('edit_email').value = this.dataset.email;
            document.getElementById('edit_phone').value = this.dataset.phone;
            document.getElementById('edit_status').value = this.dataset.status;
            document.getElementById('edit_address').value = this.dataset.address;
            document.getElementById('edit_latitude').value = this.dataset.latitude ?? '-';
            document.getElementById('edit_longitude').value = this.dataset.longitude ?? '-';
            //document.getElementById('edit_brought_forward').value = this.dataset.brought_forward;
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
@endpush
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const addressInput = document.getElementById('driver_address');
    const latInput = document.getElementById('latitude');
    const lonInput = document.getElementById('longitude');
    const statusDiv = document.getElementById('address-status');

    if (addressInput) {
        addressInput.addEventListener('blur', async function () {
            const address = this.value.trim();
            if (!address) return;

            statusDiv.textContent = "Fetching coordinates...";
            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`);
                const data = await res.json();
                if (data.length > 0) {
                    latInput.value = data[0].lat;
                    lonInput.value = data[0].lon;
                    statusDiv.textContent = "✅ Coordinates fetched successfully.";
                } else {
                    statusDiv.textContent = "⚠️ Address not found.";
                }
            } catch (e) {
                console.error(e);
                statusDiv.textContent = "❌ Error fetching coordinates.";
            }
        });
    }
});
</script>
@endpush

@endsection
