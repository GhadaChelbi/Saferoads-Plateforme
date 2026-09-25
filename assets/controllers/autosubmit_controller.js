import { Controller } from '@hotwired/stimulus';

/*
 * Soumet automatiquement un formulaire de filtres dès qu'une valeur change.
 */
export default class extends Controller {
    submit() {
        this.element.requestSubmit();
    }
}
