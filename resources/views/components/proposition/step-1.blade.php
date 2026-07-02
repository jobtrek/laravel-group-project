@props(['users'])

<div x-show="step === 1" class="space-y-8">
    <div class="space-y-4">
        <div>
            <x-proposition.input title="Titre" name="titre" :value="old('titre')" x-model="titre" />
            <p x-show="errors.titre" x-text="errors.titre || ''" class="mt-1 text-sm text-red-600"></p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Porteur</label>
            <p class="text-xs text-gray-500 mb-1">Personne garantissant cette proposition</p>
            <p class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                {{auth()->user()->name}}
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Membres</label>
            <p class="text-xs text-gray-500 mb-2">Toutes les personnes impliquées dans le projet</p>
            <div class="space-y-2">
                <template x-for="(membre, idx) in membres" :key="idx">
                    <div class="flex gap-2">
                        <select :name="'membres[' + idx + ']'" x-model="membres[idx]"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Sélectionner un utilisateur…</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    :disabled="membres.some((m, i) => i !== idx && String(m) === '{{ $user->id }}')">
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" @click="removeItem(membres, idx)"
                            x-show="membres.length > 1"
                            class="px-2 py-1 text-red-500 hover:text-red-700 text-lg font-bold leading-none">&times;</button>
                    </div>
                </template>
            </div>
            <p x-show="errors.membres" x-text="errors.membres || ''" class="mt-1 text-sm text-red-600"></p>
            <button type="button" @click="window.listHelpers.add(membres)"
                class="mt-2 text-sm text-indigo-600 hover:underline">+ Ajouter un membre</button>
        </div>
    </div>

    <hr class="border-gray-200">

    <div>
        <x-proposition.text-area name="description" section="Section 1 — Description"
            label="Description"
            description="Courte description du projet. Max 3 paragraphes."
            x-model="description" />
        <p x-show="errors.description" x-text="errors.description || ''" class="mt-1 text-sm text-red-600"></p>
    </div>

    <hr class="border-gray-200">

    <div>
        <h4 class="text-base font-semibold text-gray-800 mb-1">Section 2 — Buts</h4>
        <p class="text-xs text-gray-500 mb-3">Objectifs principaux du projet. Doivent être SMART et alignés avec la mission/vision de la fondation.</p>
        <x-proposition.repeatable-list items="buts" name="buts[]" placeholder="Objectif" add-label="+ Ajouter un objectif" />
        <p x-show="errors.buts" x-text="errors.buts || ''" class="mt-1 text-sm text-red-600"></p>
    </div>

    <hr class="border-gray-200">

    <div>
        <x-proposition.text-area name="perimetre" section="Section 3 — Périmètre"
            label="Périmètre" description="Ce que le projet fera et ne fera pas."
            x-model="perimetre" />
        <p x-show="errors.perimetre" x-text="errors.perimetre || ''" class="mt-1 text-sm text-red-600"></p>
    </div>

    <div class="flex items-center gap-4 pt-2">
        <a href="#" class="text-base text-gray-600 hover:underline">Annuler</a>
        <x-proposition.next />
    </div>
</div>
