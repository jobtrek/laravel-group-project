@props([
    'status' => 'Active',
    'title' => '',
    'chef' => '',
    'description' => '',
    'progress' => 0,
    'deadline' => '',
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
                <path stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            {{ $chef }}
        </p>
    </div>
    <p class="text-sm text-gray-500 leading-relaxed line-clamp-2">{{ $description }}</p>

    <div class="w-full bg-gray-200 rounded-full h-1.5">
        <div class="bg-green-700 h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
    </div>
    <div class="flex items-center justify-between mt-1">

        <span class="text-xs text-gray-400 flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            {{ $deadline }}
        </span>
    </div>
</div>
