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
        $schedule->command('app:unfreeze-lines-and-reset-daily-balance')
            ->dailyAt('22:40')
            ->timezone('Africa/Cairo')
            ->runInBackground()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/daily-reset.log'));

        // Reset daily_usage for lines once per day at 00:00 Cairo time
        $schedule->command('lines:reset-daily-usage')
            ->dailyAt('00:00')
            ->timezone('Africa/Cairo')
            ->runInBackground()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/daily-reset.log'));

        // Reset monthly_usage for lines once per month at 00:00 on the 1st, Cairo time
        $schedule->command('lines:reset-monthly-usage')
            ->monthlyOn(1, '00:00')
            ->timezone('Africa/Cairo')
            ->runInBackground()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/monthly-reset.log'));
        // Session timing commands have been removed - no auto-logout functionality

        // Record startup safe balances at midnight
        $schedule->command('safe:record-startup-balances')->dailyAt('00:00');
    }
}
