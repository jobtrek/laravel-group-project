@props([
    'text' => '',
    'projets' => 0,
])

<div class="bg-blue-100 rounded-lg p-4 flex-1">
    <p class="text-s text-gray-500">{{ $text  }}</p>
    <p class="text-2xs font-medium text-gray-500">{{ $projets }}</p>
</div>
