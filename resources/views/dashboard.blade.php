<x-app-layout>
    <x-slot name="header">
        <div
            class="flex justify-between items-center bg-gradient-to-r from-blue-500 to-indigo-600 p-6 rounded-xl shadow-md">
            <div>
                <h2 class="font-bold text-2xl text-white tracking-wide flex items-center">
                    <span class="bg-white text-blue-600 p-2 rounded-lg mr-3 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                    </span>
                    {{ __('Welcome Back,') }} <span
                        class="text-blue-100 ml-2 font-extrabold">{{ Auth::user()->name }}</span>!
                </h2>
                <p class="text-sm text-blue-100 mt-2 flex items-center">
                    <span class="inline-block h-2 w-2 rounded-full bg-green-400 mr-2 animate-pulse"></span>
                    {{ __('You are logged in as') }}
                    <span class="font-medium text-white ml-1 bg-blue-700/30 px-3 py-0.5 rounded-full">
                        {{ Auth::user()->roles->first()->name ?? 'User' }}
                    </span>
                </p>
            </div>
            <div class="text-right flex flex-col items-end gap-2">
                <!-- Session timer - fully inline -->
                <div id="dashboard-session-timer" class="bg-blue-50 text-blue-600 border border-blue-200 rounded-lg px-4 py-2 shadow-sm font-medium cursor-pointer hover:bg-blue-100 transition-colors duration-200" onclick="resetSessionTimer()">
                    <div class="time-display text-sm font-bold">Session expiring in: 5:00</div>
                    <div class="info text-xs">Click to extend session</div>
                </div>
                
                <div class="bg-white p-4 rounded-lg shadow-md border border-blue-100">
                    <p class="text-lg font-semibold text-gray-800">{{ \Carbon\Carbon::now()->format('l, F j, Y') }}</p>
                    <p class="text-sm text-blue-600 font-medium" x-data="{ time: '' }" x-init="setInterval(() => { time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' }); }, 1000)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-text="time"></span>
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden rounded-xl shadow-md border border-gray-100">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-wrap gap-4 mb-8">
                        @can('create-transactions')
                            <a href="{{ route('transactions.cash') }}"
                                class="px-6 py-3 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold flex items-center gap-2 shadow-md hover:shadow-lg transition-all duration-300 transform hover:translate-y-[-2px]">
                                <x-heroicon-o-currency-dollar class="w-5 h-5" /> كاش
                            </a>
                            <a href="{{ route('transactions.receive') }}"
                                class="px-6 py-3 rounded-lg bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold flex items-center gap-2 shadow-md hover:shadow-lg transition-all duration-300 transform hover:translate-y-[-2px]">
                                <x-heroicon-o-arrow-down-tray class="w-5 h-5" /> استقبال
                            </a>
                            <a href="{{ route('transactions.send') }}"
                                class="px-6 py-3 rounded-lg bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold flex items-center gap-2 shadow-md hover:shadow-lg transition-all duration-300 transform hover:translate-y-[-2px]">
                                <x-heroicon-o-paper-airplane class="w-5 h-5" /> إرسال
                            </a>
                        @endcan
                    </div>

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

            <!-- System Status -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="bg-white overflow-hidden rounded-xl shadow-md border border-gray-100 p-5 hover:shadow-lg transition-all duration-300 transform hover:scale-[1.01]">
                    <div class="flex items-center">
                        <div class="rounded-full bg-gradient-to-br from-green-400 to-green-500 p-3 mr-4 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 text-lg">System Status</h4>
                            <p class="text-sm text-gray-500">All systems operational</p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white overflow-hidden rounded-xl shadow-md border border-gray-100 p-5 hover:shadow-lg transition-all duration-300 transform hover:scale-[1.01]">
                    <div class="flex items-center">
                        <div class="rounded-full bg-gradient-to-br from-blue-400 to-blue-500 p-3 mr-4 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 text-lg">Need Help?</h4>
                            <p class="text-sm text-gray-500">Contact support team</p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white overflow-hidden rounded-xl shadow-md border border-gray-100 p-5 hover:shadow-lg transition-all duration-300 transform hover:scale-[1.01]">
                    <div class="flex items-center">
                        <div class="rounded-full bg-gradient-to-br from-purple-400 to-purple-500 p-3 mr-4 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 text-lg">Documentation</h4>
                            <p class="text-sm text-gray-500">View user guides & manuals</p>
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
