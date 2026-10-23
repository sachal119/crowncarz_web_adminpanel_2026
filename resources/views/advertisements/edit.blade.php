@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Breadcrumb & Top Bar --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('advertisements.index') }}" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left me-1"></i> Back to Advertisements
                </a>
            </div>
            <h3 class="fw-bold mb-1" style="letter-spacing: -0.5px;">Edit Mobile Advertisement</h3>
            <p class="text-muted mb-0" style="font-size: 13px;">Modify campaign details, creative media, targeting audience, and in-app action triggers.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('advertisements.index') }}" class="btn btn-outline-secondary btn-sm px-3 py-2" style="border-radius: 8px;">
                Cancel
            </a>
            <button type="submit" form="editAdForm" class="btn btn-sm px-4 py-2 fw-semibold shadow-sm" style="background: #E6B04A; color: #111827; border: none; border-radius: 8px;">
                <i class="bi bi-check2-circle me-1.5"></i> Update Advertisement
            </button>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background: rgba(239, 68, 68, 0.12); color: #b91c1c;">
            <div class="d-flex align-items-center mb-1">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                <h6 class="fw-bold mb-0">Please fix the following errors:</h6>
            </div>
            <ul class="mb-0 ps-4 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- LEFT COLUMN: EDIT FORM --}}
        <div class="col-12 col-xl-7">
            <form id="editAdForm" action="{{ route('advertisements.update', $id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Performance Stats Header Box --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: var(--cc-bg-card);">
                    <div class="card-body p-3">
                        <div class="row g-3 text-center">
                            <div class="col-4 border-end" style="border-color: var(--cc-border) !important;">
                                <div class="text-muted text-uppercase" style="font-size: 11px;">App Impressions</div>
                                <h4 class="fw-bold mb-0 text-info mt-1">{{ number_format($ad['views_count'] ?? 0) }}</h4>
                            </div>
                            <div class="col-4 border-end" style="border-color: var(--cc-border) !important;">
                                <div class="text-muted text-uppercase" style="font-size: 11px;">Total Clicks</div>
                                <h4 class="fw-bold mb-0 text-success mt-1">{{ number_format($ad['clicks_count'] ?? 0) }}</h4>
                            </div>
                            <div class="col-4">
                                <div class="text-muted text-uppercase" style="font-size: 11px;">Current Status</div>
                                <div class="mt-1">
                                    <span class="badge {{ ($ad['status'] ?? 'Active') === 'Active' ? 'bg-success' : 'bg-secondary' }} px-2.5 py-1">
                                        {{ $ad['status'] ?? 'Active' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Basic Information Card --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: var(--cc-bg-card);">
                    <div class="card-header bg-transparent border-0 pt-3 px-3 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: rgba(230, 176, 74, 0.2); color: #E6B04A;">
                                <i class="bi bi-card-heading"></i>
                            </span>
                            <h6 class="fw-bold mb-0">1. Campaign Headline & Content</h6>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small" for="adTitle">
                                Advertisement Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" id="adTitle" class="form-control" required
                                   value="{{ old('title', $ad['title'] ?? '') }}"
                                   style="background: var(--cc-input-bg); border-color: var(--cc-input-border); color: var(--cc-input-color); border-radius: 8px;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small" for="adSubtitle">
                                Subtitle / Description
                            </label>
                            <textarea name="subtitle" id="adSubtitle" rows="2" class="form-control"
                                      style="background: var(--cc-input-bg); border-color: var(--cc-input-border); color: var(--cc-input-color); border-radius: 8px;">{{ old('subtitle', $ad['subtitle'] ?? '') }}</textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small" for="adBadge">
                                    Highlight Badge Text
                                </label>
                                <input type="text" name="badge_text" id="adBadge" class="form-control"
                                       placeholder="e.g. SPECIAL, VIP, NEW"
                                       value="{{ old('badge_text', $ad['badge_text'] ?? '') }}"
                                       style="background: var(--cc-input-bg); border-color: var(--cc-input-border); color: var(--cc-input-color); border-radius: 8px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small" for="adButtonText">
                                    Call To Action Button Label
                                </label>
                                <input type="text" name="button_text" id="adButtonText" class="form-control"
                                       placeholder="e.g. Learn More, Book Now"
                                       value="{{ old('button_text', $ad['button_text'] ?? 'Learn More') }}"
                                       style="background: var(--cc-input-bg); border-color: var(--cc-input-border); color: var(--cc-input-color); border-radius: 8px;">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold small" for="adActionType">
                                    Button Click Action <span class="text-danger">*</span>
                                </label>
                                <select name="action_type" id="adActionType" class="form-select" required
                                        style="background: var(--cc-input-bg); border-color: var(--cc-input-border); color: var(--cc-input-color); border-radius: 8px;">
                                    <option value="external_url" {{ old('action_type', $ad['action_type'] ?? 'external_url') == 'external_url' ? 'selected' : '' }}>Open URL in Internal App Browser (Default)</option>
                                    <option value="in_app_screen" {{ old('action_type', $ad['action_type'] ?? '') == 'in_app_screen' ? 'selected' : '' }}>Navigate to In-App Screen (e.g. Booking)</option>
                                    <option value="call_phone" {{ old('action_type', $ad['action_type'] ?? '') == 'call_phone' ? 'selected' : '' }}>Dial Telephone Number</option>
                                    <option value="none" {{ old('action_type', $ad['action_type'] ?? '') == 'none' ? 'selected' : '' }}>None (Display only)</option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label fw-semibold small" for="adActionValue" id="adActionValueLabel">
                                    Webpage Link / Target URL <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: var(--cc-input-bg); border-color: var(--cc-input-border); color: #E6B04A;">
                                        <i class="bi bi-globe2"></i>
                                    </span>
                                    <input type="text" name="action_value" id="adActionValue" class="form-control"
                                           placeholder="https://crowncarz.com/..."
                                           value="{{ old('action_value', $ad['action_value'] ?? 'https://crowncarz.com') }}"
                                           style="background: var(--cc-input-bg); border-color: var(--cc-input-border); color: var(--cc-input-color); border-radius: 0 8px 8px 0;">
                                </div>
                                <div class="form-text text-muted small" id="adActionValueHelp">
                                    When the user taps "Learn More", this URL will open inside the Crown Carz app's internal browser.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Creative / Image Upload Card --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: var(--cc-bg-card);">
                    <div class="card-header bg-transparent border-0 pt-3 px-3 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: rgba(59, 130, 246, 0.2); color: #3b82f6;">
                                <i class="bi bi-image"></i>
                            </span>
                            <h6 class="fw-bold mb-0">2. Ad Media / Creative Banner</h6>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        {{-- Current Image Preview --}}
                        @if(!empty($ad['image_url']))
                            <div class="mb-3 p-2.5 rounded-3 border" style="background: var(--cc-bg-main); border-color: var(--cc-border) !important;">
                                <label class="form-label fw-semibold small text-muted text-uppercase mb-1" style="font-size: 11px;">Current Image:</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width: 120px; height: 65px; border-radius: 8px; overflow: hidden; border: 1px solid var(--cc-border);">
                                        <img src="{{ $ad['image_url'] }}" id="currentImgElement" alt="Current Banner" class="w-100 h-100 object-fit-cover">
                                    </div>
                                    <div class="text-truncate small">
                                        <div class="fw-semibold text-truncate mb-1" style="max-width: 320px;">{{ $ad['image_url'] }}</div>
                                        <a href="{{ $ad['image_url'] }}" target="_blank" class="text-info text-decoration-none">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> Open full size
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Replace with New Image File</label>
                            <input type="file" name="image_file" id="adImageFile" class="form-control" accept="image/*"
                                   style="background: var(--cc-input-bg); border-color: var(--cc-input-border); color: var(--cc-input-color); border-radius: 8px;">
                            <div class="form-text text-muted small">Leave empty to keep the existing image. Supported: JPG, PNG, WEBP.</div>
                        </div>

                        <div class="d-flex align-items-center my-2 text-muted small">
                            <hr class="flex-grow-1 my-0">
                            <span class="px-2 text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">OR CHANGE IMAGE URL</span>
                            <hr class="flex-grow-1 my-0">
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-semibold small" for="adImageUrl">Image URL</label>
                            <input type="url" name="image_url" id="adImageUrl" class="form-control"
                                   placeholder="https://example.com/images/banner.jpg"
                                   value="{{ old('image_url', $ad['image_url'] ?? '') }}"
                                   style="background: var(--cc-input-bg); border-color: var(--cc-input-border); color: var(--cc-input-color); border-radius: 8px;">
                        </div>
                    </div>
                </div>

                {{-- Targeting & Placement Card --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: var(--cc-bg-card);">
                    <div class="card-header bg-transparent border-0 pt-3 px-3 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: rgba(16, 185, 129, 0.2); color: #10b981;">
                                <i class="bi bi-bullseye"></i>
                            </span>
                            <h6 class="fw-bold mb-0">3. Customer App Screen Placement</h6>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        {{-- Audience hidden - strictly for Customer App --}}
                        <input type="hidden" name="target_audience" id="adAudience" value="{{ old('target_audience', $ad['target_audience'] ?? 'customer') }}">

                        <div class="mb-3">
                            <label class="form-label fw-semibold small" for="adPlacement">
                                Screen Placement in Customer App <span class="text-danger">*</span>
                            </label>
                            <select name="placement" id="adPlacement" class="form-select" required
                                    style="background: var(--cc-input-bg); border-color: var(--cc-input-border); color: var(--cc-input-color); border-radius: 8px;">
                                <option value="home_banner" {{ old('placement', $ad['placement'] ?? '') == 'home_banner' ? 'selected' : '' }}>Home Screen Top Banner (Carousel / Slider)</option>
                                <option value="popup_modal" {{ old('placement', $ad['placement'] ?? '') == 'popup_modal' ? 'selected' : '' }}>App Launch Pop-up Dialog (Modal)</option>
                                <option value="booking_screen" {{ old('placement', $ad['placement'] ?? '') == 'booking_screen' ? 'selected' : '' }}>Booking & Ride Details Screen Banner</option>
                            </select>
                            <div class="form-text text-muted small">Choose where this promotion will appear inside the Crown Carz Customer app.</div>
                        </div>


                    </div>
                </div>

                {{-- Schedule & Priority Card --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: var(--cc-bg-card);">
                    <div class="card-header bg-transparent border-0 pt-3 px-3 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: rgba(139, 92, 246, 0.2); color: #8b5cf6;">
                                <i class="bi bi-clock-history"></i>
                            </span>
                            <h6 class="fw-bold mb-0">4. Campaign Schedule & Priority</h6>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small" for="adStartDate">Start Date (Optional)</label>
                                <input type="date" name="start_date" id="adStartDate" class="form-control"
                                       value="{{ old('start_date', $ad['start_date'] ?? '') }}"
                                       style="background: var(--cc-input-bg); border-color: var(--cc-input-border); color: var(--cc-input-color); border-radius: 8px;">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small" for="adEndDate">End / Expiry Date (Optional)</label>
                                <input type="date" name="end_date" id="adEndDate" class="form-control"
                                       value="{{ old('end_date', $ad['end_date'] ?? '') }}"
                                       style="background: var(--cc-input-bg); border-color: var(--cc-input-border); color: var(--cc-input-color); border-radius: 8px;">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small" for="adPriority">Display Priority</label>
                                <input type="number" name="priority" id="adPriority" class="form-control" min="1" max="9999"
                                       value="{{ old('priority', $ad['priority'] ?? 1) }}"
                                       style="background: var(--cc-input-bg); border-color: var(--cc-input-border); color: var(--cc-input-color); border-radius: 8px;">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small" for="adStatus">Publish Status <span class="text-danger">*</span></label>
                                <select name="status" id="adStatus" class="form-select" required
                                        style="background: var(--cc-input-bg); border-color: var(--cc-input-border); color: var(--cc-input-color); border-radius: 8px;">
                                    <option value="Active" {{ old('status', $ad['status'] ?? 'Active') == 'Active' ? 'selected' : '' }}>Active (Live immediately in apps)</option>
                                    <option value="Inactive" {{ old('status', $ad['status'] ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive (Draft / Hidden)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mb-4">
                    <a href="{{ route('advertisements.index') }}" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 8px;">Cancel</a>
                    <button type="submit" class="btn px-4 py-2 fw-semibold shadow-sm" style="background: #E6B04A; color: #111827; border: none; border-radius: 8px;">
                        <i class="bi bi-check2-circle me-1.5"></i> Update Advertisement
                    </button>
                </div>
            </form>
        </div>

        {{-- RIGHT COLUMN: REALTIME PHONE PREVIEW --}}
        <div class="col-12 col-xl-5">
            <div class="position-sticky" style="top: 80px;">
                <div class="card border-0 shadow-sm" style="border-radius: 16px; background: var(--cc-bg-card);">
                    <div class="card-header bg-transparent border-0 pt-3 px-3 pb-0 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-phone-fill text-warning fs-5"></i>
                            <div>
                                <h6 class="fw-bold mb-0">Live Mobile App Preview</h6>
                                <small class="text-muted" style="font-size: 11px;">Updates in real time as you edit</small>
                            </div>
                        </div>
                        <span class="badge {{ ($ad['status'] ?? 'Active') === 'Active' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary' }}" id="livePreviewStatusBadge">
                            {{ $ad['status'] ?? 'Active' }}
                        </span>
                    </div>

                    <div class="card-body p-3 d-flex flex-column align-items-center">
                        {{-- Phone Mockup Shell --}}
                        <div class="phone-mockup shadow-lg position-relative" style="width: 300px; height: 550px; background: #000; border-radius: 40px; padding: 10px; box-shadow: 0 20px 40px -10px rgba(0,0,0,0.5), inset 0 0 2px 2px #334155;">
                            
                            {{-- Phone Screen Inside --}}
                            <div class="w-100 h-100 position-relative overflow-hidden" style="background: #0f172a; border-radius: 30px; display: flex; flex-direction: column; color: #fff;">
                                
                                {{-- Notch --}}
                                <div class="position-absolute top-0 start-50 translate-middle-x mt-2" style="width: 80px; height: 16px; background: #000; border-radius: 10px; z-index: 100;"></div>

                                {{-- Top Status & App Bar --}}
                                <div class="px-3 pt-3.5 pb-2 d-flex align-items-center justify-content-between" style="background: #1e293b;">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <img src="{{ asset('public/images/logo.png') }}" style="height: 18px; width: auto;" alt="Crown Carz">
                                        <span class="fw-bold" style="font-size: 12px; color: #E6B04A;" id="liveAppHeader">
                                            {{ ($ad['target_audience'] ?? '') === 'driver' ? 'Crown Driver' : 'Crown Carz' }}
                                        </span>
                                    </div>
                                    <i class="bi bi-bell-fill text-warning" style="font-size: 12px;"></i>
                                </div>

                                {{-- Screen Body --}}
                                <div class="flex-grow-1 p-2.5 overflow-auto position-relative" style="font-size: 11px;">
                                    
                                    {{-- Banner Section --}}
                                    <div id="liveBannerSection">
                                        <div class="d-flex align-items-center justify-content-between mb-1.5 px-1">
                                            <span class="fw-bold text-muted text-uppercase" style="font-size: 10px;">Featured Promotion</span>
                                            <span class="badge bg-warning text-dark" style="font-size: 8px;">LIVE</span>
                                        </div>

                                        {{-- Banner Card --}}
                                        <div class="card border-0 mb-3 overflow-hidden shadow-sm" style="border-radius: 12px; background: #1e293b;">
                                            <div class="position-relative" style="height: 115px; overflow: hidden; background: #000;">
                                                <img id="liveBannerImg" src="{{ $ad['image_url'] ?? asset('public/images/Car.png') }}" class="w-100 h-100 object-fit-cover" alt="Banner Preview">
                                                <span id="liveBadge" class="position-absolute top-0 start-0 badge bg-danger m-1.5 {{ empty($ad['badge_text']) ? 'd-none' : '' }}" style="font-size: 8.5px;">
                                                    {{ $ad['badge_text'] ?? '' }}
                                                </span>
                                            </div>
                                            <div class="p-2">
                                                <div class="fw-bold text-white mb-0.5" id="liveTitle" style="font-size: 12.5px; line-height: 1.2;">
                                                    {{ $ad['title'] ?? 'Advertisement Title' }}
                                                </div>
                                                <div class="text-slate-300 small mb-2 text-truncate" id="liveSub" style="font-size: 10px; color: #94a3b8;">
                                                    {{ $ad['subtitle'] ?? '' }}
                                                </div>
                                                <button type="button" class="btn btn-sm w-100 fw-bold" id="liveBtn" style="background: #E6B04A; color: #111827; border-radius: 6px; font-size: 10.5px; padding: 4px;">
                                                    {{ $ad['button_text'] ?? 'Learn More' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Pop-up Modal Simulation on Phone Screen --}}
                                    <div id="livePopupSection" class="{{ ($ad['placement'] ?? '') === 'popup_modal' ? '' : 'd-none' }} position-absolute inset-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center p-2" style="background: rgba(0,0,0,0.75); backdrop-filter: blur(2px); z-index: 50; top: 0; left: 0;">
                                        <div class="card border-0 shadow-lg position-relative overflow-hidden" style="width: 95%; border-radius: 16px; background: #1e293b;">
                                            <div class="position-relative" style="height: 105px; overflow: hidden;">
                                                <img id="livePopupImg" src="{{ $ad['image_url'] ?? asset('public/images/Car.png') }}" class="w-100 h-100 object-fit-cover" alt="Popup Preview">
                                                <span id="livePopupBadge" class="position-absolute top-0 start-0 badge bg-danger m-1.5 {{ empty($ad['badge_text']) ? 'd-none' : '' }}" style="font-size: 8px;">
                                                    {{ $ad['badge_text'] ?? '' }}
                                                </span>
                                            </div>
                                            <div class="p-2.5 text-center">
                                                <h6 class="fw-bold text-white mb-1" id="livePopupTitle" style="font-size: 12px;">{{ $ad['title'] ?? '' }}</h6>
                                                <p class="text-muted small mb-2" id="livePopupSub" style="font-size: 9.5px;">{{ $ad['subtitle'] ?? '' }}</p>
                                                <button type="button" class="btn btn-sm w-100 fw-bold" id="livePopupBtn" style="background: #E6B04A; color: #111827; border-radius: 6px; font-size: 10px; padding: 4px;">
                                                    {{ $ad['button_text'] ?? 'Learn More' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Mock Screen Content Underneath --}}
                                    <div class="p-2 rounded-2 mb-2" style="background: #1e293b;">
                                        <div class="d-flex align-items-center gap-1.5 mb-1.5 text-muted" style="font-size: 9.5px;">
                                            <i class="bi bi-geo-alt-fill text-danger"></i> Where to?
                                        </div>
                                        <div class="p-1.5 rounded-1" style="background: #0f172a; border: 1px solid #334155; font-size: 9.5px; color: #64748b;">
                                            Enter destination...
                                        </div>
                                    </div>

                                    <div class="row g-1 text-center">
                                        <div class="col-4">
                                            <div class="p-1.5 rounded-2" style="background: #1e293b;">
                                                <i class="bi bi-airplane-engines text-warning"></i>
                                                <div style="font-size: 8.5px; margin-top: 1px;">Airports</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-1.5 rounded-2" style="background: #1e293b;">
                                                <i class="bi bi-shield-check text-success"></i>
                                                <div style="font-size: 8.5px; margin-top: 1px;">VIP Fleet</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-1.5 rounded-2" style="background: #1e293b;">
                                                <i class="bi bi-clock-history text-info"></i>
                                                <div style="font-size: 8.5px; margin-top: 1px;">Schedule</div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                {{-- Bottom Navigation Mock --}}
                                <div class="px-2 py-1.5 d-flex justify-content-around text-center" style="background: #1e293b; border-top: 1px solid #334155; font-size: 8.5px; color: #94a3b8;">
                                    <div style="color: #E6B04A;"><i class="bi bi-house-door-fill d-block"></i> Home</div>
                                    <div><i class="bi bi-clock-history d-block"></i> Rides</div>
                                    <div><i class="bi bi-wallet2 d-block"></i> Wallet</div>
                                    <div><i class="bi bi-person d-block"></i> Profile</div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Real-time preview synchronizer script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const titleInput = document.getElementById('adTitle');
        const subInput = document.getElementById('adSubtitle');
        const badgeInput = document.getElementById('adBadge');
        const btnInput = document.getElementById('adButtonText');
        const imageFileInput = document.getElementById('adImageFile');
        const imageUrlInput = document.getElementById('adImageUrl');
        const audienceSelect = document.getElementById('adAudience');
        const placementSelect = document.getElementById('adPlacement');
        const statusSelect = document.getElementById('adStatus');
        const actionTypeSelect = document.getElementById('adActionType');
        const actionValueLabel = document.getElementById('adActionValueLabel');
        const actionValueInput = document.getElementById('adActionValue');

        // Target preview elements
        const liveTitle = document.getElementById('liveTitle');
        const liveSub = document.getElementById('liveSub');
        const liveBadge = document.getElementById('liveBadge');
        const liveBtn = document.getElementById('liveBtn');
        const liveBannerImg = document.getElementById('liveBannerImg');

        const livePopupSection = document.getElementById('livePopupSection');
        const livePopupTitle = document.getElementById('livePopupTitle');
        const livePopupSub = document.getElementById('livePopupSub');
        const livePopupBadge = document.getElementById('livePopupBadge');
        const livePopupBtn = document.getElementById('livePopupBtn');
        const livePopupImg = document.getElementById('livePopupImg');

        const liveAppHeader = document.getElementById('liveAppHeader');
        const livePreviewStatusBadge = document.getElementById('livePreviewStatusBadge');

        function syncPreview() {
            const titleVal = titleInput.value.trim() || 'Your Ad Title Here';
            const subVal = subInput.value.trim() || 'Offer conditions or details.';
            const badgeVal = badgeInput.value.trim();
            const btnVal = btnInput.value.trim() || 'Learn More';
            const audVal = audienceSelect.value;
            const placeVal = placementSelect.value;
            const statusVal = statusSelect.value;

            // Update Titles
            liveTitle.innerText = titleVal;
            liveSub.innerText = subVal;
            liveBtn.innerText = btnVal;

            livePopupTitle.innerText = titleVal;
            livePopupSub.innerText = subVal;
            livePopupBtn.innerText = btnVal;

            // Update Badges
            if (badgeVal) {
                liveBadge.innerText = badgeVal;
                liveBadge.classList.remove('d-none');
                livePopupBadge.innerText = badgeVal;
                livePopupBadge.classList.remove('d-none');
            } else {
                liveBadge.classList.add('d-none');
                livePopupBadge.classList.add('d-none');
            }

            // Update App Header depending on audience
            if (audVal === 'driver') {
                liveAppHeader.innerText = 'Crown Driver';
            } else {
                liveAppHeader.innerText = 'Crown Carz';
            }

            // Update Placement View
            if (placeVal === 'popup_modal') {
                livePopupSection.classList.remove('d-none');
            } else {
                livePopupSection.classList.add('d-none');
            }

            // Update Status Badge
            livePreviewStatusBadge.innerText = statusVal;
            if (statusVal === 'Active') {
                livePreviewStatusBadge.className = 'badge bg-success-subtle text-success border border-success-subtle';
            } else {
                livePreviewStatusBadge.className = 'badge bg-secondary-subtle text-secondary border border-secondary-subtle';
            }
        }

        // Action type dynamic label helper
        function updateActionTypeLabel() {
            const type = actionTypeSelect.value;
            const helpEl = document.getElementById('adActionValueHelp');
            if (type === 'external_url') {
                actionValueLabel.innerHTML = 'Webpage Link / Target URL <span class="text-danger">*</span>';
                actionValueInput.placeholder = 'https://crowncarz.com/...';
                if (helpEl) helpEl.innerText = "When the user taps 'Learn More', this URL will open inside the Crown Carz app's internal browser.";
            } else if (type === 'in_app_screen') {
                actionValueLabel.innerHTML = 'Screen Route / Identifier <span class="text-danger">*</span>';
                actionValueInput.placeholder = 'e.g. booking, fleet, wallet, profile';
                if (helpEl) helpEl.innerText = 'In-app screen route the customer will be navigated to (e.g. booking, fleet).';
            } else if (type === 'call_phone') {
                actionValueLabel.innerHTML = 'Phone Number to Call <span class="text-danger">*</span>';
                actionValueInput.placeholder = '+441189474747';
                if (helpEl) helpEl.innerText = 'Phone dialer will open pre-filled with this customer support / booking number.';
            } else {
                actionValueLabel.innerText = 'Action Target (None)';
                actionValueInput.placeholder = 'Display only (no link)';
                if (helpEl) helpEl.innerText = 'No tap action will be performed (display-only advertisement banner).';
            }
        }

        // Handle Image File selection preview
        imageFileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    liveBannerImg.src = e.target.result;
                    livePopupImg.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Handle Image URL input preview
        imageUrlInput.addEventListener('input', function () {
            if (this.value.trim() && !imageFileInput.files.length) {
                liveBannerImg.src = this.value.trim();
                livePopupImg.src = this.value.trim();
            }
        });

        titleInput.addEventListener('input', syncPreview);
        subInput.addEventListener('input', syncPreview);
        badgeInput.addEventListener('input', syncPreview);
        btnInput.addEventListener('input', syncPreview);
        audienceSelect.addEventListener('change', syncPreview);
        placementSelect.addEventListener('change', syncPreview);
        statusSelect.addEventListener('change', syncPreview);
        actionTypeSelect.addEventListener('change', updateActionTypeLabel);

        updateActionTypeLabel();
        syncPreview();
    });
</script>
@endsection
