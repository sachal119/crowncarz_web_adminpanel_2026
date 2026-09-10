@extends('layouts.app')

@section('content')
<style>
/* Fullscreen Map Layout */
.live-map-wrapper {
    position: relative;
    width: 100%;
    height: calc(100vh - 62px);
    overflow: hidden;
    margin: -1.5rem -1.5rem -2rem -1.5rem; /* Cancel main layout padding */
}

#liveGoogleMap {
    width: 100%;
    height: 100%;
    background-color: #e5e3df;
}

/* Floating Top Controls Header */
.map-floating-topbar {
    position: absolute;
    top: 16px;
    left: 16px;
    right: 16px;
    z-index: 1050;
    pointer-events: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.map-floating-topbar > * {
    pointer-events: auto;
}

.glass-panel {
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.6);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    border-radius: 12px;
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
    background-color: #198754;
    border-radius: 50%;
    box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
    animation: radarPulse 1.8s infinite;
}

@keyframes radarPulse {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
    }
    70% {
        transform: scale(1.1);
        box-shadow: 0 0 0 10px rgba(25, 135, 84, 0);
    }
    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0);
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
    background: #212529;
    color: #ffffff;
}

.filter-pill .pill-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
}

.pill-dot.dot-all { background: #6c757d; }
.pill-dot.dot-available { background: #198754; }
.pill-dot.dot-engaged { background: #dc3545; }
.pill-dot.dot-waiting { background: #ffc107; }
.pill-dot.dot-break { background: #6c757d; }

/* Search & Actions */
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

/* Floating Driver List Sidebar */
.driver-sidebar {
    position: absolute;
    top: 80px;
    left: 16px;
    bottom: 24px;
    width: 320px;
    z-index: 1040;
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
    top: 16px;
    right: 16px;
    bottom: 24px;
    width: 380px;
    max-width: calc(100vw - 32px);
    z-index: 1060;
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
    background: linear-gradient(135deg, #212529, #343a40);
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
    background: #B87333;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    font-weight: bold;
    border: 3px solid rgba(255, 255, 255, 0.2);
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
    color: #6c757d;
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
    color: #6c757d;
}

.detail-row .value {
    font-weight: 600;
    color: #212529;
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

/* Custom Marker Overlay */
.custom-driver-marker {
    cursor: pointer;
    transition: transform 0.2s ease;
}

.custom-driver-marker:hover {
    transform: scale(1.15);
}

/* Bottom Map Controls */
.bottom-map-controls {
    position: absolute;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1040;
    display: flex;
    gap: 8px;
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
    <!-- Map Container -->
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
            <div class="text-center py-4 text-muted small">
                <div class="spinner-border spinner-border-sm text-secondary mb-2" role="status"></div>
                <div>Syncing live drivers from Firebase...</div>
            </div>
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
                    <span class="value text-success" id="drawerLastUpdate">Just now</span>
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
let map;
let trafficLayer = null;
let isTrafficActive = false;
let currentMapTypeId = 'roadmap';
let markers = {}; // id -> google.maps.Marker
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

    // Render initial state
    renderAllDrivers();

    // Connect to Firebase for Real-time GPS & Status
    initFirebaseLiveStream();

    // Setup UI event handlers
    setupUIEventHandlers();
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

            if (isFollowingDriver && currentSelected.latitude && currentSelected.longitude) {
                const pos = new google.maps.LatLng(
                    parseFloat(currentSelected.latitude),
                    parseFloat(currentSelected.longitude)
                );
                map.panTo(pos);
            }
        }
    });
}

// 🚗 Render or Update Driver Markers on Map
function renderAllDrivers() {
    if (!map) return;

    let totalCount = 0;
    let availableCount = 0;
    let engagedCount = 0;
    let waitingCount = 0;
    let breakCount = 0;

    const visibleDriverIds = new Set();
    const bounds = new google.maps.LatLngBounds();
    let validGpsCount = 0;

    for (const id in driversState) {
        const driver = driversState[id];
        const status = normalizeStatus(driver.status);

        // Count stats
        totalCount++;
        if (status === 'available') availableCount++;
        else if (status === 'engaged' || status === 'on_job') engagedCount++;
        else if (status === 'waiting') waitingCount++;
        else if (status === 'on_break' || status === 'on break') breakCount++;

        // Filter condition
        if (!matchesFilter(driver, activeFilter, searchQuery)) {
            if (markers[id]) {
                markers[id].setMap(null);
            }
            continue;
        }

        const lat = parseFloat(driver.latitude);
        const lng = parseFloat(driver.longitude);

        if (isNaN(lat) || isNaN(lng) || (lat === 0 && lng === 0)) {
            if (markers[id]) {
                markers[id].setMap(null);
            }
            continue;
        }

        const pos = { lat, lng };
        visibleDriverIds.add(id);
        bounds.extend(pos);
        validGpsCount++;

        const iconConfig = getVehicleIcon(driver);

        if (markers[id]) {
            // Smoothly move existing marker
            markers[id].setPosition(pos);
            markers[id].setIcon(iconConfig);
            markers[id].setMap(map);
        } else {
            // Create new marker
            const marker = new google.maps.Marker({
                position: pos,
                map: map,
                title: `${driver.call_sign || 'D'} - ${driver.name || 'Driver'}`,
                icon: iconConfig,
                optimized: true,
            });

            marker.addListener("click", () => {
                selectDriver(id, true);
            });

            markers[id] = marker;
        }
    }

    // Clean up markers that are no longer present
    for (const id in markers) {
        if (!visibleDriverIds.has(id)) {
            markers[id].setMap(null);
        }
    }

    // Update Counts Badges
    document.getElementById('activeDriverCountBadge').textContent = validGpsCount;
    document.getElementById('countAll').textContent = totalCount;
    document.getElementById('countAvailable').textContent = availableCount;
    document.getElementById('countEngaged').textContent = engagedCount;
    document.getElementById('countWaiting').textContent = waitingCount;
    document.getElementById('countBreak').textContent = breakCount;

    // Render Sidebar List
    renderSidebarList();
}

// 🎨 Marker Icon with status color ring
function getVehicleIcon(driver) {
    const status = normalizeStatus(driver.status);
    let carIconUrl = "https://crowncarz.com/admin/public/images/Car.png";

    return {
        url: carIconUrl,
        scaledSize: new google.maps.Size(46, 46),
        anchor: new google.maps.Point(23, 23)
    };
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
            <div class="text-center py-4 text-muted small">
                <i class="bi bi-car-front fs-2 d-block mb-1 opacity-50"></i>
                No matching drivers found
            </div>
        `;
        return;
    }

    let html = '';
    driversArray.forEach(driver => {
        const status = normalizeStatus(driver.status);
        const isSelected = selectedDriverId === driver.id;
        const hasGps = driver.latitude && driver.longitude && !isNaN(parseFloat(driver.latitude));

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
                        ${hasGps ? '<span class="text-success"><i class="bi bi-geo-alt-fill"></i> Live GPS</span>' : '<span class="text-muted"><i class="bi bi-geo-alt"></i> No GPS</span>'}
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

    // Pan & Zoom on Map
    if (panCamera && map && driver.latitude && driver.longitude) {
        const pos = new google.maps.LatLng(parseFloat(driver.latitude), parseFloat(driver.longitude));
        map.panTo(pos);
        map.setZoom(16);
    }

    // Update Drawer Contents
    updateDrawerData(driver);

    // Open Drawer
    document.getElementById('driverDrawer').classList.add('open');

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

    // Header
    const initials = (driver.name || 'D').split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
    document.getElementById('drawerAvatarText').textContent = initials;
    document.getElementById('drawerDriverName').textContent = driver.name || 'Unnamed Driver';
    document.getElementById('drawerCallSign').textContent = driver.call_sign || 'D-000';
    
    const badgeEl = document.getElementById('drawerStatusBadge');
    badgeEl.className = 'status-badge ' + statusClass;
    badgeEl.textContent = statusLabel;

    // Quick Contacts
    const phone = driver.phone || '';
    const callBtn = document.getElementById('drawerCallBtn');
    const smsBtn = document.getElementById('drawerSmsBtn');
    if (phone) {
        callBtn.href = 'tel:' + phone;
        smsBtn.href = 'sms:' + phone;
        callBtn.style.display = 'inline-flex';
        smsBtn.style.display = 'inline-flex';
    } else {
        callBtn.style.display = 'none';
        smsBtn.style.display = 'none';
    }

    // Vehicle
    const reg = driver.vehicle_reg || 'NO REG';
    document.getElementById('drawerRegPlate').textContent = reg;
    document.getElementById('drawerVehicleModel').textContent = (driver.vehicle_make ? driver.vehicle_make + ' ' : '') + (driver.vehicle_model || 'Standard Saloon');
    document.getElementById('drawerVehicleColor').textContent = (driver.vehicle_color || 'Color Unspecified') + (driver.vehicle_type ? ' • ' + driver.vehicle_type : '');

    // GPS Telemetry
    const lat = parseFloat(driver.latitude);
    const lng = parseFloat(driver.longitude);
    if (!isNaN(lat) && !isNaN(lng)) {
        document.getElementById('drawerCoords').textContent = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
    } else {
        document.getElementById('drawerCoords').textContent = 'GPS Signal Inactive';
    }

    document.getElementById('drawerSpeed').textContent = (driver.speed ? Math.round(driver.speed) + ' mph' : '0 mph');
    document.getElementById('drawerLastUpdate').textContent = 'Live Connected';
    document.getElementById('drawerAddress').textContent = driver.address || 'Reading Hub';

    // Contact Card
    document.getElementById('drawerPhone').textContent = driver.phone || 'N/A';
    document.getElementById('drawerEmail').textContent = driver.email || 'N/A';
    document.getElementById('drawerBF').textContent = '£' + (parseFloat(driver.brought_forward) || 0).toFixed(2);

    // Actions
    const bfHistoryUrl = "{{ url('drivers') }}/" + driver.id + "/bf-history";
    document.getElementById('drawerBfHistoryBtn').href = bfHistoryUrl;
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
    document.getElementById('fitBoundsBtn').addEventListener('click', () => {
        if (!map) return;
        const bounds = new google.maps.LatLngBounds();
        let count = 0;
        for (const id in markers) {
            if (markers[id].getMap()) {
                bounds.extend(markers[id].getPosition());
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

    // Traffic Toggle
    document.getElementById('trafficToggleBtn').addEventListener('click', function () {
        if (!trafficLayer) return;
        isTrafficActive = !isTrafficActive;
        trafficLayer.setMap(isTrafficActive ? map : null);
        this.classList.toggle('btn-warning', isTrafficActive);
        this.classList.toggle('btn-light', !isTrafficActive);
    });

    // Map Style Toggle
    document.getElementById('mapStyleToggleBtn').addEventListener('click', function () {
        if (!map) return;
        currentMapTypeId = (currentMapTypeId === 'roadmap') ? 'hybrid' : 'roadmap';
        map.setMapTypeId(currentMapTypeId);
    });

    // Toggle Sidebar
    const sidebarEl = document.getElementById('driverSidebar');
    document.getElementById('toggleSidebarBtn').addEventListener('click', () => {
        sidebarEl.classList.toggle('collapsed');
    });

    document.getElementById('closeSidebarBtn').addEventListener('click', () => {
        sidebarEl.classList.add('collapsed');
    });

    // Close Drawer
    const drawerEl = document.getElementById('driverDrawer');
    document.getElementById('closeDrawerBtn').addEventListener('click', () => {
        drawerEl.classList.remove('open');
        selectedDriverId = null;
        isFollowingDriver = false;
        renderSidebarList();
    });

    // Focus on Map Button in Drawer
    document.getElementById('drawerFocusMapBtn').addEventListener('click', () => {
        if (selectedDriverId && driversState[selectedDriverId]) {
            const d = driversState[selectedDriverId];
            if (d.latitude && d.longitude) {
                map.panTo({ lat: parseFloat(d.latitude), lng: parseFloat(d.longitude) });
                map.setZoom(17);
            }
        }
    });

    // Follow Live GPS Toggle
    const followBtn = document.getElementById('drawerFollowToggleBtn');
    followBtn.addEventListener('click', function () {
        isFollowingDriver = !isFollowingDriver;
        this.classList.toggle('btn-danger', isFollowingDriver);
        this.classList.toggle('btn-warning', !isFollowingDriver);
        if (isFollowingDriver) {
            alert('🎥 Camera is now locked to follow this driver live.');
        }
    });
}
</script>
@endsection
