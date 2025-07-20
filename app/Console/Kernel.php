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
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('working-hours:check')->everyMinute();
        $schedule->command('sessions:check-inactive')->everyMinute();
        $schedule->command('unfreeze-lines-and-reset-daily-balance')->dailyAt('00:00');

        // Session timing commands have been removed - no auto-logout functionality

        // Record startup safe balances at midnight
        $schedule->command('safe:record-startup-balances')->dailyAt('00:00');
    }
}
