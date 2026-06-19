<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('View All projects') }}
        </h2>
    </x-slot>
    <div class="justify-center py-12">
        <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h3 class="text-2xl font-medium text-gray-900 mb-6">Projects</h3>
            <div class=" bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between gap-4">
                        <x-projects.countProjects
                            text="Total projects"
                            projets="20"/>
                        <x-projects.countProjects
                            text="Under review"
                            projets="6"/>
                        <x-projects.countProjects
                            text="Active"
                            projets="9"/>
                        <x-projects.countProjects
                            text="Completed"
                            projets="7"/>
                    </div>
                    <x-projects.inputSearch/>
                    <div>
                        <x-projects.buttons text="All"/>
                        <x-projects.buttons text="Ideation"/>
                        <x-projects.buttons text="Under review"/>
                        <x-projects.buttons text="Active"/>
                        <x-projects.buttons text="Completed"/>
                        <x-projects.buttons text="paused"/>
                    </div>
                    <button class="bg-blue-700 text-white rounded-lg p-1" >New proposal</button>
                    <div class="flex flex-col gap-4">
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
