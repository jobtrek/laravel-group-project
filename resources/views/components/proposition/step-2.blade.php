<div x-show="step === 2" class="space-y-8">
    <div>
        <h4 class="text-base font-semibold text-gray-800 mb-1">Section 4 — Phases du projet</h4>
        <p class="text-xs text-gray-500 mb-4">Add as many phases as needed. Each has its own objectives, deliverables,
            and resources.</p>

        <div class="space-y-6">
            <template x-for="(phase, pi) in phases" :key="pi">
                <div class="border border-gray-200 rounded-lg p-5 space-y-4 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <h5 class="font-semibold text-gray-800" x-text="'Phase ' + (pi + 1)"></h5>
                        <button type="button" @click="removePhase(pi)" x-show="phases.length > 1"
                            class="text-sm text-red-500 hover:text-red-700">Remove phase</button>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" :name="'phases[' + pi + '][titre]'" x-model="phase.titre"
                                placeholder="Phase name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p x-show="phaseErrors[pi]?.titre" x-text="phaseErrors[pi]?.titre || ''" class="mt-1 text-sm text-red-600"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Duration</label>
                            <input type="text" :name="'phases[' + pi + '][duree]'" x-model="phase.duree"
                                placeholder="e.g. 3 mois, 1 semaine"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p x-show="phaseErrors[pi]?.duree" x-text="phaseErrors[pi]?.duree || ''" class="mt-1 text-sm text-red-600"></p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea :name="'phases[' + pi + '][description]'" x-model="phase.description" rows="3"
                            placeholder="Overview of what this phase covers"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        <p x-show="phaseErrors[pi]?.description" x-text="phaseErrors[pi]?.description || ''" class="mt-1 text-sm text-red-600"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Objectives</label>
                        <p class="text-xs text-gray-500 mb-2">Measurable — what success looks like for this phase.</p>
                        <div class="space-y-2">
                            <template x-for="(obj, oi) in phase.objectifs" :key="oi">
                                <div class="flex gap-2">
                                    <input type="text" :name="'phases[' + pi + '][objectifs][' + oi + ']'"
                                        x-model="phase.objectifs[oi]" placeholder="Objective"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <button type="button" @click="removeItem(phase.objectifs, oi)"
                                        x-show="phase.objectifs.length > 1"
                                        class="px-2 py-1 text-red-500 hover:text-red-700 text-lg font-bold leading-none">&times;</button>
                                </div>
                            </template>
                        </div>
                        <p x-show="phaseErrors[pi]?.objectifs" x-text="phaseErrors[pi]?.objectifs || ''" class="mt-1 text-sm text-red-600"></p>
                        <button type="button" @click="phase.objectifs.push('')"
                            class="mt-2 text-sm text-indigo-600 hover:underline">+ Add objective</button>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deliverables</label>
                        <p class="text-xs text-gray-500 mb-2">Documents, processes, events, etc. produced in this phase.
                        </p>
                        <div class="space-y-2">
                            <template x-for="(liv, li) in phase.livrables" :key="li">
                                <div class="flex gap-2">
                                    <input type="text" :name="'phases[' + pi + '][livrables][' + li + ']'"
                                        x-model="phase.livrables[li]" placeholder="Deliverable"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <button type="button" @click="removeItem(phase.livrables, li)"
                                        x-show="phase.livrables.length > 1"
                                        class="px-2 py-1 text-red-500 hover:text-red-700 text-lg font-bold leading-none">&times;</button>
                                </div>
                            </template>
                        </div>
                        <p x-show="phaseErrors[pi]?.livrables" x-text="phaseErrors[pi]?.livrables || ''" class="mt-1 text-sm text-red-600"></p>
                        <button type="button" @click="phase.livrables.push('')"
                            class="mt-2 text-sm text-indigo-600 hover:underline">+ Add deliverable</button>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Required resources</label>
                        <p class="text-xs text-gray-500 mb-2">e.g. "1 personne à 20% durant toute la phase", "1000 CHF
                            pour l'achat de matériel"</p>
                        <div class="space-y-2">
                            <template x-for="(res, ri) in phase.ressources_necessaires" :key="ri">
                                <div class="flex gap-2">
                                    <input type="text" :name="'phases[' + pi + '][ressources_necessaires][' + ri + '][resource_type]'"
                                        x-model="res.resource_type" placeholder="Resource type"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <input type="number" :name="'phases[' + pi + '][ressources_necessaires][' + ri + '][amount_needed]'"
                                        x-model="res.amount_needed" placeholder="Amount needed" min="0" step="0.01"
                                        class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <button type="button" @click="removeItem(phase.ressources_necessaires, ri)"
                                        x-show="phase.ressources_necessaires.length > 1"
                                        class="px-2 py-1 text-red-500 hover:text-red-700 text-lg font-bold leading-none">&times;</button>
                                </div>
                            </template>
                        </div>
                        <p x-show="phaseErrors[pi]?.ressources" x-text="phaseErrors[pi]?.ressources || ''" class="mt-1 text-sm text-red-600"></p>
                        <button type="button" @click="phase.ressources_necessaires.push({ resource_type: '', amount_needed: '' })"
                            class="mt-2 text-sm text-indigo-600 hover:underline">+ Add resource</button>
                    </div>
                </div>
            </template>
        </div>

        <button type="button" @click="addPhase()"
            class="mt-4 w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-600 hover:border-indigo-400 hover:text-indigo-600">
            + Add phase
        </button>
    </div>

    <div x-show="phases.length > 2">
        <h4 class="text-base font-semibold text-gray-800 mb-1">Section 5 — Ressources totales</h4>
        <p class="text-xs text-gray-500 mb-2">Summary of all resources across all phases combined.</p>
        <textarea name="ressources_totales" rows="4"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="Total resources summary…"></textarea>
    </div>

    <div class="flex items-center gap-4 pt-2">
        <x-proposition.previous />
        <x-proposition.next />
    </div>
</div>
