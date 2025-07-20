<?php

namespace App\Console\Commands;

use App\Application\Services\WorkSessionService;
use App\Domain\Entities\User;
use App\Models\Domain\Entities\WorkSession;
use App\Models\WorkingHour;
use App\Notifications\AdminNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;

class CheckWorkingHours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'working-hours:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check active sessions and log out users who are outside their working hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking active sessions for working hours compliance...');

        // Get current server time
        $now = Carbon::now();
        $currentDayOfWeek = strtolower($now->englishDayOfWeek);
        $currentTime = $now->format('H:i:s');

        // Get all active sessions
        $activeSessions = WorkSession::whereNull('logout_at')->get();
        $loggedOutCount = 0;

        foreach ($activeSessions as $session) {
            /** @var \App\Domain\Entities\User $user */
            $user = User::find($session->user_id);

            // Skip if user not found or is admin
            if (!$user || $user->hasRole('admin')) {
                continue;
            }

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

                // Log the violation
                Log::warning("User {$user->id} ({$user->email}) was logged out due to working hours violation", [
                    'user_id' => $user->id,
                    'day' => $currentDayOfWeek,
                    'time' => $currentTime,
                    'session_id' => $session->id,
                ]);

                // End work session
                $workSessionService = app(WorkSessionService::class);
                $workSessionService->endSession($user);

                // Send notification to admin
                $this->notifyAdmin($user);

                $loggedOutCount++;
            }
        }

        $this->info("Completed working hours check. {$loggedOutCount} users were logged out.");

        return 0;
    }

    /**
     * Send notification to admin about working hours violation
     */
    private function notifyAdmin($user): void
    {
        $now = Carbon::now();
        $message = "Working hours violation: {$user->name} (ID: {$user->id}) was forcefully logged out due to working hours restriction.";
        $branchInfo = $user->branch ? "Branch: {$user->branch->name}" : "No branch assigned";
        $details = "Time: {$now->format('Y-m-d H:i:s')}, {$branchInfo}";

        // Get admin users
        $admins = User::role('admin')->get();

        // Notify all admins
        Notification::send($admins, new AdminNotification(
            $message . "\n" . $details
        ));
    }
}
