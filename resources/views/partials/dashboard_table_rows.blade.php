@forelse($bookings as $booking)
    @php
        $rowStyle = '';
        $platform = (int) ($booking['platform'] ?? 1);
        $partner = strtolower((string) ($booking['partner'] ?? ''));

        if ($platform === 3 || $partner === 'nonstop_ai') {
            $platformLabel = 'AI Call';
            $platformBadge = 'bg-info text-dark';
        } else {
            switch ($platform) {
                case 0:
                    $platformLabel = 'App';
                    $platformBadge = 'bg-danger';
                    break;
                case 2:
                    $platformLabel = 'Admin';
                    $platformBadge = 'bg-warning';
                    break;
                case 1:
                default:
                    $platformLabel = 'Web';
                    $platformBadge = 'bg-primary';
                    break;
            }
        }

        $statusBadges = [
            'pending'      => 'warning',
            'accepted'     => 'info',
            'declined'     => 'danger',
            'onroute'      => 'primary',
            'arrived'      => 'success',
            'pickedup'     => 'success',
            'completed'    => 'primary',
            'job_cancelled'=> 'danger',
            'no_show'      => 'secondary',
        ];

        $currentStatus = $booking['status'] ?? 'pending';
        $statusBadgeClass = $statusBadges[$currentStatus] ?? 'warning';
        $statusSelectStyle = $statusBadgeClass === 'secondary' 
            ? '' 
            : "background-color: var(--bs-{$statusBadgeClass}); color: var(--bs-black); border-color: var(--bs-{$statusBadgeClass});";

        $paymentType = strtolower($booking['payment_type'] ?? 'unknown');
        $paymentColors = [
            'cash'    => '#28a745',
            'card'    => '#0d6efd',
            'account' => '#6f42c1',
        ];
        $bgColor = $paymentColors[$paymentType] ?? '#6c757d';
        $bDriverId = trim((string)($booking['driver_id'] ?? ($booking['driverId'] ?? ($booking['driver'] ?? ''))));
        $bDriverName = trim((string)($booking['driver_name'] ?? ''));
        $bDriverCallSign = trim((string)($booking['driver_call_sign'] ?? ($booking['call_sign'] ?? '')));

        $driver = null;
        if ($bDriverId !== '' && isset($drivers)) {
            $driver = collect($drivers)->first(function($d) use ($bDriverId) {
                return (string)($d['id'] ?? '') === $bDriverId 
                    || (string)($d['key'] ?? '') === $bDriverId
                    || (string)($d['firebase_key'] ?? '') === $bDriverId
                    || (string)($d['raw_id'] ?? '') === $bDriverId
                    || (string)($d['driver_id'] ?? '') === $bDriverId
                    || strcasecmp(trim($d['name'] ?? ''), $bDriverId) === 0
                    || strcasecmp(trim($d['call_sign'] ?? ''), $bDriverId) === 0;
            });
        }
        if (!$driver && $bDriverName !== '' && isset($drivers)) {
            $driver = collect($drivers)->first(function($d) use ($bDriverName) {
                return strcasecmp(trim($d['name'] ?? ''), $bDriverName) === 0
                    || strcasecmp(trim($d['call_sign'] ?? ''), $bDriverName) === 0;
            });
        }
        if (!$driver && $bDriverCallSign !== '' && isset($drivers)) {
            $driver = collect($drivers)->first(function($d) use ($bDriverCallSign) {
                return strcasecmp(trim($d['call_sign'] ?? ''), $bDriverCallSign) === 0;
            });
        }

        $bPrice = is_numeric($booking['price'] ?? null) ? (float)$booking['price'] : 0;
        $isHighPrice = $bPrice > 50;
    @endphp
    <tr id="booking-row-{{ $booking['id'] }}" data-booking-id="{{ $booking['id'] }}" class="booking-table-row align-middle {{ $isHighPrice ? 'high-value-row' : '' }}">
        <td class="col-ref fw-bold text-nowrap">
            <span class="font-monospace text-dark" style="font-size: 11.5px; letter-spacing: 0.3px;">{{ $booking['ref_no'] ?? 'N/A' }}</span>
        </td>
        <td class="col-payment">
            @php
                $accName = $booking['account_name'] ?? '';
                if (empty($accName) && !empty($booking['account_id']) && isset($accounts)) {
                    if (isset($accounts[$booking['account_id']])) {
                        $accObj = $accounts[$booking['account_id']];
                        $accName = is_array($accObj) ? ($accObj['business_name'] ?? ($accObj['name'] ?? '')) : ($accObj->business_name ?? ($accObj->name ?? ''));
                    }
                    if (empty($accName)) {
                        $targetId = (string)$booking['account_id'];
                        $accObj = collect($accounts)->first(function($a, $k) use ($targetId) {
                            if (is_array($a)) {
                                return (string)($a['id'] ?? '') === $targetId 
                                    || (string)($a['key'] ?? '') === $targetId 
                                    || (string)$k === $targetId
                                    || (string)($a['account_id'] ?? '') === $targetId;
                            } elseif (is_object($a)) {
                                return (string)($a->id ?? '') === $targetId 
                                    || (string)($a->key ?? '') === $targetId 
                                    || (string)$k === $targetId;
                            }
                            return false;
                        });
                        if ($accObj) {
                            $accName = is_array($accObj) ? ($accObj['business_name'] ?? ($accObj['name'] ?? '')) : ($accObj->business_name ?? ($accObj->name ?? ''));
                        }
                    }
                }
                if (empty($accName) && !empty($booking['account'])) {
                    $accName = is_string($booking['account']) ? $booking['account'] : ($booking['account']['business_name'] ?? ($booking['account']['name'] ?? ''));
                }
            @endphp
            <div class="d-flex flex-column align-items-start gap-1">
                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                    <span class="badge rounded-pill px-2.5 py-1 text-white shadow-xs" style="background-color: {{ $bgColor }}; font-size: 10.5px; font-weight: 600;">
                        {{ ucfirst($paymentType) }}
                    </span>
                    @if($isHighPrice)
                        <span class="high-price-badge" style="font-size: 10.5px; padding: 1.5px 6px;">£{{ number_format($bPrice, 2) }}</span>
                    @else
                        <span class="normal-price-badge" style="font-size: 10.5px; padding: 1.5px 6px;">£{{ is_numeric($booking['price'] ?? null) ? number_format((float)$booking['price'], 2) : ($booking['price'] ?? '-') }}</span>
                    @endif
                </div>
                @if($paymentType === 'account' && !empty($accName))
                    <span class="text-truncate fw-bold text-dark" style="max-width: 130px; font-size: 10.5px; line-height: 1.2;" title="{{ $accName }}">
                        {{ $accName }}
                    </span>
                @endif
            </div>
        </td>
        <td class="col-passenger" style="{{ $rowStyle }}">
            @php
                $pName = $booking['passenger_name'] ?? 'N/A';
                $pPhone = $booking['phone_no'] ?? '';
            @endphp
            <div class="d-flex flex-column align-items-start" style="max-width: 145px;">
                <span class="text-truncate fw-semibold text-dark w-100" style="font-size: 11.5px; line-height: 1.25;" title="{{ $pName }}">{{ $pName }}</span>
                @if(!empty($pPhone))
                    <a href="tel:{{ $pPhone }}" class="font-monospace text-decoration-none text-muted d-inline-block text-truncate mt-0.5" style="max-width: 100%; font-size: 11px; letter-spacing: 0.2px;" title="{{ $pPhone }}">
                        {{ $pPhone }}
                    </a>
                @endif
            </div>
        </td>
        <td class="col-driver driver-cell" style="{{ $rowStyle }}">
            @php
                $vMake = $booking['vehicle_make'] ?? '';
                $cleanVMake = (!empty($vMake) && $vMake !== '-') ? $vMake : '';
            @endphp
            @if($driver)
                @php
                    $dName = $driver['name'] ?? 'Driver';
                    $dNumber = $driver['call_sign'] ?? ($driver['callsign'] ?? ($driver['phone'] ?? ($driver['phone_no'] ?? '')));
                    $driverCallSignVehicle = '';
                    if (!empty($dNumber) && !empty($cleanVMake)) {
                        $driverCallSignVehicle = $dNumber . '/' . $cleanVMake;
                    } elseif (!empty($dNumber)) {
                        $driverCallSignVehicle = $dNumber;
                    } elseif (!empty($cleanVMake)) {
                        $driverCallSignVehicle = $cleanVMake;
                    }
                @endphp
                <div class="d-flex flex-column align-items-start gap-1">
                    <span class="badge rounded-pill bg-danger bg-opacity-90 px-2.5 py-1 driver-badge text-truncate" style="font-size: 11px; max-width: 130px; line-height: 1.2;" title="{{ $dName }}">
                        {{ $dName }}
                    </span>
                    @if(!empty($driverCallSignVehicle))
                        <span class="badge bg-light text-dark border font-monospace px-1.5 py-0.5 text-truncate" style="font-size: 10px; font-weight: 700; border-radius: 4px; letter-spacing: 0.3px; max-width: 130px;" title="{{ $driverCallSignVehicle }}">
                            {{ $driverCallSignVehicle }}
                        </span>
                    @endif
                </div>
            @elseif(!empty($bDriverName) || !empty($bDriverCallSign) || !empty($booking['driver']))
                @php
                    $dName = $bDriverName ?: ($booking['driver'] ?? 'Driver');
                    $dNumber = $bDriverCallSign ?: ($booking['driver_phone'] ?? '');
                    $driverCallSignVehicle = '';
                    if (!empty($dNumber) && !empty($cleanVMake)) {
                        $driverCallSignVehicle = $dNumber . '/' . $cleanVMake;
                    } elseif (!empty($dNumber)) {
                        $driverCallSignVehicle = $dNumber;
                    } elseif (!empty($cleanVMake)) {
                        $driverCallSignVehicle = $cleanVMake;
                    }
                @endphp
                <div class="d-flex flex-column align-items-start gap-1">
                    <span class="badge rounded-pill bg-danger bg-opacity-90 px-2.5 py-1 driver-badge text-truncate" style="font-size: 11px; max-width: 130px; line-height: 1.2;" title="{{ $dName }}">
                        {{ $dName }}
                    </span>
                    @if(!empty($driverCallSignVehicle))
                        <span class="badge bg-light text-dark border font-monospace px-1.5 py-0.5 text-truncate" style="font-size: 10px; font-weight: 700; border-radius: 4px; letter-spacing: 0.3px; max-width: 130px;" title="{{ $driverCallSignVehicle }}">
                            {{ $driverCallSignVehicle }}
                        </span>
                    @endif
                </div>
            @else
                <div class="d-flex flex-column align-items-start gap-1">
                    <span class="badge bg-light text-muted border px-2 py-1 unassigned-driver" style="font-size: 11px; font-weight: 500;" title="No driver assigned yet">Not Assigned</span>
                    @if(!empty($cleanVMake))
                        <span class="badge bg-light text-dark border font-monospace px-1.5 py-0.5 text-truncate" style="font-size: 10px; font-weight: 700; border-radius: 4px; letter-spacing: 0.3px; max-width: 130px;" title="{{ $cleanVMake }}">
                            {{ $cleanVMake }}
                        </span>
                    @endif
                </div>
            @endif
        </td>
        <td class="col-route" style="{{ $rowStyle }}">
            @php
                $pAddress = $booking['pickup_address'] ?? '-';
                $dAddress = $booking['dropoff_address'] ?? '-';
                $viasList = [];
                if (!empty($booking['vias'])) {
                    $viasList = is_array($booking['vias']) ? $booking['vias'] : [$booking['vias']];
                } elseif (!empty($booking['via_addresses'])) {
                    $viasList = is_array($booking['via_addresses']) ? $booking['via_addresses'] : [$booking['via_addresses']];
                }
                $viasList = array_filter(array_map('trim', $viasList));
                $viasFull = implode(' → ', $viasList);
            @endphp
            <div class="d-flex flex-column gap-1" style="max-width: 290px;">
                <div class="d-flex align-items-center text-truncate" title="Pickup: {{ $pAddress }}">
                    <i class="bi bi-geo-alt-fill text-success me-1.5 flex-shrink-0" style="font-size: 11px;"></i>
                    <span class="text-truncate text-dark fw-medium" style="font-size: 11.5px; line-height: 1.25;">{{ $pAddress }}</span>
                </div>
                @if(!empty($viasFull))
                    <div class="d-flex align-items-center text-truncate ps-1" title="Via: {{ $viasFull }}">
                        <i class="bi bi-arrow-return-right text-warning me-1.5 flex-shrink-0" style="font-size: 10px;"></i>
                        <span class="text-truncate text-secondary fw-semibold" style="font-size: 11px; line-height: 1.25;">Via: {{ $viasFull }}</span>
                    </div>
                @endif
                <div class="d-flex align-items-center text-truncate" title="Dropoff: {{ $dAddress }}">
                    <i class="bi bi-geo-alt-fill text-danger me-1.5 flex-shrink-0" style="font-size: 11px;"></i>
                    <span class="text-truncate text-muted" style="font-size: 11.5px; line-height: 1.25;">{{ $dAddress }}</span>
                </div>
            </div>
        </td>
        <td class="col-datetime text-nowrap" style="{{ $rowStyle }}">
            @php
                $rawJobDate = $booking['pickup_time'] ?? ($booking['pickup_date'] ?? ($booking['job_date'] ?? null));
                $formattedDate = '-';
                $formattedTime = '-';
                $fullDateTimeTitle = '-';
                if (!empty($rawJobDate)) {
                    try {
                        $dtCarbon = \Carbon\Carbon::parse($rawJobDate);
                        $formattedDate = $dtCarbon->format('d M Y');
                        $formattedTime = $dtCarbon->format('H:i');
                        $fullDateTimeTitle = $dtCarbon->format('l, d M Y - H:i');
                    } catch (\Exception $e) {
                        $formattedDate = (string)$rawJobDate;
                        $fullDateTimeTitle = (string)$rawJobDate;
                    }
                }
            @endphp
            <div class="d-flex flex-column align-items-start" title="{{ $fullDateTimeTitle }}">
                <span class="text-dark fw-bold" style="font-size: 12px; line-height: 1.3;">
                    {{ $formattedDate }}
                </span>
                <span class="text-secondary fw-semibold font-monospace mt-0.5" style="font-size: 11.5px; line-height: 1.2;">
                    {{ $formattedTime }}
                </span>
            </div>
        </td>
        <td class="col-flight" style="{{ $rowStyle }}">
            @php
                $hasFlight = !empty($booking['flight_no']) && $booking['flight_no'] !== '-' && $booking['flight_no'] !== 'undefined';
                $fNo = $hasFlight ? trim($booking['flight_no']) : '';
            @endphp
            @if($hasFlight)
                <a href="https://www.google.com/search?q={{ urlencode('flight ' . $fNo) }}" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   onclick="openFlightTracker(event, '{{ addslashes($fNo) }}')"
                   class="flight-track-badge" 
                   style="font-size: 11px; padding: 2.5px 7px;"
                   title="Click to track flight {{ $fNo }} in new tab">
                    <i class="bi bi-airplane-fill flight-icon"></i>
                    <span class="text-truncate" style="max-width: 80px;">{{ $fNo }}</span>
                    <i class="bi bi-box-arrow-up-right flight-ext-icon"></i>
                </a>
            @else
                <span class="text-muted" style="font-size: 11px;">-</span>
            @endif
        </td>
        <td class="col-comment" title="{{ !empty($booking['job_comment']) ? $booking['job_comment'] : (!empty($booking['comments']) ? $booking['comments'] : 'No comment') }}">
            @php
                $comm = !empty($booking['job_comment']) ? $booking['job_comment'] : (!empty($booking['comments']) ? $booking['comments'] : 'No comment');
            @endphp
            <span class="truncate-cell text-muted" style="max-width: 120px; font-size: 11px;">{{ $comm }}</span>
        </td>
        <td class="col-status" style="{{ $rowStyle }}">
            <form method="POST" action="{{ route('bookings.updateStatusManual', $booking['id']) }}" class="statusForm">
                @csrf
                <select class="form-select form-select-sm text-black statusSelect fw-semibold"
                        name="status"
                        style="{{ $statusSelectStyle }}; min-width: 106px; font-size: 11px; border-radius: 6px; padding: 2px 22px 2px 8px;"
                        data-booking-id="{{ $booking['id'] }}">
                    @php
                        $statusOptions = [
                            'pending'       => 'Pending',
                            'accepted'      => 'Accepted',
                            'declined'      => 'Declined',
                            'onroute'       => 'On Route',
                            'arrived'       => 'Arrived',
                            'pickedup'      => 'Picked Up',
                            'completed'     => 'Completed',
                            'job_cancelled' => 'Job Cancelled',
                            'no_show'       => 'No Show',
                        ];
                    @endphp
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" {{ ($booking['status'] ?? '') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </form>
        </td>
        <td class="col-platform text-center" style="{{ $rowStyle }}">
            <span class="badge {{ $platformBadge }} px-2 py-1" style="font-size: 10.5px; font-weight: 600;">
                {{ $platformLabel }}
            </span>
        </td>
        @php
            $hasDriver = !empty($driver) || !empty($bDriverId) || !empty($bDriverName) || !empty($booking['driver']);
        @endphp
        <td class="col-actions" style="{{ $rowStyle }}">
            <div class="dropdown actions-dropdown">
                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' data-bs-boundary="viewport" aria-expanded="false" style="width: 28px; height: 28px; border-radius: 6px;">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3">
                    <li class="action-dispatch-item" style="{{ $hasDriver ? 'display:none;' : '' }}">
                        <a class="dropdown-item d-flex align-items-center text-secondary dispatch-driver-btn"
                           href="#"
                           data-booking-id="{{ $booking['id'] ?? '' }}"
                           data-bs-toggle="modal"
                           data-bs-target="#dispatchDriverModal">
                            <i class="bi bi-truck me-2"></i> Dispatch Driver
                        </a>
                    </li>
                    <li class="action-change-driver-item" style="{{ !$hasDriver ? 'display:none;' : '' }}">
                        <a class="dropdown-item d-flex align-items-center text-primary dispatch-driver-btn"
                           href="#"
                           data-booking-id="{{ $booking['id'] ?? '' }}"
                           data-driver-id="{{ $bDriverId }}"
                           data-driver-name="{{ $bDriverName }}"
                           data-bs-toggle="modal"
                           data-bs-target="#dispatchDriverModal">
                            <i class="bi bi-person-gear me-2"></i> Change Driver
                        </a>
                    </li>
                    <li class="action-track-item" style="{{ !$hasDriver ? 'display:none;' : '' }}">
                        <a class="dropdown-item d-flex align-items-center text-primary track-driver-link" href="{{ route('bookings.track', $booking['id']) }}">
                            <i class="bi bi-geo-alt me-2"></i> Track Driver
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-success" href="{{ route('bookings.receipt', $booking['id']) }}">
                            <i class="bi bi-receipt me-2"></i> Receipt
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-warning" href="{{ route('bookings.route', $booking['id']) }}">
                            <i class="bi bi-map me-2"></i> Route
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-info" href="{{ route('bookings.return', $booking['id']) }}">
                            <i class="bi bi-arrow-repeat me-2"></i> Create Return Job
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-success send-confirmation-email-btn"
                           href="#"
                           data-booking-id="{{ $booking['id'] ?? '' }}"
                           data-booking-ref="{{ $booking['ref_no'] ?? '' }}"
                           data-passenger-email="{{ $booking['email'] ?? '' }}"
                           data-passenger-name="{{ $booking['passenger_name'] ?? '' }}">
                            <i class="bi bi-envelope me-2"></i> Send Confirmation Email
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-warning send-confirmation-sms-btn"
                           href="#"
                           data-booking-id="{{ $booking['ref_no'] ?? ($booking['id'] ?? '') }}"
                           data-raw-id="{{ $booking['id'] ?? '' }}"
                           data-phone="{{ $booking['phone_no'] ?? '' }}"
                           data-name="{{ $booking['passenger_name'] ?? '' }}"
                           data-date="{{ isset($booking['pickup_time']) ? \Carbon\Carbon::parse($booking['pickup_time'])->format('d/M/Y') : '' }}"
                           data-time="{{ isset($booking['pickup_time']) ? \Carbon\Carbon::parse($booking['pickup_time'])->format('H:i') : '' }}"
                           data-vehicle="{{ $booking['vehicle_make'] ?? '' }}"
                           data-price="{{ $booking['price'] ?? '' }}"
                           data-fare="{{ $booking['fare'] ?? ($booking['base_price'] ?? ($booking['price'] ?? '')) }}"
                           data-parking="{{ $booking['parking'] ?? ($booking['car_park'] ?? '0.00') }}"
                           data-payment="{{ $booking['payment_type'] ?? '' }}"
                           data-pickup="{{ $booking['pickup_address'] ?? '' }}"
                           data-dropoff="{{ $booking['dropoff_address'] ?? '' }}"
                           data-flight_no="{{ $booking['flight_no'] ?? '' }}"
                           data-via="{{ !empty($booking['vias']) ? (is_array($booking['vias']) ? implode(' → ', $booking['vias']) : $booking['vias']) : '-' }}"
                           data-vias-json="{{ json_encode(is_array($booking['vias'] ?? null) ? array_values(array_filter($booking['vias'])) : (!empty($booking['vias']) ? [trim($booking['vias'])] : [])) }}">
                            <i class="bi bi-chat-left-text me-2"></i> Send Confirmation SMS
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-success send-confirmation-whatsapp-btn"
                           href="#"
                           data-booking-id="{{ $booking['ref_no'] ?? ($booking['id'] ?? '') }}"
                           data-raw-id="{{ $booking['id'] ?? '' }}"
                           data-phone="{{ $booking['phone_no'] ?? '' }}"
                           data-name="{{ $booking['passenger_name'] ?? '' }}"
                           data-date="{{ isset($booking['pickup_time']) ? \Carbon\Carbon::parse($booking['pickup_time'])->format('d/M/Y') : '' }}"
                           data-time="{{ isset($booking['pickup_time']) ? \Carbon\Carbon::parse($booking['pickup_time'])->format('H:i') : '' }}"
                           data-vehicle="{{ $booking['vehicle_make'] ?? '' }}"
                           data-price="{{ $booking['price'] ?? '' }}"
                           data-fare="{{ $booking['fare'] ?? ($booking['base_price'] ?? ($booking['price'] ?? '')) }}"
                           data-parking="{{ $booking['parking'] ?? ($booking['car_park'] ?? '0.00') }}"
                           data-payment="{{ $booking['payment_type'] ?? '' }}"
                           data-pickup="{{ $booking['pickup_address'] ?? '' }}"
                           data-dropoff="{{ $booking['dropoff_address'] ?? '' }}"
                           data-flight_no="{{ $booking['flight_no'] ?? '' }}"
                           data-via="{{ !empty($booking['vias']) ? (is_array($booking['vias']) ? implode(' → ', $booking['vias']) : $booking['vias']) : '-' }}"
                           data-vias-json="{{ json_encode(is_array($booking['vias'] ?? null) ? array_values(array_filter($booking['vias'])) : (!empty($booking['vias']) ? [trim($booking['vias'])] : [])) }}">
                            <i class="bi bi-whatsapp me-2"></i> Send Confirmation WhatsApp
                        </a>
                    </li>
                    <li class="action-recall-item" style="{{ !$hasDriver ? 'display:none;' : '' }}">
                        <a class="dropdown-item d-flex align-items-center text-danger recall-job-btn" href="#"
                           data-booking-id="{{ $booking['id'] ?? '' }}">
                            <i class="bi bi-arrow-counterclockwise me-2"></i> Recall Job
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-secondary hide-job-btn" href="#"
                           data-booking-id="{{ $booking['id'] ?? '' }}">
                            <i class="bi bi-eye-slash me-2"></i> Hide Job
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-primary" href="{{ route('bookings.edit', $booking['id']) }}">
                            <i class="bi bi-pencil-square me-2"></i> Edit Booking
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-info view-booking-btn"
                           href="#"
                           data-bs-toggle="modal"
                           data-bs-target="#viewBookingModal"
                           data-booking-id="{{ $booking['id'] ?? '' }}"
                           data-booking='@json($booking)'>
                            <i class="bi bi-eye me-2"></i> View Booking
                        </a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
@empty
    <tr id="emptyBookingsRow">
        <td colspan="11" class="text-center text-muted py-4">No future bookings found.</td>
    </tr>
@endforelse
