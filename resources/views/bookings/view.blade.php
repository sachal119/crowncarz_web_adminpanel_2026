@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold" style="color: #6B3E26;">Booking Details</h1>
    <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to List
    </a>
</div>

<div class="card shadow-sm border-0" style="background-color: #FFF5CC; border-left: 5px solid #D39F61;">
    <div class="card-body">
        <h5 class="card-title mb-3 fw-semibold" style="color: #A86B32;">Ref# {{ $booking['ref_no'] }}</h5>
        
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <h6 class="fw-bold text-muted mb-2">Passenger Info</h6>
                        <p><strong>Name:</strong> {{ $booking['passenger_name'] ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $booking['email'] ?? 'N/A' }}</p>
                        <p><strong>Phone:</strong> {{ $booking['phone_no'] ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <h6 class="fw-bold text-muted mb-2">Trip Info</h6>
                        <p><strong>Pickup:</strong> {{ $booking['pickup_address'] }}</p>
                        <p><strong>Dropoff:</strong> {{ $booking['dropoff_address'] }}</p>
                        <p><strong>Date/Time:</strong> {{ $booking['formatted_pickup_time'] ?? $booking['pickup_time'] }}</p>
                        <p><strong>Price:</strong> ${{ $booking['price'] ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <h6 class="fw-bold text-muted mb-2">Assignment</h6>
                        <p><strong>Driver ID:</strong> {{ $booking['driver_id'] ?? 'Unassigned' }}</p>
                        <p><strong>Vehicle ID:</strong> {{ $booking['vehicle_id'] ?? 'N/A' }}</p>
                        <p><strong>Status:</strong> 
                            <span class="badge" style="background-color: #D39F61; color: white;">
                                {{ ucfirst($booking['status'] ?? 'Unknown') }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <h6 class="fw-bold text-muted mb-2">Payment</h6>
                        <p><strong>Type:</strong> 
                            <span class="badge rounded-pill px-3 py-2" style="background-color: #D39F61;">
                                {{ ucfirst($booking['payment_type'] ?? 'N/A') }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <a href="{{ route('bookings.edit', $booking['id']) }}" class="btn me-2" style="background-color: #B87333; color: white;">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('bookings.receipt', $booking['id']) }}" class="btn btn-success">
                <i class="bi bi-receipt me-1"></i> Generate Receipt
            </a>
        </div>
    </div>
</div>
@endsection