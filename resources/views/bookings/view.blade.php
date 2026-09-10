@extends('layouts.app')

@section('content')
<style>
.booking-route-timeline {
  border-left: 2px dashed #cbd5e1;
  margin-left: 14px;
}
.route-node {
  padding-left: 12px;
}
.route-dot {
  box-shadow: 0 0 0 2px #ffffff;
}
.activity-timeline-container {
  border-left: 2px solid #e2e8f0;
  margin-left: 10px;
}
.activity-timeline-item {
  position: relative;
  padding-left: 18px;
  padding-bottom: 16px;
}
.activity-timeline-item:last-child {
  padding-bottom: 4px;
}
.activity-timeline-item::before {
  content: '';
  position: absolute;
  left: -5px;
  top: 5px;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background-color: #E6B04A;
  border: 2px solid #ffffff;
  box-shadow: 0 0 0 2px rgba(230, 176, 74, 0.35);
}
.activity-timeline-item.action-status::before {
  background-color: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.35);
}
.activity-timeline-item.action-dispatch::before {
  background-color: #10b981;
  box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.35);
}
.activity-timeline-item.action-recall::before {
  background-color: #ef4444;
  box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.35);
}
.activity-timeline-item.action-sms::before {
  background-color: #8b5cf6;
  box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.35);
}
.activity-timeline-item.action-email::before {
  background-color: #06b6d4;
  box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.35);
}
.activity-timeline-item.action-edit::before {
  background-color: #f59e0b;
  box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.35);
}
.activity-timeline-item.action-create::before {
  background-color: #64748b;
  box-shadow: 0 0 0 2px rgba(100, 116, 139, 0.35);
}
</style>

