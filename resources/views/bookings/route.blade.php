@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">🗺️ Booking Route</h5>
        </div>
        <div class="card-body">
            <p><strong>Pickup:</strong> {{ $booking['pickup_address'] ?? 'N/A' }}</p>
            <p><strong>Dropoff:</strong> {{ $booking['dropoff_address'] ?? 'N/A' }}</p>

            @if(!empty($booking['via_points']))
                <p><strong>Via Points:</strong></p>
                <ul>
                    @foreach($booking['via_points'] as $via)
                        <li>{{ $via }}</li>
                    @endforeach
                </ul>
            @endif

            <div id="routeMap" style="height: 500px; border-radius: 10px;"></div>
        </div>
    </div>
</div>

<script>
function initRouteMap() {
    const map = new google.maps.Map(document.getElementById("routeMap"), {
        zoom: 7,
        center: { lat: 51.5074, lng: -0.1278 }
    });

    const directionsService = new google.maps.DirectionsService();
    const directionsRenderer = new google.maps.DirectionsRenderer({ map });

    const waypoints = [
        @if(!empty($booking['via_points']))
            @foreach($booking['via_points'] as $via)
                { location: "{{ $via }}", stopover: true },
            @endforeach
        @endif
    ];

    directionsService.route({
        origin: "{{ $booking['pickup_address'] ?? '' }}",
        destination: "{{ $booking['dropoff_address'] ?? '' }}",
        waypoints: waypoints,
        travelMode: google.maps.TravelMode.DRIVING
    }, function(response, status) {
        if (status === google.maps.DirectionsStatus.OK) {
            directionsRenderer.setDirections(response);
        } else {
            console.error("Directions request failed due to " + status);
        }
    });
}
</script>

<script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initRouteMap">
</script>
@endsection
