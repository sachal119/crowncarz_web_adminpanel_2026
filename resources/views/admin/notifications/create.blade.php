@extends('layouts.app')

@section('content')
<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-lg rounded-4">
        <div class="card-header text-white rounded-top-4"
             style="background: linear-gradient(90deg, #b08400, #000000);">
            <h4 class="mb-0"><i class="bi bi-send-fill me-2"></i> Send Notification</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('notifications.send') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="topic" class="form-label fw-bold">Audience</label>
                    <select name="topic" id="topic" class="form-select">
                        <option value="crownCarzDriver">🚖 Drivers</option>
                        <option value="crownCarzUser">👤 Users</option>
                        <option value="crownCarzAll">🌍 Everyone</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Title</label>
                    <input type="text" name="title" class="form-control" placeholder="Enter notification title" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Message</label>
                    <textarea name="body" class="form-control" rows="3" placeholder="Enter notification message" required></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('notifications.history') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-clock-history me-1"></i> History
                    </a>
                    <button type="submit" class="btn" style="background-color:darkkhaki">
                        🚀 Send Notification
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
