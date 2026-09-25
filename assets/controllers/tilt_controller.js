import { Controller } from '@hotwired/stimulus';

/*
 * Carte inclinable en 3D + halo lumineux qui suit le pointeur.
 * Expose --rx / --ry (rotation) et --px / --py (position du halo) au CSS.
 * Usage : data-controller="tilt" data-action="pointermove->tilt#move pointerleave->tilt#reset"
 */
const MAX_DEG = 8;

export default class extends Controller {
    connect() {
        this.disabled = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    move(event) {
        if (this.disabled || event.pointerType === 'touch') return;
        const r = this.element.getBoundingClientRect();
        const x = (event.clientX - r.left) / r.width;   // 0 -> 1
        const y = (event.clientY - r.top) / r.height;   // 0 -> 1

        this.element.style.setProperty('--ry', `${(x - 0.5) * 2 * MAX_DEG}deg`);
        this.element.style.setProperty('--rx', `${(0.5 - y) * 2 * MAX_DEG}deg`);
        this.element.style.setProperty('--px', `${x * 100}%`);
        this.element.style.setProperty('--py', `${y * 100}%`);
        this.element.classList.add('is-tilting');
    }

    reset() {
        this.element.style.setProperty('--rx', '0deg');
        this.element.style.setProperty('--ry', '0deg');
        this.element.classList.remove('is-tilting');
    }
}
