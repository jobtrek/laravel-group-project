@props([
    'livrables_text' => '',
    'objectifs_text' => '',
])

<div class="rounded-lg border border-gray-200 p-3 flex flex-col justify-between">
    <div class="flex flex-col gap-4">
        @if($livrables_text)
            <p class="text-sm font-semibold text-gray-800"> {{ $livrables_text }}</p>
        @elseif($objectifs_text)
            <p class="text-sm font-semibold text-gray-800"> {{ $objectifs_text }}</p>
        @endif
    </div>
</div>
