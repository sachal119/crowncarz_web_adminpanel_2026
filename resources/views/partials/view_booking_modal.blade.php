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
#modal-map-wrapper {
  transition: all 0.3s ease-in-out;
}
#modal-map-wrapper.map-collapsed {
  display: none !important;
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
          <div class="col-6 col-md">
            <div class="p-2 p-md-3 rounded-3 bg-white border shadow-sm h-100 d-flex flex-column justify-content-center">
              <span class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;"><i class="bi bi-calendar3 me-1 text-primary"></i> Pickup Date & Time</span>
              <span id="modal-top-pickup-time" class="fw-bold text-dark mt-1 text-truncate" style="font-size: 0.92rem;">-</span>
            </div>
          </div>
          <div class="col-6 col-md">
            <div class="p-2 p-md-3 rounded-3 bg-white border shadow-sm h-100 d-flex flex-column justify-content-center">
              <span class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;"><i class="bi bi-speedometer2 me-1 text-primary"></i> Total Mileage</span>
              <span id="modal-top-mileage" class="fw-bold text-dark mt-1 text-truncate" style="font-size: 0.92rem;">-</span>
            </div>
          </div>
          <div class="col-6 col-md">
            <div class="p-2 p-md-3 rounded-3 bg-white border shadow-sm h-100 d-flex flex-column justify-content-center">
              <span class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;"><i class="bi bi-car-front-fill me-1 text-info"></i> Vehicle & Driver</span>
              <span id="modal-top-driver" class="fw-bold text-dark mt-1 text-truncate" style="font-size: 0.92rem;">-</span>
            </div>
          </div>
          <div class="col-6 col-md">
            <div class="p-2 p-md-3 rounded-3 bg-white border shadow-sm h-100 d-flex flex-column justify-content-center">
              <span class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem;"><i class="bi bi-cash-stack me-1 text-success"></i> Total Fare</span>
              <span id="modal-top-price" class="fw-bold text-success mt-1 text-truncate" style="font-size: 1.05rem;">-</span>
            </div>
          </div>
          <div class="col-6 col-md">
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
              <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-geo-alt-fill text-danger me-2 fs-6"></i>
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.88rem;">Journey Route & Stops</h6>
                  </div>
                  <span id="modal-route-distance-badge" class="badge bg-primary-subtle text-primary border border-primary-subtle d-none" style="font-size: 0.72rem;">
                    <i class="bi bi-speedometer2 me-1"></i><span id="modal-route-distance-val">0.00 Mi</span>
                  </span>
                  <span id="modal-vias-count-badge" class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle d-none" style="font-size: 0.72rem;">
                    <i class="bi bi-pin-map-fill me-1"></i><span id="modal-vias-count-val">0 Vias</span>
                  </span>
                  <span id="modal-route-duration-badge" class="badge bg-info-subtle text-info-emphasis border border-info-subtle d-none" style="font-size: 0.72rem;">
                    <i class="bi bi-clock-history me-1"></i><span id="modal-route-duration-val">~0 min</span>
                  </span>
                </div>
                <div class="d-flex align-items-center gap-1">
                  <button type="button" id="modal-toggle-map-btn" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem;" title="Toggle Interactive Route Map">
                    <i class="bi bi-map me-1"></i><span id="modal-toggle-map-text">Hide Map</span>
                  </button>
                  <a id="modal-route-maps-link" href="#" target="_blank" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;" title="Open full route in Google Maps">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Open in Maps
                  </a>
                </div>
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

                <!-- 🗺️ Interactive Route Map Container -->
                <div id="modal-map-wrapper" class="mt-3 rounded-3 overflow-hidden border shadow-sm position-relative" style="height: 240px; background: #eef2f6;">
                  <div id="modal-route-map-canvas" style="width: 100%; height: 100%;"></div>
                  
                  <!-- Map Loading Spinner -->
                  <div id="modal-map-loading" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-white bg-opacity-75" style="z-index: 5;">
                    <div class="spinner-border spinner-border-sm text-primary mb-1" role="status"></div>
                    <span class="small text-muted fw-medium" style="font-size: 0.75rem;">Loading route map...</span>
                  </div>
                  
                  <!-- Map Empty / Error State -->
                  <div id="modal-map-empty" class="position-absolute top-0 start-0 w-100 h-100 d-none flex-column align-items-center justify-content-center bg-light text-muted p-3 text-center" style="z-index: 4;">
                    <i class="bi bi-geo-alt fs-3 text-secondary opacity-50 mb-1"></i>
                    <div class="small fw-medium text-dark" id="modal-map-empty-text">No route coordinates available</div>
                    <div class="text-muted" style="font-size: 0.7rem;">Enter valid pickup and dropoff addresses to plot the route.</div>
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
          <button type="button" id="modal-btn-whatsapp" class="btn btn-sm btn-outline-success" style="color: #128C7E; border-color: #128C7E;">
            <i class="bi bi-whatsapp me-1"></i> WhatsApp
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
    window.GOOGLE_MAPS_API_KEY = "{{ config('services.google_maps.key') ?? '' }}";
    window.activeViewBookingId = window.activeViewBookingId || null;
    window.activeViewBookingData = window.activeViewBookingData || null;
    window._viewBookingGoogleMap = null;
    window._viewBookingDirectionsService = null;
    window._viewBookingDirectionsRenderer = null;
    window._googleMapsLoadingPromise = null;

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

    /**
     * 📍 Robust Via Stops Extractor (handles arrays, objects, JSON strings, line breaks)
     */
    window.extractBookingVias = function(booking) {
        if (!booking) return [];
        let raw = booking.vias || booking.via_addresses || booking.via_points || booking.via || [];

        if (typeof raw === 'string') {
            const trimmed = raw.trim();
            if (trimmed.startsWith('[') && trimmed.endsWith(']')) {
                try {
                    raw = JSON.parse(trimmed);
                } catch(e) {
                    raw = [trimmed];
                }
            } else if (trimmed.includes('||')) {
                raw = trimmed.split('||');
            } else if (trimmed.includes('\n')) {
                raw = trimmed.split('\n');
            } else if (trimmed.length > 0) {
                raw = [trimmed];
            } else {
                raw = [];
            }
        } else if (typeof raw === 'object' && raw !== null && !Array.isArray(raw)) {
            raw = Object.values(raw);
        }

        if (!Array.isArray(raw)) raw = [];

        const vias = [];
        raw.forEach(item => {
            if (!item) return;
            let addr = '';
            if (typeof item === 'string') {
                addr = item.trim();
            } else if (typeof item === 'object') {
                addr = (item.address || item.location || item.name || item.text || '').trim();
            }
            if (addr && addr.length > 0 && addr.toLowerCase() !== 'null' && addr.toLowerCase() !== 'undefined') {
                vias.push(addr);
            }
        });
        return vias;
    };

    /**
     * 🏎️ Extract Stored Mileage/Distance from Booking Object
     */
    window.extractBookingMileage = function(booking) {
        if (!booking) return '';
        let val = booking.distance || booking.total_distance || booking.journey_distance || booking.mileage || booking.total_mileage || '';
        if (typeof val === 'number') {
            return val > 0 ? `${val.toFixed(2)} Mi` : '';
        }
        if (typeof val === 'string') {
            val = val.trim();
            if (val && !val.toLowerCase().includes('mi') && !val.toLowerCase().includes('km')) {
                const num = parseFloat(val);
                if (!isNaN(num) && num > 0) return `${num.toFixed(2)} Mi`;
            }
            return val;
        }
        return '';
    };

    /**
     * 🌐 Google Maps Loader
     */
    window.loadGoogleMapsScript = function(callback) {
        if (window.google && window.google.maps && window.google.maps.DirectionsService) {
            if (typeof callback === 'function') callback();
            return;
        }

        if (!window._googleMapsLoadingPromise) {
            window._googleMapsLoadingPromise = new Promise((resolve, reject) => {
                const apiKey = window.GOOGLE_MAPS_API_KEY || '';
                const script = document.createElement('script');
                script.src = `https://maps.googleapis.com/maps/api/js?key=${encodeURIComponent(apiKey)}&libraries=places`;
                script.async = true;
                script.defer = true;
                script.onload = () => resolve();
                script.onerror = (e) => reject(e);
                document.head.appendChild(script);
            });
        }

        window._googleMapsLoadingPromise
            .then(() => {
                if (typeof callback === 'function') callback();
            })
            .catch(err => {
                console.error('Failed to load Google Maps script:', err);
                const mapLoading = document.getElementById('modal-map-loading');
                const mapEmpty = document.getElementById('modal-map-empty');
                const emptyText = document.getElementById('modal-map-empty-text');
                if (mapLoading) mapLoading.classList.add('d-none');
                if (mapEmpty) {
                    mapEmpty.classList.remove('d-none');
                    if (emptyText) emptyText.textContent = 'Google Maps service unavailable';
                }
            });
    };

    /**
     * 🗺️ Route Map Drawer with Waypoints & Total Mileage Calculation
     */
    window.initOrUpdateBookingRouteMap = function(origin, destination, vias) {
        const mapCanvas = document.getElementById('modal-route-map-canvas');
        const mapLoading = document.getElementById('modal-map-loading');
        const mapEmpty = document.getElementById('modal-map-empty');
        const emptyText = document.getElementById('modal-map-empty-text');

        if (!origin || !destination) {
            if (mapLoading) mapLoading.classList.add('d-none');
            if (mapEmpty) {
                mapEmpty.classList.remove('d-none');
                if (emptyText) emptyText.textContent = 'Missing pickup or dropoff address';
            }
            return;
        }

        if (mapEmpty) mapEmpty.classList.add('d-none');
        if (mapLoading) mapLoading.classList.remove('d-none');

        const renderDirections = function() {
            try {
                if (!window._viewBookingGoogleMap && mapCanvas) {
                    window._viewBookingGoogleMap = new google.maps.Map(mapCanvas, {
                        zoom: 11,
                        center: { lat: 51.5074, lng: -0.1278 }, // London/UK fallback
                        mapTypeControl: false,
                        streetViewControl: false,
                        fullscreenControl: true,
                        zoomControl: true,
                    });
                    window._viewBookingDirectionsService = new google.maps.DirectionsService();
                    window._viewBookingDirectionsRenderer = new google.maps.DirectionsRenderer({
                        map: window._viewBookingGoogleMap,
                        suppressMarkers: false,
                        polylineOptions: {
                            strokeColor: '#0284c7',
                            strokeWeight: 5,
                            strokeOpacity: 0.85
                        }
                    });
                }

                const waypoints = (vias || []).map(v => ({
                    location: v,
                    stopover: true
                }));

                window._viewBookingDirectionsService.route({
                    origin: origin,
                    destination: destination,
                    waypoints: waypoints,
                    travelMode: google.maps.TravelMode.DRIVING,
                    optimizeWaypoints: false
                }, function(response, status) {
                    if (mapLoading) mapLoading.classList.add('d-none');

                    if (status === google.maps.DirectionsStatus.OK && response) {
                        window._viewBookingDirectionsRenderer.setDirections(response);

                        // Compute total distance & duration from legs
                        let totalMeters = 0;
                        let totalSeconds = 0;
                        const legs = response.routes[0]?.legs || [];
                        legs.forEach(leg => {
                            if (leg.distance && typeof leg.distance.value === 'number') {
                                totalMeters += leg.distance.value;
                            }
                            if (leg.duration && typeof leg.duration.value === 'number') {
                                totalSeconds += leg.duration.value;
                            }
                        });

                        if (totalMeters > 0) {
                            const calculatedMiles = (totalMeters * 0.000621371).toFixed(2) + ' Mi';
                            
                            // Update Top Mileage if empty or placeholder
                            const topMileage = document.getElementById('modal-top-mileage');
                            if (topMileage && (!topMileage.textContent || topMileage.textContent === '-' || topMileage.textContent.trim() === '')) {
                                topMileage.textContent = calculatedMiles;
                            }

                            // Update Route distance badge
                            const distBadge = document.getElementById('modal-route-distance-badge');
                            const distVal = document.getElementById('modal-route-distance-val');
                            if (distBadge && distVal) {
                                distVal.textContent = calculatedMiles;
                                distBadge.classList.remove('d-none');
                            }
                        }

                        if (totalSeconds > 0) {
                            const mins = Math.round(totalSeconds / 60);
                            const durText = mins >= 60 ? `${Math.floor(mins/60)}h ${mins%60}m` : `${mins} mins`;
                            const durBadge = document.getElementById('modal-route-duration-badge');
                            const durVal = document.getElementById('modal-route-duration-val');
                            if (durBadge && durVal) {
                                durVal.textContent = '~' + durText;
                                durBadge.classList.remove('d-none');
                            }
                        }

                        setTimeout(() => {
                            if (window._viewBookingGoogleMap) {
                                google.maps.event.trigger(window._viewBookingGoogleMap, 'resize');
                            }
                        }, 200);
                    } else {
                        console.warn('Google Maps directions error:', status);
                        if (mapEmpty) {
                            mapEmpty.classList.remove('d-none');
                            if (emptyText) emptyText.textContent = `Unable to plot route (${status})`;
                        }
                    }
                });
            } catch(e) {
                console.error('Error in directions rendering:', e);
                if (mapLoading) mapLoading.classList.add('d-none');
                if (mapEmpty) {
                    mapEmpty.classList.remove('d-none');
                    if (emptyText) emptyText.textContent = 'Map display error';
                }
            }
        };

        if (window.google && window.google.maps && window.google.maps.DirectionsService) {
            renderDirections();
        } else {
            window.loadGoogleMapsScript(renderDirections);
        }
    };

    /**
     * 📋 Populate Modal Data & Setup Map
     */
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

        // Total Mileage
        const storedMileage = window.extractBookingMileage(booking);
        const topMileage = document.getElementById('modal-top-mileage');
        if (topMileage) topMileage.textContent = storedMileage || '-';

        // Route Distance Badge
        const distBadge = document.getElementById('modal-route-distance-badge');
        const distVal = document.getElementById('modal-route-distance-val');
        if (distBadge && distVal) {
            if (storedMileage) {
                distVal.textContent = storedMileage;
                distBadge.classList.remove('d-none');
            } else {
                distBadge.classList.add('d-none');
            }
        }

        const durBadge = document.getElementById('modal-route-duration-badge');
        if (durBadge) durBadge.classList.add('d-none');

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
        const pickupAddr = (booking.pickup_address || '').trim();
        const dropoffAddr = (booking.dropoff_address || '').trim();

        const pickupEl = document.getElementById('modal-pickup_address');
        if (pickupEl) pickupEl.textContent = pickupAddr || '-';

        const pickupTimeSub = document.getElementById('modal-pickup_time_sub');
        if (pickupTimeSub) pickupTimeSub.textContent = 'Pickup: ' + formattedPickupTime;

        const dropoffEl = document.getElementById('modal-dropoff_address');
        if (dropoffEl) dropoffEl.textContent = dropoffAddr || '-';

        // Render Via Stops
        const vias = window.extractBookingVias(booking);
        const viasContainer = document.getElementById('modal-vias-container');
        const viasBadge = document.getElementById('modal-vias-count-badge');
        const viasCountVal = document.getElementById('modal-vias-count-val');

        if (viasContainer) {
            viasContainer.innerHTML = '';
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

        if (viasBadge && viasCountVal) {
            if (vias.length > 0) {
                viasCountVal.textContent = `${vias.length} Via${vias.length > 1 ? 's' : ''}`;
                viasBadge.classList.remove('d-none');
            } else {
                viasBadge.classList.add('d-none');
            }
        }

        // Google Maps Route Link (with waypoints)
        const mapsLink = document.getElementById('modal-route-maps-link');
        if (mapsLink) {
            if (pickupAddr && dropoffAddr) {
                let url = `https://www.google.com/maps/dir/?api=1&origin=${encodeURIComponent(pickupAddr)}&destination=${encodeURIComponent(dropoffAddr)}`;
                if (vias.length > 0) {
                    url += `&waypoints=${encodeURIComponent(vias.join('|'))}`;
                }
                mapsLink.href = url;
                mapsLink.style.display = '';
            } else {
                mapsLink.style.display = 'none';
            }
        }

        // Render / Update Route Map
        window.initOrUpdateBookingRouteMap(pickupAddr, dropoffAddr, vias);

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

        // Toggle map view button
        const toggleMapBtn = document.getElementById('modal-toggle-map-btn');
        const mapWrapper = document.getElementById('modal-map-wrapper');
        const toggleMapText = document.getElementById('modal-toggle-map-text');

        if (toggleMapBtn && mapWrapper) {
            toggleMapBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const isCollapsed = mapWrapper.classList.toggle('map-collapsed');
                if (toggleMapText) {
                    toggleMapText.textContent = isCollapsed ? 'Show Map' : 'Hide Map';
                }
                if (!isCollapsed && window._viewBookingGoogleMap) {
                    setTimeout(() => {
                        google.maps.event.trigger(window._viewBookingGoogleMap, 'resize');
                    }, 150);
                }
            });
        }

        // Handle modal resize / map trigger on open
        const viewModalEl = document.getElementById('viewBookingModal');
        if (viewModalEl) {
            viewModalEl.addEventListener('shown.bs.modal', function() {
                if (window._viewBookingGoogleMap) {
                    google.maps.event.trigger(window._viewBookingGoogleMap, 'resize');
                    if (window._viewBookingDirectionsRenderer && window._viewBookingDirectionsRenderer.getDirections()) {
                        const bounds = window._viewBookingDirectionsRenderer.getDirections().routes[0]?.bounds;
                        if (bounds) window._viewBookingGoogleMap.fitBounds(bounds);
                    }
                }
            });

            viewModalEl.addEventListener('hidden.bs.modal', function() {
                if (document.querySelector('.modal.show')) {
                    document.body.classList.add('modal-open');
                }
            });
        }
    });
})();
</script>
