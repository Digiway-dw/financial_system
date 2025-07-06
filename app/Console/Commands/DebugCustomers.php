<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Domain\Entities\Customer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DebugCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug customers table and relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Customer Repository Debug ===');

        try {
            $repository = new \App\Infrastructure\Repositories\EloquentCustomerRepository();
            $result = $repository->getAll([]);

            $this->info('Repository getAll() worked successfully!');
            $this->info('Number of customers: ' . count($result['customers']));
            $this->info('Top by transaction count: ' . count($result['topByTransactionCount']));
            $this->info('Top by transferred: ' . count($result['topByTransferred']));
            $this->info('Top by commissions: ' . count($result['topByCommissions']));

            if (!empty($result['customers'])) {
                $customer = $result['customers'][0];
                $this->info('First customer: ' . $customer['name']);
            }
        } catch (\Exception $e) {
            $this->error('Error in repository: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ':' . $e->getLine());
        }

        return 0;
    }
}
