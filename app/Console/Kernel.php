<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('unfreeze-lines-and-reset-daily-balance')->dailyAt('00:00');
        
        // Run every 5 minutes to check for inactive sessions
        $schedule->command('sessions:close-inactive')->everyFiveMinutes();
        
        // Run every minute to clean up stale sessions
        $schedule->command('sessions:cleanup')->everyMinute();
    }
} 