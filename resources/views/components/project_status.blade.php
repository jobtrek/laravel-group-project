@props([
    'status' => ''
])
@php
    if ($status instanceof \App\Models\States\ProjectState) {
        $status = $status->getValue();
    }

    $styles = [
        'Proposition' => 'inline-block rounded-full bg-cyan-100 px-3 py-1 text-xs font-medium text-indigo-700',
        'Évaluation'  => 'inline-block rounded-full bg-cyan-100 px-3 py-1 text-xs font-medium text-indigo-700',
        'Révision'    => 'inline-block rounded-full bg-orange-100 px-3 py-1 text-xs font-medium text-orange-700',
        'Récolte'     => 'inline-block rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700',
        'En cours'    => 'inline-block rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700',
        'Complété'    => 'inline-block rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700',
        'Archivé'     => 'inline-block rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600',
    ];

    $labels = [
        'proposition' => 'Proposition',
        'évaluation'  => 'Évaluation',
        'révision'    => 'Révision',
        'récolte'     => 'Récolte',
        'en cours'    => 'En cours',
        'complété'    => 'Complété',
        'archivé'     => 'Archivé',
    ];

    $displaytext = $labels[$status] ?? $status;
@endphp
<span class="{{ $styles[$displaytext] ?? 'inline-block rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 '}}">
    {{ $displaytext }}
</span>
