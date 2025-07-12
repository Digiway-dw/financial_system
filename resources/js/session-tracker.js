// Track user activity and handle session closure
document.addEventListener('DOMContentLoaded', function () {
    console.log('[Session Tracker] Initializing...');

    const INACTIVITY_TIMEOUT = 5 * 60 * 1000; // 5 minutes in milliseconds
    const WARNING_THRESHOLD = 60 * 1000; // Show warning when 1 minute remaining

    let inactivityTimer;
    let countdownTimer;
    let lastActivity = new Date();
    let timeoutStartTime = new Date(); // Set immediately to start countdown
    let timerDisplayElement = null;
    let isInitialized = false;

    // Create session timer element if it doesn't exist
    function createTimerElement() {
        // Check for existing dashboard timer first (for dashboard page)
        const dashboardTimer = document.getElementById('dashboard-session-timer');
        if (dashboardTimer) {
            console.log('[Session Tracker] Using dashboard timer');
            timerDisplayElement = dashboardTimer;

            // Add click handler to extend session if not already added
            if (!dashboardTimer.hasAttribute('data-timer-initialized')) {
                dashboardTimer.addEventListener('click', function (e) {
                    console.log('[Session Tracker] Dashboard timer clicked');
                    e.preventDefault();
                    resetInactivityTimer();
                });
                dashboardTimer.setAttribute('data-timer-initialized', 'true');
            }

            return dashboardTimer;
        }

        // If no dashboard timer exists, create a floating timer
        if (!timerDisplayElement) {
            console.log('[Session Tracker] Creating floating timer');
            timerDisplayElement = document.createElement('div');
            timerDisplayElement.id = 'session-timer';
            timerDisplayElement.className = 'session-timer';
            timerDisplayElement.style.cssText = `
                position: fixed;
                top: 10px;
                right: 10px;
                background-color: #e2f0ff;
                color: #0056b3;
                border: 1px solid #b8daff;
                border-radius: 4px;
                padding: 10px 15px;
                font-weight: bold;
                z-index: 9999;
                cursor: pointer;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                display: block;
            `;

            // Add click handler to extend session
            timerDisplayElement.addEventListener('click', function (e) {
                console.log('[Session Tracker] Floating timer clicked');
                e.preventDefault();
                resetInactivityTimer();
            });

            // Set initial content with proper class names
            const timeDisplay = document.createElement('div');
            timeDisplay.className = 'time-display';
            timeDisplay.textContent = `Session expiring in: 5:00`;
            timerDisplayElement.appendChild(timeDisplay);

            const infoText = document.createElement('div');
            infoText.className = 'info';
            infoText.textContent = 'Click to extend session';
            infoText.style.cssText = 'font-size: 12px; font-weight: normal;';
            timerDisplayElement.appendChild(infoText);

            document.body.appendChild(timerDisplayElement);
            console.log('[Session Tracker] Floating timer added to document');
        }

        return timerDisplayElement;
    }

    // Show timer display with countdown
    function showTimerDisplay() {
        const timerElement = createTimerElement();
        updateTimerDisplay();
    }

    // Update the timer display with remaining time
    function updateTimerDisplay() {
        if (!timeoutStartTime) {
            console.error('[Session Tracker] No timeout start time set');
            timeoutStartTime = new Date(); // Set it now as fallback
        }

        const now = new Date();
        const elapsedTime = now - timeoutStartTime;
        const remainingTime = INACTIVITY_TIMEOUT - elapsedTime;

        if (!timerDisplayElement) {
            timerDisplayElement = createTimerElement();
        }

        if (remainingTime <= 0) {
            // Time's up - show 0:00
            updateTimerContent(timerDisplayElement, "0:00", "critical");
            return;
        }

        // Format time as MM:SS
        const minutes = Math.floor(remainingTime / 60000);
        const seconds = Math.floor((remainingTime % 60000) / 1000);
        const timeString = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

        // Determine severity level based on remaining time
        let severity = "normal";
        if (remainingTime < 30000) { // Less than 30 seconds
            severity = "critical";
        } else if (remainingTime < 60000) { // Less than 1 minute
            severity = "warning";
        }

        // Update timer content with appropriate styling
        updateTimerContent(timerDisplayElement, timeString, severity);
    }

    // Helper function to update timer content and styling based on type and severity
    function updateTimerContent(element, timeString, severity) {
        if (!element) {
            console.error('[Session Tracker] No timer element to update');
            return;
        }

        // Find the time display element
        let timeDisplay = element.querySelector('.time-display');
        if (!timeDisplay) {
            console.warn('[Session Tracker] No .time-display element found, creating one');
            // Create time display if it doesn't exist
            timeDisplay = document.createElement('div');
            timeDisplay.className = 'time-display';
            if (element.firstChild) {
                element.insertBefore(timeDisplay, element.firstChild);
            } else {
                element.appendChild(timeDisplay);
            }
        }

        // Update the time text
        timeDisplay.textContent = `Session expiring in: ${timeString}`;

        // Apply appropriate styling based on severity
        if (severity === "critical") {
            // Critical - red background
            element.style.backgroundColor = '#dc3545';
            element.style.color = '#fff';
            element.style.borderColor = '#dc3545';
        } else if (severity === "warning") {
            // Warning - yellow/orange background
            element.style.backgroundColor = '#f8d7da';
            element.style.color = '#721c24';
            element.style.borderColor = '#f5c6cb';
        } else {
            // Normal - blue background
            element.style.backgroundColor = '#e2f0ff';
            element.style.color = '#0056b3';
            element.style.borderColor = '#b8daff';
        }
    }

    // Function to call when the user is leaving the page
    async function handleUserExit() {
        try {
            // Try API route first, then fall back to web route
            try {
                // Make a fetch request to the logout API endpoint
                const response = await fetch('/api/auto-logout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                if (!response.ok) {
                    throw new Error('API route failed');
                }
            } catch (error) {
                console.warn('API auto-logout failed, trying web route:', error);
                // Fallback to web route if API route fails
                await fetch('/auto-logout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });
            }
        } catch (error) {
            console.error('Error during auto-logout (both routes failed):', error);
        }
    }

    // Function to handle automatic logout after inactivity
    function setupInactivityTimer() {
        clearTimeout(inactivityTimer);

        inactivityTimer = setTimeout(function () {
            console.log('[Session Tracker] User inactive for 5 minutes, logging out...');
            handleUserExit().then(() => {
                // Redirect to login page after logout
                window.location.href = '/login';
            });
        }, INACTIVITY_TIMEOUT);

        // Start the countdown timer immediately
        timeoutStartTime = new Date();
        showTimerDisplay();

        // Set up countdown timer to update every second if not already running
        if (countdownTimer) {
            clearInterval(countdownTimer);
        }
        countdownTimer = setInterval(function () {
            updateTimerDisplay();
        }, 1000);

        console.log('[Session Tracker] Timer setup completed, countdown started');
    }

    // Reset timer on any user activity
    function resetInactivityTimer() {
        console.log('[Session Tracker] Activity detected, resetting timer');
        lastActivity = new Date();
        setupInactivityTimer();
    }

    // Initialize session tracker
    function initializeSessionTracker() {
        if (isInitialized) {
            console.log('[Session Tracker] Already initialized, skipping');
            return;
        }

        try {
            console.log('[Session Tracker] Starting initialization...');

            // Create timer elements and initialize timers
            createTimerElement();
            setupInactivityTimer();

            // Force an immediate update of the timer display
            updateTimerDisplay();

            // List of events that indicate user activity
            const activityEvents = [
                'mousedown', 'mousemove', 'keydown',
                'scroll', 'touchstart', 'click', 'keypress'
            ];

            // Add event listeners for user activity
            activityEvents.forEach(function (eventName) {
                document.addEventListener(eventName, function () {
                    // Only reset if it's been at least 1 second since last reset
                    // This prevents excessive resets during continuous activity
                    const now = new Date();
                    if ((now - lastActivity) > 1000) {
                        resetInactivityTimer();
                    }
                }, true);
            });

            isInitialized = true;
            console.log('[Session Tracker] Initialization complete');
        } catch (error) {
            console.error('[Session Tracker] Error during initialization:', error);
        }
    }

    // Initialize immediately if document is already interactive or complete
    if (document.readyState === 'interactive' || document.readyState === 'complete') {
        initializeSessionTracker();
    } else {
        // Otherwise wait for DOM to be fully loaded
        setTimeout(initializeSessionTracker, 500);
    }

    // Also initialize on window load as a fallback
    window.addEventListener('load', function () {
        if (!isInitialized) {
            console.log('[Session Tracker] Initializing on window load');
            initializeSessionTracker();
        }
    });

    // Handle page visibility change (when user switches tabs or minimizes browser)
    let hiddenTime = null;
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'hidden') {
            // User has switched away from the page
            hiddenTime = new Date();
            // Don't clear the inactivity timer while page is hidden - let it continue
        } else if (document.visibilityState === 'visible' && hiddenTime) {
            // User has returned to the page
            const awayTime = (new Date() - hiddenTime) / 1000; // time away in seconds
            console.log('User was away for', awayTime, 'seconds');

            if (awayTime > 300) { // 5 minutes
                // If away for more than 5 minutes, log out
                console.log('User was away too long, logging out');
                handleUserExit().then(() => {
                    window.location.href = '/login';
                });
            } else {
                // Reset the inactivity timer and update display
                resetInactivityTimer();
            }
            hiddenTime = null;
        }
    });

    // Handle before unload event (when user closes tab or browser)
    window.addEventListener('beforeunload', function (e) {
        try {
            handleUserExit();
        } catch (error) {
            console.error('Error during beforeunload:', error);
        }
        // Modern browsers require returning undefined to avoid showing a confirmation dialog
        // when using the beforeunload event
        return undefined;
    });

    // Send a heartbeat every minute to update the last activity timestamp on server
    setInterval(function () {
        try {
            // Only send heartbeat if user has been active in the last 5 minutes
            const timeSinceLastActivity = new Date() - lastActivity;
            if (timeSinceLastActivity < INACTIVITY_TIMEOUT) {
                // Try API route first, then fall back to web route
                fetch('/api/session-heartbeat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('API route failed');
                        }
                        return response.json();
                    })
                    .catch(error => {
                        console.warn('API heartbeat failed, trying web route:', error);
                        // Fallback to web route if API route fails
                        return fetch('/session-heartbeat', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                            }
                        });
                    })
                    .catch(error => console.error('Heartbeat error (both routes failed):', error));
            }
        } catch (error) {
            console.error('Error sending heartbeat:', error);
        }
    }, 60 * 1000); // 1 minute
});
