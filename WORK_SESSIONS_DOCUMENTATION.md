# Work Sessions Feature Documentation

## Overview

The Work Sessions feature tracks user login and logout activities, calculates work durations, and provides admin interfaces for monitoring and analyzing user productivity. This document explains the implementation, testing strategy, and usage guidelines.

## Feature Components

### Database
- **Table**: `work_sessions`
- **Columns**:
  - `id`: Primary key
  - `user_id`: Foreign key to users table
  - `login_at`: Timestamp of user login
  - `logout_at`: Timestamp of user logout (null if session is active)
  - `duration_minutes`: Calculated session duration in minutes
  - `ip_address`: User's IP address during login
  - `user_agent`: Browser/device information
  - `created_at` and `updated_at`: Standard timestamps

### Models
- **WorkSession**: Eloquent model with relationship to User and duration calculation method

### Services
- **WorkSessionService**: Handles session creation, termination, and retrieval operations
  - `startSession`: Creates a new session on user login
  - `endSession`: Updates session on user logout and calculates duration
  - `getActiveSession`: Retrieves a user's active (not ended) session
  - `getUserSessions`: Gets all sessions for a specific user

### Event Listeners
- **LogSuccessfulLogin**: Creates a work session when a user logs in
- **LogSuccessfulLogout**: Updates a work session when a user logs out

### Admin Interface
- **Livewire Component**: `Admin\WorkSessions\Index` for displaying and filtering work sessions
- **View**: Blade template with filtering, statistics, and export functionality
- **Access Control**: Limited to admin, supervisor, and branch manager roles

## Implementation Details

### Event Handling
The system hooks into Laravel's authentication events:
- `Illuminate\Auth\Events\Login`: Triggers session creation
- `Illuminate\Auth\Events\Logout`: Triggers session termination and duration calculation

### Security Measures
- Only administrators, supervisors, and branch managers can view work session data
- Regular users and agents cannot access work session information
- The feature uses authorization gates for permission control

### Edge Cases Handled
- **Multiple Logins**: If a user logs in multiple times without logging out, each login creates a new session
- **Missing Logouts**: Sessions without logouts remain in the database with null logout time and duration
- **Session Management**: Only the most recent active session is closed on logout

## Testing Strategy

### Unit Tests
- Tests for `WorkSession` model duration calculation
- Tests for `WorkSessionService` methods
- Tests for event listeners

### Feature Tests
- Authentication event integration (login/logout)
- Multiple login scenario testing
- Session duration calculation testing
- Admin interface access control
- Livewire component functionality

### Manual Testing
- Verify that work sessions are created on login and updated on logout
- Confirm that only authorized users can access the admin interface
- Test filtering and export functionality
- Verify data consistency across multiple sessions

## Usage Guidelines

### For Administrators
1. Access work sessions from the Admin Dashboard
2. Use filters to find sessions by user, date range, or duration
3. Export session data to CSV for reporting
4. Monitor user productivity and work patterns
5. Identify unusual login behaviors or session durations

### Security Considerations
- Work session data contains sensitive information about user activity
- Limit access to authorized personnel only
- Regular audit of access to work session data is recommended
- Session data should be included in any data retention/deletion policies

## Future Enhancements
- Export to Excel format with formatting and charts
- Real-time notifications for extended sessions
- Integration with time tracking and productivity metrics
- Dashboard widgets showing active users and session statistics
- Geolocation tracking for remote work monitoring (where legally permitted)

## Conclusion
The Work Sessions feature provides valuable insights into user activity patterns while maintaining security and privacy. It adheres to clean architecture principles and integrates seamlessly with Laravel's authentication system.
