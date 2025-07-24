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
        if (\App\Helpers\NotificationSuppression::$suppressLoginLogout) {
            return;
        }
        $user = $event->user;
        $userId = $user->id;
        $cacheKey = "login_notification_sent_{$userId}";
        if (cache()->has($cacheKey)) {
            return; // Already sent recently
        }
        cache()->put($cacheKey, true, 10); // 10 seconds debounce

        $this->workSessionService->startSession($user, request());

        // Notify all admins and supervisors
        $adminsAndSupervisors = \App\Domain\Entities\User::role(['admin', 'general_supervisor'])->get();
        $message = "المستخدم {$user->name} ({$user->email}) قام بتسجيل الدخول.";
        Notification::send($adminsAndSupervisors, new AdminNotification($message));
    }
}
