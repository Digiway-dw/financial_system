# Work Sessions Auto-Logout Fix

## Issues Fixed

1. **Duplicate Work Sessions:** Fixed the issue where multiple active work sessions were being created for the same user.

2. **Auto-Logout Implementation:** Added functionality to automatically log out users after 5 minutes of inactivity or when they close the browser window or tab.

3. **Negative Session Durations:** Fixed the calculation of session durations to prevent negative values.

4. **User Experience:** Added a persistent visual countdown timer that always displays the remaining session time before auto-logout, allowing users to extend their session with a click.

## Changes Made

### 1. WorkSessionService Enhancement

Modified the `startSession` method in the `WorkSessionService` to first check for and close any existing active sessions before creating a new one. This prevents duplicate active sessions for the same user.

```php
public function startSession(User $user, Request $request): WorkSession
{
    // First, check if there's an active session for this user and end it
    $this->endAnyActiveSessions($user);
    
    return WorkSession::create([
        'user_id' => $user->id,
        'login_at' => now(),
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);
}
```

### 2. Auto-Logout Implementation

Added a comprehensive client-side and server-side solution:

1. **Client-Side Session Tracking:** Enhanced JavaScript code to:
   - Detect when a user closes the browser (using beforeunload event)
   - Track user inactivity and automatically log out after 5 minutes
   - Send heartbeats to update the last activity timestamp
   - Detect tab/window visibility changes and handle session timeouts
   - Display a persistent visual countdown timer showing remaining session time
   - Allow users to extend their session by clicking on the timer
   - Change timer color based on remaining time (blue > 1 min, yellow < 1 min, red < 30 sec)

2. **Server-Side Inactive Session Checker:** Created a command that runs every 5 minutes to find and close sessions that have been inactive for too long.

### 3. Session Duration Fix

Updated the `calculateDuration` method in the `WorkSession` model to handle cases where the logout time might be before the login time (which shouldn't happen normally but could occur due to clock synchronization issues):

```php
public function calculateDuration(): void
{
    if ($this->login_at && $this->logout_at) {
        // Calculate the absolute difference in minutes between login and logout
        if ($this->logout_at->lt($this->login_at)) {
            // If logout time is before login time (which shouldn't happen normally),
            // set a minimum duration (1 minute) or default to zero
            $this->duration_minutes = 0;
        } else {
            $this->duration_minutes = $this->login_at->diffInMinutes($this->logout_at);
        }
        $this->save();
    }
}
```

### 4. New and Updated Files

- `app/Console/Commands/CloseInactiveSessions.php`: Command to find and close inactive sessions after 5 minutes.
- `resources/js/session-tracker.js`: Enhanced client-side code to track user activity and handle auto-logout.
- `routes/api.php`: API endpoints for session heartbeat and auto-logout.

### 5. Scheduled Command

The `sessions:close-inactive` command runs every 5 minutes via the Laravel scheduler to ensure consistent cleanup of inactive sessions.

## How It Works

1. **Preventing Duplicate Sessions:** When a user logs in, any existing active sessions for that user are automatically closed first.

2. **Client-Side Tracking:** The enhanced JavaScript code tracks:
   - Browser close events (fires an auto-logout API call)
   - User inactivity (logs out after 5 minutes of inactivity)
   - Tab visibility changes (logs out if away for more than 5 minutes)
   - Regular heartbeats to update the last activity timestamp (only when user is active)
   - Displays a persistent countdown timer showing remaining session time
   - Allows users to click the timer to extend their session
   - Timer changes color based on remaining time (blue > 1 min, yellow < 1 min, red < 30 sec)

3. **Server-Side Cleanup:** The scheduled command runs every 5 minutes to close any sessions that have been inactive for more than 5 minutes.

4. **Accurate Duration Tracking:** The system now properly handles session duration calculations, preventing negative durations.

## Recommended Next Steps

1. **Testing:** Test the auto-logout functionality by:
   - Closing the browser and checking if the session is properly closed
   - Leaving the page inactive for over 5 minutes and checking if auto-logout occurs
   - Switching away from the tab for over 5 minutes and verifying logout behavior
   - Verify the countdown timer appears immediately after login
   - Test that clicking the timer resets the 5-minute countdown
   - Observe the timer color changes at different countdown thresholds

2. **Monitoring:** Monitor the work sessions table to ensure it's correctly tracking login/logout events and session durations.
