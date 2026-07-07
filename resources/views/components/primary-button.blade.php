@props([
    'color' => 'green'
])

@php
    $themes = match($color) {
        'black' => '',
        'green' => 'border-green-200 bg-green-50text-green-600 hover:bg-green-100',
        default => 'border-green-200 bg-green-50text-green-600 hover:bg-green-100'
    };
@endphp
<button {{ $attributes->merge(['type' => 'submit',
'class' => 'p-2 inline-block mb-4 rounded-lg border
px-4.5 py-2 text-sm font-medium transition-colors ' . $themes]) }}>
    {{ $slot }}
</button>
