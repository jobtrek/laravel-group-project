<nav x-data="{ open: false }" class="bg-[#131c3f] sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-[64px]">

            <div class="flex items-center gap-8">
                <div class="text-white w-20">
                    <img src="{{ Vite::asset('resources/views/Images/logo-white.svg') }}" alt="JobtrekLogo">
                </div>
                <div class="hidden sm:flex sm:items-center sm:gap-7 mt-1">
                    <a href="{{ route('projects') }}" class="text-[13.5px] font-semibold px-0 py-2 border-b-2 transition-colors duration-150 {{ request()->routeIs('projects') ? 'text-white border-[#93c83a]' : 'text-[#b9c1de] border-transparent hover:text-white hover:border-[#b9c1de]' }}">
                        {{ __('Home') }}
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2 bg-white/10 hover:bg-white/20 transition-colors px-3 py-1.5 rounded-full text-[13px] font-semibold text-white focus:outline-none">
                            <span class="w-6 h-6 rounded-full bg-[#93c83a] text-[#131c3f] flex items-center justify-center font-extrabold text-[12px]">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span>{{ Auth::user()->name }}</span>
                            <span class="opacity-60 text-xs ml-1">▾</span>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profil') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                             onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Déconnexion') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-[#b9c1de] hover:text-white hover:bg-white/10 focus:outline-none focus:bg-white/10 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#1b2a5b] border-t border-white/10 shadow-lg">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('projects') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 text-left text-base font-medium transition duration-150 ease-in-out {{ request()->routeIs('projects') ? 'border-[#93c83a] text-white bg-white/5' : 'border-transparent text-[#b9c1de] hover:text-white hover:bg-white/5' }}">
                {{ __('Voir tous les projets') }}
            </a>
        </div>

        <div class="pt-4 pb-2 border-t border-white/10">
            <div class="px-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-[#93c83a] text-[#131c3f] flex items-center justify-center font-extrabold text-[14px]">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-[#b9c1de]">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-4 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-[#b9c1de] hover:text-white hover:bg-white/5 transition duration-150 ease-in-out">
                    {{ __('Profil') }}
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-[#b9c1de] hover:text-white hover:bg-white/5 transition duration-150 ease-in-out">
                        {{ __('Déconnexion') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>
