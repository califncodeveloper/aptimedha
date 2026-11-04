/**
 * AptiMedha Animation Engine - Main Entry Point
 * Orchestrates Framer Motion animations across the application with fail-safe isolation
 */

import { initScrollReveal } from './scrollReveal';
import { initParallax } from './parallax';
import { initNavbarShrink } from './navbar';
import { initTextAnimations } from './textAnimation';
import { initMarquees } from './marquee';
import { initInteractions } from './interactions';
import { initPageTransition } from './pageTransition';

export function initMotion() {
    const modules = [
        ['PageTransition', initPageTransition],
        ['NavbarShrink', initNavbarShrink],
        ['TextAnimations', initTextAnimations],
        ['ScrollReveal', initScrollReveal],
        ['Parallax', initParallax],
        ['Marquees', initMarquees],
        ['Interactions', initInteractions],
    ];

    modules.forEach(([name, fn]) => {
        try {
            fn();
        } catch (err) {
            console.warn(`[AptiMedha Motion] Error initializing ${name}:`, err);
        }
    });
}

export {
    initScrollReveal,
    initParallax,
    initNavbarShrink,
    initTextAnimations,
    initMarquees,
    initInteractions,
    initPageTransition
};
