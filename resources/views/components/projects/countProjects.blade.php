@props([
    'text' => '',
    'projets' => 0,
    'route' => '#'
])

<a href="{{ $route !== '#' ? route($route) : '#' }}" class="flex-1">
    <div class="bg-blue-100 rounded-lg p-4 hover:bg-blue-200 transition cursor-pointer">
        <p class="text-sm text-gray-500">{{ $text }}</p>
        <p class="text-2xl font-semibold text-gray-500">{{ $projets }}</p>
    </div>
</a>