@props([
    'status' => ''
])

@php
    $styles = [
        'En retard' => 'inline-block rounded-full bg-[#fee2e2] px-3 py-1 text-xs font-medium text-[#dc2626]',
        'En cours' => 'inline-block rounded-full bg-[#fef9c3] px-3 py-1 text-xs font-medium text-[#ca8a04]',
        'Proposition' => 'inline-block rounded-full bg-[#dbeafe] px-3 py-1 text-xs font-medium text-[#2563eb]',
        'Archivée' => 'inline-block rounded-full bg-[#f3f4f6] px-3 py-1 text-xs font-medium text-[#4b5563]',
    ];

    $classes = $styles[$status] ?? $styles[''];
@endphp
<span class="{{ $classes }}">
    {{ $status }}
</span>
