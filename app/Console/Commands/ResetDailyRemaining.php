<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ResetDailyRemaining extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lines:reset-daily-remaining';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset daily_remaining column in lines table to 0 once per day.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now()->toDateString();
        $lastDailyReset = Cache::get('lines:last_daily_reset');
        $lastMonthlyReset = Cache::get('lines:last_monthly_reset');
        $firstOfMonth = Carbon::now()->firstOfMonth()->toDateString();

        // Daily reset
        if ($lastDailyReset !== $today) {
            DB::table('lines')->update(['daily_usage' => 0]);
            Cache::put('lines:last_daily_reset', $today, now()->addDay());
            $this->info('daily_usage reset to 0 for all lines.');
        } else {
            $this->info('Daily reset already performed today.');
        }

        // Monthly reset
        if ($lastMonthlyReset !== $firstOfMonth) {
            DB::table('lines')->update(['monthly_usage' => 0]);
            Cache::put('lines:last_monthly_reset', $firstOfMonth, now()->addMonth());
            $this->info('monthly_usage reset to 0 for all lines.');
        } else {
            $this->info('Monthly reset already performed this month.');
        }
        return 0;
    }
}
