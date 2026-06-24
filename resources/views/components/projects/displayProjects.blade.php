@props([
    'status' => 'Active',
    'title' => '',
    'chef' => '',
    'description' => '',
    'progress' => 0,
    'creationDate' => '',
])

<div class="bg-white rounded-2xl border border-gray-200 p-5 flex flex-col gap-3 shadow-sm">

    <div class="flex items-start justify-between p-2">
        <span class="text-xs font-medium bg-red-50 px-3 py-1 rounded-full">
            {{ $status }}
        </span>
    </div>

    <div>
        <h3 class="text-base font-semibold text-gray-900 leading-snug">{{ $title }}</h3>
        <p class="text-sm text-gray-500 mt-1 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
            </svg>
            {{ $chef }}
        </p>
    </div>
    <p class="text-sm text-gray-500 leading-relaxed line-clamp-2">{{ $description }}</p>
@if($status === 'collecting' || $status === 'active'):
    <div class="w-full bg-gray-200 rounded-full h-1.5">
        <div class="{{ $progress <= 20 ? 'bg-red-500' : 'bg-green-700' }} h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
    </div>
    @endif
    <div class="flex items-center justify-between mt-1">

        <span class="text-xs text-gray-400 flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
            {{ $creationDate }}
        </span>
    </div>
</div>
