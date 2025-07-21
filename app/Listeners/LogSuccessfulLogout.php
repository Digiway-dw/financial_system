<?php

namespace App\Listeners;

use App\Application\Services\WorkSessionService;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminNotification;

class LogSuccessfulLogout
{
    /**
     * Create the event listener.
     */
    public function __construct(private WorkSessionService $workSessionService) {}

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        if ($event->user) {
            $this->workSessionService->endSession($event->user);

            // Notify all admins and supervisors
            $adminsAndSupervisors = \App\Domain\Entities\User::role(['admin', 'general_supervisor'])->get();
            $user = $event->user;
            $message = "User {$user->name} ({$user->email}) has logged out.";
            Notification::send($adminsAndSupervisors, new AdminNotification($message));
        }
    }
}
