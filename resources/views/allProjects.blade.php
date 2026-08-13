@php
use App\Enums\Stage;
@endphp

<x-app-layout>
    <div x-data="{ showFilter: false }">
        <div class="min-h-screen mt-9 p-4">
            <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-gray-900">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 mb-4">
                        <x-projects.countProjects text="Propositions"
                                                  :projets="$counts->get(Stage::Propositions->value, 0)"
                                                  route="propositions"/>
                        <x-projects.countProjects text="Evaluation" :projets="$counts->get(Stage::Evaluation->value, 0)"
                                                  route="evaluation"/>
                        <x-projects.countProjects text="Récolte" :projets="$counts->get(Stage::Recolte->value, 0)"
                                                  route="recolte"/>
                        <x-projects.countProjects text="En cours" :projets="$counts->get(Stage::EnCours->value, 0)"
                                                  route="en-cours"/>
                        <x-projects.countProjects text="Complété" :projets="$counts->get(Stage::Complete->value, 0)"
                                                  route="complete"/>
                        <x-projects.countProjects text="Frigo"
                                                  :projets="$counts->get(Stage::Archive->value, 0)"
                                                  route="frigo"/>
                    </div>
                    <div class="flex flex-wrap gap-2 justify-between mb-2">
                        <a href="{{ route('create') }}"
                           class="p-2 inline-block mb-4 rounded-lg border border-white/25 bg-white/10 px-4.5 py-2 text-sm font-medium text-white hover:bg-white/20 transition-colors">
                            Nouvelle proposition
                        </a>
                        <button @click="showFilter = !showFilter"
                                class="p-2 inline-block mb-4 rounded-lg border border-white/25 bg-white/10 px-4.5 py-2 text-sm font-medium text-white hover:bg-white/20 transition-colors">
                            Filtrer
                        </button>
                    </div>
                    <div class="mb-7" x-show="showFilter" x-transition style="display: none;">
                        <x-projects.filter-bar :users="$users ?? []"/>
                    </div>
                    <div class="flex flex-col gap-3.5">
                        @foreach($projects as $project)
                            <x-projects.displayProjects :project="$project"/>
                        @endforeach
                    </div>

                    <div class="pt-6">
                        {{ $projects->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
