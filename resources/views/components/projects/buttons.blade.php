@props([
    'text' => ''
])

<button {{ $attributes->merge(['class' => 'border rounded-lg p-1', 'type' => 'button']) }}>
    {{ $text }}
</button>