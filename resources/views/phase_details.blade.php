@props([
    'phase_number' => 1,
    'title_phase' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt eiusmod',
    'time' => '',
    'description_phase' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt',
])

<x-app-layout>
    <div class="w-full max-w-7xl p-4 mx-auto">
        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="p-6 flex flex-col gap-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="mt-3 text-2xl font-bold text-gray-900">{{ $phase_number . ' - ' . $title_phase }}</h2>
                        <p class="mt-1 text-sm text-gray-500"> {{ $description_phase }}</p>
                    </div>
                    <x-projects-details.comeBackButton/>
                </div>
                <div class="flex flex-col gap-4">
                    <div class="rounded-lg border border-blue-700 p-3 flex flex-col gap-4 shadow-lg">
                        <div class="flex flex-col gap-4">
                            <p class="text-[20px] font-semibold text-gray-800">Objectifs</p>
                        </div>
                        <x-phase-details.phase_objectif_livrables objectifs_text="asdasd"/>
                        <x-phase-details.phase_objectif_livrables objectifs_text="BABA"/>
                        <x-phase-details.phase_objectif_livrables objectifs_text="DADA"/>
                    </div>

                    <div class="rounded-lg border border-green-700 p-3 flex flex-col gap-4 shadow-lg">
                        <div class="flex flex-col gap-4">
                            <p class="text-[20px] font-semibold text-gray-800">Livrables :</p>
                        </div>
                        <x-phase-details.phase_objectif_livrables livrables_text="HEHE"/>
                        <x-phase-details.phase_objectif_livrables livrables_text="LIVRABLES"/>
                        <x-phase-details.phase_objectif_livrables livrables_text="PAPA"/>
                    </div>

                    <div class="rounded-lg border border-purple-700 p-3 flex flex-col gap-4 shadow-lg">
                        <div class="flex flex-col gap-4">
                            <p class="text-[20px] font-semibold text-gray-800">Ressources requises :</p>
                        </div>
                        <x-phase-details.resources resource_quantity="200 CHF" resource_type="Argent"/>
                        <x-phase-details.resources resource_quantity="4 personnes" resource_type="Personele"/>
                        <x-phase-details.resources resource_quantity="2 tables" resource_type="Materiele"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
