@props([
    'user_status' => '',
    'team_name_user' => '',
])

<div class="flex justify-between">
    <p class="text-sm text-gray-600 mt-1 flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
        </svg>
        {{ $team_name_user }}
    </p>
    @if($users_status)
    <p class="mt-1 text-sm text-gray-600">{{ $user_status }}</p>
    @endif
</div>

