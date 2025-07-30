<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Domain\Entities\Line;
use Illuminate\Support\Facades\Log;

class UnfreezeLinesAndResetDailyBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:unfreeze-lines-and-reset-daily-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset daily usage and monthly usage for lines, unfreeze frozen lines';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now();
        $isStartOfMonth = $today->isStartOfMonth();
        
        try {
            // Unfreeze all frozen lines
            Line::where('status', 'frozen')->update(['status' => 'active']);
            $this->info('All frozen lines have been unfrozen.');
            
            // Reset daily usage for all lines
            Line::query()->update([
                'daily_usage' => 0,
            ]);
            $this->info('Daily usage reset to 0 for all lines.');
            
            // Reset monthly usage if it's the start of the month
            if ($isStartOfMonth) {
                Line::query()->update([
                    'monthly_usage' => 0,
                ]);
                $this->info('Monthly usage reset to 0 for all lines (start of month).');
            }
            
            // Reset daily_starting_balance for all lines
            $today = $today->toDateString();
            foreach (Line::all() as $line) {
                $line->daily_starting_balance = $line->current_balance;
                $line->last_daily_reset = $today;
                
                // If it's the first day of the month, also reset starting_balance
                if ($isStartOfMonth) {
                    $line->starting_balance = $line->current_balance;
                    $line->last_monthly_reset = $today;
                }
                
                $line->save();
            }
            
            $this->info('Daily and monthly starting balances have been reset.');
            Log::info('Daily and monthly usage reset completed successfully', [
                'is_start_of_month' => $isStartOfMonth,
                'time' => $today->toDateTimeString()
            ]);
            
            return 0;
        } catch (\Exception $e) {
            $this->error('فشل إعادة تعيين قيم الاستخدام: ' . $e->getMessage());
            Log::error('فشل إعادة تعيين قيم الاستخدام', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }
    }
}
