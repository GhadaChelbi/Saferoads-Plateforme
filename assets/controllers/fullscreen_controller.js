import { Controller } from '@hotwired/stimulus';

/*
 * Affiche un élément (le QR Code de session) en plein écran pour la projection en classe.
 */
export default class extends Controller {
    toggle() {
        if (document.fullscreenElement) {
            document.exitFullscreen();
        } else {
            this.element.requestFullscreen?.();
        }
    }
}
