<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Constants\Roles;
use App\Domain\Entities\User;
use App\Helpers\RoleUiHelper;
use Illuminate\Support\Facades\Gate;

class TestRoleBasedUi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:role-ui';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test role-based UI components for consistency';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing role-based UI components...');
        
        // Test for each role
        $this->testRoleUi(Roles::ADMIN, 'Administrator');
        $this->testRoleUi(Roles::GENERAL_SUPERVISOR, 'General Supervisor');
        $this->testRoleUi(Roles::BRANCH_MANAGER, 'Branch Manager');
        $this->testRoleUi(Roles::AGENT, 'Agent');
        $this->testRoleUi(Roles::TRAINEE, 'Trainee');
        $this->testRoleUi(Roles::AUDITOR, 'Auditor');
        
        $this->info('UI consistency test completed.');
        
        return 0;
    }
    
    /**
     * Test UI components for a specific role
     */
    private function testRoleUi(string $roleName, string $displayName)
    {
        $this->info("\nTesting UI for $displayName role:");
        
        // Find a user with this role
        $user = User::whereHas('roles', function($query) use ($roleName) {
            $query->where('name', $roleName);
        })->first();
        
        if (!$user) {
            $this->warn("No $displayName user found in the system.");
            return;
        }
        
        $this->info("Found $displayName: {$user->name} (ID: {$user->id})");
        
        // Check dashboard component
        $dashboardComponent = $this->getDashboardComponentForUser($user);
        $this->info("Dashboard component: $dashboardComponent");
        
        // Check transaction buttons
        $this->info("Available transaction actions:");
        $buttons = $this->getTransactionButtonsForUser($user);
        foreach ($buttons as $button) {
            $this->info("- {$button['label']} ({$button['route']})");
        }
        
        // Check management permissions
        $hasManagement = $this->hasManagementPermissionsForUser($user);
        if ($hasManagement) {
            $this->info("User has management permissions ✅");
        } else {
            $this->info("User does not have management permissions ❌");
        }
        
        // Draw a separator
        $this->line(str_repeat('-', 50));
    }
    
    /**
     * Get dashboard component for a user
     */
    private function getDashboardComponentForUser(User $user): string
    {
        // Admin dashboard
        if (Gate::forUser($user)->allows('view-admin-dashboard')) {
            return 'dashboard.admin';
        }
        
        // Supervisor dashboard
        if (Gate::forUser($user)->allows('view-supervisor-dashboard')) {
            return 'dashboard.general_supervisor';
        }
        
        // Branch manager dashboard
        if (Gate::forUser($user)->allows('view-branch-manager-dashboard')) {
            return 'dashboard.branch_manager';
        }
        
        // Auditor dashboard
        if (Gate::forUser($user)->allows('view-auditor-dashboard')) {
            return 'dashboard.auditor';
        }
        
        // Agent dashboard
        if (Gate::forUser($user)->allows('view-agent-dashboard')) {
            return 'dashboard.agent';
        }
        
        // Trainee dashboard
        if (Gate::forUser($user)->allows('view-trainee-dashboard')) {
            return 'dashboard.trainee';
        }
        
        return 'dashboard.default';
    }
    
    /**
     * Get transaction buttons for a user
     */
    private function getTransactionButtonsForUser(User $user): array
    {
        $buttons = [];
        
        // Send money
        if (Gate::forUser($user)->allows('create-transactions')) {
            $buttons[] = [
                'route' => 'transactions.send',
                'label' => 'Send Money'
            ];
        }
        
        // Receive money
        if (Gate::forUser($user)->allows('create-transactions')) {
            $buttons[] = [
                'route' => 'transactions.receive',
                'label' => 'Receive Money'
            ];
        }
        
        // Cash transactions
        if (Gate::forUser($user)->allows('create-cash-transactions')) {
            $buttons[] = [
                'route' => 'transactions.cash',
                'label' => 'Cash Transaction'
            ];
        }
        
        // Safe transfers
        if (Gate::forUser($user)->allows('initiate-safe-transfer')) {
            $buttons[] = [
                'route' => 'safes.transfer',
                'label' => 'Safe Transfer'
            ];
        }
        
        // Pending approvals
        if (Gate::forUser($user)->allows('approve-transactions')) {
            $buttons[] = [
                'route' => 'transactions.pending',
                'label' => 'Pending Approvals'
            ];
        }
        
        return $buttons;
    }
    
    /**
     * Check if user has management permissions
     */
    private function hasManagementPermissionsForUser(User $user): bool
    {
        return Gate::forUser($user)->allows('manage-users') ||
               Gate::forUser($user)->allows('manage-branches') ||
               Gate::forUser($user)->allows('manage-lines') ||
               Gate::forUser($user)->allows('manage-safes') ||
               Gate::forUser($user)->allows('manage-roles') ||
               Gate::forUser($user)->allows('manage-system-settings');
    }
}
