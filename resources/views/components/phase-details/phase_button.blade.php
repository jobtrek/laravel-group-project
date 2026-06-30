@props([
    'phase_number' => 1,
    'phase_name' => ''
])


<a class="bg-gray-50 p-1  pl-3 pr-3 border rounded-xl hover:bg-gray-100" href="/phase_details">{{ $phase_number . ' - ' . $phase_name }}</a>
