@props([
    'resource_type' => '',
    'resource_quantity' => '',
    'is_complete' => false,
])
<div
    class="rounded-lg border p-3 flex justify-between transition-colors items-center {{ $is_complete ? 'bg-green-50 border-green-400' : 'border-gray-200' }}"
>
    <div class="flex flex-col gap-4">
        <p class="text-sm font-semibold {{ $is_complete ? 'text-green-800' : 'text-gray-800' }}">Type de ressource : {{ $resource_type }}</p>
        <p class="text-sm font-semibold {{ $is_complete ? 'text-green-800' : 'text-gray-800' }}">Quantité : {{ $resource_quantity }}</p>
    </div>
</div>