<div x-show="step === 3" class="space-y-8">
    <div>
        <h4 class="text-base font-semibold text-gray-800 mb-1">Section 6 — Évaluation</h4>
        <p class="text-xs text-gray-500 mb-4">Matrice d'évaluation de l'impact de la fondation. Les quatre scores sont obligatoires.</p>

        <div class="space-y-6">
            <div>
                <label for="portee" class="block text-sm font-medium text-gray-700">
                    Portée <span class="text-gray-400 font-normal">(0 – 50)</span>
                </label>
                <p class="text-xs text-gray-500 mb-1">Les collaborateurs directs comptent pour 1 pt chacun, les partenaires comptent pour 0,5 pt chacun.</p>
                <input type="number" id="portee" name="portee" min="0" max="50"
                    x-model="portee"
                    value="{{ old('portee') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p x-show="errors.portee" x-text="errors.portee || ''" class="mt-1 text-sm text-red-600"></p>
            </div>

            <div>
                <label for="impact" class="block text-sm font-medium text-gray-700">
                    Impact <span class="text-gray-400 font-normal">(1 – 5)</span>
                </label>
                <select id="impact" name="impact" x-model="impact"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Sélectionner…</option>
                    <option value="1" {{ old('impact') == '1' ? 'selected' : '' }}>1 — Invisible au niveau de la fondation</option>
                    <option value="2" {{ old('impact') == '2' ? 'selected' : '' }}>2 — Effet mesurable mais limité</option>
                    <option value="3" {{ old('impact') == '3' ? 'selected' : '' }}>3 — Crée une nouvelle capacité pour Jobtrek</option>
                    <option value="4" {{ old('impact') == '4' ? 'selected' : '' }}>4 — Change considérablement le fonctionnement des cercles/produits</option>
                    <option value="5" {{ old('impact') == '5' ? 'selected' : '' }}>5 — Transformationnel</option>
                </select>
                <p x-show="errors.impact" x-text="errors.impact || ''" class="mt-1 text-sm text-red-600"></p>
                <x-proposition.scale-toggle toggle-label="Voir l'échelle d'impact" hide-label="Masquer l'échelle">
                    <p><strong>1</strong> — Invisible au niveau de la fondation — négligeable, ne concerne que quelques rôles</p>
                    <p><strong>2</strong> — Effet mesurable mais limité — restreint à un produit ou un cercle, pas forcément durable</p>
                    <p><strong>3</strong> — Crée une nouvelle "capacité" pour Jobtrek — amélioration touchant plusieurs produits ou cercles au fil du temps</p>
                    <p><strong>4</strong> — Change considérablement le fonctionnement des cercles/produits — durable, crée un avantage important</p>
                    <p><strong>5</strong> — Transformationnel — la fondation entre dans une nouvelle ère, changement global et durable sur une décennie</p>
                </x-proposition.scale-toggle>
            </div>

            <div>
                <label for="confiance" class="block text-sm font-medium text-gray-700">
                    Confiance <span class="text-gray-400 font-normal">(0 – 100 %)</span>
                </label>
                <p class="text-xs text-gray-500 mb-1">Certitude que le projet réussira et sera mené à terme.</p>
                <div class="flex items-center gap-2 mt-1">
                    <input type="number" id="confiance" name="confiance" min="0" max="100"
                        x-model="confiance"
                        value="{{ old('confiance') }}"
                        class="block w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <span class="text-sm text-gray-500">%</span>
                </div>
                <p x-show="errors.confiance" x-text="errors.confiance || ''" class="mt-1 text-sm text-red-600"></p>
            </div>

            <div>
                <label for="effort" class="block text-sm font-medium text-gray-700">
                    Effort <span class="text-gray-400 font-normal">(1 – 5)</span>
                </label>
                <select id="effort" name="effort" x-model="effort"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Sélectionner…</option>
                    <option value="1" {{ old('effort') == '1' ? 'selected' : '' }}>1 — Quelques jours (≤ 1 semaine ETP, &lt; 200 CHF)</option>
                    <option value="2" {{ old('effort') == '2' ? 'selected' : '' }}>2 — Quelques semaines (1 à 4 semaines ETP, &lt; 2 000 CHF)</option>
                    <option value="3" {{ old('effort') == '3' ? 'selected' : '' }}>3 — Plusieurs mois à temps partiel (1 à 3 mois ETP, 2 000 à 10 000 CHF)</option>
                    <option value="4" {{ old('effort') == '4' ? 'selected' : '' }}>4 — Plus d'un semestre (3 à 6 mois ETP, 10 000 à 50 000 CHF)</option>
                    <option value="5" {{ old('effort') == '5' ? 'selected' : '' }}>5 — Plusieurs années à &gt;100% ETP (&gt; 50 000 CHF)</option>
                </select>
                <p x-show="errors.effort" x-text="errors.effort || ''" class="mt-1 text-sm text-red-600"></p>
                <x-proposition.scale-toggle toggle-label="Voir l'échelle d'effort" hide-label="Masquer l'échelle">
                    <p><strong>1</strong> — Quelques jours de travail (≤ 1 semaine ETP). Très peu de ressources financières (&lt; 200 CHF). Logistique quasi nulle.</p>
                    <p><strong>2</strong> — Quelques semaines (1 à 4 semaines ETP). Investissement financier mineur (&lt; 2 000 CHF). Coordination au sein d'une seule équipe autonome.</p>
                    <p><strong>3</strong> — Plusieurs mois à temps partiel (1 à 3 mois ETP). Coût externe modéré (2 000 à 10 000 CHF). Nécessite une synchronisation entre équipes.</p>
                    <p><strong>4</strong> — Plus d'un semestre (3 à 6 mois ETP). Coût externe important (10 000 à 50 000 CHF). Comité de pilotage nécessaire.</p>
                    <p><strong>5</strong> — Plusieurs années à &gt;100% ETP en continu. Investissement lourd (&gt; 50 000 CHF). Validation multi-phases requise.</p>
                </x-proposition.scale-toggle>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-4 pt-2">
        <x-proposition.previous />
        <button type="submit"
        @click.prevent="validateStep3() && $el.closest('form').submit()"
            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
            Soumettre
        </button>
    </div>
</div>
