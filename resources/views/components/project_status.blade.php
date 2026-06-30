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
        'Prêt'         => 'inline-block rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-yellow-700',
        'Proposition'  => 'inline-block rounded-full bg-blue-700 px-3 py-1 text-xs font-medium text-white',
        'Modification' => 'inline-block rounded-full bg-purple-500 px-3 py-1 text-xs font-medium text-indigo-700',
        'Review'       => 'inline-block rounded-full bg-cyan-100 px-3 py-1 text-xs font-medium text-indigo-700',
        'Complété'     => 'inline-block rounded-full bg-green-400 px-3 py-1 text-xs font-medium text-gray-600',
        'Archivée'     => 'inline-block rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600',
        'Refusé'       => 'inline-block rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-600',
    ];

    $labels = [
    'active'       => 'En cours',
    'collecting'   => 'Récolte',
    'ready'        => 'Prêt',
    'submitted'    => 'Proposition',
    'modification' => 'Modification',
    'approved'     => 'Review',
    'completed'    => 'Complété',
    'archived'     => 'Archivée',
    'refused'      => 'Refusé',
];
    $displaytext = $labels[$status] ?? $status;
@endphp
<span class="{{ $styles[$displaytext] ?? 'inline-block rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 '}}">
    {{ $displaytext }}
</span>
