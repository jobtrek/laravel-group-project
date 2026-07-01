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
<div class="bg-white rounded-2xl border border-gray-200 p-5 flex flex-col gap-3 shadow-sm cursor-pointer"
     onclick="window.location='{{ route('projects-details', $project) }}'">
    <div class="flex flex-col items-start gap-2 py-2">
        <x-project_status :status="$status"/>
        <h3 class="text-base font-medium leading-snug">Importance : {{ $importance }}</h3>
    </div>

    <div class="flex items-center justify-between mt-1">
        <div>
            <h3 class="text-base font-semibold text-gray-900 leading-snug">{{ $title }}</h3>
            <p class="text-sm text-gray-500 mt-1 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                </svg>
                {{ $chef }}
            </p>
        </div>
        <div class="flex gap-2">
            @if((string)$status === 'proposition')
                    @can('evaluate projects')
                <form action="{{ route('projects.review', $project) }}" method="POST" onclick="event.stopPropagation()">
                    @csrf
                    @method('PATCH')
                    <x-projects.buttons text=" Evaluer" class="bg-blue-700 text-white p-2" type="submit"/>
                </form>
                @endcan
            @elseif((string)$status === 'évaluation')
                @can('evaluate projects')
                <form action="{{ route('projects.deny', $project) }}" method="POST" onclick="event.stopPropagation()">
                    @csrf
                    @method('PATCH')
                    <x-projects.buttons text="Refuser" class="bg-red-700 text-white p-2" type="submit"/>
                </form>
                <form action="{{ route('projects.direction-review', $project) }}" method="GET"
                      onclick="event.stopPropagation()">
                    <x-projects.buttons text="Révision" class="bg-yellow-500 text-white p-2" type="submit"/>
                </form>
                <form action="{{ route('projects.approve', $project) }}" method="POST"
                      onclick="event.stopPropagation()">
                    @csrf
                    @method('PATCH')
                    <x-projects.buttons text="Accepter" class="bg-green-600 text-white p-2" type="submit"/>
                </form>
            @endif
            @endcan
        </div>
    </div>
    @if((string)$status === 'récolte' || (string)$status === 'en cours')
        <div class="flex items-center gap-2 w-full">
            <div class="w-full bg-gray-200 rounded-full h-1.5">
                <div class="{{ $progress <= 20 ? 'bg-red-500' : 'bg-green-700' }} h-1.5 rounded-full"
                     style="width: {{ $progress }}%"></div>
            </div>
            <span class="text-xs font-semibold text-gray-600 whitespace-nowrap">
            {{ $progress }}%
        </span>
        </div>
    @endif
    <div class="flex items-center justify-between mt-1">

        <span class="text-xs text-gray-400 flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
            </svg>
            {{ $creationDate }}
        </span>
        @if($updatedAt instanceof Carbon)
            <?php $bgColor = match (true) {
                    $updatedAt->lessThan(now()->subMonth(3)) => 'bg-red-400',
                    $updatedAt->lessThan(now()->subMonth(2)) => 'bg-orange-400',
                $updatedAt->lessThan(now()->subMonth()) => 'bg-yellow-400',

                default                                              => 'bg-green-400',
                };
?>
            <span class="text-xs px-1.5 py-0.5 rounded-full text-gray-700 {{ $bgColor }} flex items-center gap-1 italic">
                Mis à jours {{ $updatedAt->locale('fr')->diffForHumans() }}
            </span>
        @endif
    </div>
</div>
