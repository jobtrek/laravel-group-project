@props(['users'])

<div x-data="{
    step: 1,
    porteur: @js(old('porteur', '')),
    membres: @js(old('membres', [''])),
    buts: @js(old('buts', [''])),
    phases: @js(old('phases', [
        [
            'titre' => '',
            'duree' => '',
            'description' => '',
            'objectifs' => [''],
            'livrables' => [''],
            'ressources_necessaires' => [['resource_type' => '', 'amount_needed' => '']]
        ]
    ])),
    addPhase() {
        this.phases.push({
            titre: '',
            duree: '',
            description: '',
            objectifs: [''],
            livrables: [''],
            ressources_necessaires: [{ resource_type: '', amount_needed: '' }]
        });
    },
    removePhase(i) {
        if (this.phases.length > 1) this.phases.splice(i, 1);
    },
    removeItem(arr, i) {
        if (arr.length > 1) arr.splice(i, 1);
    }
}">
    <p class="text-sm text-gray-500 mb-4">Step <span x-text="step"></span> of 3</p>
    <x-proposition.step-1 :users="$users" />
    <x-proposition.step-2 />
    <x-proposition.step-3 />
</div>