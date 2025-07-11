// Direct Timer Implementation
(function() {
    // Wait for DOM to be fully loaded
    window.addEventListener('DOMContentLoaded', initializeTimer);
    
    // Also try on window load (as a backup)
    window.addEventListener('load', function() {
        console.log('[DirectTimer] Window loaded, initializing timer');
        initializeTimer();
    });
    
    // Initialization flag to prevent multiple initializations
    let isInitialized = false;
    
    function initializeTimer() {
        if (isInitialized) return;
        isInitialized = true;
        
        console.log('[DirectTimer] Initializing direct timer implementation');
        
        // Get the dashboard timer element
        const timer = document.getElementById('dashboard-session-timer');
        if (!timer) {
            console.warn('[DirectTimer] Timer element not found');
            return;
        }
        
        // Find or create time display element
        let timeDisplay = timer.querySelector('.time-display');
        if (!timeDisplay) {
            timeDisplay = timer.querySelector('div:first-child');
            if (timeDisplay) {
                timeDisplay.classList.add('time-display');
            } else {
                console.warn('[DirectTimer] Time display element not found');
                return;
            }
        }
        
        // Initial timer values
        const TOTAL_SECONDS = 5 * 60; // 5 minutes
        let remainingSeconds = TOTAL_SECONDS;
        
        // Update the timer display initially
        updateTimerDisplay();
        
        // Set up interval to update timer every second
        const intervalId = setInterval(function() {
            remainingSeconds--;
            
            if (remainingSeconds <= 0) {
                clearInterval(intervalId);
                remainingSeconds = 0;
                console.log('[DirectTimer] Timer expired, should logout');
                
                // Try to logout (redirect to login page after a short delay)
                setTimeout(function() {
                    window.location.href = '/login';
                }, 2000);
            }
            
            updateTimerDisplay();
        }, 1000);
        
        // Function to update the timer display
        function updateTimerDisplay() {
            const minutes = Math.floor(remainingSeconds / 60);
            const seconds = remainingSeconds % 60;
            const timeString = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            
            timeDisplay.textContent = `Session expiring in: ${timeString}`;
            
            // Update colors based on remaining time
            if (remainingSeconds <= 30) {
                // Critical - red
                timer.style.backgroundColor = '#dc3545';
                timer.style.color = '#fff';
                timer.style.borderColor = '#dc3545';
            } else if (remainingSeconds <= 60) {
                // Warning - yellow/orange
                timer.style.backgroundColor = '#f8d7da';
                timer.style.color = '#721c24';
                timer.style.borderColor = '#f5c6cb';
            } else {
                // Normal - blue
                timer.style.backgroundColor = '#e2f0ff';
                timer.style.color = '#0056b3';
                timer.style.borderColor = '#b8daff';
            }
        }
        
        // Add click handler to reset timer
        timer.addEventListener('click', function() {
            console.log('[DirectTimer] Timer clicked, resetting');
            remainingSeconds = TOTAL_SECONDS;
            updateTimerDisplay();
        });
        
        console.log('[DirectTimer] Timer initialization complete');
    }
})();
