/**
 * AptiMedha - MCQ Practice & Placement Preparation Platform
 *
 * Main JavaScript entry point.
 * Alpine.js and Lucide Icons are loaded via CDN in layouts.
 */

// Import CSS
import '../css/app.css';

// Flash message auto-dismiss
document.addEventListener('DOMContentLoaded', () => {
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
