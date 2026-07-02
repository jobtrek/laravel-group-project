

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

Alpine.data('userMultiSelect', (initial = ['']) => ({
    selected: initial.length ? initial : [''],

    add() {
        window.listHelpers.add(this.selected);
    },

    remove(idx) {
        window.listHelpers.remove(this.selected, idx);
    },
}));

Alpine.start();
