@props([
    'status' => ''
])
@php
    $state = $status instanceof \App\Models\States\ProjectState ? $status : null;

    $displaytext = $state?->label() ?? $status;
    $styleClasses = $state?->color() ?? 'inline-block rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700';
@endphp
<span class="{{ $styleClasses }}">
    {{ $displaytext }}
</span>
