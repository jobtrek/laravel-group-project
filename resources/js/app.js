

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
};
window.listHelpers.uuid = () => (typeof crypto !== 'undefined' && crypto.randomUUID) ? crypto.randomUUID() : Math.random().toString(36).substring(2, 9);

Alpine.data('userMultiSelect', (initial = ['']) => ({
    selected: (initial.length ? initial : ['']).map((value) => ({ id: window.listHelpers.uuid(), value })),

    add() {
        window.listHelpers.add(this.selected, { id: window.listHelpers.uuid(), value: '' });
    },

    remove(idx) {
        window.listHelpers.remove(this.selected, idx);
    },
}));

Alpine.start();
