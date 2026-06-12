<div x-show="step === 3" class="space-y-8">
    <div>
        <h4 class="text-base font-semibold text-gray-800 mb-1">Section 6 — Évaluation</h4>
        <p class="text-xs text-gray-500 mb-4">The foundation's impact scoring matrix. All four scores are required.</p>

        <div class="space-y-6">
            <div>
                <label for="portee" class="block text-sm font-medium text-gray-700">
                    Portée <span class="text-gray-400 font-normal">(0 – 50)</span>
                </label>
                <p class="text-xs text-gray-500 mb-1">Direct collaborators count 1 pt each, partners count 0.5 pt each.</p>
                <input type="number" id="portee" name="portee" min="0" max="50"
                    value="{{ old('portee') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label for="impact" class="block text-sm font-medium text-gray-700">
                    Impact <span class="text-gray-400 font-normal">(1 – 5)</span>
                </label>
                <select id="impact" name="impact"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select…</option>
                    <option value="1" {{ old('impact') == '1' ? 'selected' : '' }}>1 — Invisible at foundation level</option>
                    <option value="2" {{ old('impact') == '2' ? 'selected' : '' }}>2 — Measurable but limited effect</option>
                    <option value="3" {{ old('impact') == '3' ? 'selected' : '' }}>3 — Creates a new capacity for Jobtrek</option>
                    <option value="4" {{ old('impact') == '4' ? 'selected' : '' }}>4 — Significantly changes how circles/products operate</option>
                    <option value="5" {{ old('impact') == '5' ? 'selected' : '' }}>5 — Transformational</option>
                </select>
                <x-proposition.scale-toggle toggle-label="See impact scale" hide-label="Hide scale">
                    <p><strong>1</strong> — Invisible at the foundation level — negligible, concerns only a few roles</p>
                    <p><strong>2</strong> — Measurable but limited effect — restricted to one product or circle, not necessarily lasting</p>
                    <p><strong>3</strong> — Creates a new "capacity" for Jobtrek — improvement touching several products or circles over time</p>
                    <p><strong>4</strong> — Significantly changes how circles/products operate — durable, creates an important advantage</p>
                    <p><strong>5</strong> — Transformational — the foundation enters a new era, global and lasting change over a decade</p>
                </x-proposition.scale-toggle>
            </div>

            <div>
                <label for="confiance" class="block text-sm font-medium text-gray-700">
                    Confiance <span class="text-gray-400 font-normal">(0 – 100 %)</span>
                </label>
                <p class="text-xs text-gray-500 mb-1">Certainty the project will succeed and be completed.</p>
                <div class="flex items-center gap-2 mt-1">
                    <input type="number" id="confiance" name="confiance" min="0" max="100"
                        value="{{ old('confiance') }}"
                        class="block w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <span class="text-sm text-gray-500">%</span>
                </div>
            </div>

            <div>
                <label for="effort" class="block text-sm font-medium text-gray-700">
                    Effort <span class="text-gray-400 font-normal">(1 – 5)</span>
                </label>
                <select id="effort" name="effort"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select…</option>
                    <option value="1" {{ old('effort') == '1' ? 'selected' : '' }}>1 — A few days (≤ 1 week ETP, &lt; 200 CHF)</option>
                    <option value="2" {{ old('effort') == '2' ? 'selected' : '' }}>2 — A few weeks (1–4 weeks ETP, &lt; 2 000 CHF)</option>
                    <option value="3" {{ old('effort') == '3' ? 'selected' : '' }}>3 — Several months part-time (1–3 months ETP, 2 000–10 000 CHF)</option>
                    <option value="4" {{ old('effort') == '4' ? 'selected' : '' }}>4 — More than a semester (3–6 months ETP, 10 000–50 000 CHF)</option>
                    <option value="5" {{ old('effort') == '5' ? 'selected' : '' }}>5 — Several years at &gt;100% ETP (&gt; 50 000 CHF)</option>
                </select>
                <x-proposition.scale-toggle toggle-label="See effort scale" hide-label="Hide scale">
                    <p><strong>1</strong> — A few days of work (≤ 1 week ETP). Very few financial resources (&lt; 200 CHF). Near-zero logistics.</p>
                    <p><strong>2</strong> — A few weeks (1–4 weeks ETP). Minor financial investment (&lt; 2 000 CHF). Coordination within one autonomous team.</p>
                    <p><strong>3</strong> — Several months part-time (1–3 months ETP). Moderate external cost (2 000–10 000 CHF). Requires inter-team sync.</p>
                    <p><strong>4</strong> — More than a semester (3–6 months ETP). Significant external cost (10 000–50 000 CHF). Steering committee needed.</p>
                    <p><strong>5</strong> — Several years at &gt;100% ETP continuously. Heavy investment (&gt; 50 000 CHF). Multi-phase validation required.</p>
                </x-proposition.scale-toggle>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-4 pt-2">
        <x-proposition.previous />
        <button type="submit"
            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
            Submit
        </button>
    </div>
</div>
