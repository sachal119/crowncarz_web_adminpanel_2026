@extends('layouts.app')

@section('content')
<style>
/* Fullscreen Map Layout */
.live-map-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
    min-height: calc(100vh - 65px);
    overflow: hidden;
    margin: 0;
    padding: 0;
}

#liveGoogleMap {
    width: 100%;
    height: 100%;
    min-height: calc(100vh - 65px);
    background-color: #e5e3df;
}

/* Floating Top Controls Header */
.map-floating-topbar {
    position: absolute;
    top: 14px;
    left: 14px;
    right: 14px;
    z-index: 1020;
    pointer-events: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.map-floating-topbar > * {
    pointer-events: auto;
}

.glass-panel {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border: 1px solid rgba(255, 255, 255, 0.7);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    border-radius: 14px;
}

/* Radar Badge */
.radar-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    font-weight: 700;
    font-size: 13.5px;
    color: #1a1a1a;
}

.radar-dot {
    width: 10px;
    height: 10px;
    background-color: #10b981;
    border-radius: 50%;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: radarPulse 1.8s infinite;
}

@keyframes radarPulse {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    }
    70% {
        transform: scale(1.1);
        box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
    }
    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
    }
}

/* Status Filter Pills */
.filter-pill-group {
    display: flex;
    gap: 6px;
    padding: 6px 10px;
}

.filter-pill {
    background: transparent;
    border: 1px solid transparent;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: #495057;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.filter-pill:hover {
    background: rgba(0, 0, 0, 0.05);
}

.filter-pill.active {
    background: #1e293b;
    color: #ffffff;
}

.filter-pill .pill-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
}

