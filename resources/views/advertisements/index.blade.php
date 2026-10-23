@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Breadcrumb & Top Bar --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(230, 176, 74, 0.15); color: #E6B04A; font-weight: 600; font-size: 11px;">
                    <i class="bi bi-megaphone-fill me-1"></i> MARKETING & CAMPAIGNS
                </span>
                <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6; font-weight: 600; font-size: 11px;">
                    <i class="bi bi-phone me-1"></i> PASSENGER & DRIVER APPS
                </span>
            </div>
            <h3 class="fw-bold mb-1" style="letter-spacing: -0.5px;">Mobile App Advertisements</h3>
            <p class="text-muted mb-0" style="font-size: 13px;">Manage in-app promotional banners, pop-up announcements, discounts, and campaigns displayed in the Crown Carz mobile apps.</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            {{-- App Integration & API Docs Modal Trigger --}}
            <button type="button" class="btn btn-outline-info btn-sm px-3 py-2 d-flex align-items-center shadow-sm" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#appIntegrationModal">
                <i class="bi bi-code-slash me-1.5"></i> App API & Flutter Docs
            </button>

            {{-- Live Phone Simulator Trigger --}}
            <button type="button" class="btn btn-outline-secondary btn-sm px-3 py-2 d-flex align-items-center shadow-sm" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#phoneSimulatorModal">
                <i class="bi bi-phone-fill me-1.5"></i> Live App Preview
            </button>

            {{-- Quick Seed Samples (if empty or admin wants demo) --}}
            <form action="{{ route('advertisements.seedSamples') }}" method="POST" class="d-inline" onsubmit="return confirm('Load Crown Carz sample advertisements into Firebase?');">
                @csrf
                <button type="submit" class="btn btn-outline-warning btn-sm px-3 py-2 d-flex align-items-center shadow-sm" style="border-radius: 8px;" title="Load ready-made luxury car ads">
                    <i class="bi bi-magic me-1.5"></i> Load Sample Ads
                </button>
            </form>

            {{-- Add New Ad --}}
            <a href="{{ route('advertisements.create') }}" class="btn btn-sm px-3.5 py-2 d-flex align-items-center shadow-sm fw-semibold" style="background: #E6B04A; color: #111827; border: none; border-radius: 8px;">
                <i class="bi bi-plus-circle-fill me-1.5 fs-6"></i> Add Advertisement
            </a>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3 d-flex align-items-center" role="alert" style="border-radius: 10px; background: rgba(34, 197, 94, 0.12); color: #15803d;">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3 d-flex align-items-center" role="alert" style="border-radius: 10px; background: rgba(239, 68, 68, 0.12); color: #b91c1c;">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Statistics Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: var(--cc-bg-card);">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Total Ads</div>
                        <h3 class="fw-bold mb-0 mt-1" style="color: var(--cc-text-primary);">{{ $stats['total'] }}</h3>
                        <small class="text-muted" style="font-size: 11px;">All campaigns</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(230, 176, 74, 0.15); color: #E6B04A;">
                        <i class="bi bi-badge-ad fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: var(--cc-bg-card);">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Live in Apps</div>
                        <h3 class="fw-bold mb-0 mt-1 text-success">{{ $stats['active'] }}</h3>
                        <small class="text-success" style="font-size: 11px;">Active & visible</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(34, 197, 94, 0.15); color: #16a34a;">
                        <i class="bi bi-broadcast-pin fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: var(--cc-bg-card);">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Audience Scope</div>
                        <div class="d-flex align-items-center gap-1 mt-1">
                            <span class="badge bg-primary" title="Customer App">{{ $stats['customer'] }} User</span>
                            <span class="badge bg-secondary" title="Driver App">{{ $stats['driver'] }} Driver</span>
                            <span class="badge bg-info" title="All Apps">{{ $stats['all'] }} All</span>
                        </div>
                        <small class="text-muted mt-1 d-block" style="font-size: 11px;">Audience target</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: var(--cc-bg-card);">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">User Engagement</div>
                        <h3 class="fw-bold mb-0 mt-1" style="color: #06b6d4;">{{ number_format($stats['total_clicks']) }} <span style="font-size: 13px; font-weight: normal; color: var(--cc-text-secondary);">clicks</span></h3>
                        <small class="text-muted" style="font-size: 11px;">{{ number_format($stats['total_views']) }} total views</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(6, 182, 212, 0.15); color: #06b6d4;">
                        <i class="bi bi-cursor-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter, Search & View Controls --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: var(--cc-bg-card);">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                {{-- Search Bar --}}
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text border-end-0" style="background: var(--cc-input-bg); border-color: var(--cc-input-border); color: var(--cc-text-secondary);">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="adSearchInput" class="form-control border-start-0" placeholder="Search by title, badge, action..." style="background: var(--cc-input-bg); border-color: var(--cc-input-border); color: var(--cc-input-color); font-size: 13px;">
                    </div>
                </div>

                {{-- Audience Filter --}}
                <div class="col-6 col-md-2">
                    <select id="audienceFilter" class="form-select" style="background: var(--cc-input-bg); border-color: var(--cc-input-border); color: var(--cc-input-color); font-size: 13px;">
                        <option value="all">All Audiences</option>
                        <option value="customer">Customer App Only</option>
                        <option value="driver">Driver App Only</option>
                        <option value="all_users">Both (All Users)</option>
                    </select>
                </div>

                {{-- Placement Filter --}}
                <div class="col-6 col-md-2">
                    <select id="placementFilter" class="form-select" style="background: var(--cc-input-bg); border-color: var(--cc-input-border); color: var(--cc-input-color); font-size: 13px;">
                        <option value="all">All Placements</option>
                        <option value="home_banner">Home Banner</option>
                        <option value="popup_modal">Pop-up Dialog</option>
                        <option value="booking_screen">Booking Screen</option>
                        <option value="driver_feed">Driver Feed</option>
                    </select>
                </div>

                {{-- Status Filter --}}
                <div class="col-6 col-md-2">
                    <select id="statusFilter" class="form-select" style="background: var(--cc-input-bg); border-color: var(--cc-input-border); color: var(--cc-input-color); font-size: 13px;">
                        <option value="all">All Statuses</option>
                        <option value="Active">Active Only</option>
                        <option value="Inactive">Inactive Only</option>
                    </select>
                </div>

                {{-- View Toggle (Table / Grid) --}}
                <div class="col-6 col-md-2 text-md-end">
                    <div class="btn-group shadow-sm" role="group">
                        <button type="button" id="tableViewBtn" class="btn btn-sm btn-outline-secondary active" onclick="switchView('table')">
                            <i class="bi bi-table"></i>
                        </button>
                        <button type="button" id="gridViewBtn" class="btn btn-sm btn-outline-secondary" onclick="switchView('grid')">
                            <i class="bi bi-grid-fill"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Advertisements Listing Section --}}
    @if(count($ads) === 0)
        <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 16px; background: var(--cc-bg-card);">
            <div class="card-body">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; background: rgba(230, 176, 74, 0.15); color: #E6B04A;">
                    <i class="bi bi-megaphone fs-1"></i>
                </div>
                <h4 class="fw-bold mb-2">No Advertisements Found</h4>
                <p class="text-muted mx-auto mb-4" style="max-width: 480px; font-size: 13.5px;">
                    You haven't created any mobile advertisements yet. Add an advertisement banner or click <strong>Load Sample Ads</strong> to start showcasing promotions in the passenger and driver mobile apps.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('advertisements.create') }}" class="btn px-4 py-2 fw-semibold" style="background: #E6B04A; color: #111827; border-radius: 8px;">
                        <i class="bi bi-plus-circle me-1.5"></i> Create First Ad
                    </a>
                    <form action="{{ route('advertisements.seedSamples') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary px-3 py-2" style="border-radius: 8px;">
                            <i class="bi bi-magic me-1"></i> Load Sample Ads
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else

        {{-- TABLE VIEW --}}
        <div id="tableViewWrapper" class="card border-0 shadow-sm" style="border-radius: 12px; background: var(--cc-bg-card); overflow: hidden;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="adsTable">
                    <thead style="background: var(--cc-table-th-bg); color: var(--cc-table-th-color); font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th style="width: 50px;" class="ps-3">#</th>
                            <th style="width: 110px;">Preview</th>
                            <th>Campaign / Title</th>
                            <th>Target App</th>
                            <th>Placement</th>
                            <th>Action / CTA</th>
                            <th style="width: 90px;" class="text-center">Priority</th>
                            <th style="width: 110px;" class="text-center">Live Status</th>
                            <th style="width: 140px;" class="text-center pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 13px;">
                        @foreach($ads as $id => $ad)
                            @php
                                $aud = $ad['target_audience'] ?? 'all';
                                $audBadge = match($aud) {
                                    'customer' => ['class' => 'bg-primary text-white', 'label' => 'Customer App', 'icon' => 'bi-person'],
                                    'driver' => ['class' => 'bg-secondary text-white', 'label' => 'Driver App', 'icon' => 'bi-car-front'],
                                    default => ['class' => 'bg-info text-dark', 'label' => 'All Users', 'icon' => 'bi-people-fill'],
                                };

                                $place = $ad['placement'] ?? 'home_banner';
                                $placeLabel = match($place) {
                                    'popup_modal' => 'Pop-up Dialog',
                                    'booking_screen' => 'Booking Screen',
                                    'driver_feed' => 'Driver Feed',
                                    default => 'Home Banner',
                                };

                                $actType = $ad['action_type'] ?? 'none';
                                $actLabel = match($actType) {
                                    'promo_code' => 'Promo: ' . ($ad['action_value'] ?? ''),
                                    'external_url' => 'Link: ' . Str::limit($ad['action_value'] ?? '', 20),
                                    'call_phone' => 'Call: ' . ($ad['action_value'] ?? ''),
                                    'in_app_screen' => 'Screen: ' . ($ad['action_value'] ?? ''),
                                    default => 'None (Display only)',
                                };

                                $isActive = ($ad['status'] ?? 'Active') === 'Active';
                            @endphp
                            <tr class="ad-item-row"
                                data-title="{{ strtolower($ad['title']) }}"
                                data-subtitle="{{ strtolower($ad['subtitle'] ?? '') }}"
                                data-audience="{{ $aud === 'all' ? 'all_users' : $aud }}"
                                data-placement="{{ $place }}"
                                data-status="{{ $ad['status'] ?? 'Active' }}">
                                
                                <td class="ps-3 text-muted fw-semibold" style="font-size: 11.5px;">
                                    {{ $loop->iteration }}
                                </td>

                                {{-- Image Thumbnail --}}
                                <td>
                                    <div class="position-relative" style="width: 88px; height: 50px; border-radius: 8px; overflow: hidden; border: 1px solid var(--cc-border); cursor: pointer;"
                                         onclick="previewImage('{{ $ad['image_url'] }}', '{{ addslashes($ad['title']) }}')">
                                        <img src="{{ $ad['image_url'] }}" alt="{{ $ad['title'] }}" class="w-100 h-100 object-fit-cover" onerror="this.src='{{ asset('public/images/Car.png') }}';">
                                        @if(!empty($ad['badge_text']))
                                            <span class="position-absolute top-0 start-0 badge bg-danger" style="font-size: 8.5px; border-radius: 0 0 6px 0; padding: 2px 4px;">
                                                {{ $ad['badge_text'] }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Title & Details --}}
                                <td>
                                    <div class="fw-bold mb-0" style="color: var(--cc-text-primary);">
                                        {{ $ad['title'] }}
                                    </div>
                                    @if(!empty($ad['subtitle']))
                                        <small class="text-muted d-block text-truncate" style="max-width: 320px; font-size: 12px;">
                                            {{ $ad['subtitle'] }}
                                        </small>
                                    @endif
                                    <div class="d-flex align-items-center gap-2 mt-1" style="font-size: 11px;">
                                        <span class="text-muted"><i class="bi bi-cursor me-1"></i>{{ $ad['clicks_count'] ?? 0 }} clicks</span>
                                        <span class="text-muted">•</span>
                                        <span class="text-muted"><i class="bi bi-eye me-1"></i>{{ $ad['views_count'] ?? 0 }} views</span>
                                        @if(!empty($ad['start_date']) || !empty($ad['end_date']))
                                            <span class="text-muted">•</span>
                                            <span class="text-muted"><i class="bi bi-calendar3 me-1"></i>{{ $ad['start_date'] ?? 'Now' }} ~ {{ $ad['end_date'] ?? 'Ongoing' }}</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Target App --}}
                                <td>
                                    <span class="badge {{ $audBadge['class'] }} px-2 py-1" style="font-size: 11px; font-weight: 500;">
                                        <i class="bi {{ $audBadge['icon'] }} me-1"></i>{{ $audBadge['label'] }}
                                    </span>
                                </td>

                                {{-- Placement --}}
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11.5px; font-weight: 500;">
                                        <i class="bi bi-layout-text-window-reverse me-1 text-warning"></i>{{ $placeLabel }}
                                    </span>
                                </td>

                                {{-- Action / CTA --}}
                                <td>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="badge bg-dark text-white px-2 py-1" style="font-size: 11px;">
                                            {{ $ad['button_text'] ?: 'CTA' }}
                                        </span>
                                        <small class="text-muted text-truncate" style="max-width: 140px; font-size: 11px;" title="{{ $actLabel }}">
                                            {{ $actLabel }}
                                        </small>
                                    </div>
                                </td>

                                {{-- Priority --}}
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1" style="font-size: 11px;">
                                        #{{ $ad['priority'] }}
                                    </span>
                                </td>

                                {{-- Live Status Toggle --}}
                                <td class="text-center">
                                    <div class="form-check form-switch d-inline-block p-0 m-0">
                                        <input class="form-check-input ms-0 status-toggle-checkbox" type="checkbox" role="switch"
                                               data-id="{{ $id }}"
                                               {{ $isActive ? 'checked' : '' }}
                                               onchange="toggleAdStatus('{{ $id }}', this)">
                                    </div>
                                    <span class="status-label d-block text-muted mt-0.5" id="status-label-{{ $id }}" style="font-size: 11px; font-weight: 600; color: {{ $isActive ? '#16a34a' : '#64748b' }} !important;">
                                        {{ $ad['status'] }}
                                    </span>
                                </td>

                                {{-- Action Buttons --}}
                                <td class="text-center pe-3">
                                    <div class="btn-group btn-group-sm" role="group">
                                        {{-- Live Phone Simulation for this specific ad --}}
                                        <button type="button" class="btn btn-outline-primary" title="Preview in App Phone Frame"
                                                onclick='openSingleAdPreview(@json($ad))'>
                                            <i class="bi bi-phone"></i>
                                        </button>

                                        {{-- Edit --}}
                                        <a href="{{ route('advertisements.edit', $id) }}" class="btn btn-outline-warning" title="Edit Advertisement">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        {{-- Delete --}}
                                        <button type="button" class="btn btn-outline-danger" title="Delete Advertisement"
                                                onclick="confirmDeleteAd('{{ $id }}', '{{ addslashes($ad['title']) }}')">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- GRID / CARDS VIEW --}}
        <div id="gridViewWrapper" class="row g-3 d-none">
            @foreach($ads as $id => $ad)
                @php
                    $aud = $ad['target_audience'] ?? 'all';
                    $audBadge = match($aud) {
                        'customer' => ['class' => 'bg-primary text-white', 'label' => 'Customer App'],
                        'driver' => ['class' => 'bg-secondary text-white', 'label' => 'Driver App'],
                        default => ['class' => 'bg-info text-dark', 'label' => 'All Users'],
                    };
                    $isActive = ($ad['status'] ?? 'Active') === 'Active';
                @endphp
                <div class="col-12 col-md-6 col-lg-4 ad-item-card"
                     data-title="{{ strtolower($ad['title']) }}"
                     data-subtitle="{{ strtolower($ad['subtitle'] ?? '') }}"
                     data-audience="{{ $aud === 'all' ? 'all_users' : $aud }}"
                     data-placement="{{ $ad['placement'] ?? 'home_banner' }}"
                     data-status="{{ $ad['status'] ?? 'Active' }}">
                    <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden" style="border-radius: 14px; background: var(--cc-bg-card);">
                        {{-- Card Image Banner --}}
                        <div class="position-relative" style="height: 160px; overflow: hidden; background: #0b0f19;">
                            <img src="{{ $ad['image_url'] }}" alt="{{ $ad['title'] }}" class="w-100 h-100 object-fit-cover" onerror="this.src='{{ asset('public/images/Car.png') }}';">
                            <div class="position-absolute top-0 end-0 p-2">
                                <span class="badge {{ $audBadge['class'] }} shadow-sm">{{ $audBadge['label'] }}</span>
                            </div>
                            @if(!empty($ad['badge_text']))
                                <div class="position-absolute top-0 start-0 p-2">
                                    <span class="badge bg-danger shadow-sm">{{ $ad['badge_text'] }}</span>
                                </div>
                            @endif
                            <div class="position-absolute bottom-0 start-0 w-100 p-2" style="background: linear-gradient(to top, rgba(0,0,0,0.85), transparent);">
                                <span class="badge bg-warning text-dark fw-bold">Priority #{{ $ad['priority'] }}</span>
                                <span class="badge bg-dark text-white border border-secondary">{{ ucfirst(str_replace('_', ' ', $ad['placement'])) }}</span>
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="card-body p-3 d-flex flex-column">
                            <h5 class="fw-bold mb-1" style="color: var(--cc-text-primary); font-size: 15px;">{{ $ad['title'] }}</h5>
                            <p class="text-muted small mb-3 flex-grow-1" style="font-size: 12.5px;">{{ $ad['subtitle'] ?: 'No subtitle provided.' }}</p>

                            <div class="p-2.5 rounded-3 mb-3" style="background: var(--cc-bg-main); border: 1px dashed var(--cc-border); font-size: 11.5px;">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Action Type:</span>
                                    <span class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $ad['action_type'])) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Target / Code:</span>
                                    <span class="fw-semibold text-truncate" style="max-width: 150px;">{{ $ad['action_value'] ?: '—' }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">App Analytics:</span>
                                    <span class="text-info fw-bold">{{ $ad['clicks_count'] ?? 0 }} clicks / {{ $ad['views_count'] ?? 0 }} views</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-2 border-top" style="border-color: var(--cc-border) !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="form-check form-switch p-0 m-0">
                                        <input class="form-check-input ms-0" type="checkbox" role="switch"
                                               {{ $isActive ? 'checked' : '' }}
                                               onchange="toggleAdStatus('{{ $id }}', this)">
                                    </div>
                                    <span class="small fw-semibold" style="color: {{ $isActive ? '#16a34a' : '#64748b' }};">{{ $ad['status'] }}</span>
                                </div>

                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" onclick='openSingleAdPreview(@json($ad))' title="App Phone Preview">
                                        <i class="bi bi-phone"></i>
                                    </button>
                                    <a href="{{ route('advertisements.edit', $id) }}" class="btn btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" onclick="confirmDeleteAd('{{ $id }}', '{{ addslashes($ad['title']) }}')" title="Delete">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @endif

</div>

{{-- ==========================================================
     📱 MODAL: LIVE MOBILE APP PHONE SIMULATOR
     ========================================================== --}}
<div class="modal fade" id="phoneSimulatorModal" tabindex="-1" aria-labelledby="phoneSimulatorLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; background: var(--cc-bg-card); color: var(--cc-text-primary);">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold" id="phoneSimulatorLabel">
                        <i class="bi bi-phone-fill text-warning me-1.5"></i> Live Crown Carz Mobile App Preview
                    </h5>
                    <p class="text-muted small mb-0">See exactly how your advertisements appear on the passenger and driver app screens in real-time.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                {{-- Simulator Controls --}}
                <div class="row g-2 mb-4 justify-content-center">
                    <div class="col-auto">
                        <div class="btn-group shadow-sm" role="group">
                            <input type="radio" class="btn-check" name="simAppMode" id="simModeCustomer" autocomplete="off" checked onchange="updateSimulatorView()">
                            <label class="btn btn-sm btn-outline-primary px-3" for="simModeCustomer"><i class="bi bi-person me-1"></i> Customer App</label>

                            <input type="radio" class="btn-check" name="simAppMode" id="simModeDriver" autocomplete="off" onchange="updateSimulatorView()">
                            <label class="btn btn-sm btn-outline-primary px-3" for="simModeDriver"><i class="bi bi-car-front me-1"></i> Driver App</label>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group shadow-sm" role="group">
                            <input type="radio" class="btn-check" name="simPlacement" id="simPlaceBanner" autocomplete="off" checked onchange="updateSimulatorView()">
                            <label class="btn btn-sm btn-outline-secondary px-3" for="simPlaceBanner"><i class="bi bi-layout-text-window-reverse me-1"></i> Home Banner</label>

                            <input type="radio" class="btn-check" name="simPlacement" id="simPlacePopup" autocomplete="off" onchange="updateSimulatorView()">
                            <label class="btn btn-sm btn-outline-secondary px-3" for="simPlacePopup"><i class="bi bi-window me-1"></i> Launch Popup</label>
                        </div>
                    </div>
                </div>

                {{-- Smartphone Mockup Frame --}}
                <div class="d-flex justify-content-center">
                    <div class="phone-frame shadow-2xl" style="width: 320px; height: 580px; background: #000; border-radius: 42px; padding: 12px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5), inset 0 0 2px 2px #334155; position: relative;">
                        {{-- Phone Screen Inside --}}
                        <div class="phone-screen position-relative overflow-hidden" style="width: 100%; height: 100%; background: #0f172a; border-radius: 32px; display: flex; flex-direction: column; color: #fff;">
                            
                            {{-- Phone Notch / Dynamic Island --}}
                            <div class="position-absolute top-0 start-50 translate-middle-x mt-2" style="width: 90px; height: 18px; background: #000; border-radius: 12px; z-index: 100;"></div>

                            {{-- App Top Bar --}}
                            <div class="px-3 pt-4 pb-2 d-flex align-items-center justify-content-between" style="background: #1e293b;">
                                <div class="d-flex align-items-center gap-1.5">
                                    <img src="{{ asset('public/images/logo.png') }}" style="height: 20px; width: auto;" alt="Crown Carz">
                                    <span class="fw-bold" style="font-size: 13px; color: #E6B04A;" id="simAppTitle">Crown Carz</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-bell-fill text-warning" style="font-size: 13px;"></i>
                                    <div class="rounded-circle" style="width: 24px; height: 24px; background: #334155; display: flex; align-items: center; justify-content: center; font-size: 10px;">
                                        JD
                                    </div>
                                </div>
                            </div>

                            {{-- App Screen Content Body --}}
                            <div class="flex-grow-1 p-2.5 overflow-auto" id="simScreenBody" style="font-size: 12px;">
                                
                                {{-- BANNER VIEW CONTAINER --}}
                                <div id="simBannerContainer">
                                    <div class="d-flex align-items-center justify-content-between mb-2 px-1">
                                        <span class="fw-bold" style="font-size: 11px; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px;">Special Offers</span>
                                        <span class="badge bg-warning text-dark" style="font-size: 9px;">LIVE</span>
                                    </div>

                                    {{-- The In-App Banner Card --}}
                                    <div class="card border-0 mb-3 overflow-hidden shadow-sm" id="simBannerCard" style="border-radius: 14px; background: #1e293b;">
                                        <div class="position-relative" style="height: 125px; overflow: hidden;">
                                            <img id="simBannerImg" src="{{ asset('public/images/Car.png') }}" class="w-100 h-100 object-fit-cover" alt="Banner">
                                            <span id="simBannerBadge" class="position-absolute top-0 start-0 badge bg-danger m-1.5" style="font-size: 9px;">OFFER</span>
                                        </div>
                                        <div class="p-2.5">
                                            <div class="fw-bold text-white mb-0.5" id="simBannerTitle" style="font-size: 13px; line-height: 1.2;">Airport Transfers 15% Off</div>
                                            <div class="text-slate-300 small mb-2 text-truncate" id="simBannerSub" style="font-size: 10.5px; color: #cbd5e1;">Book luxury travel with Crown Carz</div>
                                            <button type="button" class="btn btn-sm w-100 fw-bold" id="simBannerBtn" style="background: #E6B04A; color: #111827; border-radius: 8px; font-size: 11px; padding: 4px 8px;" onclick="simulateAdClick()">
                                                Claim Offer
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Mock App Search / Booking UI below banner --}}
                                    <div class="p-2.5 rounded-3 mb-2" style="background: #1e293b;">
                                        <div class="d-flex align-items-center gap-2 mb-2 text-muted" style="font-size: 10px;">
                                            <i class="bi bi-geo-alt-fill text-danger"></i>
                                            <span style="color: #94a3b8;">Where to? Book a luxury ride</span>
                                        </div>
                                        <div class="p-2 rounded-2" style="background: #0f172a; border: 1px solid #334155; font-size: 10px; color: #64748b;">
                                            Enter pickup address...
                                        </div>
                                    </div>

                                    <div class="row g-1.5 text-center">
                                        <div class="col-4">
                                            <div class="p-2 rounded-2" style="background: #1e293b;">
                                                <i class="bi bi-airplane-engines text-warning fs-6"></i>
                                                <div style="font-size: 9px; margin-top: 2px;">Airport</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 rounded-2" style="background: #1e293b;">
                                                <i class="bi bi-shield-check text-success fs-6"></i>
                                                <div style="font-size: 9px; margin-top: 2px;">VIP Exec</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 rounded-2" style="background: #1e293b;">
                                                <i class="bi bi-calendar2-check text-info fs-6"></i>
                                                <div style="font-size: 9px; margin-top: 2px;">Schedule</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- POPUP MODAL SIMULATION CONTAINER (Initially hidden or shown on toggle) --}}
                                <div id="simPopupContainer" class="d-none h-100 d-flex flex-column justify-content-center align-items-center position-relative">
                                    <div class="position-absolute inset-0 w-100 h-100" style="background: rgba(0,0,0,0.7); backdrop-filter: blur(2px); z-index: 50; top: 0; left: 0;"></div>
                                    <div class="card border-0 shadow-lg position-relative" style="z-index: 60; width: 92%; border-radius: 18px; background: #1e293b; overflow: hidden;">
                                        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-2" style="font-size: 8px; z-index: 70;" onclick="switchSimPlacement('banner')"></button>
                                        <div class="position-relative" style="height: 120px; overflow: hidden;">
                                            <img id="simPopupImg" src="{{ asset('public/images/Car.png') }}" class="w-100 h-100 object-fit-cover" alt="Popup Banner">
                                            <span id="simPopupBadge" class="position-absolute top-0 start-0 badge bg-danger m-1.5" style="font-size: 8.5px;">VIP</span>
                                        </div>
                                        <div class="p-3 text-center">
                                            <h6 class="fw-bold text-white mb-1" id="simPopupTitle" style="font-size: 13px;">Executive Luxury Fleet</h6>
                                            <p class="text-muted small mb-2.5" id="simPopupSub" style="font-size: 10.5px;">Enjoy executive comfort with Mercedes-Benz and chauffeured service across Reading & London.</p>
                                            <button type="button" class="btn btn-sm w-100 fw-bold" id="simPopupBtn" style="background: #E6B04A; color: #111827; border-radius: 8px; font-size: 11px;" onclick="simulateAdClick()">
                                                Learn More
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- App Bottom Navigation Bar --}}
                            <div class="px-3 py-2 d-flex justify-content-around text-center" style="background: #1e293b; border-top: 1px solid #334155; font-size: 9px; color: #94a3b8;">
                                <div style="color: #E6B04A;">
                                    <i class="bi bi-house-door-fill fs-6 d-block"></i> Home
                                </div>
                                <div>
                                    <i class="bi bi-clock-history fs-6 d-block"></i> Rides
                                </div>
                                <div>
                                    <i class="bi bi-wallet2 fs-6 d-block"></i> Wallet
                                </div>
                                <div>
                                    <i class="bi bi-person fs-6 d-block"></i> Profile
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Action Toast Simulator --}}
                <div id="simActionToast" class="alert alert-success d-none text-center mx-auto mt-3 py-2 px-3 shadow-sm" style="max-width: 320px; border-radius: 10px; font-size: 12px;">
                    <i class="bi bi-check2-circle me-1"></i> <span id="simToastText">Promo code copied to clipboard!</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ==========================================================
     💻 MODAL: APP DEVELOPER INTEGRATION & API DOCUMENTATION
     ========================================================== --}}
