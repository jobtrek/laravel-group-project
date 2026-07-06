<div x-show="step === 1" class="space-y-8">
    <div class="space-y-4">
        <div>
            <x-proposition.input title="Titre" name="titre" :value="old('titre')" x-model="titre" />
            <p x-show="errors.titre" x-text="errors.titre || ''" class="mt-1 text-sm text-red-600"></p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Porteur</label>
            <p class="text-xs text-gray-500 mb-1">Personne garantissant cette proposition</p>
            <p class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                {{auth()->user()->name}}
            </p>
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
