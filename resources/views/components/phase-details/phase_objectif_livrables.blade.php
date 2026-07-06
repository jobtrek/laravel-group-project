@props([
    'livrables_text' => '',
    'objectifs_text' => '',
    'project' => null,
])

<div
        x-data="{ checked: false }"
        class="rounded-lg border p-3 flex justify-between items-start gap-2 transition-colors"
        :class="checked ? 'bg-green-50 border-green-400' : 'border-gray-200'"
>
    <div class="flex flex-col gap-4">
        @if($livrables_text)
            <p class="text-sm font-semibold"
               :class="checked ? 'text-green-800' : 'text-gray-800'">{{ $livrables_text }}</p>
        @elseif($objectifs_text)
            <p class="text-sm font-semibold"
               :class="checked ? 'text-green-800' : 'text-gray-800'">{{ $objectifs_text }}</p>
        @endif
    </div>
    @if($project && (string)$project->status === 'en cours')
        @can('edit project')
            <input
                    x-model="checked"
                    id="green-checkbox"
                    type="checkbox"
                    class="w-4 h-4 text-green-600 bg-neutral-secondary-medium border-default-medium rounded-xs focus:ring-green-500 dark:focus:ring-green-600 ring-offset-neutral-primary focus:ring-2"
            >
        @endcan
    @endif
</div>