<div class="modal fade" id="appIntegrationModal" tabindex="-1" aria-labelledby="appIntegrationLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; background: var(--cc-bg-card); color: var(--cc-text-primary);">
            <div class="modal-header border-bottom border-light-subtle">
                <div>
                    <h5 class="modal-title fw-bold" id="appIntegrationLabel">
                        <i class="bi bi-code-slash text-info me-1.5"></i> Mobile App & Developer Integration Guide
                    </h5>
                    <p class="text-muted small mb-0">Use Firebase Realtime Database Stream or REST API to display active advertisements in the Flutter, iOS, and Android mobile apps.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                
                {{-- Method 1: Firebase Realtime Database (Recommended for Flutter) --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark fw-bold px-2.5 py-1">METHOD 1 (RECOMMENDED)</span>
                        <h6 class="fw-bold mb-0">Direct Firebase Realtime Database Stream (Zero Latency)</h6>
                    </div>
                    <p class="text-muted small">
                        Crown Carz uses Firebase Realtime Database. Any changes made in this admin panel will update the mobile app screens <strong>instantly</strong> without requiring an app reload!
                    </p>

                    <div class="card bg-dark text-white p-3 border-0 rounded-3 position-relative font-monospace" style="font-size: 12.5px;">
                        <button class="btn btn-sm btn-outline-light position-absolute top-0 end-0 m-2.5" onclick="copyCode('flutterCodeBlock')">
                            <i class="bi bi-clipboard"></i> Copy Flutter Code
                        </button>
<pre id="flutterCodeBlock" class="mb-0" style="white-space: pre-wrap;"><code>// 1. Listen for active advertisements in Flutter:
final DatabaseReference adsRef = FirebaseDatabase.instance.ref('advertisements');

// Listen in real-time:
Stream&lt;List&lt;Advertisement&gt;&gt; getActiveAdsStream({String audience = 'customer'}) {
  return adsRef.onValue.map((event) {
    final data = event.snapshot.value as Map&lt;dynamic, dynamic&gt;?;
    if (data == null) return [];

    final list = &lt;Advertisement&gt;[];
    data.forEach((key, val) {
      if (val is Map) {
        final ad = Advertisement.fromMap(key.toString(), Map&lt;String, dynamic&gt;.from(val));
        // Filter by Active status and target audience:
        if (ad.status == 'Active' && (ad.targetAudience == 'all' || ad.targetAudience == audience)) {
          list.add(ad);
        }
      }
    });
    // Sort by priority ascending:
    list.sort((a, b) => a.priority.compareTo(b.priority));
    return list;
  });
}</code></pre>
                    </div>
                </div>

                <hr class="border-secondary my-4">

                {{-- Method 2: REST API Endpoints --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary fw-bold px-2.5 py-1">METHOD 2</span>
                        <h6 class="fw-bold mb-0">REST API Endpoints (HTTP JSON)</h6>
                    </div>
                    <p class="text-muted small">
                        If your app uses HTTP REST API calls, use these endpoints:
                    </p>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle" style="font-size: 12.5px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">Method</th>
                                    <th>Endpoint</th>
                                    <th>Query Parameters</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-success">GET</span></td>
                                    <td><code>{{ url('/api/advertisements/active') }}</code></td>
                                    <td><code>?audience=customer|driver|all&placement=home_banner</code></td>
                                    <td>Returns all currently live and non-expired advertisements for the target app.</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-success">GET</span></td>
                                    <td><code>{{ url('/api/advertisements') }}</code></td>
                                    <td><code>?audience=customer&status=Active</code></td>
                                    <td>General advertisements query.</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-primary">POST</span></td>
                                    <td><code>{{ url('/api/advertisements/{id}/click') }}</code></td>
                                    <td>None</td>
                                    <td>Call when user taps the ad to record engagement metrics.</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-primary">POST</span></td>
                                    <td><code>{{ url('/api/advertisements/{id}/view') }}</code></td>
                                    <td>None</td>
                                    <td>Call when ad is viewed/displayed to count impressions.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Sample JSON Response --}}
                    <div class="mt-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Sample JSON Response Structure:</label>
                        <div class="card bg-dark text-white p-3 border-0 rounded-3 font-monospace" style="font-size: 12px;">
<pre class="mb-0"><code>{
  "status": true,
  "message": "Advertisements fetched successfully",
  "count": 1,
  "data": [
    {
      "id": "-OJE839u190",
      "title": "Heathrow Airport Special - 15% Off",
      "subtitle": "Use promo code AIRPORT15 at checkout",
      "image_url": "https://firebasestorage.googleapis.com/.../ad_123.jpg",
      "target_audience": "customer",
      "placement": "home_banner",
      "action_type": "promo_code",
      "action_value": "AIRPORT15",
      "button_text": "Copy Code",
      "badge_text": "HOT OFFER",
      "priority": 1,
      "status": "Active",
      "start_date": "2026-10-01",
      "end_date": "2026-12-31",
      "clicks_count": 42,
      "views_count": 318
    }
  ]
}</code></pre>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer border-top border-light-subtle">
                <a href="{{ route('api.advertisements.active') }}" target="_blank" class="btn btn-sm btn-outline-info">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Test Live API Endpoint
                </a>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ==========================================================
     🖼️ MODAL: IMAGE PREVIEW LIGHTBOX
     ========================================================== --}}
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg bg-transparent">
            <div class="modal-body p-0 text-center position-relative">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 10;"></button>
                <img id="lightboxImage" src="" class="img-fluid rounded-3 shadow-lg" alt="Ad Preview" style="max-height: 80vh; object-fit: contain;">
                <div id="lightboxCaption" class="text-white mt-2 fw-semibold" style="text-shadow: 0 2px 4px rgba(0,0,0,0.8);"></div>
            </div>
        </div>
    </div>
