import { Controller } from '@hotwired/stimulus';

/*
 * Formulaire de communication : affiche la liste du segment correspondant au type
 * de ciblage choisi (région, département, niveau…) et la date si la publication est planifiée.
 */
export default class extends Controller {
    static targets = ['type', 'segment', 'date'];

    connect() {
        this.change();
    }

    change() {
        const type = this.typeTarget.value;
        this.segmentTargets.forEach(select => {
            const active = select.dataset.type === type;
            select.hidden = !active;
            select.disabled = !active; // seul le segment actif est envoyé
        });

        const later = this.element.querySelector('input[name="com[when]"]:checked')?.value === 'later';
        this.dateTarget.hidden = !later;
    }
}
