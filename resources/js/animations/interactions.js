/**
 * AptiMedha Animation Engine - Micro-Interactions & Image Zoom Effects
 * Powered by Framer Motion (motion package)
 */

import { animate } from 'motion';
import { prefersReducedMotion } from './reducedMotion';

export function initInteractions() {
    if (prefersReducedMotion()) return;

    // 1. Subtle Image Zoom on Card Hover
    const zoomCards = document.querySelectorAll('[data-motion-zoom]');
    zoomCards.forEach((card) => {
        const img = card.querySelector('img');
        if (!img) return;

        card.addEventListener('mouseenter', () => {
            try {
                animate(img, { scale: 1.06 }, { duration: 0.45, ease: [0.22, 1, 0.36, 1] });
            } catch (e) {}
        });

        card.addEventListener('mouseleave', () => {
            try {
                animate(img, { scale: 1 }, { duration: 0.45, ease: [0.22, 1, 0.36, 1] });
            } catch (e) {}
        });
    });

    // 2. Spring Micro-interaction on Interactive Buttons & Badges
    const springElements = document.querySelectorAll('[data-motion-spring]');
    springElements.forEach((el) => {
        el.addEventListener('pointerdown', () => {
            try {
                animate(el, { scale: 0.96 }, { duration: 0.15 });
            } catch (e) {}
        });

        const release = () => {
            try {
                animate(el, { scale: 1 }, { duration: 0.25, ease: [0.34, 1.56, 0.64, 1] });
            } catch (e) {}
        };

        el.addEventListener('pointerup', release);
        el.addEventListener('pointercancel', release);
    });

    // 3. Interactive MCQ Option Click Feedback
    const interactiveOptions = document.querySelectorAll('.interactive-mcq-option, [data-interactive-option]');
    interactiveOptions.forEach((opt) => {
        opt.addEventListener('click', () => {
            try {
                animate(opt, { scale: [0.97, 1.015, 1] }, { duration: 0.28, ease: 'easeOut' });
            } catch (e) {}
        });
    });
}
