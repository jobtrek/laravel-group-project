<x-app-layout>
    <div class="max-w-2xl mx-auto py-10 px-4" x-data="{ 
        initialChiefId: @js((string) $project->leader_id),
        chiefId: @js(old('leader_id', (string) $project->leader_id))
    }">

        <h2 class="text-xl font-semibold mb-2">{{ $project->title }}</h2>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-50 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-50 text-red-700 rounded">{{ session('error') }}</div>
        @endif

        @if ($project->status instanceof \App\Models\States\RecolteState)
            <div class="mb-10 pb-8 border-b border-gray-200">
                <h3 class="text-lg font-semibold mb-4">Équipe du projet</h3>

                @if ($errors->assignTeam->any())
                    <div class="mb-4 p-3 bg-red-50 text-red-700 rounded">
                        <ul>
                            @foreach ($errors->assignTeam->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('projects.recolte.team', $project) }}" class="flex flex-col gap-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Chef de projet</label>
                        <select name="leader_id" x-model="chiefId" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="" selected disabled hidden>Sélectionner un chef de projet…</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="chiefId">
                        <label class="block text-sm font-medium text-gray-700">Membres</label>
                        <x-user-select-list :users="$users" name="membres" :selected="old('membres', $project->members->pluck('id')->map(fn($id) => (string) $id)->all() ?: [''])"
                            add-label="+ Ajouter un membre" />
                    </div>

                    <x-projects.buttons text="Enregistrer" type="submit" class="bg-blue-700 text-white p-2" />
                </form>
            </div>
        @endif

        <h3 class="text-lg font-semibold mb-4">Ajouter une ressource</h3>

        @if ($errors->default->any())
            <div class="mb-4 p-3 bg-red-50 text-red-700 rounded">
                <ul>
                    @foreach ($errors->default->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('projects.resources.store', $project) }}" class="flex flex-col gap-4"
            x-data="resourceForm()">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700">Phase</label>
                <select name="phase_id" x-model="selectedPhaseId" @change="selectedResourceType = ''"
                    class="mt-1 block w-full rounded-md border-gray-300">
                    @foreach ($project->phases as $phase)
                        <option value="{{ $phase->id }}">{{ $phase->name }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    Restant à trouver pour la phase : <span x-text="phaseRemaining.toFixed(2)"></span>
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Type de ressource</label>
                <select name="resource_type" x-model="selectedResourceType"
                    class="mt-1 block w-full rounded-md border-gray-300">
                    <option value="" selected disabled hidden>Sélectionner un type de ressource…</option>
                    <template x-for="resource in selectedPhase?.resources ?? []" :key="resource.resource_type">
                        <option :value="resource.resource_type" x-text="resource.resource_type"></option>
                    </template>
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    Restant à trouver pour ce type : <span x-text="remaining.toFixed(2)"></span>
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description"
                    class="mt-1 block w-full rounded-md border-gray-300">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Montant</label>
                <input type="number" step="0.01" min="0.01" name="amount" x-model="amount"
                    class="mt-1 block w-full rounded-md border-gray-300">
                <p class="text-xs text-gray-500 mt-1">
                    Cette contribution représente <span x-text="contributionPercent"></span>% du besoin de ce type de
                    ressource.
                    Total projeté après ajout : <span x-text="projectedTotalPercent"></span>%
                </p>
            </div>

            <x-projects.buttons text="Enregistrer" type="submit" class="bg-blue-700 text-white p-2" />
        </form>

        @if ($project->status instanceof \App\Models\States\RecolteState
            && auth()->user()?->can('launch project')
            && auth()->id() === $project->leader_id)
            <form method="POST" action="{{ route('projects.recolte.activate', $project) }}" class="mt-8">
                @csrf
                @method('PATCH')
                <button type="submit" :disabled="!chiefId"
                    :class="chiefId ? 'bg-green-700 hover:bg-green-800' : 'bg-gray-300 cursor-not-allowed'"
                    class="text-white p-2 rounded-md">
                    Démarrer le projet (passer en cours)
                </button>
            </form>
        @endif
    </div>

    <script>
        function resourceForm() {
            return {
                phases: @json($phasesData),
                selectedPhaseId: @json(old('phase_id', $project->phases->first()?->id)),
                selectedResourceType: @js(old('resource_type', '')),
                amount: @json((float) old('amount', 0)),

                get selectedPhase() {
                    return this.phases.find(p => p.id === Number(this.selectedPhaseId)) ?? null;
                },
                get selectedResource() {
                    return this.selectedPhase?.resources.find(r => r.resource_type === this.selectedResourceType) ?? null;
                },
                get phaseRemaining() {
                    return this.selectedPhase
                        ? (this.selectedPhase.needed - this.selectedPhase.found)
                        : 0;
                },
                get remaining() {
                    return this.selectedResource
                        ? (this.selectedResource.needed - this.selectedResource.found)
                        : 0;
                },
                get contributionPercent() {
                    if (!this.selectedResource || this.selectedResource.needed <= 0) return 0;
                    return Math.min(
                        100,
                        Math.round((Number(this.amount || 0) / this.selectedResource.needed) * 10000) / 100
                    );
                },
                get projectedTotalPercent() {
                    if (!this.selectedResource || this.selectedResource.needed <= 0) return 0;
                    const total = this.selectedResource.found + Number(this.amount || 0);
                    return Math.min(
                        100,
                        Math.round((total / this.selectedResource.needed) * 10000) / 100
                    );
                }
            };
        }
    </script>
</x-app-layout>