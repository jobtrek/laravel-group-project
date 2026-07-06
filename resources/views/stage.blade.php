<x-app-layout>
    <x-projects.filter-bar
        :users="$users ?? []"
        :my-proposals="$myProposals ?? []"
    />
    <div class="relative w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
        <section class="flex flex-col gap-4 p-4">
            @if($stage->prev())
                <x-projects.nav-arrow direction="left" :route="$stage->prev()" :label="$stage->prev()->title()"/>
            @endif
            @if($stage->next())
                <x-projects.nav-arrow direction="right" :route="$stage->next()" :label="$stage->next()?->title()"/>
            @endif
            <h3 class="text-2xl font-medium text-gray-900 mb-6">{{ $stage->title() }}</h3>
            @foreach($projects as $project)
                <x-projects.displayProjects
                    :project="$project"
                    :status="$project->status"
                    :title="$project->title"
                    :chef="$project->leader?->name ?? $project->proposer?->name"
                    :progress="$project->progress"
                    :importance="$project->importance"
                    :creation-date="$project->created_at->format('d M Y')"
                    :updated_at="$project->updated_at"
                />
            @endforeach
            {{ $projects->links() }}
        </section>
    </div>
</x-app-layout>
