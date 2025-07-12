<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LoginGuard
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
        if (Auth::check()) {
            $user = Auth::user();
            $lastActivity = session('last_activity', 0);
            $currentTime = now()->timestamp;
            $elapsedTime = $currentTime - $lastActivity;

            // If last activity was more than 5 minutes ago, force logout
            if ($elapsedTime > 300) { // 5 minutes = 300 seconds
                Log::info("Login guard forcing logout for user {$user->id} after {$elapsedTime} seconds of inactivity");

                // End work session for the user
                app(\App\Application\Services\WorkSessionService::class)->endSession($user);

                // Log the user out
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => 'expired',
                        'message' => 'Your session has expired due to inactivity'
                    ], 401);
                }

                return redirect()->route('login')
                    ->with('status', 'Your session has expired due to inactivity.');
            }

            // Update session timestamp on regular requests
            if (
                !$request->ajax() && !$request->wantsJson() &&
                !$request->is('session-*') && !$request->is('auto-logout')
            ) {
                session(['last_activity' => $currentTime]);
            }
        }

        return $next($request);
    }
}
