import './bootstrap';
import Alpine from 'alpinejs';
import './direct-timer'; // Direct timer implementation (must be first)
import './session-tracker'; // Import session tracker
import './timer-debug'; // Import timer debug script
import './timer-fix'; // Import direct timer fix

// Ensure Alpine is only initialized once
if (typeof window.Alpine === 'undefined') {
    window.Alpine = Alpine;

    // Start Alpine
    Alpine.start();

    // Add navigate function if it doesn't exist (for Livewire compatibility)
    if (!window.Alpine.navigate) {
        window.Alpine.navigate = function (url) {
            window.location.href = url;
        };
    }
} else {
    // If Alpine is already defined, ensure navigate function exists
    if (!window.Alpine.navigate) {
        window.Alpine.navigate = function (url) {
            window.location.href = url;
        };
    }
}
