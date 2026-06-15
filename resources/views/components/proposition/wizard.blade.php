@props(['users'])

<div x-data="{
    step: 1,
    titre: @js(old('titre', '')),
    description: @js(old('description', '')),
    perimetre: @js(old('perimetre', '')),
    portee: @js(old('portee', '')),
    impact: @js(old('impact', '')),
    confiance: @js(old('confiance', '')),
    effort: @js(old('effort', '')),
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
    errors: {},
    phaseErrors: [],

    nextStep() {
        const validators = [null, () => this.validateStep1(), () => this.validateStep2(), () => this.validateStep3()];
        if (validators[this.step] && validators[this.step]()) this.step++;
    },
    addPhase() {
        this.phases.push({
            titre: '',
            duree: '',
            description: '',
            objectifs: [''],
            livrables: [''],
            ressources_necessaires: [{ resource_type: '', amount_needed: '' }]
        });
        this.phaseErrors.push({});
    },
    removePhase(i) {
        if (this.phases.length > 1) {
            this.phases.splice(i, 1);
            this.phaseErrors.splice(i, 1);
        }
    },
    removeItem(arr, i) {
        if (arr.length > 1) arr.splice(i, 1);
    },
    validateStep1() {
        this.errors = {};
        if (!this.titre) this.errors.titre = 'Title is required.';
        if (!this.porteur) this.errors.porteur = 'Porteur is required.';
        if (!this.membres.length || this.membres.every(m => !m)) this.errors.membres = 'At least one member is required.';
        if (!this.description) this.errors.description = 'Description is required.';
        if (!this.buts.length || this.buts.every(b => !b)) this.errors.buts = 'At least one goal is required.';
        if (!this.perimetre) this.errors.perimetre = 'Périmètre is required.';
        return Object.keys(this.errors).length === 0;
    },
    validateStep2() {
        this.phaseErrors = [];
        let valid = true;
        this.phases.forEach((phase, i) => {
            const e = {};
            if (!phase.titre) e.titre = 'Phase title is required.';
            if (!phase.duree) e.duree = 'Duration is required.';
            if (!phase.description) e.description = 'Description is required.';
            if (!phase.objectifs.length || phase.objectifs.every(o => !o)) e.objectifs = 'At least one objective is required.';
            if (!phase.livrables.length || phase.livrables.every(l => !l)) e.livrables = 'At least one deliverable is required.';
            if (!phase.ressources_necessaires.length || phase.ressources_necessaires.every(r => !r.resource_type)) e.ressources = 'At least one resource with a type is required.';
            this.phaseErrors[i] = e;
            if (Object.keys(e).length) valid = false;
        });
        return valid;
    },
    validateStep3() {
        this.errors = {};
        if (this.portee === '' || this.portee === null) this.errors.portee = 'Portée is required.';
        else if (this.portee < 0 || this.portee > 50) this.errors.portee = 'Portée must be between 0 and 50.';
        if (!this.impact) this.errors.impact = 'Impact is required.';
        if (this.confiance === '' || this.confiance === null) this.errors.confiance = 'Confiance is required.';
        else if (this.confiance < 0 || this.confiance > 100) this.errors.confiance = 'Confiance must be between 0 and 100.';
        if (!this.effort) this.errors.effort = 'Effort is required.';
        return Object.keys(this.errors).length === 0;
    }
}">
    <p class="text-sm text-gray-500 mb-4">Step <span x-text="step"></span> of 3</p>
    <x-proposition.step-1 :users="$users" />
    <x-proposition.step-2 />
    <x-proposition.step-3 />
</div>
