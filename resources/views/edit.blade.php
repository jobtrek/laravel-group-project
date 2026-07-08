<x-app-layout>
    <div class="items-center flex justify-center gap-10">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Modifier le projet —
            <span class="text-indigo-200">{{ $project->title }}</span>
        </h2>
        <div class="flex bg-gray-50 rounded-xl pr-4 pl-3 p-1">
            <x-projects-details.comeBackButton :fallback-url="route('projects-details', $project)"/>
        </div>
    </div>
    <div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @php
                $butData = old('but', $project->but ?? []);
                $butData = count($butData) ? array_values($butData) : [''];

                $phasesData = old('phases', $project->phases->map(fn ($phase) => [
                    'id' => $phase->id,
                    'titre' => $phase->name,
                    'duree' => $phase->duration,
                    'description' => $phase->description,
                    'objectifs' => $phase->objectifs ?: [''],
                    'livrables' => $phase->livrables ?: [''],
                    'ressources' => $phase->resources->map(fn ($resource) => [
                        'id' => $resource->id,
                        'resource_type' => $resource->resource_type,
                        'amount_needed' => (string) $resource->amount_needed,
                    ])->all(),
                ])->all());

                if (! count($phasesData)) {
                    $phasesData = [[
                        'id' => null, 'titre' => '', 'duree' => '', 'description' => '',
                        'objectifs' => [''], 'livrables' => [''], 'ressources' => [],
                    ]];
                }

            @endphp

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                    method="POST"
                    action="{{ route('projects.update', $project) }}"
                    x-data="{
                    title: @js(old('title', $project->title)),
                    description: @js(old('description', $project->description)),
                    perimetre: @js(old('perimetre', $project->perimetre)),
                    but: @js($butData),
                    membres: @js(old('membres', $project->members->pluck('id')->map(fn ($id) => (string) $id)->all())),
                    phases: @js($phasesData),
                    portee: @js(old('portee', $project->evaluation->portee)),
                    impact: @js(old('impact', $project->evaluation->impact)),
                    confiance: @js(old('confiance', $project->evaluation->confiance)),
                    effort: @js(old('effort', $project->evaluation->effort)),

                    addBut() { this.but.push(''); },
                    removeBut(i) { this.but.splice(i, 1); },

                    addMembre() { this.membres.push(''); },
                    removeMembre(i) { this.membres.splice(i, 1); },

                    addPhase() {
                        this.phases.push({
                            id: null, titre: '', duree: '', description: '',
                            objectifs: [''], livrables: [''], ressources: [],
                        });
                    },
                    removePhase(i) { this.phases.splice(i, 1); },

                    addObjectif(p) { this.phases[p].objectifs.push(''); },
                    removeObjectif(p, i) { this.phases[p].objectifs.splice(i, 1); },

                    addLivrable(p) { this.phases[p].livrables.push(''); },
                    removeLivrable(p, i) { this.phases[p].livrables.splice(i, 1); },

                    addResource(p) {
                        this.phases[p].ressources.push({ id: null, resource_type: '', amount_needed: '' });
                    },
                    removeResource(p, i) { this.phases[p].ressources.splice(i, 1); },
                }"
            >
                @csrf
                @method('PATCH')

                <p class="text-xs font-bold uppercase tracking-widest text-white mb-3 mt-8">
                    Informations générales
                </p>

                <div class="bg-white shadow-sm rounded-lg p-5 mb-3 border border-gray-100">
                    <p class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-2">Titre du projet</p>
                    <input type="text" name="title" x-model="title"
                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                <div class="bg-white shadow-sm rounded-lg p-5 mb-3 border border-gray-100">
                    <p class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-2">Description</p>
                    <textarea name="description" x-model="description" rows="4"
                              class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                </div>

                <div class="bg-white shadow-sm rounded-lg p-5 mb-3 border border-gray-100">
                    <p class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-2">Périmètre</p>
                    <textarea name="perimetre" x-model="perimetre" rows="3"
                              class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                </div>

                <div class="bg-white shadow-sm rounded-lg p-5 mb-3 border border-gray-100">
                    <p class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-2">Membres</p>
                    <p class="text-xs text-gray-500 mb-2">Toutes les personnes impliquées dans le projet</p>
                    <div class="space-y-2">
                        <template x-for="(membre, idx) in membres" :key="idx">
                            <div class="flex gap-2">
                                <select :name="'membres[' + idx + ']'" x-model="membres[idx]"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">Sélectionner un utilisateur…</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}"
                                                :disabled="membres.some((m, i) => i !== idx && String(m) === '{{ $user->id }}')">
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" @click="removeMembre(idx)"
                                        x-show="membres.length > 1"
                                        class="shrink-0 px-2 py-1 text-red-500 hover:text-red-700 text-lg font-bold leading-none">
                                    &times;
                                </button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="addMembre()"
                            class="mt-2 text-sm text-indigo-600 hover:underline">+ Ajouter un membre
                    </button>
                </div>

                <p class="text-xs font-bold uppercase tracking-widest text-white mb-3 mt-8">
                    Buts
                </p>

                <div class="bg-white shadow-sm rounded-lg p-5 mb-3 border border-gray-100">
                    <div class="space-y-2">
                        <template x-for="(item, bIndex) in but" :key="bIndex">
                            <div class="flex gap-2">
                                <input type="text" x-model="but[bIndex]" :name="`but[${bIndex}]`"
                                       class="flex-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <button type="button" @click="removeBut(bIndex)" x-show="but.length > 1"
                                        class="text-red-500 hover:text-red-700 text-sm px-2">✕
                                </button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="addBut()"
                            class="mt-2 text-sm text-indigo-600 hover:text-indigo-800">+ Ajouter un but
                    </button>
                </div>

                <p class="text-xs font-bold uppercase tracking-widest text-white mb-3 mt-8">
                    Évaluation
                </p>

                <div class="bg-white shadow-sm rounded-lg p-5 mb-3 border border-gray-100">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Portée</label>
                            <input type="number" step="0.01" min="0" max="50" name="portee" x-model="portee"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Impact <span
                                        class="text-gray-400 font-normal">/ 5</span></label>
                            <input type="number" step="1" min="1" max="5" name="impact" x-model="impact"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confiance <span
                                        class="text-gray-400 font-normal">%</span></label>
                            <input type="number" step="1" min="0" max="100" name="confiance" x-model="confiance"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Effort <span
                                        class="text-gray-400 font-normal">/ 5</span></label>
                            <input type="number" step="1" min="1" max="5" name="effort" x-model="effort"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                    </div>
                </div>

                <p class="text-xs font-bold uppercase tracking-widest text-white mb-3 mt-8">
                    Phases
                </p>

                <template x-for="(phase, pIndex) in phases" :key="pIndex">
                    <div class="bg-white shadow-sm rounded-lg p-5 mb-3 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-sm font-semibold text-gray-400 uppercase tracking-widest">
                                Phase <span x-text="pIndex + 1"></span>
                            </p>
                            <button type="button" @click="removePhase(pIndex)" x-show="phases.length > 1"
                                    class="text-red-500 hover:text-red-700 text-sm">✕ Supprimer
                            </button>
                        </div>

                        <input type="hidden" :name="`phases[${pIndex}][id]`" :value="phase.id ?? ''">

                        <div class="mb-4">
                            <p class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-2">Titre</p>
                            <input type="text" x-model="phase.titre" :name="`phases[${pIndex}][titre]`"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>

                        <div class="mb-4">
                            <p class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-2">Durée</p>
                            <input type="text" x-model="phase.duree" :name="`phases[${pIndex}][duree]`"
                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>

                        <div class="mb-4">
                            <p class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-2">Description</p>
                            <textarea x-model="phase.description" :name="`phases[${pIndex}][description]`" rows="3"
                                      class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-2">Objectifs</p>
                            <div class="space-y-2">
                                <template x-for="(obj, oIndex) in phase.objectifs" :key="oIndex">
                                    <div class="flex gap-2">
                                        <input type="text" x-model="phase.objectifs[oIndex]"
                                               :name="`phases[${pIndex}][objectifs][${oIndex}]`"
                                               class="flex-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        <button type="button" @click="removeObjectif(pIndex, oIndex)"
                                                x-show="phase.objectifs.length > 1"
                                                class="text-red-500 hover:text-red-700 text-sm px-2">✕
                                        </button>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="addObjectif(pIndex)"
                                    class="mt-2 text-sm text-indigo-600 hover:text-indigo-800">+ Ajouter un objectif
                            </button>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-2">Livrables</p>
                            <div class="space-y-2">
                                <template x-for="(liv, lIndex) in phase.livrables" :key="lIndex">
                                    <div class="flex gap-2">
                                        <input type="text" x-model="phase.livrables[lIndex]"
                                               :name="`phases[${pIndex}][livrables][${lIndex}]`"
                                               class="flex-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        <button type="button" @click="removeLivrable(pIndex, lIndex)"
                                                x-show="phase.livrables.length > 1"
                                                class="text-red-500 hover:text-red-700 text-sm px-2">✕
                                        </button>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="addLivrable(pIndex)"
                                    class="mt-2 text-sm text-indigo-600 hover:text-indigo-800">+ Ajouter un livrable
                            </button>
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-2">Ressources nécessaires</p>

                            <div class="overflow-x-auto">
                                <table class="w-full text-sm border-collapse mb-2 min-w-[320px]">
                                    <thead>
                                    <tr class="text-left">
                                        <th class="pb-1 text-xs font-semibold text-gray-500 pr-4">Type</th>
                                        <th class="pb-1 text-xs font-semibold text-gray-500 pr-4">Montant nécessaire
                                            (CHF)
                                        </th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <template x-for="(resource, rIndex) in phase.ressources" :key="rIndex">
                                        <tr class="border-t border-gray-100">
                                            <td class="py-1 pr-4">
                                                <input type="hidden"
                                                       :name="`phases[${pIndex}][ressources][${rIndex}][id]`"
                                                       :value="resource.id ?? ''">
                                                <input type="text" x-model="resource.resource_type"
                                                       :name="`phases[${pIndex}][ressources][${rIndex}][resource_type]`"
                                                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            </td>
                                            <td class="py-1 pr-4">
                                                <input type="number" step="0.01" min="0"
                                                       x-model="resource.amount_needed"
                                                       :name="`phases[${pIndex}][ressources][${rIndex}][amount_needed]`"
                                                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            </td>
                                            <td class="py-1">
                                                <button type="button" @click="removeResource(pIndex, rIndex)"
                                                        class="text-red-500 hover:text-red-700 text-sm">✕
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    </tbody>
                                </table>
                                <button type="button" @click="addResource(pIndex)"
                                        class="text-sm text-indigo-600 hover:text-indigo-800">+ Ajouter une
                                    ressource
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <button type="button" @click="addPhase()"
                        class="mb-8 w-full py-3 border-2 border-dashed border-white/30 rounded-lg text-sm font-medium text-white hover:border-white/60 hover:bg-white/5 transition">
                    + Ajouter une phase
                </button>

                <div class="sticky bottom-6 flex justify-end mt-8">
                    <button type="submit"
                            class="px-8 py-3 bg-indigo-600 text-white font-semibold rounded-lg
                               shadow-lg hover:bg-indigo-700 active:bg-indigo-800
                               focus:outline-none focus:ring-2 focus:ring-indigo-500
                               focus:ring-offset-2 transition-colors">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
