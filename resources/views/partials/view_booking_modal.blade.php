<!-- 🌟 Reusable Booking Details & Staff Activity Modal Component -->
<style>
#viewBookingModal {
  z-index: 1095 !important;
}
.modal-backdrop.show + .modal-backdrop.show {
  z-index: 1090 !important;
}
#viewBookingModal .modal-header {
  background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
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

<div class="modal fade" id="viewBookingModal" tabindex="-1" aria-labelledby="viewBookingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable my-4">
    <div class="modal-content border-0 shadow-lg" style="background: #f8fafc; border-radius: 16px; overflow: hidden;">
      
      <!-- Modal Header -->
      <div class="modal-header text-white" style="padding: 14px 20px;">
        <div class="d-flex align-items-center flex-wrap gap-2 w-100 pe-3">
          <div class="d-flex align-items-center">
            <span class="rounded-circle bg-warning bg-opacity-25 p-2 d-inline-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
              <i class="bi bi-journal-text text-warning fs-5"></i>
            </span>
            <div>
              <h5 class="modal-title fw-bold mb-0 text-white" id="viewBookingModalLabel" style="font-size: 1.1rem;">Booking Details & Activity History</h5>
              <div class="text-white-50" style="font-size: 0.75rem;">Complete itinerary, pricing breakdown & staff audit trail</div>
            </div>
          </div>
          <div class="ms-auto d-flex align-items-center gap-2">
            <span id="modal-ref-badge" class="badge" style="background: #E6B04A; color: #111827; font-weight: 700; font-size: 0.85rem; letter-spacing: 0.5px;">-</span>
            <span id="modal-status-badge" class="badge bg-secondary text-uppercase fw-semibold" style="font-size: 0.78rem; padding: 6px 10px;">-</span>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body p-3 p-md-4" style="max-height: calc(85vh - 120px); overflow-y: auto;">
        
        <!-- Top Stats Banner -->
        <div class="row g-2 g-md-3 mb-3">
          <div class="col-6 col-md-3">
            <div class="p-2 p-md-3 rounded-3 bg-white border shadow-sm h-100 d-flex flex-column justify-content-center">
              <span class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;"><i class="bi bi-calendar3 me-1 text-primary"></i> Pickup Date & Time</span>
              <span id="modal-top-pickup-time" class="fw-bold text-dark mt-1 text-truncate" style="font-size: 0.92rem;">-</span>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="p-2 p-md-3 rounded-3 bg-white border shadow-sm h-100 d-flex flex-column justify-content-center">
              <span class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;"><i class="bi bi-car-front-fill me-1 text-info"></i> Vehicle & Driver</span>
              <span id="modal-top-driver" class="fw-bold text-dark mt-1 text-truncate" style="font-size: 0.92rem;">-</span>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="p-2 p-md-3 rounded-3 bg-white border shadow-sm h-100 d-flex flex-column justify-content-center">
              <span class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;"><i class="bi bi-cash-stack me-1 text-success"></i> Total Fare</span>
              <span id="modal-top-price" class="fw-bold text-success mt-1 text-truncate" style="font-size: 1.05rem;">-</span>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="p-2 p-md-3 rounded-3 bg-white border shadow-sm h-100 d-flex flex-column justify-content-center">
              <span class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;"><i class="bi bi-credit-card-2-front me-1 text-warning"></i> Payment Method</span>
              <span id="modal-top-payment" class="fw-bold text-dark mt-1 text-truncate text-capitalize" style="font-size: 0.92rem;">-</span>
            </div>
          </div>
        </div>

        <div class="row g-3">
          
          <!-- LEFT COLUMN: Booking & Trip Info -->
          <div class="col-lg-7">
            
            <!-- Passenger Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-3 bg-white">
              <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center">
                <i class="bi bi-person-circle text-primary me-2 fs-6"></i>
                <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.88rem;">Passenger Information</h6>
              </div>
              <div class="card-body p-3">
                <div class="row g-2">
                  <div class="col-sm-6">
                    <div class="small text-muted" style="font-size: 0.78rem;">Passenger Name</div>
                    <div id="modal-passenger_name" class="fw-semibold text-dark">-</div>
                  </div>
                  <div class="col-sm-6">
                    <div class="small text-muted" style="font-size: 0.78rem;">Phone Number</div>
                    <div id="modal-phone-container" class="fw-semibold text-dark d-flex align-items-center gap-2 flex-wrap">
                      <span id="modal-phone_no">-</span>
                      <a id="modal-phone-call" href="#" class="btn btn-xs btn-outline-primary py-0 px-2 rounded-pill" style="font-size: 0.72rem;"><i class="bi bi-telephone-fill"></i> Call</a>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="small text-muted" style="font-size: 0.78rem;">Email Address</div>
                    <div id="modal-email" class="fw-semibold text-dark text-break">-</div>
                  </div>
                  <div class="col-sm-6">
                    <div class="small text-muted" style="font-size: 0.78rem;">Flight Number</div>
                    <div id="modal-flight_no" class="fw-semibold text-dark">-</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Route & Stops Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-3 bg-white">
              <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                  <i class="bi bi-geo-alt-fill text-danger me-2 fs-6"></i>
                  <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.88rem;">Journey Route & Stops</h6>
                </div>
                <a id="modal-route-maps-link" href="#" target="_blank" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;">
                  <i class="bi bi-map me-1"></i> Open in Maps
                </a>
              </div>
              <div class="card-body p-3">
                <div class="booking-route-timeline position-relative ps-4 py-1">
                  <!-- Pickup -->
                  <div class="route-node position-relative mb-3">
                    <span class="route-dot bg-success rounded-circle position-absolute" style="left: -28px; top: 3px; width: 14px; height: 14px; border: 3px solid #d1e7dd;"></span>
                    <div class="small fw-bold text-success text-uppercase" style="font-size: 0.7rem;">Pickup Location</div>
                    <div id="modal-pickup_address" class="text-dark fw-medium" style="font-size: 0.88rem;">-</div>
                    <div id="modal-pickup_time_sub" class="text-muted small mt-0" style="font-size: 0.75rem;"></div>
                  </div>
                  
                  <!-- Via Stops (rendered dynamically) -->
                  <div id="modal-vias-container" class="mb-2"></div>

                  <!-- Dropoff -->
                  <div class="route-node position-relative">
                    <span class="route-dot bg-danger rounded-circle position-absolute" style="left: -28px; top: 3px; width: 14px; height: 14px; border: 3px solid #f8d7da;"></span>
                    <div class="small fw-bold text-danger text-uppercase" style="font-size: 0.7rem;">Dropoff Destination</div>
                    <div id="modal-dropoff_address" class="text-dark fw-medium" style="font-size: 0.88rem;">-</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Vehicle, Driver & Pricing Card -->
            <div class="card border-0 shadow-sm rounded-3 bg-white">
              <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center">
                <i class="bi bi-tag-fill text-warning me-2 fs-6"></i>
                <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.88rem;">Vehicle, Driver & Pricing Breakdown</h6>
              </div>
              <div class="card-body p-3">
                <div class="row g-2 mb-3 pb-2 border-bottom">
                  <div class="col-sm-6">
                    <div class="small text-muted" style="font-size: 0.78rem;">Vehicle Type</div>
                    <div id="modal-vehicle_id" class="fw-semibold text-dark">-</div>
                  </div>
                  <div class="col-sm-6">
                    <div class="small text-muted" style="font-size: 0.78rem;">Assigned Driver</div>
                    <div id="modal-driver-details" class="fw-semibold text-dark">-</div>
                  </div>
                </div>

                <div class="row g-2 mb-2">
                  <div class="col-4 col-sm-3">
                    <div class="small text-muted" style="font-size: 0.75rem;">Base Fare</div>
                    <div id="modal-fare" class="fw-semibold text-dark">£0.00</div>
                  </div>
                  <div class="col-4 col-sm-3">
                    <div class="small text-muted" style="font-size: 0.75rem;">Parking Fee</div>
                    <div id="modal-parking" class="fw-semibold text-dark">£0.00</div>
                  </div>
                  <div class="col-4 col-sm-3">
                    <div class="small text-muted" style="font-size: 0.75rem;">Waiting Fee</div>
                    <div id="modal-waiting" class="fw-semibold text-dark">£0.00</div>
                  </div>
                  <div class="col-4 col-sm-3">
                    <div class="small text-muted" style="font-size: 0.75rem;">Extra / Toll</div>
                    <div id="modal-extra" class="fw-semibold text-dark">£0.00</div>
                  </div>
                </div>

                <div class="p-2 rounded-2 bg-light border d-flex align-items-center justify-content-between mb-2">
                  <span class="fw-bold text-dark" style="font-size: 0.85rem;">Total Calculated Price</span>
                  <span id="modal-price" class="fw-bold text-success fs-6">£0.00</span>
                </div>

                <div class="row g-2 pt-1">
                  <div class="col-sm-6">
                    <div class="small text-muted" style="font-size: 0.78rem;">Child Seat Requested</div>
                    <div id="modal-child_seat" class="fw-semibold text-dark">-</div>
                  </div>
                  <div class="col-sm-6">
                    <div class="small text-muted" style="font-size: 0.78rem;">Payment Type</div>
                    <div id="modal-payment_type" class="fw-semibold text-dark text-capitalize">-</div>
                  </div>
                  <div class="col-12">
                    <div class="small text-muted" style="font-size: 0.78rem;">Job Comments & Special Instructions</div>
                    <div id="modal-comment" class="p-2 rounded bg-light text-secondary small text-break fst-italic" style="min-height: 38px;">-</div>
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
                  <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.88rem;">Staff Activity Log</h6>
                </div>
                <div class="d-flex align-items-center gap-1">
                  <span id="modal-activity-count" class="badge bg-secondary-subtle text-dark" style="font-size: 0.72rem;">0 logs</span>
                  <button type="button" id="modal-refresh-logs-btn" class="btn btn-xs btn-outline-secondary p-1 py-0" title="Refresh Activity Logs" style="font-size: 0.75rem;">
                    <i class="bi bi-arrow-clockwise"></i>
                  </button>
                </div>
              </div>
              
              <div class="card-body p-3 flex-grow-1" style="max-height: 520px; overflow-y: auto;">
                <!-- Logs list container -->
                <div id="modal-activity-timeline" class="activity-timeline-container position-relative ps-3">
                  <!-- Dynamic activity logs rendered here -->
                </div>

                <!-- Empty logs state -->
                <div id="modal-activity-empty" class="text-center py-5 text-muted d-none">
                  <i class="bi bi-clipboard2-check fs-2 d-block mb-2 text-secondary opacity-50"></i>
                  <div class="fw-medium">No activity records found</div>
                  <div class="small text-muted">Staff actions on this booking will appear here automatically.</div>
                </div>

                <!-- Loading spinner for logs -->
                <div id="modal-activity-loading" class="text-center py-5">
                  <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                  <div class="small text-muted mt-2">Loading staff activity history...</div>
                </div>
              </div>

              <!-- Quick Staff Note in footer -->
              <div class="card-footer bg-light border-top p-2 text-muted small" style="font-size: 0.75rem;">
                <i class="bi bi-shield-check text-success me-1"></i> Logs track actions by <strong>Dispatchers</strong>, <strong>Admins</strong>, and <strong>Super Admin</strong>.
              </div>
            </div>
          </div>

        </div>

      </div>

      <!-- Modal Footer -->
      <div class="modal-footer bg-white border-top py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <a id="modal-btn-edit" href="#" class="btn btn-sm btn-warning text-dark fw-bold px-3">
            <i class="bi bi-pencil-square me-1"></i> Edit Booking
          </a>
          <a id="modal-btn-receipt" href="#" target="_blank" class="btn btn-sm btn-outline-success">
            <i class="bi bi-receipt me-1"></i> Receipt
          </a>
          <button type="button" id="modal-btn-sms" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-chat-dots me-1"></i> SMS
          </button>
          <button type="button" id="modal-btn-email" class="btn btn-sm btn-outline-info">
            <i class="bi bi-envelope me-1"></i> Email
          </button>
        </div>
        <button type="button" class="btn btn-sm btn-secondary px-3" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<script>
