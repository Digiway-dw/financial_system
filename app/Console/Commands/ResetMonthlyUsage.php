<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
        // Check if tomorrow is the 1st day of the month
        $tomorrow = Carbon::now()->addDay()->day;

        if ($tomorrow === 1) {
            // Reset monthly_usage to 0 for all lines
            DB::table('lines')->update(['monthly_usage' => 0]);

            // Update monthly_remaining = monthly_limit - current_balance for all lines
            DB::table('lines')->update([
                'monthly_remaining' => DB::raw('monthly_limit - current_balance')
            ]);

            $this->info('✅ monthly_usage reset to 0 and monthly_remaining recalculated for all lines.');
        } else {
            $this->info('⏩ Not the last day of the month. Skipping reset.');
        }

        return 0;
    }
}