<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            <div>
                <h1 class="h4 fw-bold mb-0 text-dark">
                    Booking Details 
                    <span class="badge ms-2" style="background: #E6B04A; color: #111827; font-weight: 700; font-size: 0.85rem;">
                        REF# {{ $booking['ref_no'] ?? $booking['id'] }}
                    </span>
                    @php
                        $status = strtolower($booking['status'] ?? 'pending');
                        $badgeClass = 'bg-secondary';
                        if ($status === 'completed') $badgeClass = 'bg-success';
                        elseif (in_array($status, ['dispatched', 'accepted', 'allocated'])) $badgeClass = 'bg-primary';
                        elseif (in_array($status, ['pickedup', 'onway'])) $badgeClass = 'bg-info text-dark';
                        elseif (in_array($status, ['cancelled', 'rejected'])) $badgeClass = 'bg-danger';
                        elseif ($status === 'pending') $badgeClass = 'bg-warning text-dark';
                    @endphp
                    <span class="badge {{ $badgeClass }} text-uppercase fw-semibold" style="font-size: 0.78rem;">
                        {{ $booking['status'] ?? 'Pending' }}
                    </span>
                </h1>
                <div class="text-muted small">Complete trip itinerary, pricing details and staff audit trail</div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('bookings.edit', $booking['id']) }}" class="btn btn-warning btn-sm text-dark fw-bold">
                <i class="bi bi-pencil-square me-1"></i> Edit Booking
            </a>
            <a href="{{ route('bookings.receipt', $booking['id']) }}" target="_blank" class="btn btn-outline-success btn-sm">
                <i class="bi bi-receipt me-1"></i> View Receipt
            </a>
        </div>
    </div>

    <!-- Top Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="p-3 rounded-3 bg-white border shadow-sm h-100">
                <span class="text-muted small text-uppercase fw-semibold d-block" style="font-size: 0.72rem;">
                    <i class="bi bi-calendar3 me-1 text-primary"></i> Pickup Date & Time
                </span>
                <span class="fw-bold text-dark mt-1 d-block">
                    {{ isset($booking['pickup_time']) ? \Carbon\Carbon::parse($booking['pickup_time'])->format('d M Y H:i') : '-' }}
                </span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 rounded-3 bg-white border shadow-sm h-100">
                <span class="text-muted small text-uppercase fw-semibold d-block" style="font-size: 0.72rem;">
                    <i class="bi bi-car-front-fill me-1 text-info"></i> Vehicle & Driver
                </span>
                <span class="fw-bold text-dark mt-1 d-block">
                    @if($driver)
                        <span class="badge bg-success-subtle text-success-emphasis border">
                            @if(!empty($driver['call_sign'])) [{{ $driver['call_sign'] }}] @endif
                            {{ $driver['name'] ?? 'Driver Assigned' }}
                        </span>
                    @elseif(!empty($booking['vehicle_make']))
                        {{ $booking['vehicle_make'] }} (Unassigned)
                    @else
                        <span class="badge bg-light text-muted border">Not Assigned</span>
                    @endif
                </span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 rounded-3 bg-white border shadow-sm h-100">
                <span class="text-muted small text-uppercase fw-semibold d-block" style="font-size: 0.72rem;">
                    <i class="bi bi-cash-stack me-1 text-success"></i> Total Fare
                </span>
                <span class="fw-bold text-success mt-1 d-block fs-5">
                    £{{ number_format((float)($booking['price'] ?? 0), 2) }}
                </span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 rounded-3 bg-white border shadow-sm h-100">
                <span class="text-muted small text-uppercase fw-semibold d-block" style="font-size: 0.72rem;">
                    <i class="bi bi-credit-card-2-front me-1 text-warning"></i> Payment Method
                </span>
                <span class="fw-bold text-dark mt-1 d-block text-capitalize">
                    {{ $booking['payment_type'] ?? 'Cash' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Main Content Split -->
    <div class="row g-4">
        <!-- LEFT COLUMN: Trip & Passenger Details -->
        <div class="col-lg-7">
            <!-- Passenger Information -->
            <div class="card border-0 shadow-sm rounded-3 mb-3 bg-white">
                <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center">
                    <i class="bi bi-person-circle text-primary me-2 fs-6"></i>
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">Passenger Information</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <div class="small text-muted" style="font-size: 0.78rem;">Passenger Name</div>
                            <div class="fw-semibold text-dark">{{ $booking['passenger_name'] ?? 'N/A' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="small text-muted" style="font-size: 0.78rem;">Phone Number</div>
                            <div class="fw-semibold text-dark d-flex align-items-center gap-2">
                                <span>{{ $booking['phone_no'] ?? 'N/A' }}</span>
                                @if(!empty($booking['phone_no']))
                                    <a href="tel:{{ $booking['phone_no'] }}" class="btn btn-xs btn-outline-primary py-0 px-2 rounded-pill" style="font-size: 0.72rem;">
                                        <i class="bi bi-telephone-fill"></i> Call
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="small text-muted" style="font-size: 0.78rem;">Email Address</div>
                            <div class="fw-semibold text-dark text-break">{{ $booking['email'] ?? 'N/A' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="small text-muted" style="font-size: 0.78rem;">Flight Number</div>
                            <div class="fw-semibold text-dark">{{ $booking['flight_no'] ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Route & Stops -->
            <div class="card border-0 shadow-sm rounded-3 mb-3 bg-white">
                <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-geo-alt-fill text-danger me-2 fs-6"></i>
                        <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">Journey Route & Stops</h6>
                    </div>
                    @if(!empty($booking['pickup_address']) && !empty($booking['dropoff_address']))
                        <a href="https://www.google.com/maps/dir/?api=1&origin={{ urlencode($booking['pickup_address']) }}&destination={{ urlencode($booking['dropoff_address']) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;">
                            <i class="bi bi-map me-1"></i> Open in Maps
                        </a>
                    @endif
                </div>
                <div class="card-body p-3">
                    <div class="booking-route-timeline position-relative ps-4 py-1">
                        <!-- Pickup -->
                        <div class="route-node position-relative mb-3">
                            <span class="route-dot bg-success rounded-circle position-absolute" style="left: -28px; top: 3px; width: 14px; height: 14px; border: 3px solid #d1e7dd;"></span>
                            <div class="small fw-bold text-success text-uppercase" style="font-size: 0.7rem;">Pickup Location</div>
                            <div class="text-dark fw-medium" style="font-size: 0.88rem;">{{ $booking['pickup_address'] ?? '-' }}</div>
                            <div class="text-muted small mt-0" style="font-size: 0.75rem;">
                                Pickup Time: {{ isset($booking['pickup_time']) ? \Carbon\Carbon::parse($booking['pickup_time'])->format('d M Y H:i') : '-' }}
                            </div>
                        </div>

                        <!-- Vias -->
                        @php
                            $vias = $booking['vias'] ?? ($booking['via_addresses'] ?? []);
                            if (!is_array($vias)) $vias = [];
                            $vias = array_filter($vias);
                        @endphp
                        @foreach($vias as $idx => $via)
                            <div class="route-node position-relative mb-3">
                                <span class="route-dot bg-warning rounded-circle position-absolute" style="left: -28px; top: 3px; width: 14px; height: 14px; border: 3px solid #fff3cd;"></span>
                                <div class="small fw-bold text-warning-emphasis text-uppercase" style="font-size: 0.7rem;">Via Stop #{{ $idx + 1 }}</div>
                                <div class="text-dark fw-medium" style="font-size: 0.88rem;">{{ $via }}</div>
                            </div>
                        @endforeach

                        <!-- Dropoff -->
                        <div class="route-node position-relative">
                            <span class="route-dot bg-danger rounded-circle position-absolute" style="left: -28px; top: 3px; width: 14px; height: 14px; border: 3px solid #f8d7da;"></span>
                            <div class="small fw-bold text-danger text-uppercase" style="font-size: 0.7rem;">Dropoff Destination</div>
                            <div class="text-dark fw-medium" style="font-size: 0.88rem;">{{ $booking['dropoff_address'] ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicle & Pricing Breakdown -->
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center">
                    <i class="bi bi-tag-fill text-warning me-2 fs-6"></i>
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">Vehicle, Driver & Pricing Breakdown</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2 mb-3 pb-2 border-bottom">
                        <div class="col-sm-6">
                            <div class="small text-muted" style="font-size: 0.78rem;">Vehicle Type</div>
                            <div class="fw-semibold text-dark">{{ $booking['vehicle_make'] ?? ($booking['vehicle_id'] ?? '-') }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="small text-muted" style="font-size: 0.78rem;">Assigned Driver</div>
                            <div class="fw-semibold text-dark">
                                @if($driver)
                                    <span class="badge bg-success-subtle text-success-emphasis border">
                                        @if(!empty($driver['call_sign'])) [{{ $driver['call_sign'] }}] @endif
                                        {{ $driver['name'] ?? 'Driver Assigned' }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border">Not Assigned</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-4 col-sm-3">
                            <div class="small text-muted" style="font-size: 0.75rem;">Base Fare</div>
                            <div class="fw-semibold text-dark">£{{ number_format((float)($booking['fare'] ?? ($booking['price'] ?? 0)), 2) }}</div>
                        </div>
                        <div class="col-4 col-sm-3">
                            <div class="small text-muted" style="font-size: 0.75rem;">Parking Fee</div>
                            <div class="fw-semibold text-dark">£{{ number_format((float)($booking['parking'] ?? 0), 2) }}</div>
                        </div>
                        <div class="col-4 col-sm-3">
                            <div class="small text-muted" style="font-size: 0.75rem;">Waiting Fee</div>
                            <div class="fw-semibold text-dark">£{{ number_format((float)($booking['waiting_fee'] ?? 0), 2) }}</div>
                        </div>
                        <div class="col-4 col-sm-3">
                            <div class="small text-muted" style="font-size: 0.75rem;">Extra / Toll</div>
                            <div class="fw-semibold text-dark">£{{ number_format((float)($booking['extra'] ?? 0), 2) }}</div>
                        </div>
                    </div>

                    <div class="p-2 rounded-2 bg-light border d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-dark" style="font-size: 0.85rem;">Total Calculated Price</span>
                        <span class="fw-bold text-success fs-6">£{{ number_format((float)($booking['price'] ?? 0), 2) }}</span>
                    </div>

                    <div class="row g-2 pt-1">
                        <div class="col-sm-6">
                            <div class="small text-muted" style="font-size: 0.78rem;">Child Seat Requested</div>
                            <div class="fw-semibold text-dark">
                                @if(!empty($booking['child_seat']))
                                    <span class="badge bg-success">Yes (Requested)</span>
                                @else
                                    <span class="badge bg-light text-muted border">No</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="small text-muted" style="font-size: 0.78rem;">Payment Type</div>
                            <div class="fw-semibold text-dark text-capitalize">{{ $booking['payment_type'] ?? 'Cash' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="small text-muted" style="font-size: 0.78rem;">Job Comments & Special Instructions</div>
                            <div class="p-2 rounded bg-light text-secondary small text-break fst-italic" style="min-height: 38px;">
                                {{ $booking['job_comment'] ?? 'No special comments or instructions provided.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Staff Activity & Follow-up Audit Trail -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white d-flex flex-column">
                <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clock-history text-warning me-2 fs-6"></i>
                        <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">Staff Activity Log</h6>
                    </div>
                    <span class="badge bg-secondary-subtle text-dark" style="font-size: 0.72rem;">
                        {{ count($activity_logs ?? []) }} records
                    </span>
                </div>
                
                <div class="card-body p-3 flex-grow-1" style="max-height: 540px; overflow-y: auto;">
                    @if(!empty($activity_logs))
                        <div class="activity-timeline-container position-relative ps-3">
                            @foreach($activity_logs as $log)
                                @php
                                    $action = $log['action'] ?? 'Activity';
                                    $actLower = strtolower($action);
                                    $actionClass = 'action-default';
                                    $badgeClass = 'bg-primary-subtle text-primary border border-primary-subtle';
                                    $icon = 'bi-activity';

                                    if (str_contains($actLower, 'status')) {
                                        $actionClass = 'action-status';
                                        $badgeClass = 'bg-info-subtle text-info-emphasis border border-info-subtle';
                                        $icon = 'bi-arrow-repeat';
                                    } elseif (str_contains($actLower, 'dispatch') || str_contains($actLower, 'assign')) {
                                        $actionClass = 'action-dispatch';
                                        $badgeClass = 'bg-success-subtle text-success-emphasis border border-success-subtle';
                                        $icon = 'bi-person-check';
                                    } elseif (str_contains($actLower, 'recall') || str_contains($actLower, 'cancel')) {
                                        $actionClass = 'action-recall';
                                        $badgeClass = 'bg-danger-subtle text-danger-emphasis border border-danger-subtle';
                                        $icon = 'bi-person-x';
                                    } elseif (str_contains($actLower, 'sms')) {
                                        $actionClass = 'action-sms';
                                        $badgeClass = 'bg-purple-subtle text-purple-emphasis border border-purple-subtle';
                                        $icon = 'bi-chat-dots';
                                    } elseif (str_contains($actLower, 'email')) {
                                        $actionClass = 'action-email';
                                        $badgeClass = 'bg-cyan-subtle text-cyan-emphasis border border-cyan-subtle';
                                        $icon = 'bi-envelope';
                                    } elseif (str_contains($actLower, 'edit') || str_contains($actLower, 'update')) {
                                        $actionClass = 'action-edit';
                                        $badgeClass = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
                                        $icon = 'bi-pencil-square';
                                    } elseif (str_contains($actLower, 'create')) {
                                        $actionClass = 'action-create';
                                        $badgeClass = 'bg-secondary-subtle text-dark border border-secondary-subtle';
                                        $icon = 'bi-plus-circle';
                                    }

                                    $timeStr = '-';
                                    if (!empty($log['created_at']) || !empty($log['timestamp'])) {
                                        try {
                                            $timeStr = \Carbon\Carbon::parse($log['created_at'] ?? $log['timestamp'])->format('d M Y, H:i:s');
                                        } catch(\Exception $e) {
                                            $timeStr = $log['created_at'] ?? $log['timestamp'];
                                        }
                                    }
                                @endphp
                                <div class="activity-timeline-item {{ $actionClass }}">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-dark text-white fw-medium py-1 px-2" style="font-size: 0.75rem;">
                                                <i class="bi bi-person-fill text-warning me-1"></i>{{ $log['staff_name'] ?? 'Staff' }}
                                            </span>
                                            @if(!empty($log['staff_role']))
                                                <span class="badge bg-secondary-subtle text-secondary py-0 px-1 ms-1" style="font-size: 0.68rem;">
                                                    {{ $log['staff_role'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-muted small" style="font-size: 0.72rem;">
                                            <i class="bi bi-clock me-1"></i>{{ $timeStr }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1 mb-1">
                                        <span class="badge {{ $badgeClass }} fw-semibold" style="font-size: 0.75rem;">
                                            <i class="bi {{ $icon }} me-1"></i>{{ $action }}
                                        </span>
                                    </div>
                                    <div class="text-secondary small" style="font-size: 0.82rem; line-height: 1.4;">
                                        {{ $log['description'] ?? '' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-clipboard2-check fs-2 d-block mb-2 text-secondary opacity-50"></i>
                            <div class="fw-medium">No activity records found</div>
                            <div class="small text-muted">Staff actions on this booking will appear here automatically.</div>
                        </div>
                    @endif
                </div>

                <div class="card-footer bg-light border-top p-2 text-muted small" style="font-size: 0.75rem;">
                    <i class="bi bi-shield-check text-success me-1"></i> Logs track actions by <strong>Dispatchers</strong>, <strong>Admins</strong>, and <strong>Super Admin</strong>.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection