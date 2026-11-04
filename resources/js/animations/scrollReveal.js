/**
 * AptiMedha Animation Engine - Scroll Reveal & Staggered Animations
 * Powered by Framer Motion (motion package)
 */

import { animate, inView, stagger } from 'motion';
import { prefersReducedMotion } from './reducedMotion';

export function initScrollReveal() {
    if (prefersReducedMotion()) return;

    // 1. Single Element Scroll Reveals
    const revealElements = document.querySelectorAll('[data-motion]');
    revealElements.forEach((el) => {
        // Skip if inside a stagger container
        if (el.closest('[data-motion-stagger]') && el.hasAttribute('data-motion-item')) {
            return;
        }

        const motionType = el.getAttribute('data-motion');
        const delay = parseFloat(el.getAttribute('data-motion-delay') || '0');
        const duration = parseFloat(el.getAttribute('data-motion-duration') || '0.5');

        let keyframes;
        switch (motionType) {
            case 'fade-up':
                keyframes = { opacity: [0, 1], y: [22, 0] };
                break;
            case 'fade-down':
                keyframes = { opacity: [0, 1], y: [-22, 0] };
                break;
            case 'fade-left':
                keyframes = { opacity: [0, 1], x: [-26, 0] };
                break;
            case 'fade-right':
                keyframes = { opacity: [0, 1], x: [26, 0] };
                break;
            case 'scale-up':
                keyframes = { opacity: [0, 1], scale: [0.96, 1] };
                break;
            case 'clip-reveal':
                keyframes = { opacity: [0.3, 1], clipPath: ['inset(100% 0% 0% 0%)', 'inset(0% 0% 0% 0%)'] };
                break;
            case 'fade-in':
            default:
                keyframes = { opacity: [0, 1] };
        }

        const runAnimation = () => {
            try {
                const anim = animate(el, keyframes, {
                    duration,
                    delay,
                    ease: [0.22, 1, 0.36, 1]
                });

                if (anim && anim.finished) {
                    anim.finished.then(() => {
                        el.style.transform = '';
                        el.style.opacity = '';
                        el.style.clipPath = '';
                    }).catch(() => {
                        el.style.transform = '';
                        el.style.opacity = '';
                    });
                }
            } catch (err) {
                el.style.transform = '';
                el.style.opacity = '';
            }
        };

        const rect = el.getBoundingClientRect();
        const isInViewport = rect.top < window.innerHeight && rect.bottom > 0;

        if (isInViewport) {
            runAnimation();
        } else {
            // Only hide when below viewport waiting to be scrolled into view
            el.style.opacity = '0';
            inView(el, () => {
                runAnimation();
            }, { amount: 0.1 });
        }
    });

    // 2. Staggered Container Animations (Cards, Grids, Lists)
    const staggerContainers = document.querySelectorAll('[data-motion-stagger]');
    staggerContainers.forEach((container) => {
        const items = container.querySelectorAll('[data-motion-item]');
        if (!items || items.length === 0) return;

        const staggerInterval = parseFloat(container.getAttribute('data-motion-stagger-delay') || '0.06');
        const duration = parseFloat(container.getAttribute('data-motion-duration') || '0.45');

        const runStagger = () => {
            try {
                const anim = animate(
                    items,
                    { opacity: [0, 1], y: [20, 0] },
                    {
                        delay: stagger(staggerInterval),
                        duration,
                        ease: [0.22, 1, 0.36, 1]
                    }
                );

                if (anim && anim.finished) {
                    anim.finished.then(() => {
                        items.forEach((item) => {
                            item.style.transform = '';
                            item.style.opacity = '';
                        });
                    }).catch(() => {
                        items.forEach((item) => {
                            item.style.transform = '';
                            item.style.opacity = '';
                        });
                    });
                }
            } catch (err) {
                items.forEach((item) => {
                    item.style.transform = '';
                    item.style.opacity = '';
                });
            }
        };

        const cRect = container.getBoundingClientRect();
        const isInViewport = cRect.top < window.innerHeight && cRect.bottom > 0;

        if (isInViewport) {
            runStagger();
        } else {
            items.forEach((item) => {
                item.style.opacity = '0';
            });
            inView(container, () => {
                runStagger();
            }, { amount: 0.08 });
        }
    });
}
