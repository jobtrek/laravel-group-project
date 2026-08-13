

import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.listHelpers = {
    add(arr, value = '') {
        arr.push(value);
    },

    remove(arr, idx, min = 1) {
        if (arr.length > min) {
            arr.splice(idx, 1);
        }
    },

    // Wraps plain string values into the { id, value } shape used by
    // list repeaters that need a stable x-for key (old() input has no ids).
    toIdList(values) {
        return (values && values.length ? values : ['']).map((value) => ({ id: crypto.randomUUID(), value }));
    },

    // Same idea for object rows (e.g. resources) — keeps their own fields,
    // just adds a client-side id for the x-for key.
    toIdRows(rows, empty = {}) {
        return (rows && rows.length ? rows : [empty]).map((row) => ({ id: crypto.randomUUID(), ...row }));
    },
};

Alpine.data('userMultiSelect', (initial = ['']) => ({
    selected: window.listHelpers.toIdList(initial),

    add() {
        window.listHelpers.add(this.selected, { id: crypto.randomUUID(), value: '' });
    },

    remove(idx) {
        window.listHelpers.remove(this.selected, idx);
    },
}));

// Shared shape + handlers for a "phases" repeater, where each phase has its
// own objectifs/livrables/resources sub-lists. Used by the project edit form
// and the proposition wizard, which differ only in the resources field name,
// whether phases/resources carry a persisted id, and the minimum resource
// row count.
window.phaseRepeaterFactory = (initialPhases, options = {}) => {
    const resourcesKey = options.resourcesKey ?? 'ressources';
    const withIds = options.withIds ?? false;
    const defaultResources = options.defaultResources ?? [];
    const minResources = options.minResources ?? 1;

    const emptyPhase = () => ({
        ...(withIds ? { id: null } : {}),
        titre: '', duree: '', description: '',
        objectifs: [''], livrables: [''],
        [resourcesKey]: defaultResources.map((resource) => ({ ...resource })),
    });

    const emptyResource = () => ({
        ...(withIds ? { id: null } : {}),
        resource_type: '', amount_needed: '',
    });

    return {
        phases: initialPhases,

        addPhase() { this.phases.push(emptyPhase()); },
        removePhase(i) { window.listHelpers.remove(this.phases, i); },

        addObjectif(p) { this.phases[p].objectifs.push(''); },
        removeObjectif(p, i) { window.listHelpers.remove(this.phases[p].objectifs, i); },

        addLivrable(p) { this.phases[p].livrables.push(''); },
        removeLivrable(p, i) { window.listHelpers.remove(this.phases[p].livrables, i); },

        addResource(p) { this.phases[p][resourcesKey].push(emptyResource()); },
        removeResource(p, i) { window.listHelpers.remove(this.phases[p][resourcesKey], i, minResources); },
    };
};

Alpine.start();
