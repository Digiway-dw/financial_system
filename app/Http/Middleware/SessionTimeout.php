<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip for unauthenticated users
        if (!Auth::check()) {
            return $next($request);
        }

        // Skip for specific routes related to session management
        if (
            $request->is('session-*') || $request->is('auto-logout') ||
            $request->is('api/session-*') || $request->is('api/auto-logout')
        ) {
            return $next($request);
        }

        $lastActivity = session('last_activity');

        // If no last activity timestamp, set it now
        if (!$lastActivity) {
            session(['last_activity' => now()->timestamp]);
            return $next($request);
        }

        // Calculate time since last activity
        $currentTime = now()->timestamp;
        $sessionLifetime = 300; // 5 minutes = 300 seconds

        // Check if session has timed out
        if ($currentTime - $lastActivity > $sessionLifetime) {

            // End work session for the user
            app(\App\Application\Services\WorkSessionService::class)->endSession(Auth::user());

            // Log the user out
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // If this was an AJAX request, return JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'expired',
                    'message' => 'Your session has expired due to inactivity.'
                ], 401);
            }

            // Redirect to login with message
            return redirect()->route('login')
                ->with('status', 'Your session has expired due to inactivity.');
        }

        // Refresh last activity for non-AJAX requests that aren't status checks
        if (!$request->ajax() && !$request->wantsJson()) {
            session(['last_activity' => $currentTime]);
        }

        return $next($request);
    }
}
