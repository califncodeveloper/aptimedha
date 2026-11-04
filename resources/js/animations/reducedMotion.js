/**
 * AptiMedha Animation Engine - Accessibility & Reduced Motion Helper
 *
 * Respects user OS preference for reduced motion.
 */

export function prefersReducedMotion() {
    if (typeof window === 'undefined' || !window.matchMedia) {
        return false;
    }
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

export function getSafeDuration(preferredDuration, fallbackDuration = 0) {
    return prefersReducedMotion() ? fallbackDuration : preferredDuration;
}

