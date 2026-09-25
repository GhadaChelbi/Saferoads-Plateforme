import { Controller } from '@hotwired/stimulus';

/*
 * Fait tourner plusieurs mots au même endroit (sortie vers le haut, entrée par le bas).
 * Usage : data-controller="rotator" data-rotator-words-value='["a","b"]'
 *         avec un enfant data-rotator-target="word".
 */
export default class extends Controller {
    static targets = ['word'];
    static values = { words: Array, interval: { type: Number, default: 2600 } };

    connect() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || this.wordsValue.length < 2) return;
        this.index = 0;
        this.timer = setInterval(() => this.next(), this.intervalValue);
    }

    disconnect() {
        clearInterval(this.timer);
    }

    next() {
        const word = this.wordTarget;
        word.classList.add('is-out');

        // Change le mot une fois la sortie terminée, puis joue l'entrée
        setTimeout(() => {
            this.index = (this.index + 1) % this.wordsValue.length;
            word.textContent = this.wordsValue[this.index];
            word.classList.remove('is-out');
            word.classList.add('is-in');
            word.addEventListener('animationend', () => word.classList.remove('is-in'), { once: true });
        }, 350);
    }
}
