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
        if (!this.titre || !this.titre.trim()) this.errors.titre = 'Title is required.';
        if (!this.porteur) this.errors.porteur = 'Porteur is required.';
        if (!this.membres.length || this.membres.every(m => !m)) {
            this.errors.membres = 'At least one member is required.';
        } else if (this.membres.some(m => !m)) {
            this.errors.membres = 'Please fill or remove all empty member rows.';
        }
        if (!this.description || !this.description.trim()) this.errors.description = 'Description is required.';
        if (!this.buts.length || this.buts.every(b => !b || !b.trim())) {
            this.errors.buts = 'At least one goal is required.';
        } else if (this.buts.some(b => !b || !b.trim())) {
            this.errors.buts = 'Please fill or remove all empty goal rows.';
        }
        if (!this.perimetre || !this.perimetre.trim()) this.errors.perimetre = 'Périmètre is required.';
        return Object.keys(this.errors).length === 0;
    },
    validateStep2() {
        this.phaseErrors = [];
        let valid = true;
        this.phases.forEach((phase, i) => {
            const e = {};
            if (!phase.titre || !phase.titre.trim()) e.titre = 'Phase title is required.';
            if (!phase.duree || !phase.duree.trim()) e.duree = 'Duration is required.';
            if (!phase.description || !phase.description.trim()) e.description = 'Description is required.';
            if (!phase.objectifs.length || phase.objectifs.every(o => !o || !o.trim())) {
                e.objectifs = 'At least one objective is required.';
            } else if (phase.objectifs.some(o => !o || !o.trim())) {
                e.objectifs = 'Please fill or remove all empty objective rows.';
            }
            if (!phase.livrables.length || phase.livrables.every(l => !l || !l.trim())) {
                e.livrables = 'At least one deliverable is required.';
            } else if (phase.livrables.some(l => !l || !l.trim())) {
                e.livrables = 'Please fill or remove all empty deliverable rows.';
            }
            if (!phase.ressources_necessaires.length || phase.ressources_necessaires.every(r => !r.resource_type || !r.resource_type.trim())) {
                e.ressources = 'At least one resource with a type is required.';
            } else if (phase.ressources_necessaires.some(r => !r.resource_type || !r.resource_type.trim() || r.amount_needed === undefined || r.amount_needed === null || String(r.amount_needed).trim() === '')) {
                e.ressources = 'Please fill or remove all empty resource rows, ensuring both type and amount are provided.';
            }
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
