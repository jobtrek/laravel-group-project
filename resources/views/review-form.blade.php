<x-app-layout>
    <div class="min-h-screen">
        <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-slot name="header">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-base text-gray-800 leading-tight">
                        Demande d'informations —
                        <span class="text-indigo-600">{{ $project->title }}</span>
                    </h2>
                    <a href="{{ route('evaluation') }}"
                       class="text-sm text-gray-500 hover:text-gray-700 transition">
                        ← Retour à l'évaluation
                    </a>
                </div>
            </x-slot>

            <div class="py-10">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                    @php
                        $fieldKeys = ['title', 'description', 'but', 'perimetre',
                                      'evaluation.portee', 'evaluation.impact',
                                      'evaluation.confiance', 'evaluation.effort'];

                        foreach ($project->phases as $i => $phase) {
                            $fieldKeys[] = "phases.{$i}.titre";
                            $fieldKeys[] = "phases.{$i}.duree";
                            $fieldKeys[] = "phases.{$i}.description";
                            $fieldKeys[] = "phases.{$i}.objectifs";
                            $fieldKeys[] = "phases.{$i}.livrables";
                            $fieldKeys[] = "phases.{$i}.ressources";
                        }

                        // Result: ['title' => ['checked' => false, 'value' => ''], ...]
                        $alpineFields = collect($fieldKeys)
                            ->mapWithKeys(fn(string $k) => [$k => ['checked' => false, 'value' => '']])
                            ->all();
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
                            action="{{ route('projects.request-more-info', $project) }}"
                            x-data="{
                            fields: @js($alpineFields),
                            get hasComments() {
                                return Object.values(this.fields)
                                    .some(f => f.checked && f.value.trim() !== '');
                            }
                        }"
                    >
                        @csrf

                        <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">
                            Informations générales
                        </p>

                        <x-direction.annotatable-field field-key="title" label="Titre du projet">
                            <p class="text-gray-900 font-medium">{{ $project->title }}</p>
                        </x-direction.annotatable-field>

                        <x-direction.annotatable-field field-key="description" label="Description">
                            <p class="text-gray-700 text-sm whitespace-pre-line leading-relaxed">
                                {{ $project->description }}
                            </p>
                        </x-direction.annotatable-field>

                        <x-direction.annotatable-field field-key="but" label="Objectifs SMART">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($project->but as $but)
                                    <li class="text-gray-700 text-sm">{{ $but }}</li>
                                @endforeach
                            </ul>
                        </x-direction.annotatable-field>

                        @if ($project->perimetre)
                            <x-direction.annotatable-field field-key="perimetre" label="Périmètre">
                                <p class="text-gray-700 text-sm whitespace-pre-line">
                                    {{ $project->perimetre }}
                                </p>
                            </x-direction.annotatable-field>
                        @endif


                        @if ($project->evaluation)
                            <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mt-8 mb-3">
                                Évaluation ICE
                            </p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <x-direction.annotatable-field
                                        field-key="evaluation.portee"
                                        label="Portée">
                                    <p class="text-2xl font-bold text-indigo-600">
                                        {{ $project->evaluation->portee }}
                                    </p>
                                </x-direction.annotatable-field>

                                <x-direction.annotatable-field
                                        field-key="evaluation.impact"
                                        label="Impact">
                                    <p class="text-2xl font-bold text-indigo-600">
                                        {{ $project->evaluation->impact }} <span
                                                class="text-base font-normal text-gray-400">/ 5</span>
                                    </p>
                                </x-direction.annotatable-field>

                                <x-direction.annotatable-field
                                        field-key="evaluation.confiance"
                                        label="Confiance">
                                    <p class="text-2xl font-bold text-indigo-600">
                                        {{ $project->evaluation->confiance }}
                                        <span class="text-base font-normal text-gray-400">%</span>
                                    </p>
                                </x-direction.annotatable-field>

                                <x-direction.annotatable-field
                                        field-key="evaluation.effort"
                                        label="Effort">
                                    <p class="text-2xl font-bold text-indigo-600">
                                        {{ $project->evaluation->effort }}
                                        <span class="text-base font-normal text-gray-400">/ 5</span>
                                    </p>
                                </x-direction.annotatable-field>
                            </div>
                        @endif

                        @foreach ($project->phases as $i => $phase)
                            <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mt-8 mb-3">
                                Phase {{ $i + 1 }} — {{ $phase->name }}
                            </p>

                            <x-direction.annotatable-field
                                    :field-key="'phases.' . $i . '.titre'"
                                    label="Titre">
                                <p class="text-gray-900 font-medium text-sm">{{ $phase->name }}</p>
                            </x-direction.annotatable-field>

                            <x-direction.annotatable-field
                                    :field-key="'phases.' . $i . '.duree'"
                                    label="Durée">
                                <p class="text-gray-700 text-sm">{{ $phase->duration }}</p>
                            </x-direction.annotatable-field>

                            <x-direction.annotatable-field
                                    :field-key="'phases.' . $i . '.description'"
                                    label="Description">
                                <p class="text-gray-700 text-sm whitespace-pre-line">
                                    {{ $phase->description }}
                                </p>
                            </x-direction.annotatable-field>

                            <x-direction.annotatable-field
                                    :field-key="'phases.' . $i . '.objectifs'"
                                    label="Objectifs">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($phase->objectifs as $obj)
                                        <li class="text-gray-700 text-sm">{{ $obj }}</li>
                                    @endforeach
                                </ul>
                            </x-direction.annotatable-field>

                            <x-direction.annotatable-field
                                    :field-key="'phases.' . $i . '.livrables'"
                                    label="Livrables">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($phase->livrables as $liv)
                                        <li class="text-gray-700 text-sm">{{ $liv }}</li>
                                    @endforeach
                                </ul>
                            </x-direction.annotatable-field>

                            <x-direction.annotatable-field
                                    :field-key="'phases.' . $i . '.ressources'"
                                    label="Ressources nécessaires">
                                <table class="w-full text-sm border-collapse">
                                    <thead>
                                    <tr class="text-left">
                                        <th class="pb-1 text-xs font-semibold text-gray-500 pr-4">Type</th>
                                        <th class="pb-1 text-xs font-semibold text-gray-500">Montant nécessaire</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($phase->resources as $resource)
                                        <tr class="border-t border-gray-100">
                                            <td class="py-1 pr-4 text-gray-700">
                                                {{ $resource->resource_type }}
                                            </td>
                                            <td class="py-1 text-gray-700">
                                                {{ number_format((float) $resource->amount_needed, 2) }} CHF
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </x-direction.annotatable-field>
                        @endforeach

                        <div
                                class="sticky bottom-6 flex justify-end mt-8"
                                x-show="hasComments"
                                x-cloak
                        >
                            <button
                                    type="submit"
                                    class="px-8 py-3 bg-indigo-600 text-white font-semibold rounded-lg
                                       shadow-lg hover:bg-indigo-700 active:bg-indigo-800
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500
                                       focus:ring-offset-2 transition-colors"
                            >
                                Demander des informations complémentaires
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
