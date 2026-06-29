@props([
    'name' => '',
    'valeur' => ''
])

<div class="mt-2 flex justify-between rounded-lg bg-gray-100 px-4 py-3 gap-3">
    <span class="text-sm font-medium text-gray-700">{{ $name }}</span>
    <span class="text-sm text-gray-600">{{ $valeur }}</span>
</div>
