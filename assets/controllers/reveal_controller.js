import { Controller } from '@hotwired/stimulus';

/*
 * Animations au défilement.
 * - Chaque élément .reveal reste "en attente" (animations CSS en pause)
 *   jusqu'à ce qu'il entre dans l'écran, puis reçoit .is-visible.
 * - Les nombres [data-count] qu'il contient comptent de 0 jusqu'à leur valeur.
 */
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export default class extends Controller {
    connect() {
        const items = this.element.querySelectorAll('.reveal');

        // Pas d'IntersectionObserver ou animations réduites : tout afficher directement
        if (!('IntersectionObserver' in window) || reduceMotion) {
            items.forEach(el => el.classList.add('is-visible'));
            return;
        }

        this.observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                entry.target.querySelectorAll('[data-count]').forEach(el => this.countUp(el));
                this.observer.unobserve(entry.target);
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

        items.forEach(el => this.observer.observe(el));
    }

    disconnect() {
        this.observer?.disconnect();
    }

    // Compteur animé : 0 -> valeur finale, avec ralentissement en fin de course
    countUp(el) {
        const target = parseFloat(el.dataset.count);
        const decimals = parseInt(el.dataset.decimals || '0', 10);
        const fmt = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
        const duration = 1400;
        const start = performance.now();

        const step = now => {
            const t = Math.min(1, (now - start) / duration);
            const eased = 1 - Math.pow(1 - t, 4);
            el.textContent = fmt.format(target * eased);
            if (t < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    }
}