(function() {
    window.SHARED_DETAILS_JSON_BASE_URL = window.SHARED_DETAILS_JSON_BASE_URL || "{{ url('bookings/details-json') }}";
    window.activeViewBookingId = window.activeViewBookingId || null;
    window.activeViewBookingData = window.activeViewBookingData || null;

    window.formatBookingDateTime = window.formatBookingDateTime || function(dtStr) {
        if (!dtStr) return '-';
        try {
            const dt = new Date(dtStr);
            if (isNaN(dt.getTime())) return dtStr;
            return dt.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) + ' ' + dt.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
        } catch(e) {
            return dtStr;
        }
    };

    window.formatActivityDateTime = window.formatActivityDateTime || function(dtStr) {
        if (!dtStr) return '-';
        try {
            const dt = new Date(dtStr);
            if (isNaN(dt.getTime())) return dtStr;
            return dt.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) + ' ' + dt.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        } catch(e) {
            return dtStr;
        }
    };

    window.escapeBookingHtml = window.escapeBookingHtml || function(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };

    window.getActivityActionBadge = window.getActivityActionBadge || function(action) {
        action = action || 'Activity';
        const lower = action.toLowerCase();
        let bgClass = 'bg-primary-subtle text-primary border border-primary-subtle';
        let icon = 'bi-activity';
        let typeClass = 'action-default';

        if (lower.includes('status')) {
            bgClass = 'bg-info-subtle text-info-emphasis border border-info-subtle';
            icon = 'bi-arrow-repeat';
            typeClass = 'action-status';
        } else if (lower.includes('dispatch') || lower.includes('assign')) {
            bgClass = 'bg-success-subtle text-success-emphasis border border-success-subtle';
            icon = 'bi-person-check';
            typeClass = 'action-dispatch';
        } else if (lower.includes('recall') || lower.includes('cancel')) {
            bgClass = 'bg-danger-subtle text-danger-emphasis border border-danger-subtle';
            icon = 'bi-person-x';
            typeClass = 'action-recall';
        } else if (lower.includes('sms')) {
            bgClass = 'bg-purple-subtle text-purple-emphasis border border-purple-subtle';
            icon = 'bi-chat-dots';
            typeClass = 'action-sms';
        } else if (lower.includes('email')) {
            bgClass = 'bg-cyan-subtle text-cyan-emphasis border border-cyan-subtle';
            icon = 'bi-envelope';
            typeClass = 'action-email';
        } else if (lower.includes('edit') || lower.includes('update')) {
            bgClass = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
            icon = 'bi-pencil-square';
            typeClass = 'action-edit';
        } else if (lower.includes('create') || lower.includes('initial')) {
            bgClass = 'bg-secondary-subtle text-dark border border-secondary-subtle';
            icon = 'bi-plus-circle';
            typeClass = 'action-create';
        }

        return {
            badgeHtml: `<span class="badge ${bgClass} fw-semibold" style="font-size: 0.75rem;"><i class="bi ${icon} me-1"></i>${window.escapeBookingHtml(action)}</span>`,
            typeClass: typeClass
        };
    };

    window.populateViewBookingModal = function(booking) {
        if (!booking) return;
        window.activeViewBookingData = booking;

        const bookingId = booking.id || booking.booking_id || '';
        const refNo = booking.ref_no || bookingId || '-';
        const status = (booking.status || 'pending').toLowerCase();

        // Header badges
        const refBadge = document.getElementById('modal-ref-badge');
        if (refBadge) refBadge.textContent = 'REF# ' + refNo;

        const statusBadge = document.getElementById('modal-status-badge');
        if (statusBadge) {
            statusBadge.textContent = status.toUpperCase();
            let statusBg = '#6c757d';
            if (status === 'completed') statusBg = '#198754';
            else if (status === 'dispatched' || status === 'accepted' || status === 'allocated') statusBg = '#0d6efd';
            else if (status === 'pickedup' || status === 'onway') statusBg = '#0dcaf0';
            else if (status === 'cancelled' || status === 'rejected' || status === 'declined' || status === 'job_cancelled') statusBg = '#dc3545';
            else if (status === 'pending') statusBg = '#ffc107';
            statusBadge.style.backgroundColor = statusBg;
            statusBadge.style.color = (status === 'pending') ? '#111827' : '#ffffff';
        }

        // Top Stats Banner
        const formattedPickupTime = window.formatBookingDateTime(booking.pickup_time || ((booking.pickup_date || '') + ' ' + (booking.pickup_time || '')));
        const topPickupTime = document.getElementById('modal-top-pickup-time');
        if (topPickupTime) topPickupTime.textContent = formattedPickupTime;

        // Driver details
        let driverName = booking.driver_name || booking.driver || '';
        let driverCallSign = booking.driver_call_sign || booking.call_sign || '';
        let driverText = 'Not Assigned';
        if (driverCallSign || driverName) {
            driverText = (driverCallSign ? `[${driverCallSign}] ` : '') + (driverName || 'Driver');
        } else if (booking.driver_id || booking.driverId) {
            driverText = 'Driver Assigned';
        }

        const topDriver = document.getElementById('modal-top-driver');
        if (topDriver) topDriver.textContent = driverText;

        const topPrice = document.getElementById('modal-top-price');
        if (topPrice) topPrice.textContent = '£' + (parseFloat(booking.price || 0).toFixed(2));

        const topPayment = document.getElementById('modal-top-payment');
        if (topPayment) topPayment.textContent = booking.payment_type || 'Cash';

        // Passenger Info
        const passName = document.getElementById('modal-passenger_name');
        if (passName) passName.textContent = booking.passenger_name || '-';

        const phoneNo = document.getElementById('modal-phone_no');
        if (phoneNo) phoneNo.textContent = booking.phone_no || '-';

        const phoneCall = document.getElementById('modal-phone-call');
        if (phoneCall) {
            if (booking.phone_no) {
                phoneCall.href = 'tel:' + booking.phone_no;
                phoneCall.style.display = '';
            } else {
                phoneCall.style.display = 'none';
            }
        }

        const emailEl = document.getElementById('modal-email');
        if (emailEl) emailEl.textContent = booking.email || '-';

        const flightEl = document.getElementById('modal-flight_no');
        if (flightEl) flightEl.textContent = booking.flight_no || '-';

        // Route & Stops
        const pickupAddr = document.getElementById('modal-pickup_address');
        if (pickupAddr) pickupAddr.textContent = booking.pickup_address || '-';

        const pickupTimeSub = document.getElementById('modal-pickup_time_sub');
        if (pickupTimeSub) pickupTimeSub.textContent = 'Pickup: ' + formattedPickupTime;

        const dropoffAddr = document.getElementById('modal-dropoff_address');
        if (dropoffAddr) dropoffAddr.textContent = booking.dropoff_address || '-';

        // Render Via Stops
        const viasContainer = document.getElementById('modal-vias-container');
        if (viasContainer) {
            viasContainer.innerHTML = '';
            let vias = [];
            if (Array.isArray(booking.vias)) vias = booking.vias;
            else if (Array.isArray(booking.via_addresses)) vias = booking.via_addresses;
            else if (typeof booking.vias === 'object' && booking.vias !== null) vias = Object.values(booking.vias);

            vias = vias.filter(v => v && String(v).trim().length > 0);

            if (vias.length > 0) {
                vias.forEach((via, idx) => {
                    const viaNode = document.createElement('div');
                    viaNode.className = 'route-node position-relative mb-3';
                    viaNode.innerHTML = `
                        <span class="route-dot bg-warning rounded-circle position-absolute" style="left: -28px; top: 3px; width: 14px; height: 14px; border: 3px solid #fff3cd;"></span>
                        <div class="small fw-bold text-warning-emphasis text-uppercase" style="font-size: 0.7rem;">Via Stop #${idx + 1}</div>
                        <div class="text-dark fw-medium" style="font-size: 0.88rem;">${window.escapeBookingHtml(via)}</div>
                    `;
                    viasContainer.appendChild(viaNode);
                });
            }
        }

        // Google Maps Route Link
        const mapsLink = document.getElementById('modal-route-maps-link');
        if (mapsLink) {
            if (booking.pickup_address && booking.dropoff_address) {
                mapsLink.href = `https://www.google.com/maps/dir/?api=1&origin=${encodeURIComponent(booking.pickup_address)}&destination=${encodeURIComponent(booking.dropoff_address)}`;
                mapsLink.style.display = '';
            } else {
                mapsLink.style.display = 'none';
            }
        }

        // Vehicle, Driver & Pricing Breakdown
        const vehicleEl = document.getElementById('modal-vehicle_id');
        if (vehicleEl) vehicleEl.textContent = booking.vehicle_make || booking.vehicle_id || '-';

        const driverDetailsEl = document.getElementById('modal-driver-details');
        if (driverDetailsEl) {
            if (driverCallSign || driverName) {
                driverDetailsEl.innerHTML = `<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-2 py-1">${driverCallSign ? `<strong>[${window.escapeBookingHtml(driverCallSign)}]</strong> ` : ''}${window.escapeBookingHtml(driverName || 'Driver')}</span>`;
            } else {
                driverDetailsEl.innerHTML = `<span class="badge bg-light text-muted border px-2 py-1">Not Assigned</span>`;
            }
        }

        const fareEl = document.getElementById('modal-fare');
        if (fareEl) fareEl.textContent = '£' + (parseFloat(booking.fare || booking.price || 0).toFixed(2));

        const parkingEl = document.getElementById('modal-parking');
        if (parkingEl) parkingEl.textContent = '£' + (parseFloat(booking.parking || 0).toFixed(2));

        const waitingEl = document.getElementById('modal-waiting');
        if (waitingEl) waitingEl.textContent = '£' + (parseFloat(booking.waiting_fee || 0).toFixed(2));

        const extraEl = document.getElementById('modal-extra');
        if (extraEl) extraEl.textContent = '£' + (parseFloat(booking.extra || 0).toFixed(2));

        const priceEl = document.getElementById('modal-price');
        if (priceEl) priceEl.textContent = '£' + (parseFloat(booking.price || 0).toFixed(2));

        const childSeatEl = document.getElementById('modal-child_seat');
        if (childSeatEl) {
            childSeatEl.innerHTML = (booking.child_seat == 1 || booking.child_seat === true || booking.child_seat === '1')
                ? '<span class="badge bg-success px-2 py-1">Yes (Requested)</span>'
                : '<span class="badge bg-light text-muted border px-2 py-1">No</span>';
        }

        const payTypeEl = document.getElementById('modal-payment_type');
        if (payTypeEl) payTypeEl.textContent = booking.payment_type || 'Cash';

        const commentEl = document.getElementById('modal-comment');
        if (commentEl) commentEl.textContent = booking.job_comment || 'No special comments or instructions provided.';

        // Modal Action buttons
        const editBtn = document.getElementById('modal-btn-edit');
        if (editBtn) editBtn.href = `{{ url('/bookings') }}/${encodeURIComponent(bookingId)}/edit`;

        const receiptBtn = document.getElementById('modal-btn-receipt');
        if (receiptBtn) receiptBtn.href = `{{ url('/receipt') }}/${encodeURIComponent(bookingId)}`;
    };

    window.loadBookingDetailsAndLogs = async function(bookingId) {
        if (!bookingId) return;
        window.activeViewBookingId = bookingId;

        const timelineContainer = document.getElementById('modal-activity-timeline');
        const emptyContainer = document.getElementById('modal-activity-empty');
        const loadingContainer = document.getElementById('modal-activity-loading');
        const countBadge = document.getElementById('modal-activity-count');

        if (loadingContainer) loadingContainer.classList.remove('d-none');
        if (emptyContainer) emptyContainer.classList.add('d-none');
        if (timelineContainer) timelineContainer.innerHTML = '';

        try {
            const detailsUrl = `${window.SHARED_DETAILS_JSON_BASE_URL}?id=${encodeURIComponent(bookingId)}&booking_id=${encodeURIComponent(bookingId)}`;
            const res = await fetch(detailsUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!res.ok) throw new Error('Failed to load booking details');

            const data = await res.json();
            if (data?.booking) {
                window.populateViewBookingModal(data.booking);
            }

            const logs = data?.activity_logs || [];
            if (countBadge) countBadge.textContent = `${logs.length} record${logs.length === 1 ? '' : 's'}`;

            if (loadingContainer) loadingContainer.classList.add('d-none');

            if (logs.length === 0) {
                if (emptyContainer) emptyContainer.classList.remove('d-none');
            } else {
                if (emptyContainer) emptyContainer.classList.add('d-none');
                
                let timelineHtml = '';
                logs.forEach(log => {
                    const actionInfo = window.getActivityActionBadge(log.action);
                    const formattedTime = window.formatActivityDateTime(log.created_at || log.timestamp);
                    const staffName = log.staff_name || 'Staff Member';
                    const staffRole = log.staff_role ? `<span class="badge bg-secondary-subtle text-secondary py-0 px-1 ms-1" style="font-size: 0.68rem;">${window.escapeBookingHtml(log.staff_role)}</span>` : '';
                    const desc = log.description || '';

                    timelineHtml += `
                        <div class="activity-timeline-item ${actionInfo.typeClass}">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-dark text-white fw-medium py-1 px-2" style="font-size: 0.75rem;">
                                        <i class="bi bi-person-fill text-warning me-1"></i>${window.escapeBookingHtml(staffName)}
                                    </span>
                                    ${staffRole}
                                </div>
                                <span class="text-muted small" style="font-size: 0.72rem;">
                                    <i class="bi bi-clock me-1"></i>${window.escapeBookingHtml(formattedTime)}
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-1 mb-1">
                                ${actionInfo.badgeHtml}
                            </div>
                            <div class="text-secondary small" style="font-size: 0.82rem; line-height: 1.4;">
                                ${window.escapeBookingHtml(desc)}
                            </div>
                        </div>
                    `;
                });

                if (timelineContainer) timelineContainer.innerHTML = timelineHtml;
            }
        } catch (err) {
            console.error('Error fetching booking details/logs:', err);
            if (loadingContainer) loadingContainer.classList.add('d-none');
            if (emptyContainer) {
                emptyContainer.classList.remove('d-none');
                emptyContainer.innerHTML = `
                    <i class="bi bi-exclamation-triangle text-danger fs-3 d-block mb-2"></i>
                    <div class="text-danger fw-medium">Failed to load activity logs</div>
                    <div class="small text-muted">Please click refresh to retry.</div>
                `;
            }
        }
    };

    window.openViewBookingModal = function(bookingOrId) {
        if (!bookingOrId) return;
        let booking = null;
        let bookingId = '';

        if (typeof bookingOrId === 'object') {
            booking = bookingOrId;
            bookingId = booking.id || booking.booking_id || '';
            window.populateViewBookingModal(booking);
        } else {
            bookingId = String(bookingOrId);
        }

        const modalEl = document.getElementById('viewBookingModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }

        if (bookingId) {
            window.loadBookingDetailsAndLogs(bookingId);
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        const refreshLogsBtn = document.getElementById('modal-refresh-logs-btn');
        if (refreshLogsBtn) {
            refreshLogsBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (window.activeViewBookingId) {
                    window.loadBookingDetailsAndLogs(window.activeViewBookingId);
                }
            });
        }

        // Handle stacked modal scrolling restoration
        const viewModalEl = document.getElementById('viewBookingModal');
        if (viewModalEl) {
            viewModalEl.addEventListener('hidden.bs.modal', function() {
                if (document.querySelector('.modal.show')) {
                    document.body.classList.add('modal-open');
                }
            });
        }
    });
})();
</script>
