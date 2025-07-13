<x-app-layout>
    {{-- Removed the <x-slot name="header"> section with the large welcome message --}}

    <div class="py-8 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden rounded-xl shadow-md border border-gray-100">
                <div class="p-6 text-gray-900">
                   
                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-b from-blue-100 to-transparent h-20 rounded-t-xl -mx-6 -mt-6">
                        </div>
                        <div class="relative">
                            @livewire('dashboard')
                        </div>
                    </div>
                </div>
            </div>

            
        </div>
    </div>
</x-app-layout>

<!-- Inline timer script to ensure it loads without npm/Vite -->
<script>
    // Global variables for the timer
    var TOTAL_SECONDS = 5 * 60; // 5 minutes
    var remainingSeconds = TOTAL_SECONDS;
    var timerIntervalId = null;
    var timerInitialized = false;
    var lastActivityTime = new Date();
    var sessionCheckInterval = null;

    // Initialize timer on page load
    window.addEventListener('load', initSessionTimer);
    document.addEventListener('DOMContentLoaded', initSessionTimer);

    // User activity detection
    document.addEventListener('mousedown', resetActivityTimer);
    document.addEventListener('mousemove', resetActivityTimer);
    document.addEventListener('keydown', resetActivityTimer);
    document.addEventListener('scroll', resetActivityTimer);
    document.addEventListener('touchstart', resetActivityTimer);
    document.addEventListener('click', resetActivityTimer);

    // Handle browser close or tab close detection via beforeunload
    window.addEventListener('beforeunload', function(e) {
        // Log the time of browser closure in localStorage
        localStorage.setItem('browser_closed_at', new Date().getTime());
        // Allow the browser to close normally
        return undefined;
    });

    // Check for browser closure on page load
    window.addEventListener('load', function() {
        var closedTime = localStorage.getItem('browser_closed_at');
        if (closedTime) {
            var now = new Date().getTime();
            var timeAway = (now - parseInt(closedTime)) / 1000; // in seconds

            console.log('[SessionTimer] Browser was closed for ' + timeAway + ' seconds');

            // If browser was closed for more than 5 minutes, auto-logout
            if (timeAway > 300) { // 5 minutes
                console.log('[SessionTimer] Browser was closed too long, logging out');
                window.location.href = '/login';
            }

            // Clear the stored time
            localStorage.removeItem('browser_closed_at');
        }

        // Start regular session status checks
        startSessionChecks();
    });

    // Regular session status checks
    function startSessionChecks() {
        if (sessionCheckInterval) {
            clearInterval(sessionCheckInterval);
        }

        // Check session status every 30 seconds
        sessionCheckInterval = setInterval(function() {
            checkSessionStatus();
        }, 30000); // 30 seconds
    }

    // Check session status with the server
    function checkSessionStatus() {
        try {
            fetch('/session-status')
                .then(response => response.json())
                .then(data => {
                    console.log('[SessionTimer] Session status check:', data);
                    if (data.status === 'expired') {
                        console.log('[SessionTimer] Session expired on server, logging out');
                        window.location.href = '/login';
                    }
                })
                .catch(error => {
                    console.warn('[SessionTimer] Error checking session status:', error);
                });
        } catch (e) {
            console.warn('[SessionTimer] Error in session status check:', e);
        }
    }

    // Reset timer function (called when timer is clicked)
    function resetSessionTimer() {
        console.log('[SessionTimer] Timer clicked, resetting');
        remainingSeconds = TOTAL_SECONDS;
        updateTimerDisplay();

        // Also try AJAX heartbeat to update session on server
        try {
            var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrfToken) {
                fetch('/session-heartbeat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                }).catch(function(error) {
                    console.warn('[SessionTimer] Heartbeat error:', error);
                });
            }
        } catch (e) {
            console.warn('[SessionTimer] Error sending heartbeat:', e);
        }
    }

    // Reset activity timer based on user interaction
    function resetActivityTimer() {
        // Only reset if more than 1 second since last reset (prevent excess calls)
        var now = new Date();
        if ((now - lastActivityTime) > 1000) {
            lastActivityTime = now;
            resetSessionTimer();
        }
    }

    // Main timer initialization function
    function initSessionTimer() {
        // Only initialize once
        if (timerInitialized) {
            return;
        }
        timerInitialized = true;

        console.log('[SessionTimer] Setting up timer functionality');

        // Update the timer immediately
        updateTimerDisplay();

        // Set up interval to update timer every second
        timerIntervalId = setInterval(function() {
            remainingSeconds--;

            if (remainingSeconds <= 0) {
                clearInterval(timerIntervalId);
                remainingSeconds = 0;
                console.log('[SessionTimer] Timer expired, logging out...');

                // Try to logout via AJAX first
                try {
                    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    if (csrfToken) {
                        fetch('/auto-logout', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        }).catch(function() {
                            // Fallback to redirect on error
                            window.location.href = '/login';
                        });
                    } else {
                        // No CSRF token, just redirect
                        window.location.href = '/login';
                    }
                } catch (e) {
                    console.error('[SessionTimer] Error during logout:', e);
                    window.location.href = '/login';
                }

                // Redirect to login page after a short delay as fallback
                setTimeout(function() {
                    window.location.href = '/login';
                }, 2000);

                return;
            }

            updateTimerDisplay();
        }, 1000);
    }

    // Update timer display and colors
    function updateTimerDisplay() {
        var timer = document.getElementById('dashboard-session-timer');
        if (!timer) {
            console.warn('[SessionTimer] Timer element not found');
            return;
        }

        var timeDisplay = timer.querySelector('.time-display');
        if (!timeDisplay) {
            console.warn('[SessionTimer] Time display element not found');
            return;
        }

        var minutes = Math.floor(remainingSeconds / 60);
        var seconds = remainingSeconds % 60;
        var timeString = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;

        timeDisplay.textContent = 'Session expiring in: ' + timeString;

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

    // Handle page visibility change (tab switching)
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            // User returned to the page - update the timer if they were away too long
            var now = new Date();
            var timeAway = (now - lastActivityTime) / 1000; // seconds

            if (timeAway > 300) { // 5 minutes
                // Been away too long, log out
                window.location.href = '/login';
            } else {
                // Reset timer on return
                resetSessionTimer();
            }
        }
    });
</script>
