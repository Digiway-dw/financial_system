<?php

namespace App\Listeners;

use App\Application\Services\WorkSessionService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminNotification;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     */
    public function __construct(private WorkSessionService $workSessionService) {}

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $this->workSessionService->startSession($event->user, request());

        // Notify all admins and supervisors
        $adminsAndSupervisors = \App\Domain\Entities\User::role(['admin', 'general_supervisor'])->get();
        $user = $event->user;
        $message = "User {$user->name} ({$user->email}) has logged in.";
        Notification::send($adminsAndSupervisors, new AdminNotification($message));
    }
}
