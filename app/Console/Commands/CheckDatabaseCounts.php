<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Entities\User;
use App\Models\Domain\Entities\Branch;
use App\Models\Domain\Entities\Safe;
use App\Models\Domain\Entities\Line;
use App\Models\Domain\Entities\Customer;
use App\Models\Domain\Entities\Transaction;

class CheckDatabaseCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:check-counts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks record counts for key database tables.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking database record counts:');

        $this->info('Users: ' . User::count());
        $this->info('Branches: ' . Branch::count());
        $this->info('Safes: ' . Safe::count());
        $this->info('Lines: ' . Line::count());
        $this->info('Customers: ' . Customer::count());
        $this->info('Transactions: ' . Transaction::count());

        $this->info('Check complete.');
    }
}
