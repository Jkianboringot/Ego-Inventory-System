import './bootstrap';
import Alpine from 'alpinejs';
import * as Livewire from '../../vendor/livewire/livewire/dist/livewire.esm.js';

// Import OverlayScrollbars
import { OverlayScrollbars } from 'overlayscrollbars';
import '../css/OverlayScrollbars.min.css';

// Make Alpine globally available
window.Alpine = Alpine;

// Initialize components function
function initializeComponents() {
    const sidebarWrapper = document.querySelector('.sidebar-wrapper');
    if (sidebarWrapper && typeof OverlayScrollbars !== 'undefined') {
        OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
                theme: 'os-theme-light',
                autoHide: 'leave',
                clickScroll: true,
            },
        });
    }
}

// Start Alpine and Livewire
Alpine.start();
Livewire.start();

// Initialize on load
document.addEventListener('DOMContentLoaded', () => {
    initializeComponents();
});

// Reinitialize after Livewire navigation
document.addEventListener('livewire:navigated', () => {
    initializeComponents();
});