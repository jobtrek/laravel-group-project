<x-app-layout>
    <div class="max-w-4xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold mb-6">Projects — Transition Test</h1>

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
                                    Importance:
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
                </div>
            </div>
        </div>
    @empty
        <p class="text-gray-500">No projects found.</p>
    @endforelse
</div>
</div>
</x-app-layout>
