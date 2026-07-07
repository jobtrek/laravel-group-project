@php
    use App\Enums\Role;
    $excludedRoles = [Role::Admin, Role::Collaborateur, Role::ChefDeProjet]
@endphp;
<x-app-layout>
    <div class="bg-[#131c3f]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-2xl font-bold text-white">Créer un utilisateur</h1>
        </div>
    </div>

    <div class="max-w-2xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="mb-6 pb-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Nouvel utilisateur</h2>
            </div>
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('Nom')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="email" :value="__('E-mail')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="role" :value="__('Rôle')" />
                    <select id="role" name="role"
                            class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('Sélectionner un rôle') }}</option>
                        @foreach (array_filter(Role::cases(), fn($r) => !in_array($r, $excludedRoles) ) as $role)
                            <option value="{{ $role->value }}" @selected(old('role') === $role->value)>
                                {{ $role->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-4">
                    <x-input-label for="password" :value="__('Mot de passe')" />
                    <x-text-input id="password" class="block mt-1 w-full"
                                    type="password"
                                    name="password"
                                    required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                    type="password"
                                    name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between mt-4">
                    <a href="{{ route('administration') }}"
                       class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                        &larr; Retour
                    </a>
                    <button type="submit"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-[#131c3f] hover:bg-[#1b2a5b] transition-colors">
                        {{ __("Créer l'utilisateur") }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
