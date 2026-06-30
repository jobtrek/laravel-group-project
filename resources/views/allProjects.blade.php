<x-app-layout>
    <x-projects.filter-bar :users="$users ?? []"/>
    <div class="justify-center py-12">
        <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class=" bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
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
                    <a href="{{ route('create') }}" class="bg-blue-700 text-white rounded-lg p-1">New proposal</a>
                    <div class="flex flex-col gap-4 mt-4">
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
                </div>
            </div>
            <div class="pt-6">
                {{ $projects->links() }}
            </div>
        </div>
    </div>
</x-app-layout>