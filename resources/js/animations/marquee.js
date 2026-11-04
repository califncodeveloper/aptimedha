/**
 * AptiMedha Animation Engine - Testimonials & Placement Partners Animated Marquee
 * Powered by Framer Motion (motion package)
 */

import { animate } from 'motion';
import { prefersReducedMotion } from './reducedMotion';

export function initMarquees() {
    const marqueeContainers = document.querySelectorAll('[data-motion-marquee]');
    if (marqueeContainers.length === 0) return;

    if (prefersReducedMotion()) {
        marqueeContainers.forEach((container) => {
            const track = container.querySelector('.motion-marquee-track');
            if (track) {
                track.style.overflowX = 'auto';
                track.style.transform = 'none';
            }
        });
        return;
    }

    marqueeContainers.forEach((container) => {
        const track = container.querySelector('.motion-marquee-track');
        if (!track) return;

        // Clone items once so track loops seamlessly without visible seams
        const originalContent = track.innerHTML;
        track.innerHTML = originalContent + originalContent;

        const duration = parseFloat(container.getAttribute('data-motion-duration') || '36');
        const reverse = container.getAttribute('data-motion-direction') === 'reverse';

        const startX = reverse ? '-50%' : '0%';
        const endX = reverse ? '0%' : '-50%';

        try {
            const controls = animate(
                track,
                { x: [startX, endX] },
                {
                    duration,
                    ease: 'linear',
                    repeat: Infinity
                }
            );

            // Pause animation on hover / focus for readability and accessibility
            container.addEventListener('mouseenter', () => controls.pause());
            container.addEventListener('mouseleave', () => controls.play());
            container.addEventListener('touchstart', () => controls.pause(), { passive: true });
            container.addEventListener('touchend', () => controls.play(), { passive: true });
        } catch (e) {}
    });
}
