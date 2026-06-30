@props([
    'resource_type' => '',
    'resource_quantity' => '',
])
<div
    x-data="{ checked: true }"
    class="rounded-lg border p-3 flex justify-between transition-colors items-center"
    :class="checked ? 'bg-green-50 border-green-400' : 'border-gray-200'"
>
    <div class="flex flex-col gap-4">
            <p class="text-sm font-semibold" :class="checked ? 'text-green-800' : 'text-gray-800'">Type de ressource : {{ $resource_type }}</p>
            <p class="text-sm font-semibold" :class="checked ? 'text-green-800' : 'text-gray-800'">Quantité : {{ $resource_quantity }}</p>
    </div>
    <input
        x-model="checked"
        id="green-checkbox"
        type="checkbox"
        class="w-4 h-4 text-green-600 bg-neutral-secondary-medium border-default-medium rounded-xs focus:ring-green-500 dark:focus:ring-green-600 ring-offset-neutral-primary focus:ring-2"
    >
</div>

