<x-app-layout>
    <div class="max-w-2xl mx-auto py-10 px-4" x-data="resourceForm()">

        <h2 class="text-xl font-semibold mb-6">Ajouter une ressource — {{ $project->title }}</h2>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 text-red-700 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('projects.resources.store', $project) }}" class="flex flex-col gap-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700">Phase</label>
                <select name="phase_id" x-model="selectedPhaseId" class="mt-1 block w-full rounded-md border-gray-300">
                    @foreach ($project->phases as $phase)
                        <option value="{{ $phase->id }}">{{ $phase->name }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    Restant à trouver : <span x-text="remaining.toFixed(2)"></span>
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Type de ressource</label>
                <input type="text" name="resource_type" value="{{ old('resource_type') }}"
                       class="mt-1 block w-full rounded-md border-gray-300">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" class="mt-1 block w-full rounded-md border-gray-300">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Montant</label>
                <input type="number" step="0.01" min="0.01" name="amount" x-model="amount"
                       class="mt-1 block w-full rounded-md border-gray-300">
                <p class="text-xs text-gray-500 mt-1">
                    Cette contribution représente <span x-text="contributionPercent"></span>% du besoin de la phase.
                    Total projeté après ajout : <span x-text="projectedTotalPercent"></span>%
                </p>
            </div>

            <x-projects.buttons text="Enregistrer" type="submit" class="bg-blue-700 text-white p-2"/>
        </form>
    </div>

    <script>
        function resourceForm() {
            return {
                phases: @json($phasesData),
                selectedPhaseId: @json(old('phase_id', $project->phases->first()?->id)),
                amount: @json((float) old('amount', 0)),

                get selectedPhase() {
                    return this.phases.find(p => p.id === Number(this.selectedPhaseId)) ?? null;
                },
                get remaining() {
                    return this.selectedPhase
                        ? (this.selectedPhase.needed - this.selectedPhase.found)
                        : 0;
                },
                get contributionPercent() {
                    if (!this.selectedPhase || this.selectedPhase.needed <= 0) return 0;
                    return Math.min(
                        100,
                        Math.round((Number(this.amount || 0) / this.selectedPhase.needed) * 10000) / 100
                    );
                },
                get projectedTotalPercent() {
                    if (!this.selectedPhase || this.selectedPhase.needed <= 0) return 0;
                    const total = this.selectedPhase.found + Number(this.amount || 0);
                    return Math.min(
                        100,
                        Math.round((total / this.selectedPhase.needed) * 10000) / 100
                    );
                }
            };
        }
    </script>
</x-app-layout>