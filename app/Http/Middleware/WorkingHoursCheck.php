<?php

namespace App\Http\Middleware;

use App\Models\WorkingHour;
use App\Notifications\AdminNotification;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class WorkingHoursCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            /** @var \App\Domain\Entities\User $user */
            $user = Auth::user();

            // Skip check for admin users
            if ($user->hasRole('admin')) {
                return $next($request);
            }

            // Get current server time
            $now = Carbon::now();
            $currentDayOfWeek = strtolower($now->englishDayOfWeek);
            $currentTime = $now->format('H:i:s');

            // Check if user has working hours defined for current day
            $workingHour = WorkingHour::where('user_id', $user->id)
                ->where('day_of_week', $currentDayOfWeek)
                ->where('is_enabled', true)
                ->first();

            // If no working hours defined for today or user is outside working hours
            if (
                !$workingHour ||
                $currentTime < $workingHour->start_time ||
                $currentTime > $workingHour->end_time
            ) {

                // Log the violation attempt
             

                // Send notification to admin
                $this->notifyAdmin($user, $request);

                // End work session for the user if exists
                app(\App\Application\Services\WorkSessionService::class)->endSession($user);

                // Log the user out
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Return response based on request type
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => 'unauthorized',
                        'message' => 'You are not allowed to access the system during this time.'
                    ], 403);
                }

                return redirect()->route('login')
                    ->with('status', 'You are not allowed to access the system during this time.');
            }
        }

        return $next($request);
    }

    /**
     * Send notification to admin about working hours violation
     */
    private function notifyAdmin($user, Request $request): void
    {
        $now = Carbon::now();
        $message = "Working hours violation: {$user->name} (ID: {$user->id}) attempted to access the system outside allowed hours.";
        $branchInfo = $user->branch ? "Branch: {$user->branch->name}" : "No branch assigned";
        $details = "Time: {$now->format('Y-m-d H:i:s')}, IP: {$request->ip()}, {$branchInfo}";

        // Get admin users
        $admins = \App\Domain\Entities\User::role('admin')->get();

        // Notify all admins
        Notification::send($admins, new AdminNotification(
            $message . "\n" . $details
        ));
    }
}
