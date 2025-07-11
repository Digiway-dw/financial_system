<?php

namespace App\Listeners;

use App\Application\Services\WorkSessionService;
use Illuminate\Auth\Events\Logout;

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
        }
    }
}
