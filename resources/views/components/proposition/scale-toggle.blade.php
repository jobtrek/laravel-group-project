@props(['toggleLabel' => 'Voir l\'échelle', 'hideLabel' => 'Masquer l\'échelle'])

<div x-data="{ open: false }" class="mt-2">
    <button type="button" @click="open = !open"
        class="text-xs text-indigo-600 hover:underline"
        x-text="open ? '{{ $hideLabel }}' : '{{ $toggleLabel }}'"></button>
    <div x-show="open" class="mt-2 text-xs text-gray-600 space-y-1 border-l-2 border-indigo-100 pl-3">
        {{ $slot }}
    </div>
</div>
