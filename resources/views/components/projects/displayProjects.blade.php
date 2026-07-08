@props([
    'project' => null,
    'status' => '',
    'title' => '',
    'chef' => '',
    'importance' => 0,
    'progress' => 0,
    'creationDate' => '',
    'updatedAt' => null
])

<?php



use Carbon\Carbon;
$bgColor = '';
use App\Http\Controllers\ProjectController;

?>
<div class="relative border border-white/25 bg-gray-50 text-sm font-medium hover:border-blue-500 transition-colors rounded-2xl p-3 sm:p-5 flex flex-col gap-3">

    <a href="{{ route('projects-details', $project) }}"
       class="absolute inset-0 z-0 text-gray-7000"
       aria-label="Voir le projet {{ $title }}">
    </a>

    <div class="relative z-10 pointer-events-none flex flex-col items-start gap-2 py-2">
        <x-project_status :status="$status"/>
        <h3 class="text-base font-medium leading-snug text-gray-700">Importance : {{ $importance }}</h3>
    </div>

    <div class="relative z-10 pointer-events-none flex items-center justify-between mt-1">
        <div>
            <h2 class="text-base font-semibold text-gray-700 leading-snug break-words">{{ $title }}</h2>
            <p class="text-sm text-gray-700 mt-1 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                </svg>
                {{ $chef }}
            </p>
        </div>
        <div class="flex flex-wrap gap-1.5 sm:gap-2 pointer-events-auto">
            @if((string)$status === 'proposition')
                @can('send to direction')
                    <form action="{{ route('projects.send-to-direction', $project) }}" method="POST"
                          class="relative z-10">
                        @csrf
                        @method('PATCH')
                        <x-projects.buttons text="Confirmer la proposition" class="bg-blue-700 text-white p-2"
                                            type="submit"/>
                    </form>
                @endcan
            @elseif((string)$status === 'évaluation')
                @can('evaluate projects')
                    <form action="{{ route('projects.deny', $project) }}" method="POST" class="relative z-10">
                        @csrf
                        @method('PATCH')
                        <x-projects.buttons text="Refuser"
                                            class="bg-red-700 hover:bg-red-800 text-white text-sm rounded-lg px-3 py-1.5 transition-colors"
                                            type="submit"/>
                    </form>
                    <form action="{{ route('projects.direction-review', $project) }}" method="GET" class="relative z-10">
                        <x-projects.buttons text="Révision"
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm rounded-lg px-3 py-1.5 transition-colors"
                                            type="submit"/>
                    </form>
                    <form action="{{ route('projects.approve', $project) }}" method="POST" class="relative z-10">
                        @csrf
                        @method('PATCH')
                        <x-projects.buttons text="Accepter"
                                            class="bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg px-3 py-1.5 transition-colors"
                                            type="submit"/>
                    </form>
                @endcan
            @elseif((string)$status === 'en cours')
               @if($project->progress >= 100 && auth()->user()?->can('complete project') && (auth()->id() === $project->leader_id || auth()->user()?->hasRole(\App\Enums\Role::ProjectManager->value)))
                    <form action="{{ route('projects.complete', $project) }}" method="POST" class="relative z-10">
                        @csrf
                        @method('PATCH')
                        <x-projects.buttons text="Marquer comme complété" class="bg-green-600 text-white p-2"
                                            type="submit"/>
                    </form>
                @endif
            @endif
        </div>
    </div>
    @if((string)$status === 'récolte' || (string)$status === 'en cours')
        <x-progressBar :progress="$progress"/>
        <div class="relative z-10 flex justify-end gap-2">
            @can('add resources')
                <a href="{{ route('projects.resources.create', $project) }}"
                   class="bg-blue-700 text-white text-sm rounded-lg px-3 py-1.5">
                    Ajouter une ressource
                </a>
            @endcan
            @if((string)$status === 'récolte' && auth()->user()?->can('launch project'))
                @php $canLaunch = auth()->id() === $project->leader_id;
                     $isAdmin = auth()->user()->can('manage everything');
                @endphp
            <form action="{{ route('projects.recolte.activate', $project) }}" method="POST" class="relative z-10">
                    @csrf
                    @method('PATCH')
                                        <x-projects.buttons text="Démarrer le projet"
                        class="text-sm rounded-lg px-3 py-1.5 {{ $canLaunch || $isAdmin ? 'bg-green-700 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}"
                        type="submit"
                        :disabled="!$canLaunch && !$isAdmin"/>
                </form>
            @endif
        </div>
    @endif
    <div class="relative z-10 pointer-events-none flex items-center justify-between mt-1">

        <span class="text-xs text-gray-700 flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
            </svg>
            {{ $creationDate }}
        </span>
        @if($updatedAt instanceof Carbon)
                <?php
                $stalenessColors = config('projects.staleness_colors') ?? [];
                [$bgColor, $textColor, $dotColor] = match (true) {
                    $updatedAt->lessThan(now()->subMonths($stalenessColors['red_after_months'] ?? 3))
                    => ['bg-red-50', 'text-red-700', 'bg-red-500'],
                    $updatedAt->lessThan(now()->subMonths($stalenessColors['orange_after_months'] ?? 2))
                    => ['bg-orange-50', 'text-orange-700', 'bg-orange-500'],
                    $updatedAt->lessThan(now()->subMonths($stalenessColors['warning_after_months'] ?? 1))
                    => ['bg-yellow-50', 'text-yellow-700', 'bg-yellow-500'],
                    default => ['bg-green-50', 'text-green-700', 'bg-green-500'],
                };
                ?>
            <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2 py-1 rounded-full {{ $bgColor }} {{ $textColor }} ring-1 ring-inset ring-black/5">
        <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
        <span class="italic">Mis à jour {{ $updatedAt->locale('fr')->diffForHumans() }}</span>
        </span>
        @endif
    </div>
</div>
