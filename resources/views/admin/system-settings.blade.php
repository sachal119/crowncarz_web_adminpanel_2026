@extends('layouts.app')

@section('content')
<div class="container py-4">

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-pill px-4 py-2 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-pill px-4 py-2 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- 🌟 WHATSAPP SAAS CONNECTION CARD -->
    <div class="card shadow-lg border-0 rounded-4 mb-4" id="whatsappConnectionCard">
        <div class="card-header text-white rounded-top-4 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2" 
             style="background: linear-gradient(135deg, #075E54 0%, #128C7E 50%, #25D366 100%);">
            <div class="d-flex align-items-center">
                <span class="rounded-circle bg-white bg-opacity-20 p-2 d-inline-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px;">
                    <i class="bi bi-whatsapp fs-4 text-white"></i>
                </span>
                <div>
                    <h4 class="mb-0 fw-bold">WhatsApp SaaS Gateway Connection</h4>
                    <div class="text-white-50 small">Configure live WhatsApp line for instant bookings, driver dispatch, and PDF report delivery</div>
                </div>
            </div>
            <div id="waStatusBadgeContainer">
                @if(($waStatus['connected'] ?? false))
                    <span class="badge bg-light text-success fw-bold px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.9rem;">
                        <i class="bi bi-patch-check-fill text-success me-1"></i> CONNECTED ({{ $waStatus['phone'] ?? 'Active' }})
                    </span>
                @elseif(($waStatus['status'] ?? '') === 'QR_REQUIRED' || ($waStatus['qr'] ?? null))
                    <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.9rem;">
                        <i class="bi bi-qr-code-scan me-1"></i> QR CODE REQUIRED
                    </span>
                @elseif(($waStatus['status'] ?? '') === 'NOT_CONFIGURED')
                    <span class="badge bg-secondary text-white fw-semibold px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.9rem;">
                        <i class="bi bi-key me-1"></i> NOT CONFIGURED
                    </span>
                @else
                    <span class="badge bg-danger text-white fw-semibold px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.9rem;">
                        <i class="bi bi-x-circle me-1"></i> {{ $waStatus['status'] ?? 'DISCONNECTED' }}
                    </span>
                @endif
            </div>
        </div>

        <div class="card-body p-4 bg-white">
            <div class="row g-4">
                <!-- Left: Configuration Form -->
                <div class="col-lg-7 border-end-lg">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-sliders me-2 text-success"></i> Gateway Credentials
                    </h5>
                    <p class="text-secondary small mb-4">
                        Enter your WhatsApp SaaS Gateway API Key and URL below. Once saved, the gateway status will update in real time.
                    </p>

                    <form id="waSettingsForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">
                                <i class="bi bi-globe me-1 text-primary"></i> Gateway Base URL
                            </label>
                            <input type="url" name="whatsapp_gateway_url" id="waGatewayUrl"
                                   value="{{ $settings['whatsapp_gateway_url'] ?? config('services.whatsapp.base_url', 'https://sachalabdullah.shop') }}" 
                                   class="form-control rounded-3 shadow-sm" 
                                   placeholder="https://sachalabdullah.shop" required>
                            <div class="form-text text-muted">The default SaaS gateway URL is <code>https://sachalabdullah.shop</code>.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">
                                <i class="bi bi-key-fill me-1 text-warning"></i> Client API Key
                            </label>
                            <div class="input-group">
                                <input type="text" name="whatsapp_api_key" id="waApiKey"
                                       value="{{ $settings['whatsapp_api_key'] ?? config('services.whatsapp.api_key', '') }}" 
                                       class="form-control rounded-start-3 shadow-sm" 
                                       placeholder="wa_live_xxxxxxxxxxxxxxxxxxxxxxxxxxxx" required>
                                <button type="button" class="btn btn-outline-secondary" id="btnToggleApiKeyMask">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="form-text text-muted">Included in every request as <code>X-API-Key</code>.</div>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <button type="submit" class="btn btn-success fw-bold px-4 py-2 rounded-3 shadow-sm" id="btnSaveWaSettings"
                                    style="background-color: #128C7E; border-color: #128C7E;">
                                <i class="bi bi-save2 me-1"></i> Save WhatsApp Credentials
                            </button>
                            <button type="button" class="btn btn-outline-secondary rounded-3 px-3 py-2" id="btnRefreshWaStatus">
                                <i class="bi bi-arrow-clockwise me-1"></i> Refresh Status
                            </button>
                        </div>
                    </form>

                    <!-- Quick Test Message Section -->
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="fw-bold text-dark mb-2">
                            <i class="bi bi-send-check me-1 text-success"></i> Send Test WhatsApp Message
                        </h6>
                        <form id="waTestForm" class="row g-2 align-items-center">
                            @csrf
                            <div class="col-sm-7">
                                <input type="text" id="waTestPhone" class="form-control rounded-3 shadow-sm" placeholder="e.g. 07123456789 or 447123456789" required>
                            </div>
                            <div class="col-sm-5">
                                <button type="submit" class="btn btn-outline-success fw-semibold w-100 rounded-3" id="btnSendWaTest">
                                    <i class="bi bi-whatsapp me-1"></i> Send Test Message
                                </button>
                            </div>
                        </form>
                        <div id="waTestResult" class="mt-2 small"></div>
                    </div>
                </div>

                <!-- Right: Live Connection Box & QR Code Scanner -->
                <div class="col-lg-5">
                    <div class="p-4 rounded-4 bg-light border text-center h-100 d-flex flex-column justify-content-center align-items-center" id="waStatusBox">
                        <div id="waStatusDetails">
                            @if(($waStatus['connected'] ?? false))
                                <div class="text-success mb-3">
                                    <i class="bi bi-check-circle-fill" style="font-size: 3.5rem;"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">WhatsApp Line Connected</h5>
                                <p class="text-secondary small mb-3">Your WhatsApp gateway is active and ready to send instant messages and PDF reports.</p>
                                <div class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fs-6 mb-2">
                                    <i class="bi bi-telephone-fill me-1"></i> {{ $waStatus['phone'] ?? 'Active' }}
                                </div>
                                @if(!empty($waStatus['tenant']['name'] ?? null))
                                    <div class="text-muted small">Tenant: <strong>{{ $waStatus['tenant']['name'] }}</strong></div>
                                @endif
                            @elseif(!empty($waStatus['qr']))
                                <h5 class="fw-bold text-dark mb-2">Scan QR Code with WhatsApp</h5>
                                <p class="text-muted small mb-3">
                                    Open WhatsApp on your phone &gt; <strong>Linked Devices</strong> &gt; <strong>Link a Device</strong> and point your camera at this QR code:
                                </p>
                                <div class="bg-white p-3 rounded-4 shadow-sm border d-inline-block mb-3">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode($waStatus['qr']) }}" 
                                         alt="WhatsApp QR Code" class="img-fluid rounded-3" style="width: 200px; height: 200px;">
                                </div>
                                <div class="text-muted small d-flex align-items-center justify-content-center gap-2">
                                    <span class="spinner-grow spinner-grow-sm text-success" role="status"></span>
                                    <span>Waiting for scan... (Auto-polling)</span>
                                </div>
                            @elseif(($waStatus['status'] ?? '') === 'NOT_CONFIGURED')
                                <div class="text-muted mb-3">
                                    <i class="bi bi-key fs-1"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">API Key Required</h5>
                                <p class="text-muted small mb-0">Please enter your WhatsApp Gateway API Key on the left and click <strong>Save WhatsApp Credentials</strong> to connect.</p>
                            @else
                                <div class="text-warning mb-3">
                                    <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">Status: {{ $waStatus['status'] ?? 'DISCONNECTED' }}</h5>
                                <p class="text-muted small mb-3">{{ $waStatus['message'] ?? 'WhatsApp is currently not connected.' }}</p>
                                <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="checkWhatsAppStatus()">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Check Now
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ⚙️ GENERAL SYSTEM SETTINGS CARD -->
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header text-white rounded-top-4" style="background: linear-gradient(90deg, #ffb347, #ffcc33);">
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-gear-wide-connected me-2"></i> General System Settings
            </h4>
        </div>

        <div class="card-body p-4 bg-white">
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
                        <label class="form-label fw-semibold"><i class="bi bi-whatsapp text-success me-1"></i> Contact WhatsApp</label>
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
                        <i class="bi bi-save2 me-2"></i> Save General Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let waPollingInterval = null;

async function checkWhatsAppStatus() {
    try {
        const res = await fetch("{{ route('whatsapp.status.ajax') }}", {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await res.json();
        renderWhatsAppStatus(data);
    } catch (err) {
        console.warn('Error checking WhatsApp status:', err);
    }
}

function renderWhatsAppStatus(data) {
    const badgeContainer = document.getElementById('waStatusBadgeContainer');
    const detailsContainer = document.getElementById('waStatusDetails');
    if (!badgeContainer || !detailsContainer) return;

    if (data.connected) {
        // Stop polling if connected
        if (waPollingInterval) {
            clearInterval(waPollingInterval);
            waPollingInterval = null;
        }

        badgeContainer.innerHTML = `
            <span class="badge bg-light text-success fw-bold px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.9rem;">
                <i class="bi bi-patch-check-fill text-success me-1"></i> CONNECTED (${data.phone || 'Active'})
            </span>
        `;

        detailsContainer.innerHTML = `
            <div class="text-success mb-3">
                <i class="bi bi-check-circle-fill" style="font-size: 3.5rem;"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">WhatsApp Line Connected</h5>
            <p class="text-secondary small mb-3">Your WhatsApp gateway is active and ready to send instant messages and PDF reports.</p>
            <div class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fs-6 mb-2">
                <i class="bi bi-telephone-fill me-1"></i> ${data.phone || 'Active'}
            </div>
            ${data.tenant?.name ? `<div class="text-muted small">Tenant: <strong>${data.tenant.name}</strong></div>` : ''}
        `;
    } else if (data.qr) {
        // Start polling if not already started
        if (!waPollingInterval) {
            waPollingInterval = setInterval(checkWhatsAppStatus, 5000);
        }

        badgeContainer.innerHTML = `
            <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.9rem;">
                <i class="bi bi-qr-code-scan me-1"></i> QR CODE REQUIRED
            </span>
        `;

        const qrImgUrl = `https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=${encodeURIComponent(data.qr)}`;

        detailsContainer.innerHTML = `
            <h5 class="fw-bold text-dark mb-2">Scan QR Code with WhatsApp</h5>
            <p class="text-muted small mb-3">
                Open WhatsApp on your phone &gt; <strong>Linked Devices</strong> &gt; <strong>Link a Device</strong> and point your camera at this QR code:
            </p>
            <div class="bg-white p-3 rounded-4 shadow-sm border d-inline-block mb-3">
                <img src="${qrImgUrl}" alt="WhatsApp QR Code" class="img-fluid rounded-3" style="width: 200px; height: 200px;">
            </div>
            <div class="text-muted small d-flex align-items-center justify-content-center gap-2">
                <span class="spinner-grow spinner-grow-sm text-success" role="status"></span>
                <span>Waiting for scan... (Auto-polling)</span>
            </div>
        `;
    } else if (data.status === 'NOT_CONFIGURED') {
        badgeContainer.innerHTML = `
            <span class="badge bg-secondary text-white fw-semibold px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.9rem;">
                <i class="bi bi-key me-1"></i> NOT CONFIGURED
            </span>
        `;
        detailsContainer.innerHTML = `
            <div class="text-muted mb-3">
                <i class="bi bi-key fs-1"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">API Key Required</h5>
            <p class="text-muted small mb-0">Please enter your WhatsApp Gateway API Key on the left and click <strong>Save WhatsApp Credentials</strong> to connect.</p>
        `;
    } else {
        badgeContainer.innerHTML = `
            <span class="badge bg-danger text-white fw-semibold px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.9rem;">
                <i class="bi bi-x-circle me-1"></i> ${data.status || 'DISCONNECTED'}
            </span>
        `;
        detailsContainer.innerHTML = `
            <div class="text-warning mb-3">
                <i class="bi bi-exclamation-triangle-fill fs-1"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">Status: ${data.status || 'DISCONNECTED'}</h5>
            <p class="text-muted small mb-3">${data.message || 'WhatsApp is currently not connected.'}</p>
            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="checkWhatsAppStatus()">
                <i class="bi bi-arrow-clockwise me-1"></i> Check Now
            </button>
        `;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Save WhatsApp Settings via AJAX
    const waForm = document.getElementById('waSettingsForm');
    const btnSave = document.getElementById('btnSaveWaSettings');

    if (waForm) {
        waForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            btnSave.disabled = true;
            btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

            const payload = {
                whatsapp_gateway_url: document.getElementById('waGatewayUrl').value.trim(),
                whatsapp_api_key: document.getElementById('waApiKey').value.trim(),
                _token: "{{ csrf_token() }}"
            };

            try {
                const res = await fetch("{{ route('whatsapp.settings.save') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    alert('✅ ' + data.message);
                    checkWhatsAppStatus();
                } else {
                    alert('❌ ' + (data.message || 'Failed to save WhatsApp credentials.'));
                }
            } catch (err) {
                alert('⚠️ Network error saving WhatsApp credentials.');
            } finally {
                btnSave.disabled = false;
                btnSave.innerHTML = '<i class="bi bi-save2 me-1"></i> Save WhatsApp Credentials';
            }
        });
    }

    // Refresh Status Button
    const btnRefresh = document.getElementById('btnRefreshWaStatus');
    if (btnRefresh) {
        btnRefresh.addEventListener('click', function() {
            checkWhatsAppStatus();
        });
    }

    // Send Test Message
    const testForm = document.getElementById('waTestForm');
    const btnSendTest = document.getElementById('btnSendWaTest');
    const testResultEl = document.getElementById('waTestResult');

    if (testForm) {
        testForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const phone = document.getElementById('waTestPhone').value.trim();
            if (!phone) return;

            btnSendTest.disabled = true;
            btnSendTest.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';
            testResultEl.innerHTML = '';

            try {
                const res = await fetch("{{ route('whatsapp.test.send') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ phone: phone, _token: "{{ csrf_token() }}" })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    testResultEl.innerHTML = `<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> ${data.message || 'Test message sent successfully!'}</span>`;
                } else {
                    testResultEl.innerHTML = `<span class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> ${data.message || 'Failed to send test message.'}</span>`;
                }
            } catch (err) {
                testResultEl.innerHTML = '<span class="text-danger">⚠️ Connection error sending test message.</span>';
            } finally {
                btnSendTest.disabled = false;
                btnSendTest.innerHTML = '<i class="bi bi-whatsapp me-1"></i> Send Test Message';
            }
        });
    }

    // Toggle API Key Mask
    const btnToggleMask = document.getElementById('btnToggleApiKeyMask');
    const apiKeyInput = document.getElementById('waApiKey');
    if (btnToggleMask && apiKeyInput) {
        let isMasked = false;
        btnToggleMask.addEventListener('click', function() {
            isMasked = !isMasked;
            apiKeyInput.type = isMasked ? 'password' : 'text';
            btnToggleMask.innerHTML = isMasked ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
        });
    }

    // Auto start polling if QR code is already visible
    @if(($waStatus['status'] ?? '') === 'QR_REQUIRED' || !empty($waStatus['qr']))
        waPollingInterval = setInterval(checkWhatsAppStatus, 5000);
    @endif
});
</script>
@endpush
@endsection
