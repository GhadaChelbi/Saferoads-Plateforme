import { Controller } from '@hotwired/stimulus';

/*
 * Réordonne les éléments d'une liste avec des boutons ↑ / ↓ (animation de permutation).
 * La numérotation (.module-num) est recalculée après chaque déplacement.
 */
export default class extends Controller {
    static targets = ['list'];

    up(event) {
        const item = event.currentTarget.closest('li');
        if (item.previousElementSibling) this.move(item, item.previousElementSibling, 'before');
    }

    down(event) {
        const item = event.currentTarget.closest('li');
        if (item.nextElementSibling) this.move(item, item.nextElementSibling, 'after');
    }

    move(item, sibling, where) {
        // Animation FLIP : on mémorise les positions, on déplace, puis on anime l'écart
        const before = new Map([item, sibling].map(el => [el, el.getBoundingClientRect().top]));
        sibling[where](item);
        [item, sibling].forEach(el => {
            const delta = before.get(el) - el.getBoundingClientRect().top;
            el.animate([{ transform: `translateY(${delta}px)` }, { transform: 'none' }], { duration: 250, easing: 'ease-out' });
        });
        this.listTarget.querySelectorAll('.module-num').forEach((n, i) => { n.textContent = i + 1; });
    }
}
