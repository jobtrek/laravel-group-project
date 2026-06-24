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
        if (!this.titre || !this.titre.trim()) this.errors.titre = 'Le titre est requis.';
        if (!this.porteur) this.errors.porteur = 'Le porteur est requis.';
        if (!this.membres.length || this.membres.every(m => !m)) {
            this.errors.membres = 'Au moins un membre est requis.';
        } else if (this.membres.some(m => !m)) {
            this.errors.membres = 'Veuillez remplir ou supprimer toutes les lignes de membres vides.';
        }
        if (!this.description || !this.description.trim()) this.errors.description = 'La description est requise.';
        if (!this.buts.length || this.buts.every(b => !b || !b.trim())) {
            this.errors.buts = 'Au moins un objectif est requis.';
        } else if (this.buts.some(b => !b || !b.trim())) {
            this.errors.buts = 'Veuillez remplir ou supprimer toutes les lignes d\'objectifs vides.';
        }
        if (!this.perimetre || !this.perimetre.trim()) this.errors.perimetre = 'Le périmètre est requis.';
        return Object.keys(this.errors).length === 0;
    },
    validateStep2() {
        this.phaseErrors = [];
        let valid = true;
        this.phases.forEach((phase, i) => {
            const e = {};
            if (!phase.titre || !phase.titre.trim()) e.titre = 'Le titre de la phase est requis.';
            if (!phase.duree || !phase.duree.trim()) e.duree = 'La durée est requise.';
            if (!phase.description || !phase.description.trim()) e.description = 'La description est requise.';
            if (!phase.objectifs.length || phase.objectifs.every(o => !o || !o.trim())) {
                e.objectifs = 'Au moins un objectif est requis.';
            } else if (phase.objectifs.some(o => !o || !o.trim())) {
                e.objectifs = 'Veuillez remplir ou supprimer toutes les lignes d\'objectifs vides.';
            }
            if (!phase.livrables.length || phase.livrables.every(l => !l || !l.trim())) {
                e.livrables = 'Au moins un livrable est requis.';
            } else if (phase.livrables.some(l => !l || !l.trim())) {
                e.livrables = 'Veuillez remplir ou supprimer toutes les lignes de livrables vides.';
            }
            if (!phase.ressources_necessaires.length || phase.ressources_necessaires.every(r => !r.resource_type || !r.resource_type.trim())) {
                e.ressources = 'Au moins une ressource avec un type est requise.';
            } else if (phase.ressources_necessaires.some(r => !r.resource_type || !r.resource_type.trim() || r.amount_needed === undefined || r.amount_needed === null || String(r.amount_needed).trim() === '')) {
                e.ressources = 'Veuillez remplir ou supprimer toutes les lignes de ressources vides, en veillant à ce que le type et la quantité soient fournis.';
            }
            this.phaseErrors[i] = e;
            if (Object.keys(e).length) valid = false;
        });
        return valid;
    },
    validateStep3() {
        this.errors = {};
        if (this.portee === '' || this.portee === null) this.errors.portee = 'La portée est requise.';
        else if (this.portee < 0 || this.portee > 50) this.errors.portee = 'La portée doit être comprise entre 0 et 50.';
        if (!this.impact) this.errors.impact = 'L\'impact est requis.';
        if (this.confiance === '' || this.confiance === null) this.errors.confiance = 'La confiance est requise.';
        else if (this.confiance < 0 || this.confiance > 100) this.errors.confiance = 'La confiance doit être comprise entre 0 et 100.';
        if (!this.effort) this.errors.effort = 'L\'effort est requis.';
        return Object.keys(this.errors).length === 0;
    }
}">
    <p class="text-sm text-gray-500 mb-4">Étape <span x-text="step"></span> sur 3</p>
    <x-proposition.step-1 :users="$users" />
    <x-proposition.step-2 />
    <x-proposition.step-3 />
</div>
