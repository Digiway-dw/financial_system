<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ResetMonthlyUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lines:reset-monthly-usage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset monthly_usage column in lines table to 0 once per month.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $firstOfMonth = Carbon::now()->firstOfMonth()->toDateString();
        $lastMonthlyReset = Cache::get('lines:last_monthly_reset');

        if ($lastMonthlyReset === $firstOfMonth) {
            $this->info('Monthly reset already performed this month.');
            return 0;
        }

        // Reset monthly_usage to 0 for all lines
        DB::table('lines')->update(['monthly_usage' => 0]);

        // Update monthly_remaining = monthly_limit - current_balance for all lines
        DB::table('lines')->update([
            'monthly_remaining' => DB::raw('monthly_limit - current_balance')
        ]);

        Cache::put('lines:last_monthly_reset', $firstOfMonth, now()->addMonth());
        $this->info('monthly_usage reset to 0 and monthly_remaining recalculated for all lines.');
        return 0;
    }
}
