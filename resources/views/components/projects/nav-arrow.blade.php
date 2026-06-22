@props([
    'direction' => 'right',
    'route' => '#',
    'label' => '',
])

@if($direction === 'left')
    <a href="{{ $route !== '#' ? route($route) : '#' }}"
       class="fixed left-4 top-1/2 -translate-y-1/2 flex items-center gap-2 px-4 py-3 rounded-full bg-white border border-gray-200 shadow-sm hover:bg-gray-100 transition">
        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        <span class="text-base font-medium text-gray-600">{{ $label }}</span>
    </a>
@else
    <a href="{{ $route !== '#' ? route($route) : '#' }}"
       class="fixed right-4 top-1/2 -translate-y-1/2 flex items-center gap-2 px-4 py-3 rounded-full bg-white border border-gray-200 shadow-sm hover:bg-gray-100 transition">
        <span class="text-base font-medium text-gray-600">{{ $label }}</span>
        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
    </a>
@endif