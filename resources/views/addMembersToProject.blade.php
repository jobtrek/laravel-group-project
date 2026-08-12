@php
    use App\Enums\Role;
@endphp
<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white">Gérer les membres</h1>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Liste des membres</h2>
            </div>

            <div class="grid grid-cols-1 gap-4">
                @foreach ($members as $member)
                    <x-user-card :name="$member->name" :roles="$member->roles" :email="$member->email">
                        @if ($member === $project->leader)
                            <div class="ml-auto">Leader</div>
                        @endif
                    </x-user-card>
                @endforeach
            </div>

            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Collaborateurs</h2>
            </div>
            <div class="grid grid-cols-1 gap-4">
                @foreach ($users as $user)
                    <x-user-card :name="$user->name" :roles="$user->roles" :email="$user->email">
                        <div class="ml-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" class="w-8 h-8 shrink-0">
                                <rect width="200" height="200" fill="#93c83a" rx="16" />
                                <g fill="none" stroke="#FFFFFF" stroke-width="16" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <line x1="100" y1="50" x2="100" y2="150" />
                                    <line x1="50" y1="100" x2="150" y2="100" />
                                </g>
                            </svg>
                        </div>
                    </x-user-card>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
