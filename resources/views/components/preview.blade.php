@props([
    'title' => 'Nouveau véhicule',
    'chef' => 'Alice',
    'status' => 'Proposition',
    'difficulty' => 'Moyen',
    'description' => "Achat d'un véhicule pour l'équipe terrain",
    'borderColorClass' => 'border-l-orange-500'
])

<div class="bg-white rounded-lg border border-gray-200 border-l-4 {{ $borderColorClass }} px-4 py-3">

    <h2 class="text-sm font-semibold text-gray-900 mb-1">
        {{ $title }}
    </h2>

    <p class="text-xs text-gray-700 mb-3">
        Chef : {{ $chef }}
    </p>

    <div class="flex flex-wrap gap-1.5">
        <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800">
            {{ $status }}
        </span>
        <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-orange-100 text-orange-800">
            {{ $difficulty }}
        </span>
    </div>

    <p class="text-xs text-gray-500 leading-relaxed mt-3">
        {{ $description }}
    </p>

</div>
