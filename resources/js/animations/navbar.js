/**
 * AptiMedha Animation Engine - Navbar Scroll Shrink Effect
 * Powered by Framer Motion & native high-performance scroll observation
 */

import { prefersReducedMotion } from './reducedMotion';

export function initNavbarShrink() {
    const header = document.querySelector('header.sticky');
    if (!header) return;

    const navInner = header.querySelector('.flex.items-center.justify-between');
    const logo = header.querySelector('.group.transition-transform');

    let isShrunk = false;

    const updateNavbar = () => {
        const scrollY = window.scrollY || window.pageYOffset || 0;
        const shouldShrink = scrollY > 35;

        if (shouldShrink && !isShrunk) {
            isShrunk = true;
            header.classList.add('navbar-shrunk', 'shadow-sm', 'bg-white/98');
            header.classList.remove('bg-white/95', 'shadow-xs');
            if (navInner) {
                navInner.style.height = window.innerWidth >= 640 ? '56px' : '52px';
                navInner.style.transition = prefersReducedMotion() ? 'none' : 'height 0.25s cubic-bezier(0.22, 1, 0.36, 1)';
            }
            if (logo && !prefersReducedMotion()) {
                logo.style.transform = 'scale(0.96)';
                logo.style.transition = 'transform 0.25s cubic-bezier(0.22, 1, 0.36, 1)';
            }
        } else if (!shouldShrink && isShrunk) {
            isShrunk = false;
            header.classList.remove('navbar-shrunk', 'shadow-sm', 'bg-white/98');
            header.classList.add('bg-white/95', 'shadow-xs');
            if (navInner) {
                navInner.style.height = '';
            }
            if (logo) {
                logo.style.transform = '';
            }
        }
    };

    window.addEventListener('scroll', updateNavbar, { passive: true });
    updateNavbar();
}
