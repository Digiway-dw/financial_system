<?php

namespace App\Listeners;

use App\Application\Services\WorkSessionService;
use Illuminate\Auth\Events\Login;

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
    }
}
