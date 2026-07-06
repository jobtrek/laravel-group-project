<x-app-layout>
    <div x-data="{ showFilter: false }">
        <div class="min-h-screen mt-9">
            <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="text-gray-900">
                    <div class="flex justify-between gap-4 mb-4">
                        <x-projects.countProjects text="Propositions"
                                                  :projets="$counts->get('proposition', 0) + $counts->get('révision', 0)"
                                                  route="propositions"/>
                        <x-projects.countProjects text="Evaluation" :projets="$counts->get('évaluation', 0)"
                                                  route="evaluation"/>
                        <x-projects.countProjects text="Récolte" :projets="$counts->get('récolte', 0)"
                                                  route="recolte"/>
                        <x-projects.countProjects text="En cours" :projets="$counts->get('en cours', 0)"
                                                  route="en-cours"/>
                        <x-projects.countProjects text="Frigo"
                                                  :projets="$counts->get('archivé', 0) + $counts->get('complété', 0)"
                                                  route="frigo"/>
                    </div>
                    <div class="flex justify-between mb-2">
                        <a href="{{ route('create') }}" class="p-2 inline-block mb-4 rounded-lg border border-white/25 bg-white/10 px-4.5 py-2 text-sm font-medium text-white hover:bg-white/20 transition-colors">
                            Nouveau projet
                        </a>
                        <button @click="showFilter = !showFilter" class="p-2 inline-block mb-4 rounded-lg border border-white/25 bg-white/10 px-4.5 py-2 text-sm font-medium text-white hover:bg-white/20 transition-colors">
                            Filtrer
                        </button>
                    </div>
                    <div class="mb-7" x-show="showFilter" x-transition style="display: none;">
                        <x-projects.filter-bar :users="$users ?? []" />
                    </div>
                    <div class="flex flex-col gap-3.5">
                        @foreach($projects as $project)
                            @if($project && $project->status !== 'archivé')
                                <x-projects.displayProjects
                                    :project="$project"
                                    :status="$project->status"
                                    :title="$project->title"
                                    :chef="$project->leader?->name ?? $project->proposer?->name"
                                    :progress="$project->progress"
                                    :importance="$project->importance"
                                    :creation-date="$project->created_at->format('d M Y')"
                                    :updated_at="$project->updated_at"/>
                            @endif
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
