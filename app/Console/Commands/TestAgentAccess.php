<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Gate;
use App\Constants\Roles;
use App\Domain\Entities\User;
use Illuminate\Support\Facades\DB;

class TestAgentAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:agent-access';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test agent access restrictions to ensure they cannot access user management';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing agent access restrictions...');

        // Find an agent user
        $agent = User::whereHas('roles', function ($query) {
            $query->where('name', Roles::AGENT);
        })->first();

        if (!$agent) {
            $this->error('No agent user found in the system. Please create one first.');
            return 1;
        }

        $this->info("Found agent: {$agent->name} (ID: {$agent->id})");

        // Test various access permissions
        $this->testAccess($agent, 'view-user-list', 'View user list');
        $this->testAccess($agent, 'create-users', 'Create users');
        $this->testAccess($agent, 'edit-users', 'Edit users');
        $this->testAccess($agent, 'delete-users', 'Delete users');
        $this->testAccess($agent, 'manage-users', 'Manage users');

        // Test dashboard access
        $this->testAccess($agent, 'view-agent-dashboard', 'Access agent dashboard');
        $this->testAccess($agent, 'view-admin-dashboard', 'Access admin dashboard');
        $this->testAccess($agent, 'view-supervisor-dashboard', 'Access supervisor dashboard');

        $this->info('Agent access test completed.');

        return 0;
    }

    /**
     * Test if a user has access to a specific ability
     */
    private function testAccess(User $user, string $ability, string $description)
    {
        $hasAccess = Gate::forUser($user)->allows($ability);

        if ($hasAccess) {
            $this->warn("❌ Agent CAN {$description} - This might be an issue!");
        } else {
            $this->info("✅ Agent CANNOT {$description} - This is correct.");
        }
    }
}
