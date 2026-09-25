import { Controller } from '@hotwired/stimulus';

/*
 * Validation du formulaire de connexion : e-mail valide et mot de passe renseigné.
 * Les erreurs s'affichent après le premier passage dans le champ ; le serveur revérifie tout.
 */
const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[A-Za-z]{2,}$/;

const RULES = {
    email: v => !v.trim() ? "L'adresse e-mail est obligatoire."
        : !EMAIL_RE.test(v.trim()) ? "L'adresse e-mail n'est pas valide." : '',
    password: v => !v ? 'Le mot de passe est obligatoire.' : '',
};

export default class extends Controller {
    static targets = ['field', 'error', 'submit'];

    connect() {
        this.element.addEventListener('submit', e => {
            this.fieldTargets.forEach(f => f.dataset.touched = '1');
            if (!this.validate()) e.preventDefault();
        });
    }

    touch(event) {
        event.target.closest('.field').dataset.touched = '1';
        this.validate();
    }

    validate() {
        let ok = true;
        this.fieldTargets.forEach((field, i) => {
            const message = RULES[field.dataset.rule](field.querySelector('input').value);
            const show = field.dataset.touched === '1';
            if (message) ok = false;
            field.classList.toggle('is-invalid', show && !!message);
            field.classList.toggle('is-valid', show && !message);
            this.errorTargets[i].textContent = show ? message : '';
        });
        return ok;
    }

    // Remplit un compte de démonstration
    fill({ params: { email } }) {
        this.element.querySelector('#email').value = email;
        this.element.querySelector('#password').value = 'demo1234';
        this.fieldTargets.forEach(f => f.dataset.touched = '1');
        this.validate();
    }
}
