@props([
    'resource_type' => '',
    'resource_quantity' => '',
])
 <div class="flex gap-4 rounded-lg border border-gray-200 p-3">
     <p class="text-sm font-semibold text-gray-800">Type de ressource : {{ $resource_type }}</p>
     <p class="text-sm font-semibold text-gray-800">Quantité : {{ $resource_quantity }}</p>
 </div>

