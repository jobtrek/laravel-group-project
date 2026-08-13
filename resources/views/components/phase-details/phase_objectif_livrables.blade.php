@props([
    'livrables_text' => '',
    'objectifs_text' => '',
    'project' => null,
    'phase' => null,
    'itemType' => '',
    'itemIndex' => null,
    'completed' => false,
])

<div
    x-data="{ checked: @js($completed) }"
    class="rounded-lg border p-3 flex justify-between items-start gap-2 transition-colors {{ $completed ? 'bg-green-50 border-green-400' : 'border-gray-200' }}"
    :class="checked ? 'bg-green-50 border-green-400' : 'border-gray-200'"
>
    <div class="flex flex-col gap-4">
        @if($livrables_text)
            <p class="text-sm font-semibold {{ $completed ? 'text-green-800' : 'text-gray-800' }}">{{ $livrables_text }}</p>
        @elseif($objectifs_text)
            <p class="text-sm font-semibold {{ $completed ? 'text-green-800' : 'text-gray-800' }}">{{ $objectifs_text }}</p>
        @endif
    </div>
    @if($project && $project->status instanceof \App\Models\States\EncoursState)
        @can('edit project', $project)
            <form method="POST" action="{{ route('phase_details.items.toggle', [$project, $phase, $itemType, $itemIndex]) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="completed" value="{{ $completed ? '0' : '1' }}">
                <input
                    type="checkbox"
                    class="w-4 h-4 text-green-600 bg-neutral-secondary-medium border-default-medium rounded-xs focus:ring-green-500 dark:focus:ring-green-600 ring-offset-neutral-primary focus:ring-2"
                    {{ $completed ? 'checked' : '' }}
                    onchange="this.form.submit()"
                >
            </form>
        @endcan
    @endif
</div>