<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StartupSafeBalance;
use App\Models\Domain\Entities\Branch;
use App\Models\Domain\Entities\Safe;
use Carbon\Carbon;

class RecordStartupSafeBalances extends Command
{
    protected $signature = 'safe:record-startup-balances';
    protected $description = 'Record the startup safe balance for each branch and all branches at 12:00 AM';

    public function handle()
    {
        $today = Carbon::today();

        // For each branch
        $branches = Branch::all();
        foreach ($branches as $branch) {
            $balance = Safe::where('branch_id', $branch->id)->sum('current_balance');
            StartupSafeBalance::updateOrCreate(
                [
                    'branch_id' => $branch->id,
                    'date' => $today->toDateString(),
                ],
                [
                    'balance' => $balance,
                ]
            );
        }

        // For all branches (branch_id = null)
        $totalBalance = Safe::sum('current_balance');
        StartupSafeBalance::updateOrCreate(
            [
                'branch_id' => null,
                'date' => $today->toDateString(),
            ],
            [
                'balance' => $totalBalance,
            ]
        );

        $this->info('Startup safe balances recorded for ' . $today->toDateString());
    }
} 