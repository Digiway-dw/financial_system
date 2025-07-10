<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Constants\Roles;
use App\Domain\Entities\User;
use Illuminate\Support\Facades\Gate;

class TestAgentCustomerPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:agent-customer-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test agent permissions for customer management and transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing agent permissions for customer management and transactions...');

        // Find an agent user
        $agent = User::whereHas('roles', function ($query) {
            $query->where('name', Roles::AGENT);
        })->first();

        if (!$agent) {
            $this->error('No agent user found in the system. Please create one first.');
            return 1;
        }

        $this->info("Found agent: {$agent->name} (ID: {$agent->id})");

        // Test customer management permissions
        $this->info("\nCustomer Management Permissions:");
        $this->testPermission($agent, 'view-customers', 'View customers');
        $this->testPermission($agent, 'create-customers', 'Create customers');
        $this->testPermission($agent, 'edit-customers', 'Edit customers');
        $this->testPermission($agent, 'delete-customers', 'Delete customers');
        $this->testPermission($agent, 'manage-customers', 'Manage customers');

        // Test transaction permissions
        $this->info("\nTransaction Permissions:");
        $this->testPermission($agent, 'create-transactions', 'Create transactions');
        $this->testPermission($agent, 'edit-own-transactions', 'Edit own transactions');
        $this->testPermission($agent, 'edit-all-transactions', 'Edit all transactions');
        $this->testPermission($agent, 'approve-transactions', 'Approve transactions');
        $this->testPermission($agent, 'create-cash-transactions', 'Create cash transactions');
        $this->testPermission($agent, 'initiate-safe-transfer', 'Initiate safe transfers');
        $this->testPermission($agent, 'approve-safe-transfer', 'Approve safe transfers');

        // Test data visibility
        $this->info("\nData Visibility Permissions:");
        $this->testPermission($agent, 'view-all-branches-data', 'View all branches data');
        $this->testPermission($agent, 'view-own-branch-data', 'View own branch data');
        $this->testPermission($agent, 'view-other-employees-data', 'View other employees data');

        $this->info("\nAgent permissions test completed.");

        return 0;
    }

    /**
     * Test if a user has a specific permission
     */
    private function testPermission(User $user, string $permission, string $description)
    {
        $hasPermission = Gate::forUser($user)->allows($permission);

        if ($hasPermission) {
            $this->info("✅ Agent CAN {$description}");
        } else {
            $this->warn("❌ Agent CANNOT {$description}");
        }
    }
}
