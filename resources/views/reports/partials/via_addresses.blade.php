@php
    // Older bookings may use vias or via instead of via_addresses.
    $viaAddresses = [];
    foreach (['via_addresses', 'vias', 'via'] as $field) {
        $rawVias = $booking->{$field} ?? [];
        $viaAddresses = array_values(array_filter(
            array_map(static fn ($address) => is_string($address) ? trim($address) : '', is_array($rawVias) ? $rawVias : [$rawVias]),
            static fn ($address) => $address !== '' && $address !== '-'
        ));
        if ($viaAddresses !== []) {
            break;
        }
    }
@endphp
@forelse($viaAddresses as $address)
    <div style="white-space: normal; overflow-wrap: break-word; word-wrap: break-word;">{{ $address }}</div>
@empty
    <span style="color: #94a3b8;">&ndash;</span>
@endforelse
