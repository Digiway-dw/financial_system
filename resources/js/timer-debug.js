// Debug script for session timer
document.addEventListener('DOMContentLoaded', function() {
    console.log('Timer debug script loaded');
    
    // Wait a short time to ensure session-tracker.js has initialized
    setTimeout(function() {
        // Get references to timer elements
        const dashboardTimer = document.getElementById('dashboard-session-timer');
        const floatingTimer = document.getElementById('session-timer');
        
        console.log('Timer elements found:');
        console.log('- Dashboard timer exists:', !!dashboardTimer);
        console.log('- Floating timer exists:', !!floatingTimer);
        
        if (dashboardTimer) {
            console.log('Dashboard timer visibility:', window.getComputedStyle(dashboardTimer).display);
        }
        
        if (floatingTimer) {
            console.log('Floating timer visibility:', window.getComputedStyle(floatingTimer).display);
        }
        
        // Diagnostic helper for timers - logs clicks
        const monitorTimer = function(timer, name) {
            if (timer) {
                // Check if already monitored
                if (!timer.hasAttribute('data-debug-monitored')) {
                    timer.addEventListener('click', function() {
                        console.log(`${name} clicked at ${new Date().toLocaleTimeString()}`);
                    });
                    timer.setAttribute('data-debug-monitored', 'true');
                    console.log(`Debug monitoring added to ${name}`);
                }
            }
        };
        
        // Monitor all timer elements
        monitorTimer(dashboardTimer, 'Dashboard timer');
        monitorTimer(floatingTimer, 'Floating timer');
    }, 500);
});
