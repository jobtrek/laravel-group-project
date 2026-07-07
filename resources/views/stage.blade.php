<x-app-layout>
    <div x-data="{ showFilter: false }">


        <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <section class="flex flex-col gap-4 p-4">
                @if($stage->prev())
                    <x-projects.nav-arrow direction="left" :route="$stage->prev()" :label="$stage->prev()->title()"/>
                @endif
                @if($stage->next())
                    <x-projects.nav-arrow direction="right" :route="$stage->next()" :label="$stage->next()?->title()"/>
                @endif

                <div class="md:hidden flex flex-wrap gap-2 pt-2">
                    @if($stage->prev())
                        <a href="{{ route($stage->prev()) }}"
                           class="inline-flex items-center gap-1 px-3 py-2 rounded-full bg-white border border-gray-200 shadow-sm text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                            {{ $stage->prev()->title() }}
                        </a>
                    @endif
                    @if($stage->next())
                        <a href="{{ route($stage->next()) }}"
                           class="inline-flex items-center gap-1 px-3 py-2 rounded-full bg-white border border-gray-200 shadow-sm text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition">
                            {{ $stage->next()->title() }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endif
                </div>
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-2xl font-medium text-white">{{ $stage->title() }}</h3>
                    <button @click="showFilter = !showFilter"
                            class="p-2 inline-block mb-4 rounded-lg border border-white/25 bg-white/10 px-4.5 py-2 text-sm font-medium text-white hover:bg-white/20 transition-colors">
                        Filtrer
                    </button>
                </div>

                <div x-show="showFilter" x-transition style="display: none;">
                    <x-projects.filter-bar :users="$users ?? []" :my-proposals="$myProposals ?? []"/>
                </div>
                @foreach($projects as $project)
                    <x-projects.displayProjects
                            :project="$project"
                            :status="$project->status"
                            :title="$project->title"
                            :chef="$project->leader?->name ?? $project->proposer?->name"
                            :progress="$project->progress"
                            :importance="$project->importance"
                            :creation-date="$project->created_at->locale('fr')->translatedFormat('d M Y')"
                            :updated_at="$project->updated_at"
                    />
                @endforeach
                {{ $projects->links() }}
            </section>
        </div>
    </div>
</x-app-layout>
