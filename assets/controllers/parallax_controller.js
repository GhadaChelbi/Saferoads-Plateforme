import { Controller } from '@hotwired/stimulus';

/*
 * Effet de profondeur dans la bannière : le téléphone et la lueur
 * suivent légèrement le pointeur (variables CSS --mx / --my entre -1 et 1).
 */
export default class extends Controller {
    move(event) {
        if (event.pointerType === 'touch') return;
        const rect = this.element.getBoundingClientRect();
        const mx = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        const my = ((event.clientY - rect.top) / rect.height) * 2 - 1;
        this.element.style.setProperty('--mx', mx.toFixed(3));
        this.element.style.setProperty('--my', my.toFixed(3));
    }

    reset() {
        this.element.style.setProperty('--mx', 0);
        this.element.style.setProperty('--my', 0);
    }
}
