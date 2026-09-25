import { Controller } from '@hotwired/stimulus';

/*
 * Chronomètre de la maquette : décompte de N secondes puis recommence.
 * Usage : data-controller="countdown" data-countdown-from-value="20"
 */
export default class extends Controller {
    static values = { from: { type: Number, default: 20 } };

    connect() {
        this.remaining = this.fromValue;
        this.render();
        this.timer = setInterval(() => {
            this.remaining = this.remaining > 0 ? this.remaining - 1 : this.fromValue;
            this.render();
        }, 1000);
    }

    disconnect() {
        clearInterval(this.timer);
    }

    render() {
        this.element.textContent = `0:${String(this.remaining).padStart(2, '0')}`;
        // Passe en orange sous 5 secondes
        this.element.classList.toggle('is-low', this.remaining <= 5);
    }
}
