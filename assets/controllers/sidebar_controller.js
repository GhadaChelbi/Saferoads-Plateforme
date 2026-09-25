import { Controller } from '@hotwired/stimulus';

/*
 * Menu latéral des espaces connectés : ouverture / fermeture sur mobile.
 */
export default class extends Controller {
    toggle() {
        this.element.classList.toggle('is-open');
    }
}
