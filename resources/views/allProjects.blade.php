<x-app-layout>
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
                            text="Révision"
                            projets="6"
                            route="review"/>    
                        <x-projects.countProjects
                            text="Récolte"
                            projets="9"
                            route="recolte"/>
                        <x-projects.countProjects
                            text="En cours"
                            projets="7"
                            route="en-cours"/>
                        <x-projects.countProjects
                            text="Archives"
                            projets="7"
                            route="archive"/>
                    </div>
                         <a href="{{ route('create') }}" class="bg-blue-700 text-white rounded-lg p-1">Nouvelle proposition</a>
                    <div class="flex flex-col gap-4 mt-4">
                        <x-projects.displayProjects
                            status="En retard"
                            title="Refonte du site web"
                            chef="Marie Dupont"
                            description="Refonte totale de l'interface utilisateur avec Tailwind CSS et Laravel."
                            progress="100"
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
