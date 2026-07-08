<div x-show="step === 2" class="space-y-8">
    <div>
        <h4 class="text-base font-semibold text-gray-800 mb-1">Section 4 — Phases du projet</h4>
        <p class="text-xs text-gray-500 mb-4">Ajoutez autant de phases que nécessaire. Chacune possède ses propres objectifs, livrables et ressources.</p>

        <div class="space-y-6">
            <template x-for="(phase, pi) in phases" :key="phase.id">
                <div class="border border-gray-200 rounded-lg p-5 space-y-4 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <h5 class="font-semibold text-gray-800" x-text="'Phase ' + (pi + 1)"></h5>
                        <button type="button" @click="removePhase(pi)" x-show="phases.length > 1"
                            class="text-sm text-red-500 hover:text-red-700">Supprimer la phase</button>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Titre</label>
                            <input type="text" :name="'phases[' + pi + '][titre]'" x-model="phase.titre"
                                placeholder="Nom de la phase"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p x-show="phaseErrors[pi]?.titre" x-text="phaseErrors[pi]?.titre || ''" class="mt-1 text-sm text-red-600"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Durée</label>
                            <input type="text" :name="'phases[' + pi + '][duree]'" x-model="phase.duree"
                                placeholder="ex. 3 mois, 1 semaine"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p x-show="phaseErrors[pi]?.duree" x-text="phaseErrors[pi]?.duree || ''" class="mt-1 text-sm text-red-600"></p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea :name="'phases[' + pi + '][description]'" x-model="phase.description" rows="3"
                            placeholder="Aperçu global du contenu de cette phase"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        <p x-show="phaseErrors[pi]?.description" x-text="phaseErrors[pi]?.description || ''" class="mt-1 text-sm text-red-600"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Objectifs</label>
                        <p class="text-xs text-gray-500 mb-2">Mesurables — ce à quoi ressemble la réussite de cette phase.</p>
                        <div class="space-y-2">
                            <template x-for="(obj, oi) in phase.objectifs" :key="obj.id">
                                <div class="flex gap-2">
                                    <input type="text" :name="'phases[' + pi + '][objectifs][' + oi + ']'"
                                        x-model="obj.value" placeholder="Objectif"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <button type="button" @click="removeItem(phase.objectifs, oi)"
                                        x-show="phase.objectifs.length > 1"
                                        class="px-2 py-1 text-red-500 hover:text-red-700 text-lg font-bold leading-none">&times;</button>
                                </div>
                            </template>
                        </div>
                        <p x-show="phaseErrors[pi]?.objectifs" x-text="phaseErrors[pi]?.objectifs || ''" class="mt-1 text-sm text-red-600"></p>
                        <button type="button" @click="phase.objectifs.push({ id: window.listHelpers.uuid(), value: '' })"
                            class="mt-2 text-sm text-indigo-600 hover:underline">+ Ajouter un objectif</button>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Livrables</label>
                        <p class="text-xs text-gray-500 mb-2">Documents, processus, événements, etc. produits durant cette phase.</p>
                        <div class="space-y-2">
                            <template x-for="(liv, li) in phase.livrables" :key="liv.id">
                                <div class="flex gap-2">
                                    <input type="text" :name="'phases[' + pi + '][livrables][' + li + ']'"
                                        x-model="liv.value" placeholder="Livrable"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <button type="button" @click="removeItem(phase.livrables, li)"
                                        x-show="phase.livrables.length > 1"
                                        class="px-2 py-1 text-red-500 hover:text-red-700 text-lg font-bold leading-none">&times;</button>
                                </div>
                            </template>
                        </div>
                        <p x-show="phaseErrors[pi]?.livrables" x-text="phaseErrors[pi]?.livrables || ''" class="mt-1 text-sm text-red-600"></p>
                        <button type="button" @click="phase.livrables.push({ id: window.listHelpers.uuid(), value: '' })"
                            class="mt-2 text-sm text-indigo-600 hover:underline">+ Ajouter un livrable</button>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ressources requises</label>
                        <p class="text-xs text-gray-500 mb-2">ex. "1 personne à 20% durant toute la phase", "1000 CHF pour l'achat de matériel"</p>
                        <div class="space-y-2">
                            <template x-for="(res, ri) in phase.ressources_necessaires" :key="res.id">
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <input type="text" :name="'phases[' + pi + '][ressources_necessaires][' + ri + '][resource_type]'"
                                        x-model="res.resource_type" placeholder="Type de ressource"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <div class="relative w-full sm:w-32">
                                        <input type="number" :name="'phases[' + pi + '][ressources_necessaires][' + ri + '][amount_needed]'"
                                            x-model="res.amount_needed" placeholder="Quantité" min="0" step="0.01"
                                            class="block w-full rounded-md border-gray-300 shadow-sm pr-12 focus:border-indigo-500 focus:ring-indigo-500">
                                        <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-sm text-gray-400">CHF</span>
                                    </div>
                                    <button type="button" @click="removeItem(phase.ressources_necessaires, ri)"
                                        x-show="phase.ressources_necessaires.length > 1"
                                        class="self-start sm:self-center px-2 py-1 text-red-500 hover:text-red-700 text-lg font-bold leading-none">&times;</button>
                                </div>
                            </template>
                        </div>
                        <p x-show="phaseErrors[pi]?.ressources" x-text="phaseErrors[pi]?.ressources || ''" class="mt-1 text-sm text-red-600"></p>
                        <button type="button" @click="phase.ressources_necessaires.push({ id: window.listHelpers.uuid(), resource_type: '', amount_needed: '' })"
                            class="mt-2 text-sm text-indigo-600 hover:underline">+ Ajouter une ressource</button>
                    </div>
                </div>
            </template>
        </div>

        <button type="button" @click="addPhase()"
            class="mt-4 w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-600 hover:border-indigo-400 hover:text-indigo-600">
            + Ajouter une phase
        </button>
    </div>

    <div x-show="phases.length > 2">
        <h4 class="text-base font-semibold text-gray-800 mb-1">Section 5 — Ressources totales</h4>
        <p class="text-xs text-gray-500 mb-2">Résumé global de toutes les ressources combinées pour l'ensemble des phases.</p>
        <textarea name="ressources_totales" rows="4"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="Résumé des ressources totales…"></textarea>
    </div>

    <div class="flex items-center gap-4 pt-2">
        <x-proposition.previous />
        <x-proposition.next />
    </div>
</div>
