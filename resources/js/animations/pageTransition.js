/**
 * AptiMedha Animation Engine - Smooth Page Transitions
 * Powered by Framer Motion (motion package)
 */

import { animate } from 'motion';
import { prefersReducedMotion } from './reducedMotion';

export function initPageTransition() {
    if (prefersReducedMotion()) return;

    const mainEl = document.querySelector('main');
    if (!mainEl) return;

    try {
        const anim = animate(
            mainEl,
            { opacity: [0.94, 1], y: [8, 0] },
            {
                duration: 0.3,
                ease: [0.22, 1, 0.36, 1]
            }
        );

        if (anim && anim.finished) {
            anim.finished.then(() => {
                mainEl.style.transform = '';
                mainEl.style.opacity = '';
            }).catch(() => {
                mainEl.style.transform = '';
                mainEl.style.opacity = '';
            });
        }
    } catch (err) {
        mainEl.style.transform = '';
        mainEl.style.opacity = '';
    }
}
