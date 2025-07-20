<?php

namespace App\Console\Commands;

use App\Application\Services\WorkSessionService;
use Illuminate\Console\Command;

class CheckInactiveSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:check-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for inactive sessions and end them based on the configured session lifetime';

    /**
     * Execute the console command.
     */
    public function handle(WorkSessionService $workSessionService)
    {
        $this->info('Checking for inactive sessions...');

        $count = $workSessionService->checkInactiveSessions();

        if ($count > 0) {
            $this->info("Ended {$count} inactive session(s).");
        } else {
            $this->info('No inactive sessions found.');
        }

        $sessionLifetime = $workSessionService->getSessionLifetime();
        $this->info("Current session lifetime: {$sessionLifetime} minutes.");

        return Command::SUCCESS;
    }
}
