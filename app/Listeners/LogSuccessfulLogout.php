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
        if (\App\Helpers\NotificationSuppression::$suppressLoginLogout) {
            return;
        }
        if ($event->user) {
            $user = $event->user;
            $userId = $user->id;
            $cacheKey = "logout_notification_sent_{$userId}";
            if (cache()->has($cacheKey)) {
                return; // Already sent recently
            }
            cache()->put($cacheKey, true, 10); // 10 seconds debounce

            $this->workSessionService->endSession($user);

            // Notify all admins and supervisors
            $adminsAndSupervisors = \App\Domain\Entities\User::role(['admin', 'general_supervisor'])->get();
            $message = "المستخدم {$user->name} ({$user->email}) قام بتسجيل الخروج.";
            Notification::send($adminsAndSupervisors, new AdminNotification($message));
        }
    }
}
