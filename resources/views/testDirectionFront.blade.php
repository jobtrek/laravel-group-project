<x-app-layout>
    <div class="max-w-4xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold mb-6">Projets — Test de transition</h1>

@if (session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
        {{ session('error') }}
    </div>
@endif

<div class="flex flex-col gap-4">
    @forelse ($projects as $project)
        <div class="border rounded-lg p-4 bg-white shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <h2 class="text-lg font-semibold">{{ $project->title }}</h2>
                    <p class="text-gray-600 text-sm mt-1">{{ $project->description }}</p>
                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                <span>
                                    Importance :
                                    <strong>{{ $project->evaluation?->importance ?? '—' }}</strong>
                                </span>
                        <span>
                                    Status:
                                    <span class="font-medium capitalize">{{ $project->status->getValue() }}</span>
                                </span>
                    </div>
                </div>

                <div class="flex gap-2 shrink-0">
                    <form method="POST" action="{{ route('projects.approve', $project) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="px-3 py-1.5 bg-green-600 text-white text-sm rounded hover:bg-green-700 disabled:opacity-40">
                            Approve
                        </button>
                    </form>

                    <form method="POST" action="{{ route('projects.deny', $project) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="px-3 py-1.5 bg-red-600 text-white text-sm rounded hover:bg-red-700 disabled:opacity-40">
                            Deny
                        </button>
                    </form>

                    @if ($project->status->getValue() === 'submitted')
                        <form method="POST" action="/projects/{{ $project->id }}/request-more-info">
                            @csrf
                            <textarea name="comment" rows="2"
                                      placeholder="What additional information do you need?"
                                      class="w-full border rounded p-2 text-sm mb-1"></textarea>
                            <button type="submit"
                                    class="px-3 py-1.5 bg-yellow-600 text-white text-sm rounded hover:bg-yellow-700">
                                Request More Info
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @php $moreInfoComments = $project->comments->where('stage', 'modification'); @endphp
            @if ($moreInfoComments->isNotEmpty())
                <div class="mt-3 space-y-1">
                    @foreach ($moreInfoComments as $comment)
                        <div class="p-2 bg-yellow-50 border-l-4 border-yellow-400 text-sm rounded">
                            <strong>{{ $comment->user->name }} requested:</strong>
                            {{ $comment->content }}
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($project->status->getValue() === 'modification' && auth()->id() === $project->proposer_id)
                <form method="POST" action="/projects/{{ $project->id }}/resubmit" class="mt-3">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                        Resubmit
                    </button>
                </form>
            @endif
        </div>
    @empty
        <p class="text-gray-500">No projects found.</p>
    @endforelse
</div>
</div>
</x-app-layout>
