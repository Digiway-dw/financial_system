# API Routing Fixes

## Issue
The session heartbeat and auto-logout features were not working properly due to 404 errors when accessing the API endpoints:
- `POST http://127.0.0.1:8000/api/session-heartbeat 404 (Not Found)`
- `POST http://127.0.0.1:8000/api/auto-logout 404 (Not Found)`

## Root Cause
1. The API routes were not properly registered in the Laravel application.
2. In Laravel 10's new application structure, the API routes need to be explicitly registered in `bootstrap/app.php`.
3. The session-tracker.js was not handling API route failures gracefully.

## Fixes Applied

### 1. Updated bootstrap/app.php to register API routes
```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        api: __DIR__ . '/../routes/api.php',
        health: '/up',
    )
```

### 2. Added API middleware configuration
```php
->withMiddleware(function (Middleware $middleware): void {
    // Add our middlewares to the global middleware stack
    $middleware->append(\App\Http\Middleware\BypassAuthorization::class);
    $middleware->append(\App\Http\Middleware\AgentDashboardAccess::class);
    
    // Set up API middleware group
    $middleware->api([
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ]);
})
```

### 3. Added test route to API routes for easier diagnosis
```php
// Add a simple test route that doesn't require auth
Route::get('/test', function () {
    Log::info('API test route accessed');
    return response()->json([
        'status' => 'API test route is working',
        'prefix' => request()->route()->getPrefix(),
        'middleware' => request()->route()->middleware(),
        'uri' => request()->route()->uri(),
        'action' => request()->route()->getActionName()
    ]);
});
```

### 4. Updated session-tracker.js to add fallback to web routes
Modified the JavaScript to try the API routes first, then fall back to the equivalent web routes:

```javascript
// For session heartbeat
fetch('/api/session-heartbeat', {
    // API request configuration
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
        // Web route request configuration
    });
})
.catch(error => console.error('Heartbeat error (both routes failed):', error));

// For auto-logout (similar fallback pattern)
```

## Testing Instructions
1. Start the Laravel development server: `php artisan serve`
2. Open the application in your browser
3. Check if the session heartbeat is working by:
   - Opening the browser's developer tools
   - Going to the Network tab
   - Looking for successful POST requests to `/api/session-heartbeat`
4. Test the fallback mechanism by:
   - Temporarily commenting out the API route in `routes/api.php`
   - Checking if the fallback to `/session-heartbeat` works properly

## Additional Notes
- Both API and web routes for session management are now available, providing redundancy
- API routes are prefixed with `/api/` while web routes use the root path
- The system now logs both successful and failed attempts at session heartbeat and auto-logout
- API test route available at `/api/test` to help diagnose API routing issues
