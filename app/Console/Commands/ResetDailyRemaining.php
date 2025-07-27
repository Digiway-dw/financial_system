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
        $lastReset = Cache::get('lines:last_reset');

        if ($lastReset === $today) {
            $this->info('Daily reset already performed today.');
            return 0;
        }

        DB::table('lines')->update(['daily_usage' => 0]);
        Cache::put('lines:last_reset', $today, now()->addDay());
        $this->info('daily_remaining reset to 0 for all lines.');
        return 0;
    }
}
