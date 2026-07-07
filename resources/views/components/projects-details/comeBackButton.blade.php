@props(['fallbackUrl' => null])

<button
        type="button"
        @if($fallbackUrl)
            onclick='if (window.history.length > 1) { window.history.back(); } else { window.location.href = @js($fallbackUrl); }'
                    @else
            onclick="window.history.back()"
        @endif
        {{ $attributes->merge(['class' => 'text-gray-400 hover:text-gray-600']) }}
>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
    </svg>
</button>
