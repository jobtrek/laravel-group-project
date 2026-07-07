<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Corrections requises —
                <span class="text-indigo-600">{{ $project->title }}</span>
            </h2>
            <a href="{{ route('propositions') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
                ← Retour aux propositions
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @php $old = old('corrections', []); @endphp

            <div
                class="mb-6 flex items-start gap-3 rounded-lg bg-amber-50 border border-amber-200 p-4 text-sm text-amber-800">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.5"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
                <p>
                    La direction a demandé des précisions sur les champs ci-dessous.
                    Corrigez chaque élément signalé, puis soumettez à nouveau votre proposition.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('projects.revision-submit', $project) }}">
                @csrf
                @if ($comments->hasAny(['title', 'description', 'but', 'perimetre']))
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">
                        Informations générales
                    </p>

                    @if ($comments->has('title'))
                        <x-revision.flagged-field label="Titre du projet" :comment="$comments['title']->content">
                            <input type="text" name="corrections[title]" value="{{ $old['title'] ?? $project->title }}" required
                                maxlength="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm
                                                                               focus:ring-indigo-500 focus:border-indigo-500">
                        </x-revision.flagged-field>
                    @endif

                    @if ($comments->has('description'))
                        <x-revision.flagged-field label="Description" :comment="$comments['description']->content">
                            <textarea name="corrections[description]" rows="5" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm
                                                                               focus:ring-indigo-500 focus:border-indigo-500 resize-none">{{ $old['description'] ?? $project->description }}</textarea>
                        </x-revision.flagged-field>
                    @endif

                    @if ($comments->has('but'))
                        <x-revision.flagged-field label="Objectifs SMART" :comment="$comments['but']->content">
                            <div x-data="{ items: @js($old['but'] ?? $project->but ?? []) }" class="mt-1 space-y-2"> <template
                                    x-for="(item, idx) in items" :key="idx">
                                    <div class="flex gap-2">
                                        <input type="text" :name="'corrections[but][' + idx + ']'" x-model="items[idx]" required
                                            class="block w-full rounded-md border-gray-300 shadow-sm text-sm
                                                                                           focus:ring-indigo-500 focus:border-indigo-500">
                                        <button type="button" @click="items.splice(idx, 1)" x-show="items.length > 1"
                                            class="px-2 py-1 text-red-500 hover:text-red-700 text-lg font-bold leading-none">&times;</button>
                                    </div>
                                </template>
                                <button type="button" @click="items.push('')"
                                    class="mt-1 text-sm text-indigo-600 hover:underline">
                                    + Ajouter un objectif
                                </button>
                            </div>
                        </x-revision.flagged-field>
                    @endif

                    @if ($comments->has('perimetre'))
                        <x-revision.flagged-field label="Périmètre" :comment="$comments['perimetre']->content">
                            <textarea name="corrections[perimetre]" rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm
                                                                               focus:ring-indigo-500 focus:border-indigo-500 resize-none">{{ $old['perimetre'] ?? $project->perimetre }}</textarea>
                        </x-revision.flagged-field>
                    @endif
                @endif

                @if ($comments->hasAny(['evaluation.portee', 'evaluation.impact', 'evaluation.confiance', 'evaluation.effort']))
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mt-8 mb-3">
                        Évaluation ICE
                    </p>

                    @if ($comments->has('evaluation.portee'))
                        <x-revision.flagged-field label="Portée (0 – 50)" :comment="$comments['evaluation.portee']->content">
                            <input type="number" name="corrections[evaluation.portee]"
                                value="{{ $old['evaluation.portee'] ?? $project->evaluation->portee }}" min="0" max="50"
                                required class="mt-1 block w-32 rounded-md border-gray-300 shadow-sm text-sm
                                                                               focus:ring-indigo-500 focus:border-indigo-500">
                        </x-revision.flagged-field>
                    @endif

                    @if ($comments->has('evaluation.impact'))
                        @php $currentImpact = $old['evaluation.impact'] ?? $project->evaluation->impact; @endphp
                        <x-revision.flagged-field label="Impact (1 – 5)" :comment="$comments['evaluation.impact']->content">
                            <select name="corrections[evaluation.impact]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm
                                                                               focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Sélectionner…</option>
                                <option value="1" @selected($currentImpact == 1)>1 — Invisible au niveau de la fondation</option>
                                <option value="2" @selected($currentImpact == 2)>2 — Effet mesurable mais limité</option>
                                <option value="3" @selected($currentImpact == 3)>3 — Crée une nouvelle capacité pour Jobtrek
                                </option>
                                <option value="4" @selected($currentImpact == 4)>4 — Change considérablement le fonctionnement
                                </option>
                                <option value="5" @selected($currentImpact == 5)>5 — Transformationnel</option>
                            </select>
                        </x-revision.flagged-field>
                    @endif

                    @if ($comments->has('evaluation.confiance'))
                        <x-revision.flagged-field label="Confiance (0 – 100 %)"
                            :comment="$comments['evaluation.confiance']->content">
                            <div class="flex items-center gap-2 mt-1">
                                <input type="number" name="corrections[evaluation.confiance]"
                                    value="{{ $old['evaluation.confiance'] ?? $project->evaluation->confiance }}" min="0"
                                    max="100" required
                                    class="block w-24 rounded-md border-gray-300 shadow-sm text-sm
                                                                                   focus:ring-indigo-500 focus:border-indigo-500">
                                <span class="text-sm text-gray-500">%</span>
                            </div>
                        </x-revision.flagged-field>
                    @endif

                    @if ($comments->has('evaluation.effort'))
                        @php $currentEffort = $old['evaluation.effort'] ?? $project->evaluation->effort; @endphp
                        <x-revision.flagged-field label="Effort (1 – 5)" :comment="$comments['evaluation.effort']->content">
                            <select name="corrections[evaluation.effort]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm
                                                                               focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Sélectionner…</option>
                                <option value="1" @selected($currentEffort == 1)>1 — Quelques jours (≤ 1 semaine ETP)</option>
                                <option value="2" @selected($currentEffort == 2)>2 — Quelques semaines (1 à 4 semaines ETP)
                                </option>
                                <option value="3" @selected($currentEffort == 3)>3 — Plusieurs mois à temps partiel</option>
                                <option value="4" @selected($currentEffort == 4)>4 — Plus d'un semestre</option>
                                <option value="5" @selected($currentEffort == 5)>5 — Plusieurs années</option>
                            </select>
                        </x-revision.flagged-field>
                    @endif
                @endif

                {{-- PHASES --}}
                @foreach ($project->phases as $i => $phase)
                    @php
                        $phaseKeys = [
                            "phases.{$i}.titre",
                            "phases.{$i}.duree",
                            "phases.{$i}.description",
                            "phases.{$i}.objectifs",
                            "phases.{$i}.livrables",
                            "phases.{$i}.ressources",
                        ];
                        $hasPhaseComments = collect($phaseKeys)->contains(fn($k) => $comments->has($k));
                    @endphp

                    @if ($hasPhaseComments)
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mt-8 mb-3">
                            Phase {{ $i + 1 }} — {{ $phase->name }}
                        </p>

                        @if ($comments->has("phases.{$i}.titre"))
                            <x-revision.flagged-field label="Titre" :comment="$comments['phases.' . $i . '.titre']->content">
                                <input type="text" name="corrections[phases.{{ $i }}.titre]"
                                    value="{{ $old["phases.{$i}.titre"] ?? $phase->name }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm
                                                                                                       focus:ring-indigo-500 focus:border-indigo-500">
                            </x-revision.flagged-field>
                        @endif

                        @if ($comments->has("phases.{$i}.duree"))
                            <x-revision.flagged-field label="Durée" :comment="$comments['phases.' . $i . '.duree']->content">
                                <input type="text" name="corrections[phases.{{ $i }}.duree]"
                                    value="{{ $old["phases.{$i}.duree"] ?? $phase->duration }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm
                                                                                                       focus:ring-indigo-500 focus:border-indigo-500">
                            </x-revision.flagged-field>
                        @endif

                        @if ($comments->has("phases.{$i}.description"))
                            <x-revision.flagged-field label="Description" :comment="$comments['phases.' . $i . '.description']->content">
                                <textarea name="corrections[phases.{{ $i }}.description]" rows="4" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm
                                                                                                       focus:ring-indigo-500 focus:border-indigo-500 resize-none">{{ $old["phases.{$i}.description"] ?? $phase->description }}</textarea>
                            </x-revision.flagged-field>
                        @endif

                        @if ($comments->has("phases.{$i}.objectifs"))
                            <x-revision.flagged-field label="Objectifs" :comment="$comments['phases.' . $i . '.objectifs']->content">
                                <div x-data="{ items: @js($old["phases.{$i}.objectifs"] ?? $phase->objectifs) }"
                                    class="mt-1 space-y-2">
                                    <template x-for="(item, idx) in items" :key="idx">
                                        <div class="flex gap-2">
                                            <input type="text" :name="'corrections[phases.{{ $i }}.objectifs][' + idx + ']'"
                                                x-model="items[idx]" required
                                                class="block w-full rounded-md border-gray-300 shadow-sm text-sm
                                                                                                                   focus:ring-indigo-500 focus:border-indigo-500">
                                            <button type="button" @click="items.splice(idx, 1)" x-show="items.length > 1"
                                                class="px-2 py-1 text-red-500 hover:text-red-700 text-lg font-bold leading-none">&times;</button>
                                        </div>
                                    </template>
                                    <button type="button" @click="items.push('')"
                                        class="mt-1 text-sm text-indigo-600 hover:underline">
                                        + Ajouter un objectif
                                    </button>
                                </div>
                            </x-revision.flagged-field>
                        @endif

                        @if ($comments->has("phases.{$i}.livrables"))
                            <x-revision.flagged-field label="Livrables" :comment="$comments['phases.' . $i . '.livrables']->content">
                                <div x-data="{ items: @js($old["phases.{$i}.livrables"] ?? $phase->livrables) }"
                                    class="mt-1 space-y-2">
                                    <template x-for="(item, idx) in items" :key="idx">
                                        <div class="flex gap-2">
                                            <input type="text" :name="'corrections[phases.{{ $i }}.livrables][' + idx + ']'"
                                                x-model="items[idx]" required
                                                class="block w-full rounded-md border-gray-300 shadow-sm text-sm
                                                                                                                   focus:ring-indigo-500 focus:border-indigo-500">
                                            <button type="button" @click="items.splice(idx, 1)" x-show="items.length > 1"
                                                class="px-2 py-1 text-red-500 hover:text-red-700 text-lg font-bold leading-none">&times;</button>
                                        </div>
                                    </template>
                                    <button type="button" @click="items.push('')"
                                        class="mt-1 text-sm text-indigo-600 hover:underline">
                                        + Ajouter un livrable
                                    </button>
                                </div>
                            </x-revision.flagged-field>
                        @endif

                        @if ($comments->has("phases.{$i}.ressources"))
                            @php
                                $defaultResources = $phase->resources->map(fn($r) => [
                                    'resource_type' => $r->resource_type,
                                    'amount_needed' => $r->amount_needed,
                                ])->values()->toArray();
                                $initResources = $old["phases.{$i}.ressources"] ?? $defaultResources;
                            @endphp
                            <x-revision.flagged-field label="Ressources nécessaires" :comment="$comments['phases.' . $i . '.ressources']->content">
                                <div x-data="{ resources: @js($initResources) }" class="mt-1 space-y-2">
                                    <template x-for="(res, ri) in resources" :key="ri">
                                        <div class="flex gap-2 items-center">
                                            <input type="text"
                                                :name="'corrections[phases.{{ $i }}.ressources][' + ri + '][resource_type]'"
                                                x-model="res.resource_type" placeholder="Type de ressource" required
                                                class="block w-full rounded-md border-gray-300 shadow-sm text-sm
                                                                                                                   focus:ring-indigo-500 focus:border-indigo-500">
                                            <input type="number"
                                                :name="'corrections[phases.{{ $i }}.ressources][' + ri + '][amount_needed]'"
                                                x-model="res.amount_needed" placeholder="Montant (CHF)" min="0" step="0.01" required
                                                class="block w-36 rounded-md border-gray-300 shadow-sm text-sm
                                                                                                                   focus:ring-indigo-500 focus:border-indigo-500">
                                            <button type="button" @click="resources.splice(ri, 1)" x-show="resources.length > 1"
                                                class="px-2 py-1 text-red-500 hover:text-red-700 text-lg font-bold leading-none">&times;</button>
                                        </div>
                                    </template>
                                    <button type="button" @click="resources.push({ resource_type: '', amount_needed: '' })"
                                        class="mt-1 text-sm text-indigo-600 hover:underline">
                                        + Ajouter une ressource
                                    </button>
                                </div>
                            </x-revision.flagged-field>
                        @endif
                    @endif
                @endforeach

                <div class="flex justify-end mt-8">
                    <button type="submit" class="px-8 py-3 bg-indigo-600 text-white font-semibold rounded-lg
                               shadow-lg hover:bg-indigo-700 active:bg-indigo-800
                               focus:outline-none focus:ring-2 focus:ring-indigo-500
                               focus:ring-offset-2 transition-colors">
                        Soumettre les corrections
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>