.pill-dot.dot-all { background: #64748b; }
.pill-dot.dot-available { background: #10b981; }
.pill-dot.dot-engaged { background: #f43f5e; }
.pill-dot.dot-waiting { background: #f59e0b; }
.pill-dot.dot-break { background: #64748b; }

/* Search Box */
.map-search-box {
    position: relative;
    width: 220px;
}

.map-search-box input {
    border-radius: 20px;
    padding-left: 34px;
    font-size: 12.5px;
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid #dee2e6;
}

.map-search-box i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
    font-size: 13px;
}

/* ========================================================================= */
/* 📍 SNAPCHAT / HEATMAP STYLE AVATAR PINS                                   */
/* ========================================================================= */
.snap-marker-container {
    position: absolute;
    cursor: pointer;
    transform: translate(-50%, -100%);
    transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), z-index 0.2s;
    user-select: none;
    z-index: 100;
}

.snap-marker-container:hover {
    transform: translate(-50%, -108%) scale(1.15);
    z-index: 9999 !important;
}

.snap-marker-container.selected {
    transform: translate(-50%, -110%) scale(1.22);
    z-index: 10000 !important;
}

.snap-pin-wrapper {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Call Sign Floating Mini Badge */
.snap-callsign-badge {
    background: #1e293b;
    color: #ffffff;
    font-size: 9.5px;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 10px;
    letter-spacing: 0.4px;
    margin-bottom: 3px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
    white-space: nowrap;
    border: 1px solid rgba(255, 255, 255, 0.3);
    text-transform: uppercase;
}

/* Avatar Circular Bubble */
.snap-avatar-bubble {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 19px;
    font-weight: 900;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
    position: relative;
    border: 3px solid #ffffff;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
}

/* Status Ring Themes & Gradients */
/* Available (Green / Emerald) */
.snap-status-available .snap-avatar-bubble {
    background: linear-gradient(135deg, #10b981, #059669);
    border-color: #34d399;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.35), 0 6px 16px rgba(16, 185, 129, 0.4);
}
.snap-status-available .snap-callsign-badge {
    background: #065f46;
}

/* Engaged / On Job (Red / Rose) */
.snap-status-engaged .snap-avatar-bubble {
    background: linear-gradient(135deg, #f43f5e, #e11d48);
    border-color: #fb7185;
    box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.35), 0 6px 16px rgba(244, 63, 94, 0.4);
}
.snap-status-engaged .snap-callsign-badge {
    background: #881337;
}

/* Waiting (Amber / Gold) */
.snap-status-waiting .snap-avatar-bubble {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border-color: #fcd34d;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.35), 0 6px 16px rgba(245, 158, 11, 0.4);
}
.snap-status-waiting .snap-callsign-badge {
    background: #78350f;
}

/* On Break (Slate / Grey) */
.snap-status-on-break .snap-avatar-bubble,
.snap-status-offline .snap-avatar-bubble {
    background: linear-gradient(135deg, #64748b, #475569);
    border-color: #94a3b8;
    box-shadow: 0 0 0 2px rgba(100, 116, 139, 0.3), 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Pin Pointer Tip underneath */
.snap-pin-tip {
    width: 0;
    height: 0;
    border-left: 7px solid transparent;
    border-right: 7px solid transparent;
    border-top: 8px solid #ffffff;
    margin-top: -2px;
    filter: drop-shadow(0 2px 3px rgba(0, 0, 0, 0.2));
}

.snap-status-available .snap-pin-tip { border-top-color: #34d399; }
.snap-status-engaged .snap-pin-tip { border-top-color: #fb7185; }
.snap-status-waiting .snap-pin-tip { border-top-color: #fcd34d; }
.snap-status-on-break .snap-pin-tip,
.snap-status-offline .snap-pin-tip { border-top-color: #94a3b8; }

/* Radar ripple pulse */
.snap-pulse-ring {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    transform: translate(-50%, -50%);
    pointer-events: none;
    animation: snapPulse 2s infinite cubic-bezier(0.24, 0, 0.38, 1);
}

.snap-status-available .snap-pulse-ring {
    border: 2.5px solid #10b981;
}
.snap-status-engaged .snap-pulse-ring {
    border: 2.5px solid #f43f5e;
}

@keyframes snapPulse {
    0% {
        width: 44px;
        height: 44px;
        opacity: 0.9;
    }
    100% {
        width: 78px;
        height: 78px;
        opacity: 0;
    }
}

/* Floating Driver List Sidebar */
.driver-sidebar {
    position: absolute;
    top: 72px;
    left: 14px;
    bottom: 20px;
    width: 320px;
    z-index: 1025;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.driver-sidebar.collapsed {
    transform: translateX(-340px);
    opacity: 0;
    pointer-events: none;
}

.driver-sidebar-header {
    padding: 14px 16px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.driver-list-scroll {
    overflow-y: auto;
    flex: 1;
    padding: 8px;
}

.driver-card-item {
    background: #ffffff;
    border: 1px solid #eaedf1;
    border-radius: 10px;
    padding: 10px 12px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.driver-card-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border-color: #B87333;
}

.driver-card-item.selected {
    border-color: #B87333;
    background: #fffdf8;
    box-shadow: 0 0 0 2px rgba(184, 115, 51, 0.25);
}

.driver-badge-sign {
    background: #212529;
    color: #f8f9fa;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
}

.status-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 12px;
    text-transform: uppercase;
}

.status-available { background: #d1e7dd; color: #0f5132; }
.status-engaged { background: #f8d7da; color: #842029; }
.status-waiting { background: #fff3cd; color: #664d03; }
.status-on-break { background: #e2e3e5; color: #41464b; }
.status-offline { background: #f8f9fa; color: #6c757d; border: 1px solid #dee2e6; }

/* Driver Details Drawer (Slide-in) */
.driver-drawer {
    position: absolute;
    top: 14px;
    right: 14px;
    bottom: 20px;
    width: 380px;
    max-width: calc(100vw - 28px);
    z-index: 1030;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform: translateX(420px);
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

.driver-drawer.open {
    transform: translateX(0);
}

.drawer-header {
    padding: 16px 20px;
    background: linear-gradient(135deg, #1e293b, #334155);
    color: #ffffff;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.drawer-body {
    padding: 18px 20px;
    overflow-y: auto;
    flex: 1;
}

.drawer-avatar {
    width: 54px;
    height: 54px;
    background: linear-gradient(135deg, #B87333, #d48b48);
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    font-weight: 900;
    border: 3px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

/* UK Reg Plate UI */
.uk-number-plate {
    display: inline-block;
    background: #fcd116;
    color: #111;
    font-family: 'Charles Wright', 'Arial Black', Impact, sans-serif;
    font-weight: 900;
    font-size: 13px;
    padding: 3px 10px;
    border-radius: 4px;
    border: 1.5px solid #222;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.6), 0 2px 4px rgba(0,0,0,0.15);
}

.detail-section-title {
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
    margin-top: 16px;
}

.detail-card {
    background: #f8f9fa;
    border: 1px solid #edf2f7;
    border-radius: 10px;
    padding: 12px 14px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px 0;
    font-size: 12.5px;
}

.detail-row:not(:last-child) {
    border-bottom: 1px dashed #e9ecef;
}

.detail-row .label {
    color: #64748b;
}

.detail-row .value {
    font-weight: 600;
    color: #1e293b;
}

.action-btn-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    text-decoration: none;
    font-size: 15px;
    transition: transform 0.2s;
}

.action-btn-circle:hover {
    transform: scale(1.08);
    color: #fff;
}

@media (max-width: 768px) {
    .driver-sidebar {
        width: 280px;
    }
    .driver-drawer {
        width: 100%;
        top: 0;
        bottom: 0;
        right: 0;
        border-radius: 0;
    }
    .map-search-box {
        width: 100%;
    }
}
</style>

<div class="live-map-wrapper">
    <!-- Map Canvas -->
    <div id="liveGoogleMap"></div>

    <!-- Floating Top Bar -->
    <div class="map-floating-topbar">
        <!-- Live Indicator & Filter Pills -->
        <div class="glass-panel d-flex align-items-center flex-wrap">
            <div class="radar-badge">
                <span class="radar-dot"></span>
                <span>Live Radar</span>
                <span class="badge bg-dark rounded-pill ms-1" id="activeDriverCountBadge">0</span>
            </div>
            <div class="vr my-2 d-none d-md-block" style="opacity: 0.15;"></div>
            <div class="filter-pill-group">
                <button class="filter-pill active" data-filter="all">
                    <span class="pill-dot dot-all"></span> All (<span id="countAll">0</span>)
                </button>
                <button class="filter-pill" data-filter="available">
                    <span class="pill-dot dot-available"></span> Available (<span id="countAvailable">0</span>)
                </button>
                <button class="filter-pill" data-filter="engaged">
                    <span class="pill-dot dot-engaged"></span> Engaged (<span id="countEngaged">0</span>)
                </button>
                <button class="filter-pill" data-filter="waiting">
                    <span class="pill-dot dot-waiting"></span> Waiting (<span id="countWaiting">0</span>)
                </button>
                <button class="filter-pill" data-filter="on-break">
                    <span class="pill-dot dot-break"></span> Break (<span id="countBreak">0</span>)
                </button>
            </div>
        </div>

        <!-- Search Box & Tools -->
        <div class="d-flex align-items-center gap-2">
            <div class="glass-panel p-1">
                <div class="map-search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control form-control-sm border-0" id="driverSearchInput" placeholder="Search call sign, name...">
                </div>
            </div>

            <div class="glass-panel p-1 d-flex gap-1">
                <button class="btn btn-sm btn-light border-0 shadow-none px-2" id="toggleSidebarBtn" title="Toggle Driver List">
                    <i class="bi bi-list-ul fs-6"></i>
                </button>
                <button class="btn btn-sm btn-light border-0 shadow-none px-2" id="fitBoundsBtn" title="Fit All Drivers">
                    <i class="bi bi-arrows-fullscreen fs-6"></i>
                </button>
                <button class="btn btn-sm btn-light border-0 shadow-none px-2" id="trafficToggleBtn" title="Toggle Live Traffic">
                    <i class="bi bi-cone-striped fs-6"></i>
                </button>
                <button class="btn btn-sm btn-light border-0 shadow-none px-2" id="mapStyleToggleBtn" title="Toggle Map Style (Road / Satellite)">
                    <i class="bi bi-layers-fill fs-6"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Floating Driver Sidebar (Left) -->
    <div class="driver-sidebar glass-panel" id="driverSidebar">
        <div class="driver-sidebar-header">
            <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                <i class="bi bi-person-badge-fill text-warning"></i> Active Drivers
            </div>
            <button class="btn btn-sm btn-close text-reset shadow-none" id="closeSidebarBtn"></button>
        </div>
        <div class="driver-list-scroll" id="driverCardsList">
            <!-- Populated immediately on DOM load -->
        </div>
    </div>

    <!-- Slide-in Driver Details Drawer (Right) -->
    <div class="driver-drawer glass-panel" id="driverDrawer">
        <div class="drawer-header">
            <div class="d-flex align-items-center gap-3">
                <div class="drawer-avatar" id="drawerAvatarText">D</div>
                <div>
                    <h6 class="mb-0 fw-bold" id="drawerDriverName">Driver Name</h6>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="driver-badge-sign" id="drawerCallSign">D-000</span>
                        <span class="status-badge status-available" id="drawerStatusBadge">Available</span>
                    </div>
                </div>
            </div>
            <button class="btn-close btn-close-white shadow-none" id="closeDrawerBtn"></button>
        </div>

        <div class="drawer-body">
            <!-- Quick Contact Buttons -->
            <div class="d-flex justify-content-around align-items-center py-2 mb-3 bg-white rounded-3 border">
                <a href="#" class="action-btn-circle bg-success shadow-sm" id="drawerCallBtn" title="Call Driver">
                    <i class="bi bi-telephone-fill"></i>
                </a>
                <a href="#" class="action-btn-circle bg-info shadow-sm" id="drawerSmsBtn" title="Send SMS">
                    <i class="bi bi-chat-text-fill"></i>
                </a>
                <button class="action-btn-circle bg-dark shadow-sm border-0" id="drawerFocusMapBtn" title="Center on Map">
                    <i class="bi bi-crosshair"></i>
                </button>
                <button class="action-btn-circle bg-warning text-dark shadow-sm border-0" id="drawerFollowToggleBtn" title="Follow Live GPS">
                    <i class="bi bi-camera-video-fill"></i>
                </button>
            </div>

            <!-- Vehicle Information -->
            <div class="detail-section-title">
                <i class="bi bi-car-front-fill me-1"></i> Vehicle Information
            </div>
            <div class="detail-card mb-3">
                <div class="detail-row">
                    <span class="label">Registration Plate</span>
                    <span class="value">
                        <span class="uk-number-plate" id="drawerRegPlate">NO PLATE</span>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="label">Make & Model</span>
                    <span class="value" id="drawerVehicleModel">-</span>
                </div>
                <div class="detail-row">
                    <span class="label">Color / Type</span>
                    <span class="value" id="drawerVehicleColor">-</span>
                </div>
            </div>

            <!-- Live Telemetry & GPS -->
            <div class="detail-section-title">
                <i class="bi bi-broadcast-pin me-1"></i> Live Location & Telemetry
            </div>
            <div class="detail-card mb-3">
                <div class="detail-row">
                    <span class="label">GPS Coordinates</span>
                    <span class="value font-monospace small" id="drawerCoords">0.0000, 0.0000</span>
                </div>
                <div class="detail-row">
                    <span class="label">Speed</span>
                    <span class="value" id="drawerSpeed">0 mph</span>
                </div>
                <div class="detail-row">
                    <span class="label">Last Location Ping</span>
                    <span class="value text-success" id="drawerLastUpdate">Live Connected</span>
                </div>
                <div class="detail-row">
                    <span class="label">Base Address</span>
                    <span class="value small text-truncate" style="max-width: 180px;" id="drawerAddress">-</span>
                </div>
            </div>

            <!-- Driver Contact Info -->
            <div class="detail-section-title">
                <i class="bi bi-person-lines-fill me-1"></i> Driver Contact
            </div>
            <div class="detail-card mb-3">
                <div class="detail-row">
                    <span class="label">Phone Number</span>
                    <span class="value" id="drawerPhone">-</span>
                </div>
                <div class="detail-row">
                    <span class="label">Email Address</span>
                    <span class="value small" id="drawerEmail">-</span>
                </div>
                <div class="detail-row">
                    <span class="label">Brought Forward (BF)</span>
                    <span class="value text-primary" id="drawerBF">£0.00</span>
                </div>
            </div>

            <!-- Quick Management Actions -->
            <div class="d-grid gap-2 mt-4">
                <a href="{{ route('booking.create') }}" target="_blank" class="btn btn-warning text-dark fw-bold" id="drawerAssignBookingBtn">
                    <i class="bi bi-calendar-plus me-1"></i> Create Booking with Driver
                </a>
                <a href="#" class="btn btn-outline-secondary btn-sm" id="drawerBfHistoryBtn" target="_blank">
                    <i class="bi bi-clock-history me-1"></i> View Driver BF History
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Firebase SDK -->
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>

<!-- Google Maps JS API -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initLiveMapPage&loading=async"></script>

<script>
// Global State
let map = null;
let trafficLayer = null;
let isTrafficActive = false;
let currentMapTypeId = 'roadmap';
let overlayMarkers = {}; // id -> CustomDriverOverlay
let driversState = {}; // id -> driver object
let vehiclesState = {}; // driver_id -> vehicle object
let activeFilter = 'all';
let searchQuery = '';
let selectedDriverId = null;
let isFollowingDriver = false;

// Initial SSR Drivers
const initialDrivers = @json($drivers);
if (Array.isArray(initialDrivers)) {
    initialDrivers.forEach(d => {
        if (d && d.id) {
            driversState[d.id] = d;
        }
    });
}

// 📍 Snapchat / Heatmap Style Custom Overlay Marker Class
class CustomDriverOverlay {
    constructor(driver, mapInstance, clickCallback) {
        this.driver = driver;
        this.map = mapInstance;
        this.lat = parseFloat(driver.latitude);
        this.lng = parseFloat(driver.longitude);
        this.clickCallback = clickCallback;
        this.div = null;

        if (window.google && google.maps && google.maps.OverlayView) {
            this.overlay = new google.maps.OverlayView();
            this.overlay.onAdd = () => this.onAdd();
            this.overlay.draw = () => this.draw();
            this.overlay.onRemove = () => this.onRemove();
            this.overlay.setMap(mapInstance);
        }
    }

    onAdd() {
        this.div = document.createElement('div');
        this.div.className = 'snap-marker-container';
        this.updateContent();

        // Marker Click Event
        this.div.addEventListener('click', (e) => {
            e.stopPropagation();
            if (this.clickCallback) {
                this.clickCallback(this.driver.id);
            }
        });

        const panes = this.overlay.getPanes();
        if (panes && panes.overlayMouseTarget) {
            panes.overlayMouseTarget.appendChild(this.div);
        }
    }

    draw() {
        if (!this.div || !this.overlay) return;
        const projection = this.overlay.getProjection();
        if (!projection) return;

        const pos = new google.maps.LatLng(this.lat, this.lng);
        const point = projection.fromLatLngToDivPixel(pos);

        if (point) {
            this.div.style.left = point.x + 'px';
            this.div.style.top = point.y + 'px';
        }
    }

    updateDriver(driver) {
        this.driver = driver;
        this.lat = parseFloat(driver.latitude);
        this.lng = parseFloat(driver.longitude);
        if (this.div) {
            this.updateContent();
        }
        this.draw();
    }

    updateContent() {
        if (!this.div) return;
        const d = this.driver;
        const status = normalizeStatus(d.status);
        const letter = (d.name || 'D').trim().charAt(0).toUpperCase();
        const callSign = d.call_sign || 'D-00';
        const isSelected = selectedDriverId === d.id;

        let statusClass = 'snap-status-offline';
        if (status === 'available') statusClass = 'snap-status-available';
        else if (status === 'engaged' || status === 'on_job') statusClass = 'snap-status-engaged';
        else if (status === 'waiting') statusClass = 'snap-status-waiting';
        else if (status === 'on_break') statusClass = 'snap-status-on-break';

        this.div.className = `snap-marker-container ${statusClass} ${isSelected ? 'selected' : ''}`;
        this.div.innerHTML = `
            <div class="snap-pin-wrapper">
                <div class="snap-callsign-badge">${callSign}</div>
                <div class="snap-avatar-bubble">
                    <div class="snap-pulse-ring"></div>
                    <span>${letter}</span>
                </div>
                <div class="snap-pin-tip"></div>
            </div>
        `;
    }

    onRemove() {
        if (this.div && this.div.parentNode) {
            this.div.parentNode.removeChild(this.div);
            this.div = null;
        }
    }

    setVisible(visible) {
        if (this.div) {
            this.div.style.display = visible ? 'block' : 'none';
        }
    }

    getPosition() {
        return new google.maps.LatLng(this.lat, this.lng);
    }
}

// 🗺️ Initialize Google Map
function initLiveMapPage() {
    const ukCenter = { lat: 51.4543, lng: -0.9781 }; // Reading / London Hub

    map = new google.maps.Map(document.getElementById("liveGoogleMap"), {
        center: ukCenter,
        zoom: 12,
        minZoom: 6,
        maxZoom: 20,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        mapTypeControl: false,
        fullscreenControl: false,
        streetViewControl: false,
        zoomControl: true,
        zoomControlOptions: {
            position: google.maps.ControlPosition.RIGHT_BOTTOM
        },
        styles: [
            { featureType: "poi", elementType: "labels", stylers: [{ visibility: "off" }] },
            { featureType: "transit", elementType: "labels", stylers: [{ visibility: "off" }] }
        ]
    });

    trafficLayer = new google.maps.TrafficLayer();

    // Render markers on map
    renderAllDrivers();
}

window.initLiveMapPage = initLiveMapPage;

// 🔥 Firebase Real-time Listener
function initFirebaseLiveStream() {
    const firebaseConfig = {
        apiKey: "AIzaSyDf9Ujb0fAaE7CWAtkb5qCUJn5RDsRvPAQ",
        authDomain: "crown-carz.firebaseapp.com",
        databaseURL: "https://crown-carz-default-rtdb.firebaseio.com",
        projectId: "crown-carz",
        storageBucket: "crown-carz.firebasestorage.app",
        messagingSenderId: "854843072109",
        appId: "1:854843072109:web:dd5ff34f1ed03521d5a964",
        measurementId: "G-VCG3S5E63H"
    };

    if (!firebase.apps.length) {
        firebase.initializeApp(firebaseConfig);
    }

    const db = firebase.database();

    // 1️⃣ Listen for Vehicles
    db.ref("vehicles").on("value", (snapshot) => {
        const vehicles = snapshot.val();
        if (vehicles) {
            for (const vKey in vehicles) {
                const v = vehicles[vKey];
                if (v && v.driver_id) {
                    vehiclesState[v.driver_id] = v;
                    if (driversState[v.driver_id]) {
                        driversState[v.driver_id].vehicle_make = v.make || driversState[v.driver_id].vehicle_make;
                        driversState[v.driver_id].vehicle_model = v.model || driversState[v.driver_id].vehicle_model;
                        driversState[v.driver_id].vehicle_reg = v.registration || v.plate || driversState[v.driver_id].vehicle_reg;
                        driversState[v.driver_id].vehicle_color = v.color || driversState[v.driver_id].vehicle_color;
                        driversState[v.driver_id].vehicle_type = v.type || driversState[v.driver_id].vehicle_type;
                    }
                }
            }
            if (selectedDriverId && driversState[selectedDriverId]) {
                updateDrawerData(driversState[selectedDriverId]);
            }
            renderSidebarList();
        }
    });

    // 2️⃣ Listen for Live Drivers (Coordinates, status, bearing, speed)
    db.ref("drivers").on("value", (snapshot) => {
        const drivers = snapshot.val();
        if (!drivers) return;

        for (const id in drivers) {
            const d = drivers[id];
            const existing = driversState[id] || {};
            driversState[id] = {
                ...existing,
                ...d,
                id: id
            };

            // Merge vehicle if cached
            if (vehiclesState[id]) {
                const v = vehiclesState[id];
                driversState[id].vehicle_make = v.make || driversState[id].vehicle_make;
                driversState[id].vehicle_model = v.model || driversState[id].vehicle_model;
                driversState[id].vehicle_reg = v.registration || v.plate || driversState[id].vehicle_reg;
                driversState[id].vehicle_color = v.color || driversState[id].vehicle_color;
                driversState[id].vehicle_type = v.type || driversState[id].vehicle_type;
            }
        }

        renderAllDrivers();

        // If a driver is currently selected and we are in "follow" mode, follow smoothly
        if (selectedDriverId && driversState[selectedDriverId]) {
            const currentSelected = driversState[selectedDriverId];
            updateDrawerData(currentSelected);

            if (isFollowingDriver && currentSelected.latitude && currentSelected.longitude && map) {
                const pos = new google.maps.LatLng(
                    parseFloat(currentSelected.latitude),
                    parseFloat(currentSelected.longitude)
                );
                map.panTo(pos);
            }
        }
    });
}

// 🚗 Render or Update Driver Markers on Map & Counts
function renderAllDrivers() {
    updateCounts();
    renderSidebarList();

    if (!map) return;

    const visibleDriverIds = new Set();
    const bounds = new google.maps.LatLngBounds();

    for (const id in driversState) {
        const driver = driversState[id];

        // Filter condition
        if (!matchesFilter(driver, activeFilter, searchQuery)) {
            if (overlayMarkers[id]) {
                overlayMarkers[id].setVisible(false);
            }
            continue;
        }

        const lat = parseFloat(driver.latitude);
        const lng = parseFloat(driver.longitude);

        if (isNaN(lat) || isNaN(lng) || (lat === 0 && lng === 0)) {
            if (overlayMarkers[id]) {
                overlayMarkers[id].setVisible(false);
            }
            continue;
        }

        const pos = { lat, lng };
        visibleDriverIds.add(id);
        bounds.extend(pos);

        if (overlayMarkers[id]) {
            // Update existing custom overlay marker
            overlayMarkers[id].updateDriver(driver);
            overlayMarkers[id].setVisible(true);
        } else {
            // Create new Snapchat-style custom overlay marker
            const overlay = new CustomDriverOverlay(driver, map, (clickedId) => {
                selectDriver(clickedId, true);
            });
            overlayMarkers[id] = overlay;
        }
    }

    // Hide overlay markers that shouldn't be visible
    for (const id in overlayMarkers) {
        if (!visibleDriverIds.has(id)) {
            overlayMarkers[id].setVisible(false);
        }
    }
}

// 🔢 Update Counts Badges
function updateCounts() {
    let totalCount = 0;
    let availableCount = 0;
    let engagedCount = 0;
    let waitingCount = 0;
    let breakCount = 0;
    let validGpsCount = 0;

    for (const id in driversState) {
        const driver = driversState[id];
        const status = normalizeStatus(driver.status);
        totalCount++;

        if (status === 'available') availableCount++;
        else if (status === 'engaged' || status === 'on_job') engagedCount++;
        else if (status === 'waiting') waitingCount++;
        else if (status === 'on_break' || status === 'on break') breakCount++;

        const lat = parseFloat(driver.latitude);
        const lng = parseFloat(driver.longitude);
        if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
            validGpsCount++;
        }
    }

    const badgeEl = document.getElementById('activeDriverCountBadge');
    if (badgeEl) badgeEl.textContent = validGpsCount;

    const countAllEl = document.getElementById('countAll');
    if (countAllEl) countAllEl.textContent = totalCount;

    const countAvailEl = document.getElementById('countAvailable');
    if (countAvailEl) countAvailEl.textContent = availableCount;

    const countEngagedEl = document.getElementById('countEngaged');
    if (countEngagedEl) countEngagedEl.textContent = engagedCount;

    const countWaitingEl = document.getElementById('countWaiting');
    if (countWaitingEl) countWaitingEl.textContent = waitingCount;

    const countBreakEl = document.getElementById('countBreak');
    if (countBreakEl) countBreakEl.textContent = breakCount;
}

// 🔍 Filter & Search Helpers
function normalizeStatus(rawStatus) {
    if (!rawStatus) return 'offline';
    const s = rawStatus.toString().toLowerCase().trim().replace(/[\s-]/g, '_');
    if (s === 'available') return 'available';
    if (s === 'engaged' || s === 'on_job' || s === 'on_trip') return 'engaged';
    if (s === 'waiting') return 'waiting';
    if (s === 'on_break' || s === 'on break') return 'on_break';
    return s;
}

function matchesFilter(driver, filter, search) {
    const status = normalizeStatus(driver.status);

    if (filter === 'available' && status !== 'available') return false;
    if (filter === 'engaged' && (status !== 'engaged' && status !== 'on_job')) return false;
    if (filter === 'waiting' && status !== 'waiting') return false;
    if (filter === 'on-break' && (status !== 'on_break' && status !== 'on break')) return false;

    if (search) {
        const q = search.toLowerCase();
        const name = (driver.name || '').toLowerCase();
        const callSign = (driver.call_sign || '').toLowerCase();
        const phone = (driver.phone || '').toLowerCase();
        const reg = (driver.vehicle_reg || '').toLowerCase();

        if (!name.includes(q) && !callSign.includes(q) && !phone.includes(q) && !reg.includes(q)) {
            return false;
        }
    }

    return true;
}

// 📋 Render Left Sidebar Driver Cards
function renderSidebarList() {
    const listContainer = document.getElementById('driverCardsList');
    if (!listContainer) return;

    const driversArray = Object.values(driversState).filter(d => matchesFilter(d, activeFilter, searchQuery));

    // Sort: available first, then engaged, then waiting
    driversArray.sort((a, b) => {
        const order = { 'available': 1, 'engaged': 2, 'on_job': 2, 'waiting': 3, 'on_break': 4, 'offline': 5 };
        const statusA = order[normalizeStatus(a.status)] || 99;
        const statusB = order[normalizeStatus(b.status)] || 99;
        return statusA - statusB;
    });

    if (driversArray.length === 0) {
        listContainer.innerHTML = `
            <div class="text-center py-5 text-muted small">
                <i class="bi bi-person-x fs-2 d-block mb-2 text-secondary opacity-50"></i>
                <div class="fw-semibold text-dark">No Active Drivers Found</div>
                <div class="text-muted mt-1">Drivers on duty will appear here automatically.</div>
            </div>
        `;
        return;
    }

    let html = '';
    driversArray.forEach(driver => {
        const status = normalizeStatus(driver.status);
        const isSelected = selectedDriverId === driver.id;
        const hasGps = driver.latitude && driver.longitude && !isNaN(parseFloat(driver.latitude)) && parseFloat(driver.latitude) !== 0;
        const letter = (driver.name || 'D').trim().charAt(0).toUpperCase();

        let statusClass = 'status-offline';
        let statusLabel = 'Offline';
        if (status === 'available') { statusClass = 'status-available'; statusLabel = 'Available'; }
        else if (status === 'engaged' || status === 'on_job') { statusClass = 'status-engaged'; statusLabel = 'Engaged'; }
        else if (status === 'waiting') { statusClass = 'status-waiting'; statusLabel = 'Waiting'; }
        else if (status === 'on_break') { statusClass = 'status-on-break'; statusLabel = 'On Break'; }

        html += `
            <div class="driver-card-item ${isSelected ? 'selected' : ''}" onclick="selectDriver('${driver.id}', true)">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-circle bg-warning text-dark fw-bold px-2 py-1">${letter}</span>
                        <span class="driver-badge-sign">${driver.call_sign || 'D-00'}</span>
                        <strong class="text-dark fs-6">${driver.name || 'Unnamed Driver'}</strong>
                    </div>
                    <span class="status-badge ${statusClass}">${statusLabel}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center text-muted small mt-2">
                    <div>
                        <i class="bi bi-car-front text-secondary me-1"></i>
                        <span>${driver.vehicle_model || driver.vehicle_make || 'Standard Vehicle'}</span>
                    </div>
                    <div>
                        ${hasGps ? '<span class="text-success fw-bold"><i class="bi bi-geo-alt-fill"></i> Live GPS</span>' : '<span class="text-muted"><i class="bi bi-geo-alt"></i> No GPS</span>'}
                    </div>
                </div>
            </div>
        `;
    });

    listContainer.innerHTML = html;
}

// 🎯 Select Driver and Open Drawer
function selectDriver(driverId, panCamera = true) {
    selectedDriverId = driverId;
    const driver = driversState[driverId];
    if (!driver) return;

    // Pan & Zoom on Map if loaded
    if (panCamera && map && driver.latitude && driver.longitude) {
        const lat = parseFloat(driver.latitude);
        const lng = parseFloat(driver.longitude);
        if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
            const pos = new google.maps.LatLng(lat, lng);
            map.panTo(pos);
            map.setZoom(16);
        }
    }

    // Refresh overlay classes
    for (const id in overlayMarkers) {
        if (overlayMarkers[id]) {
            overlayMarkers[id].updateContent();
        }
    }

    // Update Drawer Contents
    updateDrawerData(driver);

    // Open Drawer
    const drawerEl = document.getElementById('driverDrawer');
    if (drawerEl) drawerEl.classList.add('open');

    // Update selected card highlight
    renderSidebarList();
}

function updateDrawerData(driver) {
    if (!driver) return;

    const status = normalizeStatus(driver.status);
    let statusClass = 'status-offline';
    let statusLabel = 'Offline';
    if (status === 'available') { statusClass = 'status-available'; statusLabel = 'Available'; }
    else if (status === 'engaged' || status === 'on_job') { statusClass = 'status-engaged'; statusLabel = 'Engaged / On Trip'; }
    else if (status === 'waiting') { statusClass = 'status-waiting'; statusLabel = 'Waiting'; }
    else if (status === 'on_break') { statusClass = 'status-on-break'; statusLabel = 'On Break'; }

    // Header Letter & Name
    const letter = (driver.name || 'D').trim().charAt(0).toUpperCase();
    const avatarEl = document.getElementById('drawerAvatarText');
    if (avatarEl) avatarEl.textContent = letter;

    const nameEl = document.getElementById('drawerDriverName');
    if (nameEl) nameEl.textContent = driver.name || 'Unnamed Driver';

    const callSignEl = document.getElementById('drawerCallSign');
    if (callSignEl) callSignEl.textContent = driver.call_sign || 'D-000';
    
    const badgeEl = document.getElementById('drawerStatusBadge');
    if (badgeEl) {
        badgeEl.className = 'status-badge ' + statusClass;
        badgeEl.textContent = statusLabel;
    }

    // Quick Contacts
    const phone = driver.phone || '';
    const callBtn = document.getElementById('drawerCallBtn');
    const smsBtn = document.getElementById('drawerSmsBtn');
    if (callBtn && smsBtn) {
        if (phone) {
            callBtn.href = 'tel:' + phone;
            smsBtn.href = 'sms:' + phone;
            callBtn.style.display = 'inline-flex';
            smsBtn.style.display = 'inline-flex';
        } else {
            callBtn.style.display = 'none';
            smsBtn.style.display = 'none';
        }
    }

    // Vehicle
    const reg = driver.vehicle_reg || 'NO REG';
    const regEl = document.getElementById('drawerRegPlate');
    if (regEl) regEl.textContent = reg;

    const modelEl = document.getElementById('drawerVehicleModel');
    if (modelEl) modelEl.textContent = (driver.vehicle_make ? driver.vehicle_make + ' ' : '') + (driver.vehicle_model || 'Standard Saloon');

    const colorEl = document.getElementById('drawerVehicleColor');
    if (colorEl) colorEl.textContent = (driver.vehicle_color || 'Color Unspecified') + (driver.vehicle_type ? ' • ' + driver.vehicle_type : '');

    // GPS Telemetry
    const lat = parseFloat(driver.latitude);
    const lng = parseFloat(driver.longitude);
    const coordsEl = document.getElementById('drawerCoords');
    if (coordsEl) {
        if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
            coordsEl.textContent = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
        } else {
            coordsEl.textContent = 'GPS Signal Inactive';
        }
    }

    const speedEl = document.getElementById('drawerSpeed');
    if (speedEl) speedEl.textContent = (driver.speed ? Math.round(driver.speed) + ' mph' : '0 mph');

    const addressEl = document.getElementById('drawerAddress');
    if (addressEl) addressEl.textContent = driver.address || 'Reading Hub';

    // Contact Card
    const phoneEl = document.getElementById('drawerPhone');
    if (phoneEl) phoneEl.textContent = driver.phone || 'N/A';

    const emailEl = document.getElementById('drawerEmail');
    if (emailEl) emailEl.textContent = driver.email || 'N/A';

    const bfEl = document.getElementById('drawerBF');
    if (bfEl) bfEl.textContent = '£' + (parseFloat(driver.brought_forward) || 0).toFixed(2);

    // Actions
    const bfHistoryUrl = "{{ url('drivers') }}/" + driver.id + "/bf-history";
    const bfBtn = document.getElementById('drawerBfHistoryBtn');
    if (bfBtn) bfBtn.href = bfHistoryUrl;
}

// 🎛️ UI Handlers (Filters, Search, Buttons)
function setupUIEventHandlers() {
    // Status Filter Pills
    document.querySelectorAll('.filter-pill').forEach(pill => {
        pill.addEventListener('click', function () {
            document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            activeFilter = this.getAttribute('data-filter');
            renderAllDrivers();
        });
    });

    // Search Input
    const searchInput = document.getElementById('driverSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            searchQuery = this.value.trim();
            renderAllDrivers();
        });
    }

    // Fit All Bounds Button
    const fitBtn = document.getElementById('fitBoundsBtn');
    if (fitBtn) {
        fitBtn.addEventListener('click', () => {
            if (!map) return;
            const bounds = new google.maps.LatLngBounds();
            let count = 0;
            for (const id in overlayMarkers) {
                const overlay = overlayMarkers[id];
                if (overlay && overlay.div && overlay.div.style.display !== 'none') {
                    bounds.extend(overlay.getPosition());
                    count++;
                }
            }
            if (count > 1) {
                map.fitBounds(bounds);
            } else if (count === 1) {
                map.setCenter(bounds.getCenter());
                map.setZoom(15);
            } else {
                map.setCenter({ lat: 51.4543, lng: -0.9781 });
                map.setZoom(12);
            }
        });
    }

    // Traffic Toggle
    const trafficBtn = document.getElementById('trafficToggleBtn');
    if (trafficBtn) {
        trafficBtn.addEventListener('click', function () {
            if (!trafficLayer || !map) return;
            isTrafficActive = !isTrafficActive;
            trafficLayer.setMap(isTrafficActive ? map : null);
            this.classList.toggle('btn-warning', isTrafficActive);
            this.classList.toggle('btn-light', !isTrafficActive);
        });
    }

    // Map Style Toggle
    const mapStyleBtn = document.getElementById('mapStyleToggleBtn');
    if (mapStyleBtn) {
        mapStyleBtn.addEventListener('click', function () {
            if (!map) return;
            currentMapTypeId = (currentMapTypeId === 'roadmap') ? 'hybrid' : 'roadmap';
            map.setMapTypeId(currentMapTypeId);
        });
    }

    // Toggle Sidebar
    const sidebarEl = document.getElementById('driverSidebar');
    const toggleSidebarBtn = document.getElementById('toggleSidebarBtn');
    if (toggleSidebarBtn && sidebarEl) {
        toggleSidebarBtn.addEventListener('click', () => {
            sidebarEl.classList.toggle('collapsed');
        });
    }

    const closeSidebarBtn = document.getElementById('closeSidebarBtn');
    if (closeSidebarBtn && sidebarEl) {
        closeSidebarBtn.addEventListener('click', () => {
            sidebarEl.classList.add('collapsed');
        });
    }

    // Close Drawer
    const drawerEl = document.getElementById('driverDrawer');
    const closeDrawerBtn = document.getElementById('closeDrawerBtn');
    if (closeDrawerBtn && drawerEl) {
        closeDrawerBtn.addEventListener('click', () => {
            drawerEl.classList.remove('open');
            selectedDriverId = null;
            isFollowingDriver = false;
            for (const id in overlayMarkers) {
                if (overlayMarkers[id]) {
                    overlayMarkers[id].updateContent();
                }
            }
            renderSidebarList();
        });
    }

    // Focus on Map Button in Drawer
    const focusMapBtn = document.getElementById('drawerFocusMapBtn');
    if (focusMapBtn) {
        focusMapBtn.addEventListener('click', () => {
            if (selectedDriverId && driversState[selectedDriverId] && map) {
                const d = driversState[selectedDriverId];
                if (d.latitude && d.longitude) {
                    map.panTo({ lat: parseFloat(d.latitude), lng: parseFloat(d.longitude) });
                    map.setZoom(17);
                }
            }
        });
    }

    // Follow Live GPS Toggle
    const followBtn = document.getElementById('drawerFollowToggleBtn');
    if (followBtn) {
        followBtn.addEventListener('click', function () {
            isFollowingDriver = !isFollowingDriver;
            this.classList.toggle('btn-danger', isFollowingDriver);
            this.classList.toggle('btn-warning', !isFollowingDriver);
            if (isFollowingDriver) {
                alert('🎥 Camera is now locked to follow this driver live.');
            }
        });
    }
}

// ⚡ Immediate Startup on DOM Ready
document.addEventListener('DOMContentLoaded', function () {
    // 1️⃣ Immediately render initial server-passed drivers
    renderSidebarList();
    updateCounts();

    // 2️⃣ Setup all click/filter listeners
    setupUIEventHandlers();

    // 3️⃣ Start Real-time Firebase stream immediately
    initFirebaseLiveStream();
});
</script>
@endsection
