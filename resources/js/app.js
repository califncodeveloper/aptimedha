/**
 * AptiMedha - MCQ Practice & Placement Preparation Platform
 *
 * Main JavaScript entry point.
 * Powered by Vite, Tailwind CSS 4, Alpine.js, and Framer Motion.
 */

import '../css/app.css';
import { initMotion } from './animations';

document.addEventListener('DOMContentLoaded', () => {
    // Initialize Framer Motion animation engine
    initMotion();

    // Auto-dismiss flash messages after 5 seconds
    const flashMessages = document.querySelectorAll('[data-auto-dismiss]');
    flashMessages.forEach((el) => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.3s ease';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 300);
        }, 5000);
    });
});
