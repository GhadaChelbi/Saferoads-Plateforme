import { Controller } from '@hotwired/stimulus';

/*
 * Quiz de validation d'un module MOOC : corrige les réponses et affiche le score.
 * Chaque <fieldset data-quiz-target="question" data-correct="index"> contient des radios.
 */
const PASS_RATE = 0.66;

export default class extends Controller {
    static targets = ['question', 'submit', 'result'];

    // Le bouton s'active quand toutes les questions ont une réponse
    answer() {
        const answered = this.questionTargets.every(q => q.querySelector('input:checked'));
        this.submitTarget.disabled = !answered;
    }

    check() {
        let score = 0;

        this.questionTargets.forEach(q => {
            const checked = q.querySelector('input:checked');
            const correct = checked && checked.value === q.dataset.correct;
            if (correct) score++;

            q.classList.remove('is-right', 'is-wrong');
            q.classList.add(correct ? 'is-right' : 'is-wrong');
            q.querySelectorAll('.quiz-a').forEach((label, i) => {
                label.classList.toggle('is-answer', String(i) === q.dataset.correct);
            });
        });

        const total = this.questionTargets.length;
        const passed = score / total >= PASS_RATE;
        this.resultTarget.textContent = passed
            ? `✓ ${score}/${total} — module validé !`
            : `✕ ${score}/${total} — relisez la leçon et réessayez.`;
        this.resultTarget.className = `quiz-result ${passed ? 'is-pass' : 'is-fail'}`;
    }
}
