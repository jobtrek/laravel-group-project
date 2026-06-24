<x-app-layout>
    <x-projects.filter-bar :users="$users ?? []" />
    <div class="relative w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
        <section class="flex flex-col gap-4 p-4">
            @if($stage->prev())
                <x-projects.nav-arrow direction="left" :route="$stage->prev()['route']" :label="$stage->prev()['label']" />
            @endif
            @if($stage->next())
                <x-projects.nav-arrow direction="right" :route="$stage->next()['route']" :label="$stage->next()['label']" />
            @endif
            <h3 class="text-2xl font-medium text-gray-900 mb-6">{{ $stage->title() }}</h3>
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
        </section>
    </div>
</x-app-layout>
