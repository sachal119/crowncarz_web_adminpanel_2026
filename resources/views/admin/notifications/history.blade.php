@extends('layouts.app')

@section('content')
<div class="container py-5">

    @if(session('success'))
        <div class="alert alert-success shadow-sm rounded-3 animate__animated animate__fadeInDown">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <!-- Header -->
        <div class="card-header d-flex justify-content-between align-items-center text-white"
             style="background: linear-gradient(135deg, #b08400, #000000); box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-bell-fill me-2"></i> Notification History
            </h4>
            <a href="{{ route('notifications.create') }}" class="btn btn-light text-dark fw-semibold px-4 py-2 rounded-pill shadow-sm hover-scale">
                <i class="bi bi-plus-circle me-1"></i> Add Notification
            </a>
        </div>

        <!-- Body -->
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table align-middle table-borderless">
                    <thead>
                        <tr class="bg-light text-uppercase small text-muted">
                            <th>#</th>
                            <th>Audience</th>
                            <th>Title</th>
                            <th>Message</th>
                            <th>Sent At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $id => $notification)
                            <tr class="notification-row">
                                <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    @if($notification['topic'] == 'crownCarzDriver')
                                        <span class="badge rounded-pill bg-info px-3 py-2 shadow-sm">
                                            <i class="bi bi-truck-front me-1"></i> Drivers
                                        </span>
                                    @elseif($notification['topic'] == 'crownCarzUser')
                                        <span class="badge rounded-pill bg-success px-3 py-2 shadow-sm">
                                            <i class="bi bi-person-fill me-1"></i> Users
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-warning text-dark px-3 py-2 shadow-sm">
                                            <i class="bi bi-people-fill me-1"></i> Everyone
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $notification['title'] ?? '-' }}</td>
                                <td>{{ $notification['body'] ?? '-' }}</td>
                                <td class="text-muted small">
                                    <i class="bi bi-clock-history me-1"></i> 
                                    {{ $notification['created_at'] ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-bell-slash display-6 d-block mb-2"></i>
                                    <span>No notifications found</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Extra Styling --}}
<style>
    .hover-scale {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-scale:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    }
    .notification-row {
        transition: background-color 0.25s ease, transform 0.2s ease;
    }
    /*.notification-row:hover {*/
    /*    background-color: #fff7f2;*/
    /*    transform: scale(1.01);*/
    /*}*/
</style>
@endsection
