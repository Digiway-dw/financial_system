<?php

namespace App\Http\Middleware;

use App\Application\Services\WorkSessionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackActiveSession
{
    protected $workSessionService;

    public function __construct(WorkSessionService $workSessionService)
    {
        $this->workSessionService = $workSessionService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is logged in, update their last activity timestamp
        if (Auth::check()) {
            $lastActivity = session('last_activity', 0);
            $currentTime = now()->timestamp;

            // If the last activity was more than 5 minutes ago, force logout
            if ($lastActivity > 0 && ($currentTime - $lastActivity) > 300) { // 5 minutes = 300 seconds
                // End work session for the user
                $this->workSessionService->endSession(Auth::user());

                // Log the user out
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('status', 'Your session has expired due to inactivity.');
            }

            // Store the current timestamp in the session for non-AJAX requests
            if (!$request->ajax() && !$request->wantsJson()) {
                session(['last_activity' => $currentTime]);
            }
        }

        return $next($request);
    }
}
