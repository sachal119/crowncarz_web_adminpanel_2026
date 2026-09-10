@forelse($bookings as $booking)
    @php
        $isHighPrice   = ($booking['price'] ?? 0) > 50;
        $isHidden      = ($booking['hidden'] ?? false) == true;

        if ($isHidden) {
            $rowStyle = 'background-color: yellow; color: #000;';
        } elseif ($isHighPrice) {
            $rowStyle = 'background-color: burlywood; color: #000;';
        } else {
            $rowStyle = '';
        }
       
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
                $dId = (string)($d['id'] ?? '');
                $dKey = (string)($d['key'] ?? '');
                return ($dId !== '' && $dId === $bDriverId) || ($dKey !== '' && $dKey === $bDriverId);
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
    @endphp
    <tr id="booking-row-{{ $booking['id'] }}" data-booking-id="{{ $booking['id'] }}" class="booking-table-row" style="{{ $rowStyle }}">
        <td class="col-ref" style="{{ $rowStyle }}">{{ $booking['ref_no'] ?? 'N/A' }}</td>
        <td class="col-payment" style="{{ $rowStyle }}">
            <span class="badge rounded-pill px-3 py-2" style="background-color: {{ $bgColor }}; color: #fff;">
                {{ ucfirst($paymentType) }}
            </span>
        </td>
        <td class="col-passenger" style="{{ $rowStyle }}">{{ $booking['passenger_name'] ?? 'N/A' }}</td>
        <td class="col-driver driver-cell" style="{{ $rowStyle }}">
            @if($driver)
                @php
                    $dCall = !empty($driver['call_sign']) ? $driver['call_sign'] . '/' : '';
                    $dName = $driver['name'] ?? 'Driver';
                @endphp
                <span class="badge rounded-pill bg-danger bg-opacity-90 px-2.5 py-1.5 me-1 driver-badge" style="font-size: 11.5px;">
                    {{ $dCall }}{{ $dName }}
                </span>
            @elseif(!empty($bDriverName) || !empty($bDriverCallSign) || !empty($booking['driver']))
                @php
                    $dCall = !empty($bDriverCallSign) ? $bDriverCallSign . '/' : '';
                    $dName = $bDriverName ?: ($booking['driver'] ?? 'Driver');
                @endphp
                <span class="badge rounded-pill bg-danger bg-opacity-90 px-2.5 py-1.5 me-1 driver-badge" style="font-size: 11.5px;">
                    {{ $dCall }}{{ $dName }}
                </span>
            @else
                <span class="badge bg-light text-muted border px-2 py-1 unassigned-driver" style="font-size: 11px; font-weight: 500;">Not Assigned</span>
            @endif
        </td>
        <td class="col-phone" style="{{ $rowStyle }}">{{ $booking['phone_no'] ?? 'N/A' }}</td>
        <td class="col-pickup" style="{{ $rowStyle }}">{{ Str::limit($booking['pickup_address'] ?? '', 30) }}</td>
        <td class="col-dropoff" style="{{ $rowStyle }}">{{ Str::limit($booking['dropoff_address'] ?? '', 30) }}</td>
        <td class="col-vias" style="{{ $rowStyle }}">
            @php
                $viasList = [];
                if (!empty($booking['vias'])) {
                    $viasList = is_array($booking['vias']) ? $booking['vias'] : [$booking['vias']];
                } elseif (!empty($booking['via_addresses'])) {
                    $viasList = is_array($booking['via_addresses']) ? $booking['via_addresses'] : [$booking['via_addresses']];
                }
                $viasList = array_filter(array_map('trim', $viasList));
                $viasCount = count($viasList);
                $viasFull = implode(' → ', $viasList);
            @endphp
            @if($viasCount > 0)
                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11px; font-weight: 500; max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block; vertical-align: middle;" title="{{ $viasFull }}">
                    <i class="bi bi-signpost-split text-warning me-1"></i>{{ Str::limit($viasFull, 16) }}
                </span>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td class="col-date" style="{{ $rowStyle }}">{{ isset($booking['pickup_time']) ? \Carbon\Carbon::parse($booking['pickup_time'])->format('d M Y') : '-' }}</td>
        <td class="col-time" style="{{ $rowStyle }}">{{ isset($booking['pickup_time']) ? \Carbon\Carbon::parse($booking['pickup_time'])->format('H:i') : '-' }}</td>
        <td class="col-vehicle" style="{{ $rowStyle }}">
            @php
                $type = $booking['vehicle_make'] ?? '-';
                $badges = [
                    'Saloon' => 'primary',
                    'Estate' => 'success',
                    'MPV' => 'warning',
                    '8 Seater' => 'danger',
                    'Executive' => 'dark',
                ];
                $badgeClass = $badges[$type] ?? 'secondary';
            @endphp
            <span class="badge bg-{{ $badgeClass }}" style="font-size: 0.85rem;">{{ $type }}</span>
        </td>
        <td class="col-flight" style="{{ $rowStyle }}">{{ $booking['flight_no'] ?? '-' }}</td>
        <td class="col-price" style="{{ $rowStyle }}">{{ $booking['price'] ?? '-' }}</td>
        <td class="col-comment" style="{{ $rowStyle }}">
            {{ isset($booking['job_comment']) ? Str::limit($booking['job_comment'], 20) : 'No comment' }}
        </td>
        <td class="col-status" style="{{ $rowStyle }}">
            <form method="POST" action="{{ route('bookings.updateStatusManual', $booking['id']) }}" class="statusForm">
                @csrf
                <select class="form-select form-select-sm text-black statusSelect"
                        name="status"
                        style="{{ $statusSelectStyle }}; width:100px;"
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
        <td class="col-platform" style="{{ $rowStyle }}">
            <span class="badge {{ $platformBadge }}" style="font-size: 0.75rem;">
                {{ $platformLabel }}
            </span>
        </td>
        <td class="col-actions" style="{{ $rowStyle }}">
            <div class="dropdown actions-dropdown" style="position: static;">
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3">
                    <li class="action-dispatch-item" style="{{ !empty($booking['driver_id']) ? 'display:none;' : '' }}">
                        <a class="dropdown-item d-flex align-items-center text-secondary dispatch-driver-btn"
                           href="#"
                           data-booking-id="{{ $booking['id'] ?? '' }}"
                           data-bs-toggle="modal"
                           data-bs-target="#dispatchDriverModal">
                            <i class="bi bi-truck me-2"></i> Dispatch Driver
                        </a>
                    </li>
                    <li class="action-track-item" style="{{ empty($booking['driver_id']) ? 'display:none;' : '' }}">
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
                           data-booking-id="{{ $booking['ref_no'] ?? '' }}"
                           data-phone="{{ $booking['phone_no'] ?? '' }}"
                           data-name="{{ $booking['passenger_name'] ?? '' }}"
                           data-date="{{ isset($booking['pickup_time']) ? \Carbon\Carbon::parse($booking['pickup_time'])->format('d M Y') : '' }}"
                           data-time="{{ isset($booking['pickup_time']) ? \Carbon\Carbon::parse($booking['pickup_time'])->format('H:i') : '' }}"
                           data-vehicle="{{ $booking['vehicle_make'] ?? '' }}"
                           data-price="{{ $booking['price'] ?? '' }}"
                           data-payment="{{ ucfirst($booking['payment_type'] ?? '') }}"
                           data-pickup="{{ $booking['pickup_address'] ?? '' }}"
                           data-dropoff="{{ $booking['dropoff_address'] ?? '' }}"
                           data-flight_no="{{ $booking['flight_no'] ?? '' }}"
                           data-via="{{ !empty($booking['vias']) ? implode(' → ', $booking['vias']) : '-' }}">
                            <i class="bi bi-chat-left-text me-2"></i> Send Confirmation SMS
                        </a>
                    </li>
                    <li>
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
        <td colspan="17" class="text-center text-muted py-4">No future bookings found.</td>
    </tr>
@endforelse
