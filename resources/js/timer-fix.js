// Dashboard timer direct fix
document.addEventListener('DOMContentLoaded', function () {
    console.log('[Timer Fix] Dashboard timer direct fix loaded');

    // Try to fix the dashboard timer immediately
    setTimeout(function () {
        const dashboardTimer = document.getElementById('dashboard-session-timer');
        if (dashboardTimer) {
            console.log('[Timer Fix] Found dashboard timer, applying direct fix');

            // Make sure it has the right structure
            let timeDisplay = dashboardTimer.querySelector('.time-display');
            if (!timeDisplay) {
                console.log('[Timer Fix] Adding time-display class to first child');
                const firstChild = dashboardTimer.querySelector('div:first-child');
                if (firstChild) {
                    firstChild.className = 'time-display text-sm font-bold';
                }
            }

            // Add click handler to reset timer
            dashboardTimer.addEventListener('click', function () {
                console.log('[Timer Fix] Dashboard timer clicked');
                // Try to trigger the reset in the main script
                const now = new Date();
                const resetEvent = new MouseEvent('click', {
                    bubbles: true,
                    cancelable: true,
                    view: window
                });
                document.dispatchEvent(resetEvent);

                // Also update the display directly
                const timeDisplay = dashboardTimer.querySelector('.time-display');
                if (timeDisplay) {
                    timeDisplay.textContent = 'Session expiring in: 5:00';
                }

                // Reset colors
                dashboardTimer.style.backgroundColor = '#e2f0ff';
                dashboardTimer.style.color = '#0056b3';
                dashboardTimer.style.borderColor = '#b8daff';
            });

            // Start a separate countdown for this timer if needed
            let localTimeoutStartTime = new Date();
            let localCountdownTimer = setInterval(function () {
                const now = new Date();
                const elapsedTime = now - localTimeoutStartTime;
                const remainingTime = 5 * 60 * 1000 - elapsedTime;

                if (remainingTime <= 0) {
                    // Time's up
                    if (timeDisplay) {
                        timeDisplay.textContent = 'Session expiring in: 0:00';
                    }
                    dashboardTimer.style.backgroundColor = '#dc3545';
                    dashboardTimer.style.color = '#fff';
                    clearInterval(localCountdownTimer);
                    return;
                }

                // Format time as MM:SS
                const minutes = Math.floor(remainingTime / 60000);
                const seconds = Math.floor((remainingTime % 60000) / 1000);
                const timeString = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

                // Update the display
                const timeDisplay = dashboardTimer.querySelector('.time-display');
                if (timeDisplay) {
                    timeDisplay.textContent = `Session expiring in: ${timeString}`;
                }

                // Change color based on remaining time
                if (remainingTime < 30000) { // Less than 30 seconds
                    dashboardTimer.style.backgroundColor = '#dc3545';
                    dashboardTimer.style.color = '#fff';
                } else if (remainingTime < 60000) { // Less than 1 minute
                    dashboardTimer.style.backgroundColor = '#f8d7da';
                    dashboardTimer.style.color = '#721c24';
                } else {
                    dashboardTimer.style.backgroundColor = '#e2f0ff';
                    dashboardTimer.style.color = '#0056b3';
                }
            }, 1000);

            console.log('[Timer Fix] Direct fix applied successfully');
        }
    }, 1000);
});
