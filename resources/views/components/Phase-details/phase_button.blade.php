@props([
    'phase_number' => 1,
    'phase_name' => ''
])


<a href="/phase_details">{{ $phase_number . ' - ' . $phase_name }}</a>
