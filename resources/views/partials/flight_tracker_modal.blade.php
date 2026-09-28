<!-- ✈️ Live Flight Tracker Modal (In-Page Same Screen Popup) -->
<div class="modal fade" id="flightTrackerModal" tabindex="-1" aria-labelledby="flightTrackerModalLabel" aria-hidden="true" style="z-index: 1099;">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden" style="background: #0f172a; color: #f8fafc;">
      
      <!-- Modal Header -->
      <div class="modal-header border-bottom border-secondary border-opacity-25 px-4 py-3" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
        <div class="d-flex align-items-center gap-3 w-100 pe-2">
          <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background: rgba(230, 176, 74, 0.15); border: 1px solid rgba(230, 176, 74, 0.35);">
            <i class="bi bi-airplane-engines text-warning fs-5"></i>
          </div>
          <div class="overflow-hidden">
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <h5 class="modal-title fw-bold text-white mb-0" id="ftm-flight-no">Flight BA327</h5>
              <span id="ftm-airline-badge" class="badge bg-warning text-dark fw-bold px-2 py-1" style="font-size: 0.75rem;">British Airways</span>
            </div>
            <div class="text-white-50 small mt-0.5" id="ftm-subtitle" style="font-size: 0.78rem;">Live Flight Tracker & Status Overview</div>
          </div>
          <div class="ms-auto d-flex align-items-center gap-2 flex-shrink-0">
            <a id="ftm-google-direct-btn" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1 d-none d-sm-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
              <i class="bi bi-google text-warning"></i> Open on Google <i class="bi bi-box-arrow-up-right ms-1 text-white-50" style="font-size: 0.7rem;"></i>
            </a>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
        </div>
      </div>

      <!-- Modal Body -->
      <div class="modal-body p-3 p-md-4" style="background: #0b1120;">
        
        <!-- Live Action Buttons Hub -->
        <div class="row g-2 mb-3">
          <div class="col-6 col-md-3">
            <a id="ftm-btn-google" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-dark w-100 py-2.5 px-2 border border-secondary border-opacity-50 rounded-3 text-center d-flex flex-column align-items-center justify-content-center transition-all" style="background: #1e293b;">
              <i class="bi bi-google text-warning fs-5 mb-1"></i>
              <span class="fw-bold text-white" style="font-size: 0.8rem;">Google Search</span>
              <span class="text-white-50" style="font-size: 0.68rem;">Live Arrival / Gate</span>
            </a>
          </div>
          <div class="col-6 col-md-3">
            <a id="ftm-btn-fr24" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-dark w-100 py-2.5 px-2 border border-secondary border-opacity-50 rounded-3 text-center d-flex flex-column align-items-center justify-content-center transition-all" style="background: #1e293b;">
              <i class="bi bi-radar text-info fs-5 mb-1"></i>
              <span class="fw-bold text-white" style="font-size: 0.8rem;">FlightRadar24</span>
              <span class="text-white-50" style="font-size: 0.68rem;">Live Radar GPS</span>
            </a>
          </div>
          <div class="col-6 col-md-3">
            <a id="ftm-btn-flightaware" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-dark w-100 py-2.5 px-2 border border-secondary border-opacity-50 rounded-3 text-center d-flex flex-column align-items-center justify-content-center transition-all" style="background: #1e293b;">
              <i class="bi bi-compass text-success fs-5 mb-1"></i>
              <span class="fw-bold text-white" style="font-size: 0.8rem;">FlightAware</span>
              <span class="text-white-50" style="font-size: 0.68rem;">Speed / Altitude</span>
            </a>
          </div>
          <div class="col-6 col-md-3">
            <a id="ftm-btn-airport" href="https://www.heathrow.com/arrivals" target="_blank" rel="noopener noreferrer" class="btn btn-dark w-100 py-2.5 px-2 border border-secondary border-opacity-50 rounded-3 text-center d-flex flex-column align-items-center justify-content-center transition-all" style="background: #1e293b;">
              <i class="bi bi-building text-primary fs-5 mb-1"></i>
              <span class="fw-bold text-white" id="ftm-airport-btn-title" style="font-size: 0.8rem;">Airport Arrivals</span>
              <span class="text-white-50" style="font-size: 0.68rem;">Terminal Board</span>
            </a>
          </div>
        </div>

        <!-- Embedded Live Radar / Flight Radar Frame -->
        <div class="card border-0 rounded-3 overflow-hidden shadow-lg mb-3" style="background: #1e293b;">
          <div class="card-header border-bottom border-secondary border-opacity-25 py-2 px-3 d-flex align-items-center justify-content-between text-white" style="background: rgba(15, 23, 42, 0.7);">
            <div class="d-flex align-items-center gap-2">
              <span class="spinner-grow spinner-grow-sm text-success" role="status"></span>
              <span class="fw-bold small" style="font-size: 0.82rem;">Live FlightRadar24 Radar</span>
            </div>
            <div class="d-flex align-items-center gap-2">
              <button id="ftm-reload-iframe" class="btn btn-xs btn-outline-secondary py-0 px-2 text-white-50" style="font-size: 0.72rem;" title="Reload Radar">
                <i class="bi bi-arrow-clockwise"></i> Refresh
              </button>
            </div>
          </div>
          <div class="card-body p-0 position-relative" style="height: 380px; background: #111827;">
            <iframe id="ftm-radar-iframe" src="" style="width: 100%; height: 100%; border: 0;" loading="lazy" allowfullscreen></iframe>
            <div id="ftm-radar-fallback" class="position-absolute top-0 start-0 w-100 h-100 d-none flex-column align-items-center justify-content-center p-4 text-center" style="background: #111827;">
              <i class="bi bi-airplane text-warning mb-2" style="font-size: 2.5rem;"></i>
              <h6 class="text-white fw-bold mb-1" id="ftm-fallback-title">Live Tracking Active</h6>
              <p class="text-white-50 small mb-3" style="max-width: 400px;">Click below to view the official real-time status with live terminal, gate, arrival schedule, and route map.</p>
              <div class="d-flex gap-2 flex-wrap justify-content-center">
                <a id="ftm-fallback-google-btn" href="#" target="_blank" class="btn btn-warning btn-sm px-3 fw-bold">
                  <i class="bi bi-google me-1"></i> Open Google Flight Status
                </a>
                <a id="ftm-fallback-fr24-btn" href="#" target="_blank" class="btn btn-outline-info btn-sm px-3">
                  <i class="bi bi-radar me-1"></i> Open FlightRadar24
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Copy & Quick Share Box -->
        <div class="p-3 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: rgba(30, 41, 59, 0.6); border: 1px solid rgba(255, 255, 255, 0.08);">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle-fill text-warning fs-5"></i>
            <div>
              <div class="fw-semibold text-white small">Flight Code: <span id="ftm-copy-code" class="text-warning font-monospace fw-bold">BA327</span></div>
              <div class="text-white-50" style="font-size: 0.72rem;">Click button to copy flight tracker link for driver / staff.</div>
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
