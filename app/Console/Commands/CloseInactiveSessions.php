<?php

namespace App\Console\Commands;

use App\Application\Services\WorkSessionService;
use App\Models\Domain\Entities\WorkSession;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * NOTE: This command has been disabled as part of removing session timing functionality.
 * All session expiring, auto-logout, and timer features have been removed.
 */

/*
class CloseInactiveSessions extends Command
{
    protected $signature = 'sessions:close-inactive';
    protected $description = 'Close all inactive work sessions';

    protected $workSessionService;

    public function __construct(WorkSessionService $workSessionService)
    {
        parent::__construct();
        $this->workSessionService = $workSessionService;
    }

    public function handle()
    {
        // Find all active sessions where last activity was more than 5 minutes ago
        $cutoffTime = Carbon::now()->subMinutes(5);

        $activeSessions = WorkSession::whereNull('logout_at')
            ->get();

        $closedCount = 0;

        foreach ($activeSessions as $session) {
            // If the session was created/updated more than 5 minutes ago
            if (
                $session->created_at < $cutoffTime &&
                ($session->updated_at < $cutoffTime || !$session->updated_at)
            ) {

                // End the session
                $session->logout_at = now();
                $session->calculateDuration();
                $session->save();

                $closedCount++;
            }
        }

        $this->info("Closed {$closedCount} inactive sessions.");

        return 0;
    }
}
*/
