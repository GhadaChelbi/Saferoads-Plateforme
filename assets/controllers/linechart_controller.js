import { Controller } from '@hotwired/stimulus';

/*
 * Graphique d'évolution (une série) en SVG : tracé animé, aire légère,
 * graduations arrondies, crosshair + infobulle au survol.
 * Usage : data-controller="linechart" data-linechart-points-value='[{"label":"janv.","value":1240}, …]'
 */
const NS = 'http://www.w3.org/2000/svg';
const fmt = new Intl.NumberFormat('fr-FR');

function el(name, attrs = {}, parent = null) {
    const n = document.createElementNS(NS, name);
    Object.entries(attrs).forEach(([k, v]) => n.setAttribute(k, v));
    parent?.appendChild(n);
    return n;
}

// Pas "rond" (1, 2, 5 × 10^n) pour 4 graduations
function niceStep(max) {
    const raw = Math.max(max, 1) / 4;
    const p = 10 ** Math.floor(Math.log10(raw));
    const n = raw / p;
    return (n <= 1 ? 1 : n <= 2 ? 2 : n <= 5 ? 5 : 10) * p;
}

export default class extends Controller {
    static values = { points: Array, unit: { type: String, default: '' } };

    connect() {
        this.tip = document.createElement('div');
        this.tip.className = 'lc-tip';
        this.element.appendChild(this.tip);
        this.observer = new ResizeObserver(() => this.draw());
        this.observer.observe(this.element);
    }

    disconnect() {
        this.observer?.disconnect();
    }

    draw() {
        this.svg?.remove();
        const pts = this.pointsValue;
        const w = this.element.clientWidth, h = 240;
        const m = { t: 16, r: 16, b: 28, l: 48 };
        const iw = w - m.l - m.r, ih = h - m.t - m.b;
        if (iw <= 0 || pts.length < 2) return;

        const step = niceStep(Math.max(...pts.map(p => p.value)));
        const max = Math.ceil(Math.max(...pts.map(p => p.value)) / step) * step;
        const x = i => m.l + (i / (pts.length - 1)) * iw;
        const y = v => m.t + ih - (v / max) * ih;

        const svg = this.svg = el('svg', { viewBox: `0 0 ${w} ${h}`, width: w, height: h, class: 'lc-svg', role: 'img', 'aria-label': 'Évolution dans le temps' });
        this.element.prepend(svg);

        // Grille et axe Y
        for (let v = 0; v <= max; v += step) {
            el('line', { x1: m.l, x2: m.l + iw, y1: y(v), y2: y(v), class: 'lc-gridline' }, svg);
            el('text', { x: m.l - 8, y: y(v) + 4, 'text-anchor': 'end', class: 'lc-tick' }, svg).textContent = fmt.format(v);
        }
        // Axe X
        pts.forEach((p, i) => {
            el('text', { x: x(i), y: h - 8, 'text-anchor': 'middle', class: 'lc-tick' }, svg).textContent = p.label;
        });

        // Aire + ligne
        const d = pts.map((p, i) => `${i ? 'L' : 'M'}${x(i)},${y(p.value)}`).join(' ');
        el('path', { d: `${d} L${x(pts.length - 1)},${y(0)} L${x(0)},${y(0)} Z`, class: 'lc-area' }, svg);
        const line = el('path', { d, class: 'lc-line' }, svg);
        const len = line.getTotalLength();
        line.style.strokeDasharray = len;
        line.style.strokeDashoffset = this.drawn ? 0 : len;
        requestAnimationFrame(() => { line.style.transition = 'stroke-dashoffset 1.4s cubic-bezier(.2,.8,.2,1)'; line.style.strokeDashoffset = 0; });
        this.drawn = true;

        // Point final + étiquette de la dernière valeur
        const last = pts[pts.length - 1];
        el('circle', { cx: x(pts.length - 1), cy: y(last.value), r: 5, class: 'lc-dot' }, svg);

        // Survol
        const cross = el('line', { y1: m.t, y2: m.t + ih, class: 'lc-cross' }, svg);
        const dot = el('circle', { r: 5, class: 'lc-dot', style: 'opacity:0' }, svg);
        const hit = el('rect', { x: m.l, y: m.t, width: iw, height: ih, fill: 'transparent' }, svg);
        hit.addEventListener('pointermove', e => {
            const r = svg.getBoundingClientRect();
            const i = Math.max(0, Math.min(pts.length - 1, Math.round(((e.clientX - r.left - m.l) / iw) * (pts.length - 1))));
            cross.setAttribute('x1', x(i)); cross.setAttribute('x2', x(i)); cross.style.opacity = 1;
            dot.setAttribute('cx', x(i)); dot.setAttribute('cy', y(pts[i].value)); dot.style.opacity = 1;
            this.tip.innerHTML = `<strong>${fmt.format(pts[i].value)}</strong> ${this.unitValue}<br><small>${pts[i].label}</small>`;
            this.tip.style.opacity = 1;
            const tx = Math.min(w - this.tip.offsetWidth - 4, Math.max(4, x(i) - this.tip.offsetWidth / 2));
            this.tip.style.transform = `translate(${tx}px, ${Math.max(0, y(pts[i].value) - this.tip.offsetHeight - 12)}px)`;
        });
        hit.addEventListener('pointerleave', () => { cross.style.opacity = 0; dot.style.opacity = 0; this.tip.style.opacity = 0; });
    }
}
