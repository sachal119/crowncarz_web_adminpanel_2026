@extends('layouts.app')

@section('content')
@php
    $trackingDriverId = trim((string) ($booking['driver_id'] ?? ''));
    $trackingDriverName = $booking['driver_name'] ?? ($trackingDriverId !== '' ? 'Assigned driver' : 'Awaiting assignment');
    $trackingCallSign = $booking['driver_call_sign'] ?? ($booking['call_sign'] ?? '');
@endphp
<style>
.tracking-page { padding: 24px; color: var(--cc-text-primary); }
.tracking-heading { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; }
.tracking-eyebrow { color: var(--cc-text-secondary); font-size: 10px; font-weight: 700; letter-spacing: 1.6px; text-transform: uppercase; }
.tracking-heading h1 { margin: 5px 0 0; font-size: 25px; font-weight: 750; letter-spacing: -.7px; }
.tracking-ref { padding: 9px 14px; border: 1px solid var(--cc-border); border-radius: 10px; background: var(--cc-bg-card); font-size: 12px; color: var(--cc-text-secondary); }
.tracking-ref strong { color: var(--cc-text-primary); margin-left: 6px; }
.tracking-layout { display: grid; grid-template-columns: 310px minmax(0, 1fr); border: 1px solid var(--cc-border); border-radius: 18px; overflow: hidden; background: var(--cc-bg-card); box-shadow: 0 8px 28px rgba(15,23,42,.05); }
.tracking-sidebar { padding: 24px; border-right: 1px solid var(--cc-border); }
.tracking-section-title { font-size: 13px; font-weight: 700; margin: 0; }
.tracking-status { display: inline-flex; align-items: center; gap: 7px; border-radius: 20px; padding: 6px 10px; font-size: 11px; font-weight: 650; background: var(--cc-bg-card-subtle); color: var(--cc-text-secondary); }
.tracking-status::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #d49a37; flex-shrink: 0; }
.tracking-status[data-state="connected"]::before { background: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,.12); }
.tracking-status[data-state="error"]::before { background: #ef4444; }
.tracking-driver { display: flex; align-items: center; gap: 12px; margin: 22px 0; }
.tracking-avatar { width: 46px; height: 46px; border-radius: 14px; display: grid; place-items: center; background: rgba(230,176,74,.14); color: #b88322; font-size: 22px; flex-shrink: 0; }
.tracking-driver-name { font-size: 15px; font-weight: 700; overflow-wrap: anywhere; }
.tracking-small { font-size: 12px; color: var(--cc-text-secondary); line-height: 1.6; }
.tracking-divider { height: 1px; background: var(--cc-border); margin: 22px 0; }
.tracking-booking-status { display: inline-block; margin-top: 10px; padding: 4px 9px; border-radius: 6px; background: rgba(230,176,74,.13); color: var(--cc-text-primary); font-size: 11px; font-weight: 600; }
.tracking-stops { margin-top: 22px; }
.tracking-stop { position: relative; padding: 0 0 26px 26px; }
.tracking-stop:not(:last-child)::after { content: ''; position: absolute; left: 5px; top: 16px; bottom: 4px; border-left: 1px dashed var(--cc-border); }
.tracking-stop:last-child { padding-bottom: 0; }
.tracking-stop-dot { position: absolute; left: 0; top: 3px; width: 11px; height: 11px; border: 3px solid #10b981; border-radius: 50%; }
.tracking-stop:last-child .tracking-stop-dot { border-color: #ef4444; border-radius: 3px; }
.tracking-stop-label { font-size: 10px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; color: var(--cc-text-secondary); margin-bottom: 7px; }
.tracking-address { font-size: 13px; line-height: 1.65; overflow-wrap: anywhere; }
.tracking-metrics { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 24px; }
.tracking-metric { border: 1px solid var(--cc-border); border-radius: 10px; padding: 12px; background: var(--cc-bg-card-subtle); }
.tracking-metric strong { display: block; font-size: 16px; margin-bottom: 4px; }
.tracking-metric span { font-size: 10px; color: var(--cc-text-secondary); }
.tracking-map-panel { min-width: 0; display: flex; flex-direction: column; }
.tracking-map-toolbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; padding: 14px 18px; border-bottom: 1px solid var(--cc-border); }
.tracking-map-label { display: flex; gap: 9px; align-items: center; font-size: 12px; font-weight: 700; }
.tracking-map-label i { color: #b88322; }
.tracking-controls { display: flex; gap: 6px; }
.tracking-control { border: 1px solid var(--cc-border); border-radius: 8px; padding: 7px 11px; background: var(--cc-bg-card); color: var(--cc-text-primary); font-size: 11px; font-weight: 600; }
.tracking-control:hover { background: var(--cc-bg-card-subtle); }
.tracking-control[aria-pressed="true"] { background: #1e293b; border-color: #1e293b; color: #fff; }
.tracking-control:disabled { opacity: .45; cursor: not-allowed; }
.tracking-control:focus-visible { outline: 3px solid #e6b04a; outline-offset: 2px; }
.tracking-map-stage { position: relative; flex: 1; min-height: 540px; }
#trackingMap { height: 100%; width: 100%; position: absolute; inset: 0; background: #e9eeeb; }
.tracking-map-message { position: absolute; top: 16px; left: 50%; transform: translateX(-50%); z-index: 1; max-width: calc(100% - 32px); width: max-content; padding: 10px 16px; border-radius: 10px; background: var(--cc-bg-card); color: var(--cc-text-secondary); box-shadow: 0 3px 15px rgba(15,23,42,.12); font-size: 12px; pointer-events: none; }
.tracking-map-message[hidden] { display: none; }
.tracking-map-footer { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; padding: 12px 18px; border-top: 1px solid var(--cc-border); font-size: 11px; color: var(--cc-text-secondary); }
.tracking-legend { display: flex; gap: 15px; }
.tracking-legend span { display: inline-flex; align-items: center; gap: 6px; }
.tracking-legend b { width: 7px; height: 7px; border-radius: 50%; background: #10b981; }
.tracking-legend .destination { background: #ef4444; }
.tracking-legend .driver { background: #1e293b; }
@media (min-width: 1500px) { .tracking-map-stage { min-height: 620px; } }
@media (max-width: 1000px) { .tracking-layout { grid-template-columns: 260px minmax(0, 1fr); } .tracking-sidebar { padding: 18px; } }
@media (max-width: 767px) {
    .tracking-page { padding: 14px; }
    .tracking-heading { align-items: flex-start; flex-direction: column; gap: 10px; }
    .tracking-heading h1 { font-size: 22px; }
    .tracking-layout { display: flex; flex-direction: column; }
    .tracking-map-panel { order: -1; }
    .tracking-map-stage { min-height: 420px; }
    .tracking-sidebar { border-right: 0; border-top: 1px solid var(--cc-border); }
    .tracking-map-toolbar { padding: 12px; }
}
</style>

<div class="tracking-page">
    <header class="tracking-heading">
        <div><div class="tracking-eyebrow">Crown Carz · Journey tracking</div><h1>Live driver tracking</h1></div>
        <div class="tracking-ref">Booking reference <strong>{{ $booking['ref_no'] ?? $booking['id'] ?? 'N/A' }}</strong></div>
    </header>
    <div class="tracking-layout">
        <aside class="tracking-sidebar" aria-label="Journey details">
            <div class="d-flex align-items-center justify-content-between gap-2">
                <h2 class="tracking-section-title">Your driver</h2>
                <span id="trackingStatus" class="tracking-status" role="status">Connecting</span>
            </div>
            <div class="tracking-driver">
                <div class="tracking-avatar"><i class="bi bi-car-front-fill" aria-hidden="true"></i></div>
                <div><div class="tracking-driver-name">{{ $trackingDriverName }}</div><div class="tracking-small">{{ $trackingCallSign !== '' ? 'Call sign · ' . $trackingCallSign : 'Crown Carz driver' }}</div></div>
            </div>
            <div class="tracking-small" id="trackingUpdate" aria-live="polite">Waiting for a location update</div>
            <div class="tracking-divider"></div>
            <h2 class="tracking-section-title">Journey details</h2>
            <span class="tracking-booking-status">{{ ucwords(str_replace('_', ' ', $booking['status'] ?? 'Pending')) }}</span>
            <div class="tracking-stops">
                <div class="tracking-stop"><span class="tracking-stop-dot"></span><div class="tracking-stop-label">Pickup</div><div class="tracking-address">{{ $booking['pickup_address'] ?? 'Not provided' }}</div></div>
                <div class="tracking-stop"><span class="tracking-stop-dot"></span><div class="tracking-stop-label">Dropoff</div><div class="tracking-address">{{ $booking['dropoff_address'] ?? 'Not provided' }}</div></div>
            </div>
            <div class="tracking-metrics">
                <div class="tracking-metric"><strong id="trackingDistance">—</strong><span>Route distance</span></div>
                <div class="tracking-metric"><strong id="trackingDuration">—</strong><span>Est. journey time</span></div>
            </div>
            <p class="tracking-small mt-3 mb-0" id="trackingRouteNote">Pickup to dropoff estimate, not driver arrival time.</p>
        </aside>
        <section class="tracking-map-panel" aria-label="Driver location map">
            <div class="tracking-map-toolbar">
                <div class="tracking-map-label"><i class="bi bi-broadcast" aria-hidden="true"></i> Live location</div>
                <div class="tracking-controls">
                    <button type="button" id="trackingFollow" class="tracking-control" aria-pressed="true" disabled><i class="bi bi-crosshair me-1" aria-hidden="true"></i> Follow driver</button>
                    <button type="button" id="trackingOverview" class="tracking-control" disabled><i class="bi bi-arrows-fullscreen me-1" aria-hidden="true"></i> Full route</button>
                </div>
            </div>
            <div class="tracking-map-stage">
                <div id="trackingMap" aria-label="Live driver and journey route"></div>
                <div id="trackingMapMessage" class="tracking-map-message" role="status">Loading journey map…</div>
            </div>
            <div class="tracking-map-footer">
                <div class="tracking-legend"><span><b></b> Pickup</span><span><b class="destination"></b> Dropoff</span><span><b class="driver"></b> Driver</span></div>
                <span>Location checked every 5 seconds</span>
            </div>
        </section>
    </div>
</div>

<script>
(() => {
    const driverId = @json($trackingDriverId);
    const pickupAddress = @json($booking['pickup_address'] ?? '');
    const dropoffAddress = @json($booking['dropoff_address'] ?? '');
    const status = document.getElementById('trackingStatus');
    const updated = document.getElementById('trackingUpdate');
    const message = document.getElementById('trackingMapMessage');
    const followButton = document.getElementById('trackingFollow');
    const overviewButton = document.getElementById('trackingOverview');
    let map, driverMarker, routeBounds, timer, request;
    let following = true;
    let disposed = false;
    let lastReceived = null;

    function setStatus(text, state = '') {
        status.textContent = text;
        status.dataset.state = state;
    }
    function showMessage(text) {
        message.textContent = text;
        message.hidden = !text;
    }
    function setFollowing(value) {
        following = value;
        followButton.setAttribute('aria-pressed', String(value));
    }
    function fitJourney() {
        const bounds = new google.maps.LatLngBounds();
        if (routeBounds) bounds.union(routeBounds);
        if (driverMarker && driverMarker.getPosition()) bounds.extend(driverMarker.getPosition());
        if (!bounds.isEmpty()) {
            map.fitBounds(bounds, 64);
            google.maps.event.addListenerOnce(map, 'idle', () => {
                if (map.getZoom() > 16) map.setZoom(16);
            });
        }
    }
    function drawRoute() {
        if (!pickupAddress || !dropoffAddress) {
            document.getElementById('trackingRouteNote').textContent = 'Route unavailable: pickup or dropoff address is missing.';
            return;
        }
        const renderer = new google.maps.DirectionsRenderer({
            map, suppressMarkers: true, preserveViewport: true,
            polylineOptions: { strokeColor: '#b88322', strokeOpacity: .85, strokeWeight: 5 }
        });
        new google.maps.DirectionsService().route({
            origin: pickupAddress, destination: dropoffAddress,
            travelMode: google.maps.TravelMode.DRIVING,
            unitSystem: google.maps.UnitSystem.IMPERIAL
        }, (result, routeStatus) => {
            if (disposed) return;
            if (routeStatus !== 'OK' || !result.routes.length) {
                document.getElementById('trackingRouteNote').textContent = 'Journey route unavailable. Driver tracking will continue.';
                return;
            }
            renderer.setDirections(result);
            const route = result.routes[0];
            const leg = route.legs[0];
            routeBounds = route.bounds;
            [[leg.start_location, '#10b981', 'Pickup'], [leg.end_location, '#ef4444', 'Dropoff']].forEach(([position, color, title]) => {
                new google.maps.Marker({ map, position, title, icon: {
                    path: google.maps.SymbolPath.CIRCLE, scale: 7,
                    fillColor: color, fillOpacity: 1, strokeColor: '#ffffff', strokeWeight: 3
                }});
            });
            document.getElementById('trackingDistance').textContent = leg.distance ? leg.distance.text : '—';
            document.getElementById('trackingDuration').textContent = leg.duration ? leg.duration.text : '—';
            overviewButton.disabled = false;
            if (!following) fitJourney();
        });
    }
    async function updateDriverLocation() {
        if (disposed || !driverId) return;
        request = new AbortController();
        const timeout = setTimeout(() => request.abort(), 10000);
        try {
            const response = await fetch(`https://crowncarz.com/crowncarz_api/api/driver-location/${encodeURIComponent(driverId)}`, { signal: request.signal });
            if (!response.ok) throw new Error('Location request failed');
            const data = await response.json();
            const lat = Number(data.lat), lng = Number(data.lng);
            if (data.lat == null || data.lng == null || String(data.lat).trim() === '' || String(data.lng).trim() === '' || !Number.isFinite(lat) || !Number.isFinite(lng) || Math.abs(lat) > 90 || Math.abs(lng) > 180) {
                throw new Error('Location unavailable');
            }
            if (disposed) return;
            const firstLocation = !driverMarker.getPosition();
            driverMarker.setPosition({ lat, lng });
            const heading = Number(data.heading);
            driverMarker.setIcon({
                path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                scale: 6, fillColor: '#1e293b', fillOpacity: 1,
                strokeColor: '#ffffff', strokeWeight: 2,
                rotation: Number.isFinite(heading) ? heading : 0
            });
            lastReceived = new Date();
            const offline = data.isOnline === false || data.isOnline === 0 || data.isOnline === '0' || data.isOnline === 'false';
            setStatus(offline ? 'Offline' : 'Connected', offline ? '' : 'connected');
            updated.textContent = `${offline ? 'Last known position · fetched' : 'Location received'} ${lastReceived.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' })}`;
            showMessage(offline ? 'Driver is offline. Showing last known position.' : '');
            followButton.disabled = false;
            overviewButton.disabled = false;
            if (following) {
                map.panTo({ lat, lng });
                if (firstLocation) map.setZoom(15);
            } else if (firstLocation) fitJourney();
        } catch (error) {
            if (disposed) return;
            setStatus(lastReceived ? 'Reconnecting' : 'Unavailable', 'error');
            updated.textContent = lastReceived ? `Last received ${lastReceived.toLocaleTimeString()}` : 'Waiting for driver location';
            showMessage(lastReceived ? 'Connection interrupted. Showing last received location.' : 'Driver location unavailable. Retrying automatically…');
        } finally {
            clearTimeout(timeout);
            if (!disposed) timer = setTimeout(updateDriverLocation, 5000);
        }
    }
    window.initBookingTrackingMap = function () {
        if (map || disposed) return;
        map = new google.maps.Map(document.getElementById('trackingMap'), {
            center: { lat: 51.4543, lng: -0.9781 }, zoom: 12,
            mapTypeControl: false, streetViewControl: false, fullscreenControl: true,
            zoomControl: true, clickableIcons: false,
            styles: [
                { featureType: 'poi', stylers: [{ visibility: 'off' }] },
                { featureType: 'transit', stylers: [{ visibility: 'off' }] },
                { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#ffffff' }] },
                { featureType: 'landscape', elementType: 'geometry', stylers: [{ color: '#edf0e9' }] },
                { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#c8dfe9' }] }
            ]
        });
        driverMarker = new google.maps.Marker({ map, title: 'Driver location', zIndex: 10 });
        map.addListener('dragstart', () => setFollowing(false));
        followButton.addEventListener('click', () => {
            setFollowing(!following);
            if (following && driverMarker.getPosition()) {
                map.panTo(driverMarker.getPosition());
                map.setZoom(15);
            }
        });
        overviewButton.addEventListener('click', () => { setFollowing(false); fitJourney(); });
        showMessage(driverId ? 'Waiting for driver location…' : 'No driver assigned to this booking yet.');
        drawRoute();
        if (driverId) updateDriverLocation();
        else {
            setStatus('Unassigned');
            updated.textContent = 'Tracking starts once a driver is assigned. Reload to check for an assignment.';
        }
    };
    window.bookingTrackingMapError = function () {
        setStatus('Map unavailable', 'error');
        showMessage('The map could not load. Please reload to try again.');
    };
    window.gm_authFailure = window.bookingTrackingMapError;
    window.addEventListener('pagehide', () => {
        disposed = true;
        clearTimeout(timer);
        if (request) request.abort();
    });
    window.addEventListener('pageshow', event => { if (event.persisted) window.location.reload(); });
})();
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initBookingTrackingMap" onerror="bookingTrackingMapError()"></script>
@endsection
