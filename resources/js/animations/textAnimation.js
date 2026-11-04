/**
 * AptiMedha Animation Engine - Staggered Text Animations
 * Powered by Framer Motion (motion package)
 */

import { animate, inView, stagger } from 'motion';
import { prefersReducedMotion } from './reducedMotion';

export function initTextAnimations() {
    const textElements = document.querySelectorAll('[data-motion-text="words"]');
    if (textElements.length === 0) return;

    if (prefersReducedMotion()) {
        textElements.forEach((el) => {
            el.style.opacity = '1';
        });
        return;
    }

    textElements.forEach((el) => {
        try {
            const originalText = el.innerText.trim();
            if (!originalText) return;

            if (!el.getAttribute('aria-label')) {
                el.setAttribute('aria-label', originalText);
            }

            const words = originalText.split(/\s+/);
            el.innerHTML = words.map((word) => {
                return `<span class="inline-block overflow-hidden align-top"><span class="inline-block motion-word-item">${word}&nbsp;</span></span>`;
            }).join('');

            const wordSpans = el.querySelectorAll('.motion-word-item');
            const baseDelay = parseFloat(el.getAttribute('data-motion-delay') || '0.1');

            inView(el, () => {
                try {
                    const anim = animate(
                        wordSpans,
                        { opacity: [0, 1], y: ['100%', '0%'] },
                        {
                            delay: stagger(0.04, { startDelay: baseDelay }),
                            duration: 0.5,
                            ease: [0.22, 1, 0.36, 1]
                        }
                    );

                    if (anim && anim.finished) {
                        anim.finished.then(() => {
                            wordSpans.forEach((span) => {
                                span.style.transform = '';
                                span.style.opacity = '';
                            });
                        }).catch(() => {
                            wordSpans.forEach((span) => {
                                span.style.transform = '';
                                span.style.opacity = '';
                            });
                        });
                    }
                } catch (e) {
                    wordSpans.forEach((span) => {
                        span.style.transform = '';
                        span.style.opacity = '';
                    });
                }
            }, { amount: 0.1 });
        } catch (e) {}
    });
}
