<!-- ✈️ Live Flight Tracker Modal (Google-Style In-Page Popup) -->
<div class="modal fade" id="flightTrackerModal" tabindex="-1" aria-labelledby="flightTrackerModalLabel" aria-hidden="true" style="z-index: 1099;">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden" style="background: #111827; color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
      
      <!-- Modal Header -->
      <div class="modal-header border-bottom border-secondary border-opacity-25 px-4 py-3" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
        <div class="d-flex align-items-center gap-3 w-100 pe-2">
          <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background: rgba(230, 176, 74, 0.15); border: 1px solid rgba(230, 176, 74, 0.35);">
            <i class="bi bi-airplane-engines text-warning fs-5"></i>
          </div>
          <div class="overflow-hidden">
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <h5 class="modal-title fw-bold text-white mb-0" id="ftm-header-title">British Airways BA 327</h5>
              <span id="ftm-status-pill" class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 px-2.5 py-1 fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">ACTIVE TRACKING</span>
            </div>
            <div class="text-white-50 small mt-0.5" id="ftm-header-subtitle" style="font-size: 0.78rem;">Live Flight Overview & Status Details</div>
          </div>
          <div class="ms-auto d-flex align-items-center gap-2 flex-shrink-0">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
        </div>
      </div>

      <!-- Modal Body -->
      <div class="modal-body p-3 p-md-4" style="background: #0b1120;">
        
        <!-- 🌟 Google-Style Flight Card Widget -->
        <div class="card border border-secondary border-opacity-25 rounded-4 p-4 mb-3 shadow-lg position-relative overflow-hidden" style="background: #182234;">
          
          <!-- Top Row: Airline & Flight No -->
          <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
            <div>
              <div class="d-flex align-items-center gap-2 mb-1">
                <div class="text-white-50 text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.8px;" id="ftm-route-hint">FLIGHT DETAILS</div>
                <span id="ftm-aircraft-pill" class="badge bg-dark text-info border border-info border-opacity-25 px-2 py-0.5" style="font-size: 0.68rem; letter-spacing: 0.3px;">Airbus A320neo</span>
              </div>
              <h3 class="fw-bold text-white mb-0" id="ftm-card-title">British Airways BA 327</h3>
            </div>
            <div class="d-flex align-items-center gap-2">
              <span class="badge bg-warning text-dark fw-bold px-3 py-1.5 rounded-pill font-monospace" style="font-size: 0.85rem;" id="ftm-code-badge">BA327</span>
            </div>
          </div>

          <!-- Route Visualizer (NCE ──────✈️──────> LHR) -->
          <div class="py-3 px-3 rounded-3 mb-3" style="background: #0f172a; border: 1px solid rgba(255, 255, 255, 0.06);">
            <div class="d-flex align-items-center justify-content-between position-relative px-2">
              
              <!-- Origin Point -->
              <div class="text-start">
                <div class="fw-bold text-white font-monospace" style="font-size: 1.6rem; letter-spacing: 1px;" id="ftm-origin-code">DEP</div>
                <div class="text-white-50 small" id="ftm-origin-city">Departure Airport</div>
              </div>

              <!-- Flight Progress Line -->
              <div class="flex-grow-1 mx-3 position-relative text-center">
                <div class="d-flex align-items-center justify-content-center position-relative" style="height: 24px;">
                  <div style="height: 2px; width: 100%; background: linear-gradient(90deg, #10b981 0%, #38bdf8 50%, #E6B04A 100%);"></div>
                  <div class="position-absolute bg-dark px-2 rounded-pill border border-secondary border-opacity-50 text-warning d-flex align-items-center gap-1 shadow-sm" style="font-size: 0.72rem; transform: translateY(-1px);">
                    <i class="bi bi-airplane-fill" style="transform: rotate(90deg);"></i>
                    <span id="ftm-flight-duration">Live En Route</span>
                  </div>
                </div>
              </div>

              <!-- Destination Point -->
              <div class="text-end">
                <div class="fw-bold text-warning font-monospace" style="font-size: 1.6rem; letter-spacing: 1px;" id="ftm-dest-code">ARR</div>
                <div class="text-white-50 small" id="ftm-dest-city">Arrival Airport</div>
              </div>

            </div>
          </div>

          <!-- Detailed Times & Terminal Grid -->
          <div class="row g-2 mb-2 text-center">
            
            <!-- Departure Column -->
            <div class="col-6">
              <div class="p-3 rounded-3 h-100 text-start" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.05);">
                <div class="text-white-50 text-uppercase fw-semibold mb-1" style="font-size: 0.7rem;">Departure Info</div>
                <div class="fw-bold text-white" style="font-size: 1.1rem;" id="ftm-dep-time">Scheduled</div>
                <div class="text-white-50 small mt-1" style="font-size: 0.75rem;">
                  <span class="me-2">Terminal: <strong class="text-white" id="ftm-dep-terminal">-</strong></span>
                  <span>Gate: <strong class="text-white" id="ftm-dep-gate">-</strong></span>
                </div>
              </div>
            </div>

            <!-- Arrival Column -->
            <div class="col-6">
              <div class="p-3 rounded-3 h-100 text-start" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.05);">
                <div class="text-white-50 text-uppercase fw-semibold mb-1" style="font-size: 0.7rem;">Arrival / Landing Info</div>
                <div class="fw-bold text-success" style="font-size: 1.1rem;" id="ftm-arr-time">Live Status</div>
                <div class="text-white-50 small mt-1" style="font-size: 0.75rem;">
                  <span class="me-2">Terminal: <strong class="text-white" id="ftm-arr-terminal">LHR / UK</strong></span>
                  <span>Gate: <strong class="text-white" id="ftm-arr-gate">-</strong></span>
                </div>
              </div>
            </div>

          </div>

          <!-- Booking Context Row (if available) -->
          <div id="ftm-booking-context" class="p-2.5 rounded-3 mt-3 d-none align-items-center justify-content-between flex-wrap gap-2" style="background: rgba(230, 176, 74, 0.08); border: 1px dashed rgba(230, 176, 74, 0.3);">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-person-badge text-warning fs-5"></i>
              <div class="small">
                <span class="text-white-50">Passenger:</span> <strong class="text-white" id="ftm-passenger-name">-</strong>
                <span class="text-white-50 mx-1.5">•</span>
                <span class="text-white-50">Pickup Time:</span> <strong class="text-warning" id="ftm-pickup-time">-</strong>
              </div>
            </div>
            <div class="small text-white-50 text-truncate" style="max-width: 320px;" id="ftm-route-text">-</div>
          </div>

        </div>

        <!-- 🚀 Direct Live Tracker Shortcuts Grid (No iframe block, opens instantly) -->
        <div class="mb-3">
          <div class="text-white-50 small fw-semibold text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.6px;">
            <i class="bi bi-broadcast me-1 text-warning"></i> Open Live Radar & Official Flight Cards
          </div>
          <div class="row g-2">
            
            <!-- Google Live Status Card Button -->
            <div class="col-12 col-md-6">
              <a id="ftm-btn-google" href="#" target="_blank" rel="noopener noreferrer" class="btn w-100 p-3 rounded-3 text-start d-flex align-items-center gap-3 transition-all hover-scale" style="background: #1e293b; border: 1px solid #38bdf8; color: #fff;">
                <div class="rounded-circle bg-primary bg-opacity-20 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                  <i class="bi bi-google text-info fs-4"></i>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                  <div class="d-flex align-items-center justify-content-between">
                    <strong class="text-white" style="font-size: 0.9rem;">Google Live Flight Card</strong>
                    <i class="bi bi-box-arrow-up-right text-info small"></i>
                  </div>
                  <div class="text-white-50 small mt-0.5" style="font-size: 0.73rem;">Official arrival time, terminal, gate & live delay card</div>
                </div>
              </a>
            </div>

            <!-- FlightRadar24 Live 3D Radar Button -->
            <div class="col-12 col-md-6">
              <a id="ftm-btn-fr24" href="#" target="_blank" rel="noopener noreferrer" class="btn w-100 p-3 rounded-3 text-start d-flex align-items-center gap-3 transition-all hover-scale" style="background: #1e293b; border: 1px solid #10b981; color: #fff;">
                <div class="rounded-circle bg-success bg-opacity-20 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                  <i class="bi bi-radar text-success fs-4"></i>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                  <div class="d-flex align-items-center justify-content-between">
                    <strong class="text-white" style="font-size: 0.9rem;">FlightRadar24 Live Map</strong>
                    <i class="bi bi-box-arrow-up-right text-success small"></i>
                  </div>
                  <div class="text-white-50 small mt-0.5" style="font-size: 0.73rem;">Live airplane GPS location & 3D altitude radar</div>
                </div>
              </a>
            </div>

            <!-- FlightAware Button -->
            <div class="col-6 col-md-6">
              <a id="ftm-btn-flightaware" href="#" target="_blank" rel="noopener noreferrer" class="btn w-100 p-2.5 rounded-3 text-start d-flex align-items-center gap-2.5 transition-all" style="background: #182234; border: 1px solid rgba(255,255,255,0.08); color: #fff;">
                <i class="bi bi-compass text-warning fs-5"></i>
                <div>
                  <div class="fw-bold text-white small" style="font-size: 0.82rem;">FlightAware Status</div>
                  <div class="text-white-50" style="font-size: 0.68rem;">Speed & flight path history</div>
                </div>
              </a>
            </div>

            <!-- Heathrow / UK Airport Arrivals Board Button -->
            <div class="col-6 col-md-6">
              <a id="ftm-btn-airport" href="https://www.heathrow.com/arrivals" target="_blank" rel="noopener noreferrer" class="btn w-100 p-2.5 rounded-3 text-start d-flex align-items-center gap-2.5 transition-all" style="background: #182234; border: 1px solid rgba(255,255,255,0.08); color: #fff;">
                <i class="bi bi-building text-primary fs-5"></i>
                <div>
                  <div class="fw-bold text-white small" id="ftm-airport-btn-title" style="font-size: 0.82rem;">Airport Arrivals Board</div>
                  <div class="text-white-50" style="font-size: 0.68rem;">Live terminal arrival boards</div>
                </div>
              </a>
            </div>

          </div>
        </div>

        <!-- Copy & Quick Share Box -->
        <div class="p-3 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: rgba(30, 41, 59, 0.6); border: 1px solid rgba(255, 255, 255, 0.08);">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle-fill text-warning fs-5"></i>
            <div>
              <div class="fw-semibold text-white small">Flight Code: <span id="ftm-copy-code" class="text-warning font-monospace fw-bold">BA327</span></div>
              <div class="text-white-50" style="font-size: 0.72rem;">Click button to copy flight tracker link for driver / staff / WhatsApp.</div>
            </div>
          </div>
          <button id="ftm-copy-btn" type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
            <i class="bi bi-clipboard"></i> <span>Copy Tracking Link</span>
          </button>
        </div>

      </div>

      <!-- Modal Footer -->
      <div class="modal-footer border-top border-secondary border-opacity-25 px-4 py-2.5" style="background: #0f172a;">
        <span class="text-white-50 small me-auto" style="font-size: 0.72rem;">
          <i class="bi bi-shield-check text-success me-1"></i> Real-time Flight Monitoring • Crown Carz
        </span>
        <button type="button" class="btn btn-sm btn-secondary px-3" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
