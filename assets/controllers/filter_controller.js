import { Controller } from '@hotwired/stimulus';

/*
 * Filtre la liste des matchs par mode (duel, examen, défi).
 * Usage : data-controller="filter", boutons data-action="filter#show"
 * avec data-filter-mode-param="duel|examen|defi|all".
 */
export default class extends Controller {
    static targets = ['item', 'empty'];

    show({ params: { mode }, currentTarget }) {
        // Bouton actif
        this.element.querySelectorAll('.chip').forEach(chip => {
            chip.classList.toggle('is-active', chip === currentTarget);
            chip.setAttribute('aria-pressed', chip === currentTarget);
        });

        // Affiche les matchs du mode choisi, avec une animation d'entrée décalée
        let visible = 0;
        this.itemTargets.forEach(item => {
            const match = mode === 'all' || item.dataset.mode === mode;
            item.hidden = !match;
            if (match) {
                item.style.setProperty('--i', visible++);
                item.classList.remove('is-entering');
                void item.offsetWidth; // relance l'animation
                item.classList.add('is-entering');
            }
        });

        this.emptyTarget.hidden = visible > 0;
    }
}
