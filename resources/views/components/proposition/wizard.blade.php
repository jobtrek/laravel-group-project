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
    validateStep1() {
        this.errors = {};
        if (!this.titre.trim()) this.errors.titre = 'Title is required.';
        if (!this.porteur) this.errors.porteur = 'Please select a porteur.';
        if (this.membres.every(m => !m)) this.errors.membres = 'At least one member is required.';
        else if (this.membres.some(m => !m)) this.errors.membres = 'Please fill or remove all empty member rows.';
        if (!this.description.trim()) this.errors.description = 'Description is required.';
        if (this.buts.every(b => !String(b).trim())) this.errors.buts = 'At least one goal is required.';
        if (!this.perimetre.trim()) this.errors.perimetre = 'Périmètre is required.';
        return Object.keys(this.errors).length === 0;
    },
    validateStep2() {
        const pe = this.phases.map(() => ({}));
        let valid = true;
        this.phases.forEach((phase, i) => {
            if (!phase.titre.trim()) { pe[i].titre = 'Required.'; valid = false; }
            if (!phase.duree.trim()) { pe[i].duree = 'Required.'; valid = false; }
            if (!phase.description.trim()) { pe[i].description = 'Required.'; valid = false; }
            if (phase.objectifs.every(o => !String(o).trim())) { pe[i].objectifs = 'At least one objective is required.'; valid = false; }
            if (phase.livrables.every(l => !String(l).trim())) { pe[i].livrables = 'At least one deliverable is required.'; valid = false; }
            if (phase.ressources_necessaires.some(r => !String(r.resource_type || '').trim() || String(r.amount_needed || '') === '')) {
                pe[i].ressources = 'All resource fields are required.'; valid = false;
            }
        });
        this.phaseErrors = pe;
        return valid;
    },
    validateStep3() {
        this.errors = {};
        if (String(this.portee).trim() === '') this.errors.portee = 'Portée is required.';
        else if (Number(this.portee) < 0 || Number(this.portee) > 50) this.errors.portee = 'Must be between 0 and 50.';
        if (!this.impact) this.errors.impact = 'Please select an impact level.';
        if (String(this.confiance).trim() === '') this.errors.confiance = 'Confiance is required.';
        else if (Number(this.confiance) < 0 || Number(this.confiance) > 100) this.errors.confiance = 'Must be between 0 and 100.';
        if (!this.effort) this.errors.effort = 'Please select an effort level.';
        return Object.keys(this.errors).length === 0;
    },
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
    }
}">
    <p class="text-sm text-gray-500 mb-4">Step <span x-text="step"></span> of 3</p>
    <x-proposition.step-1 :users="$users" />
    <x-proposition.step-2 />
    <x-proposition.step-3 />
</div>
