@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">🚘 Track Driver</h5>
            <span class="badge bg-light text-dark">Booking Ref: {{ $booking['ref_no'] ?? $booking['id'] ?? 'N/A' }}</span>
        </div>

        <div class="card-body">
            <p><strong>Pickup:</strong> {{ $booking['pickup_address'] ?? 'N/A' }}</p>
            <p><strong>Dropoff:</strong> {{ $booking['dropoff_address'] ?? 'N/A' }}</p>

            <div id="map" style="height: 520px; border-radius: 10px;"></div>
        </div>
    </div>
</div>

<script>
let map;
let driverMarker;
let directionsService;
let directionsRenderer;

const driverId = "{{ $booking['driver_id'] ?? '' }}";
const pickupAddress = "{{ $booking['pickup_address'] ?? '' }}";
const dropoffAddress = "{{ $booking['dropoff_address'] ?? '' }}";

function initMap() {

    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 12,
        center: { lat: 51.5074, lng: -0.1278 } // Default London
    });

    // Driver Marker
    driverMarker = new google.maps.Marker({
        map: map,
        icon: {
            url: "https://cdn-icons-png.flaticon.com/512/3097/3097180.png",
            scaledSize: new google.maps.Size(40, 40)
        }
    });

    // Directions
    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({
        suppressMarkers: true,
        polylineOptions: {
            strokeColor: "#000",
            strokeWeight: 5
        }
    });

    directionsRenderer.setMap(map);

    drawRoute();
    updateDriverLocation();

    setInterval(updateDriverLocation, 5000); // live tracking
}

// Draw Pickup → Dropoff route
function drawRoute() {

    directionsService.route({
        origin: pickupAddress,
        destination: dropoffAddress,
        travelMode: google.maps.TravelMode.DRIVING
    }, function(result, status) {
        if (status === "OK") {
            directionsRenderer.setDirections(result);
        } else {
            console.error("Route error:", status);
        }
    });
}

// Fetch driver live location
function updateDriverLocation() {

    fetch(`https://crowncarz.com/crowncarz_api/api/driver-location/${driverId}`)
        .then(res => res.json())
        .then(data => {

            if (!data.lat || !data.lng) return;

            const position = {
                lat: Number(data.lat),
                lng: Number(data.lng)
            };

            driverMarker.setPosition(position);
            map.panTo(position);
            
              map.setZoom(18); // try 17,18,19 as per need

            // Rotate marker by heading
            driverMarker.setIcon({
  url: data.isOnline
    ? "https://crowncarz.com/admin/public/images/Car.png"
    : "https://crowncarz.com/admin/public/images/Car.png",

  scaledSize: new google.maps.Size(180, 200), // width x height (2:1)
//   anchor: new google.maps.Point(20, 10),    // center align
  rotation: data.heading || 0
});


            console.log("Driver location:", data);
        })
        .catch(err => console.error("Driver location error:", err));
}

window.onload = initMap;
</script>


<script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap">
</script>
@endsection
