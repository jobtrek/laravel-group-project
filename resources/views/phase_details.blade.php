<x-app-layout>
    <div class="min-h-screen">
        <div class="w-full max-w-7xl p-4 mx-auto">
            <div class="rounded-xl border border-gray-200 bg-white">
                <div class="p-6 flex flex-col gap-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="mt-3 text-2xl font-bold text-gray-900">
                                {{ $phase->order }} - {{ $phase->name }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-500">{{ $phase->description }}</p>
                            @if ($phase->duration)
                                <p class="mt-2 text-sm font-medium text-gray-600">
                                    {{ __('Durée : :duration', ['duration' => $phase->duration]) }}
                                </p>
                            @endif
                        </div>
                        <x-projects-details.comeBackButton />
                    </div>
                    <div class="rounded-lg border border-gray-200 p-3 flex flex-col justify-center">
                        <p class="text-sm font-semibold text-gray-800">Avancement</p>
                        <div class="mt-3 h-1.5 w-full rounded-full bg-gray-100">
                            <div class="h-1.5 rounded-full bg-emerald-400" style="width: {{ $phase->progress }}%"></div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-4">
                       <div class="rounded-lg border border-blue-700 p-3 flex flex-col gap-4 shadow-lg">
    <p class="text-[20px] font-semibold text-gray-800">Objectifs</p>
    @forelse($phase->objectifs ?? [] as $index => $objectif)
        <x-phase-details.phase_objectif_livrables
            :project="$project"
            :phase="$phase"
            :objectifs_text="$objectif"
            item-type="objectif"
            :item-index="$index"
            :completed="$phase->isItemCompleted('objectif', $index)"
        />
    @empty
        <p class="text-sm text-gray-500">{{ __('Aucun objectif défini') }}</p>
    @endforelse
</div>

<div class="rounded-lg border border-green-700 p-3 flex flex-col gap-4 shadow-lg">
    <p class="text-[20px] font-semibold text-gray-800">Livrables :</p>
    @forelse($phase->livrables ?? [] as $index => $livrable)
        <x-phase-details.phase_objectif_livrables
            :project="$project"
            :phase="$phase"
            :livrables_text="$livrable"
            item-type="livrable"
            :item-index="$index"
            :completed="$phase->isItemCompleted('livrable', $index)"
        />
    @empty
        <p class="text-sm text-gray-500">{{ __('Aucun livrable défini') }}</p>
    @endforelse
</div>

                        <div class="rounded-lg border border-purple-700 p-3 flex flex-col gap-4 shadow-lg">
                            <p class="text-[20px] font-semibold text-gray-800">Ressource requise :</p>
                            @forelse($phase->resources ?? [] as $resource)
                                <x-phase-details.resources :resource_type="$resource->resource_type" :resource_quantity="number_format(
                                    $phase->contributions
                                        ->where('resource_type', $resource->resource_type)
                                        ->sum('amount'),
                                    2,
                                ) .
                                    ' / ' .
                                    number_format($resource->amount_needed, 2) .
                                    ' CHF'" :is_complete="$phase->isResourceComplete($resource)" />          
                            @empty
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