</div>

{{-- ==========================================================
     🗑️ MODAL: DELETE CONFIRMATION
     ========================================================== --}}
<div class="modal fade" id="deleteAdModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; background: var(--cc-bg-card); color: var(--cc-text-primary);">
            <div class="modal-body p-4 text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px; background: rgba(239, 68, 68, 0.15); color: #ef4444;">
                    <i class="bi bi-exclamation-triangle fs-3"></i>
                </div>
                <h6 class="fw-bold mb-1">Delete Advertisement?</h6>
                <p class="text-muted small mb-3" id="deleteAdTitleText">Are you sure you want to remove this advertisement?</p>
                <form id="deleteAdForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-danger px-3 fw-semibold">Yes, Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- JAVASCRIPT LOGIC --}}
<script>
    // Global list of advertisements for interactive simulator
    const allAdsData = @json(array_values($ads));

    // Filter functionality
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('adSearchInput');
        const audienceFilter = document.getElementById('audienceFilter');
        const placementFilter = document.getElementById('placementFilter');
        const statusFilter = document.getElementById('statusFilter');

        function applyFilters() {
            const query = (searchInput.value || '').toLowerCase().trim();
            const aud = audienceFilter.value;
            const place = placementFilter.value;
            const st = statusFilter.value;

            const rows = document.querySelectorAll('.ad-item-row');
            rows.forEach(row => {
                const title = row.getAttribute('data-title') || '';
                const sub = row.getAttribute('data-subtitle') || '';
                const rowAud = row.getAttribute('data-audience') || '';
                const rowPlace = row.getAttribute('data-placement') || '';
                const rowSt = row.getAttribute('data-status') || '';

                const matchesQuery = !query || title.includes(query) || sub.includes(query);
                const matchesAud = (aud === 'all') || (rowAud === aud);
                const matchesPlace = (place === 'all') || (rowPlace === place);
                const matchesSt = (st === 'all') || (rowSt === st);

                if (matchesQuery && matchesAud && matchesPlace && matchesSt) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            const cards = document.querySelectorAll('.ad-item-card');
            cards.forEach(card => {
                const title = card.getAttribute('data-title') || '';
                const sub = card.getAttribute('data-subtitle') || '';
                const cardAud = card.getAttribute('data-audience') || '';
                const cardPlace = card.getAttribute('data-placement') || '';
                const cardSt = card.getAttribute('data-status') || '';

                const matchesQuery = !query || title.includes(query) || sub.includes(query);
                const matchesAud = (aud === 'all') || (cardAud === aud);
                const matchesPlace = (place === 'all') || (cardPlace === place);
                const matchesSt = (st === 'all') || (cardSt === st);

                if (matchesQuery && matchesAud && matchesPlace && matchesSt) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', applyFilters);
        audienceFilter.addEventListener('change', applyFilters);
        placementFilter.addEventListener('change', applyFilters);
        statusFilter.addEventListener('change', applyFilters);

        // Initialize simulator with first ad
        updateSimulatorView();
    });

    // View switcher (Table vs Grid)
    function switchView(mode) {
        const table = document.getElementById('tableViewWrapper');
        const grid = document.getElementById('gridViewWrapper');
        const tableBtn = document.getElementById('tableViewBtn');
        const gridBtn = document.getElementById('gridViewBtn');

        if (mode === 'table') {
            table.classList.remove('d-none');
            grid.classList.add('d-none');
            tableBtn.classList.add('active');
            gridBtn.classList.remove('active');
        } else {
            table.classList.add('d-none');
            grid.classList.remove('d-none');
            tableBtn.classList.remove('active');
            gridBtn.classList.add('active');
        }
    }

    // Image lightbox preview
    function previewImage(url, title) {
        document.getElementById('lightboxImage').src = url;
        document.getElementById('lightboxCaption').innerText = title;
        new bootstrap.Modal(document.getElementById('imagePreviewModal')).show();
    }

    // Delete confirmation modal
    function confirmDeleteAd(id, title) {
        document.getElementById('deleteAdTitleText').innerText = `Are you sure you want to permanently delete "${title}"?`;
        document.getElementById('deleteAdForm').action = `{{ url('admin/advertisements') }}/${id}/delete`;
        new bootstrap.Modal(document.getElementById('deleteAdModal')).show();
    }

    // Toggle status via AJAX
    function toggleAdStatus(id, checkbox) {
        const label = document.getElementById(`status-label-${id}`);
        fetch(`{{ url('admin/advertisements') }}/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (label) {
                    label.innerText = data.status;
                    label.style.color = (data.status === 'Active') ? '#16a34a' : '#64748b';
                }
            } else {
                checkbox.checked = !checkbox.checked;
                alert('Error updating status: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            checkbox.checked = !checkbox.checked;
            alert('Failed to connect to server: ' + err);
        });
    }

    // Interactive Phone Simulator logic
    function updateSimulatorView(overrideAd = null) {
        const isCustomer = document.getElementById('simModeCustomer').checked;
        const isBanner = document.getElementById('simPlaceBanner').checked;

        document.getElementById('simAppTitle').innerText = isCustomer ? 'Crown Carz' : 'Crown Driver';

        const bannerContainer = document.getElementById('simBannerContainer');
        const popupContainer = document.getElementById('simPopupContainer');

        if (isBanner) {
            bannerContainer.classList.remove('d-none');
            popupContainer.classList.add('d-none');
        } else {
            bannerContainer.classList.add('d-none');
            popupContainer.classList.remove('d-none');
        }

        // Find relevant ad
        let targetAudience = isCustomer ? ['customer', 'all'] : ['driver', 'all'];
        let targetPlacement = isBanner ? 'home_banner' : 'popup_modal';

        let selected = overrideAd;
        if (!selected) {
            selected = allAdsData.find(a => 
                (a.status === 'Active') && 
                targetAudience.includes(a.target_audience) && 
                (a.placement === targetPlacement || a.placement === 'home_banner')
            ) || allAdsData[0];
        }

        if (selected) {
            // Update Banner elements
            document.getElementById('simBannerImg').src = selected.image_url || '{{ asset('public/images/Car.png') }}';
            document.getElementById('simBannerTitle').innerText = selected.title || 'Crown Carz Promotion';
            document.getElementById('simBannerSub').innerText = selected.subtitle || 'Book luxury transportation across UK.';
            document.getElementById('simBannerBtn').innerText = selected.button_text || 'Learn More';
            const bBadge = document.getElementById('simBannerBadge');
            if (selected.badge_text) {
                bBadge.innerText = selected.badge_text;
                bBadge.classList.remove('d-none');
            } else {
                bBadge.classList.add('d-none');
            }

            // Update Popup elements
            document.getElementById('simPopupImg').src = selected.image_url || '{{ asset('public/images/Car.png') }}';
            document.getElementById('simPopupTitle').innerText = selected.title || 'Crown Carz Promotion';
            document.getElementById('simPopupSub').innerText = selected.subtitle || 'Exclusive in-app announcement.';
            document.getElementById('simPopupBtn').innerText = selected.button_text || 'Learn More';
            const pBadge = document.getElementById('simPopupBadge');
            if (selected.badge_text) {
                pBadge.innerText = selected.badge_text;
                pBadge.classList.remove('d-none');
            } else {
                pBadge.classList.add('d-none');
            }

            // Save currently previewed ad for click action simulation
            window.currentPreviewedAd = selected;
        }
    }

    function switchSimPlacement(place) {
        if (place === 'banner') {
            document.getElementById('simPlaceBanner').checked = true;
        } else {
            document.getElementById('simPlacePopup').checked = true;
        }
        updateSimulatorView();
    }

    function openSingleAdPreview(ad) {
        if (ad.target_audience === 'driver') {
            document.getElementById('simModeDriver').checked = true;
        } else {
            document.getElementById('simModeCustomer').checked = true;
        }

        if (ad.placement === 'popup_modal') {
            document.getElementById('simPlacePopup').checked = true;
        } else {
            document.getElementById('simPlaceBanner').checked = true;
        }

        updateSimulatorView(ad);
        new bootstrap.Modal(document.getElementById('phoneSimulatorModal')).show();
    }

    function simulateAdClick() {
        const ad = window.currentPreviewedAd || {};
        const toast = document.getElementById('simActionToast');
        const toastText = document.getElementById('simToastText');

        if (ad.action_type === 'promo_code') {
            toastText.innerText = `Promo Code "${ad.action_value || 'PROMO'}" copied to phone clipboard!`;
        } else if (ad.action_type === 'call_phone') {
            toastText.innerText = `Dialer opened with phone: ${ad.action_value || '+44 1189 47 47 47'}`;
        } else if (ad.action_type === 'in_app_screen') {
            toastText.innerText = `Navigated to in-app screen: "${ad.action_value || 'Home'}"`;
        } else if (ad.action_type === 'external_url') {
            toastText.innerText = `Opened external browser link: ${ad.action_value || 'https://crowncarz.com'}`;
        } else {
            toastText.innerText = `Banner clicked (No redirect action configured)`;
        }

        toast.classList.remove('d-none');
        setTimeout(() => toast.classList.add('d-none'), 3500);
    }

    function copyCode(id) {
        const text = document.getElementById(id).innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert('Code snippet copied to clipboard!');
        });
    }
</script>
@endsection
