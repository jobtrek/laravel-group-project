<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('All Projects') }}
        </h2>
    </x-slot>
    <div class="justify-center py-12">
        <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class=" bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between gap-4 mb-4">
                        <x-projects.countProjects
                            text="Propositions"
                            projets="20"
                            route="propositions"/>
                        <x-projects.countProjects
                            text="Review"
                            projets="6"
                            route="review"/>    
                        <x-projects.countProjects
                            text="Recolte"
                            projets="9"
                            route="recolte"/>
                        <x-projects.countProjects
                            text="En cours"
                            projets="7"
                            route="en-cours"/>
                    </div>
                    <a href="{{ route('create') }}" class="bg-blue-700 text-white rounded-lg 2 p-1">New proposal</a>
                    <div class="flex flex-col gap-4 mt-4">
                        <x-projects.displayProjects
                            status="En retard"
                            title="Refonte du site web"
                            chef="Marie Dupont"
                            description="Refonte totale de l'interface utilisateur avec Tailwind CSS et Laravel."
                            progress="45"
                            deadline="12 Oct 2024"
                        />
                        <x-projects.displayProjects
                            status="En retard"
                            title="Refonte du site web"
                            chef="Marie Dupont"
                            description="Refonte totale de l'interface utilisateur avec Tailwind CSS et Laravel."
                            progress="45"
                            deadline="12 Oct 2024"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
