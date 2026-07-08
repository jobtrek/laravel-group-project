@props([
    'color' => 'green'
])
@php
    $greenTheme = 'border-green-200 bg-green-50 text-green-600 hover:bg-green-100';

    $themes = match($color) {
        'black' => '',
        'green' => $greenTheme,
        default => $greenTheme,
    };
@endphp

<button {{ $attributes->merge(['type' => 'submit',
'class' => 'p-2 inline-block mb-4 rounded-lg border
px-4.5 py-2 text-sm font-medium transition-colors ' . $themes]) }}>
    {{ $slot }}
</button>
