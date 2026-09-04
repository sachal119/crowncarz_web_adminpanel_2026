@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header text-white rounded-top-4" style="background: linear-gradient(90deg, #ffb347, #ffcc33);">
            <h4 class="mb-0">
                <i class="bi bi-gear-wide-connected me-2"></i> System Settings
            </h4>
        </div>

        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-pill px-4 py-2" role="alert">
                    <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('system.settings.update') }}" method="POST">
                @csrf

                <div class="row g-4">
                    <!-- Contact Info -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><i class="bi bi-envelope-at me-1 text-primary"></i> Email</label>
                        <input type="email" name="email" value="{{ $settings['email'] ?? '' }}" class="form-control rounded-pill shadow-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><i class="bi bi-telephone me-1 text-success"></i> Phone</label>
                        <input type="text" name="phone" value="{{ $settings['phone'] ?? '' }}" class="form-control rounded-pill shadow-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><i class="bi bi-geo-alt me-1 text-danger"></i> Address</label>
                        <input type="text" name="address" value="{{ $settings['address'] ?? '' }}" class="form-control rounded-pill shadow-sm">
                    </div>

                    <!-- Social Media -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><i class="bi bi-facebook text-primary me-1"></i> Facebook</label>
                        <input type="text" name="facebook" value="{{ $settings['facebook'] ?? '' }}" class="form-control rounded-pill shadow-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><i class="bi bi-twitter text-info me-1"></i> Twitter</label>
                        <input type="text" name="twitter" value="{{ $settings['twitter'] ?? '' }}" class="form-control rounded-pill shadow-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><i class="bi bi-instagram text-danger me-1"></i> Instagram</label>
                        <input type="text" name="instagram" value="{{ $settings['instagram'] ?? '' }}" class="form-control rounded-pill shadow-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><i class="bi bi-linkedin text-primary me-1"></i> LinkedIn</label>
                        <input type="text" name="linkedin" value="{{ $settings['linkedin'] ?? '' }}" class="form-control rounded-pill shadow-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><i class="bi bi-tiktok text-dark me-1"></i> TikTok</label>
                        <input type="text" name="tiktok" value="{{ $settings['tiktok'] ?? '' }}" class="form-control rounded-pill shadow-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><i class="bi bi-snapchat text-warning me-1"></i> Snapchat</label>
                        <input type="text" name="snapchat" value="{{ $settings['snapchat'] ?? '' }}" class="form-control rounded-pill shadow-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><i class="bi bi-whatsapp text-success me-1"></i> Whatsapp</label>
                        <input type="text" name="whatsapp" value="{{ $settings['whatsapp'] ?? '' }}" class="form-control rounded-pill shadow-sm">
                    </div>

                    <!-- App Links -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><i class="bi bi-google-play text-success me-1"></i> Play Store Link</label>
                        <input type="text" name="playstore" value="{{ $settings['playstore'] ?? '' }}" class="form-control rounded-pill shadow-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><i class="bi bi-apple text-dark me-1"></i> App Store Link</label>
                        <input type="text" name="appstore" value="{{ $settings['appstore'] ?? '' }}" class="form-control rounded-pill shadow-sm">
                    </div>
                     <div class="col-md-4">
                        <label class="form-label fw-semibold"><i class="bi bi-geo-alt-fill text-primary me-1"></i> Address Autocomplete Key</label>
                        <input type="text" name="autocomplete_key" value="{{ $settings['autocomplete_key'] ?? '' }}" class="form-control rounded-pill shadow-sm">
                    </div>
                </div>

                <!-- Save Button -->
                <div class="mt-4 text-end">
                    <button class="btn btn-lg px-5 py-2 text-white shadow" style="background: linear-gradient(90deg, #ffb347, #ffcc33); border: none; border-radius: 50px;">
                        <i class="bi bi-save2 me-2"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection



