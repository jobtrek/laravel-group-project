<x-app-layout>
    <div class="w-full max-w-7xl p-4 mx-auto">
        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="p-6 flex flex-col gap-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="mt-3 text-2xl font-bold text-gray-900">
                            {{ $phase->order }} - {{ $phase->name }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">{{ $phase->description }}</p>
                        @if($phase->duration)
                            <p class="mt-2 text-sm font-medium text-gray-600">
                                Durée : {{ $phase->duration }}
                            </p>
                        @endif
                    </div>
                    <x-projects-details.comeBackButton/>
                </div>
                <div class="flex flex-col gap-4">
                    <div class="rounded-lg border border-blue-700 p-3 flex flex-col gap-4 shadow-lg">
                        <p class="text-[20px] font-semibold text-gray-800">Objectifs</p>
                        @forelse($phase->objectifs as $objectif)
                            <x-phase-details.phase_objectif_livrables :objectifs_text="$objectif"/>
                        @empty
                            <p class="text-sm text-gray-500">Aucun objectif défini</p>
                        @endforelse
                    </div>

                    <div class="rounded-lg border border-green-700 p-3 flex flex-col gap-4 shadow-lg">
                        <p class="text-[20px] font-semibold text-gray-800">Livrables :</p>
                        @forelse($phase->livrables as $livrable)
                            <x-phase-details.phase_objectif_livrables :livrables_text="$livrable"/>
                        @empty
                            <p class="text-sm text-gray-500">Aucun livrable défini</p>
                        @endforelse
                    </div>

                    <div class="rounded-lg border border-purple-700 p-3 flex flex-col gap-4 shadow-lg">
                        <p class="text-[20px] font-semibold text-gray-800">Ressources requises :</p>
                        @forelse($phase->resources as $resource)
                            <x-phase-details.resources
                                :resource_type="$resource->resource_type"
                                :resource_quantity="$resource->amount_needed . ' CHF'"
                            />
                        @empty
                            <p class="text-sm text-gray-500">Aucune ressource requise</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
