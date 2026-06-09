<div x-data="{
    step: 1,
    membres: @js(old('membres', [''])),
    buts: @js(old('buts', [''])),
    phases: [{
        titre: '', duree: '', description: '',
        objectifs: [''], livrables: [''], ressources: ['']
    }],
    addPhase() {
        this.phases.push({ titre: '', duree: '', description: '', objectifs: [''], livrables: [''], ressources: [''] });
    },
    removePhase(i) {
        if (this.phases.length > 1) this.phases.splice(i, 1);
    },
    removeItem(arr, i) {
        if (arr.length > 1) arr.splice(i, 1);
    }
}">
    <p class="text-sm text-gray-500 mb-4">Step <span x-text="step"></span> of 3</p>
    <x-proposition.step-1 />
    <x-proposition.step-2 />
    <x-proposition.step-3 />
</div>
