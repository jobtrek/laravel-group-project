@props(['fallbackUrl' => null])

<button
        type="button"
        @if($fallbackUrl)
            onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '{{ $fallbackUrl }}'; }"
        @else
            onclick="window.history.back()"
        @endif
        {{ $attributes->merge(['class' => 'text-gray-400 hover:text-gray-600']) }}
>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
    </svg>
</button>