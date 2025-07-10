<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now();
        // Unfreeze all frozen lines
        \App\Models\Domain\Entities\Line::where('status', 'frozen')->update(['status' => 'active']);
        // Reset daily_starting_balance for all lines
        foreach (\App\Models\Domain\Entities\Line::all() as $line) {
            $line->daily_starting_balance = $line->current_balance;
            // If it's the first day of the month, also reset starting_balance
            if ($today->isStartOfMonth()) {
                $line->starting_balance = $line->current_balance;
            }
            $line->save();
        }
        $this->info('All frozen lines have been unfrozen and daily/monthly starting balances have been reset.');
        return 0;
    }
}
