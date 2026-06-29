@props([
    'status' => ''
])
@php
    if ($status instanceof \App\Models\States\ProjectState) {
        $status = $status->getValue();
    }

    $styles = [
        'En cours'     => 'inline-block rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700',
        'Récolte'      => 'inline-block rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700',
        'Prêt'         => 'inline-block rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700',
        'Proposition'  => 'inline-block rounded-full bg-cyan-100 px-3 py-1 text-xs font-medium text-indigo-700',
        'Modification' => 'inline-block rounded-full bg-cyan-100 px-3 py-1 text-xs font-medium text-indigo-700',
        'Review'       => 'inline-block rounded-full bg-cyan-100 px-3 py-1 text-xs font-medium text-indigo-700',
        'Complété'     => 'inline-block rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600',
        'Archivée'     => 'inline-block rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600',
        'Refusé'       => 'inline-block rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-600',
    ];

    $labels = [
    'submitted'    => 'Proposition',
    'modification' => 'Modification',
    'approved'     => 'Review',
    'collecting'   => 'Récolte',
    'ready'        => 'Prêt',
    'active'       => 'En cours',
    'completed'    => 'Complété',
    'archived'     => 'Archivée',
    'refused'      => 'Refusé',
];
    $displaytext = $labels[$status] ?? $status;
@endphp
<span class="{{ $styles[$displaytext] }}">
    {{ $displaytext }}
</span>
