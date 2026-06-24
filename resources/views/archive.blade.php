<x-app-layout>
    <x-projects.filter-bar :users="$users ?? []" />
    <div class="relative w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
    <section class="flex flex-col gap-4 p-4">
        <x-projects.nav-arrow direction="left" route="en-cours" label="En cours" />
        <h3 class="text-2xl font-medium text-gray-900 mb-6">Archives</h3>
        <x-projects.displayProjects
                            status="En retard"
                            title="Refonte du site web"
                            chef="Marie Dupont"
                            description="Refonte totale de l'interface utilisateur avec Tailwind CSS et Laravel."
                            progress="0"
                            deadline="12 Oct 2024"
                        />
                        <x-projects.displayProjects
                            status="En retard"
                            title="Refonte du site web"
                            chef="Marie Dupont"
                            description="Refonte totale de l'interface utilisateur avec Tailwind CSS et Laravel."
                            progress="0"
                            deadline="12 Oct 2024"
                        />
    </section>
    </div>
</x-app-layout>
