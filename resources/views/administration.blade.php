@php
use App\Enums\Role;
@endphp;
<x-app-layout>
    <div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-white">Administration</h1>
                <a href="{{ route('register') }}"
                   class="rounded-lg border border-white/25 bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/20 transition-colors">
                    Créer un utilisateur
                </a>
            </div>
        </div>
    </div>
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Liste des utilisateurs</h2>
                <span class="text-sm text-gray-500">{{ $users->count() }} utilisateur(s)</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($users as $user)
                    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#93c83a] text-[#131c3f] flex items-center justify-center font-extrabold text-sm">
                                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                        @if($user->roles->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5 mt-3">
                                @foreach($user->roles as $role)
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-medium">
                                        {{ Role::tryFrom($role->name)->label() ?? $role->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
