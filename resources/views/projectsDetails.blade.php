    @props([
        'titre_projet' => 'Refonte du site web',
        'description_projet' => "Refonte totale de l'interface utilisateur avec Tailwind CSS et Laravel",
        'proposition_status' => 'En cours',
        'but_totaux' => 60,
        'but_a_faire' => 40,
        'budget' => 2444,
        'user_name_msg' => 'Jac',
        'comment_msg' => "Le projet progresse globalement de manière satisfaisante et les principales fonctionnalités ont été mises en place conformément aux objectifs définis. Cependant, la partie dédiée aux commandes en ligne rencontre encore plusieurs dysfonctionnements qui empêchent son utilisation optimale par les utilisateurs. En effet, certains processus liés à la passation des commandes ne fonctionnent pas correctement, ce qui peut entraîner des erreurs lors de la sélection des produits, de la validation du panier ou de la confirmation de la commande. Ces problèmes impactent directement l'expérience utilisateur et nécessitent des corrections ainsi que des tests complémentaires afin de garantir la fiabilité du système. La priorité pour la prochaine phase du développement sera donc d'identifier précisément les causes de ces anomalies, de mettre en œuvre les correctifs nécessaires et de réaliser une série de tests fonctionnels afin d'assurer un parcours de commande fluide, sécurisé et efficace pour l'ensemble des utilisateurs.",
        'input_date' => '20/06/2026',
        'phase_number' => 1,
        'phase_name' => ''
    ])


    <x-app-layout>
        <div class="w-full max-w-7xl p-4 mx-auto">
            <div class="rounded-xl border border-gray-200 bg-white">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <x-project_status :status="$proposition_status"/>
                        <x-projects_Details.comeBackButton/>
                    </div>

                    <h2 class="mt-3 text-2xl font-bold text-gray-900">{{ $titre_projet }}</h2>
                    <p class="mt-1 text-sm text-gray-500"> {{ $description_projet }}</p>
                    <div class="flex justify-between">
                        <x-projects_Details.baseInfo name="Proposeur :" valeur="Jean Dupont"/>
                        <x-projects_Details.baseInfo name="Date de creation :" valeur="12/06/2026"/>
                        <x-projects_Details.baseInfo name="Buts :" :valeur="$but_a_faire .' / '. $but_totaux"/>
                        <x-projects_Details.baseInfo name="Budget :" :valeur="$budget . ' ' . 'CHF'"/>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <div class="rounded-lg border border-gray-200 p-3 flex flex-col justify-center">
                            <p class="text-sm font-semibold text-gray-800">Avancement</p>
                            <div class="mt-3 h-1.5 w-full rounded-full bg-gray-100">
                                <div class="h-1.5 w-1/3 rounded-full bg-emerald-400"></div>
                            </div>
                        </div>
                            <div class="rounded-lg border border-gray-200 p-3">
                                <p class="text-sm font-semibold text-gray-800">Details</p>
                                <x-projects_Details.details/>
                            </div>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-3">
                        <div class="rounded-lg border border-gray-200 p-3 flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-gray-800">Buts</p>
                                <button onclick="document.getElementById('input-container').classList.toggle('hidden')"
                                        class="rounded-md bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 text-sm font-medium text-white transition-colors shadow-sm">+ Ajouter un but</button>
                            </div>
                            <div id="input-container" class="hidden mt-3">
                                <x-projects_Details.inputAddtask/>
                            </div>
                        </div>
                        <div class="rounded-lg border border-gray-200 p-3">
                            <p class="text-sm font-semibold text-gray-800">Equipe</p>
                            <div>
                                <x-projects_Details.teamUsers team_name_user="Marie" user_status="{{ true }}"/>
                                <x-projects_Details.teamUsers team_name_user="Jean" user_status="{{ true }}"/>
                                <x-projects_Details.teamUsers team_name_user="Igor"/>
                                <x-projects_Details.teamUsers team_name_user="Laura Pereira da Silva Santos Carvalho pereira"/>
                            </div></div>
                    </div>
                    <div class="mt-4 rounded-lg border border-gray-200 p-3">
                        <p class="text-sm font-semibold text-gray-800">Phases :</p>
                        <div>
                            <x-Phase-details.phase_button phase_name="BABA" phase_number="{{ 1 }}"/>
                        </div>
                    </div>
                    <div class="mt-2 rounded-lg border border-gray-200 p-4">
                        <x-projects_Details.graphique/>
                    </div>
                    <div class="mt-4 rounded-lg border border-gray-200 p-3">
                        <p class="text-sm font-semibold text-gray-800">Commentaires</p>
                        <div class="mt-3 space-y-3 overflow-y-auto">
                            <span class="mt-1 text-sm text-gray-600">Actuellement, aucun commentaire n'a été ajouté</span>
                            <x-projects_Details.Comment_msg messager_name="{{ $user_name_msg }}" commentaire_msg="{{ $comment_msg }}" date_msg="{{ $input_date }}"/>
                            <x-projects_Details.Comment_msg messager_name="{{ $user_name_msg }}" commentaire_msg="{{ $comment_msg }}" date_msg="{{ $input_date }}"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
