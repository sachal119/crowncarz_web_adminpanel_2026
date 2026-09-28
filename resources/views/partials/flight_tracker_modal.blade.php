<!-- ✈️ Google Live Flight Card Modal -->
<div class="modal fade" id="flightTrackerModal" tabindex="-1" aria-labelledby="flightTrackerModalLabel" aria-hidden="true" style="z-index: 1099;">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 overflow-hidden shadow-2xl" style="background: #202124; color: #e8eaed; font-family: -apple-system, BlinkMacSystemFont, 'Google Sans', 'Roboto', 'Segoe UI', sans-serif;">
      
      <!-- Top Modal Bar -->
      <div class="d-flex align-items-center justify-content-between px-4 pt-3 pb-2" style="background: #202124; border-bottom: 1px solid #3c4043;">
        <div class="d-flex align-items-center gap-2">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="text-primary">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z" fill="#8ab4f8"/>
          </svg>
          <span class="text-white-50 small fw-medium" style="font-size: 0.8rem; letter-spacing: 0.3px;">Live Flight Tracker</span>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Body (Exact Google Flight Card UI) -->
      <div class="modal-body p-4 pt-3" style="background: #202124;">
        
        <!-- 🔄 Loading State (Shows while API is fetching) -->
        <div id="ftm-loader" class="text-center py-5">
          <div class="spinner-border text-primary" role="status" style="width: 2.8rem; height: 2.8rem; border-width: 3px; color: #8ab4f8 !important;">
            <span class="visually-hidden">Loading flight details...</span>
          </div>
          <div class="mt-3 text-white-50 small font-monospace" id="ftm-loader-text">
            Fetching live flight data...
          </div>
        </div>

        <!-- ❌ Error / Not Found State -->
        <div id="ftm-error" class="text-center py-4 d-none">
          <div class="mb-3">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="text-warning">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="#f2994a"/>
            </svg>
          </div>
          <h5 class="text-white fw-normal mb-1">No Live Flight Details Found</h5>
          <p class="text-white-50 small mb-3" id="ftm-error-msg">
            Live telemetry is currently unavailable for this flight number.
          </p>
          <a id="ftm-err-google-btn" href="#" target="_blank" class="btn btn-sm btn-outline-light px-3 py-1.5 rounded-pill" style="font-size: 0.82rem; border-color: #5f6368;">
            Search on Google
          </a>
        </div>

        <!-- ✈️ Content Container (Populated ONLY when API returns data) -->
        <div id="ftm-content" class="d-none">
          
          <!-- Header Title: Airline & Flight No -->
          <div class="mb-3">
            <h2 class="fw-normal text-white mb-0" id="ftm-google-title" style="font-size: 1.55rem; letter-spacing: -0.2px;">
              -
            </h2>
            <div class="text-secondary mt-0.5" id="ftm-google-subtitle" style="color: #bdc1c6 !important; font-size: 0.95rem;">
              -
            </div>
          </div>

          <!-- Summary Bar (Time / Destination / Status) -->
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 px-1">
            <div class="d-flex align-items-baseline gap-2">
              <span class="fw-bold text-white fs-5" id="ftm-summary-time">-</span>
              <span class="small font-monospace" style="color: #9aa0a6;" id="ftm-summary-code">-</span>
            </div>
            <div class="d-flex align-items-center gap-2">
              <span class="small" style="color: #bdc1c6;">to <strong class="text-white" id="ftm-summary-dest">-</strong></span>
              <span id="ftm-google-status-pill" class="badge text-uppercase fw-semibold" style="border: 1px solid #81c995; color: #81c995; background: rgba(129, 201, 149, 0.12); font-size: 0.72rem; padding: 4px 10px; border-radius: 4px; letter-spacing: 0.5px;">
                ON TIME
              </span>
            </div>
          </div>

          <!-- 🌟 MAIN GOOGLE FLIGHT CARD BOX 🌟 -->
          <div class="rounded-4 p-4 mb-3" style="background: #303134; border: 1px solid #3c4043;">
            
            <!-- Route Visualizer (ORIGIN ─── ✈ ─── DEST) -->
            <div class="d-flex align-items-center justify-content-between mb-4">
              <!-- Origin Airport -->
              <div class="text-start" style="min-width: 90px;">
                <div class="fw-bold text-white font-monospace" style="font-size: 2.2rem; line-height: 1;" id="ftm-g-origin-code">-</div>
                <a href="#" target="_blank" id="ftm-origin-link" class="small text-decoration-none mt-1 d-inline-block" style="color: #8ab4f8; font-size: 0.8rem;">Airport info</a>
              </div>

              <!-- Flight Progress Line with Plane Icon & Duration -->
              <div class="flex-grow-1 mx-3 position-relative text-center">
                <div class="text-center small mb-1" style="color: #9aa0a6; font-size: 0.8rem;" id="ftm-g-duration">Direct Flight</div>
                <div class="d-flex align-items-center position-relative" style="height: 18px;">
                  <div style="height: 1px; width: 100%; background: #5f6368;"></div>
                  <div class="position-absolute start-50 translate-middle-x bg-transparent px-2" style="color: #81c995;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="transform: rotate(90deg);">
                      <path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/>
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Destination Airport -->
              <div class="text-end" style="min-width: 90px;">
                <div class="fw-bold text-white font-monospace" style="font-size: 2.2rem; line-height: 1;" id="ftm-g-dest-code">-</div>
                <a href="https://www.heathrow.com/arrivals" target="_blank" id="ftm-dest-link" class="small text-decoration-none mt-1 d-inline-block" style="color: #8ab4f8; font-size: 0.8rem;">Airport info</a>
              </div>
            </div>

            <!-- Departure & Arrival Grid (2 Columns) -->
            <div class="row g-4 pt-2 border-top" style="border-color: #3c4043 !important;">
              
              <!-- Left: Departure Details -->
              <div class="col-12 col-md-6">
                <div class="text-white fw-medium mb-2" style="font-size: 0.92rem;" id="ftm-g-dep-header">
                  Departure
                </div>
                <div class="row g-2 align-items-baseline">
                  <div class="col-6">
                    <div class="small" style="color: #9aa0a6; font-size: 0.76rem;">Scheduled departure</div>
                    <div class="fw-bold text-white mt-1" style="font-size: 1.35rem; color: #81c995 !important;" id="ftm-g-dep-time">-</div>
                  </div>
                  <div class="col-3 text-center">
                    <div class="small" style="color: #9aa0a6; font-size: 0.76rem;">Terminal</div>
                    <div class="fw-bold text-white mt-1" style="font-size: 1.15rem;" id="ftm-g-dep-terminal">-</div>
                  </div>
                  <div class="col-3 text-center">
                    <div class="small" style="color: #9aa0a6; font-size: 0.76rem;">Gate</div>
                    <div class="fw-bold text-white mt-1" style="font-size: 1.15rem;" id="ftm-g-dep-gate">-</div>
                  </div>
                </div>
              </div>

              <!-- Right: Arrival Details -->
              <div class="col-12 col-md-6 border-start-md" style="border-color: #3c4043 !important;">
                <div class="text-white fw-medium mb-2" style="font-size: 0.92rem;" id="ftm-g-arr-header">
                  Arrival
                </div>
                <div class="row g-2 align-items-baseline">
                  <div class="col-6">
                    <div class="small" style="color: #9aa0a6; font-size: 0.76rem;">Scheduled arrival</div>
                    <div class="fw-bold text-white mt-1" style="font-size: 1.35rem; color: #81c995 !important;" id="ftm-g-arr-time">-</div>
                  </div>
                  <div class="col-3 text-center">
                    <div class="small" style="color: #9aa0a6; font-size: 0.76rem;">Terminal</div>
                    <div class="fw-bold text-white mt-1" style="font-size: 1.15rem;" id="ftm-g-arr-terminal">-</div>
                  </div>
                  <div class="col-3 text-center">
                    <div class="small" style="color: #9aa0a6; font-size: 0.76rem;">Gate</div>
                    <div class="fw-bold text-white mt-1" style="font-size: 1.15rem;" id="ftm-g-arr-gate">-</div>
                  </div>
                </div>
              </div>

            </div>

            <!-- Bottom Footer of Google Card -->
            <div class="d-flex align-items-center justify-content-between pt-3 mt-3 border-top" style="border-color: #3c4043 !important;">
              <div class="small" style="color: #9aa0a6; font-size: 0.75rem;" id="ftm-g-source">
                Live Aviation Telemetry • Source: AviationStack Live
              </div>
              <a id="ftm-btn-open-google" href="#" target="_blank" class="text-decoration-none d-flex align-items-center gap-1" style="color: #8ab4f8; font-size: 0.8rem;">
                <span>Open on Google</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/>
                </svg>
              </a>
            </div>

          </div>

          <!-- Booking Context Strip (Passenger & Pickup Details with generous internal padding) -->
          <div id="ftm-booking-context" class="p-3 px-4 rounded-3 d-none align-items-center justify-content-between flex-wrap gap-2" style="background: #2a2b2e; border: 1px solid #3c4043;">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-person-fill text-warning fs-5"></i>
              <div class="small">
                <span style="color: #9aa0a6;">Passenger:</span> <strong class="text-white" id="ftm-passenger-name">-</strong>
                <span style="color: #5f6368;" class="mx-2">•</span>
                <span style="color: #9aa0a6;">Pickup:</span> <strong class="text-warning" id="ftm-pickup-time">-</strong>
              </div>
            </div>
            <div class="small text-truncate" style="max-width: 320px; color: #bdc1c6;" id="ftm-route-text">-</div>
          </div>

        </div>

      </div>

    </div>
  </div>
</div>


