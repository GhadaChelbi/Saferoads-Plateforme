import { Controller } from '@hotwired/stimulus';

/*
 * Recherche instantanée dans un tableau : masque les lignes qui ne contiennent pas le texte saisi.
 */
export default class extends Controller {
    static targets = ['row', 'empty'];

    filter(event) {
        const query = event.target.value.trim().toLowerCase();
        let visible = 0;

        this.rowTargets.forEach(row => {
            const match = !query || row.textContent.toLowerCase().includes(query);
            row.hidden = !match;
            if (match) visible++;
        });

        this.emptyTarget.hidden = visible > 0;
    }
}
