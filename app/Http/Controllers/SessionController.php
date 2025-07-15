<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Application\Services\WorkSessionService;

class SessionController extends Controller
{
    protected $workSessionService;

    public function __construct(WorkSessionService $workSessionService)
    {
        $this->workSessionService = $workSessionService;
    }

    /*
     * NOTE: Session timer functionality has been disabled.
     * All session expiring, auto-logout, and timer features have been removed.
     * These methods are commented out to preserve controller structure.
     */

    /*
    /**
     * Handle session heartbeat requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    /*
    public function heartbeat(Request $request)
    {
        Log::info('Session heartbeat received for user: ' . ($request->user() ? $request->user()->id : 'unknown'));

        // Update session last activity time
        session(['last_activity' => now()->timestamp]);

        // Set cookie with expiration time of 5 minutes for browser closure detection
        return response()->json(['status' => 'success'])
            ->cookie('session_alive', 'true', 5); // 5 minutes
    }
    */

    /*
    /**
     * Handle auto-logout requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    /*
    public function autoLogout(Request $request)
    {
        $user = $request->user();
        $userId = $user ? $user->id : 'unknown';

        Log::info('Auto-logout received for user: ' . $userId);

        // End the user's work session if they're logged in
        if ($user) {
            $this->workSessionService->endSession($user);
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // Clear the session_alive cookie
        return response()->json([
            'status' => 'success',
            'message' => 'User logged out successfully'
        ])->cookie('session_alive', '', -1); // Expire the cookie
    }
    */

    /*
    /**
     * Check session status
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    /*
    public function checkStatus(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'expired',
                'message' => 'User not authenticated'
            ]);
        }

        $lastActivity = session('last_activity', 0);
        $currentTime = now()->timestamp;
        $elapsedTime = $currentTime - $lastActivity;
        $timeout = 300; // 5 minutes = 300 seconds

        // If more than 5 minutes have passed, session should be expired
        if ($elapsedTime > $timeout) {
            // Auto-logout the user server-side
            $this->autoLogout($request);

            Log::info('Session expired via status check: User ' . Auth::id() . ' inactive for ' . $elapsedTime . ' seconds');

            return response()->json([
                'status' => 'expired',
                'message' => 'Session expired',
                'elapsed_seconds' => $elapsedTime,
            ]);
        }

        return response()->json([
            'status' => 'active',
            'elapsed_seconds' => $elapsedTime,
            'remaining_seconds' => $timeout - $elapsedTime
        ]);
    }
    */
}
