<x-app-layout>
    <x-projects.filter-bar :users="$users ?? []" />
    <div class="justify-center py-12">
        <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class=" bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between gap-4 mb-4">
                        <x-projects.countProjects text="Propositions" :projets="$counts->get('submitted', 0)" route="propositions"/>
                        <x-projects.countProjects text="Review" :projets="$counts->get('approved', 0)" route="review"/>
                        <x-projects.countProjects text="Recolte" :projets="$counts->get('collecting', 0)" route="recolte"/>
                        <x-projects.countProjects text="En cours" :projets="$counts->get('active', 0)" route="en-cours"/>
                    </div>
                    <a href="{{ route('create') }}" class="bg-blue-700 text-white rounded-lg p-1">New proposal</a>
                    <div class="flex flex-col gap-4 mt-4">
                        @foreach($projects as $project)
                            <x-projects.displayProjects
                                :status="$project->status"
                                :title="$project->title"
                                :chef="$project->leader?->name ?? $project->proposer?->name"
                                :progress="0"
                                :description="$project->description"
                                :creation-date="$project->created_at->format('d M Y')"
                            />
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
