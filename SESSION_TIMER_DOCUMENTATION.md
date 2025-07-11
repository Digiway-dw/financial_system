# Session Timer Implementation

This document explains how the session timer is implemented in the financial system. The timer counts down from 5 minutes and logs the user out after inactivity.

## Implementation Details

The session timer is implemented as a fully inline JavaScript solution in the `dashboard.blade.php` file. This approach ensures that:

1. The timer works without requiring npm/Vite asset building
2. The timer is visible on all pages where needed
3. The timer resets on user activity
4. The timer triggers auto-logout on inactivity
5. The timer detects browser closure and logs out users after 5 minutes of inactivity

## Technical Implementation

The implementation uses the following features:

### Timer Display

The timer appears in the dashboard header with the following HTML structure:

```html
<div id="dashboard-session-timer" class="bg-blue-50 text-blue-600 border border-blue-200 rounded-lg px-4 py-2 shadow-sm font-medium cursor-pointer hover:bg-blue-100 transition-colors duration-200" onclick="resetSessionTimer()">
    <div class="time-display text-sm font-bold">Session expiring in: 5:00</div>
    <div class="info text-xs">Click to extend session</div>
</div>
```

### JavaScript Timer Logic

The timer logic is implemented directly in the dashboard.blade.php file as an inline script:

- **Timer Initialization**: Timer starts automatically when the page loads
- **Activity Detection**: Timer resets when the user interacts with the page
- **Visual Feedback**: Timer changes color as the countdown approaches zero:
  - Blue: Normal (more than 1 minute remaining)
  - Yellow/Orange: Warning (less than 1 minute remaining)
  - Red: Critical (less than 30 seconds remaining)
- **Session Extension**: Clicking the timer resets the countdown
- **Auto-Logout**: User is redirected to the login page when the timer reaches zero

### Browser Closure Detection

The system implements browser closure detection:

1. When the browser is about to close, the closure time is stored in localStorage
2. When the user returns to the application, it checks how much time has passed
3. If more than 5 minutes have passed, the user is automatically logged out
4. A session cookie with a 5-minute expiration is also used to track session status

### Server-Side Session Management

The implementation includes server-side session management via:

1. **Session Heartbeat**: A heartbeat request is sent to `/session-heartbeat` when the timer is reset
2. **Session Status Check**: A request to `/session-status` checks if the session is still valid
3. **Auto-Logout**: A request to `/auto-logout` ends the session before redirecting to login
4. **Session Timeout Middleware**: Server-side middleware that checks for inactive sessions

### Session Controller

A dedicated `SessionController` handles:

- Session heartbeats to extend the session
- Auto-logout functionality to properly end work sessions
- Session status checks to verify if a session is still valid

## Fallback Mechanisms

The implementation includes several fallback mechanisms:

1. **Multiple Initialization Events**: The timer initializes on both `load` and `DOMContentLoaded` events
2. **Tab Visibility Tracking**: The timer tracks when users switch tabs and logs them out if they've been away too long
3. **Error Handling**: All AJAX requests have error handling to ensure the user is always redirected to login on timeout
4. **Browser Closure Detection**: The system tracks when the browser is closed and logs out users after 5 minutes
5. **Server-Side Middleware**: A server-side middleware provides an additional layer of protection

## Browser Compatibility

This implementation has been tested and works on:
- Chrome
- Firefox
- Edge
- Safari

## Troubleshooting

If the timer is not visible or not functioning correctly, try the following:

1. Clear browser cache
2. Clear Laravel view cache with `php artisan view:clear`
3. Clear Laravel application cache with `php artisan cache:clear`
4. Check browser console for JavaScript errors
5. Ensure localStorage is enabled in the browser

## Future Improvements

Potential future improvements:
- Server-side validation of session timeouts
- Configurable timeout duration
- Remember user's last page for better post-login experience
- Integration with WebSockets for real-time session management
