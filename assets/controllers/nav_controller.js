import { Controller } from '@hotwired/stimulus';

/*
 * Barre de navigation : devient plus compacte et opaque dès qu'on fait défiler la page.
 */
export default class extends Controller {
    connect() {
        this.onScroll = () => this.element.classList.toggle('is-scrolled', window.scrollY > 24);
        window.addEventListener('scroll', this.onScroll, { passive: true });
        this.onScroll();
    }

    disconnect() {
        window.removeEventListener('scroll', this.onScroll);
    }
}
