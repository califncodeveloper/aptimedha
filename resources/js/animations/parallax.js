/**
 * AptiMedha Animation Engine - Parallax Scrolling & Parallax Card Effects
 * High performance hardware-accelerated parallax with requestAnimationFrame
 */

import { prefersReducedMotion } from './reducedMotion';

export function initParallax() {
    if (prefersReducedMotion()) return;

    const parallaxElements = document.querySelectorAll('[data-motion-parallax]');
    const parallaxCards = document.querySelectorAll('[data-motion-parallax-card]');

    if (parallaxElements.length === 0 && parallaxCards.length === 0) return;

    let ticking = false;

    const onScroll = () => {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                const scrollY = window.scrollY || window.pageYOffset || 0;
                const windowHeight = window.innerHeight;

                // 1. Ambient Background & Floating Badge Parallax
                parallaxElements.forEach((el) => {
                    const speed = parseFloat(el.getAttribute('data-motion-parallax-speed') || '0.15');
                    const direction = el.getAttribute('data-motion-parallax-dir') || 'down';
                    const factor = direction === 'up' ? -1 : 1;
                    const offset = scrollY * speed * factor;
                    el.style.transform = `translate3d(0, ${offset.toFixed(1)}px, 0)`;
                });

                // 2. Scroll Parallax Card Effect for Project / Placement Cards
                parallaxCards.forEach((card, index) => {
                    const rect = card.getBoundingClientRect();
                    if (rect.top < windowHeight + 100 && rect.bottom > -100) {
                        const depthFactor = (index % 2 === 0 ? 0.05 : 0.09);
                        const centerOffset = (rect.top + rect.height / 2) - (windowHeight / 2);
                        const translateY = -(centerOffset * depthFactor);
                        card.style.transform = `translate3d(0, ${translateY.toFixed(1)}px, 0)`;
                    }
                });

                ticking = false;
            });
            ticking = true;
        }
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}
